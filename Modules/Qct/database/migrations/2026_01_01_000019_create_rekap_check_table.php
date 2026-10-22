<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak menyimpan angka; Terverifikasi = diarsipkan dalam file PDF
        Schema::create('rekap_check', function (Blueprint $table) {
            $table->string('id_rekap_check', 15)->primary();  // RCK-YYYYMM
            $table->string('periode', 7)->unique();           // YYYY-MM
            $table->enum('status', ['Draft', 'Terverifikasi'])->default('Draft');
            $table->string('diverifikasi_oleh', 10)->nullable(); // Direktur
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->string('file_pdf', 255)->nullable();
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
        Schema::dropIfExists('rekap_check');
    }
};
