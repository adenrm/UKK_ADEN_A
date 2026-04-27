@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Form Pembayaran SPP</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Siswa:</strong> {{ $studentSpp->user->name }}<br>
                        <strong>Kelas:</strong> {{ $studentSpp->user->userData->class->name ?? '-' }}<br>
                        <strong>SPP/Bulan:</strong> Rp {{ number_format($studentSpp->spp->nominal_per_bulan) }}
                    </div>
                    
                    <form action="{{ route('payment.store', $studentSpp->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label>Nominal Pembayaran</label>
                            <input type="number" name="nominal_bayar" class="form-control" required>
                            <small class="text-muted">Minimal Rp 10.000</small>
                        </div>
                        
                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-control" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label>Dibayar Oleh</label>
                            <input type="text" name="dibayar_oleh" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Keterangan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection