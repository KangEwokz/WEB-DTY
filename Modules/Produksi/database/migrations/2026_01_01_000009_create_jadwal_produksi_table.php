<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_produksi', function (Blueprint $table) {
            $table->string('id_jadwal', 20)->primary();       // JDW-tanggal-urut
            $table->string('merge', 10);
            $table->date('tgl_produksi');
            $table->unsignedSmallInteger('line');             // 401, 402, 403
            $table->unsignedTinyInteger('pos_awal');
            $table->unsignedTinyInteger('pos_akhir');
            $table->string('customer', 100)->nullable();
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('merge')->references('merge')->on('benang');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_produksi');
    }
};
