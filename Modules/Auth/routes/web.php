<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\DashboardController;
use Modules\Auth\Http\Controllers\LoginController;

// UC01 Login (hanya untuk yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', 'dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // UC02 Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
