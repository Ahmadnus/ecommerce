<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Product listing with category filter, search, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Product::query()
            ->active()
            ->with([
                'categories',
                'variants' => fn($q) => $q->where('is_active', true),
            ]);
 
        $currentCategory = null;
 
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)
                                ->orWhere('id', $request->category)
                                ->firstOrFail();
 
            $descendantIds = $category->getAllDescendants()->pluck('id');
            $categoryIds   = $descendantIds->prepend($category->id);
 
            $query->whereHas(
                'categories',
                fn($q) => $q->whereIn('categories.id', $categoryIds)
            );
 
            $currentCategory = $category;
        }
 
        if ($request->filled('search')) {
            $query->search($request->search);
        }
 
        match ($request->get('sort', 'featured')) {
            'price_asc'  => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };
 
        $products = $query->paginate(12)->withQueryString();
 
        $categoryTree = Category::active()
            ->roots()
            ->with('allActiveChildren')
            ->orderBy('sort_order')
            ->get();
 
        // ── NEW: Load active home sections for the dynamic homepage ─────────
        // Eager-load the category relation so section titles/category names
        // are available without extra queries in the view.
        $homeSections = HomeSection::active()
            ->ordered()
            ->with('category')
            ->get();
        // ───────────────────────────────────────────────────────────────────
 
        return view('products.index', [
            'products'        => $products,
            'categoryTree'    => $categoryTree,
            'currentCategory' => $currentCategory,
            'homeSections'    => $homeSections,   // ← NEW
        ]);
    }
    /**
     * Single product detail page.
     */
   public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with([
                'categories.parent',
                'variants' => fn($q) => $q->with('attributeValues.attribute')
                                          ->orderBy('is_active', 'desc')
                                          ->orderBy('price_override'),
            ])
            ->firstOrFail();
 
        $primaryCategory = $product->categories->first();
 
        $related = $primaryCategory
            ? Product::active()
                ->inCategory($primaryCategory->id)
                ->where('id', '!=', $product->id)
                ->with(['variants' => fn($q) => $q->where('is_active', true)])
                ->limit(4)
                ->get()
            : collect();
 
        $variantAttributes = $product->variants
            ->flatMap(fn(ProductVariant $v) => $v->attributeValues)
            ->unique('id')
            ->groupBy(fn(AttributeValue $av) => $av->attribute->name);
 
        return view('products.show', [
            'product'           => $product,
            'related'           => $related,
            'variantAttributes' => $variantAttributes,
        ]);
    }

}