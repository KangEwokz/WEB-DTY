<?php

namespace Modules\Produksi\Database\Seeders;                     // "alamat" class ini = folder Modules/Produksi/database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class ProduksiDatabaseSeeder extends Seeder                      // seeder modul Produksi; insertOrIgnore = lewati data yang sudah ada, jadi aman dijalankan ulang
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed --class="Modules\Produksi\Database\Seeders\ProduksiDatabaseSeeder"
    {
        DB::table('area')->insertOrIgnore([                      // 1. contoh area kerja divisi Produksi (id_divisi 01)
            ['id_area' => 'CRL', 'nm_area' => 'Creel', 'id_divisi' => '01', 'status_aktif' => 'Aktif'],     // pemasangan bahan POY
            ['id_area' => 'DOF', 'nm_area' => 'Doffing', 'id_divisi' => '01', 'status_aktif' => 'Aktif'],   // pengambilan bobbin jadi
            ['id_area' => 'PCK', 'nm_area' => 'Packing', 'id_divisi' => '01', 'status_aktif' => 'Aktif'],   // pengemasan
        ]);

        DB::table('karyawan')->insertOrIgnore([                  // 2. contoh operator Produksi (departemen 01 DTY, divisi 01 Produksi, jabatan 06 Operator)
            ['id_karyawan' => '2021-0001', 'nama_karyawan' => 'Andi Kurniawan', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '01', 'id_jabatan' => '06', 'id_area' => 'CRL', 'grup' => 'A', 'tgl_masuk' => '2021-03-01', 'status_aktif' => 'Aktif'],
            ['id_karyawan' => '2021-0002', 'nama_karyawan' => 'Dedi Firmansyah', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '01', 'id_jabatan' => '06', 'id_area' => 'DOF', 'grup' => 'A', 'tgl_masuk' => '2021-03-01', 'status_aktif' => 'Aktif'],
            ['id_karyawan' => '2021-0003', 'nama_karyawan' => 'Lina Kartika', 'jns_kelamin' => 'P', 'id_departemen' => '01', 'id_divisi' => '01', 'id_jabatan' => '06', 'id_area' => 'PCK', 'grup' => 'B', 'tgl_masuk' => '2021-07-01', 'status_aktif' => 'Aktif'],
            ['id_karyawan' => '2021-0004', 'nama_karyawan' => 'Wahyu Pratama', 'jns_kelamin' => 'L', 'id_departemen' => '01', 'id_divisi' => '01', 'id_jabatan' => '06', 'id_area' => 'DOF', 'grup' => 'B', 'tgl_masuk' => '2021-07-01', 'status_aktif' => 'Aktif'],
        ]);

        foreach ([['M075A', 75, 36, 'Kuning'], ['M100A', 100, 48, 'Hijau'], ['M150A', 150, 48, 'Merah'], ['M300A', 300, 96, 'Biru']] as $b) { // 3. master benang (data proyek QCT lama); urutan isi: [merge, denier, filamen, warna ppt]
            DB::table('benang')->insertOrIgnore([
                'merge' => $b[0],
                'denier' => $b[1],
                'filamen' => $b[2],
                'warna_ppt' => $b[3],
                'toleransi_denier' => 3.00,                      // toleransi 3% (nilai sementara dari proyek lama)
                'status_aktif' => 'Aktif',
                'created_by' => '2017-0001',                     // diinput Supervisor Produksi (Agus Prasetyo)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $nomor = 1;                                              // 4. master mesin: 30 mesin, awalnya L1-L3 masing-masing 10 posisi; $nomor = nomor urut kode mesin
        foreach (['L1', 'L2', 'L3'] as $line) {                  // ulangi setiap line
            for ($posisi = 1; $posisi <= 10; $posisi++) {        // ulangi posisi 1 sampai 10
                DB::table('mesin')->insertOrIgnore([
                    'kode_mesin' => 'MCN-'.str_pad($nomor++, 3, '0', STR_PAD_LEFT), // nomor dibuat 3 digit: 'MCN-' + '001'; $nomor++ = pakai nomor lalu tambah 1
                    'line' => $line,
                    'posisi' => $posisi,
                    'status' => 'Running',
                    'created_by' => '2017-0001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach (['VS01' => 'Handling', 'VS02' => 'OTY', 'VS03' => 'Hairy', 'VS04' => 'Oil Spot', 'VS05' => 'Tight Spot'] as $kode => $nama) { // master jenis abnormal (data proyek QCT lama)
            DB::table('jenis_abnormal')->insertOrIgnore([
                'id_jns_abn' => $kode,
                'nm_abnormal' => $nama,
                'status_aktif' => 'Aktif',
                'created_by' => '2016-0001',                     // diinput HOD (Dewi Lestari), seperti proyek lama
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['Normal' => ['TRN', 10], 'Abnormal' => ['TRA', 5]] as $jenis => $isi) { // master troly: 10 troly normal + 5 troly abnormal; $isi = [awalan kode, jumlah]
            for ($i = 1; $i <= $isi[1]; $i++) {                  // ulangi sebanyak jumlahnya
                DB::table('troly')->insertOrIgnore([
                    'kode_troly' => $isi[0].'-'.str_pad($i, 3, '0', STR_PAD_LEFT), // contoh 'TRN' + '-' + '001'
                    'jenis' => $jenis,
                    'kapasitas' => 20,                           // 20 bobbin per troly
                    'status_aktif' => 'Aktif',
                    'created_by' => '2017-0001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $menu = ['absensi', 'laporan', 'doffing'];               // 5. permission: menu produksi (= bagian tengah nama route)
        $aksi = [                                                // akhiran nama route => keterangan
            'index' => 'Lihat daftar',
            'show' => 'Lihat detail',
            'create' => 'Buka form tambah',
            'store' => 'Simpan',
            'edit' => 'Buka form ubah',
            'update' => 'Simpan perubahan',
            'destroy' => 'Hapus',
        ];

        foreach ($menu as $nama) {                               // ulangi setiap menu, contoh 'absensi'
            foreach ($aksi as $akhiran => $keterangan) {         // ulangi setiap aksi, contoh 'create'
                DB::table('permission')->insertOrIgnore([        // simpan permission, contoh produksi.absensi.create
                    'id_permission' => 'produksi.'.$nama.'.'.$akhiran,
                    'nm_permission' => $keterangan.' '.$nama.' produksi',
                ]);
            }
        }

        DB::table('permission')->insertOrIgnore([                // 6. permission master benang & mesin
            ['id_permission' => 'produksi.benang.index', 'nm_permission' => 'Lihat daftar benang'],
            ['id_permission' => 'produksi.benang.create', 'nm_permission' => 'Buka form tambah benang'],
            ['id_permission' => 'produksi.benang.store', 'nm_permission' => 'Simpan benang'],
            ['id_permission' => 'produksi.benang.edit', 'nm_permission' => 'Buka form ubah benang'],
            ['id_permission' => 'produksi.benang.update', 'nm_permission' => 'Simpan perubahan benang'],
            ['id_permission' => 'produksi.benang.status', 'nm_permission' => 'Aktifkan/nonaktifkan benang'],
            ['id_permission' => 'produksi.mesin.index', 'nm_permission' => 'Lihat daftar mesin'],
            ['id_permission' => 'produksi.mesin.create', 'nm_permission' => 'Buka form tambah mesin'],
            ['id_permission' => 'produksi.mesin.store', 'nm_permission' => 'Simpan mesin baru'],
            ['id_permission' => 'produksi.mesin.edit', 'nm_permission' => 'Buka form ubah mesin (line, posisi, status)'],
            ['id_permission' => 'produksi.mesin.update', 'nm_permission' => 'Simpan perubahan mesin'],
        ]);

        foreach (['troly' => 'troly', 'jenis-abnormal' => 'jenis abnormal'] as $kode => $nama) { // 6b. permission master troly & jenis abnormal; $kode = bagian tengah nama route
            foreach (['index' => 'Lihat daftar', 'create' => 'Buka form tambah', 'store' => 'Simpan', 'edit' => 'Buka form ubah', 'update' => 'Simpan perubahan', 'status' => 'Aktifkan/nonaktifkan'] as $akhiran => $keterangan) {
                DB::table('permission')->insertOrIgnore([
                    'id_permission' => 'produksi.'.$kode.'.'.$akhiran, // contoh produksi.jenis-abnormal.create
                    'nm_permission' => $keterangan.' '.$nama,
                ]);
            }
        }

        DB::table('permission')->insertOrIgnore([                // 6c. permission rekap bulanan
            ['id_permission' => 'produksi.rekap-bulanan.index', 'nm_permission' => 'Buka rekap produksi bulanan'],
            ['id_permission' => 'produksi.rekap-bulanan.cetak', 'nm_permission' => 'Cetak rekap produksi bulanan'],
        ]);

        $lihat = ['produksi.rekap-bulanan.index', 'produksi.rekap-bulanan.cetak', 'produksi.absensi.index', 'produksi.absensi.show', 'produksi.laporan.index', 'produksi.laporan.show', 'produksi.doffing.index','produksi.benang.index', 'produksi.mesin.index', 'produksi.troly.index', 'produksi.jenis-abnormal.index']; // permission "hanya melihat"

        $daftar = [                                              // 7. pasangan role => permission miliknya
            'supervisor' => [                                    // supervisor mencatat absensi & laporan + mengelola master benang & mesin
                'produksi.absensi.index', 'produksi.absensi.show', 'produksi.absensi.create', 'produksi.absensi.store', 'produksi.absensi.edit', 'produksi.absensi.update', 'produksi.absensi.destroy',
                'produksi.laporan.index', 'produksi.laporan.show', 'produksi.laporan.create', 'produksi.laporan.store', 'produksi.laporan.edit', 'produksi.laporan.update', 'produksi.laporan.destroy',
                'produksi.rekap-bulanan.index', 'produksi.rekap-bulanan.cetak',
                'produksi.doffing.index', 'produksi.doffing.create', 'produksi.doffing.store', 'produksi.doffing.edit', 'produksi.doffing.update', 'produksi.doffing.destroy',
                'produksi.benang.index', 'produksi.benang.create', 'produksi.benang.store', 'produksi.benang.edit', 'produksi.benang.update', 'produksi.benang.status',
                'produksi.mesin.index', 'produksi.mesin.create', 'produksi.mesin.store', 'produksi.mesin.edit', 'produksi.mesin.update',
                'produksi.troly.index', 'produksi.troly.create', 'produksi.troly.store', 'produksi.troly.edit', 'produksi.troly.update', 'produksi.troly.status',
                'produksi.jenis-abnormal.index', 'produksi.jenis-abnormal.create', 'produksi.jenis-abnormal.store', 'produksi.jenis-abnormal.edit', 'produksi.jenis-abnormal.update', 'produksi.jenis-abnormal.status',
            ],
            'hod' => $lihat,                                     // hanya melihat
            'kadiv' => $lihat,                                   // hanya melihat
            'direktur' => $lihat,                                // hanya melihat
        ];

        foreach ($daftar as $id_role => $permissions) {          // ulangi setiap role
            foreach ($permissions as $id_permission) {           // ulangi setiap permission milik role itu
                DB::table('role_permission')->insertOrIgnore([   // simpan pasangan; lewati jika sudah ada
                    'id_role' => $id_role,
                    'id_permission' => $id_permission,
                ]);
            }
        }
    }
}
