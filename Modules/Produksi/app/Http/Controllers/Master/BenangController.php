<?php

namespace Modules\Produksi\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC06: master benang (merge)
class BenangController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('benang');
        if ($request->cari) {
            $query->where('merge', 'like', '%'.$request->cari.'%');
        }
        if ($request->status_aktif) {
            $query->where('status_aktif', $request->status_aktif);
        }
        $benang = $query->orderBy('merge')->paginate(10)->withQueryString();

        return view('produksi::master.benang.index', compact('benang'));
    }

    public function create()
    {
        return view('produksi::master.benang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'merge' => 'required|max:10|alpha_dash|unique:benang,merge',
            'denier' => 'required|integer|min:1',
            'filamen' => 'required|integer|min:1',
            'warna_ppt' => 'nullable|max:20',
        ]);

        DB::table('benang')->insert([
            'merge' => strtoupper($request->merge),
            'denier' => $request->denier,
            'filamen' => $request->filamen,
            'warna_ppt' => $request->warna_ppt,
            'status_aktif' => 'Aktif',
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.benang.index')->with('success', 'Data benang berhasil disimpan.');
    }

    public function edit($id)
    {
        $benang = DB::table('benang')->where('merge', $id)->first();
        if (! $benang) {
            abort(404);
        }

        return view('produksi::master.benang.edit', compact('benang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'denier' => 'required|integer|min:1',
            'filamen' => 'required|integer|min:1',
            'warna_ppt' => 'nullable|max:20',
            'status_aktif' => 'required|in:Aktif,Non-Aktif',
        ]);

        DB::table('benang')->where('merge', $id)->update([
            'denier' => $request->denier,
            'filamen' => $request->filamen,
            'warna_ppt' => $request->warna_ppt,
            'status_aktif' => $request->status_aktif,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.benang.index')->with('success', 'Data benang berhasil diubah.');
    }

    // Master tidak dihapus, cukup dinonaktifkan
    public function destroy($id)
    {
        DB::table('benang')->where('merge', $id)->update([
            'status_aktif' => 'Non-Aktif',
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.benang.index')->with('success', 'Data benang berhasil dinonaktifkan.');
    }
}
