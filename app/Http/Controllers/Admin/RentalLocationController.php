<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Branches / Pickup Locations — إدارة الفروع ومواقع الاستلام
 */
class RentalLocationController extends Controller
{
    public function index()
    {
        return view('admin.locations.index', [
            'locations' => RentalLocation::withCount(['vehicles', 'pickupBookings'])
                                ->ordered()
                                ->paginate(20),
            'types'     => RentalLocation::types(),
        ]);
    }

    public function create()
    {
        return view('admin.locations.create', ['types' => RentalLocation::types()]);
    }

    public function store(Request $request)
    {
        RentalLocation::create($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', 'تمت إضافة الفرع');
    }

    public function edit(RentalLocation $location)
    {
        return view('admin.locations.edit', [
            'location' => $location,
            'types'    => RentalLocation::types(),
        ]);
    }

    public function update(Request $request, RentalLocation $location)
    {
        $location->update($this->validated($request, $location));

        return redirect()->route('admin.locations.index')->with('success', 'تم تحديث الفرع');
    }

    public function destroy(RentalLocation $location)
    {
        $location->delete();

        return back()->with('success', 'تم حذف الفرع');
    }

    private function validated(Request $request, ?RentalLocation $location = null): array
    {
        $data = $request->validate([
            'name.ar'     => ['required', 'string', 'max:150'],
            'name.en'     => ['nullable', 'string', 'max:150'],
            'city.ar'     => ['required', 'string', 'max:120'],
            'city.en'     => ['nullable', 'string', 'max:120'],
            'address.ar'  => ['nullable', 'string', 'max:255'],
            'address.en'  => ['nullable', 'string', 'max:255'],
            'code'        => ['nullable', 'string', 'max:40'],
            'type'        => ['required', Rule::in(array_keys(RentalLocation::types()))],
            'phone'       => ['nullable', 'string', 'max:40'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'pickup_fee'  => ['nullable', 'numeric', 'min:0'],
            'one_way_fee' => ['nullable', 'numeric', 'min:0'],
            'opens_at'    => ['nullable', 'date_format:H:i'],
            'closes_at'   => ['nullable', 'date_format:H:i'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_24h']      = $request->boolean('is_24h');
        $data['is_active']   = $request->boolean('is_active', true);
        $data['pickup_fee']  = $data['pickup_fee'] ?? 0;
        $data['one_way_fee'] = $data['one_way_fee'] ?? 0;
        $data['sort_order']  = $data['sort_order'] ?? 0;

        // A 24-hour branch has no opening window to store.
        if ($data['is_24h']) {
            $data['opens_at'] = $data['closes_at'] = null;
        }

        return $data;
    }
}
