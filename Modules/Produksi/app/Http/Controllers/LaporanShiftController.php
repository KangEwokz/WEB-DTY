<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class LaporanShiftController extends Controller                  // laporan akhir shift: total output 1 shift
{
    public function index(Request $request)                      // tampil daftar laporan (route GET /produksi/laporan)
    {
        $query = DB::table('laporan_shift')                      // mulai dari tabel laporan_shift
            ->join('karyawan', 'laporan_shift.created_by', '=', 'karyawan.id_karyawan') // gabung karyawan untuk ambil nama supervisor
            ->select('laporan_shift.*', 'karyawan.nama_karyawan as nama_spv');

        if ($request->tanggal) {                                 // jika filter tanggal diisi
            $query->where('laporan_shift.tgl_laporan', $request->tanggal); // tampilkan tanggal itu saja
        }                                                        // akhir if

        $laporan = $query->orderBy('laporan_shift.tgl_laporan', 'desc') // tanggal terbaru di atas
            ->orderBy('laporan_shift.shift', 'desc')
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        return view('produksi::laporan.index', compact('laporan')); // tampilkan Modules/Produksi/resources/views/laporan/index.blade.php
    }

    public function create()                                     // tampil form laporan (route GET /produksi/laporan/create)
    {
        $jam = (int) date('H');                                  // jam sekarang 0-23
        $shift_sekarang = $jam >= 7 && $jam < 15 ? 'S1' : ($jam >= 15 && $jam < 23 ? 'S2' : 'S3'); // tebak shift: 07-15 S1, 15-23 S2, selain itu S3

        $pilihan = $this->pilihanAbnormal();                     // pilihan dropdown untuk tabel temuan abnormal (mesin, benang, jenis, troly)
        $abnormal_lama = [];                                     // belum ada temuan tersimpan (form baru)

        return view('produksi::laporan.create', compact('shift_sekarang', 'pilihan', 'abnormal_lama')); // tampilkan form
    }

    public function store(Request $request)                      // simpan laporan (route POST /produksi/laporan)
    {
        $request->validate(                                      // periksa isi form
            array_merge(['tgl_laporan' => 'required|date', 'shift' => 'required|in:S1,S2,S3'], $this->aturan()), // array_merge = gabungkan aturan tanggal & shift dengan aturan angka + abnormal di fungsi aturan()
            $this->pesanError(),
            $this->namaKolom()
        );                                                       // akhir validasi

        $id_laporan = 'LAP-'.date('Ymd', strtotime($request->tgl_laporan)).'-'.$request->shift; // buat id, contoh LAP-20261112-S1

        if (DB::table('laporan_shift')->where('id_laporan', $id_laporan)->exists()) { // jika laporan tanggal + shift ini sudah ada
            return back()->with('error', 'Laporan tanggal '.date('d-m-Y', strtotime($request->tgl_laporan)).' shift '.$request->shift.' sudah ada. Silakan ubah data yang sudah ada.')->withInput(); // tolak + pesan
        }                                                        // akhir if

        $abnormal = array_values($request->abnormal ?? []);      // daftar bobbin abnormal; array_values = nomori ulang 0,1,2,... (baris yang dihapus di form meninggalkan nomor kosong)
        $error = $this->cekKapasitas($abnormal);                 // cek isi troly abnormal tidak melebihi kapasitas
        if ($error) {
            return back()->withErrors(['abnormal' => $error])->withInput(); // kembali + pesan
        }

        DB::table('laporan_shift')->insert(array_merge($this->dataAngka($request), [ // gabungkan angka-angka laporan dengan data kepala
            'id_laporan' => $id_laporan,
            'tgl_laporan' => $request->tgl_laporan,
            'shift' => $request->shift,
            'jml_abnormal' => count($abnormal),                  // total bobbin abnormal = jumlah baris rincian
            'jml_doffing' => $this->hitungDoffing($request->tgl_laporan, $request->shift), // jumlah troly doffing di tanggal & shift ini
            'created_by' => Auth::user()->id_karyawan,           // supervisor yang sedang login
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $this->simpanAbnormal($id_laporan, $abnormal);           // simpan rincian per bobbin

        return redirect()->route('produksi.laporan.show', $id_laporan)->with('success', 'Laporan shift '.$request->shift.' berhasil disimpan.'); // ke detail + pesan sukses
    }

    public function show(string $id)                             // tampil detail laporan (route GET /produksi/laporan/{id})
    {
        $laporan = $this->ambilLaporan($id);                     // ambil laporan + nama pencatat

        $id_absensi = str_replace('LAP-', 'ABS-', $id);          // absensi shift yang sama: LAP-20261112-S1 -> ABS-20261112-S1
        $absensi = DB::table('absensi_detail')                   // hitung kehadiran shift ini
            ->where('id_absensi', $id_absensi)
            ->select('kehadiran', DB::raw('count(*) as jumlah')) // jumlah operator per kehadiran
            ->groupBy('kehadiran')                               // groupBy = kelompokkan per nilai kehadiran
            ->pluck('jumlah', 'kehadiran');                      // hasil: ['Hadir' => 4, 'Sakit' => 1]

        $abnormal = DB::table('abnormal_produksi')               // rincian bobbin abnormal laporan ini
            ->join('mesin', 'abnormal_produksi.kode_mesin', '=', 'mesin.kode_mesin')                 // + lokasi mesin
            ->join('jenis_abnormal', 'abnormal_produksi.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn') // + nama jenis abnormal
            ->select('abnormal_produksi.*', 'mesin.line', 'mesin.posisi', 'jenis_abnormal.nm_abnormal')
            ->where('abnormal_produksi.id_laporan', $id)
            ->orderBy('abnormal_produksi.no_bobbin')
            ->get();

        $rekap_jenis = [];                                       // jumlah bobbin per jenis abnormal, contoh ['Hairy' => 3]
        $rekap_troly = [];                                       // jumlah bobbin per troly abnormal, contoh ['TRA-001' => 5]
        foreach ($abnormal as $a) {                              // ulangi setiap bobbin
            $rekap_jenis[$a->nm_abnormal] = ($rekap_jenis[$a->nm_abnormal] ?? 0) + 1; // tambah 1 ke jenisnya
            $rekap_troly[$a->kode_troly] = ($rekap_troly[$a->kode_troly] ?? 0) + 1;   // tambah 1 ke trolynya
        }
        $kapasitas = DB::table('troly')->whereIn('kode_troly', array_keys($rekap_troly))->pluck('kapasitas', 'kode_troly'); // kapasitas troly yang dipakai, contoh ['TRA-001' => 20]

        $doffing = DB::table('doffing')                          // daftar troly doffing di tanggal & shift yang sama
            ->where('tgl_doffing', $laporan->tgl_laporan)
            ->where('shift', $laporan->shift)
            ->orderBy('id_doffing')
            ->get();

        return view('produksi::laporan.show', compact('laporan', 'absensi', 'id_absensi', 'abnormal', 'rekap_jenis', 'rekap_troly', 'kapasitas', 'doffing')); // tampilkan halaman detail
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/laporan/{id}/edit)
    {
        $laporan = $this->ambilLaporan($id);                     // ambil laporan

        $abnormal_lama = [];                                     // temuan tersimpan, diubah ke bentuk daftar biasa agar sama dengan isi form (old)
        foreach (DB::table('abnormal_produksi')->where('id_laporan', $id)->orderBy('no_bobbin')->get() as $a) {
            $abnormal_lama[] = (array) $a;                       // (array) = ubah objek jadi array, contoh ['kode_mesin' => 'MCN-003', ...]
        }

        $pilihan = $this->pilihanAbnormal($id);                  // pilihan dropdown (termasuk data yang sudah dipakai laporan ini)

        return view('produksi::laporan.edit', compact('laporan', 'pilihan', 'abnormal_lama')); // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /produksi/laporan/{id})
    {
        $laporan = $this->ambilLaporan($id);                     // pastikan ada (404 jika tidak)

        $request->validate($this->aturan(), $this->pesanError(), $this->namaKolom()); // tanggal & shift tidak bisa diubah, jadi hanya angka + abnormal

        $abnormal = array_values($request->abnormal ?? []);      // daftar bobbin abnormal, dinomori ulang
        $error = $this->cekKapasitas($abnormal);                 // cek kapasitas troly abnormal
        if ($error) {
            return back()->withErrors(['abnormal' => $error])->withInput();
        }

        DB::table('laporan_shift')->where('id_laporan', $id)->update(array_merge($this->dataAngka($request), [ // angka baru + siapa yang mengubah
            'jml_abnormal' => count($abnormal),                  // total dihitung ulang
            'jml_doffing' => $this->hitungDoffing($laporan->tgl_laporan, $laporan->shift), // jumlah doffing dihitung ulang
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]));

        DB::table('abnormal_produksi')->where('id_laporan', $id)->delete(); // hapus rincian lama
        $this->simpanAbnormal($id, $abnormal);                   // simpan rincian baru

        return redirect()->route('produksi.laporan.show', $id)->with('success', 'Laporan shift '.$laporan->shift.' berhasil diubah.'); // ke detail + pesan sukses
    }

    public function destroy(string $id)                          // hapus laporan (route DELETE /produksi/laporan/{id})
    {
        $laporan = $this->ambilLaporan($id);                     // pastikan ada

        DB::table('laporan_shift')->where('id_laporan', $id)->delete(); // hapus

        return redirect()->route('produksi.laporan.index')->with('success', 'Laporan '.date('d-m-Y', strtotime($laporan->tgl_laporan)).' shift '.$laporan->shift.' berhasil dihapus.'); // ke daftar + pesan
    }

    private function ambilLaporan(string $id)                    // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 laporan + nama pencatat & pengubah; 404 jika tidak ada
    {
        $laporan = DB::table('laporan_shift')
            ->join('karyawan as pencatat', 'laporan_shift.created_by', '=', 'pencatat.id_karyawan')    // tabel karyawan dipanggil 2 kali, jadi diberi nama berbeda
            ->leftJoin('karyawan as pengubah', 'laporan_shift.updated_by', '=', 'pengubah.id_karyawan') // left = tetap tampil walau belum pernah diubah
            ->select('laporan_shift.*', 'pencatat.nama_karyawan as nama_spv', 'pengubah.nama_karyawan as nama_pengubah')
            ->where('laporan_shift.id_laporan', $id)
            ->first();

        if (! $laporan) {                                        // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $laporan;                                         // kembalikan hasilnya
    }

    private function pilihanAbnormal(?string $id_laporan = null)  // pilihan dropdown tabel temuan abnormal; $id_laporan diisi saat edit agar data lama yang sudah nonaktif tetap muncul
    {
        $dipakai = DB::table('abnormal_produksi')->where('id_laporan', $id_laporan)->get(); // rincian yang sudah tersimpan (kosong saat tambah)

        return [
            'mesin' => DB::table('mesin')->orderBy('kode_mesin')->get(), // semua mesin
            'benang' => DB::table('benang')->where('status_aktif', 'Aktif')->orWhereIn('merge', $dipakai->pluck('merge'))->orderBy('merge')->get(), // benang aktif + yang sudah dipakai
            'jenis' => DB::table('jenis_abnormal')->where('status_aktif', 'Aktif')->orWhereIn('id_jns_abn', $dipakai->pluck('id_jns_abn'))->orderBy('id_jns_abn')->get(),
            'troly' => DB::table('troly')->where('jenis', 'Abnormal')->where(function ($q) use ($dipakai) { // hanya troly ABNORMAL: (aktif ATAU sudah dipakai)
                $q->where('status_aktif', 'Aktif')->orWhereIn('kode_troly', $dipakai->pluck('kode_troly'));
            })->orderBy('kode_troly')->get(),
        ];
    }

    private function hitungDoffing(string $tanggal, string $shift) // jumlah troly doffing di tanggal & shift tertentu
    {
        return DB::table('doffing')->where('tgl_doffing', $tanggal)->where('shift', $shift)->count(); // count = hitung jumlah baris
    }

    private function cekKapasitas(array $abnormal)              // cek jumlah bobbin per troly abnormal tidak melebihi kapasitasnya; hasil = pesan error atau null
    {
        $isi = [];                                               // jumlah bobbin per troly, contoh ['TRA-001' => 21]
        foreach ($abnormal as $a) {
            $isi[$a['kode_troly']] = ($isi[$a['kode_troly']] ?? 0) + 1; // tambah 1 ke trolynya
        }

        foreach ($isi as $kode_troly => $jumlah) {               // periksa setiap troly yang dipakai
            $kapasitas = DB::table('troly')->where('kode_troly', $kode_troly)->value('kapasitas'); // kapasitas troly ini
            if ($jumlah > $kapasitas) {                          // jika melebihi
                return 'Troly '.$kode_troly.' hanya muat '.$kapasitas.' bobbin, tetapi diisi '.$jumlah.' bobbin. Pindahkan sebagian ke troly abnormal lain.';
            }
        }

        return null;                                             // semua aman
    }

    private function simpanAbnormal(string $id_laporan, array $abnormal) // simpan rincian bobbin abnormal, nomor bobbin 1, 2, 3, ...
    {
        foreach ($abnormal as $i => $a) {                        // $i = urutan 0, 1, 2, ...
            DB::table('abnormal_produksi')->insert([
                'id_laporan' => $id_laporan,
                'no_bobbin' => $i + 1,                           // nomor bobbin mulai dari 1
                'kode_mesin' => $a['kode_mesin'],
                'merge' => $a['merge'],
                'id_jns_abn' => $a['id_jns_abn'],
                'kode_troly' => $a['kode_troly'],
                'keterangan' => $a['keterangan'] ?? null,
            ]);
        }
    }

    private function aturan()                                    // aturan validasi angka laporan + rincian abnormal (dipakai store & update)
    {
        return [
            'abnormal' => 'nullable|array',                      // daftar bobbin abnormal, boleh kosong
            'abnormal.*.kode_mesin' => 'required|exists:mesin,kode_mesin',        // .* = setiap baris; mesin wajib & harus ada
            'abnormal.*.merge' => 'required|exists:benang,merge',                 // benang wajib & harus ada
            'abnormal.*.id_jns_abn' => 'required|exists:jenis_abnormal,id_jns_abn', // jenis abnormal wajib & harus ada
            'abnormal.*.kode_troly' => 'required|exists:troly,kode_troly,jenis,Abnormal', // troly wajib & harus troly berjenis Abnormal (exists:tabel,kolom,kolom_tambahan,nilai)
            'abnormal.*.keterangan' => 'nullable|max:100',
            'jml_break' => 'required|integer|min:0',             // wajib, bilangan bulat, tidak boleh minus (jml_doffing tidak ada di sini karena dihitung otomatis dari tabel doffing)
            'jml_problem_mesin' => 'required|integer|min:0',
            'ket_problem_mesin' => 'nullable|max:1000',          // boleh kosong, maks 1000 karakter
            'bleeding_time' => 'required|integer|min:0',         // dalam menit
            'suspend_time' => 'required|integer|min:0',          // dalam menit
            'waste' => 'required|numeric|min:0|max:999999.99',   // numeric = boleh desimal, contoh 12.5
            'catatan' => 'nullable|max:1000',
        ];
    }

    private function dataAngka(Request $request)                 // ambil angka-angka dari form untuk disimpan (dipakai store & update)
    {
        return [
            'jml_break' => $request->jml_break,
            'jml_problem_mesin' => $request->jml_problem_mesin,
            'ket_problem_mesin' => $request->ket_problem_mesin,
            'bleeding_time' => $request->bleeding_time,
            'suspend_time' => $request->suspend_time,
            'waste' => $request->waste,
            'catatan' => $request->catatan,
        ];
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus bilangan bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute tidak boleh minus.',
            'max' => ':attribute terlalu panjang/besar.',
            'in' => ':attribute tidak valid.',
            'date' => ':attribute harus berupa tanggal.',
            'abnormal.*.kode_mesin.required' => 'Bobbin ke-:position: mesin wajib dipilih.',   // :position = nomor baris mulai 1
            'abnormal.*.merge.required' => 'Bobbin ke-:position: benang wajib dipilih.',
            'abnormal.*.id_jns_abn.required' => 'Bobbin ke-:position: jenis abnormal wajib dipilih.',
            'abnormal.*.kode_troly.required' => 'Bobbin ke-:position: troly abnormal wajib dipilih.',
            'abnormal.*.kode_troly.exists' => 'Bobbin ke-:position: troly harus troly berjenis Abnormal.',
            'abnormal.*.kode_mesin.exists' => 'Bobbin ke-:position: mesin tidak ditemukan.',
            'abnormal.*.merge.exists' => 'Bobbin ke-:position: benang tidak ditemukan.',
            'abnormal.*.id_jns_abn.exists' => 'Bobbin ke-:position: jenis abnormal tidak ditemukan.',
            'abnormal.*.keterangan.max' => 'Bobbin ke-:position: keterangan maksimal 100 karakter.',
        ];
    }

    private function namaKolom()                                 // nama kolom yang tampil di pesan error
    {
        return [
            'tgl_laporan' => 'Tanggal',
            'shift' => 'Shift',
            'jml_break' => 'Jumlah break',
            'jml_problem_mesin' => 'Jumlah problem mesin',
            'ket_problem_mesin' => 'Keterangan problem mesin',
            'bleeding_time' => 'Bleeding time',
            'suspend_time' => 'Suspend time',
            'waste' => 'Waste',
            'catatan' => 'Catatan',
        ];
    }
}
