<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel permission (daftar fitur/halaman)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('permission', function (Blueprint $table) { // buat tabel bernama permission
            $table->string('id_permission', 50)->primary();      // primary key = NAMA ROUTE (contoh: admin.karyawan.create)
            $table->string('nm_permission', 100);                // keterangan (contoh: Tambah data karyawan)
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // permission Non-Aktif = fitur ditutup untuk semua role
            $table->timestamps();                                // kolom created_at dan updated_at
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('permission');                      // hapus tabel permission
    }
};
