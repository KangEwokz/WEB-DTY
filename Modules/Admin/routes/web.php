<?php

use Illuminate\Support\Facades\Route;                            // class Route untuk mendaftarkan URL
use Modules\Admin\Http\Controllers\AreaController;               // panggil AreaController
use Modules\Admin\Http\Controllers\DepartemenController;         // panggil DepartemenController
use Modules\Admin\Http\Controllers\DivisiController;             // panggil DivisiController
use Modules\Admin\Http\Controllers\JabatanController;            // panggil JabatanController
use Modules\Admin\Http\Controllers\KaryawanController;           // panggil KaryawanController

Route::middleware(['auth', 'akses'])->group(function () {        // wajib login + wajib punya permission (nama route dicek oleh CekAkses)
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan.index');              // GET    /karyawan            -> daftar karyawan
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('admin.karyawan.create');     // GET    /karyawan/create     -> form tambah
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('admin.karyawan.store');             // POST   /karyawan            -> simpan data baru
    Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('admin.karyawan.edit');      // GET    /karyawan/2019-0001/edit -> form ubah; {id} = id karyawan
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('admin.karyawan.update');       // PUT    /karyawan/2019-0001  -> simpan perubahan
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');  // DELETE /karyawan/2019-0001  -> hapus data

    Route::get('/departemen', [DepartemenController::class, 'index'])->name('admin.departemen.index');            // GET   /departemen           -> daftar departemen
    Route::get('/departemen/create', [DepartemenController::class, 'create'])->name('admin.departemen.create');   // GET   /departemen/create    -> form tambah
    Route::post('/departemen', [DepartemenController::class, 'store'])->name('admin.departemen.store');           // POST  /departemen           -> simpan data baru
    Route::get('/departemen/{id}/edit', [DepartemenController::class, 'edit'])->name('admin.departemen.edit');    // GET   /departemen/01/edit   -> form ubah
    Route::put('/departemen/{id}', [DepartemenController::class, 'update'])->name('admin.departemen.update');     // PUT   /departemen/01        -> simpan perubahan
    Route::patch('/departemen/{id}/status', [DepartemenController::class, 'status'])->name('admin.departemen.status'); // PATCH /departemen/01/status -> ganti Aktif <-> Non-Aktif

    Route::get('/divisi', [DivisiController::class, 'index'])->name('admin.divisi.index');                        // GET   /divisi               -> daftar divisi
    Route::get('/divisi/create', [DivisiController::class, 'create'])->name('admin.divisi.create');               // GET   /divisi/create        -> form tambah
    Route::post('/divisi', [DivisiController::class, 'store'])->name('admin.divisi.store');                       // POST  /divisi               -> simpan data baru
    Route::get('/divisi/{id}/edit', [DivisiController::class, 'edit'])->name('admin.divisi.edit');                // GET   /divisi/01/edit       -> form ubah
    Route::put('/divisi/{id}', [DivisiController::class, 'update'])->name('admin.divisi.update');                 // PUT   /divisi/01            -> simpan perubahan
    Route::patch('/divisi/{id}/status', [DivisiController::class, 'status'])->name('admin.divisi.status');        // PATCH /divisi/01/status     -> ganti Aktif <-> Non-Aktif

    Route::get('/jabatan', [JabatanController::class, 'index'])->name('admin.jabatan.index');                     // GET   /jabatan              -> daftar jabatan
    Route::get('/jabatan/create', [JabatanController::class, 'create'])->name('admin.jabatan.create');            // GET   /jabatan/create       -> form tambah
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('admin.jabatan.store');                    // POST  /jabatan              -> simpan data baru
    Route::get('/jabatan/{id}/edit', [JabatanController::class, 'edit'])->name('admin.jabatan.edit');             // GET   /jabatan/01/edit      -> form ubah
    Route::put('/jabatan/{id}', [JabatanController::class, 'update'])->name('admin.jabatan.update');              // PUT   /jabatan/01           -> simpan perubahan
    Route::patch('/jabatan/{id}/status', [JabatanController::class, 'status'])->name('admin.jabatan.status');     // PATCH /jabatan/01/status    -> ganti Aktif <-> Non-Aktif

    Route::get('/area', [AreaController::class, 'index'])->name('admin.area.index');                              // GET   /area                 -> daftar area
    Route::get('/area/create', [AreaController::class, 'create'])->name('admin.area.create');                     // GET   /area/create          -> form tambah
    Route::post('/area', [AreaController::class, 'store'])->name('admin.area.store');                             // POST  /area                 -> simpan data baru
    Route::get('/area/{id}/edit', [AreaController::class, 'edit'])->name('admin.area.edit');                      // GET   /area/VIS/edit        -> form ubah
    Route::put('/area/{id}', [AreaController::class, 'update'])->name('admin.area.update');                       // PUT   /area/VIS             -> simpan perubahan
    Route::patch('/area/{id}/status', [AreaController::class, 'status'])->name('admin.area.status');              // PATCH /area/VIS/status      -> ganti Aktif <-> Non-Aktif
});
