<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;

/**
 * ReviewAdminService — business logic for the admin product-review
 * moderation screens. Never returns views/redirects.
 */
class ReviewAdminService
{
    /**
     * Filtered review list + status stats for the admin index.
     * $filters keys: status, product_id, rating.
     */
    public function getIndexData(array $filters): array
    {
        $query = ProductReview::with(['product', 'user'])
            ->orderByDesc('created_at');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        $reviews = $query->paginate(25)->withQueryString();

        $stats = [
            'total'    => ProductReview::count(),
            'pending'  => ProductReview::where('status', 'pending')->count(),
            'approved' => ProductReview::where('status', 'approved')->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
        ];

        return compact('reviews', 'stats');
    }

    public function getReviewsForProduct(Product $product)
    {
        return $product->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function approve(ProductReview $review): void
    {
        $review->update(['status' => 'approved']);
    }

    public function reject(ProductReview $review): void
    {
        $review->update(['status' => 'rejected']);
    }

    /**
     * Toggle the pinned flag. Returns the new pinned state.
     */
    public function togglePin(ProductReview $review): bool
    {
        $review->update(['is_pinned' => ! $review->is_pinned]);

        return $review->is_pinned;
    }

    public function delete(ProductReview $review): void
    {
        $review->delete();
    }
}
