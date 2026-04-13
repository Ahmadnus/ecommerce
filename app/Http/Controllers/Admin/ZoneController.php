<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    /**
     * List all zones for a given country.
     */
    public function index(Country $country): View
    {
        $zones = $country->zones()->get();
        return view('admin.zones.index', compact('country', 'zones'));
    }

    /**
     * Store a new zone via inline form (no separate create page needed).
     */
    public function store(Request $request, Country $country): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'shipping_price' => 'required|numeric|min:0',
            'delivery_days'  => 'nullable|integer|min:1|max:365',
            'is_active'      => 'nullable|boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ], [
            'name.required'           => 'اسم المنطقة مطلوب.',
            'shipping_price.required' => 'سعر الشحن مطلوب.',
            'shipping_price.numeric'  => 'سعر الشحن يجب أن يكون رقماً.',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $country->zones()->create($validated);

        return redirect()
            ->route('admin.countries.zones.index', $country)
            ->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    /**
     * Update an existing zone (inline, no separate edit page).
     */
    public function update(Request $request, Country $country, Zone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'shipping_price' => 'required|numeric|min:0',
            'delivery_days'  => 'nullable|integer|min:1|max:365',
            'is_active'      => 'nullable|boolean',
            'sort_order'     => 'nullable|integer|min:0',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $zone->update($validated);

        return redirect()
            ->route('admin.countries.zones.index', $country)
            ->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    public function destroy(Country $country, Zone $zone): RedirectResponse
    {
        $zone->delete();
        return redirect()
            ->route('admin.countries.zones.index', $country)
            ->with('success', 'تم حذف المنطقة.');
    }
}