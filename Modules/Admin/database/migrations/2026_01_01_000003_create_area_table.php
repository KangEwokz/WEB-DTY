<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area', function (Blueprint $table) {
            $table->string('id_area', 10)->primary();         // KPG, VIS, LAB
            $table->string('nm_area', 50);
            $table->char('id_divisi', 2);                     // KPG -> Admin, VIS & LAB -> QCT
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif');
            // FK created_by/updated_by ke karyawan dibuat di migration karyawan,
            // karena tabel karyawan baru ada setelah tabel area.
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area');
    }
};
