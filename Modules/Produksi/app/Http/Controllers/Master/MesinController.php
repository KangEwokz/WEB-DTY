<?php

namespace Modules\Produksi\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC06: master mesin. Mesin sudah lengkap (3 line x 10 posisi), yang diubah hanya statusnya.
class MesinController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('mesin');
        if ($request->line) {
            $query->where('line', $request->line);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $mesin = $query->orderBy('kode_mesin')->paginate(10)->withQueryString();

        return view('produksi::master.mesin.index', compact('mesin'));
    }

    public function edit($id)
    {
        $mesin = DB::table('mesin')->where('kode_mesin', $id)->first();
        if (! $mesin) {
            abort(404);
        }

        return view('produksi::master.mesin.edit', compact('mesin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Running,Stop,Maintenance',
        ]);

        DB::table('mesin')->where('kode_mesin', $id)->update([
            'status' => $request->status,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.mesin.index')->with('success', 'Status mesin berhasil diubah.');
    }
}
