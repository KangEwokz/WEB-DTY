<?php

namespace Modules\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

// Satu-satunya model Eloquent di aplikasi.
// Dibutuhkan oleh sistem login Laravel dan Spatie Permission (HasRoles).
// Tidak dipakai untuk query data lain; data lain memakai DB::table(...).
class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';

    protected $fillable = ['username', 'password', 'id_karyawan', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
