@extends('layouts.admin')

@section('title', 'Tambah Siswa')

@section('content')
     <div class="p-5">
        <div class="flex flex-col gap-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                <a href="{{ route('admin.staff.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
                <h3 class="text-4xl font-medium mt-5">Tambah Petugas</h3>
                <p class="text-lg">Tambah data petugas baru.</p>
                <form action="{{ route('admin.staff.store') }}" method="post" class="mt-5">
                    @csrf
                    <table>
                        <tr>
                            <td>Nama Petugas</td>
                            <td>:</td>
                            <td>
                                <input required type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md">
                            </td>
                        </tr>
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
                            <td>Konfirmasi Password</td>
                            <td>:</td>
                            <td><input required type="password" name="password_confirmation" id="password_confirmation" class="w-full p-2 border border-gray-300 rounded-md"></td>
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
