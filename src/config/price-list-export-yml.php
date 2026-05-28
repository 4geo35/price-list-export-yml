<?php

return [
    // Route settings
    "ymlCacheKey" => "price-list-yml-export",
    "ymlCacheLifetime" => env("CATALOG_EXPORT_YML_CACHE_LIFETIME", 86400),
    "ymlPrefix" => env("CATALOG_EXPORT_YML_PREFIX", "price-list-export"),

    // Controllers
    "customExportPriceListController" => null,

    // Managers
    "customYmlActionsManager" => null,
];
