# Aplikasi Pencatatan Pengecekan Visual, Denier & Abnormal Benang DTY

Aplikasi web untuk mini pabrik DTY. Dipakai untuk tugas UTS/UAS mata kuliah PMT (kelompok kami mendapat divisi QCT), penelitian, dan nanti pengujian di mata kuliah Pengujian Perangkat Lunak.
**Fokus utama aplikasi adalah divisi QCT.** Divisi Admin dan Produksi hanya menyediakan data pendukung.
Cakupan proyek berhenti di QCT. Packing, akunting, dan absensi divisi lain TIDAK termasuk.

Rancangan lengkap ada di `docs/rancangan/` dan menjadi sumber kebenaran. Jangan menambah fitur atau tabel di luar rancangan tanpa bertanya dulu.
- `SKEMA.md`: semua tabel, kolom, tipe, kunci, dan keterangan. Isinya sama dengan `RDTY-FINAL.xlsx`.
- `ERD-QCT.drawio` / `ERD-QCT.png`: relasi antar tabel.
- `UseCase-QCT.drawio` / `UseCase-QCT.png`: 6 aktor dan 18 use case, tanpa generalisasi.
- `Tabel-UseCase-ERD.docx`: tabel use case, relasi ERD, dan matriks use case ke tabel (CRUD).

## ATURAN UTAMA DARI PEMILIK PROYEK (wajib, tidak boleh dilanggar)
1. **Gunakan kodingan standar/basic.** Pakai fitur bawaan Laravel yang paling sederhana dan mudah dibaca mahasiswa. Hindari pola lanjutan: tidak ada Service/Repository layer, Observer, Trait buatan sendiri, Event/Listener, Livewire, Inertia, atau Vue/React. Kode harus bisa dijelaskan baris per baris saat presentasi.
2. **Gunakan Query Builder (`DB::table(...)`), BUKAN ORM/Eloquent.** Tidak ada model Eloquent per tabel dan tidak ada relasi `hasMany`/`belongsTo`. Join dan agregasi ditulis langsung dengan Query Builder.
   Satu-satunya pengecualian adalah `Modules\Auth\Models\User`, karena dibutuhkan oleh sistem login Laravel dan package Spatie Permission (`HasRoles`). Model ini tidak dipakai untuk query data lain.
3. **Gunakan template yang diberikan pemilik proyek.** Template (HTML, CSS, JS, gambar) sudah disimpan pemilik proyek di folder **`public/`**. Periksa isi `public/` untuk menemukan file HTML dan aset template. Aset tetap di `public/` dan dipanggil dengan `asset('...')`, jangan dipindah atau disalin. Struktur HTML template dipecah menjadi layout Blade (layout, sidebar, navbar, footer) di modul Auth. Pakai komponen dan class dari template, jangan mendesain tampilan sendiri atau menambah framework CSS lain. **Jika template tidak ditemukan di `public/`, BERHENTI dan tanyakan dulu sebelum membuat view apa pun.**
4. **Uji setiap route dengan Postman dan simpan hasilnya.** Detail di bagian *Pengujian*.
5. **Struktur modul dengan nwidart/laravel-modules.** Aplikasi dibagi menjadi modul **Auth, Admin, Produksi, Qct**. Semua kode, baik master, transaksi, maupun output (controller, view, route, migration, seeder, middleware, helper), disimpan di modul masing-masing, **tidak di folder induk Laravel**. Detail di bagian *Struktur kode*.

## Stack
- Laravel (versi terbaru yang stabil), PHP 8.2+, MySQL/MariaDB
- nwidart/laravel-modules untuk struktur modul
- Blade + template dari pemilik proyek
- Spatie Laravel-Permission untuk RBAC
- Query Builder (`Illuminate\Support\Facades\DB`)
- barryvdh/laravel-dompdf untuk PDF rekap
- Postman + Newman (CLI) untuk pengujian

