<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'shopping_cart';
    private const TAX_RATE    = 0.10;   // 10%
    private const FREE_SHIPPING_THRESHOLD = 50.00;
    private const SHIPPING_COST           = 5.00;

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
     * Build the full cart summary array consumed by views & checkout.
     */
    public function getSummary(): array
    {
        $cartItems = $this->all();
        $items     = [];
        $subtotal  = 0.0;

        foreach ($cartItems as $key => $cartItem) {
            $product = Product::find($cartItem['product_id']);
            if (! $product) continue;

            $variant   = isset($cartItem['variant_id'])
                       ? ProductVariant::find($cartItem['variant_id'])
                       : null;

            $unitPrice = $variant
                       ? (float) ($variant->price_override ?? $product->discount_price ?? $product->base_price)
                       : (float) ($product->discount_price ?? $product->base_price);

            $qty      = (int) $cartItem['quantity'];
            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            // Image: prefer Spatie media, fallback to direct URL
            $image = method_exists($product, 'getFirstMediaUrl')
                   ? ($product->getFirstMediaUrl('products') ?: $product->image_url)
                   : $product->image_url;

            $variantName = null;
            if ($variant && $variant->relationLoaded('attributeValues')) {
                $variantName = $variant->attributeValues->pluck('value')->implode(' / ');
            } elseif ($variant) {
                $variantName = $variant->load('attributeValues')
                                       ->attributeValues->pluck('value')->implode(' / ');
            }

            $items[$key] = [
                'product_id'  => $product->id,
                'variant_id'  => $variant?->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'sku'         => $variant?->sku ?? $product->sku,
                'variant_name'=> $variantName,
                'image'       => $image,
                'price'       => $unitPrice,
                'quantity'    => $qty,
                'subtotal'    => $lineTotal,
                'max_stock'   => $variant?->stock_quantity ?? $product->total_stock,
            ];
        }

        $tax      = round($subtotal * self::TAX_RATE, 2);
        $shipping = $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0.0 : self::SHIPPING_COST;
        $total    = $subtotal + $tax + $shipping;

        return compact('items', 'subtotal', 'tax', 'shipping', 'total');
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

        if (! isset($cart[$itemKey])) return false;

        if ($quantity <= 0) {
            $this->remove($itemKey);
            return true;
        }

        $cart[$itemKey]['quantity'] = $quantity;
        Session::put(self::SESSION_KEY, $cart);

        return true;
    }

    public function remove(string $itemKey): bool
    {
        $cart = $this->all();
        if (! isset($cart[$itemKey])) return false;
        unset($cart[$itemKey]);
        Session::put(self::SESSION_KEY, $cart);
        return true;
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }
}