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
}
