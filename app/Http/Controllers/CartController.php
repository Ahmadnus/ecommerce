<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    // ─── Views ────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $summary = $this->cart->getSummary();
        return view('cart.index', compact('summary'));
    }

    // ─── AJAX ─────────────────────────────────────────────────────────────────

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'nullable|integer|min:1|max:100',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $result = $this->cart->add(
            productId: $request->integer('product_id'),
            quantity:  $request->integer('quantity', 1),
            variantId: $request->integer('variant_id') ?: null,
        );

        return response()->json([
            'success'    => true,
            'message'    => 'تمت الإضافة إلى السلة',
            'item_count' => $result['item_count'],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $this->cart->update($request->string('item_key'), $request->integer('quantity'));
        $summary = $this->cart->getSummary();

        return response()->json([
            'success'      => true,
            'empty'        => $this->cart->isEmpty(),
            'subtotal'     => $summary['subtotal'],
            'delivery_fee' => $summary['delivery_fee'],  // ← was 'tax'
            'total'        => $summary['total'],
        ]);
    }

    public function remove(string $itemKey): JsonResponse
    {
        $this->cart->remove($itemKey);
        $summary = $this->cart->getSummary();

        return response()->json([
            'success'      => true,
            'empty'        => $this->cart->isEmpty(),
            'subtotal'     => $summary['subtotal'],
            'delivery_fee' => $summary['delivery_fee'],  // ← was 'tax'
            'total'        => $summary['total'],
        ]);
    }
}