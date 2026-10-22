<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->char('id_jabatan', 2)->primary();         // 01, 02, ...
            $table->string('nm_jabatan', 30);
            $table->char('id_divisi', 2)->nullable();         // Direktur boleh kosong

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
