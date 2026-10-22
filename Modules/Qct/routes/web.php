<?php

use Illuminate\Support\Facades\Route;
use Modules\Qct\Http\Controllers\Master\AreaQctController;
use Modules\Qct\Http\Controllers\Master\GradeController;
use Modules\Qct\Http\Controllers\Master\JenisAbnormalController;
use Modules\Qct\Http\Controllers\Output\LaporanCheckController;
use Modules\Qct\Http\Controllers\Output\RekapAbsensiController;
use Modules\Qct\Http\Controllers\Output\RekapCheckController;
use Modules\Qct\Http\Controllers\Transaksi\AbnormalController;
use Modules\Qct\Http\Controllers\Transaksi\AbsensiController;
use Modules\Qct\Http\Controllers\Transaksi\CheckBatchController;
use Modules\Qct\Http\Controllers\Transaksi\SampelController;

// Route divisi QCT (HOD, Operator) + rekap check untuk Direktur
Route::middleware(['auth', 'divisi:QCT'])->prefix('qct')->name('qct.')->group(function () {

    // ---------- HOD ----------
    // UC08 Kelola Data Master QCT
    Route::middleware('permission:kelola-master-qct')->group(function () {
        Route::resource('area', AreaQctController::class)->except(['show']);
        Route::resource('grade', GradeController::class)->except(['show']);
        Route::resource('jenis-abnormal', JenisAbnormalController::class)->except(['show']);
    });

    // UC09 Kelola Absensi & Penugasan Area QCT
    Route::resource('absensi', AbsensiController::class)->except(['show'])->middleware('permission:kelola-absensi');

    // ---------- Operator ----------
    // UC12 Cek Visual (operator area Visual)
    Route::resource('check-batch', CheckBatchController::class)->except(['index', 'show'])
        ->middleware(['permission:kelola-cek-visual', 'area:VIS']);

    // UC13 Cek Denier (operator area Lab)
    Route::middleware(['permission:kelola-cek-denier', 'area:LAB'])->group(function () {
        Route::get('check-batch/{id}/denier', [CheckBatchController::class, 'editDenier'])->name('check-batch.denier');
        Route::put('check-batch/{id}/denier', [CheckBatchController::class, 'updateDenier'])->name('check-batch.denier.update');
    });

    // UC12 & UC13: daftar dan detail troly (Visual dan Lab)
    Route::middleware(['permission:kelola-cek-visual|kelola-cek-denier', 'area:VIS,LAB'])->group(function () {
        Route::get('check-batch', [CheckBatchController::class, 'index'])->name('check-batch.index');
        Route::get('check-batch/{id}', [CheckBatchController::class, 'show'])->name('check-batch.show');
    });

    // UC14 Abnormal: bagian visual (operator area Visual)
    Route::resource('abnormal', AbnormalController::class)->except(['index', 'show'])
        ->middleware(['permission:kelola-abnormal', 'area:VIS']);

    // UC14 Abnormal: hasil uji lab (operator area Lab)
    Route::middleware(['permission:kelola-abnormal', 'area:LAB'])->group(function () {
        Route::get('abnormal/{id}/lab', [AbnormalController::class, 'editLab'])->name('abnormal.lab');
        Route::put('abnormal/{id}/lab', [AbnormalController::class, 'updateLab'])->name('abnormal.lab.update');
    });

    // UC14 Abnormal: daftar (Visual dan Lab)
    Route::get('abnormal', [AbnormalController::class, 'index'])->name('abnormal.index')
        ->middleware(['permission:kelola-abnormal', 'area:VIS,LAB']);

    // UC15 Kelola Data Sampel (Visual dan Lab)
    Route::resource('sampel', SampelController::class)->except(['show'])
        ->middleware(['permission:kelola-sampel', 'area:VIS,LAB']);
    // ---------- Verifikasi HOD ----------
    // UC10 Verifikasi Rekap Absensi Bulanan QCT
    Route::middleware('permission:verifikasi-rekap-absensi')->group(function () {
        Route::get('rekap-absensi', [RekapAbsensiController::class, 'index'])->name('rekap-absensi.index');
        Route::get('rekap-absensi/{id}', [RekapAbsensiController::class, 'show'])->name('rekap-absensi.show');
        Route::post('rekap-absensi/{id}/verifikasi', [RekapAbsensiController::class, 'verifikasi'])->name('rekap-absensi.verifikasi');
    });

    // UC11 Verifikasi Laporan Check QCT (per tanggal + shift)
    Route::middleware('permission:verifikasi-laporan-check')->group(function () {
        Route::get('laporan-check', [LaporanCheckController::class, 'index'])->name('laporan-check.index');
        Route::get('laporan-check/{id}', [LaporanCheckController::class, 'show'])->name('laporan-check.show');
        Route::post('laporan-check/{id}/verifikasi', [LaporanCheckController::class, 'verifikasi'])->name('laporan-check.verifikasi');
    });

    // ---------- Direktur ----------
    // UC16 Lihat Rekap Check QCT
    Route::middleware('permission:lihat-rekap-check')->group(function () {
        Route::get('rekap-check', [RekapCheckController::class, 'index'])->name('rekap-check.index');
        Route::get('rekap-check/{id}', [RekapCheckController::class, 'show'])->name('rekap-check.show');
    });

    // UC17 Verifikasi Rekap Check «include» UC18 Arsipkan (PDF)
    Route::post('rekap-check/{id}/verifikasi', [RekapCheckController::class, 'verifikasi'])->name('rekap-check.verifikasi')
        ->middleware('permission:verifikasi-rekap-check');
});
