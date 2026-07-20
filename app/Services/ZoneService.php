<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Zone;

/**
 * ZoneService — business logic for the admin shipping-zones CRUD
 * (zones nested under countries). Never returns views/redirects.
 */
class ZoneService
{
    public function getZonesForCountry(Country $country)
    {
        // currentSchedule is eager-loaded so the matrix can show this month's
        // effective delivery estimate without an extra query per governorate.
        return $country->zones()->ordered()->with('currentSchedule')->get();
    }

    public function create(Country $country, array $validated): Zone
    {
        return $country->zones()->create($validated);
    }

    public function update(Zone $zone, array $validated): Zone
    {
        $zone->update($validated);

        return $zone;
    }

    public function delete(Zone $zone): void
    {
        $zone->delete();
    }

    /**
     * Normalize validated zone input (is_active/sort_order defaults,
     * calling_code cleanup).
     */
    public function normalizeZoneData(array $validated, bool $isActive): array
    {
        $validated['is_active']    = $isActive;
        $validated['sort_order']   = $validated['sort_order'] ?? 0;

        if (isset($validated['calling_code'])) {
            $validated['calling_code'] = ltrim($validated['calling_code'], '+') ?: null;
        }

        return $validated;
    }
}
