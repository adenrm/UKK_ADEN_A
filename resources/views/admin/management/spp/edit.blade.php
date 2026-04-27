@extends('layouts.admin')

@section('title', 'Edit SPP')

@section('content')
    <div class="p-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                  <a href="{{ route('admin.spp.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
        <h3 class="text-4xl font-medium mt-5">Edit SPP</h3>
        <form action="{{ route('admin.spp.update', $spp) }}" method="post">
            @csrf
            @method('put')
         <table class="mt-5">
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td>
                    <input value="{{ $spp->tahun_ajaran }}" required required type="text" name="tahun_ajaran" id="tahun_ajaran" class="w-full p-2 border border-gray-300 rounded-md">
                </td>
            </tr>
             <tr>
                 <td>Keterangan</td>
                 <td>:</td>
                 <td>
                     <input value="{{ $spp->keterangan }}" required required type="text" name="keterangan" id="keterangan" class="w-full p-2 border border-gray-300 rounded-md">
                 </td>
             </tr>
                        <tr>
                            <td>Nominal</td>
                            <td>:</td>
                            <td><input value="{{ $spp->nominal_per_bulan }}" required type="number" name="nominal_per_bulan" id="nominal_per_bulan" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-black hover:text-white focus:text-white p-2 rounded-md">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </table>
        </form>
    </div>
    </div>
@endsection