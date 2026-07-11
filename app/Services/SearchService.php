<?php

namespace App\Services;

use App\Models\Product;

/**
 * SearchService — powers the live-search dropdown in the navbar.
 * Returns plain data arrays; JSON shaping stays in SearchController.
 */
class SearchService
{
    /**
     * Search active products by name/description/SKU (capped at 8 results
     * for dropdown performance) and format them for the frontend.
     *
     * Returns ['results' => Collection, 'total' => int].
     */
    public function search(string $q): array
    {
        $products = Product::active()
            ->with(['categories', 'variants' => fn($q) => $q->where('is_active', true)])
            ->where(fn($query) =>
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%")
            )
            ->orderByDesc('is_featured')
            ->limit(8)   // cap results for dropdown performance
            ->get();

        // Format for the frontend — keep payload small
        $results = $products->map(function (Product $p) {
            // Use CurrencyService if available, fallback to raw JOD
            try {
                $svc       = app(CurrencyService::class);
                $price     = $svc->convert((float) ($p->discount_price ?? $p->base_price));
                $formatted = $svc->format((float) ($p->discount_price ?? $p->base_price));
                $original  = $p->is_on_sale ? $svc->format((float) $p->base_price) : null;
            } catch (\Throwable) {
                $price     = (float) ($p->discount_price ?? $p->base_price);
                $formatted = number_format($price, 2) . ' د.أ';
                $original  = null;
            }

            return [
                'id'               => $p->id,
                'name'             => $p->name,
                'slug'             => $p->slug,
                'url'              => route('products.show', $p->slug),
                'price'            => $price,
                'price_formatted'  => $formatted,
                'is_on_sale'       => $p->is_on_sale,
                'original_price'   => $original,
                'image'            => $p->getFirstMediaUrl('products')
                                        ?: $p->image_url
                                        ?: null,
                'category'         => $p->categories->first()?->name,
                'in_stock'         => $p->in_stock,
            ];
        });

        return [
            'results' => $results,
            'total'   => $products->count(),
        ];
    }
}
