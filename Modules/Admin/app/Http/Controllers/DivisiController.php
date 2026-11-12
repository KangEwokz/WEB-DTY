<?php

namespace Modules\Admin\Http\Controllers;                        // "alamat" class ini = folder Modules/Admin/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class DivisiController extends Controller                        // class DivisiController mewarisi Controller
{
    public function index()                                      // tampil daftar divisi (route GET /divisi)
    {
        $divisi = DB::table('divisi')                            // mulai dari tabel divisi
            ->join('departemen', 'divisi.id_departemen', '=', 'departemen.id_departemen') // gabung departemen untuk ambil kode & nama
            ->select('divisi.*', 'departemen.kd_departemen', 'departemen.nm_departemen')  // kolom yang diambil
            ->orderBy('divisi.id_divisi')                        // urut id divisi
            ->get();                                             // ambil semua

        return view('admin::divisi.index', compact('divisi'));   // tampilkan Modules/Admin/resources/views/divisi/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /divisi/create)
    {
        $departemen = DB::table('departemen')->where('status_aktif', 'Aktif')->get(); // pilihan departemen (yang aktif saja)

        return view('admin::divisi.create', compact('departemen')); // tampilkan form + kirim pilihan dropdown
    }

    public function store(Request $request)                      // simpan data baru (route POST /divisi)
    {
        $request->validate([                                     // periksa isi form
            'kd_divisi' => 'required|max:5|unique:divisi,kd_divisi', // wajib, maks 5 karakter, tidak boleh kembar
            'nm_divisi' => 'required|max:50',                    // wajib, maks 50 karakter
            'id_departemen' => 'required|exists:departemen,id_departemen', // wajib dan harus ada di tabel departemen
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah dipakai.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
        ], [                                                     // nama kolom yang tampil di pesan error
            'kd_divisi' => 'Kode divisi',
            'nm_divisi' => 'Nama divisi',
            'id_departemen' => 'Departemen',
        ]);                                                      // akhir validasi

        $id_terakhir = DB::table('divisi')->max('id_divisi');    // id terbesar saat ini, contoh '04'
        $id_divisi = str_pad((int) $id_terakhir + 1, 2, '0', STR_PAD_LEFT); // '04' -> 4 -> 5 -> '05'

        DB::table('divisi')->insert([                            // simpan ke tabel divisi
            'id_divisi' => $id_divisi,                           // id yang dibuat di atas
            'kd_divisi' => strtoupper($request->kd_divisi),      // huruf besar, contoh 'mtc' -> 'MTC'
            'nm_divisi' => $request->nm_divisi,                  // isi dari form
            'id_departemen' => $request->id_departemen,
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi '.$request->nm_divisi.' berhasil ditambahkan.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /divisi/{id}/edit)
    {
        $divisi = DB::table('divisi')->where('id_divisi', $id)->first(); // ambil data yang mau diubah

        if (! $divisi) {                                         // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $departemen = DB::table('departemen')->where('status_aktif', 'Aktif')->orWhere('id_departemen', $divisi->id_departemen)->get(); // pilihan aktif + yang sedang dipakai divisi ini

        return view('admin::divisi.edit', compact('divisi', 'departemen')); // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /divisi/{id})
    {
        $divisi = DB::table('divisi')->where('id_divisi', $id)->first(); // ambil data lama

        if (! $divisi) {                                         // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // aturan sama seperti store
            'kd_divisi' => 'required|max:5|unique:divisi,kd_divisi,'.$id.',id_divisi', // kode milik data ini sendiri boleh tetap
            'nm_divisi' => 'required|max:50',
            'id_departemen' => 'required|exists:departemen,id_departemen',
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'unique' => ':attribute sudah dipakai.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
        ], [
            'kd_divisi' => 'Kode divisi',
            'nm_divisi' => 'Nama divisi',
            'id_departemen' => 'Departemen',
        ]);                                                      // akhir validasi

        DB::table('divisi')->where('id_divisi', $id)->update([   // ubah data divisi dengan id ini
            'kd_divisi' => strtoupper($request->kd_divisi),
            'nm_divisi' => $request->nm_divisi,
            'id_departemen' => $request->id_departemen,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return redirect()->route('admin.divisi.index')->with('success', 'Divisi '.$request->nm_divisi.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /divisi/{id}/status)
    {
        $divisi = DB::table('divisi')->where('id_divisi', $id)->first(); // ambil data

        if (! $divisi) {                                         // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $status_baru = $divisi->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('divisi')->where('id_divisi', $id)->update([   // simpan status baru
            'status_aktif' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Divisi '.$divisi->nm_divisi.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }
}
