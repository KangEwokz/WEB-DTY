<?php

namespace Modules\Produksi\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC06 Kelola Jadwal Produksi
class JadwalProduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('jadwal_produksi')
            ->join('benang', 'jadwal_produksi.merge', '=', 'benang.merge')
            ->select('jadwal_produksi.*', 'benang.denier', 'benang.filamen', 'benang.warna_ppt');
        if ($request->tanggal) {
            $query->where('jadwal_produksi.tgl_produksi', $request->tanggal);
        }
        if ($request->line) {
            $query->where('jadwal_produksi.line', $request->line);
        }
        $jadwal = $query->orderBy('jadwal_produksi.tgl_produksi', 'desc')
            ->orderBy('jadwal_produksi.line')
            ->paginate(10)->withQueryString();

        return view('produksi::transaksi.jadwal-produksi.index', compact('jadwal'));
    }

    public function create()
    {
        $benang = DB::table('benang')->where('status_aktif', 'Aktif')->orderBy('merge')->get();

        return view('produksi::transaksi.jadwal-produksi.create', compact('benang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'merge' => 'required|exists:benang,merge,status_aktif,Aktif',
            'tgl_produksi' => 'required|date',
            'line' => 'required|in:401,402,403',
            'pos_awal' => 'required|integer|between:1,10',
            'pos_akhir' => 'required|integer|between:1,10|gte:pos_awal',
            'customer' => 'nullable|max:100',
        ]);

        DB::table('jadwal_produksi')->insert([
            'id_jadwal' => buat_kode('JDW', $request->tgl_produksi),
            'merge' => $request->merge,
            'tgl_produksi' => $request->tgl_produksi,
            'line' => $request->line,
            'pos_awal' => $request->pos_awal,
            'pos_akhir' => $request->pos_akhir,
            'customer' => $request->customer,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.jadwal-produksi.index')->with('success', 'Jadwal produksi berhasil disimpan.');
    }

    public function edit($id)
    {
        $jadwal = DB::table('jadwal_produksi')->where('id_jadwal', $id)->first();
        if (! $jadwal) {
            abort(404);
        }
        $benang = DB::table('benang')->where('status_aktif', 'Aktif')->orderBy('merge')->get();

        return view('produksi::transaksi.jadwal-produksi.edit', compact('jadwal', 'benang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'merge' => 'required|exists:benang,merge,status_aktif,Aktif',
            'tgl_produksi' => 'required|date',
            'line' => 'required|in:401,402,403',
            'pos_awal' => 'required|integer|between:1,10',
            'pos_akhir' => 'required|integer|between:1,10|gte:pos_awal',
            'customer' => 'nullable|max:100',
        ]);

        DB::table('jadwal_produksi')->where('id_jadwal', $id)->update([
            'merge' => $request->merge,
            'tgl_produksi' => $request->tgl_produksi,
            'line' => $request->line,
            'pos_awal' => $request->pos_awal,
            'pos_akhir' => $request->pos_akhir,
            'customer' => $request->customer,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.jadwal-produksi.index')->with('success', 'Jadwal produksi berhasil diubah.');
    }

    public function destroy($id)
    {
        DB::table('jadwal_produksi')->where('id_jadwal', $id)->delete();

        return redirect()->route('produksi.jadwal-produksi.index')->with('success', 'Jadwal produksi berhasil dihapus.');
    }
}
