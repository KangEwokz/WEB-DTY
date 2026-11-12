<?php

use Illuminate\Support\Facades\Route;                            // class Route untuk mendaftarkan URL
use Modules\Produksi\Http\Controllers\AbsensiController;         // panggil AbsensiController
use Modules\Produksi\Http\Controllers\BenangController;          // panggil BenangController
use Modules\Produksi\Http\Controllers\DoffingController;         // panggil DoffingController
use Modules\Produksi\Http\Controllers\JenisAbnormalController;   // panggil JenisAbnormalController
use Modules\Produksi\Http\Controllers\LaporanShiftController;    // panggil LaporanShiftController
use Modules\Produksi\Http\Controllers\MesinController;           // panggil MesinController
use Modules\Produksi\Http\Controllers\RekapBulananController;    // panggil RekapBulananController
use Modules\Produksi\Http\Controllers\TrolyController;           // panggil TrolyController

Route::middleware(['auth', 'akses'])->prefix('produksi')->group(function () { // wajib login + punya permission; prefix = semua URL diawali /produksi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('produksi.absensi.index');                // GET    /produksi/absensi                     -> daftar absensi per shift
    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('produksi.absensi.create');       // GET    /produksi/absensi/create              -> form absensi & penempatan
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('produksi.absensi.store');               // POST   /produksi/absensi                     -> simpan absensi
    Route::get('/absensi/{id}', [AbsensiController::class, 'show'])->name('produksi.absensi.show');             // GET    /produksi/absensi/ABS-20261112-S1     -> detail absensi
    Route::get('/absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('produksi.absensi.edit');        // GET    /produksi/absensi/ABS-20261112-S1/edit -> form ubah
    Route::put('/absensi/{id}', [AbsensiController::class, 'update'])->name('produksi.absensi.update');         // PUT    /produksi/absensi/ABS-20261112-S1     -> simpan perubahan
    Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('produksi.absensi.destroy');    // DELETE /produksi/absensi/ABS-20261112-S1     -> hapus absensi

    Route::get('/doffing', [DoffingController::class, 'index'])->name('produksi.doffing.index');                // GET    /produksi/doffing                     -> daftar doffing
    Route::get('/doffing/create', [DoffingController::class, 'create'])->name('produksi.doffing.create');       // GET    /produksi/doffing/create              -> form tambah
    Route::post('/doffing', [DoffingController::class, 'store'])->name('produksi.doffing.store');               // POST   /produksi/doffing                     -> simpan doffing
    Route::get('/doffing/{id}/edit', [DoffingController::class, 'edit'])->name('produksi.doffing.edit');        // GET    /produksi/doffing/DOF-20261112-S1-001/edit -> form ubah
    Route::put('/doffing/{id}', [DoffingController::class, 'update'])->name('produksi.doffing.update');         // PUT    /produksi/doffing/DOF-20261112-S1-001 -> simpan perubahan
    Route::delete('/doffing/{id}', [DoffingController::class, 'destroy'])->name('produksi.doffing.destroy');    // DELETE /produksi/doffing/DOF-20261112-S1-001 -> hapus

    Route::get('/laporan', [LaporanShiftController::class, 'index'])->name('produksi.laporan.index');           // GET    /produksi/laporan                     -> daftar laporan akhir shift
    Route::get('/laporan/create', [LaporanShiftController::class, 'create'])->name('produksi.laporan.create');  // GET    /produksi/laporan/create              -> form laporan
    Route::post('/laporan', [LaporanShiftController::class, 'store'])->name('produksi.laporan.store');          // POST   /produksi/laporan                     -> simpan laporan
    Route::get('/laporan/{id}', [LaporanShiftController::class, 'show'])->name('produksi.laporan.show');        // GET    /produksi/laporan/LAP-20261112-S1     -> detail laporan
    Route::get('/laporan/{id}/edit', [LaporanShiftController::class, 'edit'])->name('produksi.laporan.edit');   // GET    /produksi/laporan/LAP-20261112-S1/edit -> form ubah
    Route::put('/laporan/{id}', [LaporanShiftController::class, 'update'])->name('produksi.laporan.update');    // PUT    /produksi/laporan/LAP-20261112-S1     -> simpan perubahan
    Route::delete('/laporan/{id}', [LaporanShiftController::class, 'destroy'])->name('produksi.laporan.destroy'); // DELETE /produksi/laporan/LAP-20261112-S1   -> hapus laporan

    Route::get('/rekap-bulanan', [RekapBulananController::class, 'index'])->name('produksi.rekap-bulanan.index');      // GET /produksi/rekap-bulanan?bulan=2026-11       -> pilih bulan + pratinjau
    Route::get('/rekap-bulanan/cetak', [RekapBulananController::class, 'cetak'])->name('produksi.rekap-bulanan.cetak'); // GET /produksi/rekap-bulanan/cetak?bulan=2026-11 -> halaman cetak A4

    Route::get('/benang', [BenangController::class, 'index'])->name('produksi.benang.index');                  // GET    /produksi/benang                      -> daftar benang
    Route::get('/benang/create', [BenangController::class, 'create'])->name('produksi.benang.create');         // GET    /produksi/benang/create               -> form tambah
    Route::post('/benang', [BenangController::class, 'store'])->name('produksi.benang.store');                 // POST   /produksi/benang                      -> simpan benang baru
    Route::get('/benang/{id}/edit', [BenangController::class, 'edit'])->name('produksi.benang.edit');          // GET    /produksi/benang/M150A/edit           -> form ubah
    Route::put('/benang/{id}', [BenangController::class, 'update'])->name('produksi.benang.update');           // PUT    /produksi/benang/M150A                -> simpan perubahan
    Route::patch('/benang/{id}/status', [BenangController::class, 'status'])->name('produksi.benang.status');  // PATCH  /produksi/benang/M150A/status         -> ganti Aktif <-> Non-Aktif

    Route::get('/mesin', [MesinController::class, 'index'])->name('produksi.mesin.index');                     // GET    /produksi/mesin                       -> daftar mesin
    Route::get('/mesin/create', [MesinController::class, 'create'])->name('produksi.mesin.create');            // GET    /produksi/mesin/create                -> form tambah mesin
    Route::post('/mesin', [MesinController::class, 'store'])->name('produksi.mesin.store');                    // POST   /produksi/mesin                       -> simpan mesin baru
    Route::get('/mesin/{id}/edit', [MesinController::class, 'edit'])->name('produksi.mesin.edit');             // GET    /produksi/mesin/MCN-001/edit          -> form ubah line, posisi, status
    Route::put('/mesin/{id}', [MesinController::class, 'update'])->name('produksi.mesin.update');              // PUT    /produksi/mesin/MCN-001               -> simpan perubahan

    Route::get('/troly', [TrolyController::class, 'index'])->name('produksi.troly.index');                     // GET    /produksi/troly                       -> daftar troly
    Route::get('/troly/create', [TrolyController::class, 'create'])->name('produksi.troly.create');            // GET    /produksi/troly/create                -> form tambah
    Route::post('/troly', [TrolyController::class, 'store'])->name('produksi.troly.store');                    // POST   /produksi/troly                       -> simpan troly baru
    Route::get('/troly/{id}/edit', [TrolyController::class, 'edit'])->name('produksi.troly.edit');             // GET    /produksi/troly/TRA-001/edit          -> form ubah kapasitas
    Route::put('/troly/{id}', [TrolyController::class, 'update'])->name('produksi.troly.update');              // PUT    /produksi/troly/TRA-001               -> simpan perubahan
    Route::patch('/troly/{id}/status', [TrolyController::class, 'status'])->name('produksi.troly.status');     // PATCH  /produksi/troly/TRA-001/status        -> ganti Aktif <-> Non-Aktif

    Route::get('/jenis-abnormal', [JenisAbnormalController::class, 'index'])->name('produksi.jenis-abnormal.index');                 // GET   /produksi/jenis-abnormal              -> daftar
    Route::get('/jenis-abnormal/create', [JenisAbnormalController::class, 'create'])->name('produksi.jenis-abnormal.create');        // GET   /produksi/jenis-abnormal/create       -> form tambah
    Route::post('/jenis-abnormal', [JenisAbnormalController::class, 'store'])->name('produksi.jenis-abnormal.store');                // POST  /produksi/jenis-abnormal              -> simpan
    Route::get('/jenis-abnormal/{id}/edit', [JenisAbnormalController::class, 'edit'])->name('produksi.jenis-abnormal.edit');         // GET   /produksi/jenis-abnormal/VS01/edit    -> form ubah
    Route::put('/jenis-abnormal/{id}', [JenisAbnormalController::class, 'update'])->name('produksi.jenis-abnormal.update');          // PUT   /produksi/jenis-abnormal/VS01         -> simpan perubahan
    Route::patch('/jenis-abnormal/{id}/status', [JenisAbnormalController::class, 'status'])->name('produksi.jenis-abnormal.status'); // PATCH /produksi/jenis-abnormal/VS01/status  -> ganti Aktif <-> Non-Aktif
});
