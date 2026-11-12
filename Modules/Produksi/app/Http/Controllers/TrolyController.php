<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class TrolyController extends Controller                         // master troly: Normal (hasil doffing) dan Abnormal (tempat bobbin abnormal)
{
    public function index(Request $request)                      // tampil daftar troly (route GET /produksi/troly)
    {
        $query = DB::table('troly');                             // mulai dari tabel troly

        if ($request->jenis) {                                   // jika filter jenis dipilih
            $query->where('jenis', $request->jenis);             // tampilkan jenis itu saja
        }                                                        // akhir if

        if ($request->status) {                                  // jika filter status dipilih
            $query->where('status_aktif', $request->status);
        }                                                        // akhir if

        $troly = $query->orderBy('jenis', 'desc')                // Normal dulu, lalu Abnormal (urut huruf terbalik: N sebelum A)
            ->orderBy('kode_troly')                              // lalu kode
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        return view('produksi::troly.index', compact('troly'));  // tampilkan Modules/Produksi/resources/views/troly/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /produksi/troly/create)
    {
        return view('produksi::troly.create');                   // tampilkan form kosong
    }

    public function store(Request $request)                      // simpan troly baru (route POST /produksi/troly)
    {
        $request->validate([                                     // periksa isi form
            'jenis' => 'required|in:Normal,Abnormal',            // wajib, Normal atau Abnormal
            'kapasitas' => 'required|integer|min:1|max:255',     // wajib, 1-255 bobbin
        ], $this->pesanError());                                 // akhir validasi

        $awalan = $request->jenis == 'Normal' ? 'TRN' : 'TRA';   // awalan kode sesuai jenis: TRN = normal, TRA = abnormal
        $kode_terakhir = DB::table('troly')->where('kode_troly', 'like', $awalan.'-%')->max('kode_troly'); // kode terbesar jenis ini, contoh 'TRA-005'
        $urut = $kode_terakhir ? (int) substr($kode_terakhir, 4) + 1 : 1; // ambil '005' -> 5 -> 6; jika belum ada mulai dari 1
        $kode_troly = $awalan.'-'.str_pad($urut, 3, '0', STR_PAD_LEFT); // gabungkan: 'TRA' + '-' + '006'

        DB::table('troly')->insert([                             // simpan ke tabel troly
            'kode_troly' => $kode_troly,
            'jenis' => $request->jenis,
            'kapasitas' => $request->kapasitas,
            'status_aktif' => 'Aktif',                           // troly baru selalu Aktif
            'created_by' => Auth::user()->id_karyawan,           // penginput = user yang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('produksi.troly.index')->with('success', 'Troly '.$kode_troly.' ('.$request->jenis.') berhasil ditambahkan.'); // kembali + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/troly/{id}/edit)
    {
        $troly = $this->cariTroly($id);                          // ambil data (404 jika tidak ada)

        return view('produksi::troly.edit', compact('troly'));   // tampilkan form ubah
    }

    public function update(Request $request, string $id)         // simpan perubahan kapasitas (route PUT /produksi/troly/{id})
    {
        $this->cariTroly($id);                                   // pastikan ada

        $request->validate([                                     // jenis tidak bisa diubah karena menentukan awalan kode
            'kapasitas' => 'required|integer|min:1|max:255',
        ], $this->pesanError());

        DB::table('troly')->where('kode_troly', $id)->update([   // simpan kapasitas baru
            'kapasitas' => $request->kapasitas,
            'updated_by' => Auth::user()->id_karyawan,           // pengubah = user yang login
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.troly.index')->with('success', 'Kapasitas troly '.$id.' diubah menjadi '.$request->kapasitas.' bobbin.'); // kembali + pesan sukses
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /produksi/troly/{id}/status)
    {
        $troly = $this->cariTroly($id);                          // ambil data

        $status_baru = $troly->status_aktif == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('troly')->where('kode_troly', $id)->update([   // simpan status baru
            'status_aktif' => $status_baru,
            'updated_by' => Auth::user()->id_karyawan,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Troly '.$id.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }

    private function cariTroly(string $id)                       // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 troly; 404 jika tidak ada
    {
        $troly = DB::table('troly')->where('kode_troly', $id)->first();

        if (! $troly) {                                          // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $troly;                                           // kembalikan hasilnya
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'jenis.required' => 'Jenis troly wajib dipilih.',
            'jenis.in' => 'Jenis troly tidak valid.',
            'kapasitas.required' => 'Kapasitas wajib diisi.',
            'kapasitas.integer' => 'Kapasitas harus bilangan bulat.',
            'kapasitas.min' => 'Kapasitas minimal 1 bobbin.',
            'kapasitas.max' => 'Kapasitas maksimal 255 bobbin.',
        ];
    }
}
