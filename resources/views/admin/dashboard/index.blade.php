@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mt-5 mx-[10%]">
        <div class="flex gap-5 mt-5">
           <div class="flex w-[40%] gap-5 ">
             <h1 class="text-3xl">Selamat datang, {{ Auth::user()->name }}</h1>
           </div>
           <div class="w-[60%]">
            {{-- // KOSONG --}}
           </div>
        </div>

        <div class="p-5 flex gap-5 bg-purple-600 rounded-md shadow-md mt-4 h-36">
            <div class="">
                {{-- <img src="{{ asset('fotocontoh.jpg') }}" alt="" class="w-[100px] h-[100px] object-cover rounded-md"> --}}
            </div>
            <div class="text-md font-medium">
                Nama: <span class="text-white">{{ Auth::user()->name }}</span>
                <br>
                Email: <span class="text-white">{{ Auth::user()->email }}</span>
                <br>
                Role: <span class="text-white">{{ Auth::user()->level }}</span>
                <br>
                Sejak: <span class="text-white">{{ Auth::user()->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <div class="flex gap-5 mt-5 select-none">
            <div class="bg-green-600 w-[25%] p-4 h-24 rounded-md shadow-md">
                <h4 class="text-xl font-medium">Lunas</h4>
                <h3 class="text-3xl font-bold text-white">
                    {{ $lunas }} <span class="text-sm text-white">Bulan</span>
                </h3>
            </div>
            <div class="bg-yellow-600 w-[25%] p-4 h-24 rounded-md shadow-md">
                <h4 class="text-xl font-medium">Belum Lunas</h4>
                <h3 class="text-3xl font-bold text-white">
                    {{ $belumLunas }} <span class="text-sm text-white">Bulan</span>
                </h3>
            </div>
            <div class="bg-red-600 w-[25%] p-4 h-24 rounded-md shadow-md">
                <h4 class="text-xl font-medium">Belum Bayar</h4>
                <h3 class="text-3xl font-bold text-white">
                    {{ $belumBayar }} <span class="text-sm text-white">Bulan</span>
                </h3>
            </div>
            <div class="bg-blue-600 w-[25%] p-4 h-24 rounded-md shadow-md">
                <h4 class="text-xl font-medium">Jumlah Siswa</h4>
                <h3 class="text-3xl font-bold text-white">
                    {{ $totalstudents }} <span class="text-sm text-white">Orang</span>
                </h3>
            </div>            
        </div>

        <div class="p-5 bg-white mt-5 rounded-md shadow-md">
            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Fuga, iure? Hic id recusandae impedit voluptate, molestias maiores porro temporibus, libero nesciunt ex repudiandae. Iure magnam ab et sunt sapiente iusto quos beatae numquam! Ipsa, aut impedit nostrum aspernatur vel maxime perspiciatis fugiat rerum laboriosam iusto, error consequuntur esse ratione natus!
        </div>
    </div>
@endsection