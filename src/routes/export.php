<?php

use Illuminate\Support\Facades\Route;
use GIS\PriceListExportYml\Http\Controllers\Export\PriceListController;

Route::middleware(["web"])
    ->as("export.")
    ->group(function () {
        Route::prefix(config("price-list-export-yml.ymlPrefix"))
            ->as("price-lists.")
            ->group(function () {
                $controllerClass = config("price-list-export-yml.customExportPriceListController") ?? PriceListController::class;
                Route::get("/yml", [$controllerClass, "yml"])->name("yml");
            });
    });
