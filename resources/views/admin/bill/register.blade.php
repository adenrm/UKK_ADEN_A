@extends('layouts.admin')

@section('title', 'Register SPP')

@section('content')
<div class="p-5">
    <div class="bg-white p-5 mx-auto justify-items-center rounded-lg shadow-xl w-full max-w-md">
        <h3 class="text-4xl font-medium mt-5">Register SPP</h3>
        <p>Hallo {{ $student->name }}</p>

        <div class="mt-5">
            <form action="{{ route('admin.bill.register.spp') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ $student->id }}">
                <table>
                    <tr>
                        <td>SPP</td>
                        <td>:</td>
                        <td>
                            <select name="spp_id" id="spp_id" class="w-full p-2 border border-gray-300 rounded-md" required>
                                <option value="" selected disabled>Pilih SPP</option>
                                @foreach ($spps as $item)
                                    <option value="{{ $item->id }}">{{ $item->keterangan }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Tahun Masuk</td>
                        <td>:</td>
                        <td>
                            <input type="number" name="tahun_masuk" id="tahun_masuk" class="w-full p-2 border border-gray-300 rounded-md" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="submit" class="bg-blue-500 text-white w-full py-2 rounded-md">Register</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
@endsection