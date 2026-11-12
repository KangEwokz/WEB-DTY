<?php

namespace Modules\Auth\Http\Controllers;                         // "alamat" class ini = folder Modules/Auth/app/Http/Controllers

use App\Http\Controllers\Controller;                             // controller dasar bawaan Laravel
use Illuminate\Http\Request;                                     // Request = data yang dikirim browser (isi form, dll)
use Illuminate\Support\Facades\Auth;                             // Auth = untuk tahu siapa user yang sedang login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database
use Illuminate\Support\Facades\Hash;                             // Hash = untuk mengacak password sebelum disimpan

class UserController extends Controller                          // class UserController mewarisi Controller (kelola akun login)
{
    public function index(Request $request)                      // tampil daftar akun (route GET /akun)
    {
        $query = DB::table('users')                              // mulai dari tabel users
            ->join('karyawan', 'users.id_karyawan', '=', 'karyawan.id_karyawan') // gabung karyawan untuk ambil nama & foto
            ->join('role', 'users.id_role', '=', 'role.id_role') // gabung role untuk ambil nama role
            ->select('users.*', 'karyawan.nama_karyawan', 'karyawan.foto', 'role.nm_role'); // kolom yang diambil (password tetap terbawa tapi tidak ditampilkan)

        if ($request->cari) {                                    // jika kotak pencarian diisi
            $query->where(function ($q) use ($request) {         // dikelompokkan dalam kurung: (username like ... OR nama like ...)
                $q->where('users.username', 'like', '%'.$request->cari.'%')          // cari di username
                    ->orWhere('karyawan.nama_karyawan', 'like', '%'.$request->cari.'%'); // ATAU di nama karyawan
            });
        }                                                        // akhir if

        $user = $query->orderBy('users.username')                // urut username A-Z
            ->paginate(10)                                       // 10 data per halaman
            ->withQueryString();                                 // kata pencarian ikut terbawa saat pindah halaman

        return view('auth::user.index', compact('user'));        // tampilkan Modules/Auth/resources/views/user/index.blade.php
    }

    public function create()                                     // tampil form buat akun (route GET /akun/create)
    {
        $karyawan = DB::table('karyawan')                        // pilihan karyawan
            ->leftJoin('users', 'karyawan.id_karyawan', '=', 'users.id_karyawan') // gabung users; jika karyawan belum punya akun, kolom users kosong (null)
            ->whereNull('users.id')                              // ambil yang BELUM punya akun saja
            ->where('karyawan.status_aktif', 'Aktif')            // dan karyawannya aktif
            ->select('karyawan.id_karyawan', 'karyawan.nama_karyawan')
            ->orderBy('karyawan.nama_karyawan')                  // urut nama A-Z
            ->get();

        $role = $this->pilihanRole();                            // pilihan role (lihat fungsi pilihanRole di bawah)

        return view('auth::user.create', compact('karyawan', 'role')); // tampilkan form + kirim pilihan dropdown
    }

