<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\Master\AreaController;
use Modules\Admin\Http\Controllers\Master\DivisiController;
use Modules\Admin\Http\Controllers\Master\JabatanController;
use Modules\Admin\Http\Controllers\Master\KaryawanController;
use Modules\Admin\Http\Controllers\Master\UserController;
use Modules\Admin\Http\Controllers\Output\RekapAbsensiController;

// Route divisi Admin (Staf, area KPG)
Route::middleware(['auth', 'divisi:Admin'])->prefix('admin')->name('admin.')->group(function () {

    // UC03 Kelola Data Karyawan (+ divisi, jabatan, area milik Admin)
    Route::middleware('permission:kelola-karyawan')->group(function () {
        Route::resource('divisi', DivisiController::class)->except(['show', 'destroy']);
        Route::resource('jabatan', JabatanController::class)->except(['show', 'destroy']);
        Route::resource('area', AreaController::class)->except(['show']);
        Route::resource('karyawan', KaryawanController::class);
    });

    // UC04 Kelola Akun Pengguna
    Route::resource('user', UserController::class)->except(['show'])->middleware('permission:kelola-akun');

    // UC05 Melihat Rekap Absensi QCT (baca saja)
    Route::middleware('permission:lihat-rekap-absensi')->group(function () {
        Route::get('rekap-absensi', [RekapAbsensiController::class, 'index'])->name('rekap-absensi.index');
        Route::get('rekap-absensi/{id}', [RekapAbsensiController::class, 'show'])->name('rekap-absensi.show');
    });
});
