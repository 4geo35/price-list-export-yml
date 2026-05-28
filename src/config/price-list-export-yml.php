<?php

return [
    // Route settings
    "ymlCacheKey" => "price-list-yml-export",
    "ymlCacheLifetime" => env("PRICE_LIST_EXPORT_YML_CACHE_LIFETIME", 86400),
    "ymlPrefix" => env("PRICE_LIST_EXPORT_YML_PREFIX", "price-list-export"),

    // Xml settings
    "shopName" => env("PRICE_LIST_EXPORT_YML_SHOP_NAME", env("APP_NAME")),
    "companyName" => env("PRICE_LIST_EXPORT_YML_COMPANY_NAME", env("APP_NAME")),

    "currencyEnabled" => env("PRICE_LIST_EXPORT_YML_CURRENCY_ENABLED", true),
    "currencyId" => env("PRICE_LIST_EXPORT_YML_CURRENCY_ID", "RUR"),
    "currencyRate" => env("PRICE_LIST_EXPORT_YML_CURRENCY_RATE", 1),

    "productImageTemplate" => env("PRICE_LIST_EXPORT_YML_PRODUCT_IMAGE_TEMPLATE", "original"),

    // Controllers
    "customExportPriceListController" => null,

    // Managers
    "customYmlActionsManager" => null,
];
