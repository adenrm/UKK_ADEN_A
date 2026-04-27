@extends('layouts.admin')

@section('title', 'Tambah SPP')

@section('content')
     <div class="p-5">
        <div class="flex flex-col gap-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                <a href="{{ route('admin.spp.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
                <h3 class="text-4xl font-medium mt-5">Tambah SPP</h3>
                <p class="text-lg">Tambah data SPP baru.</p>
                <form action="{{ route('admin.spp.store') }}" method="post" class="mt-5">
                    @csrf
                    <table>
                        <tr>
                            <td>Tahun Ajaran</td>
                            <td>:</td>
                            <td><input required type="text" name="tahun_ajaran" id="year" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td>
                                <input required type="text" name="keterangan" id="keterangan" class="w-full p-2 border border-gray-300 rounded-md">
                            </td>
                        </tr>
                        <tr>
                            <td>Nominal</td>
                            <td>:</td>
                            <td><input required type="number" name="nominal_per_bulan" id="amount" class="w-full p-2 border border-gray-300 rounded-md"></td>
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
