@extends('layouts.admin')

@section('title', 'Manajemen')

@section('content')
    <div class="p-5">
        <div class="flex flex-col gap-5">
            <a href="{{ route('admin.student.index') }}" class="bg-white rounded-md p-5 flex gap-5 shadow-md">
                
                <h1 class="text-2xl font-medium">Siswa</h1>
            </a>
             <a href="{{ route('admin.staff.index') }}" class="bg-white rounded-md p-5 flex gap-5 shadow-md">
                
                <h1 class="text-2xl font-medium">Petugas</h1>
            </a>
             <a href="{{ route('admin.spp.index') }}" class="bg-white rounded-md p-5 flex gap-5 shadow-md">
                
                <h1 class="text-2xl font-medium">SPP</h1>
            </a>
            <a href="{{ route('admin.bill.index') }}" class="bg-white rounded-md p-5 flex gap-5 shadow-md">
                <h1 class="text-2xl font-medium">Tagihan</h1>
            </a>
             <a href="{{ route('admin.payment.index') }}" class="bg-white rounded-md p-5 flex gap-5 shadow-md">
                
                <h1 class="text-2xl font-medium">Riwayat Pembayaran</h1>
            </a>
        </div>


    </div>
@endsection
