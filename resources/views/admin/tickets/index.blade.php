@extends('layouts.admin')

@section('title', 'Tiket')

@section('content')
    <div class="p-5">
        <div class="bg-white rounded-md shadow-md p-5">
            <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Kembali
            </a>
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
                    @foreach ($tickets as $item)
                    <tr class="border border-slate-400 px-4 py-2">
                        <td class="border border-slate-400 px-4 py-2">
                            {{ $item->user->name }}
                        </td>
                        <td class="border border-slate-400 px-4 py-2">
                            {{ $item->keterangan }}
                        </td>
                        <td class="border border-slate-400 px-4 py-2">
                            <form action="{{ route('admin.ticket.updateStatus', $item->id) }}" method="POST">
                                @csrf
                                @if ($item->status === 1)    
                                <select name="status" id="" onchange="this.form.submit()"  class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="pending" disabled selected>Pending</option>
                                    <option value="rejected">Ditolak</option>
                                    <option value="accepted">Diterima</option>
                                </select>
                                @else
                                    {{ $item->status === 2 ? 'Accepted' : 'Rejected' }}
                                @endif
                            </form>
                        </td>
                        <td class="border border-slate-400 px-4 py-2">
                            {{ $item->created_at->diffForHumans() ?? 'Tidak ada data waktu' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection