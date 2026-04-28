<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SppBulan;
use App\Models\StudentSpp;
use App\Services\SppPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;
    
    public function __construct(SppPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    public function index()
    {
        $payments = Payment::where('user_id', Auth::user()->id)->get();
        return view('student.payment.index', compact('payments'));
    }

    public function create($studentSppId)
    {
        $studentSpp = StudentSpp::with(['user.userData', 'spp'])->findOrFail($studentSppId);
        return view('pembayaran.form', compact('studentSpp'));
    }
    
    // Proses pembayaran
    public function store(Request $request, $studentSppId)
    {
        $request->validate([
            'nominal_bayar' => 'required|numeric|min:10000',
            'metode_pembayaran' => 'required|in:tunai,transfer,virtual_account,qris',
            'dibayar_oleh' => 'required|string|max:100'
        ]);
        
        try {
            $result = $this->paymentService->processPayment(
                $studentSppId,
                $request->nominal_bayar,
                $request->only(['metode_pembayaran', 'dibayar_oleh', 'keterangan'])
            );
            
            if ($request->ajax()) {
                return response()->json($result);
            }
            
            return redirect()->route('bill.index', $studentSppId)
                ->with('success', $result['message']);
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Pembayaran gagal: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Riwayat pembayaran
    public function riwayat($studentSppId)
    {
        $payment = Payment::where('student_spp_id', $studentSppId)
            ->with('details.sppBulan')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('payment.riwayat', compact('payment'));
    }
}
