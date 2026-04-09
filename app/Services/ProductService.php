<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
     * Check if a product has sufficient stock for the requested quantity.
     */
    public function checkStock(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->findById($productId);
        return $product && $product->stock_quantity >= $quantity;
    }

    /**
     * Decrease stock after a successful order (called by OrderService).
     */
    public function decreaseStock(int $productId, int $quantity): void
    {
        $product = $this->productRepository->findById($productId);
        if ($product) {
            $this->productRepository->update($productId, [
                'stock_quantity' => max(0, $product->stock_quantity - $quantity),
            ]);
        }
    }
}
