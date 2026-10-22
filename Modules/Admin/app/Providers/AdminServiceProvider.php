<?php

namespace Modules\Admin\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AdminServiceProvider extends ModuleServiceProvider
{
    // Nama modul (dipakai untuk namespace view: 'admin::...')
    protected string $name = 'Admin';

    protected string $nameLower = 'admin';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
