<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel role_permission (role X boleh membuka fitur Y)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('role_permission', function (Blueprint $table) { // buat tabel penghubung role dan permission
            $table->string('id_role', 20);                       // role yang diberi akses
            $table->string('id_permission', 50);                 // fitur yang boleh dibuka

            $table->primary(['id_role', 'id_permission']);       // pasangan role + permission tidak boleh kembar
            $table->foreign('id_role')->references('id_role')->on('role');                         // id_role harus ada di tabel role
            $table->foreign('id_permission')->references('id_permission')->on('permission');       // id_permission harus ada di tabel permission
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('role_permission');                 // hapus tabel role_permission
    }
};
