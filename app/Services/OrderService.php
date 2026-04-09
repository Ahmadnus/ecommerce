<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * OrderService
 *
 * Handles the order creation process, including:
 * - Generating order numbers
 * - Creating order + line items in a transaction
 * - Reducing product stock
 * - Clearing the cart after successful order
 */
class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CartService $cartService,
        private ProductService $productService,
    ) {}

    /**
     * Create a new order from the current cart.
     *
     * @param  array $checkoutData  Validated data from CheckoutRequest
     * @param  int|null $userId     Auth user ID (null for guest checkout)
     * @return Order                The newly created order
     * @throws \Exception           If cart is empty or stock is insufficient
     */
    public function createOrder(array $checkoutData, ?int $userId = null): Order
    {
        $cartSummary = $this->cartService->getSummary();

        if (empty($cartSummary['items'])) {
            throw new \Exception('Cannot create order from an empty cart.');
        }

        // Validate stock availability before creating order
        foreach ($cartSummary['items'] as $productId => $item) {
            if (!$this->productService->checkStock($productId, $item['quantity'])) {
                throw new \Exception("Insufficient stock for: {$item['name']}");
            }
        }

        // Wrap in a DB transaction — if anything fails, everything rolls back
        return DB::transaction(function () use ($checkoutData, $cartSummary, $userId) {

            // 1. Create the order record
            $order = $this->orderRepository->create([
                'user_id'         => $userId,
                'order_number'    => $this->generateOrderNumber(),
                'status'          => 'pending',
                'subtotal'        => $cartSummary['subtotal'],
                'tax_amount'      => $cartSummary['tax'],
                'shipping_amount' => $cartSummary['shipping'],
                'discount_amount' => 0,
                'total_amount'    => $cartSummary['total'],
                'shipping_name'   => $checkoutData['name'],
                'shipping_email'  => $checkoutData['email'],
                'shipping_phone'  => $checkoutData['phone'] ?? null,
                'shipping_address'=> $checkoutData['address'],
                'shipping_city'   => $checkoutData['city'],
                'shipping_state'  => $checkoutData['state'] ?? null,
                'shipping_zip'    => $checkoutData['zip'],
                'shipping_country'=> $checkoutData['country'] ?? 'US',
                'notes'           => $checkoutData['notes'] ?? null,
                'payment_method'  => $checkoutData['payment_method'] ?? 'card',
                'payment_status'  => 'unpaid',
            ]);

            // 2. Create order line items
            foreach ($cartSummary['items'] as $productId => $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['price'],
                    'total_price'  => $item['subtotal'],
                ]);

                // 3. Reduce stock for each product
                $this->productService->decreaseStock($productId, $item['quantity']);
            }

            // 4. Clear the cart
            $this->cartService->clearCart();

            return $order;
        });
    }

    /**
     * Generate a unique, human-readable order number.
     * Format: ORD-2024-XXXXXXXX
     */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Y') . '-' . strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Get order by its number (for confirmation page).
     */
    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }
}
