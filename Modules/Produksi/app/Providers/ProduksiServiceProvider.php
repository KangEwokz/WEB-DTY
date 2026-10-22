<?php

namespace Modules\Produksi\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ProduksiServiceProvider extends ModuleServiceProvider
{
    // Nama modul (dipakai untuk namespace view: 'produksi::...')
    protected string $name = 'Produksi';

    protected string $nameLower = 'produksi';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
