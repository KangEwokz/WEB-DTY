<?php

namespace Modules\Admin\Database\Seeders;                        // "alamat" class ini = folder Modules/Admin/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class AdminDatabaseSeeder extends Seeder                         // seeder = pengisi data contoh ke database
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed
    {
        DB::table('departemen')->insert([                        // isi tabel departemen (1 baris = 1 array)
            ['id_departemen' => '01', 'kd_departemen' => 'DTY', 'nm_departemen' => 'Draw Textured Yarn'],
            ['id_departemen' => '02', 'kd_departemen' => 'POY', 'nm_departemen' => 'Partially Oriented Yarn'],
            ['id_departemen' => '03', 'kd_departemen' => 'PEI', 'nm_departemen' => 'Politeknik Enginering Indorama'],
        ]);

        DB::table('divisi')->insert([                            // isi tabel divisi
            ['id_divisi' => '01', 'kd_divisi' => 'PRD', 'nm_divisi' => 'Produksi', 'id_departemen' => '01'],   // milik DTY
            ['id_divisi' => '02', 'kd_divisi' => 'QCT', 'nm_divisi' => 'QCT', 'id_departemen' => '01'],        // milik DTY
            ['id_divisi' => '03', 'kd_divisi' => 'ELK', 'nm_divisi' => 'Elektrik', 'id_departemen' => '02'],   // milik POY
            ['id_divisi' => '04', 'kd_divisi' => 'INS', 'nm_divisi' => 'Instrumen', 'id_departemen' => '02'],  // milik POY
        ]);

        DB::table('jabatan')->insert([                           // isi tabel jabatan (daftar umum)
            ['id_jabatan' => '01', 'nm_jabatan' => 'Direktur'],
            ['id_jabatan' => '02', 'nm_jabatan' => 'HOD'],       // Head of Department, memimpin departemen
            ['id_jabatan' => '03', 'nm_jabatan' => 'Kadiv'],     // Kepala Divisi, memimpin divisi
            ['id_jabatan' => '04', 'nm_jabatan' => 'Supervisor'],
            ['id_jabatan' => '05', 'nm_jabatan' => 'Admin'],     // bisa ada di departemen atau divisi mana pun
            ['id_jabatan' => '06', 'nm_jabatan' => 'Operator'],
            ['id_jabatan' => '07', 'nm_jabatan' => 'Teknisi'],
            ['id_jabatan' => '08', 'nm_jabatan' => 'CEO'],       // jabatan untuk superadmin
        ]);

        DB::table('area')->insert([                              // isi tabel area
            ['id_area' => 'VIS', 'nm_area' => 'Visual', 'id_divisi' => '02'], // milik divisi QCT
            ['id_area' => 'LAB', 'nm_area' => 'Lab', 'id_divisi' => '02'],    // milik divisi QCT
        ]);

        DB::table('karyawan')->insert([                          // isi tabel karyawan
            ['id_karyawan' => '2015-0001', 'nama_karyawan' => 'Budi Santoso', 'jns_kelamin' => 'L', 'id_departemen' => null, 'id_divisi' => null, 'id_jabatan' => '01', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2015-01-05', 'status_aktif' => 'Aktif'],        // Direktur
            ['id_karyawan' => '2015-0002', 'nama_karyawan' => 'Super Admin', 'jns_kelamin' => 'L', 'id_departemen' => '03', 'id_divisi' => null, 'id_jabatan' => '08', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2015-01-05', 'status_aktif' => 'Aktif'],         // CEO PEI (superadmin)
            ['id_karyawan' => '2016-0001', 'nama_karyawan' => 'Dewi Lestari', 'jns_kelamin' => 'P', 'id_departemen' => '01', 'id_divisi' => null, 'id_jabatan' => '02', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2016-06-01', 'status_aktif' => 'Aktif'],        // HOD DTY
            ['id_karyawan' => '2016-0002', 'nama_karyawan' => 'Rudi Hartono', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '02', 'id_jabatan' => '03', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2016-08-01', 'status_aktif' => 'Aktif'],        // Kadiv QCT
            ['id_karyawan' => '2017-0001', 'nama_karyawan' => 'Agus Prasetyo', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '01', 'id_jabatan' => '04', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2017-03-01', 'status_aktif' => 'Aktif'],       // Supervisor Produksi
            ['id_karyawan' => '2018-0001', 'nama_karyawan' => 'Siti Rahmawati', 'jns_kelamin' => 'P', 'id_departemen' => '01', 'id_divisi' => null, 'id_jabatan' => '05', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2018-02-01', 'status_aktif' => 'Aktif'],      // Admin departemen DTY
            ['id_karyawan' => '2019-0001', 'nama_karyawan' => 'Rina Marlina', 'jns_kelamin' => 'P', 'id_departemen' => '01', 'id_divisi' => '02', 'id_jabatan' => '06', 'id_area' => 'VIS', 'grup' => 'A', 'tgl_masuk' => '2019-04-01', 'status_aktif' => 'Aktif'],        // Operator QCT area Visual
            ['id_karyawan' => '2019-0002', 'nama_karyawan' => 'Joko Susilo', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '02', 'id_jabatan' => '06', 'id_area' => 'LAB', 'grup' => 'A', 'tgl_masuk' => '2019-04-01', 'status_aktif' => 'Aktif'],         // Operator QCT area Lab
            ['id_karyawan' => '2019-0003', 'nama_karyawan' => 'Hendra Wijaya', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '02', 'id_jabatan' => '06', 'id_area' => 'VIS', 'grup' => 'A', 'tgl_masuk' => '2019-04-01', 'status_aktif' => 'Non-Aktif'],   // sudah keluar (uji login ditolak)
            ['id_karyawan' => '2020-0001', 'nama_karyawan' => 'Bambang Saputra', 'jns_kelamin' => 'L', 'id_departemen' => '02', 'id_divisi' => '03', 'id_jabatan' => '07', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2020-02-01', 'status_aktif' => 'Aktif'],     // Teknisi Elektrik POY
            ['id_karyawan' => '2020-0002', 'nama_karyawan' => 'Yusuf Hidayat', 'jns_kelamin' => 'L', 'id_departemen' => '02', 'id_divisi' => '04', 'id_jabatan' => '07', 'id_area' => null, 'grup' => 'NG', 'tgl_masuk' => '2020-02-01', 'status_aktif' => 'Aktif'],       // Teknisi Instrumen POY
        ]);
    }
}
