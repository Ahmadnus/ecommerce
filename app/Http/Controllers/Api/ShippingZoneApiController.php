<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingZoneApiController extends Controller
{
    public function index(Country $country): JsonResponse
    {
        $currentMonth = now()->format('Y-m');

        $zones = $country->zones()
            ->active()
            ->ordered()
            ->get();

        Log::info('Zone schedules debug', [
            'month' => $currentMonth,
            'zones' => $zones->toArray(),
        ]);

        if ($zones->isEmpty()) {
            return response()->json([
                'zones' => [],
                'current_month' => $currentMonth,
            ]);
        }

        $zoneIds = $zones->pluck('id')->all();

        $schedules = DB::table('zone_delivery_schedules')
            ->whereIn('zone_id', $zoneIds)
            ->where('month', $currentMonth)
            ->where('is_active', true)
            ->get()
            ->keyBy('zone_id');

        $result = $zones->map(function ($zone) use ($schedules) {
            $raw = $schedules->get($zone->id);

            $availableDays = null;
            if ($raw?->available_days) {
                $decoded = json_decode($raw->available_days, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && ! empty($decoded)) {
                    $decoded = array_values(array_unique($decoded));
                    sort($decoded, SORT_NATURAL);
                    $availableDays = $decoded;
                }
            }

            $deliveryDays = $raw?->delivery_days ?? $zone->delivery_days;
            $deliveryDays = $deliveryDays !== null ? (int) $deliveryDays : null;

            $deliveryLabel = null;
            if ($deliveryDays !== null) {
                $deliveryLabel = match ($deliveryDays) {
                    1 => 'يوم عمل واحد',
                    2 => 'يومان',
                    default => $deliveryDays . ' أيام عمل',
                };
            }

            $daysDisplay = $availableDays
                ? implode('، ', $availableDays)
                : 'جميع أيام الشهر';

            return [
                'id'             => $zone->id,
                'name'           => $zone->name,
                'name_en'        => $zone->name_en,
                'shipping_price' => (float) $zone->shipping_price,
                'delivery_days'  => $deliveryDays,

                'has_schedule'   => $raw !== null,
                'schedule_month' => $raw?->month,

                'schedule' => $raw ? [
                    'month'          => $raw->month,
                    'delivery_days'   => $deliveryDays,
                    'available_days'  => $availableDays,
                    'days_display'    => $daysDisplay,
                    'delivery_label'  => $deliveryLabel,
                ] : null,
            ];
        })->values();

        return response()->json([
            'zones' => $result,
            'current_month' => $currentMonth,
        ]);
    }
}