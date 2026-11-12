<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class UserMenuPermissionSeeder extends Seeder                    // pengisi permission halaman Akun Pengguna
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed --class="Modules\Auth\Database\Seeders\UserMenuPermissionSeeder"
    {
        $daftar = [                                              // id permission => keterangan
            'auth.user.index' => 'Lihat daftar akun',
            'auth.user.create' => 'Buka form buat akun',
            'auth.user.store' => 'Simpan akun baru',
            'auth.user.edit' => 'Buka form ubah akun / reset password',
            'auth.user.update' => 'Simpan perubahan akun',
            'auth.user.status' => 'Aktifkan/nonaktifkan akun',
        ];

        foreach ($daftar as $id_permission => $keterangan) {     // ulangi setiap permission
            DB::table('permission')->insertOrIgnore([            // simpan permission; lewati jika sudah ada
                'id_permission' => $id_permission,
                'nm_permission' => $keterangan,
            ]);

            DB::table('role_permission')->insertOrIgnore([       // berikan ke role admin (admin yang mengurus akun karyawan)
                'id_role' => 'admin',
                'id_permission' => $id_permission,
            ]);
        }                                                        // akhir perulangan
    }
}
