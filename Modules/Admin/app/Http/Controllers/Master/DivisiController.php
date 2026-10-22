<?php

namespace Modules\Admin\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC03: data divisi. Tidak ada hapus karena tabel divisi tidak punya kolom status.
class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('divisi');
        if ($request->cari) {
            $query->where('nm_divisi', 'like', '%'.$request->cari.'%');
        }
        $divisi = $query->orderBy('id_divisi')->paginate(10)->withQueryString();

        return view('admin::master.divisi.index', compact('divisi'));
    }

    public function create()
    {
        return view('admin::master.divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_divisi' => 'required|max:5|unique:divisi,kd_divisi',
            'nm_divisi' => 'required|max:50',
        ]);

        DB::table('divisi')->insert([
            'id_divisi' => buat_kode('DIV'),
            'kd_divisi' => strtoupper($request->kd_divisi),
            'nm_divisi' => $request->nm_divisi,
        ]);

        return redirect()->route('admin.divisi.index')->with('success', 'Data divisi berhasil disimpan.');
    }

    public function edit($id)
    {
        $divisi = DB::table('divisi')->where('id_divisi', $id)->first();
        if (! $divisi) {
            abort(404);
        }

        return view('admin::master.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_divisi' => 'required|max:5|unique:divisi,kd_divisi,'.$id.',id_divisi',
            'nm_divisi' => 'required|max:50',
        ]);

        DB::table('divisi')->where('id_divisi', $id)->update([
            'kd_divisi' => strtoupper($request->kd_divisi),
            'nm_divisi' => $request->nm_divisi,
        ]);

        return redirect()->route('admin.divisi.index')->with('success', 'Data divisi berhasil diubah.');
    }
}
