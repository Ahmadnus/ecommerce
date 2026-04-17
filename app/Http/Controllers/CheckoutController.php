<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Zone;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    // ─── Show checkout page ───────────────────────────────────────────────────

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'سلة التسوق فارغة.');
        }

        $summary = $this->cart->getSummary();

        // Load all active countries that have at least one active zone
        $countries = Country::active()
            ->ordered()
            ->whereHas('activeZones')
            ->with(['activeZones' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->get();

        $user = Auth::user();

        return view('cart.checkout', compact('summary', 'user', 'countries'));
    }

    // ─── AJAX: zones for a given country ─────────────────────────────────────

    /**
     * Returns active zones for a country as JSON.
     * Called by the JS on the checkout page when the user selects a country.
     */
public function zonesForCountry($countryId)
{
    $zones = Zone::where('country_id', $countryId)
        ->where('is_active', 1)
        ->orderBy('sort_order')
        ->get(['id', 'name', 'shipping_price', 'delivery_days']);

    return response()->json($zones);
}
    // ─── Place the order ──────────────────────────────────────────────────────

    public function placeOrder(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', 'سلة التسوق فارغة.');
        }

        $validated = $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:30',
            'shipping_address' => 'required|string|max:500',
            'shipping_city'    => 'required|string|max:100',
            'shipping_zip'     => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:1000',
            'country_id'       => 'required|exists:countries,id',
            'zone_id'          => 'required|exists:zones,id',
            'payment_method'   => 'required|in:cod',
        ]);

        // Verify the selected zone actually belongs to the selected country
        $zone = Zone::where('id', $validated['zone_id'])
            ->where('country_id', $validated['country_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $summary      = $this->cart->getSummary();
        $deliveryFee  = (float) $zone->shipping_price;    // JOD — from admin-defined zone
        $total        = round($summary['subtotal'] + $deliveryFee, 2);

        try {
            $order = DB::transaction(function () use ($validated, $summary, $zone, $deliveryFee, $total) {

                $userId = Auth::check() ? Auth::user()->id : null;

                $order = Order::create([
                    'user_id'          => $userId,
                    'order_number'     => Order::generateOrderNumber(),
                    'status'           => Order::STATUS_PENDING,
                    'payment_method'   => Order::PAYMENT_COD,
                    'payment_status'   => Order::PAYMENT_PENDING,

                    // ── Zone-based shipping ───────────────────────────────
                    'zone_id'          => $zone->id,
                    'shipping_area'    => $zone->name . ' (' . $zone->country->name . ')',
                    'delivery_days'    => $zone->delivery_days,
                    'tax_amount'       => $deliveryFee,    // reusing existing column for delivery fee
                    // ─────────────────────────────────────────────────────

                    'subtotal'         => (float) $summary['subtotal'],
                    'shipping_amount'  => 0.00,
                    'total_amount'     => $total,

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

                    // Decrement stock with lock to prevent race conditions
                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    } else {
                        $variant = ProductVariant::where('product_id', $item['product_id'])
                            ->lockForUpdate()->first();
                    }

                    if (!$variant || $variant->stock_quantity < $item['quantity']) {
                        throw new \RuntimeException('المخزون غير كافٍ للمنتج: ' . $item['name']);
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
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}