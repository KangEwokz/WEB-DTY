<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SuperAdmin selalu diizinkan untuk semua permission
        Gate::before(function ($user) {
            return $user->hasRole('SuperAdmin') ? true : null;
        });

        // Modul Karyawan hanya untuk SuperAdmin (lewat Gate::before) dan karyawan divisi Admin
        Gate::define('akses-karyawan', function ($user) {
            return DB::table('karyawan')
                ->where('id_karyawan', $user->id_karyawan)
                ->where('id_divisi', 'ADM')
                ->exists();
        });
    }
}
