<?php

namespace Modules\Qct\Http\Controllers\Output;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// UC10 Verifikasi Rekap Absensi Bulanan QCT (HOD).
// Tabel rekap_absensi hanya menyimpan status, verifikator, waktu, dan file PDF; angkanya dihitung dengan query.
class RekapAbsensiController extends Controller
{
    // Daftar periode (bulan) yang punya data absensi
    public function index()
    {
        $periode = DB::table('absensi')
            ->select(DB::raw("DATE_FORMAT(tgl_absen, '%Y-%m') as periode"), DB::raw('COUNT(*) as jml_absen'))
            ->groupBy('periode')
            ->orderBy('periode', 'desc')
            ->paginate(10);

        // Status rekap per periode, contoh ['2026-09' => 'Terverifikasi']
        $status = DB::table('rekap_absensi')->pluck('status', 'periode');

        return view('qct::output.rekap-absensi.index', compact('periode', 'status'));
    }

    // $id = RAB-YYYYMM
    public function show($id)
    {
        $periode = $this->periode($id);
        $data = $this->hitung($periode);
        if (count($data) == 0) {
            abort(404);
        }
        $rekap = $this->ambilRekap($id);
        $bisa_verifikasi = ! $rekap && $periode < date('Y-m');

        return view('qct::output.rekap-absensi.show', compact('id', 'periode', 'data', 'rekap', 'bisa_verifikasi'));
    }

    // HOD memverifikasi rekap. Sistem membuat PDF, lalu absensi periode itu terkunci dan rekap tampil untuk Staf Admin.
    public function verifikasi($id)
    {
        $periode = $this->periode($id);
        $data = $this->hitung($periode);
        if (count($data) == 0) {
            abort(404);
        }
        if (sudah_terkunci($periode.'-01')) {
            return redirect()->route('qct.rekap-absensi.show', $id)->with('error', 'Rekap absensi periode ini sudah diverifikasi.');
        }
        if ($periode >= date('Y-m')) {
            return redirect()->route('qct.rekap-absensi.show', $id)->with('error', 'Periode belum selesai, rekap baru bisa diverifikasi setelah akhir bulan.');
        }

        // Cetak PDF rekap ke public/storage/rekap-absensi
        $verifikator = DB::table('karyawan')->where('id_karyawan', auth()->user()->id_karyawan)->value('nama_karyawan');
        $pdf = Pdf::loadView('qct::output.rekap-absensi.pdf', [
            'periode' => $periode,
            'data' => $data,
            'verifikator' => $verifikator,
            'waktu' => now(),
        ]);
        $file_pdf = 'rekap-absensi/'.$id.'.pdf';
        Storage::disk('public')->put($file_pdf, $pdf->output());

        DB::table('rekap_absensi')->insert([
            'id_rekap' => $id,
            'periode' => $periode,
            'status' => 'Terverifikasi',
            'diverifikasi_oleh' => auth()->user()->id_karyawan,
            'waktu_verifikasi' => now(),
            'file_pdf' => $file_pdf,
            'created_by' => auth()->user()->id_karyawan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qct.rekap-absensi.show', $id)->with('success', 'Rekap absensi berhasil diverifikasi. Absensi periode ini sekarang terkunci.');
    }

    // Jumlah kehadiran per karyawan dalam satu periode
    private function hitung($periode)
    {
        return DB::table('absensi')
            ->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereYear('absensi.tgl_absen', substr($periode, 0, 4))
            ->whereMonth('absensi.tgl_absen', substr($periode, 5, 2))
            ->select(
                'karyawan.id_karyawan',
                'karyawan.nama_karyawan',
                'karyawan.grup',
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Ijin' THEN 1 ELSE 0 END) as ijin"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Alfa' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN absensi.kehadiran = 'Cuti' THEN 1 ELSE 0 END) as cuti"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('karyawan.id_karyawan', 'karyawan.nama_karyawan', 'karyawan.grup')
            ->orderBy('karyawan.nama_karyawan')
            ->get();
    }

    private function ambilRekap($id)
    {
        return DB::table('rekap_absensi')
            ->leftJoin('karyawan', 'rekap_absensi.diverifikasi_oleh', '=', 'karyawan.id_karyawan')
            ->where('rekap_absensi.id_rekap', $id)
            ->select('rekap_absensi.*', 'karyawan.nama_karyawan as verifikator')
            ->first();
    }

    // RAB-202609 -> 2026-09
    private function periode($id)
    {
        return substr($id, 4, 4).'-'.substr($id, 8, 2);
    }
}
