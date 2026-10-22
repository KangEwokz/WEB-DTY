# Skema Database (sumber: RDTY-FINAL.xlsx)

Kolom: Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan.
Semua tabel memakai kolom audit sesuai yang tertulis (created_by/updated_by → karyawan, created_at/updated_at → timestamps Laravel).

## 1. Admin


### 1.1 Divisi  (divisi) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_divisi | Char | 2 | PK |  | 01 = Admin, 02 = Produksi, 03 = QCT |
| kd_divisi | Varchar | 5 | UQ |  | ADM, PRD, QCT |
| nm_divisi | Varchar | 50 |  |  | Nama divisi |

### 1.2 Jabatan  (jabatan) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_jabatan | Char | 2 | PK |  | 01, 02, ... |
| nm_jabatan | Varchar | 30 |  |  | Direktur, HOD, Supervisor, Staf, Operator, ... |
| id_divisi | Char | 2 | FK | divisi | Jabatan milik divisi tertentu (Direktur boleh kosong) |

### 1.3 Karyawan  (karyawan) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_karyawan | Varchar | 10 | PK |  | Tahun masuk + urut, contoh 2019-0001 |
| nama_karyawan | Varchar | 50 |  |  |  |
| jns_kelamin | Enum | 'L','P' |  |  |  |
| id_divisi | Char | 2 | FK | divisi |  |
| id_jabatan | Char | 2 | FK | jabatan |  |
| id_area | Varchar | 10 | FK | area | Area tetap/default karyawan; dipakai bila absensi shift belum diisi |
| grup | Enum | 'A'..'G','NG' |  |  | Grup kerja shift; NG = non-grup |
| tgl_masuk | Date | yyyy-mm-dd |  |  |  |
| no_hp | Varchar | 15 |  |  |  |
| email | Varchar | 100 |  |  |  |
| status_aktif | Enum | 'Aktif','Non-Aktif' |  |  | Karyawan keluar dinonaktifkan, tidak dihapus |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 1.4 Users  (users) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id | Bigint |  | PK |  | Auto increment bawaan Laravel |
| username | Varchar | 20 | UQ |  |  |
| password | Varchar | 255 |  |  | Hash bcrypt |
| id_karyawan | Varchar | 10 | FK, UQ | karyawan | Satu karyawan satu akun |
| role | Enum | 'direktur','hod','supervisor','staf','operator' |  |  | Role umum; akses menu = role + divisi + area |
| status | Enum | 'Aktif','Non-Aktif' |  |  |  |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 1.5 Area  (area) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_area | Varchar | 10 | PK |  | KPG = Kepegawaian, VIS = Visual, LAB = Lab |
| nm_area | Varchar | 50 |  |  |  |
| id_divisi | Char | 2 | FK | divisi | Area milik divisi (KPG→Admin, VIS & LAB→QCT) |
| status_aktif | Enum | 'Aktif','Non-Aktif' |  |  | Master tidak dihapus, cukup dinonaktifkan (ditambahkan saat implementasi) |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

## 2. Produksi


### 2.1 Benang  (benang) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| merge | Varchar | 10 | PK |  | Kode merge benang |
| denier | Int |  |  |  |  |
| filamen | Int |  |  |  |  |
| warna_ppt | Varchar | 20 |  |  | Warna kertas/ppt penanda merge |
| status_aktif | Enum | 'Aktif','Non-Aktif' |  |  | Master tidak dihapus, cukup dinonaktifkan (ditambahkan saat implementasi) |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 2.2 Mesin  (mesin) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| kode_mesin | Varchar | 8 | PK |  | Contoh 401-01 (line-posisi) |
| line | Smallint |  |  |  | 401, 402, 403 (sistem). Diubah dari Tinyint karena Tinyint maks 255 |
| posisi | Tinyint |  |  |  | 1–10 |
| status | Enum | 'Running','Stop','Maintenance' |  |  |  |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 2.3 Jadwal Produksi  (jadwal_produksi) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_jadwal | Varchar | 20 | PK |  | JDW-tanggal-urut |
| merge | Varchar | 10 | FK | benang |  |
| tgl_produksi | Date | yyyy-mm-dd |  |  |  |
| line | Smallint |  |  |  | Line/sistem 401–403. Diubah dari Tinyint karena Tinyint maks 255 |
| pos_awal | Tinyint |  |  |  | Posisi mesin awal |
| pos_akhir | Tinyint |  |  |  | Posisi mesin akhir |
| customer | Varchar | 100 |  |  |  |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 2.4 Doffing  (doffing) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_doffing | Varchar | 20 | PK |  | DOF-tanggal-shift-urut |
| tgl_doffing | Date | yyyy-mm-dd |  |  |  |
| shift | Enum | 'S1','S2','S3' |  |  |  |
| nmr_troly | Varchar | 10 |  |  |  |
| merge | Varchar | 10 | FK | benang |  |
| jml_bobbin | Int |  |  |  | Jumlah bobbin dalam troly |
| berat_troly | Decimal | 6,2 |  |  | kg |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

## 3. QCT


