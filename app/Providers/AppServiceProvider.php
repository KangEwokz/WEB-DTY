<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;                             // Paginator = pengatur tampilan link halaman (1, 2, 3, ...)
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
        Paginator::useBootstrapFive();                           // link halaman memakai tampilan Bootstrap 5 (sama dengan template Royal-UI)
    }
}
