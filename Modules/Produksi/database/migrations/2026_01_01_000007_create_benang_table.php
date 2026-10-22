<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benang', function (Blueprint $table) {
            $table->string('merge', 10)->primary();           // kode merge benang
            $table->integer('denier');
            $table->integer('filamen');
            $table->string('warna_ppt', 20)->nullable();      // warna kertas penanda merge
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benang');
    }
};
