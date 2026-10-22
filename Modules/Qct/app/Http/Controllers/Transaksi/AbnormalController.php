<?php

namespace Modules\Qct\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC14 Kelola Data Abnormal Benang. Satu baris = satu bobbin abnormal.
// Bagian visual (troly, mesin asal, jenis) diisi operator Visual dengan status Pending,
// lalu hasil uji lab (denier, elong, tenacity, DTI, grade, Packing/Cutting) diisi operator Lab.
class AbnormalController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('abnormal')
            ->join('doffing', 'abnormal.id_doffing', '=', 'doffing.id_doffing')
            ->join('jenis_abnormal', 'abnormal.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->select('abnormal.*', 'doffing.tgl_doffing', 'doffing.shift', 'doffing.nmr_troly', 'doffing.merge', 'jenis_abnormal.nm_abnormal');
        if ($request->tanggal) {
            $query->where('doffing.tgl_doffing', $request->tanggal);
        }
        if ($request->shift) {
            $query->where('doffing.shift', $request->shift);
        }
        if ($request->status) {
            $query->where('abnormal.status', $request->status);
        }
        $abnormal = $query->orderBy('abnormal.id_abnormal', 'desc')->paginate(10)->withQueryString();
        $area = area_aktif();

        return view('qct::transaksi.abnormal.index', compact('abnormal', 'area'));
    }

    // Bagian visual
    public function create(Request $request)
    {
        $troly = $this->trolySudahDicek();
        $mesin = DB::table('mesin')->orderBy('kode_mesin')->get();
        $jenis = DB::table('jenis_abnormal')->where('status_aktif', 'Aktif')->orderBy('id_jns_abn')->get();
        $pilih = $request->id_doffing;   // troly terpilih kalau datang dari halaman detail troly

        return view('qct::transaksi.abnormal.create', compact('troly', 'mesin', 'jenis', 'pilih'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_doffing' => 'required|exists:check_batch,id_doffing',   // troly harus sudah dicek visual
            'kode_mesin' => 'required|exists:mesin,kode_mesin',
            'id_jns_abn' => 'required|exists:jenis_abnormal,id_jns_abn,status_aktif,Aktif',
        ]);

        $doffing = DB::table('doffing')->where('id_doffing', $request->id_doffing)->first();
        if (sudah_terkunci($doffing->tgl_doffing, $doffing->shift)) {
            return back()->withInput()->with('error', 'Laporan tanggal dan shift ini sudah diverifikasi HOD.');
        }

        // Jumlah bobbin abnormal tidak boleh melebihi jumlah bobbin dalam troly
        $jumlah = DB::table('abnormal')->where('id_doffing', $doffing->id_doffing)->count();
        if ($jumlah >= $doffing->jml_bobbin) {
            return back()->withInput()->with('error', 'Jumlah bobbin abnormal sudah sama dengan jumlah bobbin troly.');
        }

        DB::table('abnormal')->insert([
            'id_abnormal' => buat_kode('ABN', $doffing->tgl_doffing, $doffing->shift),
            'id_doffing' => $doffing->id_doffing,
            'kode_mesin' => $request->kode_mesin,
            'id_jns_abn' => $request->id_jns_abn,
            'status' => 'Pending',
            'insp_visual' => auth()->user()->id_karyawan,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id_check = DB::table('check_batch')->where('id_doffing', $doffing->id_doffing)->value('id_check');

        return redirect()->route('qct.check-batch.show', $id_check)->with('success', 'Data bobbin abnormal berhasil disimpan.');
    }

    public function edit($id)
    {
        $abnormal = $this->ambil($id);
        if (! $abnormal) {
            abort(404);
        }
        $pesan = $this->tidakBolehDiubah($abnormal);
        if ($pesan) {
            return redirect()->route('qct.abnormal.index')->with('error', $pesan);
        }
        $mesin = DB::table('mesin')->orderBy('kode_mesin')->get();
        $jenis = DB::table('jenis_abnormal')->where('status_aktif', 'Aktif')->orderBy('id_jns_abn')->get();

        return view('qct::transaksi.abnormal.edit', compact('abnormal', 'mesin', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        $abnormal = $this->ambil($id);
        if (! $abnormal) {
            abort(404);
        }
        $pesan = $this->tidakBolehDiubah($abnormal);
        if ($pesan) {
            return redirect()->route('qct.abnormal.index')->with('error', $pesan);
        }

        $request->validate([
            'kode_mesin' => 'required|exists:mesin,kode_mesin',
            'id_jns_abn' => 'required|exists:jenis_abnormal,id_jns_abn,status_aktif,Aktif',
        ]);

        DB::table('abnormal')->where('id_abnormal', $id)->update([
            'kode_mesin' => $request->kode_mesin,
            'id_jns_abn' => $request->id_jns_abn,
            'insp_visual' => auth()->user()->id_karyawan,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.abnormal.index')->with('success', 'Data abnormal berhasil diubah.');
    }

    public function destroy($id)
    {
        $abnormal = $this->ambil($id);
        if (! $abnormal) {
            abort(404);
        }
        $pesan = $this->tidakBolehDiubah($abnormal);
        if ($pesan) {
            return redirect()->route('qct.abnormal.index')->with('error', $pesan);
        }

        DB::table('abnormal')->where('id_abnormal', $id)->delete();

        return redirect()->route('qct.abnormal.index')->with('success', 'Data abnormal berhasil dihapus.');
    }

    // Bagian lab: hasil uji bobbin abnormal
    public function editLab($id)
    {
        $abnormal = $this->ambil($id);
        if (! $abnormal) {
            abort(404);
        }
        if (sudah_terkunci($abnormal->tgl_doffing, $abnormal->shift)) {
            return redirect()->route('qct.abnormal.index')->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }
        $grade = DB::table('grade')->where('status_aktif', 'Aktif')->orderBy('id_grade')->get();

        return view('qct::transaksi.abnormal.lab', compact('abnormal', 'grade'));
    }

    public function updateLab(Request $request, $id)
    {
        $abnormal = $this->ambil($id);
        if (! $abnormal) {
            abort(404);
        }
        if (sudah_terkunci($abnormal->tgl_doffing, $abnormal->shift)) {
            return redirect()->route('qct.abnormal.index')->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }

        $request->validate([
            'act_denier' => 'required|numeric|between:0,9999.99',
            'elong' => 'required|numeric|between:0,999.99',
            'tenacity' => 'required|numeric|between:0,999.99',
            'dti' => 'required|numeric|between:0,999.99',
            'id_grade' => 'required|exists:grade,id_grade,status_aktif,Aktif',
            'status' => 'required|in:Packing,Cutting',
        ]);

        DB::table('abnormal')->where('id_abnormal', $id)->update([
            'act_denier' => $request->act_denier,
            'elong' => $request->elong,
            'tenacity' => $request->tenacity,
            'dti' => $request->dti,
            'id_grade' => $request->id_grade,
            'status' => $request->status,
            'insp_lab' => auth()->user()->id_karyawan,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.abnormal.index')->with('success', 'Hasil uji lab abnormal berhasil disimpan.');
    }

    // Bagian visual hanya boleh diubah/dihapus selama masih Pending dan belum dikunci
    private function tidakBolehDiubah($abnormal)
    {
        if (sudah_terkunci($abnormal->tgl_doffing, $abnormal->shift)) {
            return 'Data sudah diverifikasi HOD, tidak bisa diubah.';
        }
        if ($abnormal->status != 'Pending') {
            return 'Bobbin ini sudah diuji Lab, data visual tidak bisa diubah.';
        }

        return null;
    }

    private function ambil($id)
    {
        return DB::table('abnormal')
            ->join('doffing', 'abnormal.id_doffing', '=', 'doffing.id_doffing')
            ->join('jenis_abnormal', 'abnormal.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->where('abnormal.id_abnormal', $id)
            ->select('abnormal.*', 'doffing.tgl_doffing', 'doffing.shift', 'doffing.nmr_troly', 'doffing.merge', 'jenis_abnormal.nm_abnormal')
            ->first();
    }

    // Troly yang sudah dicek visual (untuk pilihan di form), terbaru di atas
    private function trolySudahDicek()
    {
        return DB::table('check_batch')
            ->join('doffing', 'check_batch.id_doffing', '=', 'doffing.id_doffing')
            ->select('doffing.id_doffing', 'doffing.tgl_doffing', 'doffing.shift', 'doffing.nmr_troly', 'doffing.merge')
            ->orderBy('doffing.id_doffing', 'desc')
            ->limit(100)
            ->get();
    }
}
