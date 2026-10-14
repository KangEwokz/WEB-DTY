<?php

use Illuminate\Support\Facades\Route;
use Modules\Karyawan\Http\Controllers\AbsensiController;
use Modules\Karyawan\Http\Controllers\DashboardController;
use Modules\Karyawan\Http\Controllers\KaryawanController;

// Modul hanya untuk SuperAdmin dan karyawan divisi Admin (Gate akses-karyawan),
// hak akses tiap aksi dicek lewat permission di controller
Route::middleware(['auth', 'can:akses-karyawan'])->group(function () {
    // Harus sebelum resource agar tidak tertangkap karyawan/{karyawan}
    Route::get('karyawan/dashboard', [DashboardController::class, 'index'])->name('karyawan.dashboard');
    Route::get('karyawan/absensi', [AbsensiController::class, 'index'])->name('absensi.index');

    Route::resource('karyawan', KaryawanController::class)->names('karyawan');
});
