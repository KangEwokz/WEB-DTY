<?php

namespace Modules\Qct\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC08: master grade. Kriteria grade masih dikonfirmasi ke pabrik, jadi grade dipilih manual.
class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('grade');
        if ($request->cari) {
            $query->where('id_grade', 'like', '%'.$request->cari.'%');
        }
        $grade = $query->orderBy('id_grade')->paginate(10)->withQueryString();

        return view('qct::master.grade.index', compact('grade'));
    }

    public function create()
    {
        return view('qct::master.grade.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_grade' => 'required|max:5|alpha_num|unique:grade,id_grade',
            'keterangan' => 'nullable|max:100',
        ]);

        DB::table('grade')->insert([
            'id_grade' => strtoupper($request->id_grade),
            'keterangan' => $request->keterangan,
            'status_aktif' => 'Aktif',
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.grade.index')->with('success', 'Data grade berhasil disimpan.');
    }

    public function edit($id)
    {
        $grade = DB::table('grade')->where('id_grade', $id)->first();
        if (! $grade) {
            abort(404);
        }

        return view('qct::master.grade.edit', compact('grade'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|max:100',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',
        ]);

        DB::table('grade')->where('id_grade', $id)->update([
            'keterangan' => $request->keterangan,
            'status_aktif' => $request->status_aktif,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.grade.index')->with('success', 'Data grade berhasil diubah.');
    }

    // Master tidak dihapus, cukup dinonaktifkan
    public function destroy($id)
    {
        DB::table('grade')->where('id_grade', $id)->update([
            'status_aktif' => 'Non-Aktif',
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.grade.index')->with('success', 'Data grade berhasil dinonaktifkan.');
    }
}
