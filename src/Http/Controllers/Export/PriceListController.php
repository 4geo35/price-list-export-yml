<?php

namespace GIS\PriceListExportYml\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use GIS\PriceListExportYml\Facades\YmlActions;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class PriceListController extends Controller
{
    public function yml(): Response|ResponseFactory
    {
        $xml = YmlActions::getXmlContent();
        if (!$xml) { abort(404); }
        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
