<?php

namespace Modules\Admin\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC03: area milik divisi Admin (contoh KPG). Area QCT (VIS, LAB) dikelola HOD di modul Qct.
class AreaController extends Controller
{
    // id_divisi milik Admin
    private $divisi = '01';

    public function index(Request $request)
    {
        $query = DB::table('area')->where('id_divisi', $this->divisi);
        if ($request->cari) {
            $query->where('nm_area', 'like', '%'.$request->cari.'%');
        }
        $area = $query->orderBy('id_area')->paginate(10)->withQueryString();

        return view('admin::master.area.index', compact('area'));
    }

    public function create()
    {
        return view('admin::master.area.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_area' => 'required|max:10|alpha_num|unique:area,id_area',
            'nm_area' => 'required|max:50',
        ]);

        DB::table('area')->insert([
            'id_area' => strtoupper($request->id_area),
            'nm_area' => $request->nm_area,
            'id_divisi' => $this->divisi,
            'status_aktif' => 'Aktif',
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.area.index')->with('success', 'Data area berhasil disimpan.');
    }

    public function edit($id)
    {
        $area = DB::table('area')->where('id_area', $id)->where('id_divisi', $this->divisi)->first();
        if (! $area) {
            abort(404);
        }

        return view('admin::master.area.edit', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_area' => 'required|max:50',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',
        ]);

        DB::table('area')->where('id_area', $id)->where('id_divisi', $this->divisi)->update([
            'nm_area' => $request->nm_area,
            'status_aktif' => $request->status_aktif,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.area.index')->with('success', 'Data area berhasil diubah.');
    }

    // Master tidak dihapus, cukup dinonaktifkan
    public function destroy($id)
    {
        DB::table('area')->where('id_area', $id)->where('id_divisi', $this->divisi)->update([
            'status_aktif' => 'Non-Aktif',
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.area.index')->with('success', 'Data area berhasil dinonaktifkan.');
    }
}
