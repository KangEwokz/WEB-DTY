<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tanggal, shift, troly, merge diambil dengan join ke doffing
        Schema::create('check_batch', function (Blueprint $table) {
            $table->string('id_check', 25)->primary();        // BTH-tanggal-shift-urut
            $table->string('id_doffing', 20)->unique();       // satu troly dicek satu kali
            $table->enum('hasil_visual', ['OK', 'NG'])->nullable();  // diisi operator Visual
            $table->enum('hasil_denier', ['OK', 'NG'])->nullable();  // diisi operator Lab
            $table->string('id_grade', 5)->nullable();
            $table->enum('status', ['Packing', 'Hold'])->nullable();
            $table->string('insp_visual', 10)->nullable();
            $table->string('insp_lab', 10)->nullable();
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_doffing')->references('id_doffing')->on('doffing');
            $table->foreign('id_grade')->references('id_grade')->on('grade');
            $table->foreign('insp_visual')->references('id_karyawan')->on('karyawan');
            $table->foreign('insp_lab')->references('id_karyawan')->on('karyawan');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_batch');
    }
};
