<?php

use Illuminate\Support\Facades\Route;
use Modules\Produksi\Http\Controllers\Master\BenangController;
use Modules\Produksi\Http\Controllers\Master\MesinController;
use Modules\Produksi\Http\Controllers\Transaksi\DoffingController;
use Modules\Produksi\Http\Controllers\Transaksi\JadwalProduksiController;

// Route divisi Produksi (Supervisor)
Route::middleware(['auth', 'divisi:Produksi'])->prefix('produksi')->name('produksi.')->group(function () {

    // UC06 Kelola Jadwal Produksi (+ benang, mesin)
    Route::middleware('permission:kelola-jadwal-produksi')->group(function () {
        Route::resource('benang', BenangController::class)->except(['show']);
        Route::resource('mesin', MesinController::class)->only(['index', 'edit', 'update']);
        Route::resource('jadwal-produksi', JadwalProduksiController::class)->except(['show']);
    });

    // UC07 Kelola Data Doffing
    Route::resource('doffing', DoffingController::class)->except(['show'])->middleware('permission:kelola-doffing');
});
