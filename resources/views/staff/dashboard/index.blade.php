@extends('layouts.staff')

@section('title', 'Dashboard')

@section('content')
    <div class="p-5">
        <div class="bg-white p-5 shadow-md rounded-md">
            <h1 class="text-2xl font-medium my-5">Selamat datang, {{ auth()->user()->name }}!</h1>
            <p class="text-lg text-gray-600">Anda sekarang login sebagai staff.</p>

            <div class="mt-5">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi consequatur corrupti iste aliquid voluptas repudiandae ratione vitae facilis perspiciatis quis necessitatibus voluptatum suscipit tempore commodi cum at blanditiis, iure soluta maiores illum minus sapiente doloremque incidunt ducimus. Enim, corrupti placeat. Commodi, possimus accusamus! Cum inventore esse, commodi explicabo assumenda quibusdam.
            </div>
        </div>
    </div>
@endsection