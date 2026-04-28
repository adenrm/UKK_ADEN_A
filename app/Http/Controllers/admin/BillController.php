<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGrade;
use App\Models\OverPayment;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SppBulan;
use App\Models\Spps;
use App\Models\StudentSpp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index()
    {
        $sppList = Spps::all();
        $classes = ClassGrade::all();
        $studentList = User::where('level', 'student')->get();
        if (Auth::user()->level == 'admin')
        {
            return view('admin.bill.index', compact('sppList', 'classes', 'studentList'));
        } else {
            return view('staff.bill.index', compact('sppList', 'classes', 'studentList'));
        }
    }

    public function detail(User $student)
    {
        try {
            $student = User::with([
                'UserData.ClassGrade',
                'studentSpp.spp',
                'studentSpp.sppBulan' => function($query) {
                    $query->orderBy('tahun')
                          ->orderBy('bulan');
                }
            ])->findOrFail($student->id);
            
            if (!$student->studentSpp) {
                return redirect()
                    ->route('bill.index')
                    ->with('error', "student {$student->name} belum memiliki data SPP. Silakan register SPP terlebih dahulu.");
            }
            
            $tagihan = $student->studentSpp->sppBulan;
            $summary = $this->calculateSummary($tagihan);
            $tunggakan = $this->checkTunggakan($tagihan);
            
            $riwayatBayar = Payment::where('student_spp_id', $student->studentSpp->id)
                ->with('details.sppBulan')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $sppList = Spps::where('is_active', true)->get();
            $tahunOptions = range(date('Y') - 1, date('Y') + 2);
            $rekapSemester = $this->calculateSemesterSummary($tagihan);

                return view('admin.bill.payment.detail', compact(
                'student',
                'tagihan',
                'summary',
                'tunggakan',
                'riwayatBayar',
                'sppList',
                'tahunOptions',
                'rekapSemester'
            ));
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            // \Log::error('Error in BillController@detail: ' . $e->getMessage());
            
            return redirect()
                ->route('bill.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     private function calculateSummary($tagihan)
    {
        $totalTagihan = $tagihan->sum('nominal');
        $totalDibayar = $tagihan->whereIn('status', ['paid', 'partial'])->sum(function($item) {
            return $item->nominal - ($item->sisa_utang ?? 0);
        });
        $sisaTagihan = $totalTagihan - $totalDibayar;
        
        $persentase = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
        
        $bulanLunas = $tagihan->where('status', 'paid')->count();
        $totalBulan = $tagihan->count();
        
        $status = 'BELUM LUNAS';
        $statusColor = 'red';
        if ($sisaTagihan <= 0) {
            $status = 'LUNAS';
            $statusColor = 'green';
        } elseif ($persentase > 0) {
            $status = 'SEBAGIAN';
            $statusColor = 'yellow';
        }
        
        return [
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan,
            'persentase' => round($persentase, 2),
            'bulan_lunas' => $bulanLunas,
            'total_bulan' => $totalBulan,
            'status' => $status,
            'status_color' => $statusColor
        ];
    }

     private function checkTunggakan($tagihan)
    {
        $now = Carbon::now();
        $tunggakan = [];
        
        foreach ($tagihan as $item) {
            if ($item->status != 'paid') {
                $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo);
                
                if ($jatuhTempo->lt($now)) {
                    $terlambat = $jatuhTempo->diffInMonths($now);
                    $denda = $terlambat * 5000;
                    
                    $tunggakan[] = [
                        'bulan' => $item->nama_bulan,
                        'tahun' => $item->tahun,
                        'terlambat' => $terlambat . ' bulan',
                        'denda' => $denda,
                        'sisa_tagihan' => $item->sisa_utang > 0 ? $item->sisa_utang : $item->nominal
                    ];
                }
            }
        }
        
        return $tunggakan;
    }

     private function calculateSemesterSummary($tagihan)
    {
        $semester1 = $tagihan->whereBetween('bulan', [1, 6]);
        $semester2 = $tagihan->whereBetween('bulan', [7, 12]);
        
        return [
            'semester_1' => [
                'total' => $semester1->sum('nominal'),
                'dibayar' => $semester1->filter(function($item) {
                    return $item->status == 'paid';
                })->sum('nominal'),
                'bulan_lunas' => $semester1->where('status', 'paid')->count()
            ],
            'semester_2' => [
                'total' => $semester2->sum('nominal'),
                'dibayar' => $semester2->filter(function($item) {
                    return $item->status == 'paid';
                })->sum('nominal'),
                'bulan_lunas' => $semester2->where('status', 'paid')->count()
            ]
        ];
    }

    public function generate()
    {
        if (Auth::user()->level == 'admin')
        {
            return view('admin.bill.create');
        } else {
            return view('staff.bill.create');
        }
    }

    public function register($id)
    {
        $student = User::findOrFail($id);
        $spps = Spps::all();

        if (Auth::user()->level == 'admin')
        {
            return view('admin.bill.register', compact('student', 'spps'));
        } else {
            return view('staff.bill.register', compact('student', 'spps'));
        }
    }

    public function registerSpp(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'spp_id' => 'required|integer|exists:spps,id',
            'tahun_masuk' => 'required|integer',
        ]);

        $studentSpp = StudentSpp::create([
            'user_id' => $request->input('user_id'),
            'spp_id' => $request->input('spp_id'),
            'tahun_masuk' => $request->input('tahun_masuk'),
            'status' => 'active',
        ]);


        

        $studentSpp->load('spp');
        $StudentSppId = $studentSpp->id;
    
        for ($Bulan = 1; $Bulan <= 12; $Bulan++) {
            SppBulan::create([
                'student_spp_id' => $StudentSppId,
                'tahun' => $request->input('tahun_masuk'),
                'bulan' => $Bulan,
                'nominal' => $studentSpp->spp->nominal_per_bulan,
                'status' => 'unpaid',
                'tanggal_jatuh_tempo' => $request->input('tahun_masuk') . '-' . $Bulan . '-01',
                'tanggal_dibayar' => null,
                'sisa_utang' => 0,
            ]);
        }
            return redirect()->route('bill.index')
                ->with('success', 'SPP berhasil terdaftar.');
    }


    public function createPayment($id)
    {
        $student = User::findOrFail($id);
        $studentSpp = StudentSpp::findOrFail($student->studentSpp->id);
        
        if (Auth::user()->level == 'admin')
        {
            return view('admin.bill.payment.create', compact('studentSpp'));
        } else {
            return view('staff.bill.payment.create', compact('studentSpp'));
        }
    }

  
    public function riwayat($studentId)
    {
        $payment = Payment::where('student_spp_id', $studentId)
            ->with('details.sppBulan')
            ->orderBy('created_at', 'desc')
            ->get();
            
        if (Auth::user()->level == 'admin')
        {
            return view('admin.payment.riwayat', compact('payment'));
        } else {
            return view('staff.payment.riwayat', compact('payment'));
        }
    }
}
