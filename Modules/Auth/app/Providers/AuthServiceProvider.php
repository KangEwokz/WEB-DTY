<?php

namespace Modules\Auth\Providers;

use Illuminate\Pagination\Paginator;
use Nwidart\Modules\Support\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    // Nama modul (dipakai untuk namespace view: 'auth::...')
    protected string $name = 'Auth';

    protected string $nameLower = 'auth';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        // Link paginasi memakai class Bootstrap 5 (sesuai template Royal UI)
        Paginator::useBootstrapFive();
    }
}
