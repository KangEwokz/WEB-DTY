<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class RoleMenuPermissionSeeder extends Seeder                    // pengisi permission halaman Role & Permission
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed --class="Modules\Auth\Database\Seeders\RoleMenuPermissionSeeder"
    {
        DB::table('permission')->insertOrIgnore([                // insertOrIgnore = lewati jika id sudah ada (aman dijalankan ulang)
            ['id_permission' => 'auth.role.index', 'nm_permission' => 'Lihat daftar role'],
            ['id_permission' => 'auth.role.create', 'nm_permission' => 'Buka form tambah role'],
            ['id_permission' => 'auth.role.store', 'nm_permission' => 'Simpan role baru'],
            ['id_permission' => 'auth.role.edit', 'nm_permission' => 'Buka form ubah role'],
            ['id_permission' => 'auth.role.update', 'nm_permission' => 'Simpan perubahan role'],
            ['id_permission' => 'auth.role.status', 'nm_permission' => 'Aktifkan/nonaktifkan role'],
            ['id_permission' => 'auth.role.permission', 'nm_permission' => 'Buka halaman atur permission'],
            ['id_permission' => 'auth.role.simpan-permission', 'nm_permission' => 'Simpan permission role'],
        ]);                                                      // sengaja TIDAK diberikan ke role mana pun: hanya superadmin (otomatis boleh semua) yang bisa mengatur role
    }
}
