<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Product;

/**
 * ProductRepositoryInterface
 *
 * Defines the contract for product data access.
 * This abstraction allows swapping Eloquent for any other data source
 * (e.g. Elasticsearch, Redis cache) without touching business logic.
 */
interface ProductRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function getFeatured(int $limit = 8): Collection;

    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator;

    public function search(string $term, int $perPage = 12): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
