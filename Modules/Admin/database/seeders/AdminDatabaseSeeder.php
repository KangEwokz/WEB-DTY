<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DivisiSeeder::class,
            JabatanSeeder::class,
            AreaSeeder::class,
            KaryawanSeeder::class,
        ]);
    }
}
