<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * AdminOrderService — business logic for the admin orders screens
 * (listing, detail eager-loads, status updates with stock restore).
 * Distinct from OrderService, which backs order creation.
 * Never returns views/redirects.
 */
class AdminOrderService
{
    /**
     * Filtered order list + admin display currency (always base/JOD).
     * $filters keys: status.
     */
    public function getIndexData(array $filters): array
    {
        $query = Order::with([
            'items.product',
            'items.productVariant.attributeValues.attribute',
            'zone',
        ])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Always resolve via the base currency for admin — admins see storage currency (JOD)
        $activeCurrency = Currency::where('is_base', true)->first()
            ?? Currency::where('is_active', true)->orderBy('sort_order')->first();

        return compact('orders', 'activeCurrency');
    }

    public function loadOrderDetails(Order $order): Order
    {
        return $order->load([
            'items.product',
            'items.productVariant.attributeValues.attribute',
            'zone',
        ]);
    }

    /**
     * Update the order status; cancelling a previously non-cancelled order
     * restores variant stock. Both happen in one transaction.
     *
     * @throws \Throwable on failure (transaction rolled back)
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $oldStatus = $order->status;

        try {
            DB::transaction(function () use ($order, $oldStatus, $newStatus) {
                // Restore stock when cancelling a previously non-cancelled order
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $item->variant()->increment('stock_quantity', $item->quantity);
                        }
                    }
                }

                $order->update(['status' => $newStatus]);
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }

        return $order;
    }
}
