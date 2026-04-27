@extends('layouts.admin')

@section('title', 'Manajemen Siswa')

@section('content')
    <div class="p-5">
        <div class="flex flex-col gap-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                <div class="flex justify-between items-center">
                    <div class="">
                            <a href="{{ route('admin.management') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
                        <h3 class="text-4xl font-medium mt-5">Manajemen Siswa</h3>
                        <p class="text-lg">Daftar siswa yang terdaftar di sistem.</p>
                       @if(session('success'))
    <div id="alert-success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded fixed top-5 right-5 z-50 transition-opacity duration-500" role="alert">
        <div class="flex justify-between items-center">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="ml-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
@endif
                    </div>
                    <div class="">
                        <a href="{{ route('admin.student.create') }}" class=" cursor-crosshair bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                            Tambah Siswa
                        </a>
                    </div>
                </div>
                <table class="table-auto border-collapse border border-slate-400 w-full">
                    <tr>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            No
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            NISN
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            NIS
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            Nama
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            Kelas
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            Rayon
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            No Telp
                        </th>
                        <th class="border border-slate-300 bg-slate-100 p-2">
                            Aksi
                        </th>
                    </tr>
                    @foreach ($students as $student)
                    <tr class="odd:bg-white even:bg-slate-50 hover:bg-blue-50 transition-colors">
                            <th class="px-6 py-4">
                                {{ $loop->iteration }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $student->UserData->nisn ?? 'Null' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $student->UserData->nis ?? 'Null' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $student->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $student->UserData->ClassGrade->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $student->UserData->rayon ?? 'Null' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $student->UserData->phone ?? 'Null' }}
                            </td>
                            <td class=" py-4 flex gap-2">
                                <a href="{{ route('admin.student.edit', $student) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                    Edit
                                </a>
                                <form onsubmit="return confirm('Serius nih mau hapus?')" action="{{ route('admin.student.destroy', $student) }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <input type="submit" value="Delete" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md cursor-pointer">
                                </form>
                            </td>
                        </tr>
                        @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
