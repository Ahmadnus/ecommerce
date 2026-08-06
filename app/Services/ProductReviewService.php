<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

/**
 * ProductReviewService — business logic for storefront review submission:
 * product lookup, rate limiting, duplicate checks, and creation.
 * Never returns responses; returns an error message string or null.
 */
class ProductReviewService
{
    /**
     * Find the active product for a review by slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException (404)
     */
    public function findActiveProduct(string $slug): Product
    {
        return Product::where('slug', $slug)->where('status', 'active')->firstOrFail();
    }

    /**
     * Submit a review. Returns a user-facing error message when the
     * submission is rejected (rate limit / duplicate), or null on success.
     *
     * $input keys: reviewer_name, reviewer_email, rating, comment.
     */
    public function submitReview(Product $product, ?User $user, array $input, string $ip): ?string
    {
        // Rate limiting — max 5 per IP per hour
        $key = 'review:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return 'لقد تجاوزت الحد المسموح به. يرجى المحاولة لاحقاً.';
        }
        RateLimiter::hit($key, 3600);

        // Prevent duplicate for logged-in users
        if ($user) {
            if (ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
                return 'لقد قمت بتقييم هذا المنتج مسبقاً.';
            }
        }

        // Prevent duplicate for guests by email
        if (!$user && !empty($input['reviewer_email'])) {
            if (ProductReview::where('product_id', $product->id)->where('reviewer_email', $input['reviewer_email'])->exists()) {
                return 'تم استلام تقييمك لهذا المنتج مسبقاً.';
            }
        }

        ProductReview::create([
            'product_id'     => $product->id,
            'user_id'        => $user?->id,
            'reviewer_name'  => $user ? $user->name : ($input['reviewer_name'] ?? null),
            'reviewer_email' => $user ? $user->email : ($input['reviewer_email'] ?? null),
            'rating'         => (int) ($input['rating'] ?? 0),
            'comment'        => $input['comment'] ?? null,
            'status'         => 'pending',
        ]);

        return null;
    }
}
