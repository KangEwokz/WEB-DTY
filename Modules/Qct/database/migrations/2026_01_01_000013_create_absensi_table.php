<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->string('id_absen', 20)->primary();        // ABS-tanggal-urut
            $table->string('id_karyawan', 10);                // karyawan QCT
            $table->date('tgl_absen');
            $table->enum('shift', ['S1', 'S2', 'S3', 'GS']);
            $table->string('id_area', 10)->nullable();        // penugasan area shift ini (VIS/LAB)
            $table->enum('kehadiran', ['Hadir', 'Sakit', 'Ijin', 'Alfa', 'Cuti']);
            $table->string('dok_file', 255)->nullable();      // surat sakit/ijin
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan');
            $table->foreign('id_area')->references('id_area')->on('area');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
