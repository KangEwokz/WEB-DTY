<?php

use Illuminate\Support\Facades\Route;                            // class Route untuk mendaftarkan URL
use Modules\Auth\Http\Controllers\DashboardController;           // panggil DashboardController
use Modules\Auth\Http\Controllers\LoginController;               // panggil LoginController
use Modules\Auth\Http\Controllers\RoleController;                // panggil RoleController
use Modules\Auth\Http\Controllers\UserController;                // panggil UserController

Route::redirect('/', '/dashboard');                              // buka "/" -> dialihkan ke "/dashboard"

Route::middleware('guest')->group(function () {                  // kelompok route khusus user yang BELUM login
    Route::get('/login', [LoginController::class, 'index'])->name('login'); // GET /login -> tampilkan form login; nama 'login' dipakai Laravel untuk mengarahkan user yang belum login
    Route::post('/login', [LoginController::class, 'login']);    // POST /login -> proses data form login
});                                                              // akhir kelompok guest

Route::middleware('auth')->group(function () {                   // kelompok route khusus user yang SUDAH login (kalau belum, dilempar ke /login)
    Route::get('/dashboard', [DashboardController::class, 'index']); // GET /dashboard -> tampilkan dashboard
    Route::post('/logout', [LoginController::class, 'logout']);  // POST /logout -> proses logout (POST agar wajib pakai token @csrf)
});                                                              // akhir kelompok auth

Route::middleware(['auth', 'akses'])->group(function () {        // wajib login + wajib punya permission (dicek oleh CekAkses)
    Route::get('/role', [RoleController::class, 'index'])->name('auth.role.index');                          // GET   /role                 -> daftar role
    Route::get('/role/create', [RoleController::class, 'create'])->name('auth.role.create');                 // GET   /role/create          -> form tambah role
    Route::post('/role', [RoleController::class, 'store'])->name('auth.role.store');                         // POST  /role                 -> simpan role baru
    Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('auth.role.edit');                  // GET   /role/hod/edit        -> form ubah nama role
    Route::put('/role/{id}', [RoleController::class, 'update'])->name('auth.role.update');                   // PUT   /role/hod             -> simpan perubahan
    Route::patch('/role/{id}/status', [RoleController::class, 'status'])->name('auth.role.status');          // PATCH /role/hod/status      -> ganti Aktif <-> Non-Aktif
    Route::get('/role/{id}/permission', [RoleController::class, 'permission'])->name('auth.role.permission'); // GET   /role/hod/permission  -> halaman centang permission
    Route::put('/role/{id}/permission', [RoleController::class, 'simpanPermission'])->name('auth.role.simpan-permission'); // PUT /role/hod/permission -> simpan centangan

    Route::get('/akun', [UserController::class, 'index'])->name('auth.user.index');                          // GET   /akun                 -> daftar akun pengguna
    Route::get('/akun/create', [UserController::class, 'create'])->name('auth.user.create');                 // GET   /akun/create          -> form buat akun
    Route::post('/akun', [UserController::class, 'store'])->name('auth.user.store');                         // POST  /akun                 -> simpan akun baru
    Route::get('/akun/{id}/edit', [UserController::class, 'edit'])->name('auth.user.edit');                  // GET   /akun/3/edit          -> form ubah akun (termasuk reset password); {id} = id angka di tabel users
    Route::put('/akun/{id}', [UserController::class, 'update'])->name('auth.user.update');                   // PUT   /akun/3               -> simpan perubahan
    Route::patch('/akun/{id}/status', [UserController::class, 'status'])->name('auth.user.status');          // PATCH /akun/3/status        -> ganti Aktif <-> Non-Aktif
});
