<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckoutService
 *
 * Business logic for the storefront checkout flow (COD orders with
 * zone-based delivery fees). Deliberately separate from OrderService,
 * which backs a different (repository-based) order-creation flow with
 * different columns — merging them would change behavior.
 *
 * Never returns views/redirects; returns data/models and throws on failure.
 */
class CheckoutService
{
    public function __construct(private readonly CartService $cart) {}

    public function guestCheckoutEnabled(): bool
    {
        return get_otp_setting('guest_checkout_enabled', '0') === '1';
    }

    /**
     * Active countries that have active zones, with those zones loaded
     * in display order. Used by the checkout page and zone selection page.
     */
    public function getCountriesWithZones()
    {
        return Country::active()
            ->ordered()
            ->whereHas('activeZones')
            ->with(['activeZones' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->get();
    }

    /**
     * Active zones of a country for the AJAX dropdown.
     */
    public function zonesForCountry(Country $country)
    {
        return $country->activeZones()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'shipping_price', 'delivery_days']);
    }

    /**
     * Resolve an active zone belonging to the given country.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveZone(int $zoneId, int $countryId): Zone
    {
        return Zone::where('id', $zoneId)
            ->where('country_id', $countryId)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function findOrder(int $orderId): ?Order
    {
        return Order::find($orderId);
    }

    /**
     * Place a COD order from the current cart.
     *
     * $validated: the validated checkout form data (shipping_* fields,
     * country_id, zone_id, notes, guest_email, optional shipping_phone_code).
     *
     * Creates the order + items and decrements stock (row-locked) inside a
     * transaction; clears the cart only after the transaction commits.
     *
     * @throws \RuntimeException on insufficient stock (rolled back)
     * @throws \Exception on any other failure (rolled back)
     */
    public function placeOrder(array $validated): Order
    {
        $zone = $this->resolveZone((int) $validated['zone_id'], (int) $validated['country_id']);

        $summary     = $this->cart->getSummary();
        $deliveryFee = (float) $zone->shipping_price;
        $total       = round($summary['subtotal'] + $deliveryFee, 2);

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
              'shipping_phone' => ($validated['shipping_phone_code'] ?? '')
    ? '+' . $validated['shipping_phone_code'] . $validated['shipping_phone']
    : $validated['shipping_phone'],
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

        return $order;
    }

    /**
     * Attach a zone to an order that was created without one, recomputing
     * the delivery fee and total.
     *
     * Returns null when the order no longer exists (zone is validated first,
     * matching the original controller's order of operations).
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if zone invalid
     */
    public function confirmZone(int $orderId, int $zoneId, int $countryId): ?Order
    {
        $zone = $this->resolveZone($zoneId, $countryId);

        $order = Order::find($orderId);

        if (!$order) {
            return null;
        }

        $deliveryFee = (float) $zone->shipping_price;
        $order->update([
            'zone_id'       => $zone->id,
            'shipping_area' => $zone->name . ' (' . $zone->country->name . ')',
            'delivery_days' => $zone->delivery_days,
            'tax_amount'    => $deliveryFee,
            'total_amount'  => round((float) $order->subtotal + $deliveryFee, 2),
        ]);

        return $order;
    }
}
