<?php

namespace Modules\Auth\Http\Controllers;                         // "alamat" class ini = folder Modules/Auth/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class RoleController extends Controller                          // class RoleController mewarisi Controller
{
    public function index()                                      // tampil daftar role (route GET /role)
    {
        $role = DB::table('role')                                // mulai dari tabel role
            ->select(
                'role.*',                                        // semua kolom role
                DB::raw('(select count(*) from role_permission where role_permission.id_role = role.id_role) as jml_permission'), // hitung jumlah permission milik role ini
                DB::raw('(select count(*) from users where users.id_role = role.id_role) as jml_user') // hitung jumlah akun yang memakai role ini
            )
            ->orderBy('role.nm_role')                            // urut nama role A-Z
            ->get();                                             // ambil semua

        return view('auth::role.index', compact('role'));        // tampilkan Modules/Auth/resources/views/role/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /role/create)
    {
        return view('auth::role.create');                        // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan role baru (route POST /role)
    {
        $request->validate([                                     // periksa isi form
            'id_role' => 'required|max:20|alpha_dash|unique:role,id_role', // wajib, maks 20, hanya huruf/angka/-/_ (tanpa spasi), tidak boleh kembar
            'nm_role' => 'required|max:50',                      // wajib, maks 50 karakter
        ], [                                                     // daftar pesan error
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'alpha_dash' => ':attribute hanya boleh huruf, angka, - dan _ (tanpa spasi).',
            'unique' => ':attribute sudah dipakai.',
        ], [                                                     // nama kolom yang tampil di pesan error
            'id_role' => 'Kode role',
            'nm_role' => 'Nama role',
        ]);                                                      // akhir validasi

        DB::table('role')->insert([                              // simpan ke tabel role
            'id_role' => strtolower($request->id_role),          // strtolower = huruf kecil semua, contoh 'Foreman' -> 'foreman'
            'nm_role' => $request->nm_role,
            'status_aktif' => 'Aktif',                           // role baru selalu Aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('auth.role.permission', strtolower($request->id_role))->with('success', 'Role '.$request->nm_role.' berhasil ditambahkan. Silakan centang permission-nya.'); // langsung ke halaman atur permission role baru
    }

    public function edit(string $id)                             // tampil form ubah (route GET /role/{id}/edit)
    {
        $role = DB::table('role')->where('id_role', $id)->first(); // ambil data yang mau diubah

        if (! $role) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        return view('auth::role.edit', compact('role'));         // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /role/{id})
    {
        $role = DB::table('role')->where('id_role', $id)->first(); // ambil data lama

        if (! $role) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // kode role tidak bisa diubah (dipakai di tabel users), jadi hanya nama
            'nm_role' => 'required|max:50',
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
        ], [
            'nm_role' => 'Nama role',
        ]);                                                      // akhir validasi

        DB::table('role')->where('id_role', $id)->update([       // ubah nama role dengan id ini
            'nm_role' => $request->nm_role,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return redirect()->route('auth.role.index')->with('success', 'Role '.$request->nm_role.' berhasil diubah.'); // kembali ke daftar + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /role/{id}/status)
    {
        $role = DB::table('role')->where('id_role', $id)->first(); // ambil data

        if (! $role) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        if ($id == 'superadmin') {                               // role superadmin tidak boleh dinonaktifkan (nanti tidak ada yang bisa mengatur sistem)
            return back()->with('error', 'Role Super Admin tidak bisa dinonaktifkan.'); // kembali + pesan error
        }                                                        // akhir if

        if ($id == Auth::user()->id_role) {                      // tidak boleh menonaktifkan role milik sendiri (nanti diri sendiri tidak bisa login)
            return back()->with('error', 'Anda tidak bisa menonaktifkan role yang sedang Anda pakai.'); // kembali + pesan error
        }                                                        // akhir if

        $status_baru = $role->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('role')->where('id_role', $id)->update([       // simpan status baru
            'status_aktif' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Role '.$role->nm_role.' sekarang '.$status_baru.'. '.($status_baru == 'Non-Aktif' ? 'Akun dengan role ini tidak bisa login.' : '')); // kembali + pesan sukses (ditambah peringatan jika dinonaktifkan)
    }

    public function permission(string $id)                       // tampil halaman centang permission (route GET /role/{id}/permission)
    {
        $role = DB::table('role')->where('id_role', $id)->first(); // ambil role yang mau diatur

        if (! $role) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        if ($id == 'superadmin') {                               // superadmin selalu boleh semua (lihat User::punyaAkses), jadi tidak perlu diatur
            return redirect()->route('auth.role.index')->with('error', 'Super Admin otomatis punya semua akses, tidak perlu diatur.'); // kembali + pesan
        }                                                        // akhir if

        $semua_permission = DB::table('permission')->orderBy('id_permission')->get(); // ambil semua permission yang ada

        $grup = [];                                              // wadah: permission dikelompokkan per menu
        foreach ($semua_permission as $p) {                      // ulangi setiap permission, contoh $p->id_permission = 'admin.karyawan.create'
            $bagian = explode('.', $p->id_permission);           // pecah dengan titik -> ['admin', 'karyawan', 'create']
            $grup[$bagian[1]][] = $p;                            // masukkan ke kelompok 'karyawan'
        }                                                        // hasil: ['karyawan' => [...], 'divisi' => [...], ...]

        $dimiliki = DB::table('role_permission')->where('id_role', $id)->pluck('id_permission')->toArray(); // daftar permission yang SUDAH dimiliki role ini, contoh ['admin.karyawan.index', ...]

        return view('auth::role.permission', compact('role', 'grup', 'dimiliki')); // tampilkan halaman centang
    }

    public function simpanPermission(Request $request, string $id) // simpan centangan (route PUT /role/{id}/permission)
    {
        $role = DB::table('role')->where('id_role', $id)->first(); // ambil role

        if (! $role || $id == 'superadmin') {                    // jika tidak ditemukan atau superadmin
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        $request->validate([                                     // periksa data yang dikirim
            'permission' => 'nullable|array',                    // boleh kosong (semua tidak dicentang); jika ada harus berupa daftar
            'permission.*' => 'exists:permission,id_permission', // setiap isi daftar harus ada di tabel permission
        ]);                                                      // akhir validasi

        $dicentang = $request->permission ?? [];                 // daftar yang dicentang; jika tidak ada yang dicentang = daftar kosong

        DB::table('role_permission')->where('id_role', $id)->delete(); // 1. hapus semua permission lama milik role ini

        foreach ($dicentang as $id_permission) {                 // 2. ulangi setiap permission yang dicentang
            DB::table('role_permission')->insert([               // simpan pasangan baru
                'id_role' => $id,
                'id_permission' => $id_permission,
            ]);
        }                                                        // akhir perulangan

        return redirect()->route('auth.role.index')->with('success', 'Permission role '.$role->nm_role.' berhasil disimpan ('.count($dicentang).' permission).'); // kembali ke daftar + pesan sukses; count() = jumlah isi daftar
    }
}
