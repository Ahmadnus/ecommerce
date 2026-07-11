<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(
        private readonly WishlistService $wishlist,
    ) {}

    /**
     * Show the user's wishlist page.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('wishlist.index', $this->wishlist->getWishlistData($user));
    }

    /**
     * Toggle a product in/out of the user's wishlist.
     * Returns JSON — called via AJAX from the heart button.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $result = $this->wishlist->toggle($user, $product);

        return response()->json([
            'wishlisted' => $result['wishlisted'],
            'message'    => $result['wishlisted']
                ? __('app.wishlist_messages.added')
                : __('app.wishlist_messages.removed'),
            'count'      => $result['count'],
        ]);
    }
}
