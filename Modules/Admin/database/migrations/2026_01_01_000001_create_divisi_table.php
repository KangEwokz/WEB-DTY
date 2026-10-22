<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->char('id_divisi', 2)->primary();          // 01 = Admin, 02 = Produksi, 03 = QCT
            $table->string('kd_divisi', 5)->unique();         // ADM, PRD, QCT
            $table->string('nm_divisi', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
