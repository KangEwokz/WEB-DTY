<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sampel', function (Blueprint $table) {
            $table->string('id_sampel', 20)->primary();       // SMP-tanggal-shift-urut
            $table->string('merge', 10);
            $table->date('tgl_sampel');
            $table->enum('shift', ['S1', 'S2', 'S3']);
            $table->string('id_jns_abn', 5)->nullable();      // kosong = sampel OK
            $table->decimal('act_denier', 6, 2)->nullable();
            $table->decimal('elong', 5, 2)->nullable();
            $table->decimal('tenacity', 5, 2)->nullable();
            $table->decimal('dti', 5, 2)->nullable();
            $table->string('id_grade', 5)->nullable();
            $table->string('insp_visual', 10)->nullable();
            $table->string('insp_lab', 10)->nullable();
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('merge')->references('merge')->on('benang');
            $table->foreign('id_jns_abn')->references('id_jns_abn')->on('jenis_abnormal');
            $table->foreign('id_grade')->references('id_grade')->on('grade');
            $table->foreign('insp_visual')->references('id_karyawan')->on('karyawan');
            $table->foreign('insp_lab')->references('id_karyawan')->on('karyawan');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sampel');
    }
};
