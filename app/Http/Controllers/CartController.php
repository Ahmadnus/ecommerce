<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CartController
 *
 * Handles cart operations. All mutating actions (add/update/remove) return JSON
 * for AJAX use. The cart page itself is a standard Blade view.
 */
class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    /**
     * Show the cart page.
     * GET /cart
     */
    public function index()
    {
        $summary = $this->cartService->getSummary();
        return view('cart.index', compact('summary'));
    }

    /**
     * Add item to cart (AJAX).
     * POST /cart/add
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        try {
            $this->cartService->addItem(
                $request->input('product_id'),
                $request->input('quantity', 1)
            );

            return response()->json([
                'success'    => true,
                'message'    => 'Item added to cart!',
                'item_count' => $this->cartService->getItemCount(),
                'subtotal'   => number_format($this->cartService->getSubtotal(), 2),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update cart item quantity (AJAX).
     * PATCH /cart/update
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'required|integer|min:0|max:99',
        ]);

        try {
            $this->cartService->updateItem(
                $request->input('product_id'),
                $request->input('quantity')
            );

            $summary = $this->cartService->getSummary();

            return response()->json([
                'success'    => true,
                'item_count' => $summary['item_count'],
                'subtotal'   => number_format($summary['subtotal'], 2),
                'tax'        => number_format($summary['tax'], 2),
                'shipping'   => number_format($summary['shipping'], 2),
                'total'      => number_format($summary['total'], 2),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove item from cart (AJAX).
     * DELETE /cart/remove/{productId}
     */
    public function remove(int $productId): JsonResponse
    {
        $this->cartService->removeItem($productId);

        $summary = $this->cartService->getSummary();

        return response()->json([
            'success'    => true,
            'message'    => 'Item removed.',
            'item_count' => $summary['item_count'],
            'subtotal'   => number_format($summary['subtotal'], 2),
            'tax'        => number_format($summary['tax'], 2),
            'shipping'   => number_format($summary['shipping'], 2),
            'total'      => number_format($summary['total'], 2),
            'empty'      => empty($summary['items']),
        ]);
    }

    /**
     * Get cart item count (for nav badge refresh).
     * GET /cart/count
     */
    public function count(): JsonResponse
    {
        return response()->json(['count' => $this->cartService->getItemCount()]);
    }
}
