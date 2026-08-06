<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\ShippingZoneApiService;
use Illuminate\Http\JsonResponse;

class ShippingApiController extends Controller
{
    public function __construct(
        private readonly ShippingZoneApiService $shipping,
    ) {}

    /**
     * GET /api/shipping/zones/{country}
     * Returns active zones for a country as JSON.
     * Called by the checkout page via Fetch API.
     */
    public function zones(Country $country): JsonResponse
    {
        return response()->json($this->shipping->getCheckoutZonesPayload($country));
    }

    /**
     * GET /api/shipping/countries
     * Returns all active countries for a country selector.
     */
    public function countries(): JsonResponse
    {
        return response()->json(['countries' => $this->shipping->getActiveCountries()]);
    }
}
