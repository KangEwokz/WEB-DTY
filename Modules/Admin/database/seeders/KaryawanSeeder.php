<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        // Satu karyawan contoh per aktor, ditambah operator QCT grup B untuk contoh absensi.
        // Kolom: id, nama, jenis kelamin, divisi, jabatan, area default, grup, tanggal masuk
        $data = [
            ['2015-0001', 'Hendra Wijaya', 'L', null, '01', null, 'NG', '2015-01-05'],   // Direktur
            ['2015-0002', 'Super Admin (Demo)', 'L', null, '02', null, 'NG', '2015-01-05'], // Akun demo, bukan aktor rancangan
            ['2018-0001', 'Rina Marlina', 'P', '01', '02', 'KPG', 'NG', '2018-03-12'],   // Staf Admin
            ['2017-0001', 'Agus Santoso', 'L', '02', '03', null, 'NG', '2017-07-01'],    // Supervisor Produksi
            ['2016-0001', 'Dewi Lestari', 'P', '03', '04', null, 'NG', '2016-02-15'],    // HOD QCT
            ['2019-0001', 'Budi Prasetyo', 'L', '03', '05', 'VIS', 'A', '2019-04-01'],   // Operator Visual
            ['2020-0001', 'Siti Nurhaliza', 'P', '03', '05', 'LAB', 'A', '2020-06-08'],  // Operator Lab
            ['2021-0001', 'Joko Susilo', 'L', '03', '05', 'VIS', 'B', '2021-01-11'],     // Operator Visual
            ['2021-0002', 'Ayu Pratiwi', 'P', '03', '05', 'LAB', 'B', '2021-01-11'],     // Operator Lab
            ['2022-0001', 'Wahyu Hidayat', 'L', '03', '05', 'LAB', 'C', '2022-02-01'],   // Operator Lab (akunnya dinonaktifkan)
        ];

        foreach ($data as $k) {
            DB::table('karyawan')->insert([
                'id_karyawan' => $k[0],
                'nama_karyawan' => $k[1],
                'jns_kelamin' => $k[2],
                'id_divisi' => $k[3],
                'id_jabatan' => $k[4],
                'id_area' => $k[5],
                'grup' => $k[6],
                'tgl_masuk' => $k[7],
                'status_aktif' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Contoh karyawan yang sudah keluar (untuk uji login ditolak)
        DB::table('karyawan')->insert([
            'id_karyawan' => '2019-0002',
            'nama_karyawan' => 'Rudi Hartono',
            'jns_kelamin' => 'L',
            'id_divisi' => '03',
            'id_jabatan' => '05',
            'id_area' => 'VIS',
            'grup' => 'C',
            'tgl_masuk' => '2019-09-02',
            'status_aktif' => 'Non-Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
