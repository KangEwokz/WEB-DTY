<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel area
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('area', function (Blueprint $table) {     // buat tabel area (tempat tugas di dalam divisi)
            $table->string('id_area', 10)->primary();            // primary key, maks 10 karakter (contoh: VIS)
            $table->string('nm_area', 50);                       // nama area, maks 50 karakter (contoh: Visual)
            $table->char('id_divisi', 2);                        // divisi pemilik area, wajib diisi
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // hanya boleh Aktif/Non-Aktif, bawaannya Aktif
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi'); // id_divisi harus ada di tabel divisi
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('area');                            // hapus tabel area
    }
};
