<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

/**
 * CartService
 *
 * Manages the shopping cart stored in the session.
 * Cart structure in session:
 * [
 *   'items' => [
 *     product_id => [
 *       'id'       => int,
 *       'name'     => string,
 *       'price'    => float,
 *       'image'    => string,
 *       'quantity' => int,
 *       'subtotal' => float,
 *     ],
 *     ...
 *   ]
 * ]
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /**
     * Get the full cart array from session.
     */
    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, ['items' => []]);
    }

    /**
     * Get all cart items as a flat array.
     */
    public function getItems(): array
    {
        return $this->getCart()['items'];
    }

    /**
     * Add a product to the cart or increase its quantity.
     *
     * @throws \Exception if product not found or out of stock
     */
    public function addItem(int $productId, int $quantity = 1): array
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            throw new \Exception('Product not found.');
        }

        if ($product->stock_quantity < $quantity) {
            throw new \Exception("Only {$product->stock_quantity} units available.");
        }

        $cart = $this->getCart();
        $price = $product->effective_price; // Uses sale_price if available

        if (isset($cart['items'][$productId])) {
            // Check stock including existing cart quantity
            $newQty = $cart['items'][$productId]['quantity'] + $quantity;
            if ($product->stock_quantity < $newQty) {
                throw new \Exception("Only {$product->stock_quantity} units available in total.");
            }
            $cart['items'][$productId]['quantity'] = $newQty;
            $cart['items'][$productId]['subtotal']  = round($price * $newQty, 2);
        } else {
            $cart['items'][$productId] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'slug'     => $product->slug,
                'price'    => $price,
                'image'    => $product->image,
                'quantity' => $quantity,
                'subtotal' => round($price * $quantity, 2),
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
        return $cart;
    }

    /**
     * Update quantity of a cart item. Pass 0 to remove.
     */
    public function updateItem(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        $product = Product::find($productId);
        if ($product && $product->stock_quantity < $quantity) {
            throw new \Exception("Only {$product->stock_quantity} units available.");
        }

        $cart = $this->getCart();

        if (isset($cart['items'][$productId])) {
            $cart['items'][$productId]['quantity'] = $quantity;
            $cart['items'][$productId]['subtotal']  = round($cart['items'][$productId]['price'] * $quantity, 2);
            Session::put(self::SESSION_KEY, $cart);
        }

        return $cart;
    }

    /**
     * Remove a product from the cart entirely.
     */
    public function removeItem(int $productId): array
    {
        $cart = $this->getCart();
        unset($cart['items'][$productId]);
        Session::put(self::SESSION_KEY, $cart);
        return $cart;
    }

    /**
     * Clear the entire cart.
     */
    public function clearCart(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Get total number of items (sum of all quantities).
     */
    public function getItemCount(): int
    {
        return array_sum(array_column($this->getItems(), 'quantity'));
    }

    /**
     * Get cart subtotal (before tax/shipping).
     */
    public function getSubtotal(): float
    {
        return round(array_sum(array_column($this->getItems(), 'subtotal')), 2);
    }

    /**
     * Calculate tax amount (default 10%).
     */
    public function getTax(float $rate = 0.10): float
    {
        return round($this->getSubtotal() * $rate, 2);
    }

    /**
     * Get shipping cost (free above $50).
     */
    public function getShipping(): float
    {
        return $this->getSubtotal() >= 50 ? 0.00 : 5.99;
    }

    /**
     * Get final total including tax and shipping.
     */
    public function getTotal(): float
    {
        return round($this->getSubtotal() + $this->getTax() + $this->getShipping(), 2);
    }

    /**
     * Get a summary object for checkout/display.
     */
    public function getSummary(): array
    {
        return [
            'items'      => $this->getItems(),
            'item_count' => $this->getItemCount(),
            'subtotal'   => $this->getSubtotal(),
            'tax'        => $this->getTax(),
            'shipping'   => $this->getShipping(),
            'total'      => $this->getTotal(),
        ];
    }
}
