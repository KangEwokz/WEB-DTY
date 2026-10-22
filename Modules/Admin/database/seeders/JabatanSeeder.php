<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            ['id_jabatan' => '01', 'nm_jabatan' => 'Direktur', 'id_divisi' => null],
            ['id_jabatan' => '02', 'nm_jabatan' => 'Staf', 'id_divisi' => '01'],
            ['id_jabatan' => '03', 'nm_jabatan' => 'Supervisor', 'id_divisi' => '02'],
            ['id_jabatan' => '04', 'nm_jabatan' => 'HOD', 'id_divisi' => '03'],
            ['id_jabatan' => '05', 'nm_jabatan' => 'Operator', 'id_divisi' => '03'],
        ]);
    }
}
