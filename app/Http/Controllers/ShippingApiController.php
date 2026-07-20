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
     *
     * THE single zones endpoint for the storefront (checkout + post-purchase
     * zone selection). Returns active zones enriched with this month's
     * delivery schedule — the exact payload the checkout JS renders
     * (has_schedule / schedule / schedule_month badges).
     */
    public function zones(Country $country): JsonResponse
    {
        if (! $country->is_active) {
            return response()->json(['zones' => [], 'current_month' => now()->format('Y-m')]);
        }

        return response()->json($this->shipping->getZonesWithSchedules($country));
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
