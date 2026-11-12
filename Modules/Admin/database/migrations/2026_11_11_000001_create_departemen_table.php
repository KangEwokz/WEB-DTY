<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel departemen
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('departemen', function (Blueprint $table) { // buat tabel bernama departemen (tingkat paling atas, dipimpin HOD)
            $table->char('id_departemen', 2)->primary();         // primary key, 2 karakter (contoh: 01)
            $table->string('kd_departemen', 5)->unique();        // kode singkat, tidak boleh kembar (contoh: DTY, POY, PEI)
            $table->string('nm_departemen', 50);                 // nama departemen, maks 50 karakter
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // hanya boleh Aktif/Non-Aktif, bawaannya Aktif
            $table->timestamps();                                // kolom created_at dan updated_at
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('departemen');                      // hapus tabel departemen
    }
};
