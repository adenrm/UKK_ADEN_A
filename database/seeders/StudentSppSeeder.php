<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentSpp;
use App\Models\Spps;

class StudentSppSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua siswa
        $studentList = User::where('level', 'student')->get();  
        
        // Ambil SPP aktif
        $activeSpp = Spps::where('is_active', true)->first();
        
        if (!$activeSpp) {
            $activeSpp = Spps::create([
                'tahun_ajaran' => '2024/2025',
                'nominal_per_bulan' => 450000,
                'total_bulan' => 12,
                'is_active' => true
            ]);
        }
        
        // Buat data siswa_spp untuk setiap siswa yang belum punya
        foreach ($studentList as $student) {
            $exists = StudentSpp::where('user_id', $student->id)->first();
            
            if (!$exists) {
                StudentSpp::create([
                    'user_id' => $student->id,
                    'spp_id' => $activeSpp->id,
                    'tahun_masuk' => date('Y'),
                    'status' => 'active'
                ]);
            }
        }
        
        $this->command->info('Siswa SPP berhasil di-seed');
    }
}