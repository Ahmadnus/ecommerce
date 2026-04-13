<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;

class ShippingApiController extends Controller
{
    /**
     * GET /api/shipping/zones/{country}
     * Returns active zones for a country as JSON.
     * Called by the checkout page via Fetch API.
     */
    public function zones(Country $country): JsonResponse
    {
        if (!$country->is_active) {
            return response()->json(['zones' => [], 'currency' => null]);
        }

        $zones = $country->activeZones()
            ->select('id', 'name', 'shipping_price', 'delivery_days')
            ->get()
            ->map(fn($z) => [
                'id'             => $z->id,
                'name'           => $z->name,
                'shipping_price' => (float) $z->shipping_price,
                'delivery_days'  => $z->delivery_days,
            ]);

        // Default currency for this country
        $currency = $country->defaultCurrency()->first();

        return response()->json([
            'zones'    => $zones,
            'currency' => $currency ? [
                'code'          => $currency->code,
                'symbol'        => $currency->symbol,
                'exchange_rate' => (float) $currency->exchange_rate,
            ] : null,
        ]);
    }

    /**
     * GET /api/shipping/countries
     * Returns all active countries for a country selector.
     */
    public function countries(): JsonResponse
    {
        $countries = Country::active()
            ->ordered()
            ->select('id', 'name', 'code')
            ->get();

        return response()->json(['countries' => $countries]);
    }
}