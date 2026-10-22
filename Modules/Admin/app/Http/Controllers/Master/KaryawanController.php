<?php

namespace Modules\Admin\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC03 Kelola Data Karyawan
class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('karyawan')
            ->leftJoin('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->select('karyawan.*', 'divisi.nm_divisi', 'jabatan.nm_jabatan');

        if ($request->cari) {
            $query->where('karyawan.nama_karyawan', 'like', '%'.$request->cari.'%');
        }
        if ($request->id_divisi) {
            $query->where('karyawan.id_divisi', $request->id_divisi);
        }
        if ($request->status_aktif) {
            $query->where('karyawan.status_aktif', $request->status_aktif);
        }

        $karyawan = $query->orderBy('karyawan.id_karyawan')->paginate(10)->withQueryString();
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();

        return view('admin::master.karyawan.index', compact('karyawan', 'divisi'));
    }

    public function create()
    {
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();
        $area = DB::table('area')->where('status_aktif', 'Aktif')->orderBy('id_area')->get();

        return view('admin::master.karyawan.create', compact('divisi', 'jabatan', 'area'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|max:50',
            'jns_kelamin' => 'required|in:L,P',
            'id_divisi' => 'nullable|exists:divisi,id_divisi',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_area' => 'nullable|exists:area,id_area',
            'grup' => 'required|in:A,B,C,D,E,F,G,NG',
            'tgl_masuk' => 'required|date',
            'no_hp' => 'nullable|max:15',
            'email' => 'nullable|email|max:100',
        ]);

        DB::table('karyawan')->insert([
            'id_karyawan' => buat_kode('KRY', $request->tgl_masuk),
            'nama_karyawan' => $request->nama_karyawan,
            'jns_kelamin' => $request->jns_kelamin,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_area' => $request->id_area,
            'grup' => $request->grup,
            'tgl_masuk' => $request->tgl_masuk,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'status_aktif' => 'Aktif',
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil disimpan.');
    }

    public function show($id)
    {
        $karyawan = DB::table('karyawan')
            ->leftJoin('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->leftJoin('area', 'karyawan.id_area', '=', 'area.id_area')
            ->leftJoin('users', 'karyawan.id_karyawan', '=', 'users.id_karyawan')
            ->where('karyawan.id_karyawan', $id)
            ->select('karyawan.*', 'divisi.nm_divisi', 'jabatan.nm_jabatan', 'area.nm_area', 'users.username')
            ->first();
        if (! $karyawan) {
            abort(404);
        }

        return view('admin::master.karyawan.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id)->first();
        if (! $karyawan) {
            abort(404);
        }
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();
        $area = DB::table('area')->where('status_aktif', 'Aktif')->orderBy('id_area')->get();

        return view('admin::master.karyawan.edit', compact('karyawan', 'divisi', 'jabatan', 'area'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_karyawan' => 'required|max:50',
            'jns_kelamin' => 'required|in:L,P',
            'id_divisi' => 'nullable|exists:divisi,id_divisi',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_area' => 'nullable|exists:area,id_area',
            'grup' => 'required|in:A,B,C,D,E,F,G,NG',
            'tgl_masuk' => 'required|date',
            'no_hp' => 'nullable|max:15',
            'email' => 'nullable|email|max:100',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',
        ]);

        DB::table('karyawan')->where('id_karyawan', $id)->update([
            'nama_karyawan' => $request->nama_karyawan,
            'jns_kelamin' => $request->jns_kelamin,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_area' => $request->id_area,
            'grup' => $request->grup,
            'tgl_masuk' => $request->tgl_masuk,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'status_aktif' => $request->status_aktif,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diubah.');
    }

    // Karyawan keluar tidak dihapus, cukup dinonaktifkan (akunnya otomatis tidak bisa login)
    public function destroy($id)
    {
        if ($id == auth()->user()->id_karyawan) {
            return redirect()->route('admin.karyawan.index')->with('error', 'Tidak bisa menonaktifkan diri sendiri.');
        }

        DB::table('karyawan')->where('id_karyawan', $id)->update([
            'status_aktif' => 'Non-Aktif',
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil dinonaktifkan.');
    }
}
