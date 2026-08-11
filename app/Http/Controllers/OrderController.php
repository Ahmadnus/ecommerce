<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Order success / confirmation page.
     * Accessible by the order owner only.
     */
 public function success(string $orderNumber): View|RedirectResponse
{
    // نبحث عن الطلب برقم الطلب فقط (للسماح للزوار برؤية صفحة النجاح فوراً)
    // بدل firstOrFail حتى لا تظهر صفحة 404 بعد إتمام الحجز
    $order = Order::where('order_number', $orderNumber)
                  ->with('items')
                  ->first();

    if (! $order) {
        return redirect()->route('cart.index')
            ->with('error', __('app.order_not_found'));
    }

    return view('orders.success', compact('order'));
}

    /**
     * Single order details for the storefront.
     */
    public function show(Order $order): View|RedirectResponse
    {
        if ($order->user_id && $order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')
                ->with('error', __('app.order_not_found'));
        }

        $order->load('items');

        return view('orders.success', compact('order'));
    }

    /**
     * City/zone selection after an order is placed.
     */
    public function selectCity(string $orderNumber): RedirectResponse
    {
        return redirect()->route('checkout.select-zone');
    }

    public function updateCity(Request $request, string $orderNumber): RedirectResponse
    {
        return redirect()->route('checkout.confirm-zone');
    }
    /**
     * User's order history.
     */
public function index(Request $request): View
{
    $user = $request->user();
    
    // استخدمنا العلاقة مباشرة كما فعلت في البروفايل
    $orders = $user->orders()->latest()->paginate(10);

    return view('orders.index', [
        'orders' => $orders
    ]);
}
}