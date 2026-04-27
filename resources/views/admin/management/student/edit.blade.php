@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('content')
    <div class="p-5">
            <div href="#" class="bg-white rounded-md p-5 shadow-md">
                <a href="{{ route('admin.student.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    Kembali
                </a>
        <h3 class="text-4xl font-medium mt-5">Edit Siswa</h3>
        <form action="{{ route('admin.student.update', $user) }}" method="post">
            @csrf
            @method('put')
         <table>
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
                            <td>Nama Siswa</td>
                            <td>:</td>
                            <td>
                                <input value="{{ $user->name }}" required required type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded-md">
                            </td>
                        </tr>
                        <tr>
                            <td>NISN</td>
                            <td>:</td>
                            <td>
                                <input value="{{ $user->UserData->nisn ?? '' }}" required type="number" name="nisn" id="nisn" class="w-full p-2 border border-gray-300 rounded-md">
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
                                <input value="{{ $user->UserData->nis ?? '' }}" required type="number" name="nis" id="nis" class="w-full p-2 border border-gray-300 rounded-md">
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
                                <option value="" disabled>Pilih Kelas</option>
                                @if (isset($user->UserData->class_id))
                                @foreach ($classes as $item)
                                <option value="{{ $item->id }}" {{ $user->UserData->class_id == $item->id ?? '' ? 'selected' : ''  }}>
                                    {{ $item->name }}
                                </option>
                                @endforeach
                                @else
                                @foreach ($classes as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @error('class_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>Rayon</td>
                            <td>:</td>
                            <td><input value="{{ $user->UserData->rayon ?? '' }}" required type="text" name="rayon" id="rayon" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>No Telp.</td>
                            <td>:</td>
                            <td><input value="{{ $user->UserData->phone ?? '' }}" required type="number" name="phone" id="phone" class="w-full p-2 border border-gray-300 rounded-md"></td>
                        </tr>
                        <tr>
                            <td>Program</td>
                            <td>:</td>
                            <td>
                                <select name="program" id="program" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="" disabled>Opsi</option>
                                    @if (isset($user->UserData->program))
                                    <option value="unggulan" {{ $user->UserData->program == 'unggulan' ? 'selected' : '' }}>Unggulan</option>
                                    <option value="reguler" {{ $user->UserData->program == 'reguler' ? 'selected' : '' }}>Reguler</option>
                                    @else
                                        <option value="unggulan" >Unggulan</option>
                                    <option value="reguler">Reguler</option>
                                    @endif
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Level</td>
                            <td>:</td>
                            <td>
                                <select name="level" id="level" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="" disabled>Level</option>
                                    <option value="admin" {{ $user->level == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ $user->level == 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="student" {{ $user->level == 'student' ? 'selected' : '' }}>Student</option>
                                </select>
                            </td>
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