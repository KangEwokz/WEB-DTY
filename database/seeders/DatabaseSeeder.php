<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        DB::table('divisi')->insert([
            ['id_divisi' => 'ADM', 'nm_divisi' => 'Admin'],
            ['id_divisi' => 'LGS', 'nm_divisi' => 'Logistik'],
            ['id_divisi' => 'PRD', 'nm_divisi' => 'Produksi'],
            ['id_divisi' => 'MK', 'nm_divisi' => 'Mekanik'],
            ['id_divisi' => 'INS', 'nm_divisi' => 'Instrumen'],
            ['id_divisi' => 'ELK', 'nm_divisi' => 'Elektrik'],
            ['id_divisi' => 'QCT', 'nm_divisi' => 'QCT'],
            ['id_divisi' => 'PKG', 'nm_divisi' => 'Packing'],
        ]);

        DB::table('jabatan')->insert([
            ['id_jabatan' => 'M1', 'nm_jabatan' => 'Manager'],
            ['id_jabatan' => 'M2', 'nm_jabatan' => 'Supervisor'],
            ['id_jabatan' => 'M3', 'nm_jabatan' => 'Foreman'],
            ['id_jabatan' => 'M4', 'nm_jabatan' => 'Leader'],
            ['id_jabatan' => 'M5', 'nm_jabatan' => 'Operator'],
        ]);

        DB::table('grup')->insert([
            ['id_grup' => 'A', 'nm_grup' => 'Senin'],
            ['id_grup' => 'B', 'nm_grup' => 'Selasa'],
            ['id_grup' => 'C', 'nm_grup' => 'Rabu'],
            ['id_grup' => 'D', 'nm_grup' => 'Kamis'],
            ['id_grup' => 'E', 'nm_grup' => 'Jumat'],
            ['id_grup' => 'F', 'nm_grup' => 'Sabtu'],
            ['id_grup' => 'G', 'nm_grup' => 'Minggu'],
            ['id_grup' => 'NG', 'nm_grup' => 'Non Grup'],
        ]);

        DB::table('shift')->insert([
            ['kode_shift' => 'GS', 'nama_shift' => 'General Shift', 'jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00'],
            ['kode_shift' => 'S1', 'nama_shift' => 'Shift Pagi', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:00:00'],
            ['kode_shift' => 'S2', 'nama_shift' => 'Shift Siang', 'jam_masuk' => '15:00:00', 'jam_pulang' => '23:00:00'],
            ['kode_shift' => 'S3', 'nama_shift' => 'Shift Malam', 'jam_masuk' => '23:00:00', 'jam_pulang' => '07:00:00'],
            ['kode_shift' => 'OFF', 'nama_shift' => 'Libur', 'jam_masuk' => null, 'jam_pulang' => null],
        ]);

        DB::table('karyawan')->insert([
            [
                'id_karyawan' => 'DTY-0001',
                'nama_karyawan' => 'Budi Santoso',
                'jns_kelamin' => 'L',
                'departemen' => 'DTY',
                'id_divisi' => 'ADM',
                'id_jabatan' => 'M1',
                'id_grup' => 'A',
            ],
            [
                'id_karyawan' => 'DTY-0002',
                'nama_karyawan' => 'Siti Aminah',
                'jns_kelamin' => 'P',
                'departemen' => 'DTY',
                'id_divisi' => 'ADM',
                'id_jabatan' => 'M2',
                'id_grup' => 'NG',
            ],
            [
                'id_karyawan' => 'DTY-0003',
                'nama_karyawan' => 'Andi Pratama',
                'jns_kelamin' => 'L',
                'departemen' => 'DTY',
                'id_divisi' => 'ADM',
                'id_jabatan' => 'M3',
                'id_grup' => 'B',
            ],
            [
                'id_karyawan' => 'DTY-0004',
                'nama_karyawan' => 'Dewi Lestari',
                'jns_kelamin' => 'P',
                'departemen' => 'DTY',
                'id_divisi' => 'ADM',
                'id_jabatan' => 'M4',
                'id_grup' => 'C',
            ],
            [
                'id_karyawan' => 'DTY-0005',
                'nama_karyawan' => 'Rudi Hartono',
                'jns_kelamin' => 'L',
                'departemen' => 'DTY',
                'id_divisi' => 'ADM',
                'id_jabatan' => 'M5',
                'id_grup' => 'NG',
            ],
        ]);

        Karyawan::factory()->count(50)->create();

        $menu = ['divisi', 'jabatan', 'grup', 'shift', 'karyawan'];
        $aksi = ['view', 'create', 'edit', 'delete'];

        foreach ($menu as $m) {
            foreach ($aksi as $a) {
                Permission::create(['name' => $m.'.'.$a]);
            }
        }

        Role::create(['name' => 'SuperAdmin']);

        Role::create(['name' => 'Admin'])->givePermissionTo(Permission::all());

        Role::create(['name' => 'User'])->givePermissionTo([
            'divisi.view',
            'jabatan.view',
            'grup.view',
            'shift.view',
            'karyawan.view',
        ]);

        User::create([
            'id_karyawan' => 'DTY-0001',
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@dty.local',
            'password' => 'password123@',
        ])->assignRole('SuperAdmin');

        User::create([
            'id_karyawan' => 'DTY-0002',
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@dty.local',
            'password' => 'password123@',
        ])->assignRole('Admin');

        User::create([
            'id_karyawan' => 'DTY-0003',
            'name' => 'Andi Pratama',
            'email' => 'andi.pratama@dty.local',
            'password' => 'password123@',
        ])->assignRole('User');

        User::create([
            'id_karyawan' => 'DTY-0004',
            'name' => 'Dewi Lestari',
            'email' => 'dewi.lestari@dty.local',
            'password' => 'password123@',
        ])->assignRole('User');

        User::create([
            'id_karyawan' => 'DTY-0005',
            'name' => 'Rudi Hartono',
            'email' => 'rudi.hartono@dty.local',
            'password' => 'password123@',
        ])->assignRole('User');
    }
}
