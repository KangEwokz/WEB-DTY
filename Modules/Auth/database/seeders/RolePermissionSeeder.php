<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus cache permission Spatie sebelum mengisi ulang
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Satu permission per use case (UC01 Login dan UC02 Logout tidak perlu permission)
        $permission = [
            'kelola-karyawan',            // UC03
            'kelola-akun',                // UC04
            'lihat-rekap-absensi',        // UC05
            'kelola-jadwal-produksi',     // UC06 (+ benang, mesin)
            'kelola-doffing',             // UC07
            'kelola-master-qct',          // UC08 (area QCT, grade, jenis abnormal)
            'kelola-absensi',             // UC09
            'verifikasi-rekap-absensi',   // UC10
            'verifikasi-laporan-check',   // UC11
            'kelola-cek-visual',          // UC12
            'kelola-cek-denier',          // UC13
            'kelola-abnormal',            // UC14
            'kelola-sampel',              // UC15
            'lihat-rekap-check',          // UC16
            'verifikasi-rekap-check',     // UC17
            'arsipkan-rekap-check',       // UC18
        ];
        foreach ($permission as $nama) {
            Permission::create(['name' => $nama]);
        }

        // Role dan permission-nya (lihat tabel RBAC di CLAUDE.md)
        Role::create(['name' => 'staf'])->givePermissionTo([
            'kelola-karyawan', 'kelola-akun', 'lihat-rekap-absensi',
        ]);
        Role::create(['name' => 'supervisor'])->givePermissionTo([
            'kelola-jadwal-produksi', 'kelola-doffing',
        ]);
        Role::create(['name' => 'hod'])->givePermissionTo([
            'kelola-master-qct', 'kelola-absensi', 'verifikasi-rekap-absensi', 'verifikasi-laporan-check',
        ]);
        // Operator Visual dan Lab memakai role yang sama; pembedanya middleware area:VIS / area:LAB
        Role::create(['name' => 'operator'])->givePermissionTo([
            'kelola-cek-visual', 'kelola-cek-denier', 'kelola-abnormal', 'kelola-sampel',
        ]);
        Role::create(['name' => 'direktur'])->givePermissionTo([
            'lihat-rekap-check', 'verifikasi-rekap-check', 'arsipkan-rekap-check',
        ]);

        // Khusus demo: superadmin mendapat semua permission (di luar 6 aktor rancangan)
        Role::create(['name' => 'superadmin'])->givePermissionTo($permission);
    }
}
