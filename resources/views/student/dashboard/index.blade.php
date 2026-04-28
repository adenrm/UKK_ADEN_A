@extends('layouts.app')

    @section('title', 'Dashboard')

@section('content')
<div class="p-5">
    <div class="bg-white shadow-md rounded-md p-5">
        <h3 class="text-3xl font-medium">Selamat Datang, {{ Auth::user()->name }}!</h3>
        
    </div>
    <div class="flex gap-5 mt-5">
        <div class="w-full flex flex-col gap-5">
            <div class=" bg-white shadow-md rounded-md h-[35vh] p-5">
                <h4 class="text-2xl font-medium text-center mb-3">Informasi Aplikasi</h4>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam laudantium officia perspiciatis aspernatur quia? Obcaecati impedit consequatur, enim ad eaque temporibus voluptatibus sunt odit, molestias eveniet dolores! Doloribus, aspernatur ea in suscipit animi exercitationem similique obcaecati perspiciatis tempora voluptas reprehenderit praesentium sit facilis, corrupti iure, recusandae eum dolor dolore possimus.</p>
                
            </div>
            <div class=" bg-white shadow-md rounded-md h-[35vh] p-5">

                <h4 class="text-2xl font-medium text-center mb-3">Informasi SPP Anda {{ Auth::user()->studentSpp->tahun_masuk ?? '' }}</h4>
                
                <table class="w-full rounded-md p-4">
                    @php
                    // Safe checking untuk menghindari error
                    $studentSpp = Auth::user()->studentSpp;
                    $sppBulan = $studentSpp ? $studentSpp->sppBulan : collect();
                    
                    $totalTagihan = $sppBulan->sum('nominal');
                    $totalBayar = $sppBulan->where('status', 'paid')->sum('nominal');
                    $sisa = $totalTagihan - $totalBayar;
                @endphp
                <tr>
                    <td class="font-medium my-2">Kategori</td>
                    <td>:</td>
                    <td>{{ $studentSpp->spp->keterangan ?? 'Belum Ada' }}</td>
                </tr>
                <tr>
                    <td class="font-medium my-2">Total Tagihan <td>:</td>
                    <td>{{ $totalTagihan ?? '' }}</td>
                </tr>
                <tr>
                    <td class="font-medium my-2">Total Sudah diBayar</td>
                    <td>:</td>
                    <td>{{ $totalBayar ?? '' }}</td>
                </tr>
                <tr>
                    <td class="font-medium my-2">Sisa Tagihan</td>
                    <td>:</td>
                    <td>{{ $sisa ?? '' }}</td>
                </tr>
            </table>
        </div>
        </div>

        <div class="w-full bg-white shadow-md rounded-md p-5">
            <h4 class="text-2xl font-medium text-center mb-3">Riwayat Pembayaran</h4>
            <table class="w-full rounded-md p-4">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="font-medium">Tanggal</th>
                        <th class="font-medium">Nominal</th>
                        <th class="font-medium">Metode</th>
                        <th class="font-medium">Pembayar</th>
                        <th class="font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($payments as $payment)
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-blue-50 transition-colors border-b border-gray-200">
                        <td class="px-6 py-4">
                            @php
                                $tanggal = \Carbon\Carbon::parse($payment->tanggal_bayar)
                            @endphp
                            {{ $tanggal->format('d F Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-right">Rp{{ number_format($payment->nominal_bayar, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                             @if($payment->metode_pembayaran == 'tunai')
                            💵 Tunai
                        @elseif($payment->metode_pembayaran == 'transfer')
                            💸 Transfer
                        @else
                            📱 QRIS
                        @endif
                        </td>
                        <td class="px-6 py-4">{{ $payment->dibayar_oleh ?? '' }}</td>
                        <td class="px-6 py-4">
                            @if($payment->status == 'success')
                                <span class="text-green-500 font-medium bg-green-100 px-2 py-1 rounded-md">Sukses</span>
                            @elseif($payment->status == 'failed')
                                <span class="text-red-500 font-medium bg-red-100 px-2 py-1 rounded-md">Gagal</span>
                            @else
                                <span class="text-yellow-500 font-medium bg-yellow-100 px-2 py-1 rounded-md">Pending</span>
                            @endif
                    
                        </td>
                    </tr>
                    @empty
                    <tr class="bg-gray-300">
                        <td colspan="5" class="px-6 py-4 text-center">Tidak ada riwayat pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <a href="{{ route('student.payment.index') }}" class="text-center text-blue-500  hover:underline">
                Lihat Riwayat Pembayaran
            </a>
        </div>
    </div>
</div>
@endsection
