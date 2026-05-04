<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Zone;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    private function guestCheckoutEnabled(): bool
    {
        return get_otp_setting('guest_checkout_enabled', '0') === '1';
    }

    // ─── Show checkout page ───────────────────────────────────────────────────

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', __('app.cart_empty'));
        }

        if (!$this->guestCheckoutEnabled() && !Auth::check()) {
            return redirect()->route('login')
                             ->with('info', __('app.login_required_checkout'));
        }

        $summary = $this->cart->getSummary();

        $countries = Country::active()
            ->ordered()
            ->whereHas('activeZones')
            ->with(['activeZones' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->get();

        $user         = Auth::user();
        $isGuest      = !$user;
        $guestEnabled = $this->guestCheckoutEnabled();

        return view('cart.checkout', compact(
            'summary', 'user', 'countries', 'isGuest', 'guestEnabled'
        ));
    }

    // ─── AJAX: zones for a given country ─────────────────────────────────────

    public function zonesForCountry(Country $country): JsonResponse
    {
        $zones = $country->activeZones()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'shipping_price', 'delivery_days']);

        return response()->json(['zones' => $zones]);
    }

    // ─── Place the order ──────────────────────────────────────────────────────

    public function placeOrder(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', __('app.cart_empty'));
        }

        if (!$this->guestCheckoutEnabled() && !Auth::check()) {
            return redirect()->route('login')
                             ->with('info', __('app.login_required_checkout'));
        }

        $rules = [
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|min:7|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city'    => 'required|string|max:100',
            'shipping_zip'     => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:1000',
            'country_id'       => 'required|exists:countries,id',
            'zone_id'          => 'required|exists:zones,id',
            'payment_method'   => 'required|in:cod',
        ];

        if (!Auth::check()) {
            $rules['guest_email'] = 'nullable|email|max:255';
        }

        $validated = $request->validate($rules, [
            'shipping_name.required'    => __('app.validation_full_name_required'),
            'shipping_phone.required'   => __('app.validation_phone_required'),
            'shipping_address.required' => __('app.validation_address_required'),
            'shipping_city.required'    => __('app.validation_city_required'),
            'country_id.required'       => __('app.validation_country_required'),
            'zone_id.required'          => __('app.validation_zone_required'),
        ]);

        $zone = Zone::where('id', $validated['zone_id'])
            ->where('country_id', $validated['country_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $summary     = $this->cart->getSummary();
        $deliveryFee = (float) $zone->shipping_price;
        $total       = round($summary['subtotal'] + $deliveryFee, 2);

        try {
            $order = DB::transaction(function () use ($validated, $summary, $zone, $deliveryFee, $total) {

                $userId     = Auth::check() ? Auth::user()->getAttributes()['id'] : null;
                $guestEmail = !$userId ? ($validated['guest_email'] ?? null) : null;

                $order = Order::create([
                    'user_id'          => $userId,
                    'guest_email'      => $guestEmail,
                    'guest_session_id' => !$userId ? session()->getId() : null,

                    'order_number'     => Order::generateOrderNumber(),
                    'status'           => Order::STATUS_PENDING,
                    'payment_method'   => Order::PAYMENT_COD,
                    'payment_status'   => Order::PAYMENT_PENDING,

                    'zone_id'          => $zone->id,
                    'shipping_area'    => $zone->name . ' (' . $zone->country->name . ')',
                    'delivery_days'    => $zone->delivery_days,
                    'tax_amount'       => $deliveryFee,

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

                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    } else {
                        $variant = ProductVariant::where('product_id', $item['product_id'])
                            ->lockForUpdate()->first();
                    }

                    if (!$variant || $variant->stock_quantity < $item['quantity']) {
                        throw new \RuntimeException(
                            __('app.insufficient_stock_for_product', ['product' => $item['name']])
                        );
                    }

                    $variant->decrement('stock_quantity', $item['quantity']);
                }

                return $order;
            });

            $this->cart->clear();

            return redirect()
                ->route('orders.success', $order->order_number)
                ->with('success', __('app.order_placed_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // ─── Zone selection ───────────────────────────────────────────────────────

    public function selectZone(): View|RedirectResponse
    {
        $orderId = session('pending_zone_order_id');

        if (!$orderId) {
            return redirect()->route('products.index')
                             ->with('error', __('app.session_expired_reorder'));
        }

        $order = Order::find($orderId);

        if (!$order || $order->zone_id !== null) {
            session()->forget('pending_zone_order_id');
            if ($order) {
                return redirect()->route('orders.success', $order->order_number);
            }
            return redirect()->route('products.index')
                             ->with('error', __('app.order_not_found'));
        }

        $countries = Country::active()
            ->ordered()
            ->whereHas('activeZones')
            ->with(['activeZones' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->get();

        return view('orders.select-zone', compact('order', 'countries'));
    }

    public function confirmZone(Request $request): RedirectResponse
    {
        $orderId = session('pending_zone_order_id');

        if (!$orderId) {
            return redirect()->route('products.index')
                             ->with('error', __('app.session_expired'));
        }

        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'zone_id'    => 'required|exists:zones,id',
        ]);

        $zone = Zone::where('id', $request->zone_id)
            ->where('country_id', $request->country_id)
            ->where('is_active', true)
            ->firstOrFail();

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('products.index')
                             ->with('error', __('app.order_not_found'));
        }

        $deliveryFee = (float) $zone->shipping_price;
        $order->update([
            'zone_id'       => $zone->id,
            'shipping_area' => $zone->name . ' (' . $zone->country->name . ')',
            'delivery_days' => $zone->delivery_days,
            'tax_amount'    => $deliveryFee,
            'total_amount'  => round((float) $order->subtotal + $deliveryFee, 2),
        ]);

        session()->forget('pending_zone_order_id');

        return redirect()
            ->route('orders.success', $order->order_number)
            ->with('success', __('app.order_confirmed_successfully'));
    }
}