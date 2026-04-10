<?php

namespace App\Services;
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant; // أضفنا المودل الجديد
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

    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, ['items' => []]);
    }

    public function getItems(): array
    {
        return $this->getCart()['items'];
    }

    /**
     * تعديل: إضافة الـ VariantId لأن العميل يشتري نسخة محددة (مثل مقاس 42)
     */
    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $product = Product::find($productId);

        // التأكد من وجود المنتج وأنه نشط (Status من جدول المنتجات)
        if (!$product || $product->status !== 'active') {
            throw new \Exception('المنتج غير متوفر حالياً.');
        }

        // إذا كان المنتج له Variants، يجب اختيار واحد
        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
        } else {
            // إذا لم يرسل variantId، نأخذ أول نسخة متوفرة كمثال (أو النسخة الافتراضية)
            $variant = $product->variants()->first();
        }

        if (!$variant) {
            throw new \Exception('برجاء اختيار النوع (المقاس/اللون) المطلوب.');
        }

        // التحقق من المخزون من جدول الـ Variants وليس الـ Products
        if ($variant->stock_quantity < $quantity) {
            throw new \Exception("عذراً، المتوفر فقط {$variant->stock_quantity} قطع.");
        }

        $cart = $this->getCart();
        
        // حساب السعر: نستخدم سعر الـ Variant إذا وُجد (Price Override) وإلا سعر المنتج الأساسي
        $unitPrice = $variant->price_override ?? $product->discount_price ?? $product->base_price;
        
        // مفتاح السلة يكون مزيج من المنتج والنسخة لتمييزهم
        $cartKey = $productId . '_' . ($variantId ?? 'default');

        if (isset($cart['items'][$cartKey])) {
            $newQty = $cart['items'][$cartKey]['quantity'] + $quantity;
            if ($variant->stock_quantity < $newQty) {
                throw new \Exception("لا يمكن إضافة المزيد، الكمية المتوفرة قد نفدت.");
            }
            $cart['items'][$cartKey]['quantity'] = $newQty;
            $cart['items'][$cartKey]['subtotal'] = round($unitPrice * $newQty, 2);
        } else {
    $cart['items'][$cartKey] = [
        'id'           => $product->id,
        'variant_id'   => $variant->id,
        'name'         => $product->name,
        'slug'         => $product->slug, // <--- أضف هذا السطر هنا
        'variant_name' => $variant->sku,
        'price'        => $unitPrice,
        'image'        => $variant->variant_image ?? $product->image,
        'quantity'     => $quantity,
        'subtotal'     => round($unitPrice * $quantity, 2),
    ];
}
        Session::put(self::SESSION_KEY, $cart);
        return $cart;
    }

    // ... باقي الدوال (removeItem, clearCart) تبقى كما هي مع تغيير استخدام $productId ليكون $cartKey


    /**
     * Update quantity of a cart item. Pass 0 to remove.
     */
  public function updateItem(string $itemKey, int $quantity): array
{
    if ($quantity <= 0) {
        return $this->removeItem($itemKey);
    }

    $cart = $this->getCart();

    if (isset($cart['items'][$itemKey])) {
        // --- تعديل منطق التحقق من المخزون ليدعم الـ Variants ---
        $variantId = $cart['items'][$itemKey]['variant_id'] ?? null;
        
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if ($variant && $variant->stock_quantity < $quantity) {
                throw new \Exception("عذراً، المتوفر من هذا النوع فقط {$variant->stock_quantity} قطع.");
            }
        } else {
            // للمنتجات البسيطة (إذا كان العمود لا يزال موجوداً)
            $product = \App\Models\Product::find($cart['items'][$itemKey]['id']);
            if ($product && isset($product->stock_quantity) && $product->stock_quantity < $quantity) {
                throw new \Exception("عذراً، المتوفر فقط {$product->stock_quantity} قطع.");
            }
        }
        // ---------------------------------------------------

        // تحديث الكمية والمجموع الفرعي في السلة
        $cart['items'][$itemKey]['quantity'] = $quantity;
        $cart['items'][$itemKey]['subtotal'] = round($cart['items'][$itemKey]['price'] * $quantity, 2);
        
        // استخدام نفس طريقة الحفظ القديمة الخاصة بك
        \Illuminate\Support\Facades\Session::put(self::SESSION_KEY, $cart);
    }

    return $cart;
}

/**
 * حذف منتج أو نسخة من السلة نهائياً
 */
public function removeItem(string $itemKey): array
{
    $cart = $this->getCart();
    
    // حذف العنصر باستخدام المفتاح (itemKey)
    unset($cart['items'][$itemKey]);
    
    // حفظ التعديل في الجلسة
    \Illuminate\Support\Facades\Session::put(self::SESSION_KEY, $cart);
    
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
