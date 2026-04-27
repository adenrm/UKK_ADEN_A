@extends('layouts.admin')

@section('title', 'Form Pembayaran SPP')

@section('content')
<div class="p-5">
    <div class="bg-white p-5 mx-auto justify-items-center rounded-lg shadow-xl w-full max-w-md">
           
            <h3 class="text-4xl font-medium mt-5">Form Pembayaran SPP</h3>
            <p class="text-lg">Isi form pembayaran SPP untuk {{ $studentSpp->user->name }}</p>

            <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Form Pembayaran SPP</h4>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Siswa:</strong> {{ $studentSpp->user->name }}<br>
                        <strong>Kelas:</strong> {{ $studentSpp->user->UserData->ClassGrade->name ?? '-' }}<br>
                        <strong>SPP/Bulan:</strong> Rp {{ number_format($studentSpp->spp->nominal_per_bulan) }}
                    </div>
                    
                    <form action="{{ route('admin.payment.store', $studentSpp->id) }}" method="POST">
                        @csrf
                        <table>
                            <tr>
                                <td>
                                    <label>Nominal Pembayaran</label>
                                </td>
                                <td>:</td>
                                <td>    
                                    <input type="number" name="nominal_bayar" class="w-full p-2 border border-gray-300 rounded-md" required>
                                    <small class="text-muted">Minimal Rp 10.000</small>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Metode Pembayaran</label>
                                </td>
                                <td>:</td>
                                <td>
                                    <select name="metode_pembayaran" class="w-full p-2 border border-gray-300 rounded-md" required>
                                        <option value="tunai">Tunai</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Dibayar Oleh</label>
                                </td>
                                <td>:</td>
                                <td>
                                    <input type="text" name="dibayar_oleh" class="w-full p-2 border border-gray-300 rounded-md" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Keterangan (Opsional)</label>
                                </td>
                                <td>:</td>
                                <td>
                                    <textarea name="keterangan" class="w-full p-2 border border-gray-300 rounded-md" rows="2"></textarea>
                                </td>
                            </tr>
                            <tr >
                                <td>
                                     <a href="{{ route('admin.bill.index') }}"  class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                        Kembali
                                    </a>
                                </td>
                                <td class="text-right"  colspan="2">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-black hover:text-white focus:text-white p-2 rounded-md">Bayar</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
       </div>
@endsection
