<?php

namespace Modules\Auth\Database\Seeders;                         // "alamat" class ini = folder Modules/Auth/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class RolePermissionSeeder extends Seeder                        // pengisi data role, permission, dan pasangannya
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed
    {
        DB::table('role')->insert([                              // 1. isi tabel role (hak akses)
            ['id_role' => 'superadmin', 'nm_role' => 'Super Admin'],        // boleh membuka semua halaman
            ['id_role' => 'direktur', 'nm_role' => 'Direktur'],
            ['id_role' => 'admin', 'nm_role' => 'Admin'],
            ['id_role' => 'hod', 'nm_role' => 'Head of Department'],
            ['id_role' => 'kadiv', 'nm_role' => 'Kepala Divisi'],
            ['id_role' => 'supervisor', 'nm_role' => 'Supervisor'],
            ['id_role' => 'operator', 'nm_role' => 'Operator'],
            ['id_role' => 'teknisi', 'nm_role' => 'Teknisi'],
        ]);

        DB::table('permission')->insert([                        // 2. isi tabel permission (id = nama route)
            ['id_permission' => 'admin.karyawan.index', 'nm_permission' => 'Lihat daftar karyawan'],
            ['id_permission' => 'admin.karyawan.create', 'nm_permission' => 'Buka form tambah karyawan'],
            ['id_permission' => 'admin.karyawan.store', 'nm_permission' => 'Simpan karyawan baru'],
            ['id_permission' => 'admin.karyawan.edit', 'nm_permission' => 'Buka form ubah karyawan'],
            ['id_permission' => 'admin.karyawan.update', 'nm_permission' => 'Simpan perubahan karyawan'],
            ['id_permission' => 'admin.karyawan.destroy', 'nm_permission' => 'Hapus karyawan'],
        ]);

        $daftar = [                                              // 3. pasangan: role => daftar permission miliknya
            'admin' => [                                         // admin boleh kelola penuh data karyawan
                'admin.karyawan.index',
                'admin.karyawan.create',
                'admin.karyawan.store',
                'admin.karyawan.edit',
                'admin.karyawan.update',
                'admin.karyawan.destroy',
            ],
            'direktur' => [                                      // direktur hanya boleh melihat
                'admin.karyawan.index',
            ],
            'hod' => [                                           // hod hanya boleh melihat
                'admin.karyawan.index',
            ],
            'kadiv' => [                                         // kadiv hanya boleh melihat
                'admin.karyawan.index',
            ],
        ];                                                       // role yang tidak ditulis di sini = belum punya permission

        foreach ($daftar as $id_role => $permissions) {          // ulangi untuk setiap role, contoh $id_role = 'admin'
            foreach ($permissions as $id_permission) {           // ulangi untuk setiap permission milik role itu
                DB::table('role_permission')->insert([           // simpan pasangan ke tabel role_permission
                    'id_role' => $id_role,                       // contoh: 'admin'
                    'id_permission' => $id_permission,           // contoh: 'admin.karyawan.create'
                ]);
            }
        }
    }
}
