<?php

use Illuminate\Database\Migrations\Migration;                    // class dasar migration
use Illuminate\Database\Schema\Blueprint;                        // Blueprint = alat untuk mendefinisikan kolom tabel
use Illuminate\Support\Facades\Schema;                           // Schema = untuk membuat/menghapus tabel

return new class extends Migration                               // migration ini: tabel abnormal_produksi (rincian per bobbin) + kolom total di laporan_shift
{
    public function up(): void                                   // dijalankan saat: php artisan migrate
    {
        Schema::table('laporan_shift', function (Blueprint $table) { // Schema::table = ubah tabel yang SUDAH ada (bukan membuat baru)
            $table->unsignedInteger('jml_abnormal')->default(0)->after('waste'); // total bobbin abnormal shift ini (dihitung otomatis dari rincian); after = letak kolom setelah waste
        });

        Schema::create('abnormal_produksi', function (Blueprint $table) { // temuan benang abnormal: 1 baris = 1 bobbin
            $table->string('id_laporan', 20);                    // laporan shift tempat temuan ini dicatat
            $table->unsignedSmallInteger('no_bobbin');           // nomor urut bobbin dalam laporan: 1, 2, 3, ...
            $table->string('kode_mesin', 8);                     // mesin asal bobbin
            $table->string('merge', 10);                         // jenis benang (merge)
            $table->string('id_jns_abn', 5);                     // jenis abnormal
            $table->string('kode_troly', 10);                    // troly abnormal tempat bobbin disimpan
            $table->string('keterangan', 100)->nullable();       // catatan tambahan, boleh kosong

            $table->primary(['id_laporan', 'no_bobbin']);        // 1 nomor bobbin hanya sekali dalam 1 laporan
            $table->foreign('id_laporan')->references('id_laporan')->on('laporan_shift')->cascadeOnDelete(); // jika laporan dihapus, rinciannya ikut terhapus
            $table->foreign('kode_mesin')->references('kode_mesin')->on('mesin');
            $table->foreign('merge')->references('merge')->on('benang');
            $table->foreign('id_jns_abn')->references('id_jns_abn')->on('jenis_abnormal');
            $table->foreign('kode_troly')->references('kode_troly')->on('troly');
        });
    }

    public function down(): void                                 // dijalankan saat: php artisan migrate:rollback
    {
        Schema::dropIfExists('abnormal_produksi');               // hapus tabel rincian
        Schema::table('laporan_shift', function (Blueprint $table) {
            $table->dropColumn('jml_abnormal');                  // hapus kolom total
        });
    }
};
