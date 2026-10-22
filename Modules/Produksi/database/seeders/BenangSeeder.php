<?php

namespace Modules\Produksi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenangSeeder extends Seeder
{
    public function run(): void
    {
        // Data contoh. Kolom: merge, denier, filamen, warna ppt
        $data = [
            ['4D001', 75, 36, 'Putih'],
            ['4D002', 150, 48, 'Biru'],
            ['4D003', 100, 96, 'Kuning'],
            ['4D004', 300, 96, 'Hijau'],
            ['4D005', 200, 48, 'Merah'],
        ];

        foreach ($data as $b) {
            DB::table('benang')->insert([
                'merge' => $b[0],
                'denier' => $b[1],
                'filamen' => $b[2],
                'warna_ppt' => $b[3],
                'status_aktif' => 'Aktif',
                'created_by' => '2017-0001', // Supervisor Produksi
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
