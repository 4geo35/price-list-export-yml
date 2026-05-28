<?php

namespace GIS\PriceListExportYml;

use GIS\PriceListExportYml\Helpers\YmlActionsManager;
use Illuminate\Support\ServiceProvider;

class PriceListExportYmlServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . "/config/price-list-export-yml.php", 'price-list-export-yml');
        $this->initFacades();
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . "/routes/export.php");
    }

    protected function initFacades(): void
    {
        $this->app->singleton("price-list-export-yml-actions", function () {
            $managerClass = config("price-list-export-yml.customYmlActionsManager") ?? YmlActionsManager::class;
            return new $managerClass();
        });
    }
}