### 3.1 Grade  (grade) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_grade | Varchar | 5 | PK |  | AX, SS, CL, ... |
| keterangan | Varchar | 100 |  |  | Arti grade |
| status_aktif | Enum | 'Aktif','Non-Aktif' |  |  | Master tidak dihapus, cukup dinonaktifkan (ditambahkan saat implementasi) |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.2 Jenis Abnormal  (jenis_abnormal) — master

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_jns_abn | Varchar | 5 | PK |  | VS01, VS02, ... |
| nm_abnormal | Varchar | 50 | UQ |  | Handling, OTY, Hairy, ... |
| status_aktif | Enum | 'Aktif','Non-Aktif' |  |  | Master tidak dihapus, cukup dinonaktifkan (ditambahkan saat implementasi) |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.3 Absensi  (absensi) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_absen | Varchar | 20 | PK |  | ABS-tanggal-urut |
| id_karyawan | Varchar | 10 | FK | karyawan | Karyawan QCT |
| tgl_absen | Date | yyyy-mm-dd |  |  |  |
| shift | Enum | 'S1','S2','S3','GS' |  |  |  |
| id_area | Varchar | 10 | FK | area | Penugasan area shift ini (VIS/LAB), menentukan menu operator |
| kehadiran | Enum | 'Hadir','Sakit','Ijin','Alfa','Cuti' |  |  |  |
| dok_file | Varchar | 255 |  |  | Surat sakit/ijin (boleh kosong) |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.4 Rekap Absensi  (rekap_absensi) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_rekap | Varchar | 15 | PK |  | RAB-YYYYMM |
| periode | Varchar | 7 | UQ |  | Format 'YYYY-MM' |
| status | Enum | 'Draft','Terverifikasi' |  |  | Setelah Terverifikasi, absensi periode itu terkunci dan bisa dilihat Staf Admin |
| diverifikasi_oleh | Varchar | 10 | FK | karyawan | HOD QCT |
| waktu_verifikasi | Datetime |  |  |  |  |
| file_pdf | Varchar | 255 |  |  | Hasil cetak rekap |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.5 Check Batch  (check_batch) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_check | Varchar | 25 | PK |  | BTH-tanggal-shift-urut |
| id_doffing | Varchar | 20 | FK, UQ | doffing | Satu troly dicek satu kali |
| hasil_visual | Enum | 'OK','NG' |  |  | Diisi Operator area Visual |
| hasil_denier | Enum | 'OK','NG' |  |  | Diisi Operator area Lab |
| id_grade | Varchar | 5 | FK | grade | Kriteria penentuan grade menyusul |
| status | Enum | 'Packing','Hold' |  |  | Tindak lanjut troly |
| insp_visual | Varchar | 10 | FK | karyawan | Operator QCT area Visual |
| insp_lab | Varchar | 10 | FK | karyawan | Operator QCT area Lab |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.6 Abnormal  (abnormal) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_abnormal | Varchar | 25 | PK |  | ABN-tanggal-shift-urut |
| id_doffing | Varchar | 20 | FK | doffing | Troly asal bobbin |
| kode_mesin | Varchar | 8 | FK | mesin | Mesin asal bobbin |
| id_jns_abn | Varchar | 5 | FK | jenis_abnormal |  |
| act_denier | Decimal | 6,2 |  |  |  |
| elong | Decimal | 5,2 |  |  |  |
| tenacity | Decimal | 5,2 |  |  |  |
| dti | Decimal | 5,2 |  |  |  |
| id_grade | Varchar | 5 | FK | grade |  |
| status | Enum | 'Pending','Packing','Cutting' |  |  |  |
| insp_visual | Varchar | 10 | FK | karyawan |  |
| insp_lab | Varchar | 10 | FK | karyawan |  |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.7 Sampel  (sampel) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_sampel | Varchar | 20 | PK |  | SMP-tanggal-shift-urut |
| merge | Varchar | 10 | FK | benang |  |
| tgl_sampel | Date | yyyy-mm-dd |  |  |  |
| shift | Enum | 'S1','S2','S3' |  |  |  |
| id_jns_abn | Varchar | 5 | FK | jenis_abnormal | Kosong = sampel OK |
| act_denier | Decimal | 6,2 |  |  |  |
| elong | Decimal | 5,2 |  |  |  |
| tenacity | Decimal | 5,2 |  |  |  |
| dti | Decimal | 5,2 |  |  |  |
| id_grade | Varchar | 5 | FK | grade |  |
| insp_visual | Varchar | 10 | FK | karyawan |  |
| insp_lab | Varchar | 10 | FK | karyawan |  |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.8 Laporan Check  (laporan_check) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_laporan | Varchar | 20 | PK |  | LAP-tanggal-shift |
| tgl_laporan | Date | yyyy-mm-dd |  |  |  |
| shift | Enum | 'S1','S2','S3' |  |  |  |
| status | Enum | 'Draft','Terverifikasi' |  |  | Angka dihitung dari check_batch, abnormal, sampel pada tanggal & shift tsb |
| diverifikasi_oleh | Varchar | 10 | FK | karyawan | HOD QCT |
| waktu_verifikasi | Datetime |  |  |  |  |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |

### 3.9 Rekap Check  (rekap_check) — transaksi

| Kolom | Tipe | Panjang/Nilai | Kunci | Rujukan | Keterangan |
|---|---|---|---|---|---|
| id_rekap_check | Varchar | 15 | PK |  | RCK-YYYYMM |
| periode | Varchar | 7 | UQ |  | Format 'YYYY-MM' |
| status | Enum | 'Draft','Terverifikasi' |  |  | Terverifikasi = diarsipkan |
| diverifikasi_oleh | Varchar | 10 | FK | karyawan | Direktur |
| waktu_verifikasi | Datetime |  |  |  |  |
| file_pdf | Varchar | 255 |  |  | File arsip rekap |
| created_by, updated_by | Varchar | 10 | FK | karyawan | Karyawan penginput / pengubah |
| created_at, updated_at | Timestamp |  |  |  | Otomatis dari Laravel |
