<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    // ── View ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $summary = $this->cart->getSummary();
        return view('cart.index', compact('summary'));
    }

    // ── AJAX: Add to cart ────────────────────────────────────────────────────
    /**
     * Validation and stock rules live in CartService::validateAdd().
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'nullable|integer|min:1|max:100',

        ]);

        $productId = $request->integer('product_id');
        $quantity  = max(1, $request->integer('quantity', 1));
        $variantId = $request->integer('variant_id') ?: null;

        if ($error = $this->cart->validateAdd($productId, $quantity, $variantId)) {
            return response()->json([
                'success' => false,
                'message' => $error,
            ], 422);
        }

        // ── Add to cart ───────────────────────────────────────────────────────
        $result = $this->cart->add($productId, $quantity, $variantId);

        return response()->json([
            'success'    => true,
            'message'    => 'تمت الإضافة إلى السلة ✓',
            'item_count' => $result['item_count'],
        ]);
    }

    // ── AJAX: Update qty ─────────────────────────────────────────────────────

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $this->cart->update($request->string('item_key'), $request->integer('quantity'));

        return response()->json(array_merge(
            ['success' => true, 'empty' => $this->cart->isEmpty()],
            $this->cart->getSummary()
        ));
    }

    // ── AJAX: Remove ─────────────────────────────────────────────────────────

    public function remove(string $itemKey): JsonResponse
    {
        $this->cart->remove($itemKey);

        return response()->json(array_merge(
            ['success' => true, 'empty' => $this->cart->isEmpty()],
            $this->cart->getSummary()
        ));
    }
}
