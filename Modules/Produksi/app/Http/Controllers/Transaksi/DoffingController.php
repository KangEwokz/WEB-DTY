<?php

namespace Modules\Produksi\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC07 Kelola Data Doffing (per troly)
class DoffingController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('doffing')
            ->join('benang', 'doffing.merge', '=', 'benang.merge')
            ->leftJoin('check_batch', 'doffing.id_doffing', '=', 'check_batch.id_doffing')
            ->select('doffing.*', 'benang.denier', 'benang.filamen', 'benang.warna_ppt', 'check_batch.id_check');
        if ($request->tanggal) {
            $query->where('doffing.tgl_doffing', $request->tanggal);
        }
        if ($request->shift) {
            $query->where('doffing.shift', $request->shift);
        }
        $doffing = $query->orderBy('doffing.id_doffing', 'desc')->paginate(10)->withQueryString();

        return view('produksi::transaksi.doffing.index', compact('doffing'));
    }

    public function create()
    {
        $benang = DB::table('benang')->where('status_aktif', 'Aktif')->orderBy('merge')->get();
        $sekarang = shift_sekarang();   // nilai awal tanggal & shift di form

        return view('produksi::transaksi.doffing.create', compact('benang', 'sekarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_doffing' => 'required|date',
            'shift' => 'required|in:S1,S2,S3',
            'nmr_troly' => 'required|max:10',
            'merge' => 'required|exists:benang,merge,status_aktif,Aktif',
            'jml_bobbin' => 'required|integer|min:1',
            'berat_troly' => 'nullable|numeric|between:0,9999.99',
        ]);

        DB::table('doffing')->insert([
            'id_doffing' => buat_kode('DOF', $request->tgl_doffing, $request->shift),
            'tgl_doffing' => $request->tgl_doffing,
            'shift' => $request->shift,
            'nmr_troly' => $request->nmr_troly,
            'merge' => $request->merge,
            'jml_bobbin' => $request->jml_bobbin,
            'berat_troly' => $request->berat_troly,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.doffing.index')->with('success', 'Data doffing berhasil disimpan.');
    }

    public function edit($id)
    {
        $doffing = DB::table('doffing')->where('id_doffing', $id)->first();
        if (! $doffing) {
            abort(404);
        }
        if ($this->sudahDicek($id)) {
            return redirect()->route('produksi.doffing.index')->with('error', 'Troly sudah dicek QCT, data doffing tidak bisa diubah.');
        }
        $benang = DB::table('benang')->where('status_aktif', 'Aktif')->orderBy('merge')->get();

        return view('produksi::transaksi.doffing.edit', compact('doffing', 'benang'));
    }

    public function update(Request $request, $id)
    {
        if ($this->sudahDicek($id)) {
            return redirect()->route('produksi.doffing.index')->with('error', 'Troly sudah dicek QCT, data doffing tidak bisa diubah.');
        }

        // Tanggal dan shift tidak diubah karena menjadi bagian dari ID doffing
        $request->validate([
            'nmr_troly' => 'required|max:10',
            'merge' => 'required|exists:benang,merge,status_aktif,Aktif',
            'jml_bobbin' => 'required|integer|min:1',
            'berat_troly' => 'nullable|numeric|between:0,9999.99',
        ]);

        DB::table('doffing')->where('id_doffing', $id)->update([
            'nmr_troly' => $request->nmr_troly,
            'merge' => $request->merge,
            'jml_bobbin' => $request->jml_bobbin,
            'berat_troly' => $request->berat_troly,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.doffing.index')->with('success', 'Data doffing berhasil diubah.');
    }

    public function destroy($id)
    {
        if ($this->sudahDicek($id)) {
            return redirect()->route('produksi.doffing.index')->with('error', 'Troly sudah dicek QCT, data doffing tidak bisa dihapus.');
        }

        DB::table('doffing')->where('id_doffing', $id)->delete();

        return redirect()->route('produksi.doffing.index')->with('success', 'Data doffing berhasil dihapus.');
    }

    // Troly yang sudah masuk pengecekan QCT tidak boleh diubah/dihapus produksi
    private function sudahDicek($id)
    {
        return DB::table('check_batch')->where('id_doffing', $id)->exists();
    }
}
