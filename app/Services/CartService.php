<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 * ─────────────────────────────────────────────────────────────────────────────
 * All prices stored in session and returned in getSummary() are in JOD.
 * Views convert to display currency via $activeCurrency (ResolveCurrency middleware).
 *
 * CHANGE FROM PREVIOUS VERSION:
 *   - TAX (10% percentage) is REMOVED.
 *   - DELIVERY_FEE (flat amount, read from settings table) replaces it.
 *   - Formula: total = subtotal + delivery_fee
 *              (delivery_fee = 0 when subtotal >= FREE_DELIVERY_THRESHOLD)
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
     *
     * NOTE: 'tax' key is intentionally REMOVED. Any existing code reading
     *       $summary['tax'] must be updated to $summary['delivery_fee'].
     */
    public function getSummary(): array
    {
        $cartItems    = $this->all();
        $items        = [];
        $subtotal     = 0.0;
        $deliveryFee  = $this->getDeliveryFee();
        $freeThreshold = $this->getFreeThreshold();

        foreach ($cartItems as $key => $cartItem) {
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
                'product_id'   => $product->id,
                'variant_id'   => $variant?->id,
                'name'         => $product->name,
                'slug'         => $product->slug,
                'sku'          => $variant?->sku ?? $product->sku ?? null,
                'variant_name' => $variantName,
                'image'        => $image,
                'price'        => $unitPrice,    // JOD
                'quantity'     => $qty,
                'subtotal'     => $lineTotal,    // JOD
                'max_stock'    => $variant?->stock_quantity ?? ($product->total_stock ?? 0),
            ];
        }

        // Free delivery above threshold
        $appliedDelivery = $subtotal >= $freeThreshold ? 0.0 : $deliveryFee;
        $total           = round($subtotal + $appliedDelivery, 2);

        return [
            'items'          => $items,
            'subtotal'       => $subtotal,
            'delivery_fee'   => $appliedDelivery,    // ← replaces 'tax'
            'total'          => $total,
            'free_threshold' => $freeThreshold,      // for "free delivery above X" banner
        ];
    }

    // ─── Write ────────────────────────────────────────────────────────────────

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $cart = $this->all();
        $key  = $variantId ? "p{$productId}_v{$variantId}" : "p{$productId}";

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
}