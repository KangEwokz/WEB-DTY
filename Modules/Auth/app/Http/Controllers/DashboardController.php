<?php

namespace Modules\Auth\Http\Controllers;                         // "alamat" class ini = folder Modules/Auth/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Support\Facades\Auth;                             // Auth = untuk mengambil user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class DashboardController extends Controller                     // class DashboardController mewarisi Controller
{
    public function index()                                      // fungsi tampil dashboard (route GET /dashboard)
    {
        $karyawan = DB::table('karyawan')                        // ambil data dari tabel karyawan
            ->leftJoin('departemen', 'karyawan.id_departemen', '=', 'departemen.id_departemen') // gabung tabel departemen untuk ambil nama departemen (left = tetap tampil walau kosong)
            ->leftJoin('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')                 // gabung tabel divisi untuk ambil nama divisi
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')             // gabung tabel jabatan untuk ambil nama jabatan
            ->leftJoin('area', 'karyawan.id_area', '=', 'area.id_area')                         // gabung tabel area untuk ambil nama area
            ->select('karyawan.*', 'departemen.nm_departemen', 'divisi.nm_divisi', 'jabatan.nm_jabatan', 'area.nm_area') // kolom yang diambil: semua kolom karyawan + 4 nama
            ->where('karyawan.id_karyawan', Auth::user()->id_karyawan)                          // hanya karyawan milik user yang sedang login
            ->first();                                           // ambil 1 baris saja

        $role = DB::table('role')->where('id_role', Auth::user()->id_role)->first(); // ambil data role milik user yang sedang login

        return view('auth::dashboard', compact('karyawan', 'role')); // tampilkan dashboard.blade.php + kirim variabel $karyawan dan $role
    }
}
