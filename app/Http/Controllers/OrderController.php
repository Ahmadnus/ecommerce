<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Order success / confirmation page.
     * Accessible by the order owner only.
     */
    public function success(string $orderNumber): View|RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)
                      ->where('user_id', Auth::id())
                      ->with('items')
                      ->firstOrFail();

        return view('orders.success', compact('order'));
    }

    /**
     * User's order history.
     */
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
                       ->with('items')
                       ->latest()
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}