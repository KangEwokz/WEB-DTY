<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class BenangController extends Controller                        // master benang / merge (dikelola Supervisor Produksi)
{
    public function index(Request $request)                      // tampil daftar benang (route GET /produksi/benang)
    {
        $query = DB::table('benang');                            // mulai dari tabel benang

        if ($request->cari) {                                    // jika kotak pencarian diisi
            $query->where(function ($q) use ($request) {         // (merge like ... OR warna like ...)
                $q->where('merge', 'like', '%'.$request->cari.'%')       // cari di kode merge
                    ->orWhere('warna_ppt', 'like', '%'.$request->cari.'%'); // ATAU di warna ppt
            });
        }                                                        // akhir if

        if ($request->status) {                                  // jika filter status dipilih
            $query->where('status_aktif', $request->status);     // tampilkan status itu saja
        }                                                        // akhir if

        $benang = $query->orderBy('merge')                       // urut kode merge
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        return view('produksi::benang.index', compact('benang')); // tampilkan Modules/Produksi/resources/views/benang/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /produksi/benang/create)
    {
        return view('produksi::benang.create');                  // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan benang baru (route POST /produksi/benang)
    {
        $request->validate(                                      // periksa isi form
            array_merge(['merge' => 'required|max:10|alpha_num|unique:benang,merge'], $this->aturan()), // kode merge diisi sendiri (dari pabrik), tidak boleh kembar; digabung dengan aturan lain
            $this->pesanError(),
            $this->namaKolom()
        );                                                       // akhir validasi

        DB::table('benang')->insert([                            // simpan ke tabel benang
            'merge' => strtoupper($request->merge),              // huruf besar, contoh 'm150a' -> 'M150A'
            'denier' => $request->denier,
            'filamen' => $request->filamen,
            'warna_ppt' => $request->warna_ppt,
            'toleransi_denier' => $request->toleransi_denier,
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_by' => Auth::user()->id_karyawan,           // penginput = user yang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('produksi.benang.index')->with('success', 'Benang '.strtoupper($request->merge).' berhasil ditambahkan.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/benang/{id}/edit)
    {
        $benang = $this->cariBenang($id);                        // ambil data (404 jika tidak ada)

        return view('produksi::benang.edit', compact('benang')); // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /produksi/benang/{id})
    {
        $this->cariBenang($id);                                  // pastikan ada

        $request->validate($this->aturan(), $this->pesanError(), $this->namaKolom()); // kode merge tidak bisa diubah, jadi tidak divalidasi

        DB::table('benang')->where('merge', $id)->update([       // ubah data benang ini
            'denier' => $request->denier,
            'filamen' => $request->filamen,
            'warna_ppt' => $request->warna_ppt,
            'toleransi_denier' => $request->toleransi_denier,
            'updated_by' => Auth::user()->id_karyawan,           // pengubah = user yang login
            'updated_at' => now(),
        ]);                                                      // akhir update

        return redirect()->route('produksi.benang.index')->with('success', 'Benang '.$id.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /produksi/benang/{id}/status)
    {
        $benang = $this->cariBenang($id);                        // ambil data

        $status_baru = $benang->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('benang')->where('merge', $id)->update([       // simpan status baru
            'status_aktif' => $status_baru,
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Benang '.$id.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }

    private function cariBenang(string $id)                      // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 benang; 404 jika tidak ada
    {
        $benang = DB::table('benang')->where('merge', $id)->first();

        if (! $benang) {                                         // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $benang;                                          // kembalikan hasilnya
    }

    private function aturan()                                    // aturan validasi selain kode merge (dipakai store & update)
    {
        return [
            'denier' => 'required|integer|min:1',                // wajib, bilangan bulat, minimal 1
            'filamen' => 'required|integer|min:1',
            'warna_ppt' => 'nullable|max:20',                    // boleh kosong, maks 20 karakter
            'toleransi_denier' => 'required|numeric|min:0.01|max:50', // persen, boleh desimal, 0.01 - 50
        ];
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus bilangan bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'alpha_num' => ':attribute hanya boleh huruf dan angka (tanpa spasi).',
            'unique' => ':attribute sudah dipakai.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
        ];
    }

    private function namaKolom()                                 // nama kolom yang tampil di pesan error
    {
        return [
            'merge' => 'Kode merge',
            'denier' => 'Denier',
            'filamen' => 'Filamen',
            'warna_ppt' => 'Warna ppt',
            'toleransi_denier' => 'Toleransi denier',
        ];
    }
}
