<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
    ) {}

    /**
     * Order success / confirmation page.
     * Accessible by the order owner only.
     */
    public function success(string $orderNumber): View
    {
        // نبحث عن الطلب برقم الطلب فقط (للسماح للزوار برؤية صفحة النجاح فوراً)
        $order = $this->orders->getOrderForSuccessPage($orderNumber);

        return view('orders.success', compact('order'));
    }

    /**
     * User's order history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = $this->orders->getUserOrders($user);

        return view('orders.index', [
            'orders' => $orders
        ]);
    }
}
