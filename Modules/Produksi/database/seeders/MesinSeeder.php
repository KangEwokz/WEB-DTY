<?php

namespace Modules\Produksi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MesinSeeder extends Seeder
{
    public function run(): void
    {
        // 3 line (401-403) x 10 posisi = 30 mesin, kode 401-01 s.d. 403-10
        foreach ([401, 402, 403] as $line) {
            for ($posisi = 1; $posisi <= 10; $posisi++) {
                DB::table('mesin')->insert([
                    'kode_mesin' => $line.'-'.str_pad($posisi, 2, '0', STR_PAD_LEFT),
                    'line' => $line,
                    'posisi' => $posisi,
                    'status' => 'Running',
                    'created_by' => '2017-0001', // Supervisor Produksi
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
