<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Show the user's wishlist page.
     */
public function index(): View
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $products = $user->wishlistedProducts()
        ->with(['categories', 'variants' => fn($q) => $q->where('is_active', true)])
        ->active()
        ->latest('wishlists.created_at')
        ->paginate(12);

    // جلب الـ IDs مباشرة من جدول المفضلة لضمان الدقة
    $wishlistedIds = DB::table('wishlists')
        ->where('user_id', $user->id)
        ->pluck('product_id')
        ->toArray();

    return view('wishlist.index', compact('products', 'wishlistedIds'));
}
    /**
     * Toggle a product in/out of the user's wishlist.
     * Returns JSON — called via AJAX from the heart button.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {/** @var \App\Models\User $user */
        $user = Auth::user();

        $isWishlisted = $user->wishlistedProducts()
                             ->where('product_id', $product->id)
                             ->exists();

        if ($isWishlisted) {
            // Remove
            $user->wishlistedProducts()->detach($product->id);

            return response()->json([
                'wishlisted' => false,
                'message'    => 'تمت الإزالة من المفضلة',
                'count'      => $user->wishlistedProducts()->count(),
            ]);
        }

        // Add
        $user->wishlistedProducts()->attach($product->id);

        return response()->json([
            'wishlisted' => true,
            'message'    => 'تمت الإضافة إلى المفضلة ❤️',
            'count'      => $user->wishlistedProducts()->count(),
        ]);
    }
}