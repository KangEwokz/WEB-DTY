<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel troly (master)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('troly', function (Blueprint $table) {    // buat tabel troly
            $table->string('kode_troly', 10)->primary();         // primary key: TRN-001 (Normal) atau TRA-001 (Abnormal)
            $table->enum('jenis', ['Normal', 'Abnormal']);       // Normal = untuk hasil doffing, Abnormal = tempat bobbin abnormal
            $table->unsignedTinyInteger('kapasitas')->default(20); // jumlah bobbin maksimal dalam 1 troly, bawaan 20
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // troly rusak/hilang dinonaktifkan, tidak dihapus
            $table->string('created_by', 10)->nullable();        // id karyawan penginput
            $table->string('updated_by', 10)->nullable();        // id karyawan pengubah terakhir
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('created_by')->references('id_karyawan')->on('karyawan'); // harus ada di tabel karyawan
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('troly');                           // hapus tabel troly
    }
};
