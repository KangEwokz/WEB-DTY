<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class DoffingController extends Controller                       // doffing: 1 baris = 1 troly hasil doffing; jumlahnya = jumlah doffing di laporan shift
{
    public function index(Request $request)                      // tampil daftar doffing (route GET /produksi/doffing)
    {
        $query = DB::table('doffing')                            // mulai dari tabel doffing
            ->join('karyawan', 'doffing.created_by', '=', 'karyawan.id_karyawan') // + nama pencatat
            ->select('doffing.*', 'karyawan.nama_karyawan as nama_spv');

        if ($request->tanggal) {                                 // jika filter tanggal diisi
            $query->where('doffing.tgl_doffing', $request->tanggal);
        }                                                        // akhir if

        if ($request->shift) {                                   // jika filter shift dipilih
            $query->where('doffing.shift', $request->shift);
        }                                                        // akhir if

        $total = (clone $query)->select(DB::raw('count(*) as troly'), DB::raw('sum(jml_bobbin) as bobbin'), DB::raw('sum(berat_troly) as berat'))->first(); // total sesuai filter; clone = salin query agar query utama tidak ikut berubah

        $doffing = $query->orderBy('doffing.id_doffing', 'desc') // terbaru di atas (id berisi tanggal-shift-urut)
            ->paginate(15)                                       // 15 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        return view('produksi::doffing.index', compact('doffing', 'total')); // tampilkan Modules/Produksi/resources/views/doffing/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /produksi/doffing/create)
    {
        $jam = (int) date('H');                                  // jam sekarang 0-23
        $shift_sekarang = $jam >= 7 && $jam < 15 ? 'S1' : ($jam >= 15 && $jam < 23 ? 'S2' : 'S3'); // tebak shift dari jam

        $pilihan = $this->pilihan();                             // pilihan troly normal & benang

        return view('produksi::doffing.create', compact('shift_sekarang', 'pilihan')); // tampilkan form
    }

    public function store(Request $request)                      // simpan doffing (route POST /produksi/doffing)
    {
        $request->validate(                                      // periksa isi form
            array_merge(['tgl_doffing' => 'required|date', 'shift' => 'required|in:S1,S2,S3'], $this->aturan()), // tanggal & shift + aturan lain
            $this->pesanError()
        );

        $error = $this->cekTroly($request, $request->tgl_doffing, $request->shift); // cek troly: kapasitas & belum dipakai di shift ini
        if ($error) {
            return back()->withErrors($error)->withInput();      // kembali + pesan di bawah kolom yang salah
        }

        $awalan = 'DOF-'.date('Ymd', strtotime($request->tgl_doffing)).'-'.$request->shift.'-'; // contoh 'DOF-20261112-S1-'
        $id_terakhir = DB::table('doffing')->where('id_doffing', 'like', $awalan.'%')->max('id_doffing'); // id terbesar di shift ini, contoh 'DOF-20261112-S1-004'
        $urut = $id_terakhir ? (int) substr($id_terakhir, -3) + 1 : 1; // substr(..., -3) = 3 huruf terakhir '004' -> 4 -> 5
        $id_doffing = $awalan.str_pad($urut, 3, '0', STR_PAD_LEFT); // gabungkan: 'DOF-20261112-S1-' + '005'

        DB::table('doffing')->insert([                           // simpan ke tabel doffing
            'id_doffing' => $id_doffing,
            'tgl_doffing' => $request->tgl_doffing,
            'shift' => $request->shift,
            'kode_troly' => $request->kode_troly,
            'merge' => $request->merge,
            'jml_bobbin' => $request->jml_bobbin,
            'berat_troly' => $request->berat_troly,
            'keterangan' => $request->keterangan,
            'created_by' => Auth::user()->id_karyawan,           // supervisor yang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->perbaruiLaporan($request->tgl_doffing, $request->shift); // hitung ulang jumlah doffing di laporan shift (jika laporannya sudah ada)

        return redirect()->route('produksi.doffing.index', ['tanggal' => $request->tgl_doffing, 'shift' => $request->shift]) // kembali ke daftar, langsung terfilter tanggal & shift ini
            ->with('success', 'Doffing '.$id_doffing.' (troly '.$request->kode_troly.', '.$request->jml_bobbin.' bobbin) berhasil disimpan.');
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/doffing/{id}/edit)
    {
        $doffing = $this->cariDoffing($id);                      // ambil data (404 jika tidak ada)
        $pilihan = $this->pilihan($doffing);                     // pilihan + troly & benang yang sedang dipakai

        return view('produksi::doffing.edit', compact('doffing', 'pilihan')); // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /produksi/doffing/{id})
    {
        $doffing = $this->cariDoffing($id);                      // ambil data lama

        $request->validate($this->aturan(), $this->pesanError()); // tanggal & shift tidak bisa diubah

        $error = $this->cekTroly($request, $doffing->tgl_doffing, $doffing->shift, $id); // cek troly di tanggal & shift lama, kecuali doffing ini sendiri
        if ($error) {
            return back()->withErrors($error)->withInput();
        }

        DB::table('doffing')->where('id_doffing', $id)->update([ // simpan perubahan
            'kode_troly' => $request->kode_troly,
            'merge' => $request->merge,
            'jml_bobbin' => $request->jml_bobbin,
            'berat_troly' => $request->berat_troly,
            'keterangan' => $request->keterangan,
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.doffing.index', ['tanggal' => $doffing->tgl_doffing, 'shift' => $doffing->shift])
            ->with('success', 'Doffing '.$id.' berhasil diubah.');
    }

    public function destroy(string $id)                          // hapus doffing (route DELETE /produksi/doffing/{id})
    {
        $doffing = $this->cariDoffing($id);                      // ambil data

        DB::table('doffing')->where('id_doffing', $id)->delete(); // hapus

        $this->perbaruiLaporan($doffing->tgl_doffing, $doffing->shift); // jumlah doffing di laporan shift ikut berkurang

        return back()->with('success', 'Doffing '.$id.' berhasil dihapus.'); // kembali + pesan
    }

    private function cariDoffing(string $id)                     // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 doffing; 404 jika tidak ada
    {
        $doffing = DB::table('doffing')->where('id_doffing', $id)->first();

        if (! $doffing) {                                        // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $doffing;
    }

    private function pilihan($doffing = null)                    // pilihan dropdown; $doffing diisi saat edit agar troly/benang yang dipakai tetap muncul walau sudah nonaktif
    {
        return [
            'troly' => DB::table('troly')->where('jenis', 'Normal')->where(function ($q) use ($doffing) { // hanya troly NORMAL: (aktif ATAU sedang dipakai)
                $q->where('status_aktif', 'Aktif')->orWhere('kode_troly', $doffing->kode_troly ?? null);
            })->orderBy('kode_troly')->get(),
            'benang' => DB::table('benang')->where('status_aktif', 'Aktif')->orWhere('merge', $doffing->merge ?? null)->orderBy('merge')->get(),
        ];
    }

    private function cekTroly(Request $request, string $tanggal, string $shift, ?string $kecuali = null) // cek kapasitas troly & troly belum dipakai di shift yang sama; hasil = daftar error (kosong jika aman)
    {
        $troly = DB::table('troly')->where('kode_troly', $request->kode_troly)->first(); // data troly yang dipilih

        if ($request->jml_bobbin > $troly->kapasitas) {          // jika jumlah bobbin melebihi kapasitas
            return ['jml_bobbin' => 'Troly '.$troly->kode_troly.' hanya muat '.$troly->kapasitas.' bobbin.'];
        }

        $dipakai = DB::table('doffing')                          // cari doffing lain yang memakai troly ini di tanggal & shift yang sama
            ->where('tgl_doffing', $tanggal)
            ->where('shift', $shift)
            ->where('kode_troly', $request->kode_troly)
            ->where('id_doffing', '!=', $kecuali)                // != = tidak sama dengan (doffing yang sedang diubah tidak dihitung)
            ->value('id_doffing');                               // ambil id-nya saja

        if ($dipakai) {                                          // jika sudah dipakai
            return ['kode_troly' => 'Troly '.$request->kode_troly.' sudah dipakai di shift ini ('.$dipakai.').'];
        }

        return [];                                               // aman
    }

    private function perbaruiLaporan(string $tanggal, string $shift) // hitung ulang jumlah doffing di laporan shift yang sama
    {
        $jumlah = DB::table('doffing')->where('tgl_doffing', $tanggal)->where('shift', $shift)->count(); // banyaknya troly yang di-doffing

        DB::table('laporan_shift')->where('tgl_laporan', $tanggal)->where('shift', $shift)->update(['jml_doffing' => $jumlah]); // jika laporan belum ada, tidak ada yang berubah
    }

    private function aturan()                                    // aturan validasi (dipakai store & update)
    {
        return [
            'kode_troly' => 'required|exists:troly,kode_troly,jenis,Normal', // wajib & harus troly berjenis Normal
            'merge' => 'required|exists:benang,merge',           // wajib & harus ada di tabel benang
            'jml_bobbin' => 'required|integer|min:1',            // wajib, minimal 1 (batas atas dicek dari kapasitas troly)
            'berat_troly' => 'required|numeric|min:0.01|max:9999.99', // wajib, kg, boleh desimal
            'keterangan' => 'nullable|max:100',
        ];
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'tgl_doffing.required' => 'Tanggal wajib diisi.',
            'shift.required' => 'Shift wajib dipilih.',
            'kode_troly.required' => 'Troly wajib dipilih.',
            'kode_troly.exists' => 'Troly harus troly berjenis Normal.',
            'merge.required' => 'Merge wajib dipilih.',
            'merge.exists' => 'Merge tidak ditemukan.',
            'jml_bobbin.required' => 'Jumlah bobbin wajib diisi.',
            'jml_bobbin.integer' => 'Jumlah bobbin harus bilangan bulat.',
            'jml_bobbin.min' => 'Jumlah bobbin minimal 1.',
            'berat_troly.required' => 'Berat troly wajib diisi.',
            'berat_troly.numeric' => 'Berat troly harus berupa angka.',
            'berat_troly.min' => 'Berat troly harus lebih dari 0.',
            'berat_troly.max' => 'Berat troly terlalu besar.',
            'keterangan.max' => 'Keterangan maksimal 100 karakter.',
        ];
    }
}
