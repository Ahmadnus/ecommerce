<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function index() {
    $orders = Order::with([
        'items.product',
        'items.variant.attributeValues.attribute'
    ])->latest()->paginate(10);

    return view('admin.orders.index', compact('orders'));
}

    public function show(Order $order) {
        $order->load('items.product', 'items.variant');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order) {
        $request->validate(['status' => 'required|string']);
        
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // منطق إرجاع المخزون إذا تم إلغاء الطلب وكان سابقاً مقبولاً
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