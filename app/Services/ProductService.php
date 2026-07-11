<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * ProductService
 *
 * Contains all business logic related to products.
 * Controllers should call this service — never query the DB directly from a controller.
 */
class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Get paginated products with filters from request input.
     */
    public function getFilteredProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        // Clean/validate filter inputs before passing to repository
        $cleanFilters = array_filter([
            'category_id' => $filters['category'] ?? null,
            'search'      => $filters['search']   ?? null,
            'min_price'   => $filters['min_price'] ?? null,
            'max_price'   => $filters['max_price'] ?? null,
            'sort_by'     => in_array($filters['sort_by'] ?? '', ['price', 'name', 'created_at']) ? $filters['sort_by'] : 'created_at',
            'sort_dir'    => in_array($filters['sort_dir'] ?? '', ['asc', 'desc']) ? $filters['sort_dir'] : 'desc',
        ]);

        return $this->productRepository->getAllPaginated($cleanFilters, $perPage);
    }

    /**
     * Get a single product by its URL slug. Returns null if not found or inactive.
     */
    public function getProductBySlug(string $slug)
    {
        return $this->productRepository->findBySlug($slug);
    }

    /**
     * Get featured products for homepage display.
     */
    public function getFeaturedProducts(int $limit = 8): Collection
    {
        return $this->productRepository->getFeatured($limit);
    }

    /**
     * Get all active categories (for filter sidebar).
     */
    public function getAllCategories(): Collection
    {
        return Category::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * All data for the storefront product listing page.
     * $filters keys: category, search, sort.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if the
     *         requested category doesn't exist (404, as before).
     */
    public function getStorefrontIndexData(array $filters): array
    {
        $query = \App\Models\Product::query()
            ->active()
            ->with([
                'categories',
                'variants' => fn($q) => $q->where('is_active', true),
            ]);

        $currentCategory = null;

        if (!empty($filters['category'])) {
            $category = Category::where('slug', $filters['category'])
                                ->orWhere('id', $filters['category'])
                                ->firstOrFail();

            $descendantIds = $category->getAllDescendants()->pluck('id');
            $categoryIds   = $descendantIds->prepend($category->id);

            $query->whereHas(
                'categories',
                fn($q) => $q->whereIn('categories.id', $categoryIds)
            );

            $currentCategory = $category;
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        match ($filters['sort'] ?? 'featured') {
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

        // Active home sections for the dynamic homepage, with the category
        // relation eager-loaded so section titles/category names are
        // available without extra queries in the view.
        $homeSections = \App\Models\HomeSection::active()
            ->ordered()
            ->with('category')
            ->get();

        return [
            'products'        => $products,
            'categoryTree'    => $categoryTree,
            'currentCategory' => $currentCategory,
            'homeSections'    => $homeSections,
        ];
    }

    /**
     * All data for the single product detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException (404)
     */
    public function getProductShowData(string $slug, ?\App\Models\User $user): array
    {
        $product = \App\Models\Product::where('slug', $slug)
            ->active()
            ->with([
                'categories.parent',
                'media',
                'variants' => fn($q) => $q->with('attributeValues.attribute')
                                          ->orderBy('is_active', 'desc')
                                          ->orderBy('price_override'),
            ])
            ->firstOrFail();

        $primaryCategory = $product->categories->first();

        $related = $primaryCategory
            ? \App\Models\Product::active()
                ->inCategory($primaryCategory->id)
                ->where('id', '!=', $product->id)
                ->with([
                    'media',
                    'variants' => fn($q) => $q->where('is_active', true),
                ])
                ->limit(4)
                ->get()
            : collect();

        $variantAttributes = $product->variants
            ->flatMap(fn(\App\Models\ProductVariant $v) => $v->attributeValues)
            ->unique('id')
            ->groupBy(fn(\App\Models\AttributeValue $av) => $av->attribute->name);

        $isWishlisted = $user
            ? $user->wishlistedProducts()
                   ->where('product_id', $product->id)
                   ->exists()
            : false;

        // Reviews
        $reviews = $product->reviews()
            ->where('status', 'approved')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'review_page')
            ->withQueryString();

        $userHasReviewed = $user
            ? \App\Models\ProductReview::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->exists()
            : false;

        return [
            'product'           => $product,
            'related'           => $related,
            'variantAttributes' => $variantAttributes,
            'isWishlisted'      => $isWishlisted,
            'reviews'           => $reviews,
            'userHasReviewed'   => $userHasReviewed,
        ];
    }

    /**
     * Check if a product has sufficient stock for the requested quantity.
     */
  public function checkStock(int $productId, int $quantity, ?int $variantId = null): bool
{
    if ($variantId) {
        $variant = \App\Models\ProductVariant::find($variantId);
        if (!$variant || $variant->stock_quantity < $quantity) {
            throw new \Exception("عذراً، المخزون غير كافٍ لهذه النسخة.");
        }
    } else {
        $product = \App\Models\Product::find($productId);
        // منطق الفحص للمنتجات التي ليس لها نسخ
    }
    
    return true;
}

    /**
     * Decrease stock after a successful order (called by OrderService).
     */
   public function decreaseStock(int $productId, int $quantity, ?int $variantId = null): void
{
    if ($variantId) {
        // الخصم من مخزون النسخة (المقاس/اللون)
        $variant = \App\Models\ProductVariant::findOrFail($variantId);
        
        if ($variant->stock_quantity < $quantity) {
            throw new \Exception("المخزون غير كافٍ للنسخة المحددة.");
        }

        $variant->decrement('stock_quantity', $quantity);
    } else {
        // الخصم من مخزون المنتج الرئيسي (إذا كان المنتج لا يحتوي على نسخ)
        $product = \App\Models\Product::findOrFail($productId);
        
        // تأكد أن العمود لا يزال موجوداً في جدول المنتجات لو كنت تستخدم النظام الهجين
        if (Schema::hasColumn('products', 'stock_quantity')) {
             $product->decrement('stock_quantity', $quantity);
        }
    }
}
}
