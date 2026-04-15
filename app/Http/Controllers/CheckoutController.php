<?php

// ─────────────────────────────────────────────────────────────────────────────
// DIFF: what to change in your existing CheckoutController@placeOrder
//
// The ONLY change needed is in the Order::create() call.
// Replace the 'tax_amount' column mapping with 'delivery_fee' (or keep
// tax_amount but feed it the delivery_fee value, depending on your migration).
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'سلة التسوق فارغة.');
        }

        $summary = $this->cart->getSummary();
        $user    = Auth::user();

        return view('cart.checkout', compact('summary', 'user'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'سلة التسوق فارغة.');
        }

        $validated = $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string',
            'shipping_address' => 'required|string|max:500',
            'shipping_city'    => 'required|string|max:100',
            'shipping_zip'     => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $summary = $this->cart->getSummary();

        try {
            $order = DB::transaction(function () use ($validated, $summary) {

                $userId = Auth::check() ? Auth::user()->id : null;

                $order = Order::create([
                    'user_id'          => $userId,
                    'order_number'     => Order::generateOrderNumber(),
                    'status'           => Order::STATUS_PENDING,
                    'payment_method'   => Order::PAYMENT_COD,
                    'payment_status'   => Order::PAYMENT_PENDING,

                    // ── UPDATED: tax_amount now stores delivery_fee ──────────
                    // Option A: your orders table already has a delivery_fee column:
                    //   'delivery_fee' => (float) $summary['delivery_fee'],
                    //
                    // Option B: you are keeping the tax_amount column name but
                    //   feeding it the delivery fee value (simpler, no migration needed):
                    'tax_amount'       => (float) $summary['delivery_fee'],
                    // ────────────────────────────────────────────────────────

                    'subtotal'         => (float) $summary['subtotal'],
                    'shipping_amount'  => 0.00, // handled by delivery_fee above
                    'total_amount'     => (float) $summary['total'],
                    'shipping_name'    => $validated['shipping_name'],
                    'shipping_phone'   => $validated['shipping_phone'],
                    'shipping_address' => $validated['shipping_address'],
                    'shipping_city'    => $validated['shipping_city'],
                    'shipping_zip'     => $validated['shipping_zip'] ?? null,
                    'notes'            => $validated['notes'] ?? null,
                ]);

                foreach ($summary['items'] as $item) {
                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $item['product_id'],
                        'product_variant_id' => $item['variant_id'] ?? null,
                        'product_name'       => $item['name'],
                        'quantity'           => $item['quantity'],
                        'unit_price'         => $item['price'],
                        'total_price'        => $item['subtotal'],
                    ]);

                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    } else {
                        $variant = ProductVariant::where('product_id', $item['product_id'])
                                                 ->lockForUpdate()->first();
                    }

                    if (!$variant || $variant->stock_quantity < $item['quantity']) {
                        throw new \RuntimeException("المخزون غير كافٍ للمنتج: " . $item['name']);
                    }

                    $variant->decrement('stock_quantity', $item['quantity']);
                }

                return $order;
            });

            $this->cart->clear();

            return redirect()
                ->route('orders.success', $order->order_number)
                ->with('success', 'تم إرسال طلبك بنجاح!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}