<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder

class AuthDatabaseSeeder extends Seeder                          // seeder utama modul Auth
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed
    {
        $this->call([                                            // jalankan seeder sesuai urutan
            RolePermissionSeeder::class,                         // 1. role & permission data karyawan (harus ada dulu)
            MasterPermissionSeeder::class,                       // 2. permission data master (departemen, divisi, jabatan, area)
            RoleMenuPermissionSeeder::class,                     // 3. permission halaman Role & Permission (khusus superadmin)
            UserMenuPermissionSeeder::class,                     // 4. permission halaman Akun Pengguna (untuk admin)
            UserSeeder::class,                                   // 5. akun login (butuh role)
        ]);
    }
}
