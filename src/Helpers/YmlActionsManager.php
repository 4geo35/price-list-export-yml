<?php

namespace GIS\PriceListExportYml\Helpers;

use GIS\PriceList\Interfaces\PriceListInterface;
use GIS\PriceList\Interfaces\PriceListItemInterface;
use GIS\PriceList\Models\PriceList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class YmlActionsManager
{
    protected SimpleXMLElement|null $catalog;
    protected SimpleXMLElement|null $shop;
    protected SimpleXMLElement|null $categories;
    protected SimpleXMLElement|null $offers;

    public function getXMLContent(): string|null
    {
        $postfix = config("price-list.singlePage") ? "-single" : "-full";
        $key = config("price-list-export-yml.ymlCacheKey") . $postfix;
        $lifetime = config("price-list-export-yml.ymlCacheLifetime");

        return Cache::remember($key, $lifetime, function () {
            $this->fillDocument();
            return $this->catalog->asXML();
        });
    }

    protected function fillDocument(): void
    {
        $this->initCatalog();
        $categories = $this->getRootCategories();
        foreach ($categories as $category) {
            $this->fillCategoryProducts($category);
        }
    }

    protected function initCatalog(): void
    {
        $this->catalog = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><yml_catalog></yml_catalog>');
        $this->catalog->addAttribute("date", now()->toIso8601String());

        $this->shop = $this->catalog->addChild("shop");
        $this->shop->addChild("name", config("price-list-export-yml.shopName"));
        $this->shop->addChild("company", config("price-list-export-yml.companyName"));
        $this->shop->addChild("url", config("app.url"));
        $this->initCurrencies();
        $this->categories = $this->shop->addChild("categories");
        $this->offers = $this->shop->addChild("offers");
    }

    protected function initCurrencies(): void
    {
        if (! config("price-list-export-yml.currencyEnabled")) { return;}
        $currencies = $this->shop->addChild("currencies");
        $currency = $currencies->addChild("currency");
        $currency->addAttribute("id", config("price-list-export-yml.currencyId"));
        $currency->addAttribute("rate", config("price-list-export-yml.currencyRate"));
    }

    protected function getRootCategories(): Collection
    {
        $categoryModel = config("price-list.customPriceListModel") ?? PriceList::class;
        return $categoryModel::query()
            ->select("id", "parent_id", "slug", "title")
            ->whereNotNull("published_at")
            ->whereNull("parent_id")
            ->get();
    }

    protected function fillCategoryProducts(PriceListInterface $category): void
    {
        $this->addCategory($category);

        $children = $this->getCategoryChildren($category);
        if ($children->count() > 0) {
            foreach ($children as $child) {
                $this->fillCategoryProducts($child);
            }
        }

        $products = $this->getCategoryProducts($category);
        if (! $products->count()) { return; }
        foreach ($products as $product) {
            $this->addOffer($product);
        }
    }

    protected function addCategory(PriceListInterface $category): void
    {
        $element = $this->categories->addChild("category", $category->title);
        $element->addAttribute("id", $category->id);
        if ($category->parent_id) {
            $element->addAttribute("parentId", $category->parent_id);
        }
    }

    protected function getCategoryChildren(PriceListInterface $category): Collection
    {
        return $category->children()
            ->select("id", "parent_id", "slug", "title")
            ->whereNotNull("published_at")
            ->get();
    }

    protected function getCategoryProducts(PriceListInterface $category): Collection
    {
        return $category->items()
            ->with("image")
            ->get();
    }

    protected function addOffer(PriceListItemInterface $product): void
    {
        $productPrice = $this->getProductPrice($product);
        if (! $productPrice) { return; }

        $imageUrl = $this->getProductImageUrl($product);
        $description = $this->getProductDescription($product);
        $url = $this->getProductUrl($product);

        $element = $this->offers->addChild("offer");
        $element->addAttribute("id", $product->id);
        $element->addChild("name", htmlspecialchars($product->title));
        $element->addChild("url", $url);

        $element->addChild("price", $productPrice);
        $element->addChild("currencyId", config("price-list-export-yml.currencyId"));
        $element->addChild("categoryId", $product->priceList->id);
        if ($description) {
            $element->addChild("description", $description);
        }
        if ($imageUrl) {
            $element->addChild("picture", $imageUrl);
        }
    }

    protected function getProductPrice(PriceListItemInterface $product): float
    {
        return floatval($product->price);
    }

    protected function getProductImageUrl(PriceListItemInterface $product): ?string
    {
        if (! $product->image) { return null; }
        $template = config("price-list-export-yml.productImageTemplate");
        return route("thumb-img", ["template" => $template, "filename" => $product->image->file_name]);
    }

    protected function getProductDescription(PriceListItemInterface $product): ?string
    {
        return $product->short;
    }

    protected function getProductUrl(PriceListItemInterface $product): ?string
    {
        if (config("price-list.singlePage")) {
            return route("web.price-lists.index") . "#list-" . $product->priceList->slug;
        } else {
            return route("web.price-lists.show", ["priceList" => $product->priceList]);
        }
    }
}
