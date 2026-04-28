@extends('layouts.admin')

@section('title', 'Tiket')

@section('content')
    <div class="p-5">
        <div class="bg-white rounded-md shadow-md p-5">
            <h1 class="text-2xl font-medium my-5">Tiket</h1>
            <table class="table-auto border-collapse border border-slate-400 w-full">
                <thead>
                    <tr>
                        <th class="border border-slate-400 px-4 py-2">
                            Nama
                        </th>
                        <th class="border border-slate-400 px-4 py-2">
                            Keterangan
                        </th>
                        <th class="border border-slate-400 px-4 py-2">
                            Status
                        </th>
                        <th class="border border-slate-400 px-4 py-2">
                            Waktu
                        </th>
                    </tr>
                </thead>
              <tbody class="bg-slate-100">
    @forelse ($tickets as $item)
    <tr class="border border-slate-400 px-4 py-2">
        <td class="border border-slate-400 px-4 py-2">
            {{ $item->user->name ?? 'Tidak diketahui' }}
        </td>
        <td class="border border-slate-400 px-4 py-2">
            {{ $item->keterangan ?? '-' }}
        </td>
        <td class="border border-slate-400 px-4 py-2">
            <form action="{{ route('admin.ticket.updateStatus', $item->id) }}" method="POST">
                @csrf
                @if ($item->status === 1)    
                <select name="status" id="" onchange="this.form.submit()" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="pending" disabled selected>Pending</option>
                    <option value="rejected">Ditolak</option>
                    <option value="accepted">Diterima</option>
                </select>
                @else
                    @if ($item->status === 2)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Accepted</span>
                    @elseif ($item->status === 3)
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Rejected</span>
                    @endif
                @endif
            </form>
        </td>
        <td class="border border-slate-400 px-4 py-2">
            {{ $item->created_at->diffForHumans() ?? 'Tidak ada data waktu' }}
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="border border-slate-400 px-4 py-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <!-- Icon Kosong -->
                <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 font-medium">Belum ada data tiket</p>
                <p class="text-gray-400 text-sm mt-1">Tiket akan muncul di sini setelah ada yang membuat</p>
            </div>
        </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
@endsection