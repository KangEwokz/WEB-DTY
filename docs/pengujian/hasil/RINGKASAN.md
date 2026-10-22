# Ringkasan Hasil Pengujian Route (Postman)

> **Catatan penting:** hasil ini dijalankan dengan `alat/mini-runner.cjs`, runner pengganti Newman, karena Newman tidak bisa dipasang di laptop pengembang (Node 18 bawaan Laragon gagal TLS ke registry npm). Runner ini menjalankan request dan script `pm.test` yang **sama persis** dari collection, tetapi bukan Newman. Untuk bukti resmi mata kuliah, jalankan ulang dengan Newman (lihat bagian *Cara menjalankan ulang*) lalu perbarui ringkasan ini.

| Keterangan | Nilai |
|---|---|
| Waktu pengujian | 22-10-2026 07:54 WIB |
| Collection | `docs/pengujian/DTY-QCT.postman_collection.json` |
| Environment | `docs/pengujian/local.postman_environment.json` (`base_url` = http://127.0.0.1:8000) |
| Data awal | `php artisan migrate:fresh --seed` sebelum pengujian |
| File hasil | `docs/pengujian/hasil/hasil-uji-20261022-0749-runner-pengganti.json` |
| Jumlah request | 596 |
| Assertion lulus / gagal | 956 / 1 |

## Rekap per Use Case

| Modul | Use Case | Request | Assertion Lulus | Assertion Gagal | Hasil |
|---|---|---|---|---|---|
| Auth | UC01 Login | 36 | 69 | 0 | Lulus |
| Auth | UC02 Logout | 8 | 10 | 0 | Lulus |
| Auth | Akun Demo Superadmin | 9 | 16 | 0 | Lulus |
| Admin | UC03 Kelola Data Karyawan | 59 | 103 | 0 | Lulus |
| Admin | UC04 Kelola Akun Pengguna | 36 | 59 | 1 | Gagal |
| Admin | UC05 Melihat Rekap Absensi QCT | 18 | 21 | 0 | Lulus |
| Produksi | UC06 Kelola Jadwal Produksi | 53 | 82 | 0 | Lulus |
| Produksi | UC07 Kelola Data Doffing | 33 | 54 | 0 | Lulus |
| Qct | UC08 Kelola Data Master QCT | 55 | 84 | 0 | Lulus |
| Qct | UC09 Kelola Absensi & Penugasan Area QCT | 33 | 53 | 0 | Lulus |
| Qct | UC12 Cek Visual | 30 | 47 | 0 | Lulus |
| Qct | UC13 Cek Denier | 22 | 35 | 0 | Lulus |
| Qct | UC14 Kelola Data Abnormal Benang | 38 | 64 | 0 | Lulus |
| Qct | UC15 Kelola Data Sampel | 32 | 50 | 0 | Lulus |
| Qct | UC11 Verifikasi Laporan Check QCT | 49 | 80 | 0 | Lulus |
| Qct | UC10 Verifikasi Rekap Absensi Bulanan QCT | 40 | 65 | 0 | Lulus |
| Qct | UC16 Melihat Rekap Check QCT | 29 | 40 | 0 | Lulus |
| Qct | UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | 16 | 24 | 0 | Lulus |

## Skenario wajib yang diuji

1. **Akses dengan role/divisi/area yang benar berhasil**: setiap folder diawali login aktor yang benar, lalu halaman daftar, form, simpan, ubah, dan nonaktifkan/hapus diuji (status 200 atau redirect 302 ke halaman tujuan).
2. **Akses dengan role/divisi/area lain ditolak**: request `... ditolak membuka ...` (403) untuk role lain, `Operator Visual ditolak ...` / `Operator Lab ditolak ...` untuk area lain, dan `Tamu ... dialihkan ke login` (302 ke `/login`).
3. **Input tidak valid menghasilkan error validasi**: request `... tidak valid` dan `... ditolak` yang memeriksa pesan error per field.
4. **Ubah data yang sudah diverifikasi ditolak**: folder UC11 (cek troly, abnormal, sampel setelah laporan diverifikasi HOD) dan UC10 (absensi setelah rekap bulanan diverifikasi), ditambah UC07 (doffing yang sudah dicek QCT).

Pengujian yang belum tercakup collection: unggah file surat sakit/ijin di UC09 (request Postman memakai form urlencoded). Fitur ini diuji manual dengan curl: file tersimpan di `public/storage/absensi` dan bisa dibuka (HTTP 200, `application/pdf`).

## Cara menjalankan ulang

```
php artisan migrate:fresh --seed
php artisan serve

# Newman (bukti resmi, butuh Node 20/22):
npx newman run docs/pengujian/DTY-QCT.postman_collection.json -e docs/pengujian/local.postman_environment.json -r cli,json --reporter-json-export docs/pengujian/hasil/hasil-uji-YYYYMMDD-HHMM.json

# Runner pengganti (tanpa npm):
node docs/pengujian/alat/mini-runner.cjs docs/pengujian/DTY-QCT.postman_collection.json docs/pengujian/local.postman_environment.json docs/pengujian/hasil/hasil-uji-YYYYMMDD-HHMM-runner-pengganti.json
```

## Detail per Request

| UC | Route | Method | Skenario | Hasil | Catatan |
|---|---|---|---|---|---|
| UC01 Login | `/login` | GET | Buka halaman login | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login tanpa isi (validasi) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login dengan password salah | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login akun Non-Aktif ditolak | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login karyawan yang sudah keluar ditolak | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login tanpa token CSRF ditolak | Lulus | HTTP 419 |
| UC01 Login | `/dashboard` | GET | Akses dashboard tanpa login ditolak | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard direktur | Lulus | HTTP 200 |
| UC01 Login | `/login` | GET | Buka login saat sudah login dialihkan ke dashboard | Lulus | HTTP 302 |
| UC01 Login | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard staf | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard supervisor | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard hod | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard opvisual | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard oplab | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC01 Login | `/login` | GET | Buka halaman login (superadmin) | Lulus | HTTP 200 |
| UC01 Login | `/login` | POST | Login sebagai superadmin | Lulus | HTTP 302 |
| UC01 Login | `/dashboard` | GET | Dashboard superadmin | Lulus | HTTP 200 |
| UC01 Login | `/logout` | POST | Logout superadmin | Lulus | HTTP 302 |
| UC02 Logout | `/login` | GET | Buka halaman login | Lulus | HTTP 200 |
| UC02 Logout | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC02 Logout | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC02 Logout | `/logout` | POST | Logout tanpa token CSRF ditolak | Lulus | HTTP 419 |
| UC02 Logout | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC02 Logout | `/login` | GET | Pesan logout tampil di halaman login | Lulus | HTTP 200 |
| UC02 Logout | `/dashboard` | GET | Dashboard setelah logout ditolak | Lulus | HTTP 302 |
| UC02 Logout | `/logout` | POST | Logout tanpa login dialihkan ke login | Lulus | HTTP 302 |
| Akun Demo Superadmin | `/login` | GET | Buka halaman login (superadmin) | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/login` | POST | Login sebagai superadmin | Lulus | HTTP 302 |
| Akun Demo Superadmin | `/dashboard` | GET | Dashboard superadmin (ambil token) | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/admin/karyawan` | GET | Superadmin membuka menu Admin | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/produksi/doffing` | GET | Superadmin membuka menu Produksi | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/qct/laporan-check` | GET | Superadmin membuka menu HOD QCT | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/qct/check-batch` | GET | Superadmin membuka menu operator (tombol Visual & Lab tampil) | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/qct/rekap-check` | GET | Superadmin membuka menu Direktur | Lulus | HTTP 200 |
| Akun Demo Superadmin | `/logout` | POST | Logout superadmin | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | Daftar divisi | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi/create` | GET | Form tambah divisi | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | POST | Tambah divisi tidak valid | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | POST | Tambah divisi valid | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | Divisi baru tampil | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi/04/edit` | GET | Form ubah divisi | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/divisi/04` | PUT | Ubah divisi | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | Hasil ubah divisi tampil | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/jabatan` | GET | Daftar jabatan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/jabatan/create` | GET | Form tambah jabatan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/jabatan` | POST | Tambah jabatan tidak valid | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/jabatan` | POST | Tambah jabatan valid | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/jabatan/06/edit` | GET | Form ubah jabatan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/jabatan/06` | PUT | Ubah jabatan | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/jabatan` | GET | Hasil ubah jabatan tampil | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/area` | GET | Daftar area Admin | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/area/create` | GET | Form tambah area | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/area` | POST | Tambah area tidak valid | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/area` | POST | Tambah area valid | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/area/ARSIP/edit` | GET | Form ubah area | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/area/ARSIP` | PUT | Ubah area | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/area/ARSIP` | DELETE | Nonaktifkan area | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/area` | GET | Area tampil Non-Aktif | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | GET | Daftar karyawan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan?cari=Siti&status_aktif=Aktif` | GET | Filter karyawan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/2019-0001` | GET | Detail karyawan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/9999-9999` | GET | Detail karyawan tidak ada (404) | Lulus | HTTP 404 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/create` | GET | Form tambah karyawan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | POST | Tambah karyawan tidak valid | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | POST | Tambah karyawan valid (Tono) | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | POST | Tambah karyawan valid (Sari) | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/karyawan?cari=Tono` | GET | ID karyawan dibuat otomatis | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/2024-0001/edit` | GET | Form ubah karyawan | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/2024-0001` | PUT | Ubah karyawan | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/karyawan/2024-0001` | DELETE | Nonaktifkan karyawan | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/karyawan?cari=Tono` | GET | Karyawan tampil Non-Aktif | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | GET | supervisor ditolak membuka /admin/karyawan | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | supervisor ditolak membuka /admin/divisi | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | GET | opvisual ditolak membuka /admin/karyawan | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | opvisual ditolak membuka /admin/divisi | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | GET | direktur ditolak membuka /admin/karyawan | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/admin/divisi` | GET | direktur ditolak membuka /admin/divisi | Lulus | HTTP 403 |
| UC03 Kelola Data Karyawan | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC03 Kelola Data Karyawan | `/admin/karyawan` | GET | Tamu membuka /admin/karyawan dialihkan ke login | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | GET | Daftar akun | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user?role=supervisor` | GET | Filter akun per role | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user/create` | GET | Form tambah akun (hanya karyawan aktif tanpa akun) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | POST | Tambah akun tidak valid | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | POST | Role superadmin tidak bisa diberikan Staf | Gagal | HTTP 200; GAGAL: Halaman berisi: Role yang dipilih tidak valid. (tidak berisi "Role yang dipilih tidak valid.") |
| UC04 Kelola Akun Pengguna | `/admin/user` | POST | Tambah akun untuk karyawan Non-Aktif ditolak | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | POST | Tambah akun valid | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/admin/user?cari=sari` | GET | Akun baru tampil (simpan id) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user/{{user_baru}}/edit` | GET | Form ubah akun | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user/{{user_baru}}` | PUT | Ubah akun (password dikosongkan) | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/admin/user?cari=sari` | GET | Hasil ubah akun tampil | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user/{{user_baru}}` | DELETE | Nonaktifkan akun | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/admin/user?cari=sari` | GET | Akun tampil Non-Aktif | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user/2` | DELETE | Nonaktifkan akun sendiri ditolak | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/login` | GET | Buka halaman login (akun yang dinonaktifkan) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | POST | Akun yang dinonaktifkan tidak bisa login | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | GET | supervisor ditolak membuka /admin/user | Lulus | HTTP 403 |
| UC04 Kelola Akun Pengguna | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | GET | hod ditolak membuka /admin/user | Lulus | HTTP 403 |
| UC04 Kelola Akun Pengguna | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC04 Kelola Akun Pengguna | `/admin/user` | GET | oplab ditolak membuka /admin/user | Lulus | HTTP 403 |
| UC04 Kelola Akun Pengguna | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC04 Kelola Akun Pengguna | `/admin/user` | GET | Tamu membuka /admin/user dialihkan ke login | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi` | GET | Daftar rekap absensi QCT terverifikasi | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi?tahun=2026` | GET | Filter rekap per tahun | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi/RAB-209901` | GET | belum terverifikasi (404) | Lulus | HTTP 404 |
| UC05 Melihat Rekap Absensi QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi` | GET | hod ditolak membuka /admin/rekap-absensi | Lulus | HTTP 403 |
| UC05 Melihat Rekap Absensi QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi` | GET | direktur ditolak membuka /admin/rekap-absensi | Lulus | HTTP 403 |
| UC05 Melihat Rekap Absensi QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC05 Melihat Rekap Absensi QCT | `/admin/rekap-absensi` | GET | Tamu membuka /admin/rekap-absensi dialihkan ke login | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | Daftar benang | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang/create` | GET | Form tambah benang | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | POST | Tambah benang tidak valid | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | POST | Tambah benang valid | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang/M005/edit` | GET | Form ubah benang | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang/M005` | PUT | Ubah benang | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang/M005` | DELETE | Nonaktifkan benang | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang?cari=M005` | GET | Benang tampil Non-Aktif | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin` | GET | Daftar mesin | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin?line=403` | GET | Filter mesin per line | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin/401-05/edit` | GET | Form ubah status mesin | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin/401-05` | PUT | Ubah status mesin tidak valid | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin/401-05` | PUT | Ubah status mesin | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/mesin?status=Maintenance` | GET | Mesin tampil Maintenance | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | Daftar jadwal produksi | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi/create` | GET | Form tambah jadwal | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | POST | Tambah jadwal tidak valid | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | POST | Tambah jadwal dengan merge Non-Aktif ditolak | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | POST | Tambah jadwal valid | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi?tanggal=2026-10-22` | GET | Jadwal baru tampil (ID otomatis) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi/JDW-20261022-0001/edit` | GET | Form ubah jadwal | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi/JDW-20261022-0001` | PUT | Ubah jadwal | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi/JDW-20261022-0001` | DELETE | Hapus jadwal | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | Jadwal terhapus | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | staf ditolak membuka /produksi/benang | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | staf ditolak membuka /produksi/jadwal-produksi | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | hod ditolak membuka /produksi/benang | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | hod ditolak membuka /produksi/jadwal-produksi | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | opvisual ditolak membuka /produksi/benang | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | opvisual ditolak membuka /produksi/jadwal-produksi | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | direktur ditolak membuka /produksi/benang | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/produksi/jadwal-produksi` | GET | direktur ditolak membuka /produksi/jadwal-produksi | Lulus | HTTP 403 |
| UC06 Kelola Jadwal Produksi | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC06 Kelola Jadwal Produksi | `/produksi/benang` | GET | Tamu membuka /produksi/benang dialihkan ke login | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | Daftar doffing | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing?tanggal=2026-10-21&shift=S2` | GET | Filter doffing tanggal & shift | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/create` | GET | Form tambah doffing | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | POST | Tambah doffing tidak valid | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | POST | Tambah doffing valid | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/produksi/doffing?tanggal=2026-10-22` | GET | Doffing baru tampil (ID otomatis) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/DOF-20261022-S1-0001/edit` | GET | Form ubah doffing | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/DOF-20261022-S1-0001` | PUT | Ubah doffing | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/produksi/doffing?tanggal=2026-10-22` | GET | Hasil ubah doffing tampil | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/DOF-20261022-S1-0001` | DELETE | Hapus doffing | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | Doffing terhapus | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/DOF-20261021-S1-0001/edit` | GET | Troly yang sudah dicek QCT tidak bisa diubah | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing/DOF-20261021-S1-0001` | DELETE | Troly yang sudah dicek QCT tidak bisa dihapus | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | staf ditolak membuka /produksi/doffing | Lulus | HTTP 403 |
| UC07 Kelola Data Doffing | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | oplab ditolak membuka /produksi/doffing | Lulus | HTTP 403 |
| UC07 Kelola Data Doffing | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | direktur ditolak membuka /produksi/doffing | Lulus | HTTP 403 |
| UC07 Kelola Data Doffing | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC07 Kelola Data Doffing | `/produksi/doffing` | GET | Tamu membuka /produksi/doffing dialihkan ke login | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area` | GET | Daftar area QCT | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area/create` | GET | Form tambah area QCT | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area` | POST | Tambah area QCT tidak valid | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area` | POST | Tambah area QCT valid | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/area/INS/edit` | GET | Form ubah area QCT | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area/INS` | PUT | Ubah area QCT | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/area/INS` | DELETE | Nonaktifkan area QCT | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/area` | GET | Area QCT tampil Non-Aktif | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/area/KPG/edit` | GET | Area milik Admin tidak bisa dibuka HOD (404) | Lulus | HTTP 404 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | Daftar grade | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade/create` | GET | Form tambah grade | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | POST | Tambah grade tidak valid | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | POST | Tambah grade valid | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/grade/BS/edit` | GET | Form ubah grade | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade/BS` | PUT | Ubah grade | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/grade/BS` | DELETE | Nonaktifkan grade | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | Grade tampil Non-Aktif | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | Daftar jenis abnormal | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal/create` | GET | Form tambah jenis abnormal | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | POST | Tambah jenis abnormal tidak valid | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | POST | Tambah jenis abnormal valid | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal?cari=Kotor` | GET | Kode jenis abnormal dibuat otomatis | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal/VS04/edit` | GET | Form ubah jenis abnormal | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal/VS04` | PUT | Ubah jenis abnormal | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal/VS04` | DELETE | Nonaktifkan jenis abnormal | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | Jenis abnormal tampil Non-Aktif | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | supervisor ditolak membuka /qct/grade | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | supervisor ditolak membuka /qct/jenis-abnormal | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | opvisual ditolak membuka /qct/grade | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | opvisual ditolak membuka /qct/jenis-abnormal | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | staf ditolak membuka /qct/grade | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | staf ditolak membuka /qct/jenis-abnormal | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | direktur ditolak membuka /qct/grade | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/qct/jenis-abnormal` | GET | direktur ditolak membuka /qct/jenis-abnormal | Lulus | HTTP 403 |
| UC08 Kelola Data Master QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC08 Kelola Data Master QCT | `/qct/grade` | GET | Tamu membuka /qct/grade dialihkan ke login | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | Daftar absensi QCT | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi?tanggal=2026-10-21&shift=S2` | GET | Filter absensi tanggal & shift | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi/create` | GET | Form tambah absensi | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | POST | Tambah absensi tidak valid | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | POST | Absensi karyawan non-QCT ditolak | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | POST | Absensi ganda (karyawan, tanggal, shift sama) ditolak | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | POST | Tambah absensi valid | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi?tanggal=2026-10-20` | GET | Absensi baru tampil | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi/ABS-20261020-0001/edit` | GET | Form ubah absensi | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi/ABS-20261020-0001` | PUT | Ubah absensi (jadi Sakit) | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi?tanggal=2026-10-20` | GET | Hasil ubah absensi tampil | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi/ABS-20261020-0001` | DELETE | Hapus absensi | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | Absensi terhapus | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | opvisual ditolak membuka /qct/absensi | Lulus | HTTP 403 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | supervisor ditolak membuka /qct/absensi | Lulus | HTTP 403 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | staf ditolak membuka /qct/absensi | Lulus | HTTP 403 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC09 Kelola Absensi & Penugasan Area QCT | `/qct/absensi` | GET | Tamu membuka /qct/absensi dialihkan ke login | Lulus | HTTP 302 |
| UC12 Cek Visual | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC12 Cek Visual | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | GET | Daftar troly (operator Visual) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch?tanggal=2026-10-21&tahap=belum` | GET | Filter troly belum dicek | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/create` | GET | Form cek visual tanpa memilih troly | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/create?id_doffing=DOF-20261021-S2-0002` | GET | Form cek visual troly T-05 | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | POST | Simpan cek visual tidak valid | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | POST | Simpan cek visual NG | Lulus | HTTP 302 |
| UC12 Cek Visual | `/qct/check-batch/BTH-20261021-S2-0002` | GET | Detail troly setelah cek visual | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/create?id_doffing=DOF-20261021-S2-0002` | GET | Troly yang sudah dicek tidak bisa dicek ulang | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/BTH-20261021-S2-0002/edit` | GET | Form ubah hasil visual | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/BTH-20261021-S2-0002` | PUT | Ubah hasil visual | Lulus | HTTP 302 |
| UC12 Cek Visual | `/qct/check-batch/create?id_doffing=DOF-20261021-S2-0003` | GET | Cek visual troly T-06 (untuk dihapus) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | POST | Simpan cek visual OK | Lulus | HTTP 302 |
| UC12 Cek Visual | `/qct/check-batch/BTH-20261021-S2-0003` | DELETE | Hapus cek troly (belum ada abnormal) | Lulus | HTTP 302 |
| UC12 Cek Visual | `/qct/check-batch` | GET | Cek troly terhapus | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch/BTH-20261021-S2-0001/denier` | GET | Operator Visual ditolak membuka form cek denier | Lulus | HTTP 403 |
| UC12 Cek Visual | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC12 Cek Visual | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC12 Cek Visual | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | GET | hod ditolak membuka /qct/check-batch | Lulus | HTTP 403 |
| UC12 Cek Visual | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC12 Cek Visual | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC12 Cek Visual | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC12 Cek Visual | `/qct/check-batch` | GET | supervisor ditolak membuka /qct/check-batch | Lulus | HTTP 403 |
| UC12 Cek Visual | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC12 Cek Visual | `/qct/check-batch` | GET | Tamu membuka /qct/check-batch dialihkan ke login | Lulus | HTTP 302 |
| UC13 Cek Denier | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC13 Cek Denier | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch?tahap=lab` | GET | Daftar troly (operator Lab) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/create?id_doffing=DOF-20261021-S2-0003` | GET | Operator Lab ditolak membuat cek visual | Lulus | HTTP 403 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | GET | Form cek denier troly T-04 | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | PUT | Simpan cek denier tidak valid | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | PUT | Grade Non-Aktif ditolak | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | PUT | Simpan cek denier valid | Lulus | HTTP 302 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001` | GET | Detail troly setelah cek denier | Lulus | HTTP 200 |
| UC13 Cek Denier | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC13 Cek Denier | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC13 Cek Denier | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | GET | hod ditolak membuka /qct/check-batch/BTH-20261021-S2-0001/denier | Lulus | HTTP 403 |
| UC13 Cek Denier | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC13 Cek Denier | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC13 Cek Denier | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | GET | staf ditolak membuka /qct/check-batch/BTH-20261021-S2-0001/denier | Lulus | HTTP 403 |
| UC13 Cek Denier | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC13 Cek Denier | `/qct/check-batch/BTH-20261021-S2-0001/denier` | GET | Tamu membuka /qct/check-batch/BTH-20261021-S2-0001/denier dialihkan ke login | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | GET | Daftar abnormal | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/create?id_doffing=DOF-20261021-S2-0002` | GET | Form tambah abnormal | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | POST | Tambah abnormal tidak valid | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | POST | Tambah abnormal valid (status Pending) | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/check-batch/BTH-20261021-S2-0002` | GET | Abnormal baru tampil di detail troly | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001/edit` | GET | Form ubah abnormal | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001` | PUT | Ubah abnormal | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S1-0001/edit` | GET | Abnormal yang sudah diuji Lab tidak bisa diubah Visual | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/create?id_doffing=DOF-20261021-S2-0002` | GET | Form tambah abnormal kedua | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | POST | Tambah abnormal kedua | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0002` | DELETE | Hapus abnormal (masih Pending) | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | GET | Abnormal terhapus | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001/lab` | GET | Operator Visual ditolak membuka form uji Lab | Lulus | HTTP 403 |
| UC14 Kelola Data Abnormal Benang | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal?status=Pending` | GET | Daftar abnormal Pending (operator Lab) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/create` | GET | Operator Lab ditolak menambah abnormal | Lulus | HTTP 403 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001/lab` | GET | Form uji Lab abnormal | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001/lab` | PUT | Simpan uji Lab tidak valid | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal/ABN-20261021-S2-0001/lab` | PUT | Simpan uji Lab valid (Cutting) | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal?tanggal=2026-10-21&shift=S2` | GET | Abnormal tampil Cutting | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | GET | hod ditolak membuka /qct/abnormal | Lulus | HTTP 403 |
| UC14 Kelola Data Abnormal Benang | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | GET | supervisor ditolak membuka /qct/abnormal | Lulus | HTTP 403 |
| UC14 Kelola Data Abnormal Benang | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC14 Kelola Data Abnormal Benang | `/qct/abnormal` | GET | Tamu membuka /qct/abnormal dialihkan ke login | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | Daftar sampel | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel?merge=M002` | GET | Filter sampel per merge | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel/create` | GET | Form tambah sampel | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | POST | Tambah sampel tidak valid | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | POST | Sampel ganda (merge, tanggal, shift sama) ditolak | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | POST | Tambah sampel valid (OK) | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/qct/sampel?tanggal=2026-10-21&shift=S2` | GET | Sampel baru tampil | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel/SMP-20261021-S2-0001/edit` | GET | Form ubah sampel | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel/SMP-20261021-S2-0001` | PUT | Ubah sampel (ada abnormal) | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/qct/sampel?tanggal=2026-10-21&shift=S2` | GET | Hasil ubah sampel tampil | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel/SMP-20261021-S2-0001` | DELETE | Hapus sampel | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | Sampel terhapus | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | Operator Visual juga bisa membuka sampel | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | hod ditolak membuka /qct/sampel | Lulus | HTTP 403 |
| UC15 Kelola Data Sampel | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | supervisor ditolak membuka /qct/sampel | Lulus | HTTP 403 |
| UC15 Kelola Data Sampel | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC15 Kelola Data Sampel | `/qct/sampel` | GET | Tamu membuka /qct/sampel dialihkan ke login | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check` | GET | Daftar laporan per tanggal & shift | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check?bulan=2026-10&status=belum` | GET | Filter laporan belum diverifikasi | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S2` | GET | Laporan S2 belum lengkap | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S2/verifikasi` | POST | Verifikasi laporan yang belum lengkap ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S1` | GET | Laporan S1 lengkap (angka dihitung) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S1/verifikasi` | POST | Verifikasi laporan S1 | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S1` | GET | Laporan S1 Terverifikasi | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20261021-S1/verifikasi` | POST | Verifikasi ulang ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check/LAP-20990101-S1` | GET | Laporan yang tidak ada (404) | Lulus | HTTP 404 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/check-batch/BTH-20261021-S1-0001/edit` | GET | Ubah cek visual yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/check-batch/BTH-20261021-S1-0001` | PUT | Simpan ubah cek visual yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/check-batch/BTH-20261021-S1-0001` | DELETE | Hapus cek troly yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/check-batch/BTH-20261021-S1-0001` | GET | Detail troly terkunci (tombol ubah disembunyikan) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/abnormal/create?id_doffing=DOF-20261021-S1-0001` | GET | Form tambah abnormal troly S1 | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/abnormal` | POST | Tambah abnormal pada data terverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/check-batch/BTH-20261021-S1-0002/denier` | GET | Cek denier yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/abnormal/ABN-20261021-S1-0001/lab` | GET | Uji Lab abnormal yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/sampel/SMP-20261021-S1-0001/edit` | GET | Ubah sampel yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/sampel/SMP-20261021-S1-0001` | DELETE | Hapus sampel yang sudah diverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/sampel/create` | GET | Form tambah sampel | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/sampel` | POST | Tambah sampel pada data terverifikasi ditolak | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check` | GET | opvisual ditolak membuka /qct/laporan-check | Lulus | HTTP 403 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check` | GET | supervisor ditolak membuka /qct/laporan-check | Lulus | HTTP 403 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check` | GET | direktur ditolak membuka /qct/laporan-check | Lulus | HTTP 403 |
| UC11 Verifikasi Laporan Check QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC11 Verifikasi Laporan Check QCT | `/qct/laporan-check` | GET | Tamu membuka /qct/laporan-check dialihkan ke login | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi` | GET | Daftar periode rekap absensi | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202610` | GET | Rekap bulan berjalan belum bisa diverifikasi | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202610/verifikasi` | POST | Verifikasi rekap bulan berjalan ditolak | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202609` | GET | Rekap absensi September (dihitung) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202609/verifikasi` | POST | Verifikasi rekap absensi September | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202609` | GET | Rekap absensi Terverifikasi + PDF | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/storage/rekap-absensi/RAB-202609.pdf` | GET | File PDF rekap absensi bisa diunduh | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi/RAB-202609/verifikasi` | POST | Verifikasi ulang ditolak | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/absensi?tanggal=2026-09-15` | GET | Absensi September tampil terkunci | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/absensi/ABS-20260915-0001/edit` | GET | Ubah absensi terverifikasi ditolak | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/absensi/ABS-20260915-0001` | DELETE | Hapus absensi terverifikasi ditolak | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/absensi/create` | GET | Form tambah absensi | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/absensi` | POST | Tambah absensi pada periode terverifikasi ditolak | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/admin/rekap-absensi` | GET | Rekap terverifikasi tampil untuk Staf Admin | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/admin/rekap-absensi/RAB-202609` | GET | Staf Admin melihat isi rekap | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/admin/rekap-absensi/RAB-202610` | GET | Rekap Oktober (belum terverifikasi) tidak tampil untuk Staf Admin (404) | Lulus | HTTP 404 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | GET | Buka halaman login (opvisual) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | POST | Login sebagai opvisual | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/dashboard` | GET | Dashboard opvisual (ambil token) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi` | GET | opvisual ditolak membuka /qct/rekap-absensi | Lulus | HTTP 403 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/logout` | POST | Logout opvisual | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi` | GET | staf ditolak membuka /qct/rekap-absensi | Lulus | HTTP 403 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi` | GET | direktur ditolak membuka /qct/rekap-absensi | Lulus | HTTP 403 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC10 Verifikasi Rekap Absensi Bulanan QCT | `/qct/rekap-absensi` | GET | Tamu membuka /qct/rekap-absensi dialihkan ke login | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | Daftar rekap check per bulan | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check/RCK-202609` | GET | Rekap check September (dihitung) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check/RCK-202610` | GET | Rekap check Oktober (ada laporan belum diverifikasi) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check/RCK-209901` | GET | Rekap periode tanpa data (404) | Lulus | HTTP 404 |
| UC16 Melihat Rekap Check QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | hod ditolak membuka /qct/rekap-check | Lulus | HTTP 403 |
| UC16 Melihat Rekap Check QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/login` | GET | Buka halaman login (staf) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/login` | POST | Login sebagai staf | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/dashboard` | GET | Dashboard staf (ambil token) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | staf ditolak membuka /qct/rekap-check | Lulus | HTTP 403 |
| UC16 Melihat Rekap Check QCT | `/logout` | POST | Logout staf | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/login` | GET | Buka halaman login (supervisor) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/login` | POST | Login sebagai supervisor | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/dashboard` | GET | Dashboard supervisor (ambil token) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | supervisor ditolak membuka /qct/rekap-check | Lulus | HTTP 403 |
| UC16 Melihat Rekap Check QCT | `/logout` | POST | Logout supervisor | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/login` | GET | Buka halaman login (oplab) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/login` | POST | Login sebagai oplab | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/dashboard` | GET | Dashboard oplab (ambil token) | Lulus | HTTP 200 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | oplab ditolak membuka /qct/rekap-check | Lulus | HTTP 403 |
| UC16 Melihat Rekap Check QCT | `/logout` | POST | Logout oplab | Lulus | HTTP 302 |
| UC16 Melihat Rekap Check QCT | `/qct/rekap-check` | GET | Tamu membuka /qct/rekap-check dialihkan ke login | Lulus | HTTP 302 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/login` | GET | Buka halaman login (direktur) | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/login` | POST | Login sebagai direktur | Lulus | HTTP 302 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/dashboard` | GET | Dashboard direktur (ambil token) | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202610` | GET | Buka rekap Oktober | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202610/verifikasi` | POST | Verifikasi rekap yang laporannya belum lengkap ditolak | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202609` | GET | Buka rekap September | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202609/verifikasi` | POST | Verifikasi & arsipkan rekap September | Lulus | HTTP 302 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202609` | GET | Rekap Terverifikasi + arsip PDF | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/storage/rekap-check/RCK-202609.pdf` | GET | UC18 file arsip PDF bisa diunduh | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202609/verifikasi` | POST | Verifikasi ulang ditolak | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/logout` | POST | Logout direktur | Lulus | HTTP 302 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/login` | GET | Buka halaman login (hod) | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/login` | POST | Login sebagai hod | Lulus | HTTP 302 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/dashboard` | GET | Dashboard hod (ambil token) | Lulus | HTTP 200 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/qct/rekap-check/RCK-202610/verifikasi` | POST | HOD ditolak memverifikasi rekap check | Lulus | HTTP 403 |
| UC17-UC18 Verifikasi & Arsipkan Rekap Check QCT | `/logout` | POST | Logout hod | Lulus | HTTP 302 |
