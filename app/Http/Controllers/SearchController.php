<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SearchController
 * ─────────────────────────────────────────────────────────────────────────────
 * Powers the live-search dropdown in the navbar.
 * Called by Alpine.js via fetch() as the user types.
 *
 * Route:  GET /api/search?q=...
 *
 * Returns JSON:
 * {
 *   "results": [
 *     {
 *       "id": 12,
 *       "name": "قميص رجالي أزرق",
 *       "slug": "qamees-rajali-azraq",
 *       "url":  "/products/qamees-rajali-azraq",
 *       "price": 12.50,
 *       "price_formatted": "12.50 د.أ",
 *       "is_on_sale": true,
 *       "original_price": 18.00,
 *       "image": "https://..."
 *     },
 *     ...
 *   ],
 *   "total": 4,
 *   "query": "قميص"
 * }
 */
class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        // Return empty immediately if query is too short
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'total' => 0, 'query' => $q]);
        }

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
                $svc       = app(\App\Services\CurrencyService::class);
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

        return response()->json([
            'results' => $results,
            'total'   => $products->count(),
            'query'   => $q,
        ]);
    }
}