<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Halaman awal setelah login: data karyawan, area, dan shift yang sedang berjalan
    public function index()
    {
        $karyawan = DB::table('karyawan')
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->leftJoin('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
            ->where('karyawan.id_karyawan', auth()->user()->id_karyawan)
            ->select('karyawan.*', 'jabatan.nm_jabatan', 'divisi.nm_divisi')
            ->first();

        $area = DB::table('area')->where('id_area', area_aktif())->value('nm_area');
        $shift = shift_sekarang();

        return view('auth::dashboard', compact('karyawan', 'area', 'shift'));
    }
}
