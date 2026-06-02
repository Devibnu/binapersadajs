<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageUploadHelper;
use App\Http\Controllers\Controller;
use App\Models\TradingProduct;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TradingProductController extends Controller
{
    public function index()
    {
        $tradingProducts = TradingProduct::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('paneladmin.trading-products.index', compact('tradingProducts'));
    }

    public function create()
    {
        return view('paneladmin.trading-products.create', [
            'tradingProduct' => new TradingProduct([
                'is_active' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeImage($request, $validated);

        $tradingProduct = TradingProduct::create($validated);
        $this->clearWebsiteTradingCache();
        app(ActivityLogger::class)->log('create', 'Trading Products', 'Trading product ditambahkan: ' . $tradingProduct->name, $tradingProduct);

        return redirect()
            ->route('paneladmin.trading-products.index')
            ->with('success', $this->successMessage('Trading product berhasil ditambahkan.', $request));
    }

    public function show(TradingProduct $tradingProduct)
    {
        return view('paneladmin.trading-products.show', compact('tradingProduct'));
    }

    public function edit(TradingProduct $tradingProduct)
    {
        return view('paneladmin.trading-products.edit', compact('tradingProduct'));
    }

    public function update(Request $request, TradingProduct $tradingProduct)
    {
        $validated = $this->validatedData($request, $tradingProduct);
        $validated['slug'] = $this->slugFor($validated['slug'] ?? null, $validated['name'], $tradingProduct);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $this->storeImage($request, $validated, $tradingProduct);

        $tradingProduct->update($validated);
        $this->clearWebsiteTradingCache();
        app(ActivityLogger::class)->log('update', 'Trading Products', 'Trading product diperbarui: ' . $tradingProduct->name, $tradingProduct);

        return redirect()
            ->route('paneladmin.trading-products.index')
            ->with('success', $this->successMessage('Trading product berhasil diperbarui.', $request));
    }

    public function destroy(TradingProduct $tradingProduct)
    {
        app(ActivityLogger::class)->log('delete', 'Trading Products', 'Trading product dihapus: ' . $tradingProduct->name, $tradingProduct);
        ImageUploadHelper::deleteStoredImage($tradingProduct->image);
        $tradingProduct->delete();
        $this->clearWebsiteTradingCache();

        return redirect()
            ->route('paneladmin.trading-products.index')
            ->with('success', 'Trading product berhasil dihapus.');
    }

    private function validatedData(Request $request, ?TradingProduct $tradingProduct = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('trading_products', 'slug')->ignore($tradingProduct),
            ],
            'category' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);
    }

    private function slugFor(?string $slug, string $name, ?TradingProduct $tradingProduct = null): string
    {
        $baseSlug = Str::slug($slug ?: $name) ?: 'trading-product';
        $candidate = $baseSlug;
        $counter = 1;

        while (
            TradingProduct::where('slug', $candidate)
                ->when($tradingProduct, fn ($query) => $query->whereKeyNot($tradingProduct->getKey()))
                ->exists()
        ) {
            $candidate = $baseSlug . '-' . $counter++;
        }

        return $candidate;
    }

    private function storeImage(Request $request, array &$validated, ?TradingProduct $tradingProduct = null): void
    {
        if (! $request->hasFile('image')) {
            unset($validated['image']);

            return;
        }

        if ($tradingProduct?->image) {
            ImageUploadHelper::deleteStoredImage($tradingProduct->image);
        }

        $validated['image'] = ImageUploadHelper::uploadAndCompress($request->file('image'), 'trading-products', 1200);
    }

    private function clearWebsiteTradingCache(): void
    {
        Cache::forget('website_trading_products');
        Cache::forget('website_trading_categories');
    }

    private function successMessage(string $message, Request $request): string
    {
        return $request->hasFile('image')
            ? $message . ' Gambar berhasil diupload dan dioptimasi.'
            : $message;
    }
}
