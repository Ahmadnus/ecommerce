<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * OrderController
 *
 * Handles the checkout flow: display form → validate → create order → confirm.
 */
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
    ) {}

    /**
     * Show the checkout page.
     * GET /checkout
     */
    public function checkout()
    {
        $summary = $this->cartService->getSummary();

        if (empty($summary['items'])) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', compact('summary'));
    }

    /**
     * Process the order submission.
     * POST /checkout
     */
    public function placeOrder(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->createOrder(
                $request->validated(),
                auth()->id() // null for guests
            );

            return redirect()->route('orders.confirmation', $order->order_number)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the order confirmation page.
     * GET /orders/{orderNumber}/confirmation
     */
    public function confirmation(string $orderNumber)
    {
        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            abort(404);
        }

        return view('checkout.confirmation', compact('order'));
    }
}
