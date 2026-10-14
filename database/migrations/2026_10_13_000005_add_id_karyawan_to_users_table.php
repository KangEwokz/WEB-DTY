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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->unique()->after('id');

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_karyawan']);
            $table->dropUnique(['id_karyawan']);
            $table->dropColumn('id_karyawan');
        });
    }
};
