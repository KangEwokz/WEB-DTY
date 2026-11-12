<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel users dan sessions
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('users', function (Blueprint $table) {    // buat tabel users (akun login)
            $table->id();                                        // kolom id angka otomatis (1, 2, 3, ...), primary key
            $table->string('username', 20)->unique();            // nama login, maks 20 karakter, tidak boleh kembar
            $table->string('password');                          // password yang sudah diacak (hash)
            $table->string('id_karyawan', 10)->unique();         // pemilik akun; unique = 1 karyawan hanya 1 akun
            $table->string('id_role', 20);                       // hak akses akun ini
            $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif'); // akun Non-Aktif tidak bisa login
            $table->rememberToken();                             // kolom remember_token bawaan Laravel (fitur "ingat saya")
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan'); // id_karyawan harus ada di tabel karyawan
            $table->foreign('id_role')->references('id_role')->on('role');             // id_role harus ada di tabel role
        });

        Schema::create('sessions', function (Blueprint $table) { // buat tabel sessions (wajib karena SESSION_DRIVER=database di .env)
            $table->string('id')->primary();                     // ID session (disimpan juga di cookie browser)
            $table->foreignId('user_id')->nullable()->index();   // id user pemilik session, kosong jika belum login
            $table->string('ip_address', 45)->nullable();        // alamat IP pengunjung
            $table->text('user_agent')->nullable();              // info browser pengunjung
            $table->longText('payload');                         // isi data session
            $table->integer('last_activity')->index();           // waktu aktivitas terakhir
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('sessions');                        // hapus tabel sessions
        Schema::dropIfExists('users');                           // hapus tabel users
    }
};
