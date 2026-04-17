<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
     * Strict validation rules (all attribute logic is fully dynamic):
     *
     * 1. Product must be active.
     * 2. If the product has active variants → variant_id is REQUIRED.
     * 3. The variant must belong to this product AND be active.
     * 4. The variant must cover ALL distinct attribute types the product uses
     *    (e.g. colour + size) — resolved dynamically, no hardcoded names.
     * 5. The variant must have sufficient stock.
     * 6. Products with no variants → check product-level stock.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'nullable|integer|min:1|max:100',
            'variant_id' => 'nullable|integer',
        ]);

        $productId = $request->integer('product_id');
        $quantity  = max(1, $request->integer('quantity', 1));
        $variantId = $request->integer('variant_id') ?: null;

        // Load product + its active variants (with their attribute relationships)
        $product = Product::active()
            ->with([
                'variants' => fn($q) => $q
                    ->where('is_active', true)
                    ->with(['attributeValues.attribute']),
            ])
            ->findOrFail($productId);

        $activeVariants = $product->variants;
        $hasVariants    = $activeVariants->isNotEmpty();

        // ── Rule 2: variants exist but none supplied ──────────────────────────
        if ($hasVariants && ! $variantId) {
            // Build a dynamic list of required attribute names for the error message
            $requiredNames = $activeVariants
                ->flatMap(fn($v) => $v->attributeValues->pluck('attribute.name'))
                ->unique()
                ->values()
                ->implode(' و ');

            return response()->json([
                'success'             => false,
                'message'             => 'يرجى اختيار ' . $requiredNames . ' أولاً',
                'required_attributes' => $activeVariants
                    ->flatMap(fn($v) => $v->attributeValues->map(fn($av) => [
                        'id'   => $av->attribute_id,
                        'name' => $av->attribute->name,
                    ]))
                    ->unique('id')
                    ->values(),
            ], 422);
        }

        // ── Rules 3-5: variant supplied ──────────────────────────────────────
        if ($variantId) {
            $variant = $activeVariants->firstWhere('id', $variantId);

            // Rule 3: variant belongs to this product and is active
            if (! $variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'المتغير المحدد غير صالح أو غير متاح',
                ], 422);
            }

            // Rule 4: variant must cover all required attribute types (dynamic)
            $requiredTypeIds = $activeVariants
                ->flatMap(fn($v) => $v->attributeValues->pluck('attribute_id'))
                ->unique()
                ->sort()
                ->values();

            $variantTypeIds = $variant->attributeValues
                ->pluck('attribute_id')
                ->unique()
                ->sort()
                ->values();

            $missing = $requiredTypeIds->diff($variantTypeIds);

            if ($missing->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار جميع الخصائص المطلوبة',
                ], 422);
            }

            // Rule 5: stock
            if ($variant->stock_quantity < $quantity) {
                $avail = $variant->stock_quantity;
                return response()->json([
                    'success' => false,
                    'message' => $avail === 0
                        ? 'نفد هذا الخيار من المخزون'
                        : "الكمية غير متوفرة — المتاح: {$avail} قطعة",
                ], 422);
            }
        } else {
            // Rule 6: no-variant product stock
            if ($product->total_stock < $quantity) {
                $avail = $product->total_stock;
                return response()->json([
                    'success' => false,
                    'message' => $avail === 0
                        ? 'المنتج غير متوفر حالياً'
                        : "الكمية المطلوبة غير متوفرة — المتاح: {$avail} قطعة",
                ], 422);
            }
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