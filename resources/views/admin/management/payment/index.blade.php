@extends('layouts.admin')

@section('title', 'History Pembayaran')

@section('content')
    <div class="p-5">
        <div class="bg-white p-5 w-full rounded-md shadow-md">
            <a href="{{ route('admin.management') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Kembali
            </a>
            <h1 class="text-2xl font-medium my-5">Riwayat Pembayaran</h1>
            <table class="min-w-full border">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">
                            Nama
                        </th>
                    <th class="py-3 px-4 text-left">
                        Nominal
                    </th>
                    <th class="py-3 px-4 text-left">
                        Rayon
                    </th>
                    <th class="py-3 px-4 text-left">
                        Program
                    </th>
                    <th class="py-3 px-4 text-left">
                        Metode
                    </th>
                    <th class="py-3 px-4 text-left">
                        Status
                    </th>
                    <th class="py-3 px-4 text-left">
                        Keterangan
                    </th>
                    <th class="py-3 px-4 text-left">
                        Pembayar
                    </th>
                    <th class="py-3 px-4 text-left">
                        Waktu
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $item)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">
                    {{ $item->user->name ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ 'Rp' . number_format($item->nominal_bayar, 0, ',', '.') ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->user->UserData->rayon ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->user->UserData->program ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                       @if($item->metode_pembayaran == 'tunai')
                            💵 Tunai
                        @elseif($item->metode_pembayaran == 'transfer')
                            💸 Transfer
                        @else
                            📱 QRIS
                        @endif
                </td>
                <td class="py-3 px-4">
                    @if ($item->status == 'success')
                        <span class="text-green-500 bg-green-100 px-2 py-2 rounded-md">Success</span>
                    @elseif ($item->status == 'failed')
                        <span class="text-red-500 bg-red-100 px-2 py-2 rounded-md">Failed</span>
                    @elseif ($item->status == 'pending')
                        <span class="text-yellow-500 bg-yellow-100 px-2 py-2 rounded-md">Pending</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    {{ $item->keterangan ?? 'Tidak ada Keterangan' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->dibayar_oleh ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->created_at->locale('id')->diffForHumans() ?? 'N/A' }}
                </td>
            </tr>
              @empty
            <tr>
                <td colspan="7" class="border border-slate-400 px-4 py-8 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <!-- Icon Kosong -->
                        <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada data Pembayaran</p>
                        <p class="text-gray-400 text-sm mt-1">Pembayaran akan muncul di sini setelah ada yang membuatnya</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
            </table>

        </div>
    </div>
@endsection