    public function store(Request $request)                      // simpan akun baru (route POST /akun)
    {
        $request->validate([                                     // periksa isi form
            'id_karyawan' => 'required|exists:karyawan,id_karyawan|unique:users,id_karyawan', // wajib, harus ada di tabel karyawan, dan belum punya akun
            'username' => 'required|max:20|alpha_dash|unique:users,username', // wajib, maks 20, tanpa spasi, tidak boleh kembar
            'id_role' => 'required|exists:role,id_role',         // wajib dan harus ada di tabel role
            'password' => 'required|min:6|confirmed',            // wajib, minimal 6 karakter; confirmed = harus sama dengan kolom password_confirmation
        ], $this->pesanError(), $this->namaKolom());             // pesan & nama kolom diambil dari fungsi di bawah (supaya tidak ditulis 2 kali)

        if ($request->id_role == 'superadmin' && Auth::user()->id_role != 'superadmin') { // hanya superadmin yang boleh membuat akun superadmin
            return back()->with('error', 'Hanya Super Admin yang boleh memberi role Super Admin.')->withInput(); // kembali + pesan error
        }                                                        // akhir if

        DB::table('users')->insert([                             // simpan ke tabel users
            'username' => strtolower($request->username),        // username huruf kecil semua
            'password' => Hash::make($request->password),        // Hash::make = password diacak, tidak disimpan apa adanya
            'id_karyawan' => $request->id_karyawan,
            'id_role' => $request->id_role,
            'status' => 'Aktif',                                 // akun baru selalu Aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);                                                      // akhir insert (kolom id terisi otomatis)

        return redirect()->route('auth.user.index')->with('success', 'Akun '.strtolower($request->username).' berhasil dibuat.'); // kembali ke daftar + pesan sukses
    }

    public function edit(string $id)                             // tampil form ubah (route GET /akun/{id}/edit)
    {
        $user = DB::table('users')                               // ambil akun yang mau diubah
            ->join('karyawan', 'users.id_karyawan', '=', 'karyawan.id_karyawan') // + nama karyawan pemiliknya
            ->select('users.*', 'karyawan.nama_karyawan')
            ->where('users.id', $id)
            ->first();

        if (! $user) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        if ($user->id_role == 'superadmin' && Auth::user()->id_role != 'superadmin') { // akun superadmin hanya boleh diubah oleh superadmin
            return redirect()->route('auth.user.index')->with('error', 'Akun Super Admin hanya bisa diubah oleh Super Admin.'); // kembali + pesan error
        }                                                        // akhir if

        $role = $this->pilihanRole($user->id_role);              // pilihan role + role yang sedang dipakai akun ini

        return view('auth::user.edit', compact('user', 'role')); // tampilkan form ubah + kirim data
    }

    public function update(Request $request, string $id)         // simpan perubahan (route PUT /akun/{id})
    {
        $user = DB::table('users')->where('id', $id)->first();   // ambil data lama

        if (! $user) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        if ($user->id_role == 'superadmin' && Auth::user()->id_role != 'superadmin') { // akun superadmin hanya boleh diubah oleh superadmin
            abort(403);                                          // tolak
        }                                                        // akhir if

        $request->validate([                                     // pemilik akun (karyawan) tidak bisa diganti, jadi tidak divalidasi
            'username' => 'required|max:20|alpha_dash|unique:users,username,'.$id, // username milik akun ini sendiri boleh tetap
            'id_role' => 'required|exists:role,id_role',
            'password' => 'nullable|min:6|confirmed',            // nullable = boleh kosong (artinya password tidak diganti)
        ], $this->pesanError(), $this->namaKolom());

        if ($request->id_role == 'superadmin' && Auth::user()->id_role != 'superadmin') { // hanya superadmin yang boleh memberi role superadmin
            return back()->with('error', 'Hanya Super Admin yang boleh memberi role Super Admin.')->withInput();
        }                                                        // akhir if

        if ($user->id == Auth::id() && $request->id_role != $user->id_role) { // Auth::id() = id akun yang sedang login; tidak boleh mengganti role sendiri
            return back()->with('error', 'Anda tidak bisa mengganti role akun Anda sendiri.')->withInput();
        }                                                        // akhir if

        $data = [                                                // data yang akan diubah
            'username' => strtolower($request->username),
            'id_role' => $request->id_role,
            'updated_at' => now(),
        ];

        if ($request->password) {                                // jika kolom password baru diisi
            $data['password'] = Hash::make($request->password);  // tambahkan password baru (diacak) ke data yang diubah
        }                                                        // jika kosong, password lama tetap

        DB::table('users')->where('id', $id)->update($data);    // simpan perubahan

        return redirect()->route('auth.user.index')->with('success', 'Akun '.strtolower($request->username).' berhasil diubah'.($request->password ? ', password sudah diganti.' : '.')); // pesan sukses, ditambah info jika password diganti
    }

    public function status(string $id)                           // ganti status Aktif <-> Non-Aktif (route PATCH /akun/{id}/status)
    {
        $user = DB::table('users')->where('id', $id)->first();   // ambil data

        if (! $user) {                                           // jika data tidak ditemukan
            abort(404);                                          // tampilkan halaman 404
        }                                                        // akhir if

        if ($user->id == Auth::id()) {                           // tidak boleh menonaktifkan akun sendiri
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }                                                        // akhir if

        if ($user->id_role == 'superadmin' && Auth::user()->id_role != 'superadmin') { // akun superadmin hanya boleh diubah oleh superadmin
            return back()->with('error', 'Akun Super Admin hanya bisa diubah oleh Super Admin.');
        }                                                        // akhir if

        $status_baru = $user->status == 'Aktif' ? 'Non-Aktif' : 'Aktif'; // balik statusnya

        DB::table('users')->where('id', $id)->update([           // simpan status baru
            'status' => $status_baru,
            'updated_at' => now(),
        ]);                                                      // akhir update

        return back()->with('success', 'Akun '.$user->username.' sekarang '.$status_baru.'.'); // kembali + pesan sukses
    }

    private function pilihanRole($role_sekarang = null)          // fungsi bantu: daftar role untuk dropdown; private = hanya dipakai di class ini
    {
        $query = DB::table('role')->where(function ($q) use ($role_sekarang) { // (status Aktif OR role yang sedang dipakai)
            $q->where('status_aktif', 'Aktif')->orWhere('id_role', $role_sekarang);
        });

        if (Auth::user()->id_role != 'superadmin') {             // jika yang login bukan superadmin
            $query->where('id_role', '!=', 'superadmin');        // pilihan superadmin disembunyikan
        }                                                        // akhir if

        return $query->orderBy('nm_role')->get();                // urut nama A-Z lalu kembalikan hasilnya
    }

    private function pesanError()                                // fungsi bantu: pesan error validasi (dipakai store & update)
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'alpha_dash' => ':attribute hanya boleh huruf, angka, - dan _ (tanpa spasi).',
            'unique' => ':attribute sudah dipakai.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'confirmed' => 'Konfirmasi password tidak sama.',
            'id_karyawan.unique' => 'Karyawan ini sudah punya akun.', // pesan khusus untuk id_karyawan
        ];
    }

    private function namaKolom()                                 // fungsi bantu: nama kolom yang tampil di pesan error
    {
        return [
            'id_karyawan' => 'Karyawan',
            'username' => 'Username',
            'id_role' => 'Role',
            'password' => 'Password',
        ];
    }
}
