<?php

namespace Modules\Produksi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProduksiDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BenangSeeder::class,
            MesinSeeder::class,
        ]);

        // Jadwal dan doffing hanya menyimpan kode merge. Info benangnya (denier, filamen, warna ppt)
        // ada di tabel benang (BenangSeeder) dan diambil lewat join. Keterangan di kanan baris = info benangnya.

        // Contoh jadwal produksi: satu merge per line. Kolom: merge, line, posisi awal, posisi akhir, customer
        $jadwal = [
            ['4D001', 401, 1, 10, 'PT Tekstil Nusantara'], // 75/36 Putih
            ['4D002', 402, 1, 10, 'CV Benang Jaya'], // 150/48 Biru
            ['4D003', 403, 1, 10, 'PT Tekstil Nusantara'], // 100/96 Kuning
        ];
        foreach ($jadwal as $j) {
            DB::table('jadwal_produksi')->insert([
                'id_jadwal' => buat_kode('JDW', '2026-10-21'),
                'merge' => $j[0],
                'tgl_produksi' => '2026-10-21',
                'line' => $j[1],
                'pos_awal' => $j[2],
                'pos_akhir' => $j[3],
                'customer' => $j[4],
                'created_by' => '2017-0001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Contoh doffing per troly. Kolom: tanggal, shift, nomor troly, merge, jumlah bobbin, berat (kg)
        $doffing = [
            ['2026-09-15', 'S1', 'T-01', '4D001', 60, 210.00], // 75/36 Putih
            ['2026-09-15', 'S1', 'T-02', '4D002', 60, 300.00], // 150/48 Biru
            ['2026-10-21', 'S1', 'T-01', '4D003', 60, 210.50], // 100/96 Kuning
            ['2026-10-21', 'S1', 'T-02', '4D004', 60, 305.25], // 300/96 Hijau
            ['2026-10-21', 'S1', 'T-03', '4D005', 60, 250.00], // 200/48 Merah
            ['2026-10-21', 'S2', 'T-04', '4D003', 60, 212.75], // 100/96 Kuning
            ['2026-10-21', 'S2', 'T-05', '4D004', 60, 300.10], // 300/96 Hijau
            ['2026-10-21', 'S2', 'T-06', '4D005', 60, 248.40], // 200/48 Merah
        ];
        foreach ($doffing as $d) {
            DB::table('doffing')->insert([
                'id_doffing' => buat_kode('DOF', $d[0], $d[1]),
                'tgl_doffing' => $d[0],
                'shift' => $d[1],
                'nmr_troly' => $d[2],
                'merge' => $d[3],
                'jml_bobbin' => $d[4],
                'berat_troly' => $d[5],
                'created_by' => '2017-0001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
