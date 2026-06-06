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
    public function index(Country $country): View
    {
        $zones = $country->zones()->ordered()->get();

        return view('admin.zones.index', compact('country', 'zones'));
    }

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

    public function edit(Country $country, Zone $zone): View
    {
        return view('admin.zones.edit', compact('country', 'zone'));
    }

    public function update(Request $request, Country $country, Zone $zone): RedirectResponse
    {
        $validated = $this->validateZone($request);

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

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validateZone(Request $request): array
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'name_en'        => ['nullable', 'string', 'max:100'],
            'calling_code'   => ['nullable', 'string', 'max:10', 'regex:/^\+?\d{1,10}$/'],
            'shipping_price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'delivery_days'  => ['nullable', 'integer', 'min:1', 'max:365'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $validated['is_active']    = $request->boolean('is_active', true);
        $validated['sort_order']   = $validated['sort_order'] ?? 0;

        if (isset($validated['calling_code'])) {
            $validated['calling_code'] = ltrim($validated['calling_code'], '+') ?: null;
        }

        return $validated;
    }
}