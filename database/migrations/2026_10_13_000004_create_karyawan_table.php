<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->primary();
            $table->string('nama_karyawan', 50);
            $table->enum('jns_kelamin', ['L', 'P']);
            $table->string('departemen', 20)->default('DTY');
            $table->string('id_divisi', 10);
            $table->string('id_jabatan', 10);
            $table->string('id_grup', 10);

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('id_grup')->references('id_grup')->on('grup')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
