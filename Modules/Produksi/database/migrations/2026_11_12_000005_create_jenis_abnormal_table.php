<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel jenis_abnormal (master, sama dengan proyek QCT lama)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('jenis_abnormal', function (Blueprint $table) { // buat tabel jenis_abnormal (dipakai Produksi, nanti juga QCT)
            $table->string('id_jns_abn', 5)->primary();          // primary key, contoh VS01
            $table->string('nm_abnormal', 50)->unique();         // nama abnormal, tidak boleh kembar, contoh Handling, OTY, Hairy
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // tidak dihapus, cukup dinonaktifkan
            $table->string('created_by', 10)->nullable();        // id karyawan penginput
            $table->string('updated_by', 10)->nullable();        // id karyawan pengubah terakhir
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('created_by')->references('id_karyawan')->on('karyawan'); // harus ada di tabel karyawan
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('jenis_abnormal');                  // hapus tabel jenis_abnormal
    }
};
