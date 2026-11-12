<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Collection;                               // Collection = jenis data hasil ->get() (kumpulan baris)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class AbsensiController extends Controller                       // absensi & penempatan operator produksi di awal shift
{
    public function index(Request $request)                      // tampil daftar absensi per shift (route GET /produksi/absensi)
    {
        $query = DB::table('absensi_shift')                      // mulai dari tabel kepala absensi
            ->join('karyawan', 'absensi_shift.created_by', '=', 'karyawan.id_karyawan') // gabung karyawan untuk ambil nama supervisor pencatat
            ->select(
                'absensi_shift.*',
                'karyawan.nama_karyawan as nama_spv',            // as = beri nama lain agar tidak tertukar
                DB::raw("(select count(*) from absensi_detail d where d.id_absensi = absensi_shift.id_absensi and d.kehadiran = 'Hadir') as jml_hadir"),  // hitung operator Hadir
                DB::raw("(select count(*) from absensi_detail d where d.id_absensi = absensi_shift.id_absensi and d.kehadiran != 'Hadir') as jml_tidak") // hitung yang tidak hadir
            );

        if ($request->tanggal) {                                 // jika filter tanggal diisi
            $query->where('absensi_shift.tgl_absen', $request->tanggal); // tampilkan tanggal itu saja
        }                                                        // akhir if

        $absensi = $query->orderBy('absensi_shift.tgl_absen', 'desc') // tanggal terbaru di atas
            ->orderBy('absensi_shift.shift', 'desc')             // lalu shift terakhir di atas
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        return view('produksi::absensi.index', compact('absensi')); // tampilkan Modules/Produksi/resources/views/absensi/index.blade.php
    }

    public function create()                                     // tampil form absensi (route GET /produksi/absensi/create)
    {
        $operator = $this->daftarOperator();                     // semua operator produksi yang aktif (lihat fungsi di bawah)
        $area = $this->daftarArea();                             // pilihan area kerja produksi
        $shift_sekarang = $this->shiftSekarang();                // tebak shift dari jam sekarang, agar SPV tidak perlu memilih

        return view('produksi::absensi.create', compact('operator', 'area', 'shift_sekarang')); // tampilkan form
    }

    public function store(Request $request)                      // simpan absensi (route POST /produksi/absensi)
    {
        $request->validate([                                     // periksa isi form
            'tgl_absen' => 'required|date',                      // wajib, harus tanggal
            'shift' => 'required|in:S1,S2,S3',                   // wajib, salah satu shift
            'kehadiran.*' => 'in:Hadir,Sakit,Ijin,Alfa,Cuti',    // .* = setiap isi daftar kehadiran harus salah satu nilai ini
            'area.*' => 'nullable|exists:area,id_area',          // setiap area jika diisi harus ada di tabel area
            'keterangan.*' => 'nullable|max:100',                // setiap keterangan maks 100 karakter
        ], [
            'required' => ':attribute wajib diisi.',
            'in' => ':attribute tidak valid.',
            'max' => 'Keterangan maksimal :max karakter.',
        ], [
            'tgl_absen' => 'Tanggal',
            'shift' => 'Shift',
        ]);                                                      // akhir validasi

        $id_absensi = 'ABS-'.date('Ymd', strtotime($request->tgl_absen)).'-'.$request->shift; // buat id, contoh ABS-20261112-S1

        if (DB::table('absensi_shift')->where('id_absensi', $id_absensi)->exists()) { // jika absensi tanggal + shift ini sudah ada
            return back()->with('error', 'Absensi tanggal '.date('d-m-Y', strtotime($request->tgl_absen)).' shift '.$request->shift.' sudah ada. Silakan ubah data yang sudah ada.')->withInput(); // tolak + pesan
        }                                                        // akhir if

        $operator = $this->daftarOperator();                     // daftar operator yang seharusnya ada di form
        $error = $this->cekBaris($request, $operator);           // cek setiap baris: kehadiran wajib, area wajib jika Hadir
        if ($error) {                                            // jika ada baris yang salah
            return back()->withErrors($error)->withInput();      // kembali ke form + tampilkan error per baris
        }                                                        // akhir if

        DB::table('absensi_shift')->insert([                     // 1. simpan kepala absensi
            'id_absensi' => $id_absensi,
            'tgl_absen' => $request->tgl_absen,
            'shift' => $request->shift,
            'created_by' => Auth::user()->id_karyawan,           // supervisor yang sedang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->simpanBaris($request, $operator, $id_absensi);    // 2. simpan isi absensi (1 baris per operator)

        return redirect()->route('produksi.absensi.show', $id_absensi)->with('success', 'Absensi shift '.$request->shift.' berhasil disimpan.'); // ke halaman detail + pesan sukses
    }

    public function show(string $id)                             // tampil detail absensi (route GET /produksi/absensi/{id})
    {
        $absensi = $this->ambilKepala($id);                      // ambil kepala absensi + nama pencatat

        $detail = DB::table('absensi_detail')                    // ambil isi absensi
            ->join('karyawan', 'absensi_detail.id_karyawan', '=', 'karyawan.id_karyawan') // + nama operator
            ->leftJoin('area', 'absensi_detail.id_area', '=', 'area.id_area')            // + nama area (left = tetap tampil walau area kosong)
            ->select('absensi_detail.*', 'karyawan.nama_karyawan', 'karyawan.grup', 'area.nm_area')
            ->where('absensi_detail.id_absensi', $id)
            ->orderBy('area.nm_area')                            // dikelompokkan per area
            ->orderBy('karyawan.nama_karyawan')
            ->get();

        $ringkasan = [];                                         // hitung jumlah per kehadiran, contoh ['Hadir' => 4, 'Sakit' => 1]
        foreach ($detail as $d) {                                // ulangi setiap operator
            $ringkasan[$d->kehadiran] = ($ringkasan[$d->kehadiran] ?? 0) + 1; // tambah 1 ke kelompok kehadirannya (?? 0 = mulai dari 0 jika belum ada)
        }                                                        // akhir perulangan

        return view('produksi::absensi.show', compact('absensi', 'detail', 'ringkasan')); // tampilkan halaman detail
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/absensi/{id}/edit)
    {
        $absensi = $this->ambilKepala($id);                      // ambil kepala absensi

        $tersimpan = DB::table('absensi_detail')->where('id_absensi', $id)->get()->keyBy('id_karyawan'); // isi yang sudah tersimpan; keyBy = dijadikan daftar berkunci id karyawan agar mudah dicari
        $operator = $this->daftarOperator($tersimpan->keys()->toArray()); // operator aktif + operator yang sudah tercatat (walau kini nonaktif)
        $area = $this->daftarArea();                             // pilihan area

        return view('produksi::absensi.edit', compact('absensi', 'tersimpan', 'operator', 'area')); // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /produksi/absensi/{id})
    {
        $absensi = $this->ambilKepala($id);                      // pastikan absensinya ada (404 jika tidak)

        $request->validate([                                     // tanggal & shift tidak bisa diubah, jadi hanya baris operator
            'kehadiran.*' => 'in:Hadir,Sakit,Ijin,Alfa,Cuti',
            'area.*' => 'nullable|exists:area,id_area',
            'keterangan.*' => 'nullable|max:100',
        ], [
            'in' => 'Kehadiran tidak valid.',
            'max' => 'Keterangan maksimal :max karakter.',
        ]);                                                      // akhir validasi

        $tersimpan = DB::table('absensi_detail')->where('id_absensi', $id)->pluck('id_karyawan')->toArray(); // id operator yang sudah tercatat
        $operator = $this->daftarOperator($tersimpan);           // daftar operator yang sama seperti di form edit
        $error = $this->cekBaris($request, $operator);           // cek setiap baris
        if ($error) {
            return back()->withErrors($error)->withInput();      // kembali + error per baris
        }

        DB::table('absensi_detail')->where('id_absensi', $id)->delete(); // 1. hapus isi lama
        $this->simpanBaris($request, $operator, $id);            // 2. simpan isi baru

        DB::table('absensi_shift')->where('id_absensi', $id)->update([ // 3. catat siapa yang mengubah
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.absensi.show', $id)->with('success', 'Absensi shift '.$absensi->shift.' berhasil diubah.'); // ke detail + pesan sukses
    }

    public function destroy(string $id)                          // hapus absensi (route DELETE /produksi/absensi/{id})
    {
        $absensi = $this->ambilKepala($id);                      // pastikan ada

        DB::table('absensi_shift')->where('id_absensi', $id)->delete(); // hapus kepala; isinya ikut terhapus (cascadeOnDelete di migration)

        return redirect()->route('produksi.absensi.index')->with('success', 'Absensi '.date('d-m-Y', strtotime($absensi->tgl_absen)).' shift '.$absensi->shift.' berhasil dihapus.'); // ke daftar + pesan
    }

    private function ambilKepala(string $id)                     // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 kepala absensi + nama pencatat & pengubah; 404 jika tidak ada
    {
        $absensi = DB::table('absensi_shift')
            ->join('karyawan as pencatat', 'absensi_shift.created_by', '=', 'pencatat.id_karyawan')    // tabel karyawan dipanggil 2 kali, jadi diberi nama berbeda
            ->leftJoin('karyawan as pengubah', 'absensi_shift.updated_by', '=', 'pengubah.id_karyawan') // left = tetap tampil walau belum pernah diubah
            ->select('absensi_shift.*', 'pencatat.nama_karyawan as nama_spv', 'pengubah.nama_karyawan as nama_pengubah')
            ->where('absensi_shift.id_absensi', $id)
            ->first();

        if (! $absensi) {                                        // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $absensi;                                         // kembalikan hasilnya
    }

    private function daftarOperator(array $tambahan = [])        // operator produksi aktif; $tambahan = id operator lain yang tetap harus tampil (dipakai saat edit)
    {
        return DB::table('karyawan')
            ->join('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')   // gabung divisi untuk cek kode divisi
            ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan') // gabung jabatan untuk cek nama jabatan
            ->where(function ($q) use ($tambahan) {              // (operator produksi aktif) ATAU (id ada di $tambahan)
                $q->where(function ($q2) {
                    $q2->where('divisi.kd_divisi', 'PRD')         // divisi Produksi (kode PRD)
                        ->where('jabatan.nm_jabatan', 'Operator') // jabatan Operator
                        ->where('karyawan.status_aktif', 'Aktif'); // masih aktif
                })->orWhereIn('karyawan.id_karyawan', $tambahan); // whereIn = id termasuk dalam daftar
            })
            ->select('karyawan.id_karyawan', 'karyawan.nama_karyawan', 'karyawan.grup', 'karyawan.id_area') // id_area = area tetap karyawan, dipakai sebagai pilihan bawaan
            ->orderBy('karyawan.grup')                           // urut grup A, B, ...
            ->orderBy('karyawan.nama_karyawan')                  // lalu nama
            ->get();
    }

    private function daftarArea()                                // area kerja divisi Produksi yang aktif
    {
        return DB::table('area')
            ->join('divisi', 'area.id_divisi', '=', 'divisi.id_divisi')
            ->where('divisi.kd_divisi', 'PRD')                   // area milik divisi Produksi
            ->where('area.status_aktif', 'Aktif')
            ->select('area.id_area', 'area.nm_area')
            ->orderBy('area.nm_area')
            ->get();
    }

    private function shiftSekarang()                             // tebak shift dari jam sekarang
    {
        $jam = (int) date('H');                                  // jam sekarang 0-23, contoh 9
        if ($jam >= 7 && $jam < 15) {                            // 07.00 - 14.59
            return 'S1';
        }
        if ($jam >= 15 && $jam < 23) {                           // 15.00 - 22.59
            return 'S2';
        }

        return 'S3';                                             // 23.00 - 06.59
    }

    private function cekBaris(Request $request, Collection $operator)      // cek setiap baris operator; hasil = daftar error (kosong jika semua benar)
    {
        $error = [];                                             // wadah pesan error
        foreach ($operator as $o) {                              // ulangi setiap operator yang tampil di form
            $hadir = $request->kehadiran[$o->id_karyawan] ?? null; // kehadiran operator ini; null jika tidak dikirim
            $area = $request->area[$o->id_karyawan] ?? null;     // area operator ini

            if (! $hadir) {                                      // kehadiran wajib dipilih
                $error['kehadiran.'.$o->id_karyawan] = 'Kehadiran '.$o->nama_karyawan.' wajib dipilih.';
            } elseif ($hadir == 'Hadir' && ! $area) {            // jika Hadir, area wajib dipilih
                $error['area.'.$o->id_karyawan] = 'Area '.$o->nama_karyawan.' wajib dipilih karena Hadir.';
            }
        }

        return $error;                                           // kembalikan daftar error
    }

    private function simpanBaris(Request $request, Collection $operator, string $id_absensi) // simpan 1 baris absensi_detail per operator
    {
        foreach ($operator as $o) {                              // ulangi setiap operator
            $hadir = $request->kehadiran[$o->id_karyawan];       // kehadiran (sudah pasti ada karena lolos cekBaris)

            DB::table('absensi_detail')->insert([
                'id_absensi' => $id_absensi,
                'id_karyawan' => $o->id_karyawan,
                'kehadiran' => $hadir,
                'id_area' => $hadir == 'Hadir' ? $request->area[$o->id_karyawan] : null, // area hanya disimpan jika Hadir
                'keterangan' => $request->keterangan[$o->id_karyawan] ?? null,
            ]);
        }
    }
}
