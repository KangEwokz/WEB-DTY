<?php

namespace App\Models;                                            // "alamat" class ini = folder app/Models

use Illuminate\Foundation\Auth\User as Authenticatable;          // class dasar Laravel untuk user yang bisa login
use Illuminate\Support\Facades\DB;                               // DB = Query Builder untuk akses database

class User extends Authenticatable                               // model ini HANYA untuk fitur login (Auth::attempt, Auth::user); data lain pakai DB::table
{
    protected $table = 'users';                                  // nama tabel yang dipakai model ini

    protected $fillable = ['username', 'password', 'id_karyawan', 'id_role', 'status']; // kolom yang boleh diisi

    protected $hidden = ['password', 'remember_token'];          // kolom yang disembunyikan saat data user ditampilkan

    public function punyaAkses($id_permission)                   // cek apakah user ini boleh membuka fitur tertentu, contoh punyaAkses('admin.karyawan.create')
    {
        if ($this->id_role == 'superadmin') {                    // $this = user yang sedang dicek; jika role-nya superadmin
            return true;                                         // selalu boleh
        }

        return DB::table('role_permission')                      // cari di tabel role_permission
            ->join('role', 'role_permission.id_role', '=', 'role.id_role')                         // gabung tabel role (untuk cek status role)
            ->join('permission', 'role_permission.id_permission', '=', 'permission.id_permission') // gabung tabel permission (untuk cek status permission)
            ->where('role_permission.id_role', $this->id_role)   // milik role user ini
            ->where('role_permission.id_permission', $id_permission) // untuk fitur yang dicek
            ->where('role.status_aktif', 'Aktif')                // role-nya harus Aktif
            ->where('permission.status_aktif', 'Aktif')          // permission-nya harus Aktif
            ->exists();                                          // true jika ada datanya (boleh), false jika tidak ada (dilarang)
    }
}
