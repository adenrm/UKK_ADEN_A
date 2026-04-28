@extends('layouts.admin')

@section('title', 'Edit Petugas')

@section('content')
    <div class="p-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                  <a href="{{ route('admin.staff.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
        <h3 class="text-4xl font-medium mt-5">Edit Petugas</h3>
        <form action="{{ route('admin.staff.update', $user) }}" method="post">
            @csrf
            @method('put')
         <table class="mt-5">
             <tr>
                 <td>Nama Petugas</td>
                 <td>:</td>
                 <td>
                     <input value="{{ $user->name }}" required required type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md">
                 </td>
             </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><input value="{{ $user->email }}" required type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>:</td>
                            <td><input value="{{ $user->password }}" required type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-black hover:text-white focus:text-white p-2 rounded-md">
                                    Update
                                </button>
                            </td>
                        </tr>
                    </table>
        </form>
    </div>
    </div>
@endsection