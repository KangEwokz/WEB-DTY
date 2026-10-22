<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Satu akun per aktor. Password semua akun contoh: password
        // Kolom: username, id_karyawan, role
        $data = [
            ['direktur', '2015-0001', 'direktur'],
            ['staf', '2018-0001', 'staf'],
            ['supervisor', '2017-0001', 'supervisor'],
            ['hod', '2016-0001', 'hod'],
            ['opvisual', '2019-0001', 'operator'],
            ['oplab', '2020-0001', 'operator'],
            ['opvisual2', '2021-0001', 'operator'],
            ['oplab2', '2021-0002', 'operator'],
            ['superadmin', '2015-0002', 'superadmin'],   // khusus demo, bisa membuka semua menu
        ];

        foreach ($data as $u) {
            $id = DB::table('users')->insertGetId([
                'username' => $u[0],
                'password' => Hash::make('password'),
                'id_karyawan' => $u[1],
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Role disimpan Spatie di tabel model_has_roles
            User::find($id)->assignRole($u[2]);
        }

        // Contoh untuk uji login ditolak:
        // opnonaktif = akun Non-Aktif, opkeluar = akun Aktif tapi karyawannya sudah keluar
        foreach ([['opnonaktif', '2022-0001', 'Non-Aktif'], ['opkeluar', '2019-0002', 'Aktif']] as $u) {
            $id = DB::table('users')->insertGetId([
                'username' => $u[0],
                'password' => Hash::make('password'),
                'id_karyawan' => $u[1],
                'status' => $u[2],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            User::find($id)->assignRole('operator');
        }
    }
}
