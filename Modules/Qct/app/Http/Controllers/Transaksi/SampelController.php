<?php

namespace Modules\Qct\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC15 Kelola Data Sampel: satu sampel per merge per tanggal + shift. Jenis abnormal kosong = sampel OK.
class SampelController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('sampel')
            ->join('benang', 'sampel.merge', '=', 'benang.merge')
            ->leftJoin('jenis_abnormal', 'sampel.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->select('sampel.*', 'benang.denier', 'benang.filamen', 'benang.warna_ppt', 'jenis_abnormal.nm_abnormal');
        if ($request->tanggal) {
            $query->where('sampel.tgl_sampel', $request->tanggal);
        }
        if ($request->shift) {
            $query->where('sampel.shift', $request->shift);
        }
        if ($request->merge) {
            $query->where('sampel.merge', $request->merge);
        }
        $sampel = $query->orderBy('sampel.id_sampel', 'desc')->paginate(10)->withQueryString();
        $benang = DB::table('benang')->orderBy('merge')->get();

        return view('qct::transaksi.sampel.index', compact('sampel', 'benang'));
    }

    public function create()
    {
        $benang = DB::table('benang')->where('status_aktif', 'Aktif')->orderBy('merge')->get();
        $jenis = DB::table('jenis_abnormal')->where('status_aktif', 'Aktif')->orderBy('id_jns_abn')->get();
        $grade = DB::table('grade')->where('status_aktif', 'Aktif')->orderBy('id_grade')->get();
        $sekarang = shift_sekarang();

        return view('qct::transaksi.sampel.create', compact('benang', 'jenis', 'grade', 'sekarang'));
    }

    public function store(Request $request)
    {
        $request->validate($this->aturan() + [
            'merge' => 'required|exists:benang,merge,status_aktif,Aktif',
            'tgl_sampel' => 'required|date',
            'shift' => 'required|in:S1,S2,S3',
        ]);

        if (sudah_terkunci($request->tgl_sampel, $request->shift)) {
            return back()->withInput()->with('error', 'Laporan tanggal dan shift ini sudah diverifikasi HOD.');
        }

        $sudah_ada = DB::table('sampel')
            ->where('merge', $request->merge)
            ->where('tgl_sampel', $request->tgl_sampel)
            ->where('shift', $request->shift)
            ->exists();
        if ($sudah_ada) {
            return back()->withInput()->withErrors(['merge' => 'Sampel merge ini sudah diinput pada tanggal dan shift tersebut.']);
        }

        DB::table('sampel')->insert($this->isian($request) + [
            'id_sampel' => buat_kode('SMP', $request->tgl_sampel, $request->shift),
            'merge' => $request->merge,
            'tgl_sampel' => $request->tgl_sampel,
            'shift' => $request->shift,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.sampel.index')->with('success', 'Data sampel berhasil disimpan.');
    }

    public function edit($id)
    {
        $sampel = DB::table('sampel')->where('id_sampel', $id)->first();
        if (! $sampel) {
            abort(404);
        }
        if (sudah_terkunci($sampel->tgl_sampel, $sampel->shift)) {
            return redirect()->route('qct.sampel.index')->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }
        $jenis = DB::table('jenis_abnormal')->where('status_aktif', 'Aktif')->orderBy('id_jns_abn')->get();
        $grade = DB::table('grade')->where('status_aktif', 'Aktif')->orderBy('id_grade')->get();

        return view('qct::transaksi.sampel.edit', compact('sampel', 'jenis', 'grade'));
    }

    public function update(Request $request, $id)
    {
        $sampel = DB::table('sampel')->where('id_sampel', $id)->first();
        if (! $sampel) {
            abort(404);
        }
        if (sudah_terkunci($sampel->tgl_sampel, $sampel->shift)) {
            return redirect()->route('qct.sampel.index')->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }

        // Merge, tanggal, dan shift tidak diubah (bagian dari ID)
        $request->validate($this->aturan());

        DB::table('sampel')->where('id_sampel', $id)->update($this->isian($request) + [
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.sampel.index')->with('success', 'Data sampel berhasil diubah.');
    }

    public function destroy($id)
    {
        $sampel = DB::table('sampel')->where('id_sampel', $id)->first();
        if (! $sampel) {
            abort(404);
        }
        if (sudah_terkunci($sampel->tgl_sampel, $sampel->shift)) {
            return redirect()->route('qct.sampel.index')->with('error', 'Data sudah diverifikasi HOD, tidak bisa dihapus.');
        }

        DB::table('sampel')->where('id_sampel', $id)->delete();

        return redirect()->route('qct.sampel.index')->with('success', 'Data sampel berhasil dihapus.');
    }

    // Aturan validasi hasil uji (sama untuk tambah dan ubah)
    private function aturan()
    {
        return [
            'id_jns_abn' => 'nullable|exists:jenis_abnormal,id_jns_abn,status_aktif,Aktif',
            'act_denier' => 'nullable|numeric|between:0,9999.99',
            'elong' => 'nullable|numeric|between:0,999.99',
            'tenacity' => 'nullable|numeric|between:0,999.99',
            'dti' => 'nullable|numeric|between:0,999.99',
            'id_grade' => 'nullable|exists:grade,id_grade,status_aktif,Aktif',
        ];
    }

    // Kolom hasil uji + inspektor sesuai area kerja operator yang menginput
    private function isian(Request $request)
    {
        $data = [
            'id_jns_abn' => $request->id_jns_abn,
            'act_denier' => $request->act_denier,
            'elong' => $request->elong,
            'tenacity' => $request->tenacity,
            'dti' => $request->dti,
            'id_grade' => $request->id_grade,
        ];
        if (area_aktif() == 'VIS') {
            $data['insp_visual'] = auth()->user()->id_karyawan;
        } else {
            $data['insp_lab'] = auth()->user()->id_karyawan;
        }

        return $data;
    }
}
