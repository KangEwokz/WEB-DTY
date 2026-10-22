<?php

namespace Modules\Qct\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        // Kode grade dari rancangan. Arti dan kriteria grade masih dikonfirmasi ke pabrik.
        foreach (['AX', 'SS', 'CL'] as $grade) {
            DB::table('grade')->insert([
                'id_grade' => $grade,
                'keterangan' => 'Keterangan menyusul (konfirmasi pabrik)',
                'status_aktif' => 'Aktif',
                'created_by' => '2016-0001', // HOD QCT
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
