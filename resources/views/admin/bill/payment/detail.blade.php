@extends('layouts.admin')

@section('title', 'Detail')

@section('content')
    <div class="p-5">
        <div class="bg-white w-full rounded-md p-5">
            <a href="{{ route('admin.bill.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                Kembali
            </a>
            <h1 class="text-2xl font-bold my-5">Detail Pembayaran SPP</h1>
            <div class="grid grid-cols-6 gap-4">
                @foreach ($SppBulan as $item)
                <div class="
                @if ($item->status === 'paid')
                    bg-green-400 border border-green-600
                @elseif ($item->status === 'partial')
                    bg-yellow-400 border border-yellow-600
                @elseif ($item->status === 'unpaid')
                    bg-red-400 border border-red-600
                @endif
                p-5 rounded-md shadow-md text-white">
                    <p class="font-bold">
                        @if ($item->bulan === 1)
                            Januari
                        @elseif ($item->bulan === 2)
                            Februari
                        @elseif ($item->bulan === 3)
                            Maret 
                        @elseif ($item->bulan === 4)
                            April
                        @elseif ($item->bulan === 5)
                            Mei
                        @elseif ($item->bulan === 6)
                            Juni
                        @elseif ($item->bulan === 7)
                            Juli
                        @elseif ($item->bulan === 8)
                            Agustus
                        @elseif ($item->bulan === 9)
                            September
                        @elseif ($item->bulan === 10)
                            Oktober
                        @elseif ($item->bulan === 11)
                            November
                        @elseif ($item->bulan === 12)
                            Desember
                        @endif
                    </p>
                    <p>{{ 'Rp. ' . number_format($item->nominal, 2, ',', '.')  }}</p>
                    <p>
                        @if ($item->status === 'paid')
                            <span class="text-green-800 font-medium">Lunas</span>
                        @elseif ($item->status === 'partial')
                            <span class="text-yellow-800 font-medium">Belum Lunas</span>
                        @elseif ($item->status === 'unpaid')
                            <span class="text-red-800 font-medium">Belum Bayar</span>
                        @endif
                    </p>
                </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection