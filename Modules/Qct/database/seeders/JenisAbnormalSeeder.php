<?php

namespace Modules\Qct\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisAbnormalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['VS01', 'Handling'],
            ['VS02', 'OTY'],
            ['VS03', 'Hairy'],
        ];

        foreach ($data as $j) {
            DB::table('jenis_abnormal')->insert([
                'id_jns_abn' => $j[0],
                'nm_abnormal' => $j[1],
                'status_aktif' => 'Aktif',
                'created_by' => '2016-0001', // HOD QCT
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
