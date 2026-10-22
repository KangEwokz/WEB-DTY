<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doffing', function (Blueprint $table) {
            $table->string('id_doffing', 20)->primary();      // DOF-tanggal-shift-urut
            $table->date('tgl_doffing');
            $table->enum('shift', ['S1', 'S2', 'S3']);
            $table->string('nmr_troly', 10);
            $table->string('merge', 10);
            $table->integer('jml_bobbin');
            $table->decimal('berat_troly', 6, 2)->nullable(); // kg (masih keputusan terbuka)
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
        Schema::dropIfExists('doffing');
    }
};
