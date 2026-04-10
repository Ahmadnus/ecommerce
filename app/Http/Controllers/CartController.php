<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $summary = $this->cartService->getSummary();
        return view('cart.index', compact('summary'));
    }

    /**
     * POST /cart/add
     * تعديل: إضافة variant_id للتحقق
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id', // مهم جداً
            'quantity'   => 'integer|min:1|max:99',
        ]);

        try {
            // نمرر الـ variant_id للخدمة
            $this->cartService->addItem(
                (int) $request->input('product_id'),
                (int) $request->input('quantity', 1),
                $request->input('variant_id') ? (int) $request->input('variant_id') : null
            );

            return response()->json([
                'success'    => true,
                'message'    => 'تمت إضافة المنتج للسلة!',
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
     * PATCH /cart/update
     * ملاحظة: الـ ID هنا يجب أن يكون الـ Cart Key (الذي قد يحتوي على variant_id)
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'item_key' => 'required|string', // نستخدم الـ key المميز في السلة
            'quantity'  => 'required|integer|min:0|max:99',
        ]);

        try {
            $this->cartService->updateItem(
                $request->input('item_key'),
                (int) $request->input('quantity')
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
     * DELETE /cart/remove/{itemKey}
     */
    public function remove(string $itemKey): JsonResponse
    {
        $this->cartService->removeItem($itemKey);
        $summary = $this->cartService->getSummary();

        return response()->json([
            'success'    => true,
            'message'    => 'تم حذف المنتج.',
            'item_count' => $summary['item_count'],
            'subtotal'   => number_format($summary['subtotal'], 2),
            'total'      => number_format($summary['total'], 2),
            'empty'      => empty($summary['items']),
        ]);
    }

    public function count(): JsonResponse
    {
        return response()->json(['count' => $this->cartService->getItemCount()]);
    }
}