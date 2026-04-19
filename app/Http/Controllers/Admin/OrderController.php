<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Admin\OrderController
 * ─────────────────────────────────────────────────────────────────────────────
 * Changes:
 *   • index() and show() now eager-load items.productVariant.attributeValues.attribute
 *     so every variant's attributes (Color, Size, Storage…) are available in views.
 *   • show() also loads 'zone' for the delivery area display.
 */
class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'items.product',
            'items.productVariant.attributeValues.attribute',
            'zone',
        ])->latest()->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'items.product',
            'items.productVariant.attributeValues.attribute',
            'zone',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|string']);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Restore stock when cancelling a previously non-cancelled order
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $item->variant()->increment('stock_quantity', $item->quantity);
                }
            }
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}