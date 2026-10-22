<?php

namespace Modules\Qct\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC08: master jenis abnormal (Handling, OTY, Hairy, ...)
class JenisAbnormalController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('jenis_abnormal');
        if ($request->cari) {
            $query->where('nm_abnormal', 'like', '%'.$request->cari.'%');
        }
        $jenis = $query->orderBy('id_jns_abn')->paginate(10)->withQueryString();

        return view('qct::master.jenis-abnormal.index', compact('jenis'));
    }

    public function create()
    {
        return view('qct::master.jenis-abnormal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_abnormal' => 'required|max:50|unique:jenis_abnormal,nm_abnormal',
        ]);

        DB::table('jenis_abnormal')->insert([
            'id_jns_abn' => buat_kode('VS'),
            'nm_abnormal' => $request->nm_abnormal,
            'status_aktif' => 'Aktif',
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.jenis-abnormal.index')->with('success', 'Data jenis abnormal berhasil disimpan.');
    }

    public function edit($id)
    {
        $jenis = DB::table('jenis_abnormal')->where('id_jns_abn', $id)->first();
        if (! $jenis) {
            abort(404);
        }

        return view('qct::master.jenis-abnormal.edit', compact('jenis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_abnormal' => 'required|max:50|unique:jenis_abnormal,nm_abnormal,'.$id.',id_jns_abn',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',
        ]);

        DB::table('jenis_abnormal')->where('id_jns_abn', $id)->update([
            'nm_abnormal' => $request->nm_abnormal,
            'status_aktif' => $request->status_aktif,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.jenis-abnormal.index')->with('success', 'Data jenis abnormal berhasil diubah.');
    }

    // Master tidak dihapus, cukup dinonaktifkan
    public function destroy($id)
    {
        DB::table('jenis_abnormal')->where('id_jns_abn', $id)->update([
            'status_aktif' => 'Non-Aktif',
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.jenis-abnormal.index')->with('success', 'Data jenis abnormal berhasil dinonaktifkan.');
    }
}
