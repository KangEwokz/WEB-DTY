<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak menyimpan angka; dihitung dari check_batch, abnormal, sampel pada tanggal+shift
        Schema::create('laporan_check', function (Blueprint $table) {
            $table->string('id_laporan', 20)->primary();      // LAP-tanggal-shift
            $table->date('tgl_laporan');
            $table->enum('shift', ['S1', 'S2', 'S3']);
            $table->enum('status', ['Draft', 'Terverifikasi'])->default('Draft');
            $table->string('diverifikasi_oleh', 10)->nullable(); // HOD QCT
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('diverifikasi_oleh')->references('id_karyawan')->on('karyawan');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_check');
    }
};
