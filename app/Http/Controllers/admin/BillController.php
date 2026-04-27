<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGrade;
use App\Models\Payment;
use App\Models\Spps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $sppList = Spps::all();
        $classes = ClassGrade::all();
        $studentList = User::where('level', 'student')->get();
        return view('admin.bill.index', compact('sppList', 'classes', 'studentList'));
    }

    public function detail(User $student)
    {
        try {
            // 1. Cari data student beserta relasinya
            $student = User::with([
                'UserData.ClassGrade',
                'studentSpp.spp',
                'studentSpp.sppBulan' => function($query) {
                    $query->orderBy('tahun')
                          ->orderBy('bulan');
                }
            ])->findOrFail($student->id);
            
            // 2. Validasi apakah student memiliki data SPP
            if (!$student->studentSpp) {
                // CASE: student belum memiliki data SPP
                return redirect()
                    ->route('admin.bill.index')
                    ->with('error', "student {$student->name} belum memiliki data SPP. Silakan register SPP terlebih dahulu.");
            }
            
            // 3. Ambil data tagihan
            $tagihan = $student->studentSpp->sppBulan;
            
            // 4. Hitung summary
            $summary = $this->calculateSummary($tagihan);
            
            // 5. Cek status tunggakan
            $tunggakan = $this->checkTunggakan($tagihan);
            
            // 6. Ambil riwayat pembayaran
            $riwayatBayar = Payment::where('student_spp_id', $student->studentSpp->id)
                ->with('details.sppBulan')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // 7. Data untuk form generate tagihan
            $sppList = Spps::where('is_active', true)->get();
            $tahunOptions = range(date('Y') - 1, date('Y') + 2);
            
            // 8. Data untuk rekap per semester
            $rekapSemester = $this->calculateSemesterSummary($tagihan);
            
            return view('admin.bill.detail', compact(
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
                ->route('admin.bill.index')
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
        
        // Hitung persentase
        $persentase = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
        
        // Hitung bulan yang sudah lunas
        $bulanLunas = $tagihan->where('status', 'paid')->count();
        $totalBulan = $tagihan->count();
        
        // Status keseluruhan
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
                    $denda = $terlambat * 5000; // Denda Rp 5.000 per bulan
                    
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
        return view('admin.bill.create');
    }

    public function register()
    {
        return view('admin.bill.register');
    }
}
