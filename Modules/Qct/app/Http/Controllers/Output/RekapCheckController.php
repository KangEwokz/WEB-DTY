<?php

namespace Modules\Qct\Http\Controllers\Output;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// UC16 Lihat Rekap Check QCT, UC17 Verifikasi Rekap Check «include» UC18 Arsipkan (PDF). Aktor: Direktur.
// Tabel rekap_check hanya menyimpan status, verifikator, waktu, dan file PDF; angkanya dihitung dengan query.
class RekapCheckController extends Controller
{
    // UC16: daftar periode (bulan) yang punya data doffing
    public function index()
    {
        $periode = DB::table('doffing')
            ->select(
                DB::raw("DATE_FORMAT(tgl_doffing, '%Y-%m') as periode"),
                DB::raw('COUNT(*) as jml_troly'),
                DB::raw('SUM(jml_bobbin) as jml_bobbin')
            )
            ->groupBy('periode')
            ->orderBy('periode', 'desc')
            ->paginate(10);

        // Status rekap per periode, contoh ['2026-09' => 'Terverifikasi']
        $status = DB::table('rekap_check')->pluck('status', 'periode');

        return view('qct::output.rekap-check.index', compact('periode', 'status'));
    }

    // UC16: isi rekap satu bulan. $id = RCK-YYYYMM
    public function show($id)
    {
        $periode = $this->periode($id);
        $data = $this->hitung($periode);
        if (count($data['troly']) == 0) {
            abort(404);
        }
        $rekap = DB::table('rekap_check')
            ->leftJoin('karyawan', 'rekap_check.diverifikasi_oleh', '=', 'karyawan.id_karyawan')
            ->where('rekap_check.id_rekap_check', $id)
            ->select('rekap_check.*', 'karyawan.nama_karyawan as verifikator')
            ->first();

        return view('qct::output.rekap-check.show', $data + compact('id', 'periode', 'rekap'));
    }

