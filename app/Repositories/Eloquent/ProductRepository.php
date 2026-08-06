<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * ProductRepository (Eloquent Implementation)
 *
 * Handles all product database queries.
 * Business logic belongs in ProductService — this class only deals with data retrieval/persistence.
 */
class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Product $model) {}

    /**
     * Get paginated products with optional filters.
     * Filters: category_id, search, min_price, max_price, featured
     */
    public function getAllPaginated(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->active()->with('category');

        if (!empty($filters['category_id'])) {
            $query->inCategory($filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['featured'])) {
            $query->featured();
        }

        // Sorting
        $sortBy  = $filters['sort_by']  ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->with('category')->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->active()->with('category')->where('slug', $slug)->first();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return $this->model->active()->featured()->with('category')->limit($limit)->get();
    }

    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->active()->inCategory($categoryId)->with('category')->paginate($perPage);
    }

    public function search(string $term, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->active()->search($term)->with('category')->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }
}
