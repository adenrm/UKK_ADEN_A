@extends('layouts.admin')

@section('title', 'Manajemen Tagihan SPP')

@section('content')
<div class="p-5">
    <div class="bg-white rounded-md p-5 shadow-md">
        <div class="flex justify-between items-center mb-5">
            <div class="">
                <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                       Kembali
                   </a>
               <h3 class="text-2xl font-bold mt-5">Manajemen Tagihan SPP</h3>
            </div>
            <div class="flex gap-2 mt-10">
                {{-- <a href="{{ route('admin.bill.generate') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Generate Massal
                </a> --}}
            </div>
        </div>

        {{-- <!-- Filter -->
        <div class="mb-4 flex gap-4">
            <select id="filter_kelas" class="border rounded px-3 py-2">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            <select id="filter_status" class="border rounded px-3 py-2">
                <option value="">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="belum">Belum Lunas</option>
            </select>
            <input type="text" id="search" placeholder="Cari student..." class="border rounded px-3 py-2 w-64">
        </div> --}}

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="min-w-full border" id="tagihanTable">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">NISN</th>
                        <th class="py-3 px-4 text-left">Nama student</th>
                        <th class="py-3 px-4 text-left">Program</th>
                        <th class="py-3 px-4 text-center">Total Tagihan</th>
                        <th class="py-3 px-4 text-center">Total Bayar</th>
                        <th class="py-3 px-4 text-center">Sisa</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach($studentList as $student)
                    @php
                        // Safe checking untuk menghindari error
                        $studentSpp = $student->studentSpp;
                        $sppBulan = $studentSpp ? $studentSpp->sppBulan : collect();
                        
                        $totalTagihan = $sppBulan->sum('nominal');
                        $totalBayar = $sppBulan->where('status', 'paid')->sum('nominal');
                        $sisa = $totalTagihan - $totalBayar;
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $student->userData->nisn ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $student->name }}</td>
                        <td class="py-3 px-4">{{ $student->UserData->program ?? '-' }}</td>
                        <td class="py-3 px-4 text-right">
                            {{ $studentSpp ? 'Rp ' . number_format($totalTagihan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            {{ $studentSpp ? 'Rp ' . number_format($totalBayar, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold @if($sisa > 0) text-red-600 @else text-green-600 @endif">
                            {{ $studentSpp ? 'Rp ' . number_format($sisa, 0, ',', '.') : 'Belum ada tagihan' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if(!$studentSpp)
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">Belum register SPP</span>
                            @elseif($sisa <= 0)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Lunas</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($studentSpp)
                                <a href="{{ route('admin.payment.detail', $student->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                    Detail
                                </a>
                                <a href="{{ route('admin.payment.create', $student->id) }}" class="text-green-500 hover:text-green-700">
                                    Bayar
                                </a>
                            @else
                                <a href="{{ route('admin.bill.register', $student->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                    Register 
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Generate Massal -->
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Generate Tagihan Massal</h3>
            <form action="{{ route('admin.bill.generate.massal') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tahun Ajaran</label>
                    <select name="spp_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($sppList as $spp)
                            <option value="{{ $spp->id }}">{{ $spp->tahun_ajaran }} - Rp {{ number_format($spp->nominal_per_bulan) }}/bulan</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tahun</label>
                    <input type="number" name="tahun" class="w-full border rounded px-3 py-2" value="{{ date('Y') }}" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeGenerateModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tambahkan di admin/tagihan/index.blade.php --}}

<!-- Modal Register SPP -->
<div id="registerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Register SPP untuk Siswa</h3>
            <form id="registerForm">
                @csrf
                <input type="hidden" id="register_user_id" name="user_id">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Pilih SPP</label>
                    <select name="spp_id" id="register_spp_id" class="w-full border rounded px-3 py-2" required>
                        @foreach($sppList as $spp)
                            <option value="{{ $spp->id }}">
                                {{ $spp->tahun_ajaran }} - Rp {{ number_format($spp->nominal_per_bulan) }}/bulan
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRegisterModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>

     let selectedUserId = null;
    
    function registerSpp(userId) {
        selectedUserId = userId;
        document.getElementById('register_user_id').value = userId;
        document.getElementById('registerModal').classList.remove('hidden');
        document.getElementById('registerModal').classList.add('flex');
    }
    
    function closeRegisterModal() {
        document.getElementById('registerModal').classList.add('hidden');
        document.getElementById('registerModal').classList.remove('flex');
    }
    
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.bill.register.spp") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: selectedUserId,
                spp_id: document.getElementById('register_spp_id').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success');
                closeRegisterModal();
                location.reload();
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error!', 'Terjadi kesalahan', 'error');
        });
    });

    function generateMassal() {
        document.getElementById('generateModal').class.remove('hidden');
        document.getElementById('generateModal').class.add('flex');
    }
    
    function closeGenerateModal() {
        document.getElementById('generateModal').class.add('hidden');
        document.getElementById('generateModal').class.remove('flex');
    }
    
    // Filter JavaScript
    document.getElementById('filter_kelas').addEventListener('change', filterTable);
    document.getElementById('filter_status').addEventListener('change', filterTable);
    document.getElementById('search').addEventListener('keyup', filterTable);
    
    function filterTable() {
        // Implementasi filter
    }
</script>
@endpush
@endsection