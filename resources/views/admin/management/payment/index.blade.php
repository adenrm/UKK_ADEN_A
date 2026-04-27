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
                @if (!empty($payments))
                <tr class="border-b hover:bg-gray-50">
                    <td colspan="7" class="py-3 px-4 text-center text-gray-500">
                        Tidak ada data riwayat pembayaran.
                    </td>
                </tr>
                @else
                @foreach ($payments as $item)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">
                    {{ $item->user->name ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->nominal_bayar ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->metode_pembayaran ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    @if ($item->status == 'success')
                        <span class="text-green-500">Success</span>
                    @elseif ($item->status == 'failed')
                        <span class="text-red-500">Failed</span>
                    @elseif ($item->status == 'pending')
                        <span class="text-yellow-500">Pending</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    {{ $item->keterangan ?? 'Tidak ada Keterangan' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->dibayar_oleh ?? 'N/A' }}
                </td>
                <td class="py-3 px-4">
                    {{ $item->created_at->diffForHumans() ?? 'N/A' }}
                </td>
            </tr>
            @endforeach
                @endif
            </tbody>
            </table>

        </div>
    </div>
@endsection