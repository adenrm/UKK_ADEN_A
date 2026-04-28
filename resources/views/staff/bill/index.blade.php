@extends('layouts.staff')

@section('title', 'Manajemen Tagihan SPP')

@section('content')
<div class="p-5">
    <div class="bg-white rounded-md p-5 shadow-md">
        <div class="flex justify-between items-center mb-5">
            <div class="">
               <h3 class="text-2xl font-bold mt-5">Manajemen Tagihan SPP</h3>
               @if(session('error'))
    <div id="alert-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded fixed top-5 right-5 z-50 transition-opacity duration-500" role="alert">
        <div class="flex justify-between items-center">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="ml-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
@endif
            </div>
            <div class="flex gap-2 mt-10">
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
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Program</th>
                        <th class="py-3 px-4 text-center">Total Tagihan</th>
                        <th class="py-3 px-4 text-center">Total Bayar</th>
                        <th class="py-3 px-4 text-center">Sisa</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse($studentList as $student)
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
                            {{ $studentSpp ? 'Rp' . number_format($totalTagihan, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            {{ $studentSpp ? 'Rp' . number_format($totalBayar, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-bold @if($sisa > 0) text-red-600 @else text-green-600 @endif">
                            {{ $studentSpp ? 'Rp' . number_format($sisa, 0, ',', '.') : 'Belum ada tagihan' }}
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
                                <a href="{{ route('payment.detail', $student->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                    Detail
                                </a>
                                <a href="{{ route('staff.payment.create', $student->id) }}" class="text-green-500 hover:text-green-700">
                                    Bayar
                                </a>
                            @else
                                <a href="{{ route('staff.bill.register', $student->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                    Register 
                                </a>
                            @endif
                        </td>
                    </tr>
                      @empty
                    <tr>
                        <td colspan="8" class="border border-slate-400 px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <!-- Icon Kosong -->
                                <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 font-medium">Belum ada data tiket</p>
                                <p class="text-gray-400 text-sm mt-1">Tiket akan muncul di sini setelah ada yang membuat</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
        
        fetch('{{ route("bill.register.spp") }}', {
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