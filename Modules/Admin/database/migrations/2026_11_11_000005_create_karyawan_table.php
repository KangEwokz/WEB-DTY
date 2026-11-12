<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel karyawan
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('karyawan', function (Blueprint $table) { // buat tabel bernama karyawan
            $table->string('id_karyawan', 10)->primary();        // primary key, format tahun masuk + urut (contoh: 2019-0001)
            $table->string('nama_karyawan', 50);                 // nama lengkap, maks 50 karakter
            $table->enum('jns_kelamin', ['L', 'P']);             // jenis kelamin: L = laki-laki, P = perempuan
            $table->char('id_departemen', 2)->nullable();        // departemen karyawan, boleh kosong (Direktur)
            $table->char('id_divisi', 2)->nullable();            // divisi karyawan, boleh kosong (Direktur, HOD, Admin departemen)
            $table->char('id_jabatan', 2);                       // jabatan karyawan, wajib diisi
            $table->string('id_area', 10)->nullable();           // area tugas, boleh kosong (selain operator)
            $table->enum('grup', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'NG']); // grup shift, NG = non-grup
            $table->date('tgl_masuk');                           // tanggal mulai bekerja
            $table->string('no_hp', 15)->nullable();             // nomor HP, boleh kosong
            $table->string('email', 100)->nullable();            // email, boleh kosong
            $table->string('foto')->nullable();                  // lokasi file foto (contoh: foto/2019-0001.jpg), kosong = pakai placeholder
            $table->enum('status_aktif', ['Aktif', 'Non-Aktif'])->default('Aktif'); // hanya boleh Aktif/Non-Aktif, bawaannya Aktif
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen'); // id_departemen harus ada di tabel departemen
            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');             // id_divisi harus ada di tabel divisi
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan');          // id_jabatan harus ada di tabel jabatan
            $table->foreign('id_area')->references('id_area')->on('area');                   // id_area harus ada di tabel area
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('karyawan');                        // hapus tabel karyawan
    }
};
