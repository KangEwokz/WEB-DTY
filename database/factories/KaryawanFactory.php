<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jns_kelamin = fake()->randomElement(['L', 'P']);

        return [
            'id_karyawan' => fake()->unique()->numerify('DTY-####'),
            'nama_karyawan' => fake()->name($jns_kelamin == 'L' ? 'male' : 'female'),
            'jns_kelamin' => $jns_kelamin,
            'departemen' => 'DTY',
            'id_divisi' => DB::table('divisi')->inRandomOrder()->value('id_divisi'),
            'id_jabatan' => DB::table('jabatan')->inRandomOrder()->value('id_jabatan'),
            'id_grup' => DB::table('grup')->inRandomOrder()->value('id_grup'),
        ];
    }
}