## Konvensi database
- PK berupa kode yang bisa dibaca, bukan auto increment. Formatnya ada di kolom Keterangan `SKEMA.md`, contoh `DOF-20261020-S1-0001`. Pengecualian: `users.id` dan tabel bawaan Spatie memakai BIGINT auto increment. PK kode dibuat oleh helper `buat_kode()` (lihat Struktur kode), bukan diinput user.
- Nama kolom FK sama dengan nama PK yang dirujuk. Kolom yang merujuk ke orang (`insp_visual`, `insp_lab`, `diverifikasi_oleh`, `created_by`, `updated_by`) adalah FK ke `karyawan.id_karyawan` dan diisi dari user yang sedang login (`auth()->user()->id_karyawan`), tidak pernah dari form.
- `created_at` dan `updated_at` diisi manual saat insert/update dengan `now()`, karena Query Builder tidak mengisinya otomatis.
- Data master tidak dihapus, cukup dinonaktifkan (status Aktif/Non-Aktif).
- Rekap dan laporan tidak menyimpan angka. Tabel `laporan_check`, `rekap_absensi`, dan `rekap_check` hanya menyimpan status verifikasi, verifikator, waktu, dan file PDF. Angkanya dihitung dengan query.
- Bobbin yang lolos tidak dicatat satu per satu: jumlah lolos = `doffing.jml_bobbin` − jumlah baris `abnormal` pada troly itu.
- Mesin ada 3 line (401–403) × 10 posisi. Shift: S1/S2/S3, dan GS untuk non-grup.

## Struktur kode (modular dengan nwidart/laravel-modules)
Proyek memakai package **nwidart/laravel-modules**. Ada 4 modul: **Auth, Admin, Produksi, Qct**.
**Semua kode milik aplikasi berada di dalam modul**, baik master, transaksi, maupun output/rekap: controller, view, route, migration, seeder, middleware, dan helper. Folder induk Laravel (`app/Http/Controllers`, `app/Models`, `resources/views`, `database/migrations`, `database/seeders`, `routes/web.php`) tidak diisi kode aplikasi. Pengecualian hanya untuk file bawaan Laravel yang memang wajib ada (config, bootstrap, DatabaseSeeder pemanggil seeder modul).

Di dalam setiap modul, controller dan view dikelompokkan sesuai IPO: **Master** (input acuan), **Transaksi** (proses harian), dan **Output** (rekap, laporan, verifikasi, PDF).

```
Modules/
  Auth/                          # login/logout, akun & RBAC, layout template, fungsi umum
    app/Http/Controllers/        LoginController
    app/Http/Middleware/         CekDivisi, CekArea
    app/Models/User.php          # satu-satunya model Eloquent (auth + Spatie HasRoles)
    app/Helpers/helpers.php      # buat_kode(), area_aktif(), sudah_terkunci()
    database/migrations/         users, sessions, password_reset_tokens, tabel Spatie (permission)
    database/seeders/            RolePermissionSeeder, UserSeeder, AuthDatabaseSeeder
    resources/views/             login.blade.php, layouts/app.blade.php, layouts/partials/{sidebar,navbar,alert}
    routes/web.php
  Admin/                         # divisi Admin (Staf, area KPG)
    app/Http/Controllers/Master/     DivisiController, JabatanController, AreaController, KaryawanController, UserController
    app/Http/Controllers/Output/     RekapAbsensiController (UC05, baca saja)
    database/migrations/         divisi, jabatan, area, karyawan
    database/seeders/            DivisiSeeder, JabatanSeeder, AreaSeeder, KaryawanSeeder, AdminDatabaseSeeder
    resources/views/master/{divisi,jabatan,area,karyawan,user}/   resources/views/output/rekap-absensi/
    routes/web.php
  Produksi/                      # divisi Produksi (Supervisor)
    app/Http/Controllers/Master/     BenangController, MesinController
    app/Http/Controllers/Transaksi/  JadwalProduksiController, DoffingController
    database/migrations/         benang, mesin, jadwal_produksi, doffing
    database/seeders/            BenangSeeder, MesinSeeder, ProduksiDatabaseSeeder (+ contoh doffing)
    resources/views/master/{benang,mesin}/   resources/views/transaksi/{jadwal-produksi,doffing}/
    routes/web.php
  Qct/                           # divisi QCT (HOD, Operator) + rekap untuk Direktur
    app/Http/Controllers/Master/     AreaQctController (area milik QCT), GradeController, JenisAbnormalController
    app/Http/Controllers/Transaksi/  AbsensiController, CheckBatchController, AbnormalController, SampelController
    app/Http/Controllers/Output/     LaporanCheckController (UC11), RekapAbsensiController (UC10), RekapCheckController (UC16–18)
    database/migrations/         grade, jenis_abnormal, absensi, rekap_absensi, check_batch, abnormal, sampel, laporan_check, rekap_check
    database/seeders/            GradeSeeder, JenisAbnormalSeeder, QctDatabaseSeeder (+ contoh data)
    resources/views/master/...   resources/views/transaksi/...   resources/views/output/...
    routes/web.php
docs/pengujian/                  # koleksi & hasil Postman (bukan kode aplikasi)
```

