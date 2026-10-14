<?php

use Illuminate\Support\Facades\Route;
use Modules\Master\Http\Controllers\DivisiController;
use Modules\Master\Http\Controllers\GrupController;
use Modules\Master\Http\Controllers\JabatanController;
use Modules\Master\Http\Controllers\ShiftController;

// Hak akses tiap aksi dicek lewat permission di masing-masing controller
Route::middleware('auth')->prefix('master')->group(function () {
    Route::resource('divisi', DivisiController::class)->except('show');
    Route::resource('jabatan', JabatanController::class)->except('show');
    Route::resource('grup', GrupController::class)->except('show');
    Route::resource('shift', ShiftController::class)->except('show');
});
