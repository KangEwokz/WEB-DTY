<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        // created_by kosong karena tabel karyawan belum berisi data saat seeder ini jalan
        DB::table('area')->insert([
            ['id_area' => 'KPG', 'nm_area' => 'Kepegawaian', 'id_divisi' => '01', 'created_at' => now(), 'updated_at' => now()],
            ['id_area' => 'VIS', 'nm_area' => 'Visual', 'id_divisi' => '03', 'created_at' => now(), 'updated_at' => now()],
            ['id_area' => 'LAB', 'nm_area' => 'Lab', 'id_divisi' => '03', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
