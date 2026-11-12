<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel benang (master, sama dengan proyek QCT lama)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('benang', function (Blueprint $table) {   // buat tabel benang
            $table->string('merge', 10)->primary();              // primary key: kode merge dari pabrik, contoh M150A
            $table->integer('denier');                           // ukuran denier benang, contoh 150
            $table->integer('filamen');                          // jumlah filamen, contoh 48
            $table->decimal('toleransi_denier', 4, 2)->default(3.00); // toleransi denier dalam persen, contoh 3.00
            $table->string('warna_ppt', 20)->nullable();         // warna kertas/ppt penanda merge di troly, boleh kosong
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // benang tidak dihapus, cukup dinonaktifkan
            $table->string('created_by', 10)->nullable();        // id karyawan penginput
            $table->string('updated_by', 10)->nullable();        // id karyawan pengubah terakhir
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('created_by')->references('id_karyawan')->on('karyawan'); // harus ada di tabel karyawan
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('benang');                          // hapus tabel benang
    }
};
