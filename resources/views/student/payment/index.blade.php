@extends('layouts.siswa')

@section('title', 'Riwayat Pembayaran SPP')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Riwayat Pembayaran SPP</h1>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Tanggal Bayar</th>
                    <th class="py-3 px-4 text-right">Nominal</th>
                    <th class="py-3 px-4 text-left">Metode</th>
                    <th class="py-3 px-4 text-left">Dibayar Oleh</th>
                    <th class="py-3 px-4 text-left">Untuk Bulan</th>
                    <th class="py-3 px-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 text-right">Rp {{ number_format($item->nominal_bayar, 0, ',', '.') }}</td>
                    <td class="py-3 px-4">
                        @if($item->metode_pembayaran == 'tunai')
                            💵 Tunai
                        @elseif($item->metode_pembayaran == 'transfer')
                            💸 Transfer
                        @else
                            📱 QRIS
                        @endif
                    </td>
                    <td class="py-3 px-4">{{ $item->dibayar_oleh }}</td>
                    <td class="py-3 px-4">
                        @foreach($item->details as $detail)
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs m-1">
                                {{ $detail->sppBulan->nama_bulan }} {{ $detail->sppBulan->tahun }}
                            </span>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✓ Success</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-500">Belum ada riwayat pembayaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection