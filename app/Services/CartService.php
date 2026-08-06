<?php

namespace App\Services;

use App\Models\OrderCustomization;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CustomizationPricingService;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 * ─────────────────────────────────────────────────────────────────────────────
 * All prices stored in session and returned in getSummary() are in JOD.
 * Views convert to display currency via $activeCurrency (ResolveCurrency middleware).
 *
 * Delivery fee logic (flat fee + free-delivery threshold) is UNCHANGED from
 * the previous version — see getDeliveryFee() / getFreeThreshold().
 *
 * ── What's new in this version: customized garment support ─────────────────
 * A cart item can now represent a customized garment instead of a catalog
 * Product. Customized items are identified by `customization_id` being set
 * on the cart row. For these items:
 *   - product_id is 0 (demo-mode convention from CustomizationController)
 *   - price comes from CustomizationPricingService, NOT from Product/Variant
 *   - the line item's name/image come from the OrderCustomization + its
 *     resolved garment config, not from a Product row
 *   - quantity is always 1 (one saved design = one physical garment)
 *   - the cart key is unique per customization, so two different designs
 *     never collapse into the same row
 */
class CartService
{
    private const SESSION_KEY = 'shopping_cart';

    /**
     * Fallback delivery fee in JOD if the setting is missing from DB.
     * Override by inserting 'delivery_fee' into your settings table.
     */
    private const DEFAULT_DELIVERY_FEE = 3.00;

    /**
     * Fallback free-delivery threshold in JOD.
     * Override by inserting 'free_delivery_threshold' into your settings table.
     */
    private const DEFAULT_FREE_THRESHOLD = 50.00;

    public function __construct(
        private readonly CustomizationPricingService $pricing = new CustomizationPricingService(),
    ) {}

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Read delivery fee from the settings table.
     * Cached in a static var so only one DB query fires per request.
     */
    private function getDeliveryFee(): float
    {
        static $fee = null;
        if ($fee === null) {
            $raw = \App\Models\Setting::get('delivery_fee', self::DEFAULT_DELIVERY_FEE);
            $fee = (float) $raw;
        }
        return $fee;
    }

    private function getFreeThreshold(): float
    {
        static $threshold = null;
        if ($threshold === null) {
            $raw       = \App\Models\Setting::get('free_delivery_threshold', self::DEFAULT_FREE_THRESHOLD);
            $threshold = (float) $raw;
        }
        return $threshold;
    }

    // ─── Read ─────────────────────────────────────────────────────────────────

