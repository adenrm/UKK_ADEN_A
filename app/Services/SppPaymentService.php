<?php

namespace App\Services;

use App\Models\SiswaSpp;
use App\Models\SppBulan;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\Overpayment;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\StudentSpp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SppPaymentService
{
    /**
     * Proses pembayaran dengan auto-alokasi
     */
    public function processPayment($studentSppId, $nominalBayar, $dataPembayaran)
    {
        DB::beginTransaction();
        
        try {
            // Cek siswa SPP
            $siswaSpp = StudentSpp::with(['spp', 'user'])->findOrFail($studentSppId);
            
            // Ambil bulan yang belum lunas
            $bulanBelumBayar = SppBulan::where('student_spp_id', $studentSppId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->get();
            
            if ($bulanBelumBayar->isEmpty()) {
                throw new \Exception('Semua tagihan sudah lunas!');
            }
            
            // Buat record pembayaran
            $pembayaran = Payment::create([
                'user_id' => $siswaSpp->user_id,
                'student_spp_id' => $studentSppId,
                'nominal_bayar' => $nominalBayar,
                'metode_pembayaran' => $dataPembayaran['metode_pembayaran'],
                'dibayar_oleh' => $dataPembayaran['dibayar_oleh'],
                'keterangan' => $dataPembayaran['keterangan'] ?? null,
                'status' => 'success',
                'tanggal_bayar' => now()
            ]);
            
            $sisaBayar = $nominalBayar;
            $bulanTerbayar = [];
            
            // Alokasikan pembayaran
            foreach ($bulanBelumBayar as $bulan) {
                if ($sisaBayar <= 0) break;
                
                $sisaTagihanBulan = $bulan->sisa_utang > 0 ? $bulan->sisa_utang : $bulan->nominal;
                $nominalDibayar = min($sisaBayar, $sisaTagihanBulan);
                
                // Simpan detail
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
                    'nominal' => $nominalDibayar
                ];
            }
            
            // Update sisa tagihan di pembayaran
            $pembayaran->sisa_tagihan = $sisaBayar;
            $pembayaran->save();
            
            // Handle overpayment
            if ($sisaBayar > 0) {
                $this->handleOverpayment($studentSppId, $pembayaran->id, $sisaBayar);
            }
            
            DB::commit();
            
            return [
                'success' => true,
                'message' => $this->generatePaymentMessage($bulanTerbayar, $sisaBayar),
                'pembayaran' => $pembayaran,
                'bulan_terbayar' => $bulanTerbayar,
                'sisa' => $sisaBayar
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function handleOverpayment($siswaSppId, $pembayaranId, $nominal)
    {
        Overpayment::create([
            'siswa_spp_id' => $siswaSppId,
            'pembayaran_id' => $pembayaranId,
            'nominal' => $nominal,
            'status' => 'deposit',
            'nominal_terpakai' => 0
        ]);
    }
    
    private function generatePaymentMessage($bulanTerbayar, $sisa)
    {
        $message = "✅ Pembayaran berhasil!\n\n";
        $message .= "Detail pembayaran:\n";
        
        foreach ($bulanTerbayar as $b) {
            $message .= "• " . $this->getBulanName($b['bulan']) . " {$b['tahun']}: Rp " . number_format($b['nominal']) . "\n";
        }
        
        if ($sisa > 0) {
            $message .= "\n💰 Sisa pembayaran: Rp " . number_format($sisa) . " (tersimpan sebagai deposit)";
        } else {
            $message .= "\n🎉 Semua tagihan lunas!";
        }
        
        return $message;
    }
    
    private function getBulanName($bulan)
    {
        $nama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $nama[$bulan];
    }
}