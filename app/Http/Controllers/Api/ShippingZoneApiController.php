<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\ShippingZoneApiService;
use Illuminate\Http\JsonResponse;

class ShippingZoneApiController extends Controller
{
    public function __construct(
        private readonly ShippingZoneApiService $zones,
    ) {}

    public function index(Country $country): JsonResponse
    {
        return response()->json($this->zones->getZonesWithSchedules($country));
    }
}
