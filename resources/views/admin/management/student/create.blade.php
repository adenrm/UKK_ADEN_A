@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('content')
     <div class="p-5">
        <div class="flex flex-col gap-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                <a href="{{ route('admin.student.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
                <h3 class="text-4xl font-medium mt-5">Tambah Siswa</h3>
                <p class="text-lg">Tambah data siswa baru.</p>
                <form action="{{ route('admin.student.store') }}" method="post" class="mt-5">
                    @csrf
                    <table>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><input required type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>:</td>
                            <td><input required type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Nama Siswa</td>
                            <td>:</td>
                            <td>
                                <input required type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md">
                            </td>
                        </tr>
                        <tr>
                            <td>NISN</td>
                            <td>:</td>
                            <td>
                                <input required type="number" name="nisn" id="nisn" class="w-full p-2 border border-gray-300 rounded-md">
                                @error('nisn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    @if(session('error_nisn'))
                        <p class="text-red-500 text-sm mt-1">{{ session('error_nisn') }}</p>
                    @endif
                            </td>
                            
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td>:</td>
                            <td>
                                <input required type="number" name="nis" id="nis" class="w-full p-2 border border-gray-300 rounded-md">
                                @error('nis')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    @if(session('error_nis'))
                        <p class="text-red-500 text-sm mt-1">{{ session('error_nis') }}</p>
                    @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td>
                                <select name="class_id" id="class_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                                <option value="" selected disabled>Pilih Kelas</option>
                                @foreach ($classes as $item)
                                    <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Rayon</td>
                            <td>:</td>
                            <td><input required type="text" name="rayon" id="rayon" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>No Telp.</td>
                            <td>:</td>
                            <td><input required type="number" name="phone" id="phone" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Program</td>
                            <td>:</td>
                            <td>
                                <select name="program" id="program" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="" selected disabled>Opsi</option>
                                    <option value="unggulan">Unggulan</option>
                                    <option value="reguler">Reguler</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-black hover:text-white focus:text-white p-2 rounded-md">
                                    Tambah
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