    public function all(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function getItemCount(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    public function isEmpty(): bool
    {
        return empty($this->all());
    }

    /**
     * Build the full cart summary.
     *
     * Returned keys (ALL values in JOD):
     *   items[]         — cart line items
     *   subtotal        — sum of line totals
     *   delivery_fee    — flat fee from settings (0 if order qualifies for free delivery)
     *   total           — subtotal + delivery_fee
     *   free_threshold  — the threshold above which delivery is free (for UI display)
     */
    public function getSummary(): array
    {
        $cartItems     = $this->all();
        $items         = [];
        $subtotal      = 0.0;
        $deliveryFee   = $this->getDeliveryFee();
        $freeThreshold = $this->getFreeThreshold();

        foreach ($cartItems as $key => $cartItem) {

            // ── Customized garment line item ───────────────────────────────────
            if (! empty($cartItem['customization_id'])) {
                $line = $this->buildCustomizedLineItem($key, $cartItem);
                if ($line === null) {
                    continue; // the saved design was deleted — drop it silently
                }

                $items[$key] = $line;
                $subtotal   += $line['subtotal'];
                continue;
            }

            // ── Normal catalog product line item (unchanged logic) ─────────────
            $product = Product::find($cartItem['product_id']);
            if (!$product) {
                continue;
            }

            $variant = isset($cartItem['variant_id'])
                ? ProductVariant::find($cartItem['variant_id'])
                : null;

            // Prices in JOD (base currency, rate = 1.000000)
            $unitPrice = $variant
                ? (float) ($variant->price_override ?? $product->discount_price ?? $product->base_price)
                : (float) ($product->discount_price ?? $product->base_price);

            $qty       = (int) $cartItem['quantity'];
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;

            // Spatie media with image_url fallback
            $image = method_exists($product, 'getFirstMediaUrl')
                ? ($product->getFirstMediaUrl('products') ?: $product->image_url)
                : $product->image_url;

            $variantName = null;
            if ($variant) {
                $variantName = $variant->load('attributeValues')
                    ->attributeValues->pluck('value')->implode(' / ');
            }

            $items[$key] = [
                'product_id'        => $product->id,
                'variant_id'        => $variant?->id,
                'customization_id'  => null,
                'is_customized'     => false,
                'name'              => $product->name,
                'slug'              => $product->slug,
                'sku'               => $variant?->sku ?? $product->sku ?? null,
                'variant_name'      => $variantName,
                'image'             => $image,
                'price'             => $unitPrice,    // JOD
                'quantity'          => $qty,
                'subtotal'          => $lineTotal,    // JOD
                'max_stock'         => $variant?->stock_quantity ?? ($product->total_stock ?? 0),
            ];
        }

        // Free delivery above threshold
        $appliedDelivery = $subtotal >= $freeThreshold ? 0.0 : $deliveryFee;
        $total           = round($subtotal + $appliedDelivery, 2);

        return [
            'items'          => $items,
            'subtotal'       => $subtotal,
            'delivery_fee'   => $appliedDelivery,
            'total'          => $total,
            'free_threshold' => $freeThreshold,
        ];
    }

    /**
     * Build the line-item array for a customized garment.
     * Returns null if the underlying OrderCustomization no longer exists
     * (e.g. it was deleted) — caller should skip/drop the cart row.
     */
    private function buildCustomizedLineItem(string $key, array $cartItem): ?array
    {
        $customization = OrderCustomization::with('uploads')
            ->find($cartItem['customization_id']);

        if (! $customization) {
            return null;
        }

        // Price is whatever was computed and stored on the cart row at
        // add-to-cart time — NOT recalculated here. This guarantees the
        // price the customer saw when adding to cart never silently
        // changes later if an admin edits the pricing settings while the
        // item is still sitting in the customer's cart.
        $unitPrice = (float) ($cartItem['customization_price'] ?? $this->pricing->totalFor($customization));
        $qty       = (int) ($cartItem['quantity'] ?? 1);
        $lineTotal = round($unitPrice * $qty, 2);

        $garmentLabels = [
            'tshirt'          => 'تيشيرت مخصص',
            'hoodie'          => 'هودي مخصص',
            'varsity_jacket'  => 'جاكيت رياضي مخصص',
            'graduation_robe' => 'ثوب تخرج مخصص',
            'stole'           => 'وشاح تخرج مخصص',
        ];
        $garmentType = $customization->garment_type ?? 'tshirt';
        $name        = $garmentLabels[$garmentType] ?? 'منتج مخصص';

        // Real product image if one exists (rare in demo mode), otherwise
        // the first uploaded zone image, otherwise null (view shows a
        // placeholder icon — same as a product with no image today).
        $image = null;
        if ($customization->product_id && $customization->product) {
            $image = method_exists($customization->product, 'getFirstMediaUrl')
                ? ($customization->product->getFirstMediaUrl('products') ?: null)
                : null;
        }
        if (! $image && $customization->uploads->isNotEmpty()) {
            $image = $customization->uploads->first()->url();
        }

        return [
            'product_id'        => $customization->product_id ?: 0,
            'variant_id'        => null,
            'customization_id'  => $customization->id,
            'is_customized'     => true,
            'name'              => $name,
            'slug'              => null,
            'sku'               => null,
            'variant_name'      => $customization->size ? "مقاس: {$customization->size}" : null,
            'image'             => $image,
            'price'             => $unitPrice,   // JOD
            'quantity'          => $qty,
            'subtotal'          => $lineTotal,   // JOD
            'max_stock'         => null,         // made-to-order, no stock concept
        ];
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    /**
     * Add a product (or a customized garment) to the cart.
     *
     * Normal product call (unchanged):
     *     $cart->add($productId, $quantity, $variantId);
     *
     * Customized garment call (new):
     *     $cart->add(
     *         productId: 0,
     *         quantity: 1,
     *         variantId: null,
     *         customizationId: $customization->id,
     *         customPriceJod: $priceBreakdown['total'],
     *     );
     */
    public function add(
        int $productId,
        int $quantity = 1,
        ?int $variantId = null,
        ?int $customizationId = null,
        ?float $customPriceJod = null,
    ): array {
        $cart = $this->all();

        // ── Customized garment: own unique key, quantity always 1,
        //    never merges with another design or a catalog product ──────────
        if ($customizationId !== null) {
            $key = "custom_{$customizationId}";

            $cart[$key] = [
                'product_id'           => $productId,
                'variant_id'           => null,
                'customization_id'     => $customizationId,
                'customization_price'  => $customPriceJod,
                'quantity'             => 1,
            ];

            Session::put(self::SESSION_KEY, $cart);
            return ['item_count' => $this->getItemCount()];
        }

        // ── Normal catalog product (unchanged logic) ────────────────────────
        $key = $variantId ? "p{$productId}_v{$variantId}" : "p{$productId}";

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity'   => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
        return ['item_count' => $this->getItemCount()];
    }

    public function update(string $itemKey, int $quantity): bool
    {
        $cart = $this->all();
        if (!isset($cart[$itemKey])) {
            return false;
        }

        // Customized garments are made-to-order — quantity is always exactly 1.
        // Block any attempt to change it instead of silently ignoring it.
        if (! empty($cart[$itemKey]['customization_id'])) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->remove($itemKey);
        }
        $cart[$itemKey]['quantity'] = $quantity;
        Session::put(self::SESSION_KEY, $cart);
        return true;
    }

    public function remove(string $itemKey): bool
    {
        $cart = $this->all();
        if (!isset($cart[$itemKey])) {
            return false;
        }
        unset($cart[$itemKey]);
        Session::put(self::SESSION_KEY, $cart);
        return true;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Validate an add-to-cart request against the product's variants/stock.
     *
     * Strict rules (all attribute logic is fully dynamic):
     *   - Product must be active (404 via findOrFail otherwise).
     *   - A supplied variant must belong to this product AND be active.
     *   - The variant must cover ALL distinct attribute types the product
     *     uses (e.g. colour + size) — resolved dynamically.
     *   - The variant (or, for no-variant products, the product) must have
     *     sufficient stock.
     *
     * Returns null when valid, or the user-facing error message otherwise.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function validateAdd(int $productId, int $quantity, ?int $variantId): ?string
    {
        // Load product + its active variants (with their attribute relationships)
        $product = Product::active()
            ->with([
                'variants' => fn($q) => $q
                    ->where('is_active', true)
                    ->with(['attributeValues.attribute']),
            ])
            ->findOrFail($productId);

        $activeVariants = $product->variants;

        if ($variantId) {
            $variant = $activeVariants->firstWhere('id', $variantId);

            // Variant belongs to this product and is active
            if (! $variant) {
                return 'المتغير المحدد غير صالح أو غير متاح';
            }

            // Variant must cover all required attribute types (dynamic)
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

            if ($requiredTypeIds->diff($variantTypeIds)->isNotEmpty()) {
                return 'يرجى اختيار جميع الخصائص المطلوبة';
            }

            // Stock
            if ($variant->stock_quantity < $quantity) {
                $avail = $variant->stock_quantity;
                return $avail === 0
                    ? 'نفد هذا الخيار من المخزون'
                    : "الكمية غير متوفرة — المتاح: {$avail} قطعة";
            }
        } else {
            // No-variant product stock
            if ($product->total_stock < $quantity) {
                $avail = $product->total_stock;
                return $avail === 0
                    ? 'المنتج غير متوفر حالياً'
                    : "الكمية المطلوبة غير متوفرة — المتاح: {$avail} قطعة";
            }
        }

        return null;
    }
}