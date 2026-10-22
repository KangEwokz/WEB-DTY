<?php

namespace Modules\Qct\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// UC09 Kelola Absensi & Penugasan Area QCT (HOD)
class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('absensi')
            ->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('area', 'absensi.id_area', '=', 'area.id_area')
            ->select('absensi.*', 'karyawan.nama_karyawan', 'karyawan.grup', 'area.nm_area');
        if ($request->tanggal) {
            $query->where('absensi.tgl_absen', $request->tanggal);
        }
        if ($request->shift) {
            $query->where('absensi.shift', $request->shift);
        }
        $absensi = $query->orderBy('absensi.tgl_absen', 'desc')
            ->orderBy('absensi.shift')
            ->orderBy('karyawan.nama_karyawan')
            ->paginate(10)->withQueryString();

        return view('qct::transaksi.absensi.index', compact('absensi'));
    }

    public function create()
    {
        // Karyawan QCT yang masih aktif
        $karyawan = DB::table('karyawan')
            ->where('id_divisi', '03')
            ->where('status_aktif', 'Aktif')
            ->orderBy('nama_karyawan')
            ->get();
        $area = DB::table('area')->where('id_divisi', '03')->where('status_aktif', 'Aktif')->get();
        $sekarang = shift_sekarang();

        return view('qct::transaksi.absensi.create', compact('karyawan', 'area', 'sekarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // hanya karyawan QCT (divisi 03) yang masih aktif
            'id_karyawan' => 'required|exists:karyawan,id_karyawan,status_aktif,Aktif,id_divisi,03',
            'tgl_absen' => 'required|date',
            'shift' => 'required|in:S1,S2,S3,GS',
            'kehadiran' => 'required|in:Hadir,Sakit,Ijin,Alfa,Cuti',
            'id_area' => 'nullable|required_if:kehadiran,Hadir|exists:area,id_area',
            'dok_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if (sudah_terkunci($request->tgl_absen)) {
            return back()->withInput()->with('error', 'Absensi bulan ini sudah diverifikasi HOD, tidak bisa ditambah.');
        }

        // Satu karyawan hanya diabsen sekali per tanggal + shift
        $sudah_ada = DB::table('absensi')
            ->where('id_karyawan', $request->id_karyawan)
            ->where('tgl_absen', $request->tgl_absen)
            ->where('shift', $request->shift)
            ->exists();
        if ($sudah_ada) {
            return back()->withInput()->withErrors(['id_karyawan' => 'Karyawan ini sudah diabsen pada tanggal dan shift tersebut.']);
        }

        // Surat sakit/ijin disimpan di storage/app/public/absensi
        $dok_file = null;
        if ($request->hasFile('dok_file')) {
            $dok_file = $request->file('dok_file')->store('absensi', 'public');
        }

        DB::table('absensi')->insert([
            'id_absen' => buat_kode('ABS', $request->tgl_absen),
            'id_karyawan' => $request->id_karyawan,
            'tgl_absen' => $request->tgl_absen,
            'shift' => $request->shift,
            'id_area' => $request->kehadiran == 'Hadir' ? $request->id_area : null,
            'kehadiran' => $request->kehadiran,
            'dok_file' => $dok_file,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.absensi.index')->with('success', 'Data absensi berhasil disimpan.');
    }

    public function edit($id)
    {
        $absensi = DB::table('absensi')
            ->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('absensi.id_absen', $id)
            ->select('absensi.*', 'karyawan.nama_karyawan')
            ->first();
        if (! $absensi) {
            abort(404);
        }
        if (sudah_terkunci($absensi->tgl_absen)) {
            return redirect()->route('qct.absensi.index')->with('error', 'Absensi bulan ini sudah diverifikasi HOD, tidak bisa diubah.');
        }
        $area = DB::table('area')->where('id_divisi', '03')->where('status_aktif', 'Aktif')->get();

        return view('qct::transaksi.absensi.edit', compact('absensi', 'area'));
    }

    public function update(Request $request, $id)
    {
        $absensi = DB::table('absensi')->where('id_absen', $id)->first();
        if (! $absensi) {
            abort(404);
        }
        if (sudah_terkunci($absensi->tgl_absen)) {
            return redirect()->route('qct.absensi.index')->with('error', 'Absensi bulan ini sudah diverifikasi HOD, tidak bisa diubah.');
        }

        // Karyawan, tanggal, dan shift tidak diubah (bagian dari ID). Kalau salah, hapus lalu input ulang.
        $request->validate([
            'kehadiran' => 'required|in:Hadir,Sakit,Ijin,Alfa,Cuti',
            'id_area' => 'nullable|required_if:kehadiran,Hadir|exists:area,id_area',
            'dok_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $dok_file = $absensi->dok_file;
        if ($request->hasFile('dok_file')) {
            if ($dok_file) {
                Storage::disk('public')->delete($dok_file);
            }
            $dok_file = $request->file('dok_file')->store('absensi', 'public');
        }

        DB::table('absensi')->where('id_absen', $id)->update([
            'id_area' => $request->kehadiran == 'Hadir' ? $request->id_area : null,
            'kehadiran' => $request->kehadiran,
            'dok_file' => $dok_file,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.absensi.index')->with('success', 'Data absensi berhasil diubah.');
    }

    public function destroy($id)
    {
        $absensi = DB::table('absensi')->where('id_absen', $id)->first();
        if (! $absensi) {
            abort(404);
        }
        if (sudah_terkunci($absensi->tgl_absen)) {
            return redirect()->route('qct.absensi.index')->with('error', 'Absensi bulan ini sudah diverifikasi HOD, tidak bisa dihapus.');
        }

        if ($absensi->dok_file) {
            Storage::disk('public')->delete($absensi->dok_file);
        }
        DB::table('absensi')->where('id_absen', $id)->delete();

        return redirect()->route('qct.absensi.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}
