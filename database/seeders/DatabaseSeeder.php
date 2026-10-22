<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Database\Seeders\AdminDatabaseSeeder;
use Modules\Auth\Database\Seeders\AuthDatabaseSeeder;
use Modules\Produksi\Database\Seeders\ProduksiDatabaseSeeder;
use Modules\Qct\Database\Seeders\QctDatabaseSeeder;

// Hanya memanggil seeder tiap modul, urutan mengikuti dependensi FK.
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminDatabaseSeeder::class,
            AuthDatabaseSeeder::class,
            ProduksiDatabaseSeeder::class,
            QctDatabaseSeeder::class,
        ]);
    }
}
