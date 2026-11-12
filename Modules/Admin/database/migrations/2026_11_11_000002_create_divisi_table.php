<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel divisi
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('divisi', function (Blueprint $table) {   // buat tabel bernama divisi (di bawah departemen, dipimpin Kadiv)
            $table->char('id_divisi', 2)->primary();             // primary key, 2 karakter (contoh: 01)
            $table->string('kd_divisi', 5)->unique();            // kode singkat, tidak boleh kembar (contoh: QCT, ELK)
            $table->string('nm_divisi', 50);                     // nama divisi, maks 50 karakter
            $table->char('id_departemen', 2);                    // departemen pemilik divisi ini, wajib diisi
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // hanya boleh Aktif/Non-Aktif, bawaannya Aktif
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen'); // id_departemen harus ada di tabel departemen
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('divisi');                          // hapus tabel divisi
    }
};
