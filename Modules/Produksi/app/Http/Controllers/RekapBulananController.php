<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class RekapBulananController extends Controller                  // rekap produksi bulanan: halaman pilih bulan + halaman cetak (A4 landscape)
{
    public function index(Request $request)                      // halaman pilih bulan + pratinjau (route GET /produksi/rekap-bulanan)
    {
        $bulan = $this->bulanDipilih($request);                  // bulan yang dipilih, contoh '2026-11'

        return view('produksi::rekap-bulanan.index', compact('bulan')); // tampilkan halaman; isinya pratinjau dari route cetak (iframe)
    }

    public function cetak(Request $request)                      // halaman cetak tanpa sidebar (route GET /produksi/rekap-bulanan/cetak?bulan=2026-11)
    {
        $bulan = $this->bulanDipilih($request);                  // bulan yang dipilih
        $awal = $bulan.'-01';                                    // tanggal pertama, contoh '2026-11-01'
        $akhir = date('Y-m-t', strtotime($awal));                // tanggal terakhir; 't' = jumlah hari di bulan itu, contoh '2026-11-30'

        $laporan = DB::table('laporan_shift')                    // 1. angka dari laporan akhir shift, dijumlah per tanggal
            ->whereBetween('tgl_laporan', [$awal, $akhir])       // whereBetween = di antara tanggal awal dan akhir
            ->select(
                'tgl_laporan',
                DB::raw('count(*) as jml_shift'),                // berapa shift yang sudah dilaporkan (maks 3)
                DB::raw('sum(jml_break) as brk'),                // sum = jumlahkan
                DB::raw('sum(jml_problem_mesin) as problem'),
                DB::raw('sum(bleeding_time) as bleeding'),
                DB::raw('sum(suspend_time) as suspend'),
                DB::raw('sum(waste) as waste'),
                DB::raw('sum(jml_abnormal) as abnormal')
            )
            ->groupBy('tgl_laporan')                             // dikelompokkan per tanggal
            ->get()
            ->keyBy('tgl_laporan');                              // keyBy = jadikan daftar berkunci tanggal, agar mudah dicari: $laporan['2026-11-01']

        $doffing = DB::table('doffing')                          // 2. troly, bobbin, berat dari tabel doffing, per tanggal
            ->whereBetween('tgl_doffing', [$awal, $akhir])
            ->select('tgl_doffing', DB::raw('count(*) as troly'), DB::raw('sum(jml_bobbin) as bobbin'), DB::raw('sum(berat_troly) as berat'))
            ->groupBy('tgl_doffing')
            ->get()
            ->keyBy('tgl_doffing');

        $kehadiran = DB::table('absensi_detail')                 // 3. kehadiran operator per tanggal (jumlah dari semua shift)
            ->join('absensi_shift', 'absensi_detail.id_absensi', '=', 'absensi_shift.id_absensi')
            ->whereBetween('absensi_shift.tgl_absen', [$awal, $akhir])
            ->select('absensi_shift.tgl_absen', DB::raw("sum(absensi_detail.kehadiran = 'Hadir') as hadir"), DB::raw('count(*) as total')) // sum(kondisi) = hitung yang kondisinya benar
            ->groupBy('absensi_shift.tgl_absen')
            ->get()
            ->keyBy('tgl_absen');

        $nama_hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; // urutan sesuai date('w'): 0 = Minggu
        $kolom = ['jml_shift', 'troly', 'bobbin', 'berat', 'brk', 'problem', 'bleeding', 'suspend', 'waste', 'abnormal', 'hadir', 'total_operator']; // kolom angka di tabel
        $total = array_fill_keys($kolom, 0);                     // wadah total sebulan, semua mulai 0; array_fill_keys = buat array dari daftar kunci dengan nilai sama
        $hari_produksi = 0;                                      // jumlah hari yang ada datanya (untuk rata-rata)
        $baris = [];                                             // wadah baris tabel per tanggal

        for ($hari = 1; $hari <= (int) date('t', strtotime($awal)); $hari++) { // ulangi tanggal 1 sampai akhir bulan
            $tanggal = $bulan.'-'.str_pad($hari, 2, '0', STR_PAD_LEFT); // contoh '2026-11-05'
            $l = $laporan[$tanggal] ?? null;                     // data laporan tanggal ini (null jika tidak ada)
            $d = $doffing[$tanggal] ?? null;                     // data doffing
            $k = $kehadiran[$tanggal] ?? null;                   // data kehadiran

            $isi = [                                             // angka-angka 1 baris
                'jml_shift' => $l->jml_shift ?? null,
                'troly' => $d->troly ?? null,
                'bobbin' => $d->bobbin ?? null,
                'berat' => $d->berat ?? null,
                'brk' => $l->brk ?? null,
                'problem' => $l->problem ?? null,
                'bleeding' => $l->bleeding ?? null,
                'suspend' => $l->suspend ?? null,
                'waste' => $l->waste ?? null,
                'abnormal' => $l->abnormal ?? null,
                'hadir' => $k->hadir ?? null,
                'total_operator' => $k->total ?? null,
            ];

            $ada_data = $l || $d || $k;                          // true jika tanggal ini punya data apa pun
            if ($ada_data) {
                $hari_produksi++;                                // hitung sebagai hari produksi
                foreach ($kolom as $c) {
                    $total[$c] += $isi[$c] ?? 0;                 // tambahkan ke total sebulan
                }
            }

            $baris[] = [                                         // simpan 1 baris
                'tanggal' => $hari,
                'hari' => $nama_hari[date('w', strtotime($tanggal))], // nama hari, contoh 'Kamis'
                'minggu' => date('w', strtotime($tanggal)) == 0, // true jika hari Minggu (diberi warna)
                'ada_data' => $ada_data,
                'isi' => $isi,
            ];
        }

        $rata = [];                                              // rata-rata per hari produksi
        foreach ($kolom as $c) {
            $rata[$c] = $hari_produksi ? $total[$c] / $hari_produksi : 0; // hindari bagi 0 jika belum ada data
        }

        $abnormal_jenis = DB::table('abnormal_produksi')         // 4. abnormal per jenis selama sebulan
            ->join('laporan_shift', 'abnormal_produksi.id_laporan', '=', 'laporan_shift.id_laporan')
            ->join('jenis_abnormal', 'abnormal_produksi.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->whereBetween('laporan_shift.tgl_laporan', [$awal, $akhir])
            ->select('jenis_abnormal.nm_abnormal', DB::raw('count(*) as jumlah'))
            ->groupBy('jenis_abnormal.nm_abnormal')
            ->orderBy('jumlah', 'desc')                          // terbanyak di atas
            ->get();

        $waste_terbesar = $laporan->sortByDesc('waste')->first(); // 5. tanggal dengan waste terbesar; sortByDesc = urutkan dari terbesar

        $pembuat = DB::table('karyawan')                         // 6. tanda tangan: yang mencetak
            ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->where('karyawan.id_karyawan', Auth::user()->id_karyawan)
            ->select('karyawan.nama_karyawan', 'jabatan.nm_jabatan')
            ->first();
        $kadiv = $this->pejabat('Kadiv');                        // Kadiv Produksi (null jika belum ada)
        $hod = $this->pejabat('HOD');                            // HOD departemen DTY

        $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; // index 1-12
        $judul_bulan = strtoupper($nama_bulan[(int) substr($bulan, 5, 2)]).' '.substr($bulan, 0, 4); // contoh 'NOVEMBER 2026'
        $no_dokumen = 'RPB-'.str_replace('-', '', $bulan);       // contoh 'RPB-202611'
        $pratinjau = $request->has('pratinjau');                 // true jika dibuka di dalam halaman pratinjau (tidak langsung print)

        return view('produksi::rekap-bulanan.cetak', compact(
            'bulan', 'baris', 'total', 'rata', 'hari_produksi', 'abnormal_jenis', 'waste_terbesar',
            'pembuat', 'kadiv', 'hod', 'judul_bulan', 'no_dokumen', 'pratinjau'
        ));                                                      // tampilkan halaman cetak
    }

    private function bulanDipilih(Request $request)              // FUNGSI BANTU: ambil bulan dari URL (?bulan=2026-11); jika kosong/salah pakai bulan ini
    {
        $bulan = $request->bulan;                                // isi dari URL

        if (! preg_match('/^\d{4}-\d{2}$/', (string) $bulan)) {  // preg_match = cek pola: 4 angka - 2 angka (contoh 2026-11)
            $bulan = date('Y-m');                                // bawaan: bulan sekarang
        }

        return $bulan;
    }

    private function pejabat(string $nm_jabatan)                 // cari karyawan aktif pemegang jabatan di Produksi/DTY untuk kolom tanda tangan
    {
        $query = DB::table('karyawan')
            ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->where('jabatan.nm_jabatan', $nm_jabatan)
            ->where('karyawan.status_aktif', 'Aktif');

        if ($nm_jabatan == 'Kadiv') {                            // Kadiv: harus kadiv divisi Produksi
            $query->join('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')->where('divisi.kd_divisi', 'PRD');
        } else {                                                 // HOD: harus HOD departemen DTY
            $query->join('departemen', 'karyawan.id_departemen', '=', 'departemen.id_departemen')->where('departemen.kd_departemen', 'DTY');
        }

        return $query->value('karyawan.nama_karyawan');          // ambil namanya saja; null jika tidak ada
    }
}