Catatan teknis nwidart:
- Pasang dengan `composer require nwidart/laravel-modules`, lalu publish config. Ikuti petunjuk resmi versi yang terpasang untuk `merge-plugin` (`"extra": {"merge-plugin": {"include": ["Modules/*/composer.json"]}}`), kemudian `composer dump-autoload`.
- Buat modul dengan `php artisan module:make Auth Admin Produksi Qct`. Hapus file contoh hasil generate yang tidak dipakai (misalnya model, resource, atau test contoh), supaya kode tetap basic.
- View dipanggil dengan namespace modul, contoh `view('qct::transaksi.check-batch.index')` dan `@extends('auth::layouts.app')`.
- `config/auth.php` diarahkan ke `Modules\Auth\Models\User`. `app/Models/User.php` bawaan Laravel dihapus.
- Middleware `divisi` dan `area` didaftarkan sebagai alias di `bootstrap/app.php` (Laravel 11+) dan menunjuk ke class di modul Auth.
- `helpers.php` di modul Auth didaftarkan lewat `autoload.files` di `Modules/Auth/composer.json`.
- Migration dijalankan bersama dengan `php artisan migrate`. **Urutan timestamp nama file migration harus mengikuti dependensi FK lintas modul:** divisi → jabatan → area → karyawan → users/Spatie → benang → mesin → jadwal_produksi → doffing → grade → jenis_abnormal → absensi → rekap_absensi → check_batch → abnormal → sampel → laporan_check → rekap_check.
- `database/seeders/DatabaseSeeder.php` (folder induk) hanya berisi pemanggilan seeder tiap modul, dengan urutan Admin → Auth → Produksi → Qct.

## Aturan coding
**Pola controller (basic)**
```php
public function store(Request $request)
{
    $request->validate([...]);                        // validasi langsung di controller
    DB::table('doffing')->insert([
        'id_doffing' => buat_kode('DOF', $request->tgl_doffing, $request->shift),
        // kolom lain dari $request
        'created_by' => auth()->user()->id_karyawan,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    return redirect()->route('produksi.doffing.index')->with('success', 'Data doffing berhasil disimpan');
}
```
- Validasi memakai `$request->validate()` di controller. Enum memakai `in:...`, FK memakai `exists:tabel,kolom`.
- Operasi yang menulis lebih dari satu tabel dibungkus `DB::transaction(function () { ... })`.
- Data yang sudah diverifikasi dicek dengan helper `sudah_terkunci($tanggal, $shift)` di awal method edit/update/destroy. Kalau terkunci, redirect dengan pesan error. Tombol ubah/hapus juga disembunyikan di view.
- Hapus data master = `update(['status_aktif' => 'Non-Aktif'])`. Data transaksi hanya boleh dihapus sebelum diverifikasi.
- Satu controller per tabel/menu, method mengikuti resource standar: index, create, store, show, edit, update, destroy. Aksi verifikasi memakai method `verifikasi`.

**Penamaan**
- Nama tabel dan kolom mengikuti `SKEMA.md` persis (bahasa Indonesia, snake_case).
- Controller: PascalCase + `Controller`. Folder view: kebab-case. Nama route: `modul.resource.aksi`, contoh `qct.check-batch.store`.
- Semua teks di UI dan pesan flash memakai bahasa Indonesia.

**Routing**
- Isi `routes/web.php` tiap modul: `Route::middleware(['auth','divisi:QCT'])->prefix('qct')->name('qct.')->group(function () { ... });`
- Gunakan `Route::resource` ditambah route khusus untuk verifikasi, contoh `POST qct/laporan-check/{id}/verifikasi`.
- Setiap route diberi middleware `permission:...` sesuai `RolePermissionSeeder`. Menu operator diberi tambahan `area:VIS` atau `area:LAB`.

**View**
- Semua halaman `@extends('auth::layouts.app')`, yaitu layout yang dibuat dari template.
- Sidebar hanya menampilkan menu yang lolos `@can` serta cek divisi dan area.
- Tabel daftar memakai paginasi Query Builder (`->paginate(10)`) dan filter tanggal/shift. Error validasi ditampilkan per field.

**Lainnya**
- Rapikan dengan `./vendor/bin/pint` sebelum selesai.
- Jangan memasang package lain selain nwidart/laravel-modules, Spatie Permission, dan DomPDF tanpa bertanya dulu.
- Jangan mengubah struktur tabel dari `SKEMA.md` tanpa bertanya. Kalau perlu perubahan, usulkan dulu dan jelaskan alasannya.

## RBAC: role + divisi + area
Ada 5 role umum, dikelola lewat Spatie: `direktur`, `hod`, `supervisor`, `staf`, `operator`.
Akses menu = **permission dari role** + **divisi** (`karyawan.id_divisi`) + **area** (khusus operator).
- Middleware `divisi:QCT|Produksi|Admin` dipasang di grup route tiap modul. Direktur dikecualikan dan boleh membuka menu rekap check QCT.
- Middleware `area:VIS|LAB` dipasang untuk menu operator QCT. Helper `area_aktif()` mengambil `absensi.id_area` milik user pada tanggal dan shift yang sedang berjalan. Kalau absensi belum diisi, pakai `karyawan.id_area` (area default).
- Tabel `area` milik divisi: `KPG` Kepegawaian (Admin), `VIS` Visual (QCT), `LAB` Lab (QCT).

| Aktor | Role | Divisi | Area | Use case |
|---|---|---|---|---|
| Staf Admin | staf | Admin | KPG | UC03 Kelola Data Karyawan, UC04 Kelola Akun Pengguna, UC05 Melihat Rekap Absensi QCT (hanya yang Terverifikasi) |
| Supervisor Produksi | supervisor | Produksi | – | UC06 Kelola Jadwal Produksi (+ benang, mesin), UC07 Kelola Data Doffing |
| HOD QCT | hod | QCT | – | UC08 Kelola Master QCT (area QCT, grade, jenis abnormal), UC09 Kelola Absensi & Penugasan Area QCT, UC10 Verifikasi Rekap Absensi Bulanan, UC11 Verifikasi Laporan Check per tanggal+shift |
| Operator QCT (Visual) | operator | QCT | VIS | UC12 Cek Visual, UC14 Abnormal (bagian visual), UC15 Sampel |
| Operator QCT (Lab) | operator | QCT | LAB | UC13 Cek Denier, UC14 Abnormal (hasil uji lab), UC15 Sampel |
| Direktur | direktur | – | – | UC16 Lihat Rekap Check, UC17 Verifikasi Rekap Check «include» UC18 Arsipkan (generate PDF) |

Semua aktor: UC01 Login, UC02 Logout.
Permission dibuat kira-kira satu per use case, di `RolePermissionSeeder`. Role `hod` dan `supervisor` dipakai lintas divisi untuk pengembangan nanti, tapi saat ini hanya diberi permission QCT/Produksi.

## Alur pengecekan QCT
1. Supervisor menginput doffing per troly (merge, jumlah bobbin, dst.).
2. Operator Visual memilih troly yang belum dicek, lalu mengisi `check_batch.hasil_visual`. Setiap bobbin abnormal dibuat jadi baris `abnormal` (mesin asal, jenis abnormal) dengan status otomatis `Pending`.
3. Operator Lab mengisi `hasil_denier`, `id_grade`, dan status troly `Packing/Hold`. Untuk abnormal yang `Pending`, Lab mengisi act_denier, elong, tenacity, dti, grade, lalu status `Packing/Cutting`.
4. Operator Lab menginput sampel per merge per shift. `id_jns_abn` kosong berarti sampel OK.
5. HOD memverifikasi laporan per tanggal+shift. Setelah Terverifikasi, check_batch, abnormal, dan sampel pada tanggal+shift itu terkunci.
6. HOD memverifikasi rekap absensi per periode (YYYY-MM). Setelah itu absensi periode tersebut terkunci dan rekapnya muncul untuk Staf Admin.
7. Direktur memverifikasi rekap check bulanan. Sistem lalu membuat PDF dan menyimpannya di `file_pdf` sebagai arsip.

Tanggal, shift, troly, dan merge pada check_batch/abnormal diambil dengan join ke `doffing` lewat `id_doffing`, tidak disimpan ulang. Penimbangan troly dilakukan petugas packing dan berada di luar cakupan.

## Pengujian (Postman)
- Setiap route di semua modul wajib punya request di Postman collection `docs/pengujian/DTY-QCT.postman_collection.json`. Foldernya mengikuti modul (Auth, Admin, Produksi, Qct), dan subfoldernya per use case (contoh `UC12 Cek Visual`).
- Environment disimpan di `docs/pengujian/local.postman_environment.json`, berisi `base_url` dan akun per role.
- Karena aplikasi memakai web route dengan session dan CSRF:
  - setiap folder diawali request login;
  - pre-request script mengambil token `_token` dari halaman form/login lalu menyimpannya di variabel;
  - cookie session dipakai ulang oleh request berikutnya.
- Setiap request punya test script (`pm.test`). Minimal yang dicek:
  1. akses dengan role/divisi/area yang benar berhasil (200 atau redirect sukses);
  2. akses dengan role/divisi/area lain ditolak (403 atau redirect ke login);
  3. input tidak valid menghasilkan error validasi;
  4. ubah data yang sudah diverifikasi ditolak.
- Jalankan dengan Newman, lalu simpan hasilnya:
  `npx newman run docs/pengujian/DTY-QCT.postman_collection.json -e docs/pengujian/local.postman_environment.json -r cli,json --reporter-json-export docs/pengujian/hasil/hasil-uji-YYYYMMDD-HHMM.json`
  Setelah itu buat ringkasan `docs/pengujian/hasil/RINGKASAN.md`, berisi tabel: UC, route, method, skenario, hasil (Lulus/Gagal), dan catatan.
- Pengujian dilakukan di akhir setiap tahap untuk route yang baru dibuat. Hasil lama tidak dihapus, karena dipakai sebagai bukti untuk mata kuliah Pengujian Perangkat Lunak.

## Urutan pengerjaan
1. Setup project, pasang nwidart dan buat modul Auth/Admin/Produksi/Qct, pasang Spatie dan DomPDF, ubah template di `public/` menjadi layout di modul Auth (aset tetap di `public/`), daftarkan helper dan middleware
2. Migration semua tabel sesuai `SKEMA.md` di modul pemiliknya (lihat Struktur kode), ditambah tabel Spatie di modul Auth
3. Seeder: divisi, jabatan, area, karyawan contoh, user per role, RolePermissionSeeder, benang, mesin (30), grade, jenis abnormal
4. Modul Auth (login dengan username, logout), middleware divisi & area, sidebar dinamis
5. Modul Admin: UC03, UC04, UC05
6. Modul Produksi: UC06, UC07
7. Modul Qct input: UC08, UC09, UC12–UC15
8. Modul Qct verifikasi HOD: UC10, UC11, termasuk penguncian data
9. Modul Qct rekap Direktur: UC16–UC18 dengan PDF
10. Lengkapi collection Postman, jalankan seluruh pengujian, dan buat ringkasan akhir

Kerjakan per tahap. Di akhir tiap tahap, jalankan `php artisan migrate:fresh --seed` dan pengujian Postman untuk route tahap itu, simpan hasilnya, lalu laporkan sebelum lanjut ke tahap berikutnya.

## Keputusan yang masih terbuka (tanyakan, jangan diasumsikan)
- Kriteria penentuan grade: masih dikonfirmasi ke pabrik. Untuk sementara grade dipilih manual dari master `grade`.
- Area gabungan `GAB` (Visual & Lab) untuk kasus hanya satu operator yang hadir: belum diputuskan.
- `doffing.berat_troly`: dicatat produksi atau dihapus? Belum diputuskan.
- Kolom `users.role` di SKEMA dibuat sebelum memakai Spatie. Karena memakai Spatie, kolom ini tidak dibuat dan role disimpan di `model_has_roles`.
