<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel jabatan
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('jabatan', function (Blueprint $table) {  // buat tabel jabatan (daftar umum, bisa dipakai di departemen/divisi mana pun)
            $table->char('id_jabatan', 2)->primary();            // primary key, 2 karakter (contoh: 01)
            $table->string('nm_jabatan', 30);                    // nama jabatan, maks 30 karakter (contoh: HOD, Kadiv)
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // hanya boleh Aktif/Non-Aktif, bawaannya Aktif
            $table->timestamps();                                // kolom created_at dan updated_at
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('jabatan');                         // hapus tabel jabatan
    }
};
