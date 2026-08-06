<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalLocation;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Vehicle Fleet Management — إدارة أسطول السيارات
 */
class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with(['category', 'location'])
            ->when($request->filled('search'), fn($q) => $q->search($request->string('search')))
            ->when($request->filled('category'), fn($q) => $q->where('vehicle_category_id', $request->integer('category')))
            ->when($request->filled('status'), fn($q) => $q->where('availability_status', $request->string('status')))
            ->when($request->filled('location'), fn($q) => $q->where('rental_location_id', $request->integer('location')))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.vehicles.index', [
            'vehicles'   => $vehicles,
            'categories' => VehicleCategory::ordered()->get(),
            'locations'  => RentalLocation::ordered()->get(),
            'stats'      => [
                'total'       => Vehicle::count(),
                'available'   => Vehicle::where('availability_status', Vehicle::STATUS_AVAILABLE)->count(),
                'rented'      => Vehicle::where('availability_status', Vehicle::STATUS_RENTED)->count(),
                'maintenance' => Vehicle::where('availability_status', Vehicle::STATUS_MAINTENANCE)->count(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.vehicles.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateVehicle($request);

        DB::transaction(function () use ($request, $data) {
            $vehicle = Vehicle::create($data);
            $this->syncMedia($request, $vehicle);
        });

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'تمت إضافة السيارة بنجاح');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', array_merge($this->formData(), compact('vehicle')));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $this->validateVehicle($request, $vehicle);

        DB::transaction(function () use ($request, $vehicle, $data) {
            $vehicle->update($data);
            $this->syncMedia($request, $vehicle);
        });

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'تم تحديث بيانات السيارة');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return back()->with('success', 'تم حذف السيارة');
    }

    /** Quick status change from the fleet table. */
    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'availability_status' => ['required', Rule::in(array_keys(Vehicle::statuses()))],
        ]);

        $vehicle->update(['availability_status' => $request->string('availability_status')]);

        return back()->with('success', 'تم تحديث حالة السيارة');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function formData(): array
    {
        return [
            'categories'    => VehicleCategory::ordered()->get(),
            'locations'     => RentalLocation::ordered()->get(),
            'statuses'      => Vehicle::statuses(),
            'transmissions' => Vehicle::transmissions(),
            'fuelTypes'     => Vehicle::fuelTypes(),
        ];
    }

    private function validateVehicle(Request $request, ?Vehicle $vehicle = null): array
    {
        $validated = $request->validate([
            'brand'               => ['required', 'string', 'max:80'],
            'model'               => ['required', 'string', 'max:80'],
            'year'                => ['required', 'integer', 'min:1980', 'max:' . (date('Y') + 2)],
            'registration_number' => [
                'nullable', 'string', 'max:40',
                Rule::unique('vehicles', 'registration_number')->ignore($vehicle?->id)->whereNull('deleted_at'),
            ],
            'color'                 => ['nullable', 'string', 'max:40'],
            'description.ar'        => ['nullable', 'string'],
            'description.en'        => ['nullable', 'string'],
            'vehicle_category_id'   => ['nullable', 'exists:vehicle_categories,id'],
            'rental_location_id'    => ['nullable', 'exists:rental_locations,id'],
            'transmission'          => ['required', Rule::in(array_keys(Vehicle::transmissions()))],
            'fuel_type'             => ['required', Rule::in(array_keys(Vehicle::fuelTypes()))],
            'seats'                 => ['required', 'integer', 'min:1', 'max:60'],
            'doors'                 => ['required', 'integer', 'min:1', 'max:10'],
            'bags'                  => ['required', 'integer', 'min:0', 'max:30'],
            'engine_cc'             => ['nullable', 'integer', 'min:0'],
            'mileage_limit_per_day' => ['nullable', 'integer', 'min:0'],
            'features'              => ['nullable', 'array'],
            'features.*'            => ['nullable', 'string', 'max:80'],
            'daily_rate'            => ['required', 'numeric', 'min:0'],
            'weekly_rate'           => ['nullable', 'numeric', 'min:0'],
            'monthly_rate'          => ['nullable', 'numeric', 'min:0'],
            'discount_daily_rate'   => ['nullable', 'numeric', 'min:0', 'lt:daily_rate'],
            'security_deposit'      => ['nullable', 'numeric', 'min:0'],
            'min_driver_age'        => ['required', 'integer', 'min:16', 'max:99'],
            'min_rental_days'       => ['required', 'integer', 'min:1', 'max:365'],
            'availability_status'   => ['required', Rule::in(array_keys(Vehicle::statuses()))],
            'units_available'       => ['required', 'integer', 'min:0'],
            'main_image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
            'gallery_images'        => ['nullable', 'array', 'max:10'],
            'gallery_images.*'      => ['image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
        ]);

        // Drop empty rows from the dynamic feature list.
        $validated['features'] = array_values(array_filter(
            $validated['features'] ?? [],
            fn($f) => filled($f)
        ));

        $validated['is_active']   = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['sort_order']  = $request->integer('sort_order');

        // These are file uploads, not columns.
        unset($validated['main_image'], $validated['gallery_images']);

        return $validated;
    }

    private function syncMedia(Request $request, Vehicle $vehicle): void
    {
        if ($request->hasFile('main_image')) {
            $vehicle->clearMediaCollection('vehicle_main');
            $vehicle->addCompressedMedia($request->file('main_image'), 'vehicle_main');
        }

        foreach ((array) $request->file('gallery_images', []) as $image) {
            $vehicle->addCompressedMedia($image, 'vehicle_gallery');
        }
    }
}
