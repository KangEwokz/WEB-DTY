<?php

namespace Modules\Admin\Http\Controllers\Output;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC05 Melihat Rekap Absensi QCT (baca saja, hanya yang sudah Terverifikasi oleh HOD)
class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('rekap_absensi')
            ->leftJoin('karyawan', 'rekap_absensi.diverifikasi_oleh', '=', 'karyawan.id_karyawan')
            ->where('rekap_absensi.status', 'Terverifikasi')
            ->select('rekap_absensi.*', 'karyawan.nama_karyawan as verifikator');

        if ($request->tahun) {
            $query->where('rekap_absensi.periode', 'like', $request->tahun.'-%');
        }

        $rekap = $query->orderBy('rekap_absensi.periode', 'desc')->paginate(10)->withQueryString();

        return view('admin::output.rekap-absensi.index', compact('rekap'));
    }

    public function show($id)
    {
        $rekap = DB::table('rekap_absensi')
            ->leftJoin('karyawan', 'rekap_absensi.diverifikasi_oleh', '=', 'karyawan.id_karyawan')
            ->where('rekap_absensi.id_rekap', $id)
            ->where('rekap_absensi.status', 'Terverifikasi')
            ->select('rekap_absensi.*', 'karyawan.nama_karyawan as verifikator')
            ->first();
        if (! $rekap) {
            abort(404);
        }

        // Angka rekap tidak disimpan, dihitung dari tabel absensi pada periode tersebut
        $tahun = substr($rekap->periode, 0, 4);
        $bulan = substr($rekap->periode, 5, 2);

        $data = DB::table('absensi')
            ->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereYear('absensi.tgl_absen', $tahun)
            ->whereMonth('absensi.tgl_absen', $bulan)
            ->select(
                'karyawan.id_karyawan',
                'karyawan.nama_karyawan',
                'karyawan.grup',
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Ijin' THEN 1 ELSE 0 END) as ijin"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Alfa' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Cuti' THEN 1 ELSE 0 END) as cuti"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('karyawan.id_karyawan', 'karyawan.nama_karyawan', 'karyawan.grup')
            ->orderBy('karyawan.nama_karyawan')
            ->get();

        return view('admin::output.rekap-absensi.show', compact('rekap', 'data'));
    }
}
