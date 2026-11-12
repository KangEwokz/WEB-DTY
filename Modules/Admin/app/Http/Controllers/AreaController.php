<?php

namespace Modules\Admin\Http\Controllers;                        // "alamat" class ini = folder Modules/Admin/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class AreaController extends Controller                          // class AreaController mewarisi Controller
{
    public function index()                                      // tampil daftar area (route GET /area)
    {
        $area = DB::table('area')                                // mulai dari tabel area
            ->join('divisi', 'area.id_divisi', '=', 'divisi.id_divisi')                   // gabung divisi untuk ambil nama divisi
            ->join('departemen', 'divisi.id_departemen', '=', 'departemen.id_departemen') // gabung departemen (lewat divisi) untuk ambil kode departemen
            ->select('area.*', 'divisi.nm_divisi', 'departemen.kd_departemen')           // kolom yang diambil
            ->orderBy('area.id_area')                            // urut id area
            ->get();                                             // ambil semua

        return view('admin::area.index', compact('area'));       // tampilkan Modules/Admin/resources/views/area/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /area/create)
    {
        $divisi = DB::table('divisi')                            // pilihan divisi
            ->join('departemen', 'divisi.id_departemen', '=', 'departemen.id_departemen') // gabung departemen agar di dropdown tertulis "DTY - Quality Control"
            ->select('divisi.*', 'departemen.kd_departemen')
            ->where('divisi.status_aktif', 'Aktif')              // yang aktif saja
            ->get();

        return view('admin::area.create', compact('divisi'));    // tampilkan form + kirim pilihan dropdown
    }

    public function store(Request $request)                      // simpan data baru (route POST /area)
    {
        $request->validate([                                     // periksa isi form
            'id_area' => 'required|max:10|alpha_num|unique:area,id_area', // wajib, maks 10, hanya huruf/angka, tidak boleh kembar
            'nm_area' => 'required|max:50',                      // wajib, maks 50 karakter
            'id_divisi' => 'required|exists:divisi,id_divisi',   // wajib dan harus ada di tabel divisi
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'alpha_num' => ':attribute hanya boleh huruf dan angka (tanpa spasi).',
            'unique' => ':attribute sudah dipakai.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
        ], [                                                     // nama kolom yang tampil di pesan error
            'id_area' => 'Kode area',
            'nm_area' => 'Nama area',
            'id_divisi' => 'Divisi',
        ]);                                                      // akhir validasi

        DB::table('area')->insert([                              // simpan ke tabel area
            'id_area' => strtoupper($request->id_area),          // id area diketik sendiri, dijadikan huruf besar, contoh 'tex' -> 'TEX'
            'nm_area' => $request->nm_area,
            'id_divisi' => $request->id_divisi,
            'status_aktif' => 'Aktif',                           // data baru selalu Aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('admin.area.index')->with('success', 'Area '.$request->nm_area.' berhasil ditambahkan.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /area/{id}/edit)
    {
        $area = DB::table('area')->where('id_area', $id)->first(); // ambil data yang mau diubah

        if (! $area) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $divisi = DB::table('divisi')                            // pilihan divisi
            ->join('departemen', 'divisi.id_departemen', '=', 'departemen.id_departemen')
            ->select('divisi.*', 'departemen.kd_departemen')
            ->where('divisi.status_aktif', 'Aktif')              // yang aktif
            ->orWhere('divisi.id_divisi', $area->id_divisi)      // + divisi yang sedang dipakai area ini
            ->get();

        return view('admin::area.edit', compact('area', 'divisi')); // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /area/{id})
    {
        $area = DB::table('area')->where('id_area', $id)->first(); // ambil data lama

        if (! $area) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // id area tidak bisa diubah, jadi tidak divalidasi
            'nm_area' => 'required|max:50',
            'id_divisi' => 'required|exists:divisi,id_divisi',
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
        ], [
            'nm_area' => 'Nama area',
            'id_divisi' => 'Divisi',
        ]);                                                      // akhir validasi

        DB::table('area')->where('id_area', $id)->update([       // ubah data area dengan id ini
            'nm_area' => $request->nm_area,
            'id_divisi' => $request->id_divisi,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return redirect()->route('admin.area.index')->with('success', 'Area '.$request->nm_area.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /area/{id}/status)
    {
        $area = DB::table('area')->where('id_area', $id)->first(); // ambil data

        if (! $area) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $status_baru = $area->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('area')->where('id_area', $id)->update([       // simpan status baru
            'status_aktif' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Area '.$area->nm_area.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }
}
