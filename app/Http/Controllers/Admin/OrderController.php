<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminOrderService;
use Illuminate\Http\Request;

/**
 * Admin\OrderController
 * ─────────────────────────────────────────────────────────────────────────────
 * Thin HTTP layer over AdminOrderService (listing, detail, status updates
 * with stock restore on cancellation).
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly AdminOrderService $orders,
    ) {}

    public function index(Request $request)
    {
        $data = $this->orders->getIndexData($request->only(['status']));

        return view('admin.orders.index', $data);
    }

    public function show(Order $order)
    {
        $this->orders->loadOrderDetails($order);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|string']);

        try {
            $this->orders->updateStatus($order, $request->status);
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث حالة الطلب. يرجى المحاولة مرة أخرى.');
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}
