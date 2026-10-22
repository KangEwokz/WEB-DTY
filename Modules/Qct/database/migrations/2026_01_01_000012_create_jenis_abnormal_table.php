<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_abnormal', function (Blueprint $table) {
            $table->string('id_jns_abn', 5)->primary();       // VS01, VS02, ...
            $table->string('nm_abnormal', 50)->unique();      // Handling, OTY, Hairy, ...
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
        Schema::dropIfExists('jenis_abnormal');
    }
};
