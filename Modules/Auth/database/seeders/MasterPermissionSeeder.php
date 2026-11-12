<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class MasterPermissionSeeder extends Seeder                      // pengisi permission halaman Data Master (departemen, divisi, jabatan, area)
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed --class="Modules\Auth\Database\Seeders\MasterPermissionSeeder"
    {
        $menu = ['departemen', 'divisi', 'jabatan', 'area'];     // daftar menu data master (= bagian tengah nama route)

        $aksi = [                                                // daftar aksi: akhiran nama route => keterangan
            'index' => 'Lihat daftar',
            'create' => 'Buka form tambah',
            'store' => 'Simpan',
            'edit' => 'Buka form ubah',
            'update' => 'Simpan perubahan',
            'status' => 'Aktifkan/nonaktifkan',
        ];

        foreach ($menu as $nama) {                               // ulangi setiap menu, contoh $nama = 'divisi'
            foreach ($aksi as $akhiran => $keterangan) {         // ulangi setiap aksi, contoh $akhiran = 'create'
                $id_permission = 'admin.'.$nama.'.'.$akhiran;    // gabungkan jadi nama route, contoh 'admin.divisi.create'

                DB::table('permission')->insertOrIgnore([        // simpan permission; insertOrIgnore = lewati jika id sudah ada (aman dijalankan ulang)
                    'id_permission' => $id_permission,
                    'nm_permission' => $keterangan.' '.$nama,    // contoh 'Buka form tambah divisi'
                ]);

                DB::table('role_permission')->insertOrIgnore([  // berikan permission ini ke role admin
                    'id_role' => 'admin',
                    'id_permission' => $id_permission,
                ]);
            }
        }
    }
}
