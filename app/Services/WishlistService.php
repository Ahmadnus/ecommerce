<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * WishlistService — business logic for the user wishlist (listing and
 * AJAX toggle). Never returns responses.
 */
class WishlistService
{
    /**
     * Paginated wishlisted products + raw wishlist IDs for a user.
     */
    public function getWishlistData(User $user): array
    {
        $products = $user->wishlistedProducts()
            ->with([
                'categories',
                'variants' => fn ($q) => $q->where('is_active', true),
            ])
            ->active()
            ->latest('wishlists.created_at')
            ->paginate(12);

        // Get the IDs directly from wishlist table for accuracy
        $wishlistedIds = DB::table('wishlists')
            ->where('user_id', $user->id)
            ->pluck('product_id')
            ->toArray();

        return compact('products', 'wishlistedIds');
    }

    /**
     * Toggle a product in/out of the wishlist.
     *
     * Returns ['wishlisted' => bool, 'count' => int] (the new state).
     */
    public function toggle(User $user, Product $product): array
    {
        $isWishlisted = $user->wishlistedProducts()
            ->where('product_id', $product->id)
            ->exists();

        if ($isWishlisted) {
            $user->wishlistedProducts()->detach($product->id);

            return [
                'wishlisted' => false,
                'count'      => $user->wishlistedProducts()->count(),
            ];
        }

        $user->wishlistedProducts()->attach($product->id);

        return [
            'wishlisted' => true,
            'count'      => $user->wishlistedProducts()->count(),
        ];
    }
}
