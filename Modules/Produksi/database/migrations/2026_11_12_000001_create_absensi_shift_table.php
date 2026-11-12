<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel absensi_shift dan absensi_detail
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('absensi_shift', function (Blueprint $table) { // tabel "kepala" absensi: 1 baris = 1 tanggal + 1 shift
            $table->string('id_absensi', 20)->primary();         // primary key, contoh ABS-20261112-S1
            $table->date('tgl_absen');                           // tanggal shift
            $table->enum('shift', ['S1', 'S2', 'S3']);           // shift 1, 2, atau 3
            $table->string('created_by', 10);                    // id karyawan (supervisor) yang mencatat
            $table->string('updated_by', 10)->nullable();        // id karyawan yang terakhir mengubah
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->unique(['tgl_absen', 'shift']);              // 1 tanggal + 1 shift hanya boleh 1 absensi
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan'); // harus ada di tabel karyawan
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });

        Schema::create('absensi_detail', function (Blueprint $table) { // tabel "isi" absensi: 1 baris = 1 operator
            $table->string('id_absensi', 20);                    // absensi shift yang mana
            $table->string('id_karyawan', 10);                   // operator yang diabsen
            $table->enum('kehadiran', ['Hadir', 'Sakit', 'Ijin', 'Alfa', 'Cuti']); // status kehadiran
            $table->string('id_area', 10)->nullable();           // area kerja (penempatan); diisi jika Hadir
            $table->string('keterangan', 100)->nullable();       // catatan tambahan, boleh kosong

            $table->primary(['id_absensi', 'id_karyawan']);      // 1 operator hanya sekali dalam 1 absensi
            $table->foreign('id_absensi')->references('id_absensi')->on('absensi_shift')->cascadeOnDelete(); // cascadeOnDelete = jika kepala absensi dihapus, isinya ikut terhapus
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan');
            $table->foreign('id_area')->references('id_area')->on('area');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('absensi_detail');                  // hapus isi dulu (karena bergantung pada kepala)
        Schema::dropIfExists('absensi_shift');                   // lalu hapus kepala
    }
};
