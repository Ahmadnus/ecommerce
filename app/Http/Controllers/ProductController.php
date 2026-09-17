<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

    /**
     * Product listing with category filter, search, and pagination.
     */
    public function index(Request $request): View
    {
        $data = $this->products->getStorefrontIndexData(
            $request->only(['category', 'search', 'sort'])
        );

        return view('products.index', $data);
    }

    /**
     * Single product detail page.
     */
    public function show(string $slug): View
    {
        $data = $this->products->getProductShowData($slug, auth()->user());

        return view('products.show', $data);
    }

    /**
     * Quick-view popup body.
     *
     * Returns just the modal's inner markup, which the storefront fetches and
     * injects when a product card is clicked. The full show() page above stays
     * the canonical, crawlable URL: the card links there, and this endpoint is
     * only reached by JavaScript, so the popup is a pure enhancement.
     */
    public function quickView(string $slug): View
    {
        $product = \App\Models\Product::where('slug', $slug)
            ->active()
            ->with([
                'media',
                'variants' => fn ($q) => $q->where('is_active', true),
            ])
            ->firstOrFail();

        return view('products.partials.quick-view', compact('product'));
    }
}
