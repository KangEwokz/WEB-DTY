<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel laporan_shift
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('laporan_shift', function (Blueprint $table) { // laporan akhir shift: 1 baris = total output 1 tanggal + 1 shift
            $table->string('id_laporan', 20)->primary();         // primary key, contoh LAP-20261112-S1
            $table->date('tgl_laporan');                         // tanggal shift
            $table->enum('shift', ['S1', 'S2', 'S3']);           // shift 1, 2, atau 3
            $table->unsignedInteger('jml_doffing')->default(0);  // jumlah doffing (unsigned = tidak boleh minus)
            $table->unsignedInteger('jml_break')->default(0);    // jumlah benang putus
            $table->unsignedInteger('jml_problem_mesin')->default(0); // jumlah kejadian problem mesin
            $table->text('ket_problem_mesin')->nullable();       // keterangan problem mesin, boleh kosong
            $table->unsignedInteger('bleeding_time')->default(0); // total bleeding time dalam menit
            $table->unsignedInteger('suspend_time')->default(0); // total suspend time dalam menit
            $table->decimal('waste', 8, 2)->default(0);          // total waste dalam kg, 2 angka di belakang koma (contoh 12.50)
            $table->text('catatan')->nullable();                 // catatan umum supervisor, boleh kosong
            $table->string('created_by', 10);                    // id karyawan (supervisor) yang mencatat
            $table->string('updated_by', 10)->nullable();        // id karyawan yang terakhir mengubah
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->unique(['tgl_laporan', 'shift']);            // 1 tanggal + 1 shift hanya boleh 1 laporan
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('laporan_shift');                   // hapus tabel laporan_shift
    }
};
