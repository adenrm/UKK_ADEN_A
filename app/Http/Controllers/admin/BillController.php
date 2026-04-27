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
use Illuminate\Support\Facades\DB;

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

    public function register($id)
    {
        $student = User::findOrFail($id);
        $spps = Spps::all();
        return view('admin.bill.register', compact('student', 'spps'));
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

        return redirect()->route('admin.bill.index')
            ->with('success', 'SPP berhasil terdaftar.');
    }


    public function createPayment($id)
    {
        // $studentSpp = StudentSpp::with(['user.UserData', 'spp'])->findOrFail($student->id);
        $student = User::findOrFail($id);
        $studentSpp = StudentSpp::findOrFail($student->studentSpp->id);
        return view('admin.bill.payment.create', compact('studentSpp'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'nominal_bayar' => 'required|numeric|min:10000',
            'metode_pembayaran' => 'required|in:tunai,transfer,virtual_account,qris',
            'dibayar_oleh' => 'required|string|max:100'
        ]);
        
        DB::beginTransaction();
        
        try {
            // 1. Cari data StudentSpp berdasarkan ID
            $studentSpp = StudentSpp::with('user')->findOrFail($id);
            $student = $studentSpp->user;
            
            // 2. Cek apakah ada data StudentSpp
            if (!$studentSpp) {
                throw new \Exception('Data SPP tidak ditemukan.');
            }
            
            // 3. Ambil bulan yang belum lunas
            $bulanBelumBayar = SppBulan::where('student_spp_id', $studentSpp->id)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->get();
            
            if ($bulanBelumBayar->isEmpty()) {
                throw new \Exception('Semua tagihan sudah lunas!');
            }
            
            // 4. Buat record pembayaran
            $pembayaran = Payment::create([
                'user_id' => $student->id,
                'student_spp_id' => $studentSpp->id,
                'nominal_bayar' => $request->input('nominal_bayar'),
                'sisa_tagihan' => 0, // Diupdate nanti
                'metode_pembayaran' => $request->input('metode_pembayaran'),
                'dibayar_oleh' => $request->input('dibayar_oleh'),
                'keterangan' => $request->input('keterangan') ?? null,
                'status' => 'success',
                'tanggal_bayar' => now()
            ]);
            
            // 5. Alokasikan pembayaran ke bulan-bulan yang belum lunas
            $sisaBayar = $request->input('nominal_bayar'); 
            $bulanTerbayar = [];
            
            foreach ($bulanBelumBayar as $bulan) {
                if ($sisaBayar <= 0) break;
                
                $sisaTagihanBulan = $bulan->sisa_utang > 0 ? $bulan->sisa_utang : $bulan->nominal;
                $nominalDibayar = min($sisaBayar, $sisaTagihanBulan);
                
                // Simpan detail pembayaran
                PaymentDetail::create([
                    'payment_id' => $pembayaran->id,
                    'spp_bulan_id' => $bulan->id,
                    'nominal_dibayar' => $nominalDibayar
                ]);
                
                // Update status bulan
                $sisaSetelahBayar = $sisaTagihanBulan - $nominalDibayar;
                
                if ($sisaSetelahBayar <= 0) {
                    $bulan->status = 'paid';
                    $bulan->tanggal_dibayar = now();
                    $bulan->sisa_utang = 0;
                } else {
                    $bulan->status = 'partial';
                    $bulan->sisa_utang = $sisaSetelahBayar;
                }
                
                $bulan->save();
                
                $sisaBayar -= $nominalDibayar;
                $bulanTerbayar[] = [
                    'bulan' => $bulan->bulan,
                    'tahun' => $bulan->tahun,
                    'nominal' => $nominalDibayar,
                    'nama_bulan' => $bulan->nama_bulan
                ];
            }
            
            // 6. Update sisa pembayaran
            $pembayaran->sisa_tagihan = $sisaBayar;
            $pembayaran->save();
            
            // 7. Handle overpayment (kelebihan bayar)
            if ($sisaBayar > 0) {
                OverPayment::create([
                    'student_spp_id' => $studentSpp->id,
                    'pembayaran_id' => $pembayaran->id,
                    'nominal' => $sisaBayar,
                    'status' => 'deposit',
                    'nominal_terpakai' => 0
                ]);
            }
            
            DB::commit();
            
            $message = $this->generateSuccessMessage($bulanTerbayar, $sisaBayar);
            
            return redirect()
                ->route('admin.bill.detail', $student->id)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     private function generateSuccessMessage($bulanTerbayar, $sisaBayar)
    {
        $message = "✅ Pembayaran berhasil!\n\n";
        $message .= "Detail pembayaran:\n";
        
        foreach ($bulanTerbayar as $b) {
            $message .= "• {$b['nama_bulan']} {$b['tahun']}: Rp " . number_format($b['nominal'], 0, ',', '.') . "\n";
        }
        
        if ($sisaBayar > 0) {
            $message .= "\n💰 Sisa pembayaran: Rp " . number_format($sisaBayar, 0, ',', '.') . " (tersimpan sebagai deposit)";
        } else {
            $message .= "\n🎉 Semua tagihan lunas!";
        }
        
        return $message;
    }
    // Riwayat pembayaran
    public function riwayat($studentId)
    {
        $payment = Payment::where('student_spp_id', $studentId)
            ->with('details.sppBulan')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('payment.riwayat', compact('payment'));
    }
}
