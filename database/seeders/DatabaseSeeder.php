<?php

namespace Database\Seeders;                                      // "alamat" class ini = folder database/seeders

use Illuminate\Database\Seeder;                                  // class dasar seeder
use Modules\Admin\Database\Seeders\AdminDatabaseSeeder;          // panggil seeder modul Admin
use Modules\Auth\Database\Seeders\AuthDatabaseSeeder;            // panggil seeder modul Auth
use Modules\Produksi\Database\Seeders\ProduksiDatabaseSeeder;    // panggil seeder modul Produksi

class DatabaseSeeder extends Seeder                              // seeder utama, pemanggil seeder-seeder lain
{
    public function run(): void                                  // dijalankan saat: php artisan db:seed
    {
        $this->call([                                            // jalankan seeder sesuai urutan
            AdminDatabaseSeeder::class,                          // 1. isi divisi, jabatan, area, karyawan
            AuthDatabaseSeeder::class,                           // 2. isi users (butuh karyawan yang sudah ada)
            ProduksiDatabaseSeeder::class,                       // 3. contoh area & operator produksi + permission produksi (butuh role)
        ]);
    }
}
