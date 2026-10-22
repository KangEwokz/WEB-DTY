<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        // nm_divisi dipakai oleh middleware divisi:Admin / divisi:Produksi / divisi:QCT
        DB::table('divisi')->insert([
            ['id_divisi' => '01', 'kd_divisi' => 'ADM', 'nm_divisi' => 'Admin'],
            ['id_divisi' => '02', 'kd_divisi' => 'PRD', 'nm_divisi' => 'Produksi'],
            ['id_divisi' => '03', 'kd_divisi' => 'QCT', 'nm_divisi' => 'QCT'],
        ]);
    }
}
