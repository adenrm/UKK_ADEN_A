@extends('layout.admin')

@section('title', 'Generate Tagihan')

@section('content')
<div id="generateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Generate Tagihan Baru</h3>
            <form action="{{ route('admin.tagihan.generate', $student->studentSpp->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Tahun</label>
                    <input type="number" name="tahun" class="w-full border rounded px-3 py-2" value="{{ date('Y') + 1 }}" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeGenerateModal()" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Batal</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection