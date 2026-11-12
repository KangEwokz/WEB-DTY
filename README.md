# QCT DTY (DTY-WEB)

Aplikasi web untuk departemen **Draw Textured Yarn (DTY)**: data karyawan, hak akses, produksi per shift, dan rekap laporan.
Dibangun dengan **Laravel 13** dan **nwidart/laravel-modules**. Kodenya sengaja dibuat basic (Query Builder `DB::table`), dan setiap baris diberi penjelasan di sampingnya.

## Fitur

| Modul | Isi |
|---|---|
| **Auth** | Login, dashboard Data Pengguna, Akun Pengguna, Role & Permission |
| **Admin** | Data Karyawan (dengan foto), master Departemen, Divisi, Jabatan, Area |
| **Produksi** | Absensi & Penempatan, Doffing, Laporan Akhir Shift (termasuk temuan abnormal per bobbin), Rekap Bulanan (cetak A4) |
| **Data Master produksi** | Benang, Mesin, Troly, Jenis Abnormal |

## Kebutuhan

- PHP **8.3** atau lebih baru, dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`
- Composer 2
- MySQL 8 (atau MariaDB 10.6+)
- Git

Di Windows, semuanya sudah tersedia di [Laragon](https://laragon.org/). Pastikan Laragon sudah **Start All** (MySQL menyala) sebelum mulai.

## Cara clone dan menjalankan

### 1. Clone repository

```bash
git clone https://github.com/KangEwokz/WEB-DTY.git DTY-WEB
cd DTY-WEB
```

### 2. Install library PHP

```bash
composer install
```

Folder `vendor/` tidak disimpan di GitHub, jadi langkah ini wajib. Modul di folder `Modules/` ikut terpasang otomatis.

### 3. Buat file `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Di Windows (Command Prompt), ganti `cp` dengan `copy`.

Lalu buka `.env` dan sesuaikan bagian database bila perlu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dty_web
DB_USERNAME=root
DB_PASSWORD=
```

Bawaan Laragon: user `root` tanpa password.

### 4. Buat database

Buat database kosong bernama `dty_web`, lewat HeidiSQL/phpMyAdmin atau perintah berikut:

```sql
CREATE DATABASE dty_web;
```

### 5. Buat tabel dan isi data awal

```bash
php artisan migrate --seed
```

Perintah ini membuat semua tabel dan mengisi data contoh: departemen, divisi, jabatan, area, karyawan, role, permission, akun login, benang, 30 mesin, 15 troly, dan jenis abnormal.

### 6. Jalankan aplikasi

```bash
php artisan serve
```

Buka **http://localhost:8000**.

## Akun login bawaan

Semua akun memakai password **`password123`**.

| Username | Role | Bisa membuka |
|---|---|---|
| `superadmin` | Super Admin | Semua halaman, termasuk Role & Permission |
| `admin` | Admin | Data Karyawan, Data Master organisasi, Akun Pengguna |
| `supervisor` | Supervisor | Produksi (absensi, doffing, laporan, rekap) dan master produksi |
| `hod` | HOD | Melihat data karyawan dan produksi |
| `kadiv_qct` | Kadiv | Melihat data karyawan dan produksi |
| `direktur` | Direktur | Melihat data karyawan dan produksi |
| `op_vis`, `op_lab` | Operator | Dashboard saja |
| `teknisi_elk`, `teknisi_ins` | Teknisi | Dashboard saja |
| `op_nonaktif` | Operator | Tidak bisa login (contoh akun nonaktif) |

**Penting:** ganti password akun-akun ini (menu Akun Pengguna) sebelum aplikasi dipakai sungguhan.

## Hal yang perlu diketahui

- **Foto karyawan** disimpan langsung di `public/uploads/foto/` (disk `uploads`), jadi tidak perlu `php artisan storage:link`. Folder ini tidak ikut ke GitHub, sehingga foto harus diunggah ulang di komputer baru.
- **Data yang tidak ikut ke GitHub:** file `.env`, folder `vendor/`, foto karyawan, dan isi database. Data yang sudah diinput di komputer lain tidak ikut ter-clone; kalau ingin dipindahkan, gunakan export/import database (misalnya lewat HeidiSQL).
- **Drive non-NTFS** (exFAT/FAT32, misalnya flashdisk atau drive data): jika git menampilkan pesan `detected dubious ownership`, daftarkan foldernya sebagai folder aman:

  ```bash
  git config --global --add safe.directory E:/lokasi/DTY-WEB
  ```

- **Mengulang database dari awal:** `php artisan migrate:fresh --seed`. Perintah ini **menghapus semua data** lalu mengisi ulang data contoh.
- **Menambah modul baru:** `php artisan module:make NamaModul`. Jika muncul pesan timeout `composer dump-autoload`, jalankan `composer dump-autoload` secara manual.

## Struktur singkat

```
Modules/
├── Auth/       login, dashboard, akun, role & permission, layout utama (sidebar)
├── Admin/      karyawan & data master organisasi
└── Produksi/   absensi, doffing, laporan shift, rekap bulanan, master produksi
public/
├── Royal-UI/template/   template tampilan (Bootstrap 5)
└── uploads/             foto karyawan (dibuat otomatis saat upload)
```

Setiap modul punya `routes/web.php`, `app/Http/Controllers/`, `resources/views/`, dan `database/` (migration & seeder) sendiri.

### Hak akses

Hak akses diatur per **nama route**. Contohnya, role yang punya permission `produksi.doffing.create` boleh membuka form tambah doffing. Pengecekannya dilakukan oleh middleware `akses` (`Modules/Auth/app/Http/Middleware/CekAkses.php`). Pembagian permission ke tiap role bisa diatur dari menu **Role & Permission** (login sebagai `superadmin`).
