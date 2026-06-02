<?php

namespace App\Http\Controllers;

use App\Models\PageHero;
use App\Models\TradingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class WebsiteTradingController extends Controller
{
    public function index(Request $request)
    {
        $products = collect();
        $categories = collect();
        $activeCategory = $request->query('category');

        if (Schema::hasTable('trading_products')) {
            $categories = Cache::remember('website_trading_categories', 3600, function () {
                return TradingProduct::active()
                    ->select('category')
                    ->distinct()
                    ->orderBy('category')
                    ->pluck('category')
                    ->filter()
                    ->values();
            });

            $products = Cache::remember('website_trading_products', 3600, function () {
                return TradingProduct::active()->ordered()->get();
            });

            if ($activeCategory) {
                $products = $products->filter(fn (TradingProduct $product) => $product->categoryKey() === $activeCategory)->values();
            }
        }

        return view('website.trading.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'pageHero' => $this->pageHero(),
        ]);
    }

    public function show(string $slug)
    {
        $product = TradingProduct::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = TradingProduct::active()
            ->whereKeyNot($product->getKey())
            ->where('category', $product->category)
            ->ordered()
            ->take(3)
            ->get();

        return view('website.trading.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'pageHero' => $this->pageHero(),
        ]);
    }

    private function pageHero(): ?PageHero
    {
        if (! Schema::hasTable('page_heroes')) {
            return null;
        }

        return PageHero::where('page_key', 'trading')
            ->where('is_active', true)
            ->first();
    }
}
