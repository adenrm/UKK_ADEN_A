@extends('layouts.app')

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
                    <th class="py-3 px-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y H:i') }}</td>
                    <td class="py-3 px-4 text-right">Rp{{ number_format($item->nominal_bayar, 0, ',', '.') }}</td>
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
                        @if($item->status == 'success')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Sukses</span>
                        @elseif($item->status == 'failed')
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Gagal</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Pending</span>
                        @endif
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