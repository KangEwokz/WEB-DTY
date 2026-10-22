<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('id_karyawan', 10)->primary();     // tahun masuk + urut, contoh 2019-0001
            $table->string('nama_karyawan', 50);
            $table->enum('jns_kelamin', ['L', 'P']);
            $table->char('id_divisi', 2)->nullable();         // Direktur tidak punya divisi
            $table->char('id_jabatan', 2);
            $table->string('id_area', 10)->nullable();        // area default; kosong bila tidak punya area
            $table->enum('grup', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'NG']);
            $table->date('tgl_masuk');
            $table->string('no_hp', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->string('created_by', 10)->nullable();
            $table->string('updated_by', 10)->nullable();
            $table->timestamps();

            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan');
            $table->foreign('id_area')->references('id_area')->on('area');
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });

        // Lanjutan tabel area: FK penginput/pengubah ke karyawan
        Schema::table('area', function (Blueprint $table) {
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('area', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('karyawan');
    }
};
