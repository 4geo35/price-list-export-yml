<?php

namespace GIS\PriceListExportYml\Facades;

use GIS\PriceListExportYml\Helpers\YmlActionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null getXmlContent()
 *
 * @see YmlActionsManager
 */
class YmlActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "price-list-export-yml-actions";
    }
}
