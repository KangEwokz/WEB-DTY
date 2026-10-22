<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesin', function (Blueprint $table) {
            $table->string('kode_mesin', 8)->primary();       // line-posisi, contoh 401-01
            $table->unsignedSmallInteger('line');             // 401, 402, 403 (Tinyint tidak cukup)
            $table->unsignedTinyInteger('posisi');            // 1-10
            $table->enum('status', ['Running', 'Stop', 'Maintenance'])->default('Running');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesin');
    }
};
