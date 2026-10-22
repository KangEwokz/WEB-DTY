<?php

namespace Modules\Qct\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class QctServiceProvider extends ModuleServiceProvider
{
    // Nama modul (dipakai untuk namespace view: 'qct::...')
    protected string $name = 'Qct';

    protected string $nameLower = 'qct';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
