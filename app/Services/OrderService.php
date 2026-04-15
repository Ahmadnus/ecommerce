<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartService $cartService,
        private readonly ProductService $productService,
    ) {}

    public function createOrder(array $checkoutData, ?int $userId = null): Order
    {
        $cartSummary = $this->cartService->getSummary();

        if (empty($cartSummary['items'])) {
            throw new \Exception('لا يمكن إتمام الطلب والسلة فارغة.');
        }

        // 1. التأكد من المخزون قبل بدء المعاملة (Transaction)
        foreach ($cartSummary['items'] as $itemKey => $item) {
            // نمرر الـ id الحقيقي والـ variant_id
            if (!$this->productService->checkStock((int)$item['id'], (int)$item['quantity'], $item['variant_id'] ?? null)) {
                throw new \Exception("المخزون غير كافٍ للمنتج: {$item['name']}");
            }
        }

        return DB::transaction(function () use ($checkoutData, $cartSummary, $userId) {

            // 2. إنشاء سجل الطلب الأساسي
         $order = $this->orderRepository->create([
    'user_id'         => $userId,
    'order_number'    => $this->generateOrderNumber(),
    'status'          => 'pending',
    'subtotal'        => $cartSummary['subtotal'],
    
    // التعديل هنا: استبدال tax_amount بـ delivery_fee
    // واستخدام القيمة المناسبة من مصفوفة الملخص (غالباً ستكون 'delivery_fee' أو 'tax')
    'delivery_fee'    => $cartSummary['delivery_fee'] ?? $cartSummary['tax'] ?? 0,
    
    'shipping_amount' => $cartSummary['shipping'] ?? 0,
    'total_amount'    => $cartSummary['total'],
    'shipping_name'   => $checkoutData['name'],
    'shipping_email'  => $checkoutData['email'],
    'shipping_phone'  => $checkoutData['phone'] ?? null,
    'shipping_address'=> $checkoutData['address'],
    'shipping_city'   => $checkoutData['city'],
    'shipping_zip'    => $checkoutData['zip'],
    'shipping_country'=> $checkoutData['country'] ?? 'EG',
    'payment_method'  => $checkoutData['payment_method'] ?? 'cod',
    'payment_status'  => 'unpaid',
]);
            // 3. إنشاء تفاصيل الطلب وخصم المخزون
            foreach ($cartSummary['items'] as $itemKey => $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'variant_id'   => $item['variant_id'] ?? null, // تخزين الـ variant_id مهم جداً
                    'product_name' => $item['name'] . ($item['variant_name'] ? " ({$item['variant_name']})" : ""),
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['price'],
                    'total_price'  => $item['subtotal'],
                ]);

                // خصم المخزون من النسخة المحددة
                $this->productService->decreaseStock(
                    (int) $item['id'], 
                    (int) $item['quantity'], 
                    $item['variant_id'] ?? null
                );
            }

            // 4. تفريغ السلة
            $this->cartService->clearCart();

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }
}