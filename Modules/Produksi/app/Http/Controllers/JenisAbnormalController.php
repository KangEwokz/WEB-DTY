<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class JenisAbnormalController extends Controller                 // master jenis abnormal (Handling, OTY, Hairy, ...); dipakai Produksi, nanti juga QCT
{
    public function index()                                      // tampil daftar (route GET /produksi/jenis-abnormal)
    {
        $jenis = DB::table('jenis_abnormal')->orderBy('id_jns_abn')->get(); // ambil semua, urut kode

        return view('produksi::jenis-abnormal.index', compact('jenis')); // tampilkan Modules/Produksi/resources/views/jenis-abnormal/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /produksi/jenis-abnormal/create)
    {
        return view('produksi::jenis-abnormal.create');          // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan data baru (route POST /produksi/jenis-abnormal)
    {
        $request->validate([                                     // periksa isi form
            'nm_abnormal' => 'required|max:50|unique:jenis_abnormal,nm_abnormal', // wajib, maks 50, tidak boleh kembar
        ], $this->pesanError());

        $id_terakhir = DB::table('jenis_abnormal')->where('id_jns_abn', 'like', 'VS%')->max('id_jns_abn'); // kode terbesar, contoh 'VS05'
        $urut = $id_terakhir ? (int) substr($id_terakhir, 2) + 1 : 1; // ambil '05' -> 5 -> 6
        $id_jns_abn = 'VS'.str_pad($urut, 2, '0', STR_PAD_LEFT); // gabungkan: 'VS' + '06'

        DB::table('jenis_abnormal')->insert([                    // simpan ke tabel jenis_abnormal
            'id_jns_abn' => $id_jns_abn,
            'nm_abnormal' => $request->nm_abnormal,
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_by' => Auth::user()->id_karyawan,           // penginput = user yang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.jenis-abnormal.index')->with('success', 'Jenis abnormal '.$request->nm_abnormal.' berhasil ditambahkan dengan kode '.$id_jns_abn.'.'); // kembali + pesan
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/jenis-abnormal/{id}/edit)
    {
        $jenis = $this->cariJenis($id);                          // ambil data (404 jika tidak ada)

        return view('produksi::jenis-abnormal.edit', compact('jenis')); // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /produksi/jenis-abnormal/{id})
    {
        $this->cariJenis($id);                                   // pastikan ada

        $request->validate([
            'nm_abnormal' => 'required|max:50|unique:jenis_abnormal,nm_abnormal,'.$id.',id_jns_abn', // nama milik data ini sendiri boleh tetap
        ], $this->pesanError());

        DB::table('jenis_abnormal')->where('id_jns_abn', $id)->update([ // simpan nama baru
            'nm_abnormal' => $request->nm_abnormal,
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.jenis-abnormal.index')->with('success', 'Jenis abnormal '.$id.' berhasil diubah.'); // kembali + pesan
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /produksi/jenis-abnormal/{id}/status)
    {
        $jenis = $this->cariJenis($id);                          // ambil data

        $status_baru = $jenis->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('jenis_abnormal')->where('id_jns_abn', $id)->update([
            'status_aktif' => $status_baru,
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Jenis abnormal '.$jenis->nm_abnormal.' sekarang '.$status_baru.'.'); // kembali + pesan
    }

    private function cariJenis(string $id)                       // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 jenis abnormal; 404 jika tidak ada
    {
        $jenis = DB::table('jenis_abnormal')->where('id_jns_abn', $id)->first();

        if (! $jenis) {                                          // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $jenis;                                           // kembalikan hasilnya
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'nm_abnormal.required' => 'Nama abnormal wajib diisi.',
            'nm_abnormal.max' => 'Nama abnormal maksimal 50 karakter.',
            'nm_abnormal.unique' => 'Nama abnormal sudah ada.',
        ];
    }
}
