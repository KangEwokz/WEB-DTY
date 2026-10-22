<?php

namespace Modules\Qct\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC12 Cek Visual (operator area Visual) dan UC13 Cek Denier (operator area Lab), satu baris per troly.
// Tanggal, shift, troly, dan merge diambil dari tabel doffing (join), tidak disimpan ulang.
class CheckBatchController extends Controller
{
    // Daftar troly hasil doffing beserta status pengecekannya
    public function index(Request $request)
    {
        $query = DB::table('doffing')
            ->join('benang', 'doffing.merge', '=', 'benang.merge')
            ->leftJoin('check_batch', 'doffing.id_doffing', '=', 'check_batch.id_doffing')
            ->select(
                'doffing.*',
                'benang.denier',
                'benang.filamen',
                'benang.warna_ppt',
                'check_batch.id_check',
                'check_batch.hasil_visual',
                'check_batch.hasil_denier',
                'check_batch.id_grade',
                'check_batch.status',
                DB::raw('(SELECT COUNT(*) FROM abnormal WHERE abnormal.id_doffing = doffing.id_doffing) as jml_abnormal')
            );

        if ($request->tanggal) {
            $query->where('doffing.tgl_doffing', $request->tanggal);
        }
        if ($request->shift) {
            $query->where('doffing.shift', $request->shift);
        }
        if ($request->tahap == 'belum') {
            $query->whereNull('check_batch.id_check');
        } elseif ($request->tahap == 'lab') {
            $query->whereNotNull('check_batch.id_check')->whereNull('check_batch.hasil_denier');
        } elseif ($request->tahap == 'selesai') {
            $query->whereNotNull('check_batch.hasil_denier');
        }

        $troly = $query->orderBy('doffing.id_doffing', 'desc')->paginate(10)->withQueryString();
        $area = area_aktif();

        return view('qct::transaksi.check-batch.index', compact('troly', 'area'));
    }

    // UC12: form cek visual untuk satu troly
    public function create(Request $request)
    {
        $doffing = DB::table('doffing')
            ->join('benang', 'doffing.merge', '=', 'benang.merge')
            ->where('doffing.id_doffing', $request->id_doffing)
            ->select('doffing.*', 'benang.denier', 'benang.filamen', 'benang.warna_ppt')
            ->first();
        if (! $doffing) {
            return redirect()->route('qct.check-batch.index')->with('error', 'Pilih troly yang akan dicek dari daftar.');
        }
        if (DB::table('check_batch')->where('id_doffing', $doffing->id_doffing)->exists()) {
            return redirect()->route('qct.check-batch.index')->with('error', 'Troly ini sudah dicek visual.');
        }

        return view('qct::transaksi.check-batch.create', compact('doffing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_doffing' => 'required|exists:doffing,id_doffing|unique:check_batch,id_doffing',
            'hasil_visual' => 'required|in:OK,NG',
        ]);

        $doffing = DB::table('doffing')->where('id_doffing', $request->id_doffing)->first();
        if (sudah_terkunci($doffing->tgl_doffing, $doffing->shift)) {
            return redirect()->route('qct.check-batch.index')->with('error', 'Laporan tanggal dan shift ini sudah diverifikasi HOD.');
        }

        $id_check = buat_kode('BTH', $doffing->tgl_doffing, $doffing->shift);
        DB::table('check_batch')->insert([
            'id_check' => $id_check,
            'id_doffing' => $doffing->id_doffing,
            'hasil_visual' => $request->hasil_visual,
            'insp_visual' => auth()->user()->id_karyawan,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pesan = 'Hasil cek visual berhasil disimpan.';
        if ($request->hasil_visual == 'NG') {
            $pesan .= ' Tambahkan data bobbin abnormal troly ini.';
        }

        return redirect()->route('qct.check-batch.show', $id_check)->with('success', $pesan);
    }

    // Detail troly + daftar bobbin abnormalnya
    public function show($id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }

        $abnormal = DB::table('abnormal')
            ->join('jenis_abnormal', 'abnormal.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->where('abnormal.id_doffing', $check->id_doffing)
            ->select('abnormal.*', 'jenis_abnormal.nm_abnormal')
            ->orderBy('abnormal.id_abnormal')
            ->get();

        // Bobbin lolos tidak dicatat satu per satu: jumlah bobbin - jumlah abnormal
        $jml_lolos = $check->jml_bobbin - count($abnormal);
        $terkunci = sudah_terkunci($check->tgl_doffing, $check->shift);
        $area = area_aktif();

        return view('qct::transaksi.check-batch.show', compact('check', 'abnormal', 'jml_lolos', 'terkunci', 'area'));
    }

    // UC12: ubah hasil visual
    public function edit($id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }
        if (sudah_terkunci($check->tgl_doffing, $check->shift)) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }

        return view('qct::transaksi.check-batch.edit', compact('check'));
    }

