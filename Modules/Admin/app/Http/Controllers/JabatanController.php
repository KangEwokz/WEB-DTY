<?php

namespace Modules\Admin\Http\Controllers;                        // "alamat" class ini = folder Modules/Admin/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class JabatanController extends Controller                       // class JabatanController mewarisi Controller
{
    public function index()                                      // tampil daftar jabatan (route GET /jabatan)
    {
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get(); // ambil semua jabatan, urut id

        return view('admin::jabatan.index', compact('jabatan')); // tampilkan Modules/Admin/resources/views/jabatan/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /jabatan/create)
    {
        return view('admin::jabatan.create');                    // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan data baru (route POST /jabatan)
    {
        $request->validate([                                     // periksa isi form
            'nm_jabatan' => 'required|max:30|unique:jabatan,nm_jabatan', // wajib, maks 30 karakter, nama tidak boleh kembar
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah ada.',
        ], [                                                     // nama kolom yang tampil di pesan error
            'nm_jabatan' => 'Nama jabatan',
        ]);                                                      // akhir validasi

        $id_terakhir = DB::table('jabatan')->max('id_jabatan');  // id terbesar saat ini, contoh '08'
        $id_jabatan = str_pad((int) $id_terakhir + 1, 2, '0', STR_PAD_LEFT); // '08' -> 8 -> 9 -> '09'

        DB::table('jabatan')->insert([                           // simpan ke tabel jabatan
            'id_jabatan' => $id_jabatan,                         // id yang dibuat di atas
            'nm_jabatan' => $request->nm_jabatan,                // isi dari form
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan '.$request->nm_jabatan.' berhasil ditambahkan.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /jabatan/{id}/edit)
    {
        $jabatan = DB::table('jabatan')->where('id_jabatan', $id)->first(); // ambil data yang mau diubah

        if (! $jabatan) {                                        // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        return view('admin::jabatan.edit', compact('jabatan'));  // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /jabatan/{id})
    {
        $jabatan = DB::table('jabatan')->where('id_jabatan', $id)->first(); // ambil data lama

        if (! $jabatan) {                                        // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // aturan sama seperti store
            'nm_jabatan' => 'required|max:30|unique:jabatan,nm_jabatan,'.$id.',id_jabatan', // nama milik data ini sendiri boleh tetap
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah ada.',
        ], [
            'nm_jabatan' => 'Nama jabatan',
        ]);                                                      // akhir validasi

        DB::table('jabatan')->where('id_jabatan', $id)->update([ // ubah data jabatan dengan id ini
            'nm_jabatan' => $request->nm_jabatan,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return redirect()->route('admin.jabatan.index')->with('success', 'Jabatan '.$request->nm_jabatan.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /jabatan/{id}/status)
    {
        $jabatan = DB::table('jabatan')->where('id_jabatan', $id)->first(); // ambil data

        if (! $jabatan) {                                        // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $status_baru = $jabatan->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('jabatan')->where('id_jabatan', $id)->update([ // simpan status baru
            'status_aktif' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Jabatan '.$jabatan->nm_jabatan.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }
}
