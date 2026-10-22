<?php

namespace Modules\Qct\Http\Controllers\Output;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// UC11 Verifikasi Laporan Check QCT per tanggal + shift (HOD).
// Tabel laporan_check hanya menyimpan status verifikasi; angka laporan dihitung dengan query.
class LaporanCheckController extends Controller
{
    // Daftar tanggal + shift yang punya data doffing, beserta status laporannya
    public function index(Request $request)
    {
        $query = DB::table('doffing')
            ->leftJoin('laporan_check', function ($join) {
                $join->on('doffing.tgl_doffing', '=', 'laporan_check.tgl_laporan')
                    ->on('doffing.shift', '=', 'laporan_check.shift');
            })
            ->select(
                'doffing.tgl_doffing',
                'doffing.shift',
                'laporan_check.status',
                'laporan_check.waktu_verifikasi',
                DB::raw('COUNT(doffing.id_doffing) as jml_troly'),
                DB::raw('SUM(doffing.jml_bobbin) as jml_bobbin')
            )
            ->groupBy('doffing.tgl_doffing', 'doffing.shift', 'laporan_check.status', 'laporan_check.waktu_verifikasi');

        if ($request->bulan) {
            $query->whereYear('doffing.tgl_doffing', substr($request->bulan, 0, 4))
                ->whereMonth('doffing.tgl_doffing', substr($request->bulan, 5, 2));
        }
        if ($request->status == 'belum') {
            $query->whereNull('laporan_check.status');
        } elseif ($request->status == 'Terverifikasi') {
            $query->where('laporan_check.status', 'Terverifikasi');
        }

        $laporan = $query->orderBy('doffing.tgl_doffing', 'desc')->orderBy('doffing.shift')->paginate(10)->withQueryString();

        return view('qct::output.laporan-check.index', compact('laporan'));
    }

    // Isi laporan satu tanggal + shift. $id = LAP-YYYYMMDD-S1
    public function show($id)
    {
        $tanggal = $this->tanggal($id);
        $shift = $this->shift($id);
        $data = $this->hitung($tanggal, $shift);
        if (count($data['troly']) == 0) {
            abort(404);
        }

        $laporan = DB::table('laporan_check')
            ->leftJoin('karyawan', 'laporan_check.diverifikasi_oleh', '=', 'karyawan.id_karyawan')
            ->where('laporan_check.id_laporan', $id)
            ->select('laporan_check.*', 'karyawan.nama_karyawan as verifikator')
            ->first();

        return view('qct::output.laporan-check.show', $data + compact('id', 'tanggal', 'shift', 'laporan'));
    }

    // HOD memverifikasi laporan. Setelah ini check_batch, abnormal, dan sampel tanggal+shift tsb terkunci.
    public function verifikasi($id)
    {
        $tanggal = $this->tanggal($id);
        $shift = $this->shift($id);
        $data = $this->hitung($tanggal, $shift);
        if (count($data['troly']) == 0) {
            abort(404);
        }

        if (sudah_terkunci($tanggal, $shift)) {
            return redirect()->route('qct.laporan-check.show', $id)->with('error', 'Laporan ini sudah diverifikasi.');
        }
        if (count($data['kendala']) > 0) {
            return redirect()->route('qct.laporan-check.show', $id)
                ->with('error', 'Laporan belum bisa diverifikasi: '.implode(', ', $data['kendala']).'.');
        }

        DB::table('laporan_check')->insert([
            'id_laporan' => $id,
            'tgl_laporan' => $tanggal,
            'shift' => $shift,
            'status' => 'Terverifikasi',
            'diverifikasi_oleh' => auth()->user()->id_karyawan,
            'waktu_verifikasi' => now(),
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.laporan-check.show', $id)->with('success', 'Laporan berhasil diverifikasi. Data tanggal dan shift ini sekarang terkunci.');
    }

    // Semua angka laporan satu tanggal + shift
    private function hitung($tanggal, $shift)
    {
        // Troly + hasil cek + jumlah bobbin abnormal per troly
        $troly = DB::table('doffing')
            ->join('benang', 'doffing.merge', '=', 'benang.merge')
            ->leftJoin('check_batch', 'doffing.id_doffing', '=', 'check_batch.id_doffing')
            ->where('doffing.tgl_doffing', $tanggal)
            ->where('doffing.shift', $shift)
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
            )
            ->orderBy('doffing.id_doffing')
            ->get();

        $abnormal_jenis = DB::table('abnormal')
            ->join('doffing', 'abnormal.id_doffing', '=', 'doffing.id_doffing')
            ->join('jenis_abnormal', 'abnormal.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->where('doffing.tgl_doffing', $tanggal)
            ->where('doffing.shift', $shift)
            ->select(
                'jenis_abnormal.nm_abnormal',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw("SUM(CASE WHEN abnormal.status = 'Pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN abnormal.status = 'Packing' THEN 1 ELSE 0 END) as packing"),
                DB::raw("SUM(CASE WHEN abnormal.status = 'Cutting' THEN 1 ELSE 0 END) as cutting")
            )
            ->groupBy('jenis_abnormal.nm_abnormal')
            ->get();

        $sampel = DB::table('sampel')
            ->join('benang', 'sampel.merge', '=', 'benang.merge')
            ->leftJoin('jenis_abnormal', 'sampel.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->where('sampel.tgl_sampel', $tanggal)
            ->where('sampel.shift', $shift)
            ->select('sampel.*', 'benang.denier', 'benang.filamen', 'benang.warna_ppt', 'jenis_abnormal.nm_abnormal')
            ->orderBy('sampel.merge')
            ->get();

        // Ringkasan. Bobbin lolos = jumlah bobbin - jumlah bobbin abnormal.
        $ringkasan = [
            'troly' => count($troly),
            'bobbin' => $troly->sum('jml_bobbin'),
            'abnormal' => $troly->sum('jml_abnormal'),
            'lolos' => $troly->sum('jml_bobbin') - $troly->sum('jml_abnormal'),
            'visual_ok' => $troly->where('hasil_visual', 'OK')->count(),
            'visual_ng' => $troly->where('hasil_visual', 'NG')->count(),
            'denier_ok' => $troly->where('hasil_denier', 'OK')->count(),
            'denier_ng' => $troly->where('hasil_denier', 'NG')->count(),
            'packing' => $troly->where('status', 'Packing')->count(),
            'hold' => $troly->where('status', 'Hold')->count(),
            'sampel_ok' => $sampel->whereNull('id_jns_abn')->count(),
            'sampel_ng' => $sampel->whereNotNull('id_jns_abn')->count(),
        ];

        // Hal yang membuat laporan belum bisa diverifikasi
        $kendala = [];
        $belum_visual = $troly->whereNull('id_check')->count();
        $belum_denier = $troly->whereNotNull('id_check')->whereNull('hasil_denier')->count();
        $pending = $abnormal_jenis->sum('pending');
        if ($belum_visual > 0) {
            $kendala[] = $belum_visual.' troly belum dicek visual';
        }
        if ($belum_denier > 0) {
            $kendala[] = $belum_denier.' troly belum dicek denier';
        }
        if ($pending > 0) {
            $kendala[] = $pending.' bobbin abnormal masih Pending';
        }

        return compact('troly', 'abnormal_jenis', 'sampel', 'ringkasan', 'kendala');
    }

    // LAP-20261021-S1 -> 2026-10-21
    private function tanggal($id)
    {
        return date('Y-m-d', strtotime(substr($id, 4, 8)));
    }

    // LAP-20261021-S1 -> S1
    private function shift($id)
    {
        return substr($id, 13, 2);
    }
}
