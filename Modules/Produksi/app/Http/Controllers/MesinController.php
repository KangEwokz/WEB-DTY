<?php

namespace Modules\Produksi\Http\Controllers;                     // "alamat" class ini = folder Modules/Produksi/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class MesinController extends Controller                         // master mesin: kode tetap (MCN-001), line & posisi bisa dipindah
{
    public function index(Request $request)                      // tampil daftar mesin (route GET /produksi/mesin)
    {
        $query = DB::table('mesin')                              // mulai dari tabel mesin
            ->leftJoin('karyawan', 'mesin.updated_by', '=', 'karyawan.id_karyawan') // + nama pengubah terakhir (left = tetap tampil walau belum pernah diubah)
            ->select('mesin.*', 'karyawan.nama_karyawan as nama_pengubah');

        if ($request->line) {                                    // jika filter line dipilih
            $query->where('mesin.line', $request->line);         // tampilkan line itu saja
        }                                                        // akhir if

        if ($request->status) {                                  // jika filter status dipilih
            $query->where('mesin.status', $request->status);
        }                                                        // akhir if

        $mesin = $query->orderBy('mesin.line')                   // urut line L1, L2, L3
            ->orderBy('mesin.posisi')                            // lalu posisi 1, 2, 3, ...
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // filter ikut terbawa saat pindah halaman

        $jumlah = DB::table('mesin')                             // hitung jumlah mesin per status untuk ringkasan di atas tabel
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')                                  // kelompokkan per status
            ->pluck('total', 'status');                          // hasil: ['Running' => 28, 'Stop' => 1, 'Maintenance' => 1]

        return view('produksi::mesin.index', compact('mesin', 'jumlah')); // tampilkan Modules/Produksi/resources/views/mesin/index.blade.php
    }

    public function create()                                     // tampil form tambah (route GET /produksi/mesin/create)
    {
        return view('produksi::mesin.create', ['kode_baru' => $this->kodeBaru()]); // kirim kode yang akan dipakai, hanya untuk ditampilkan
    }

    public function store(Request $request)                      // simpan mesin baru (route POST /produksi/mesin)
    {
        $request->validate($this->aturan(), $this->pesanError()); // periksa isi form

        $pemakai = $this->cekPosisi($request->line, $request->posisi); // cek apakah posisi ini sudah ditempati mesin lain
        if ($pemakai) {                                          // jika sudah ditempati
            return back()->withErrors(['posisi' => 'Posisi '.$request->posisi.' di '.$request->line.' sudah ditempati mesin '.$pemakai.'.'])->withInput(); // kembali + pesan di bawah kolom posisi
        }                                                        // akhir if

        $kode_mesin = $this->kodeBaru();                         // buat kode berikutnya, contoh MCN-031

        DB::table('mesin')->insert([                             // simpan ke tabel mesin
            'kode_mesin' => $kode_mesin,
            'line' => $request->line,
            'posisi' => $request->posisi,
            'status' => $request->status,
            'created_by' => Auth::user()->id_karyawan,           // penginput = user yang login
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert

        return redirect()->route('produksi.mesin.index')->with('success', 'Mesin '.$kode_mesin.' berhasil ditambahkan di '.$request->line.' posisi '.$request->posisi.'.'); // kembali + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /produksi/mesin/{id}/edit)
    {
        $mesin = $this->cariMesin($id);                          // ambil data (404 jika tidak ada)

        return view('produksi::mesin.edit', compact('mesin'));   // tampilkan form
    }

    public function update(Request $request, string $id)         // simpan perubahan line, posisi, status (route PUT /produksi/mesin/{id})
    {
        $mesin = $this->cariMesin($id);                          // ambil data lama

        $request->validate($this->aturan(), $this->pesanError()); // periksa isi form

        $pemakai = $this->cekPosisi($request->line, $request->posisi, $id); // cek posisi tujuan, kecuali mesin ini sendiri
        if ($pemakai) {                                          // jika posisi tujuan sudah ditempati mesin lain
            return back()->withErrors(['posisi' => 'Posisi '.$request->posisi.' di '.$request->line.' sudah ditempati mesin '.$pemakai.'.'])->withInput();
        }                                                        // akhir if

        DB::table('mesin')->where('kode_mesin', $id)->update([   // simpan perubahan (kode mesin tetap)
            'line' => $request->line,
            'posisi' => $request->posisi,
            'status' => $request->status,
            'updated_by' => Auth::user()->id_karyawan,           // pengubah = user yang login
            'updated_at' => now(),
        ]);                                                      // akhir update

        $pindah = $mesin->line != $request->line || $mesin->posisi != $request->posisi; // true jika line atau posisinya berubah
        $pesan = $pindah                                         // susun pesan sesuai yang berubah
            ? 'Mesin '.$id.' dipindah dari '.$mesin->line.' posisi '.$mesin->posisi.' ke '.$request->line.' posisi '.$request->posisi.'.'
            : 'Mesin '.$id.' berhasil diubah.';

        return redirect()->route('produksi.mesin.index')->with('success', $pesan); // kembali ke daftar + pesan sukses
    }

    private function cariMesin(string $id)                       // FUNGSI BANTU (private = hanya dipakai di class ini): ambil 1 mesin; 404 jika tidak ada
    {
        $mesin = DB::table('mesin')->where('kode_mesin', $id)->first();

        if (! $mesin) {                                          // jika tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }

        return $mesin;                                           // kembalikan hasilnya
    }

    private function kodeBaru()                                  // buat kode mesin berikutnya
    {
        $kode_terakhir = DB::table('mesin')->max('kode_mesin');  // kode terbesar saat ini, contoh 'MCN-030'
        $urut = $kode_terakhir ? (int) substr($kode_terakhir, 4) + 1 : 1; // ambil '030' -> 30 -> 31; jika tabel kosong mulai dari 1

        return 'MCN-'.str_pad($urut, 3, '0', STR_PAD_LEFT);      // gabungkan: 'MCN-' + '031'
    }

    private function cekPosisi(string $line, $posisi, ?string $kecuali = null) // cari mesin yang menempati line + posisi ini; hasil = kode mesinnya, atau null jika kosong
    {
        $query = DB::table('mesin')->where('line', $line)->where('posisi', $posisi); // mesin di line & posisi tersebut

        if ($kecuali) {                                          // saat ubah: mesin yang sedang diubah tidak dihitung
            $query->where('kode_mesin', '!=', $kecuali);
        }

        return $query->value('kode_mesin');                      // value = ambil 1 nilai kolom saja, contoh 'MCN-003'
    }

    private function aturan()                                    // aturan validasi (dipakai store & update)
    {
        return [
            'line' => 'required|in:L1,L2,L3',                    // wajib, salah satu line
            'posisi' => 'required|integer|min:1|max:99',         // wajib, bilangan bulat 1-99
            'status' => 'required|in:Running,Stop,Maintenance',  // wajib, salah satu status
        ];
    }

    private function pesanError()                                // pesan error validasi
    {
        return [
            'line.required' => 'Line wajib dipilih.',
            'line.in' => 'Line tidak valid.',
            'posisi.required' => 'Posisi wajib diisi.',
            'posisi.integer' => 'Posisi harus bilangan bulat.',
            'posisi.min' => 'Posisi minimal 1.',
            'posisi.max' => 'Posisi maksimal 99.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status mesin tidak valid.',
        ];
    }
}
