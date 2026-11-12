<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini membuat tabel doffing (1 baris = 1 troly hasil doffing; nanti diperiksa QCT)
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::create('doffing', function (Blueprint $table) {  // buat tabel doffing
            $table->string('id_doffing', 20)->primary();         // primary key: DOF-tanggal-shift-urut, contoh DOF-20261112-S1-001
            $table->date('tgl_doffing');                         // tanggal shift
            $table->enum('shift', ['S1', 'S2', 'S3']);           // shift 1, 2, atau 3
            $table->string('kode_troly', 10);                    // troly NORMAL tempat bobbin hasil doffing
            $table->string('merge', 10);                         // jenis benang; 1 troly hanya 1 merge
            $table->unsignedTinyInteger('jml_bobbin');           // jumlah bobbin dalam troly (maksimal = kapasitas troly)
            $table->decimal('berat_troly', 6, 2);                // berat troly dalam kg, contoh 85.50
            $table->string('keterangan', 100)->nullable();       // catatan, boleh kosong
            $table->string('created_by', 10);                    // id karyawan penginput (supervisor)
            $table->string('updated_by', 10)->nullable();        // id karyawan pengubah terakhir
            $table->timestamps();                                // kolom created_at dan updated_at

            $table->unique(['tgl_doffing', 'shift', 'kode_troly']); // 1 troly hanya sekali dalam 1 shift
            $table->foreign('kode_troly')->references('kode_troly')->on('troly'); // harus ada di tabel troly
            $table->foreign('merge')->references('merge')->on('benang');          // harus ada di tabel benang
            $table->foreign('created_by')->references('id_karyawan')->on('karyawan');
            $table->foreign('updated_by')->references('id_karyawan')->on('karyawan');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('doffing');                         // hapus tabel doffing
    }
};
