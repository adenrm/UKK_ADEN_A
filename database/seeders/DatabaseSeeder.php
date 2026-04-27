<?php

namespace Database\Seeders;

use App\Models\ClassGrade;
use App\Models\SppBulan;
use App\Models\Spps;
use App\Models\StudentSpp;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        $spp = Spps::create([
            'tahun_ajaran' => '2024/2025',
            'nominal_per_bulan' => 450000,
            'total_bulan' => 12,
            'is_active' => true,
            'keterangan' => 'SPP Tahun Ajaran 2024/2025'
        ]);

         $classes = [
            ['name' => 'XII RPL', 'major' => 'RPL'],
            ['name' => 'XII TKJ', 'major' => 'TKJ'],
            ['name' => 'XII PMN', 'major' => 'PMN'],
        ];
        
        foreach ($classes as $class) {
            ClassGrade::create($class);
        }


       for ($i = 0; $i < 10; $i++) {
        $level = collect(['staff', 'student'])->random();
        
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->email(),
            'password' => bcrypt('password'),
            'level' => $level,
        ]);
        

        if ($level === 'student') {
            $classId = collect([1, 2])->random(); // Random kelas
            $rayon = collect(['Cicurug', 'Ciawi', 'Cisarua'])->random();
            
            UserData::create([
                'user_id' => $user->id,
                'nisn' => fake()->unique()->numberBetween(100000, 999999),
                'nis' => fake()->unique()->numberBetween(1000, 9999),
                'class_id' => $classId,
                'rayon' => $rayon,
                // 'phone' => fake()->phoneNumber('ID_ID', 8),
                'program' => collect(['unggulan', 'reguler'])->random(),
            ]);
        }
    }

            $siswaSpp = StudentSpp::create([
            'user_id' => $user->id,
            'spp_id' => $spp->id,
            'tahun_masuk' => 2024,
            'status' => 'active'
        ]);


            User::create([
                'name' => 'Deden',
                'email' => 'deden@gmail.com',
                'password' => bcrypt('password'),
                'level' => 'admin',
            ]);

             $this->generateTagihan($siswaSpp->id, $spp->nominal_per_bulan);
    }
    
    private function generateTagihan($studentSppId, $nominal)
    {
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            SppBulan::create([
                'student_spp_id' => $studentSppId,
                'bulan' => $bulan,
                'tahun' => 2024,
                'nominal' => $nominal,
                'status' => 'unpaid',
                'tanggal_jatuh_tempo' => "2024-$bulan-10",
                'sisa_utang' => $nominal
            ]);
        }
    }
}
