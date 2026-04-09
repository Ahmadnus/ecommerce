<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

/**
 * ProductController
 *
 * Thin controller — delegates all logic to ProductService.
 * Only responsible for: request handling, calling the service, returning responses.
 */
class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /**
     * Display paginated product grid with filters.
     * GET /products
     */
    public function index(Request $request)
    {
        $filters    = $request->only(['category', 'search', 'min_price', 'max_price', 'sort_by', 'sort_dir']);
        $products   = $this->productService->getFilteredProducts($filters);
        $categories = $this->productService->getAllCategories();
        $featured   = $this->productService->getFeaturedProducts(4);

        return view('products.index', compact('products', 'categories', 'featured', 'filters'));
    }

    /**
     * Display a single product's detail page.
     * GET /products/{slug}
     */
    public function show(string $slug)
    {
        $product = $this->productService->getProductBySlug($slug);

        if (!$product) {
            abort(404, 'Product not found.');
        }

        // Get related products from the same category
        $related = $this->productService->getFilteredProducts([
            'category_id' => $product->category_id,
        ], 4);

        return view('products.show', compact('product', 'related'));
    }
}
