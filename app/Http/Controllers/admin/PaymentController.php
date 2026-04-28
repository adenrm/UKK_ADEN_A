<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\OverPayment;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SppBulan;
use App\Models\StudentSpp;
use App\Models\User;
use App\Services\SppPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;
    
    public function __construct(SppPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    public function index()
    { 
        $payments = Payment::all();
        return view('admin.management.payment.index', compact('payments'));
    }
    
    public function create($id)
    {
        $student = User::findOrFail($id);
        $studentSpp = StudentSpp::findOrFail($student->studentSpp->id);
        if (Auth::user()->level == 'admin') {
            return view('admin.bill.payment.create', compact('studentSpp'));
        }
        return view('staff.payment.create', compact('studentSpp'));
    }

    public function detail($id)
    {
        $studentSppId = StudentSpp::where('user_id', $id)->first();
        $SppBulanId = SppBulan::where('student_spp_id', $studentSppId->id)->first();
        $SppBulan = SppBulan::where('student_spp_id', $studentSppId->id)->get();
        $sisaTabungan = 0;
        $OverPayment = OverPayment::where('student_spp_id', $studentSppId->id)->first();
        if ($OverPayment) {
            $sisaTabungan = $OverPayment->nominal;
        }
        return view('admin.bill.payment.detail', compact('SppBulan', 'sisaTabungan', 'OverPayment'));
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
            $studentSpp = StudentSpp::with('user')->findOrFail($id);
            $student = $studentSpp->user;
            
            if (!$studentSpp) {
                throw new \Exception('Data SPP tidak ditemukan.');
            }
            
            $bulanBelumBayar = SppBulan::where('student_spp_id', $studentSpp->id)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->get();
            
            if ($bulanBelumBayar->isEmpty()) {
                throw new \Exception('Semua tagihan sudah lunas!');
            }
            
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
            
            $sisaBayar = $request->input('nominal_bayar'); 
            $bulanTerbayar = [];
            
            foreach ($bulanBelumBayar as $bulan) {
                if ($sisaBayar <= 0) break;
                
                $sisaTagihanBulan = $bulan->sisa_utang > 0 ? $bulan->sisa_utang : $bulan->nominal;
                $nominalDibayar = min($sisaBayar, $sisaTagihanBulan);
                
                PaymentDetail::create([
                    'payment_id' => $pembayaran->id,
                    'spp_bulan_id' => $bulan->id,
                    'nominal_dibayar' => $nominalDibayar
                ]);
                
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
            
            $pembayaran->sisa_tagihan = $sisaBayar;
            $pembayaran->save();
            if ($sisaBayar > 0) {
                OverPayment::create([
                    'payment_id' => $pembayaran->id,
                    'student_spp_id' => $studentSpp->id,
                    'nominal' => $sisaBayar,
                    'status' => 'deposit',
                    'nominal_terpakai' => 0
                ]);
            }
            
            DB::commit();
            
            $message = $this->generateSuccessMessage($bulanTerbayar, $sisaBayar);
                return redirect()
                    ->route('bill.index', $student->id)
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
