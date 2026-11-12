<?php

namespace Modules\Admin\Http\Controllers;                        // "alamat" class ini = folder Modules/Admin/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class DepartemenController extends Controller                    // class DepartemenController mewarisi Controller
{
    public function index()                                      // tampil daftar departemen (route GET /departemen)
    {
        $departemen = DB::table('departemen')->orderBy('id_departemen')->get(); // ambil semua departemen, urut id

        return view('admin::departemen.index', compact('departemen')); // tampilkan Modules/Admin/resources/views/departemen/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /departemen/create)
    {
        return view('admin::departemen.create');                 // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan data baru (route POST /departemen)
    {
        $request->validate([                                     // periksa isi form; kalau gagal otomatis kembali ke form
            'kd_departemen' => 'required|max:5|unique:departemen,kd_departemen', // wajib, maks 5 karakter, tidak boleh sama dengan kode yang sudah ada
            'nm_departemen' => 'required|max:50',                // wajib, maks 50 karakter
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',       // :max diganti angka batasnya
            'unique' => ':attribute sudah dipakai.',
        ], [                                                     // nama kolom yang tampil di pesan error
            'kd_departemen' => 'Kode departemen',
            'nm_departemen' => 'Nama departemen',
        ]);                                                      // akhir validasi

        $id_terakhir = DB::table('departemen')->max('id_departemen'); // id terbesar saat ini, contoh '03'
        $id_departemen = str_pad((int) $id_terakhir + 1, 2, '0', STR_PAD_LEFT); // '03' -> 3 -> 4 -> '04' (jika tabel kosong: 0 + 1 = '01')

        DB::table('departemen')->insert([                        // simpan ke tabel departemen
            'id_departemen' => $id_departemen,                   // id yang dibuat di atas
            'kd_departemen' => strtoupper($request->kd_departemen), // strtoupper = jadikan huruf besar, contoh 'poy' -> 'POY'
            'nm_departemen' => $request->nm_departemen,          // isi dari form
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_at' => now(),                               // waktu sekarang
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('admin.departemen.index')->with('success', 'Departemen '.$request->nm_departemen.' berhasil ditambahkan.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /departemen/{id}/edit)
    {
        $departemen = DB::table('departemen')->where('id_departemen', $id)->first(); // ambil data yang mau diubah

        if (! $departemen) {                                     // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        return view('admin::departemen.edit', compact('departemen')); // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /departemen/{id})
    {
        $departemen = DB::table('departemen')->where('id_departemen', $id)->first(); // ambil data lama

        if (! $departemen) {                                     // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // aturan sama seperti store
            'kd_departemen' => 'required|max:5|unique:departemen,kd_departemen,'.$id.',id_departemen', // unique, tapi kode milik data ini sendiri boleh tetap
            'nm_departemen' => 'required|max:50',
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah dipakai.',
        ], [
            'kd_departemen' => 'Kode departemen',
            'nm_departemen' => 'Nama departemen',
        ]);                                                      // akhir validasi

        DB::table('departemen')->where('id_departemen', $id)->update([ // ubah data departemen dengan id ini
            'kd_departemen' => strtoupper($request->kd_departemen), // huruf besar
            'nm_departemen' => $request->nm_departemen,
            'updated_at' => now(),                               // waktu perubahan
        ]);                                                      // akhir update (id dan status tidak diubah di sini)

        return redirect()->route('admin.departemen.index')->with('success', 'Departemen '.$request->nm_departemen.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /departemen/{id}/status)
    {
        $departemen = DB::table('departemen')->where('id_departemen', $id)->first(); // ambil data

        if (! $departemen) {                                     // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $status_baru = $departemen->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // jika sekarang Aktif jadi Non-Aktif, dan sebaliknya

        DB::table('departemen')->where('id_departemen', $id)->update([ // simpan status baru
            'status_aktif' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Departemen '.$departemen->nm_departemen.' sekarang '.$status_baru.'.'); // kembali ke halaman sebelumnya + pesan sukses
    }
}
