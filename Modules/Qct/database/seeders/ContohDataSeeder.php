<?php

namespace Modules\Qct\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Contoh data transaksi QCT untuk demo dan pengujian:
// - 15-09-2026 S1 : lengkap dan laporannya sudah Terverifikasi (bulan September siap direkap Direktur)
// - 21-10-2026 S1 : lengkap, belum diverifikasi HOD
// - 21-10-2026 S2 : belum lengkap (troly T-04 menunggu Lab, T-05 & T-06 belum dicek visual)
class ContohDataSeeder extends Seeder
{
    private $hod = '2016-0001';

    public function run(): void
    {
        // ---------- Absensi: karyawan, tanggal, shift, area, kehadiran ----------
        $absensi = [
            ['2019-0001', '2026-09-15', 'S1', 'VIS', 'Hadir'],
            ['2020-0001', '2026-09-15', 'S1', 'LAB', 'Hadir'],
            ['2021-0001', '2026-09-15', 'S2', 'VIS', 'Hadir'],
            ['2021-0002', '2026-09-15', 'S2', null, 'Sakit'],
            ['2019-0001', '2026-09-16', 'S1', 'VIS', 'Hadir'],
            ['2020-0001', '2026-09-16', 'S1', null, 'Ijin'],
            ['2019-0001', '2026-10-21', 'S1', 'VIS', 'Hadir'],
            ['2020-0001', '2026-10-21', 'S1', 'LAB', 'Hadir'],
            ['2021-0001', '2026-10-21', 'S2', 'VIS', 'Hadir'],
            ['2021-0002', '2026-10-21', 'S2', 'LAB', 'Hadir'],
        ];
        foreach ($absensi as $a) {
            DB::table('absensi')->insert([
                'id_absen' => buat_kode('ABS', $a[1]),
                'id_karyawan' => $a[0],
                'tgl_absen' => $a[1],
                'shift' => $a[2],
                'id_area' => $a[3],
                'kehadiran' => $a[4],
                'created_by' => $this->hod,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------- Cek troly: doffing, visual, denier, grade, status, insp visual, insp lab ----------
        $check = [
            ['DOF-20260915-S1-0001', 'OK', 'OK', 'AX', 'Packing', '2019-0001', '2020-0001'],
            ['DOF-20260915-S1-0002', 'NG', 'OK', 'SS', 'Packing', '2019-0001', '2020-0001'],
            ['DOF-20261021-S1-0001', 'OK', 'OK', 'AX', 'Packing', '2019-0001', '2020-0001'],
            ['DOF-20261021-S1-0002', 'NG', 'OK', 'SS', 'Hold', '2019-0001', '2020-0001'],
            ['DOF-20261021-S1-0003', 'OK', 'OK', 'AX', 'Packing', '2019-0001', '2020-0001'],
            ['DOF-20261021-S2-0001', 'OK', null, null, null, '2021-0001', null],
        ];
        foreach ($check as $c) {
            $doffing = DB::table('doffing')->where('id_doffing', $c[0])->first();
            DB::table('check_batch')->insert([
                'id_check' => buat_kode('BTH', $doffing->tgl_doffing, $doffing->shift),
                'id_doffing' => $c[0],
                'hasil_visual' => $c[1],
                'hasil_denier' => $c[2],
                'id_grade' => $c[3],
                'status' => $c[4],
                'insp_visual' => $c[5],
                'insp_lab' => $c[6],
                'created_by' => $c[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------- Abnormal: doffing, mesin, jenis, denier, elong, tenacity, dti, grade, status ----------
        $abnormal = [
            ['DOF-20260915-S1-0002', '401-03', 'VS01', 75.80, 22.50, 4.10, 1.20, 'CL', 'Cutting'],
            ['DOF-20261021-S1-0002', '402-04', 'VS03', 151.20, 24.10, 3.90, 1.10, 'SS', 'Packing'],
            ['DOF-20261021-S1-0002', '402-07', 'VS02', 148.90, 20.30, 3.50, 1.40, 'CL', 'Cutting'],
        ];
        foreach ($abnormal as $b) {
            $doffing = DB::table('doffing')->where('id_doffing', $b[0])->first();
            DB::table('abnormal')->insert([
                'id_abnormal' => buat_kode('ABN', $doffing->tgl_doffing, $doffing->shift),
                'id_doffing' => $b[0],
                'kode_mesin' => $b[1],
                'id_jns_abn' => $b[2],
                'act_denier' => $b[3],
                'elong' => $b[4],
                'tenacity' => $b[5],
                'dti' => $b[6],
                'id_grade' => $b[7],
                'status' => $b[8],
                'insp_visual' => '2019-0001',
                'insp_lab' => '2020-0001',
                'created_by' => '2019-0001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------- Sampel: merge, tanggal, shift, jenis abnormal (null = OK), denier, elong, tenacity, dti, grade ----------
        $sampel = [
            ['4D001', '2026-09-15', 'S1', null, 75.40, 23.00, 4.20, 1.00, 'AX'],
            ['4D002', '2026-09-15', 'S1', null, 150.60, 24.00, 4.00, 1.10, 'AX'],
            ['4D001', '2026-10-21', 'S1', null, 75.20, 23.50, 4.30, 1.00, 'AX'],
            ['4D002', '2026-10-21', 'S1', 'VS03', 151.00, 22.80, 3.80, 1.30, 'SS'],
            ['4D003', '2026-10-21', 'S1', null, 100.30, 25.10, 4.10, 1.10, 'AX'],
        ];
        foreach ($sampel as $s) {
            DB::table('sampel')->insert([
                'id_sampel' => buat_kode('SMP', $s[1], $s[2]),
                'merge' => $s[0],
                'tgl_sampel' => $s[1],
                'shift' => $s[2],
                'id_jns_abn' => $s[3],
                'act_denier' => $s[4],
                'elong' => $s[5],
                'tenacity' => $s[6],
                'dti' => $s[7],
                'id_grade' => $s[8],
                'insp_lab' => '2020-0001',
                'created_by' => '2020-0001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------- Laporan 15-09-2026 S1 sudah diverifikasi HOD ----------
        DB::table('laporan_check')->insert([
            'id_laporan' => buat_kode('LAP', '2026-09-15', 'S1'),
            'tgl_laporan' => '2026-09-15',
            'shift' => 'S1',
            'status' => 'Terverifikasi',
            'diverifikasi_oleh' => $this->hod,
            'waktu_verifikasi' => '2026-09-16 08:00:00',
            'created_by' => $this->hod,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