    public function update(Request $request, $id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }
        if (sudah_terkunci($check->tgl_doffing, $check->shift)) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }

        $request->validate([
            'hasil_visual' => 'required|in:OK,NG',
        ]);

        DB::table('check_batch')->where('id_check', $id)->update([
            'hasil_visual' => $request->hasil_visual,
            'insp_visual' => auth()->user()->id_karyawan,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.check-batch.show', $id)->with('success', 'Hasil cek visual berhasil diubah.');
    }

    public function destroy($id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }
        if (sudah_terkunci($check->tgl_doffing, $check->shift)) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Data sudah diverifikasi HOD, tidak bisa dihapus.');
        }
        if (DB::table('abnormal')->where('id_doffing', $check->id_doffing)->exists()) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Hapus dulu data abnormal troly ini.');
        }

        DB::table('check_batch')->where('id_check', $id)->delete();

        return redirect()->route('qct.check-batch.index')->with('success', 'Data cek troly berhasil dihapus.');
    }

    // UC13: form cek denier (operator Lab)
    public function editDenier($id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }
        if (sudah_terkunci($check->tgl_doffing, $check->shift)) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }
        $grade = DB::table('grade')->where('status_aktif', 'Aktif')->orderBy('id_grade')->get();

        return view('qct::transaksi.check-batch.denier', compact('check', 'grade'));
    }

    public function updateDenier(Request $request, $id)
    {
        $check = $this->ambil($id);
        if (! $check) {
            abort(404);
        }
        if (sudah_terkunci($check->tgl_doffing, $check->shift)) {
            return redirect()->route('qct.check-batch.show', $id)->with('error', 'Data sudah diverifikasi HOD, tidak bisa diubah.');
        }

        $request->validate([
            'hasil_denier' => 'required|in:OK,NG',
            'id_grade' => 'required|exists:grade,id_grade,status_aktif,Aktif',
            'status' => 'required|in:Packing,Hold',
        ]);

        DB::table('check_batch')->where('id_check', $id)->update([
            'hasil_denier' => $request->hasil_denier,
            'id_grade' => $request->id_grade,
            'status' => $request->status,
            'insp_lab' => auth()->user()->id_karyawan,
            'updated_by' => auth()->user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.check-batch.show', $id)->with('success', 'Hasil cek denier berhasil disimpan.');
    }

    // Satu baris check_batch lengkap dengan data troly dari doffing dan nama inspektor
    private function ambil($id)
    {
        return DB::table('check_batch')
            ->join('doffing', 'check_batch.id_doffing', '=', 'doffing.id_doffing')
            ->join('benang', 'doffing.merge', '=', 'benang.merge')
            ->leftJoin('karyawan as kv', 'check_batch.insp_visual', '=', 'kv.id_karyawan')
            ->leftJoin('karyawan as kl', 'check_batch.insp_lab', '=', 'kl.id_karyawan')
            ->where('check_batch.id_check', $id)
            ->select(
                'check_batch.*',
                'doffing.tgl_doffing',
                'doffing.shift',
                'doffing.nmr_troly',
                'doffing.merge',
                'benang.denier',
                'benang.filamen',
                'benang.warna_ppt',
                'doffing.jml_bobbin',
                'kv.nama_karyawan as nama_visual',
                'kl.nama_karyawan as nama_lab'
            )
            ->first();
    }
}
