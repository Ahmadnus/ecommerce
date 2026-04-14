<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 * ─────────────────────────────────────────────────────────────────────────────
 * All prices stored in session and returned in getSummary() are in JOD.
 * Views convert to display currency via CurrencyService or $activeCurrency.
 *
 * This keeps the cart data stable regardless of currency switches.
 */
class CartService
{
    private const SESSION_KEY             = 'shopping_cart';
    private const TAX_RATE                = 0.10;   // 10%
    private const FREE_SHIPPING_THRESHOLD = 50.00;  // in JOD
    private const SHIPPING_COST          = 5.00;   // in JOD

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
     * ALL monetary values are in JOD (the base currency).
     * Blade templates should convert with $activeCurrency->exchange_rate for display.
     */
    public function getSummary(): array
    {
        $cartItems = $this->all();
        $items     = [];
        $subtotal  = 0.0;

        foreach ($cartItems as $key => $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if (!$product) {
                continue;
            }

            $variant = isset($cartItem['variant_id'])
                ? ProductVariant::find($cartItem['variant_id'])
                : null;

            // Prices in JOD — stored raw in DB, JOD is base (rate = 1)
            $unitPrice = $variant
                ? (float) ($variant->price_override ?? $product->discount_price ?? $product->base_price)
                : (float) ($product->discount_price ?? $product->base_price);

            $qty       = (int) $cartItem['quantity'];
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;

            // Spatie media fallback
            $image = method_exists($product, 'getFirstMediaUrl')
                ? ($product->getFirstMediaUrl('products') ?: $product->image_url)
                : $product->image_url;

            $variantName = null;
            if ($variant) {
                $variantName = $variant->load('attributeValues')
                    ->attributeValues->pluck('value')->implode(' / ');
            }

            $items[$key] = [
                'product_id'  => $product->id,
                'variant_id'  => $variant?->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'sku'         => $variant?->sku ?? $product->sku ?? null,
                'variant_name'=> $variantName,
                'image'       => $image,
                'price'       => $unitPrice,        // JOD
                'quantity'    => $qty,
                'subtotal'    => $lineTotal,         // JOD
                'max_stock'   => $variant?->stock_quantity ?? ($product->total_stock ?? 0),
            ];
        }

        $tax      = round($subtotal * self::TAX_RATE, 2);
        $shipping = $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_COST;
        $total    = round($subtotal + $tax + $shipping, 2);

        return compact('items', 'subtotal', 'tax', 'shipping', 'total');
        // All values above are in JOD
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