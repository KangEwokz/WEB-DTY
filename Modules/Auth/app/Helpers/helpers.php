<?php

use Illuminate\Support\Facades\DB;

/**
 * Membuat kode PK yang bisa dibaca (format sesuai SKEMA.md).
 *
 * Contoh:
 *   buat_kode('DOF', '2026-10-20', 'S1')  -> DOF-20261020-S1-0001
 *   buat_kode('JDW', '2026-10-20')        -> JDW-20261020-0001
 *   buat_kode('KRY', '2019-03-01')        -> 2019-0001  (id_karyawan: tahun masuk + urut)
 *   buat_kode('LAP', '2026-10-20', 'S1')  -> LAP-20261020-S1
 *   buat_kode('RAB', '2026-10')           -> RAB-202610
 *   buat_kode('DIV') / buat_kode('JAB')    -> 04  (id_divisi / id_jabatan berikutnya)
 *   buat_kode('VS')                       -> VS04 (id_jns_abn berikutnya)
 */
function buat_kode($prefix, $tanggal = null, $shift = null)
{
    // Kode 2 digit untuk divisi dan jabatan: 01, 02, ...
    if ($prefix == 'DIV' || $prefix == 'JAB') {
        $tabel = $prefix == 'DIV' ? 'divisi' : 'jabatan';
        $terakhir = DB::table($tabel)->max('id_'.$tabel);

        return str_pad((int) $terakhir + 1, 2, '0', STR_PAD_LEFT);
    }

    // Kode jenis abnormal: VS01, VS02, ...
    if ($prefix == 'VS') {
        $terakhir = DB::table('jenis_abnormal')->where('id_jns_abn', 'like', 'VS%')->max('id_jns_abn');

        return 'VS'.str_pad((int) substr((string) $terakhir, 2) + 1, 2, '0', STR_PAD_LEFT);
    }

    // Kode tanpa nomor urut: satu laporan per tanggal+shift, satu rekap per periode
    if ($prefix == 'LAP') {
        return 'LAP-'.date('Ymd', strtotime($tanggal)).'-'.$shift;
    }
    if ($prefix == 'RAB' || $prefix == 'RCK') {
        return $prefix.'-'.str_replace('-', '', substr($tanggal, 0, 7));
    }

    // Prefix => [nama tabel, kolom PK]
    $daftar = [
        'KRY' => ['karyawan', 'id_karyawan'],
        'JDW' => ['jadwal_produksi', 'id_jadwal'],
        'DOF' => ['doffing', 'id_doffing'],
        'ABS' => ['absensi', 'id_absen'],
        'BTH' => ['check_batch', 'id_check'],
        'ABN' => ['abnormal', 'id_abnormal'],
        'SMP' => ['sampel', 'id_sampel'],
    ];
    $tabel = $daftar[$prefix][0];
    $kolom = $daftar[$prefix][1];

    // Bagian depan kode sebelum nomor urut
    if ($prefix == 'KRY') {
        $awalan = date('Y', strtotime($tanggal)).'-';
    } else {
        $awalan = $prefix.'-'.date('Ymd', strtotime($tanggal)).'-';
        if ($shift) {
            $awalan .= $shift.'-';
        }
    }

    // Ambil kode terbesar dengan awalan yang sama, lalu tambah 1
    $terakhir = DB::table($tabel)->where($kolom, 'like', $awalan.'%')->max($kolom);
    $urut = $terakhir ? (int) substr($terakhir, -4) + 1 : 1;

    return $awalan.str_pad($urut, 4, '0', STR_PAD_LEFT);
}

/**
 * Shift yang sedang berjalan beserta tanggalnya.
 * Jam shift pabrik: S1 06.00-14.00, S2 14.00-22.00, S3 22.00-06.00.
 * S3 dicatat pada tanggal mulainya, jadi pukul 00.00-05.59 masih S3 tanggal kemarin.
 *
 * Contoh: shift_sekarang() -> ['tanggal' => '2026-10-21', 'shift' => 'S3']
 */
function shift_sekarang()
{
    $jam = now()->hour;

    if ($jam >= 6 && $jam < 14) {
        return ['tanggal' => now()->toDateString(), 'shift' => 'S1'];
    }
    if ($jam >= 14 && $jam < 22) {
        return ['tanggal' => now()->toDateString(), 'shift' => 'S2'];
    }
    if ($jam >= 22) {
        return ['tanggal' => now()->toDateString(), 'shift' => 'S3'];
    }

    // Pukul 00.00-05.59: lanjutan S3 yang mulai kemarin malam
    return ['tanggal' => now()->subDay()->toDateString(), 'shift' => 'S3'];
}

/**
 * Nama divisi user yang sedang login (Admin, Produksi, QCT), atau null untuk Direktur.
 */
function divisi_user()
{
    return DB::table('karyawan')
        ->join('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
        ->where('karyawan.id_karyawan', auth()->user()->id_karyawan)
        ->value('divisi.nm_divisi');
}

/**
 * Area kerja user yang sedang login pada shift ini (VIS, LAB, KPG, ...).
 * Diambil dari absensi tanggal+shift yang sedang berjalan (atau GS untuk non-grup).
 * Kalau absensi belum diisi, pakai area default karyawan.
 */
function area_aktif()
{
    $id_karyawan = auth()->user()->id_karyawan;
    $sekarang = shift_sekarang();

    $area = DB::table('absensi')
        ->where('id_karyawan', $id_karyawan)
        ->where('tgl_absen', $sekarang['tanggal'])
        ->whereIn('shift', [$sekarang['shift'], 'GS'])
        ->value('id_area');

    if (! $area) {
        $area = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->value('id_area');
    }

    return $area;
}

/**
 * Cek apakah data sudah dikunci karena sudah diverifikasi HOD.
 * - Dengan shift : cek laporan_check tanggal+shift (untuk check_batch, abnormal, sampel).
 * - Tanpa shift  : cek rekap_absensi bulan dari tanggal tersebut (untuk absensi).
 */
function sudah_terkunci($tanggal, $shift = null)
{
    if ($shift) {
        return DB::table('laporan_check')
            ->where('tgl_laporan', $tanggal)
            ->where('shift', $shift)
            ->where('status', 'Terverifikasi')
            ->exists();
    }

    return DB::table('rekap_absensi')
        ->where('periode', substr($tanggal, 0, 7))
        ->where('status', 'Terverifikasi')
        ->exists();
}
