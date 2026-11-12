<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database
use Illuminate\Support\Facades\Hash;                             // Hash = untuk mengacak (enkripsi) password

class UserSeeder extends Seeder                                  // pengisi data akun login
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed
    {
        $password = Hash::make('password123');                   // acak 'password123' jadi hash, contoh $2y$12$Kx...

        DB::table('users')->insert([                             // isi tabel users (1 baris = 1 akun)
            ['username' => 'superadmin', 'password' => $password, 'id_karyawan' => '2015-0002', 'id_role' => 'superadmin', 'status' => 'Aktif'],
            ['username' => 'direktur', 'password' => $password, 'id_karyawan' => '2015-0001', 'id_role' => 'direktur', 'status' => 'Aktif'],
            ['username' => 'hod', 'password' => $password, 'id_karyawan' => '2016-0001', 'id_role' => 'hod', 'status' => 'Aktif'],
            ['username' => 'kadiv_qct', 'password' => $password, 'id_karyawan' => '2016-0002', 'id_role' => 'kadiv', 'status' => 'Aktif'],
            ['username' => 'supervisor', 'password' => $password, 'id_karyawan' => '2017-0001', 'id_role' => 'supervisor', 'status' => 'Aktif'],
            ['username' => 'admin', 'password' => $password, 'id_karyawan' => '2018-0001', 'id_role' => 'admin', 'status' => 'Aktif'],
            ['username' => 'op_vis', 'password' => $password, 'id_karyawan' => '2019-0001', 'id_role' => 'operator', 'status' => 'Aktif'],
            ['username' => 'op_lab', 'password' => $password, 'id_karyawan' => '2019-0002', 'id_role' => 'operator', 'status' => 'Aktif'],
            ['username' => 'op_nonaktif', 'password' => $password, 'id_karyawan' => '2019-0003', 'id_role' => 'operator', 'status' => 'Non-Aktif'], // akun nonaktif (uji login ditolak)
            ['username' => 'teknisi_elk', 'password' => $password, 'id_karyawan' => '2020-0001', 'id_role' => 'teknisi', 'status' => 'Aktif'],
            ['username' => 'teknisi_ins', 'password' => $password, 'id_karyawan' => '2020-0002', 'id_role' => 'teknisi', 'status' => 'Aktif'],
        ]);
    }
}
