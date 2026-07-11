<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
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
    public function __construct(
        private readonly SearchService $search,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        // Return empty immediately if query is too short
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => [], 'total' => 0, 'query' => $q]);
        }

        return response()->json(array_merge(
            $this->search->search($q),
            ['query' => $q]
        ));
    }
}
