@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="p-5 bg-white rounded-md shadow-md h-full">
        <h1 class="text-2xl font-bold">Log Aktivitas</h1>
        <div class="mt-5">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Aktivitas</th>
                        <th class="px-4 py-2">Jenis</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Kegiatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($log as $item)
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2">
                            @php
                               $nominal = intval($item->properties->implode('nominal_bayar'));
                            @endphp
                            @if ($item->subject_type === 'App\Models\Payment' && $nominal > 0)
                                {{$item->properties->implode('dibayar_oleh')}} membayar sebesar {{ 'Rp' . number_format($nominal, 0, ',', '.') }}
                            @elseif ($item->subject_type === 'App\Models\Payment' && $item->event === 'updated')
                                Memperbarui data pembayaran
                            @elseif ($item->subject_type === 'App\Models\ClassGrade')
                                Memperbarui data kelas
                            @elseif ($item->subject_type === 'App\Models\SppBulan' && $item->event === 'updated')
                                Memperbarui data SPP Perbulan
                            @elseif ($item->subject_type === 'App\Models\SppBulan' && $item->event === 'created')
                                Membuat Data SPP Perbulan
                            @elseif ($item->subject_type === 'App\Models\StudentSpp' && $item->event === 'created')
                                Membuat Data SPP Siswa
                            @elseif ($item->subject_type === 'App\Models\StudentSpp' && $item->event === 'updated')
                                Memperbarui data SPP Siswa
                            @elseif ($item->subject_type === 'App\Models\UserData')
                                Memperbarui data siswa
                            @elseif ($item->subject_type === 'App\Models\Spps' && $item->event === 'created')
                                Membuat Data SPP
                            @elseif ($item->subject_type === 'App\Models\Spps' && $item->event === 'updated')
                                Memperbarui Data SPP
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if ($item->subject_type === 'App\Models\Payment')
                                Pembayaran
                            @elseif ($item->subject_type === 'App\Models\ClassGrade')
                                Data Kelas
                            @elseif ($item->subject_type === 'App\Models\SppBulan')
                                Data SPP Perbulan
                            @elseif ($item->subject_type === 'App\Models\Spps')
                                Data SPP
                            @elseif ($item->subject_type === 'App\Models\StudentSpp')
                                Data SPP Siswa
                            @elseif ($item->subject_type === 'App\Models\UserData')
                                Data Siswa
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            {{$item->created_at->locale('id')->diffForHumans()}}
                        </td>
                        <td class="px-4 py-2">
                            {{$item->description}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
        </div>
    </div>
@endsection