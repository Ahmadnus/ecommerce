<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
    ) {}

    // ─── Show checkout page ───────────────────────────────────────────────────

    public function index(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', __('app.cart_empty'));
        }

        if (!$this->checkout->guestCheckoutEnabled() && !Auth::check()) {
            return redirect()->route('login')
                             ->with('info', __('app.login_required_checkout'));
        }

        $summary   = $this->cart->getSummary();
        $countries = $this->checkout->getCountriesWithZones();

        $user         = Auth::user();
        $isGuest      = !$user;
        $guestEnabled = $this->checkout->guestCheckoutEnabled();

        return view('cart.checkout', compact(
            'summary', 'user', 'countries', 'isGuest', 'guestEnabled'
        ));
    }

    // ─── Place the order ──────────────────────────────────────────────────────

    public function placeOrder(Request $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')
                             ->with('error', __('app.cart_empty'));
        }

        if (!$this->checkout->guestCheckoutEnabled() && !Auth::check()) {
            return redirect()->route('login')
                             ->with('info', __('app.login_required_checkout'));
        }

        $rules = [
            'shipping_name'    => 'required|string|max:255',
          'shipping_phone'      => ['required', 'string', 'min:5', 'max:20',
        function($attribute, $value, $fail) use ($request) {
            $code = $request->input('shipping_phone_code');
            if ($code) {
                // Remove any leading + or the code itself if user typed it
                $clean = ltrim($value, '+');
                $clean = preg_replace('/^' . preg_quote($code, '/') . '/', '', $clean);
                $clean = ltrim($clean, '0');
                if (!preg_match('/^\d{6,14}$/', $clean)) {
                    $fail(__('app.validation_phone_invalid_format'));
                }
            }
        }
    ],
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

        try {
            $order = $this->checkout->placeOrder($validated);

            return redirect()
                ->route('orders.success', $order->order_number)
                ->with('success', __('app.order_placed_successfully'));

        } catch (ModelNotFoundException $e) {
            // Invalid zone/country pair — same 404 as the old firstOrFail()
            throw $e;
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

        $order = $this->checkout->findOrder($orderId);

        if (!$order || $order->zone_id !== null) {
            session()->forget('pending_zone_order_id');
            if ($order) {
                return redirect()->route('orders.success', $order->order_number);
            }
            return redirect()->route('products.index')
                             ->with('error', __('app.order_not_found'));
        }

        $countries = $this->checkout->getCountriesWithZones();

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

        $order = $this->checkout->confirmZone(
            (int) $orderId,
            (int) $request->zone_id,
            (int) $request->country_id,
        );

        if (!$order) {
            return redirect()->route('products.index')
                             ->with('error', __('app.order_not_found'));
        }

        session()->forget('pending_zone_order_id');

        return redirect()
            ->route('orders.success', $order->order_number)
            ->with('success', __('app.order_confirmed_successfully'));
    }
}
