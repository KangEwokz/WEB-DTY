<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel mesin (master)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('mesin', function (Blueprint $table) {    // buat tabel mesin
            $table->string('kode_mesin', 8)->primary();          // primary key: kode tetap mesin, contoh MCN-001 (tidak berubah walau mesin dipindah)
            $table->enum('line', ['L1', 'L2', 'L3']);            // line tempat mesin sekarang ditempatkan; bisa dipindah
            $table->tinyInteger('posisi');                       // posisi mesin di dalam line, contoh 1, 2, 3
            $table->enum('status', ['Running', 'Stop', 'Maintenance'])->default('Running'); // kondisi mesin saat ini
            $table->string('created_by', 10)->nullable();        // id karyawan penginput
            $table->string('updated_by', 10)->nullable();        // id karyawan pengubah terakhir
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->unique(['line', 'posisi']);                  // 1 posisi di 1 line hanya boleh diisi 1 mesin
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan'); // harus ada di tabel karyawan
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('mesin');                           // hapus tabel mesin
    }
};
