<?php

namespace Modules\Auth\Http\Controllers;                         // "alamat" class ini = folder Modules/Auth/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = fitur login bawaan Laravel
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class LoginController extends Controller                         // class LoginController mewarisi Controller
{
    public function index()                                      // fungsi tampil form login (route GET /login)
    {
        return view('auth::login');                              // tampilkan Modules/Auth/resources/views/login.blade.php
    }

    public function login(Request $request)                      // fungsi proses login (route POST /login); $request = isi form
    {
        // LANGKAH 1: cek input tidak boleh kosong
        $request->validate([                                     // periksa isi form; kalau gagal otomatis kembali ke form
            'username' => 'required',                            // username wajib diisi
            'password' => 'required',                            // password wajib diisi
        ], [                                                     // daftar pesan error
            'username.required' => 'Username wajib diisi.',      // pesan jika username kosong
            'password.required' => 'Password wajib diisi.',      // pesan jika password kosong
        ]);                                                      // akhir validasi

        // LANGKAH 2: cocokkan username dan password
        $berhasil = Auth::attempt([                              // cari user di tabel users + cocokkan password; true = cocok & langsung login
            'username' => $request->username,                    // username yang diketik di form
            'password' => $request->password,                    // password yang diketik di form
        ]);                                                      // hasilnya disimpan di $berhasil (true/false)

        if (! $berhasil) {                                       // jika TIDAK berhasil (username/password salah)
            return back()->with('error', 'Username atau password salah.')->withInput(); // kembali ke form + pesan error + username tetap terisi
        }                                                        // akhir if

        // LANGKAH 3: cek status akun, karyawan, dan role
        $user = Auth::user();                                    // ambil data user yang baru login (baris tabel users)
        $karyawan = DB::table('karyawan')->where('id_karyawan', $user->id_karyawan)->first(); // ambil 1 data karyawan pemilik akun ini
        $role = DB::table('role')->where('id_role', $user->id_role)->first(); // ambil 1 data role milik akun ini

        if ($user->status != 'Aktif' || $karyawan->status_aktif != 'Aktif' || $role->status_aktif != 'Aktif') { // jika akun ATAU karyawan ATAU role tidak aktif
            Auth::logout();                                      // batalkan login (karena attempt sudah terlanjur login)

            return back()->with('error', 'Akun Anda tidak aktif. Hubungi Staf Admin.')->withInput(); // kembali ke form + pesan error
        }                                                        // akhir if

        // LANGKAH 4: login berhasil
        $request->session()->regenerate();                       // buat ID session baru (keamanan)

        return redirect('/dashboard')->with('success', 'Selamat datang, '.$karyawan->nama_karyawan.'.'); // ke dashboard + pesan sukses (titik = sambung teks)
    }

    public function logout(Request $request)                     // fungsi logout (route POST /logout)
    {
        Auth::logout();                                          // hapus status login
        $request->session()->invalidate();                       // hapus semua data session
        $request->session()->regenerateToken();                  // buat token CSRF baru

        return redirect('/login')->with('success', 'Anda berhasil logout.'); // ke halaman login + pesan sukses
    }
}
