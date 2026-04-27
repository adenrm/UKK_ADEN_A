<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ClassGrade;
use App\Models\SppBulan;
use App\Models\Spps;
use App\Models\StudentSpp;
use App\Models\User;
use Illuminate\Http\Request;

class SPPController extends Controller
{
    public function index()
    {
        $spps = Spps::all();
        return view('admin.management.spp.index', compact('spps'));
    }

    public function create()
    {
        return view('admin.management.spp.create');
    }

    public function store(Request $request)
    {
        $spp = new Spps();
        $spp->keterangan = $request->keterangan;
        $spp->nominal_per_bulan = $request->nominal_per_bulan;
        $spp->tahun_ajaran = $request->tahun_ajaran;
        $spp->total_bulan = 12;
        $spp->total_nominal_bulan = $request->nominal_per_bulan * 12;
        $spp->is_active = true;
        $spp->save();
        return redirect()->route('admin.spp.index')->with('success', 'SPP berhasil di tambahkan!');
    }
    
    public function edit(Spps $spp)
    {
        return view('admin.management.spp.edit', compact('spp'));
    }

    public function update(Request $request, Spps $spp)
    {
        $spp->keterangan = $request->keterangan;
        $spp->nominal_per_bulan = $request->nominal_per_bulan;
        $spp->tahun_ajaran = $request->tahun_ajaran;
        $spp->save();
        return redirect()->route('admin.spp.index')->with('success', 'SPP berhasil di edit!');
    }

    public function updateStatus(Request $request, Spps $spp)
    {
        $spp->is_active = $request->is_active === '1' ? true : false;
        $spp->save();
        return redirect()->route('admin.spp.index')->with('success', 'Status SPP berhasil di update!');
    }

    public function destroy(Spps $spp)
    {
        $spp->delete();
        return redirect()->route('admin.spp.index')->with('success', 'SPP berhasil di hapus!');
    }


    public function indexTagihan()
    {
        $studentList = User::where('level', 'student')
            ->with(['userData.class', 'studentSpp.sppBulan'])
            ->get();
            
        $classes = ClassGrade::all();
        $sppList = Spps::all();
        
        return view('admin.tagihan.index', compact('studentList', 'classes', 'sppList'));
    }


    public function detailTagihan($studentId)
    {
        $student = User::with(['userData.class', 'studentSpp.spp', 'studentSpp.sppBulan'])->findOrFail($studentId);
        $tagihan = SppBulan::where('student_spp_id', $student->studentSpp->id)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
            
        return view('admin.tagihan.detail', compact('student', 'tagihan'));
    }

     public function registerSpp(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'spp_id' => 'required|exists:spps,id'
    ]);
    
    // Cek apakah sudah ada
    $exists = StudentSpp::where('user_id', $request->user_id)->first();
    
    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'Student sudah memiliki data SPP'
        ]);
    }
    
    // Buat data baru
    $studentSpp = StudentSpp::create([
        'user_id' => $request->user_id,
        'spp_id' => $request->spp_id,
        'tahun_masuk' => date('Y'),
        'status' => 'active'
    ]);
    
    // Generate tagihan otomatis
    $this->generateTagihanOtomatis($studentSpp->id);
    
    return response()->json([
        'success' => true,
        'message' => 'Berhasil register SPP untuk student',
        'student_spp_id' => $studentSpp->id
    ]);
}

private function generateTagihanOtomatis($studentSppId)
{
    $studentSpp = StudentSpp::with('spp')->find($studentSppId); 
    $currentYear = date('Y');
    
    // Generate dari bulan Januari sampai bulan sekarang
    $currentMonth = date('n');
    
    for ($bulan = 1; $bulan <= $currentMonth; $bulan++) {
        SppBulan::updateOrCreate(
            [
                'student_spp_id' => $studentSppId,
                'bulan' => $bulan,
                'tahun' => $currentYear
            ],
            [
                'nominal' => $studentSpp->spp->nominal_per_bulan,
                'status' => 'unpaid',
                'tanggal_jatuh_tempo' => "$currentYear-$bulan-10",
                'sisa_utang' => $studentSpp->spp->nominal_per_bulan
            ]
        );
    }
}
}
