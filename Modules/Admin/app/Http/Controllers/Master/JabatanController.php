<?php

namespace Modules\Admin\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC03: data jabatan. Tidak ada hapus karena tabel jabatan tidak punya kolom status.
class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('jabatan')
            ->leftJoin('divisi', 'jabatan.id_divisi', '=', 'divisi.id_divisi')
            ->select('jabatan.*', 'divisi.nm_divisi');
        if ($request->cari) {
            $query->where('jabatan.nm_jabatan', 'like', '%'.$request->cari.'%');
        }
        $jabatan = $query->orderBy('jabatan.id_jabatan')->paginate(10)->withQueryString();

        return view('admin::master.jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();

        return view('admin::master.jabatan.create', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_jabatan' => 'required|max:30',
            'id_divisi' => 'nullable|exists:divisi,id_divisi',
        ]);

        DB::table('jabatan')->insert([
            'id_jabatan' => buat_kode('JAB'),
            'nm_jabatan' => $request->nm_jabatan,
            'id_divisi' => $request->id_divisi,
        ]);

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil disimpan.');
    }

    public function edit($id)
    {
        $jabatan = DB::table('jabatan')->where('id_jabatan', $id)->first();
        if (! $jabatan) {
            abort(404);
        }
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();

        return view('admin::master.jabatan.edit', compact('jabatan', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_jabatan' => 'required|max:30',
            'id_divisi' => 'nullable|exists:divisi,id_divisi',
        ]);

        DB::table('jabatan')->where('id_jabatan', $id)->update([
            'nm_jabatan' => $request->nm_jabatan,
            'id_divisi' => $request->id_divisi,
        ]);

        return redirect()->route('admin.jabatan.index')->with('success', 'Data jabatan berhasil diubah.');
    }
}
