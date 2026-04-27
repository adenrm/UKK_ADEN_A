@extends('layouts.siswa')

@section('title', 'Tagihan SPP Saya')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Tagihan SPP Tahun Ajaran {{ $studentSpp->spp->tahun_ajaran ?? '2024/2025' }}</h1>
    
    <!-- Info Siswa -->
    <div class="bg-blue-50 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Nama Siswa</p>
                <p class="font-semibold">{{ Auth::user()->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Kelas</p>
                <p class="font-semibold">{{ Auth::user()->UserData->class->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">NISN</p>
                <p class="font-semibold">{{ Auth::user()->UserData->nisn ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <p class="font-semibold">
                    @php
                        $totalTagihan = $tagihan->sum('nominal');
                        $totalTerbayar = $tagihan->where('status', 'paid')->sum('nominal');
                        $sisa = $totalTagihan - $totalTerbayar;
                    @endphp
                    @if($sisa <= 0)
                        <span class="text-green-600">✓ Lunas</span>
                    @else
                        <span class="text-red-600">⚠️ Masih ada tagihan</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <p class="text-sm text-gray-600">Total Tagihan</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-sm text-gray-600">Total Dibayar</p>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <p class="text-sm text-gray-600">Sisa Tagihan</p>
            <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($sisa, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Tabel Tagihan -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Bulan</th>
                    <th class="py-3 px-4 text-left">Tahun</th>
                    <th class="py-3 px-4 text-right">Nominal</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Jatuh Tempo</th>
                    <th class="py-3 px-4 text-center">Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $item->nama_bulan }}</td>
                    <td class="py-3 px-4">{{ $item->tahun }}</td>
                    <td class="py-3 px-4 text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($item->status == 'paid')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✓ Lunas</span>
                        @elseif($item->status == 'partial')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⚠️ Sebagian</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Belum Bayar</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-center">{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                    <td class="py-3 px-4 text-center">
                        {{ $item->tanggal_dibayar ? \Carbon\Carbon::parse($item->tanggal_dibayar)->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-500">Belum ada data tagihan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tombol Bayar -->
    @if($sisa > 0)
    <div class="mt-6 text-center">
        <button onclick="showPaymentModal()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
            💰 Bayar Sekarang
        </button>
    </div>
    @endif
</div>

<!-- Modal Pembayaran -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Form Pembayaran SPP</h3>
            <form action="{{ route('siswa.pembayaran.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nominal Pembayaran</label>
                    <input type="number" name="nominal_bayar" class="w-full border rounded-lg px-3 py-2" required min="10000">
                    <p class="text-sm text-gray-500 mt-1">Minimal Rp 10.000</p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Dibayar Oleh</label>
                    <input type="text" name="dibayar_oleh" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
    }
</script>
@endpush
@endsection