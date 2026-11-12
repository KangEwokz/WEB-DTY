<?php

namespace Modules\Admin\Http\Controllers;                        // "alamat" class ini = folder Modules/Admin/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database
use Illuminate\Support\Facades\Storage;                          // Storage = untuk menyimpan/menghapus file (foto)

class KaryawanController extends Controller                      // class KaryawanController mewarisi Controller
{
    public function index(Request $request)                      // tampil daftar karyawan (route GET /karyawan)
    {
        $query = DB::table('karyawan')                           // mulai susun query dari tabel karyawan
            ->leftJoin('departemen', 'karyawan.id_departemen', '=', 'departemen.id_departemen') // gabung departemen untuk ambil nama (left = tetap tampil walau kosong)
            ->leftJoin('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')                 // gabung divisi untuk ambil nama
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')             // gabung jabatan untuk ambil nama
            ->select('karyawan.*', 'departemen.kd_departemen', 'divisi.nm_divisi', 'jabatan.nm_jabatan'); // kolom yang diambil

        if ($request->cari) {                                    // jika kotak pencarian diisi (URL: /karyawan?cari=rina)
            $query->where('karyawan.nama_karyawan', 'like', '%'.$request->cari.'%')   // cari nama yang mengandung kata tersebut
                ->orWhere('karyawan.id_karyawan', 'like', '%'.$request->cari.'%');    // ATAU id karyawan yang mengandung kata tersebut
        }                                                        // akhir if

        $karyawan = $query->orderBy('karyawan.id_karyawan')      // urutkan berdasarkan id karyawan
            ->paginate(10)                                       // ambil 10 data per halaman
            ->withQueryString();                                 // kata pencarian ikut terbawa saat pindah halaman

        return view('admin::karyawan.index', compact('karyawan')); // tampilkan Modules/Admin/resources/views/karyawan/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /karyawan/create)
    {
        $departemen = DB::table('departemen')->where('status_aktif', 'Aktif')->get(); // pilihan departemen (yang aktif saja)
        $divisi = DB::table('divisi')->where('status_aktif', 'Aktif')->get();         // pilihan divisi
        $jabatan = DB::table('jabatan')->where('status_aktif', 'Aktif')->get();       // pilihan jabatan
        $area = DB::table('area')->where('status_aktif', 'Aktif')->get();             // pilihan area

        return view('admin::karyawan.create', compact('departemen', 'divisi', 'jabatan', 'area')); // tampilkan form + kirim pilihan dropdown
    }

    public function store(Request $request)                      // simpan data baru (route POST /karyawan)
    {
        $request->validate([                                     // periksa isi form; kalau gagal otomatis kembali ke form
            'nama_karyawan' => 'required|max:50',                // wajib, maks 50 karakter
            'jns_kelamin' => 'required|in:L,P',                  // wajib, hanya boleh L atau P
            'id_departemen' => 'nullable|exists:departemen,id_departemen', // boleh kosong; jika diisi harus ada di tabel departemen
            'id_divisi' => 'nullable|exists:divisi,id_divisi',   // boleh kosong; jika diisi harus ada di tabel divisi
            'id_jabatan' => 'required|exists:jabatan,id_jabatan', // wajib dan harus ada di tabel jabatan
            'id_area' => 'nullable|exists:area,id_area',         // boleh kosong; jika diisi harus ada di tabel area
            'grup' => 'required|in:A,B,C,D,E,F,G,NG',            // wajib, salah satu grup ini
            'tgl_masuk' => 'required|date',                      // wajib, harus tanggal
            'no_hp' => 'nullable|max:15',                        // boleh kosong, maks 15 karakter
            'email' => 'nullable|email|max:100',                 // boleh kosong; jika diisi harus format email
            'foto' => 'nullable|image|max:2048',                 // boleh kosong; jika diisi harus gambar, maks 2048 KB (2 MB)
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',             // :attribute diganti nama kolom
            'max' => ':attribute terlalu panjang/besar.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'date' => ':attribute harus berupa tanggal.',
            'email' => 'Format email tidak benar.',
            'image' => 'File foto harus berupa gambar (jpg, png, dll).',
        ], [                                                     // nama kolom yang tampil di pesan error (pengganti :attribute)
            'nama_karyawan' => 'Nama karyawan',
            'jns_kelamin' => 'Jenis kelamin',
            'id_departemen' => 'Departemen',
            'id_divisi' => 'Divisi',
            'id_jabatan' => 'Jabatan',
            'id_area' => 'Area',
            'grup' => 'Grup',
            'tgl_masuk' => 'Tanggal masuk',
            'no_hp' => 'No. HP',
            'status_aktif' => 'Status',
            'foto' => 'Foto',
        ]);                                                      // akhir validasi

        // Membuat ID karyawan: tahun masuk + nomor urut, contoh 2019-0004
        $tahun = date('Y', strtotime($request->tgl_masuk));      // ambil tahun dari tanggal masuk, contoh '2019'
        $id_terakhir = DB::table('karyawan')->where('id_karyawan', 'like', $tahun.'-%')->max('id_karyawan'); // id terbesar di tahun itu, contoh '2019-0003'
        $urut = $id_terakhir ? (int) substr($id_terakhir, 5) + 1 : 1; // ambil '0003' -> 3 -> tambah 1 = 4; jika belum ada mulai dari 1
        $id_karyawan = $tahun.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT); // gabungkan: '2019' + '-' + '0004'

        $foto = null;                                            // bawaan: tidak ada foto
        if ($request->hasFile('foto')) {                         // jika user memilih file foto
            $foto = $request->file('foto')->storeAs('foto', $id_karyawan.'.'.$request->file('foto')->extension(), 'uploads'); // simpan ke public/uploads/foto/2019-0004.jpg; hasilnya 'foto/2019-0004.jpg'
        }                                                        // akhir if

        DB::table('karyawan')->insert([                          // simpan ke tabel karyawan
            'id_karyawan' => $id_karyawan,                       // id yang dibuat di atas
            'nama_karyawan' => $request->nama_karyawan,          // isi dari form
            'jns_kelamin' => $request->jns_kelamin,
            'id_departemen' => $request->id_departemen,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_area' => $request->id_area,
            'grup' => $request->grup,
            'tgl_masuk' => $request->tgl_masuk,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'foto' => $foto,                                     // lokasi foto atau null
            'status_aktif' => 'Aktif',                           // karyawan baru selalu Aktif
            'created_at' => now(),                               // waktu sekarang
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan '.$request->nama_karyawan.' berhasil ditambahkan dengan ID '.$id_karyawan.'.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                                   // tampil form ubah (route GET /karyawan/{id}/edit); $id diambil dari URL
    {
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id)->first(); // ambil data karyawan yang mau diubah

        if (! $karyawan) {                                       // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404 (tidak ditemukan)
        }                                                        // akhir if

        $departemen = DB::table('departemen')->where('status_aktif', 'Aktif')->orWhere('id_departemen', $karyawan->id_departemen)->get(); // pilihan aktif + pilihan yang sedang dipakai karyawan ini
        $divisi = DB::table('divisi')->where('status_aktif', 'Aktif')->orWhere('id_divisi', $karyawan->id_divisi)->get();
        $jabatan = DB::table('jabatan')->where('status_aktif', 'Aktif')->orWhere('id_jabatan', $karyawan->id_jabatan)->get();
        $area = DB::table('area')->where('status_aktif', 'Aktif')->orWhere('id_area', $karyawan->id_area)->get();

        return view('admin::karyawan.edit', compact('karyawan', 'departemen', 'divisi', 'jabatan', 'area')); // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)               // simpan perubahan (route PUT /karyawan/{id})
    {
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id)->first(); // ambil data lama

        if (! $karyawan) {                                       // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // aturan sama seperti store, ditambah status_aktif
            'nama_karyawan' => 'required|max:50',
            'jns_kelamin' => 'required|in:L,P',
            'id_departemen' => 'nullable|exists:departemen,id_departemen',
            'id_divisi' => 'nullable|exists:divisi,id_divisi',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_area' => 'nullable|exists:area,id_area',
            'grup' => 'required|in:A,B,C,D,E,F,G,NG',
            'tgl_masuk' => 'required|date',
            'no_hp' => 'nullable|max:15',
            'email' => 'nullable|email|max:100',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',     // wajib, Aktif atau Non-Aktif
            'foto' => 'nullable|image|max:2048',
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute terlalu panjang/besar.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'date' => ':attribute harus berupa tanggal.',
            'email' => 'Format email tidak benar.',
            'image' => 'File foto harus berupa gambar (jpg, png, dll).',
        ], [                                                     // nama kolom yang tampil di pesan error (pengganti :attribute)
            'nama_karyawan' => 'Nama karyawan',
            'jns_kelamin' => 'Jenis kelamin',
            'id_departemen' => 'Departemen',
            'id_divisi' => 'Divisi',
            'id_jabatan' => 'Jabatan',
            'id_area' => 'Area',
            'grup' => 'Grup',
            'tgl_masuk' => 'Tanggal masuk',
            'no_hp' => 'No. HP',
            'status_aktif' => 'Status',
            'foto' => 'Foto',
        ]);                                                      // akhir validasi

        $foto = $karyawan->foto;                                 // bawaan: pakai foto lama
        if ($request->hasFile('foto')) {                         // jika user memilih foto baru
            if ($karyawan->foto) {                               // jika sebelumnya sudah punya foto
                Storage::disk('uploads')->delete($karyawan->foto); // hapus file foto lama
            }                                                    // akhir if
            $foto = $request->file('foto')->storeAs('foto', $id.'.'.$request->file('foto')->extension(), 'uploads'); // simpan foto baru
        }                                                        // akhir if

        DB::table('karyawan')->where('id_karyawan', $id)->update([ // ubah data karyawan dengan id ini
            'nama_karyawan' => $request->nama_karyawan,          // isi dari form
            'jns_kelamin' => $request->jns_kelamin,
            'id_departemen' => $request->id_departemen,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_area' => $request->id_area,
            'grup' => $request->grup,
            'tgl_masuk' => $request->tgl_masuk,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'foto' => $foto,                                     // foto baru atau foto lama
            'status_aktif' => $request->status_aktif,
            'updated_at' => now(),                               // waktu perubahan
        ]);                                                      // akhir update (id_karyawan tidak diubah)

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan '.$request->nama_karyawan.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function destroy(string $id)                                // hapus data (route DELETE /karyawan/{id})
    {
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id)->first(); // ambil data yang mau dihapus

        if (! $karyawan) {                                       // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $punya_akun = DB::table('users')->where('id_karyawan', $id)->exists(); // cek apakah karyawan ini punya akun login
        if ($punya_akun) {                                       // jika punya akun, tidak boleh dihapus (data akun ikut bergantung)
            return back()->with('error', 'Karyawan '.$karyawan->nama_karyawan.' punya akun login sehingga tidak bisa dihapus. Ubah statusnya menjadi Non-Aktif.'); // kembali + pesan error
        }                                                        // akhir if

        if ($karyawan->foto) {                                   // jika punya foto
            Storage::disk('uploads')->delete($karyawan->foto);   // hapus file fotonya
        }                                                        // akhir if

        DB::table('karyawan')->where('id_karyawan', $id)->delete(); // hapus data dari tabel karyawan

        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan '.$karyawan->nama_karyawan.' berhasil dihapus.'); // kembali ke daftar + pesan sukses
    }
}
