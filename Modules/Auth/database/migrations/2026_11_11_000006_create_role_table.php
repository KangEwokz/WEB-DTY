<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel role (hak akses, terpisah dari jabatan)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('role', function (Blueprint $table) {     // buat tabel bernama role
            $table->string('id_role', 20)->primary();            // primary key, maks 20 karakter (contoh: admin, hod)
            $table->string('nm_role', 50);                       // nama lengkap role (contoh: Head of Department)
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // role Non-Aktif = user dengan role ini tidak bisa login
            $table->timestamps();                                // kolom created_at dan updated_at
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('role');                            // hapus tabel role
    }
};
