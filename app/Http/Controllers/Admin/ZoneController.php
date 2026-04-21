<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ZoneController — Admin CRUD for zones (states / cities) under a Country.
 *
 * Routes (add inside your admin group, nested under countries):
 *
 *   Route::resource('countries.zones', ZoneController::class)
 *        ->names('admin.countries.zones')
 *        ->shallow();   // ← shallow gives clean /zones/{zone}/edit URLs
 */
class ZoneController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────────

    public function index(Country $country): View
    {
        $zones = $country->zones()->ordered()->get();

        return view('admin.zones.index', compact('country', 'zones'));
    }

    // ── Create / Store ─────────────────────────────────────────────────────────

    public function create(Country $country): View
    {
        return view('admin.zones.create', compact('country'));
    }

    public function store(Request $request, Country $country): RedirectResponse
    {
        $validated = $this->validateZone($request);

        $country->zones()->create($validated);

        return redirect()
            ->route('admin.countries.zones.index', $country)
            ->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    // ── Edit / Update ──────────────────────────────────────────────────────────

    public function edit(Zone $zone): View
    {
        $zone->load('country');
        $country = $zone->country;

        return view('admin.zones.edit', compact('zone', 'country'));
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $validated = $this->validateZone($request, $zone);

        $zone->update($validated);

        return redirect()
            ->route('admin.countries.zones.index', $zone->country_id)
            ->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    // ── Delete ─────────────────────────────────────────────────────────────────

    public function destroy(Zone $zone): RedirectResponse
    {
        $countryId = $zone->country_id;
        $zone->delete();

        return redirect()
            ->route('admin.countries.zones.index', $countryId)
            ->with('success', 'تم حذف المنطقة.');
    }

    // ── Private ────────────────────────────────────────────────────────────────

    private function validateZone(Request $request, ?Zone $zone = null): array
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:120',
            'name_en'      => 'nullable|string|max:120',
            'shipping_price' => 'nullable|numeric|min:0',
            'calling_code' => ['nullable', 'string', 'max:10', 'regex:/^\+?\d{0,10}$/'],
            'is_active'    => 'nullable|boolean',
            'sort_order'   => 'nullable|integer|min:0',
        ], [
            
            'name.required'      => 'اسم المنطقة مطلوب.',
            'calling_code.regex' => 'رمز الاتصال يجب أن يحتوي على أرقام فقط (مثال: 963 أو +963).',
        ]);
$validated['shipping_price'] = $validated['shipping_price'] ?? 0;
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Normalise: strip leading "+" — store digits only
        if (!empty($validated['calling_code'])) {
            $validated['calling_code'] = ltrim($validated['calling_code'], '+');
        }

        return $validated;
    }
}