    // UC17 Verifikasi, sekaligus UC18 Arsipkan: sistem membuat PDF dan menyimpannya di file_pdf
    public function verifikasi($id)
    {
        $periode = $this->periode($id);
        $data = $this->hitung($periode);
        if (count($data['troly']) == 0) {
            abort(404);
        }
        if (DB::table('rekap_check')->where('id_rekap_check', $id)->where('status', 'Terverifikasi')->exists()) {
            return redirect()->route('qct.rekap-check.show', $id)->with('error', 'Rekap check periode ini sudah diverifikasi.');
        }
        if ($data['ringkasan']['shift_belum'] > 0) {
            return redirect()->route('qct.rekap-check.show', $id)
                ->with('error', 'Rekap belum bisa diverifikasi: '.$data['ringkasan']['shift_belum'].' laporan shift belum diverifikasi HOD.');
        }

        // UC18: cetak PDF arsip ke public/storage/rekap-check
        $verifikator = DB::table('karyawan')->where('id_karyawan', auth()->user()->id_karyawan)->value('nama_karyawan');
        $pdf = Pdf::loadView('qct::output.rekap-check.pdf', $data + [
            'periode' => $periode,
            'verifikator' => $verifikator,
            'waktu' => now(),
        ]);
        $file_pdf = 'rekap-check/'.$id.'.pdf';
        Storage::disk('public')->put($file_pdf, $pdf->output());

        DB::table('rekap_check')->insert([
            'id_rekap_check' => $id,
            'periode' => $periode,
            'status' => 'Terverifikasi',
            'diverifikasi_oleh' => auth()->user()->id_karyawan,
            'waktu_verifikasi' => now(),
            'file_pdf' => $file_pdf,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.rekap-check.show', $id)->with('success', 'Rekap check berhasil diverifikasi dan diarsipkan sebagai PDF.');
    }

    // Semua angka rekap satu bulan
    private function hitung($periode)
    {
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        // Semua troly dalam sebulan + hasil cek + jumlah bobbin abnormal
        $troly = DB::table('doffing')
            ->leftJoin('check_batch', 'doffing.id_doffing', '=', 'check_batch.id_doffing')
            ->whereYear('doffing.tgl_doffing', $tahun)
            ->whereMonth('doffing.tgl_doffing', $bulan)
            ->select(
                'doffing.*',
                'check_batch.hasil_visual',
                'check_batch.hasil_denier',
                'check_batch.id_grade',
                'check_batch.status',
                DB::raw('(SELECT COUNT(*) FROM abnormal WHERE abnormal.id_doffing = doffing.id_doffing) as jml_abnormal')
            )
            ->get();

        // Rekap per tanggal + shift, beserta status laporan HOD
        $per_shift = DB::table('doffing')
            ->leftJoin('laporan_check', function ($join) {
                $join->on('doffing.tgl_doffing', '=', 'laporan_check.tgl_laporan')
                    ->on('doffing.shift', '=', 'laporan_check.shift');
            })
            ->whereYear('doffing.tgl_doffing', $tahun)
            ->whereMonth('doffing.tgl_doffing', $bulan)
            ->select(
                'doffing.tgl_doffing',
                'doffing.shift',
                'laporan_check.status',
                DB::raw('COUNT(doffing.id_doffing) as jml_troly'),
                DB::raw('SUM(doffing.jml_bobbin) as jml_bobbin'),
                DB::raw('SUM((SELECT COUNT(*) FROM abnormal WHERE abnormal.id_doffing = doffing.id_doffing)) as jml_abnormal')
            )
            ->groupBy('doffing.tgl_doffing', 'doffing.shift', 'laporan_check.status')
            ->orderBy('doffing.tgl_doffing')
            ->orderBy('doffing.shift')
            ->get();

        $abnormal_jenis = DB::table('abnormal')
            ->join('doffing', 'abnormal.id_doffing', '=', 'doffing.id_doffing')
            ->join('jenis_abnormal', 'abnormal.id_jns_abn', '=', 'jenis_abnormal.id_jns_abn')
            ->whereYear('doffing.tgl_doffing', $tahun)
            ->whereMonth('doffing.tgl_doffing', $bulan)
            ->select(
                'jenis_abnormal.nm_abnormal',
                DB::raw('COUNT(*) as jumlah'),
                DB::raw("SUM(CASE WHEN abnormal.status = 'Packing' THEN 1 ELSE 0 END) as packing"),
                DB::raw("SUM(CASE WHEN abnormal.status = 'Cutting' THEN 1 ELSE 0 END) as cutting")
            )
            ->groupBy('jenis_abnormal.nm_abnormal')
            ->get();

        $grade = DB::table('check_batch')
            ->join('doffing', 'check_batch.id_doffing', '=', 'doffing.id_doffing')
            ->whereYear('doffing.tgl_doffing', $tahun)
            ->whereMonth('doffing.tgl_doffing', $bulan)
            ->whereNotNull('check_batch.id_grade')
            ->select('check_batch.id_grade', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('check_batch.id_grade')
            ->get();

        $sampel = DB::table('sampel')
            ->whereYear('tgl_sampel', $tahun)
            ->whereMonth('tgl_sampel', $bulan)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN id_jns_abn IS NULL THEN 1 ELSE 0 END) as ok')
            )
            ->first();

        // Bobbin lolos = jumlah bobbin - jumlah bobbin abnormal
        $ringkasan = [
            'troly' => count($troly),
            'bobbin' => $troly->sum('jml_bobbin'),
            'abnormal' => $troly->sum('jml_abnormal'),
            'lolos' => $troly->sum('jml_bobbin') - $troly->sum('jml_abnormal'),
            'visual_ng' => $troly->where('hasil_visual', 'NG')->count(),
            'denier_ng' => $troly->where('hasil_denier', 'NG')->count(),
            'packing' => $troly->where('status', 'Packing')->count(),
            'hold' => $troly->where('status', 'Hold')->count(),
            'sampel_total' => (int) $sampel->total,
            'sampel_ok' => (int) $sampel->ok,
            'shift_total' => count($per_shift),
            'shift_belum' => $per_shift->where('status', '!=', 'Terverifikasi')->count(),
        ];

        return compact('troly', 'per_shift', 'abnormal_jenis', 'grade', 'ringkasan');
    }

    // RCK-202609 -> 2026-09
    private function periode($id)
    {
        return substr($id, 4, 4).'-'.substr($id, 8, 2);
    }
}
