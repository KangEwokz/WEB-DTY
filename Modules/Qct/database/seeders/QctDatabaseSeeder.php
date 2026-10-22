<?php

namespace Modules\Qct\Database\Seeders;

use Illuminate\Database\Seeder;

class QctDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GradeSeeder::class,
            JenisAbnormalSeeder::class,
            ContohDataSeeder::class,
        ]);
    }
}
