<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

/**
 * Vehicle Types / Classes — إدارة فئات السيارات
 */
class VehicleCategoryController extends Controller
{
    public function index()
    {
        return view('admin.vehicle-categories.index', [
            'categories' => VehicleCategory::withCount('vehicles')->ordered()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.vehicle-categories.create');
    }

    public function store(Request $request)
    {
        $category = VehicleCategory::create($this->validated($request));
        $this->syncImage($request, $category);

        return redirect()->route('admin.vehicle-categories.index')->with('success', 'تمت إضافة الفئة');
    }

    public function edit(VehicleCategory $vehicleCategory)
    {
        return view('admin.vehicle-categories.edit', ['category' => $vehicleCategory]);
    }

    public function update(Request $request, VehicleCategory $vehicleCategory)
    {
        $vehicleCategory->update($this->validated($request));
        $this->syncImage($request, $vehicleCategory);

        return redirect()->route('admin.vehicle-categories.index')->with('success', 'تم تحديث الفئة');
    }

    public function destroy(VehicleCategory $vehicleCategory)
    {
        $vehicleCategory->delete();

        return back()->with('success', 'تم حذف الفئة');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name.ar'        => ['required', 'string', 'max:120'],
            'name.en'        => ['nullable', 'string', 'max:120'],
            'description.ar' => ['nullable', 'string', 'max:500'],
            'description.en' => ['nullable', 'string', 'max:500'],
            'icon'           => ['nullable', 'string', 'max:80'],
            'price_from'     => ['nullable', 'numeric', 'min:0'],
            'sort_order'     => ['nullable', 'integer', 'min:0'],
            'image'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
        ]);

        unset($data['image']);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = $data['sort_order'] ?? 0;

        return $data;
    }

    private function syncImage(Request $request, VehicleCategory $category): void
    {
        if ($request->hasFile('image')) {
            $category->clearMediaCollection('vehicle_category_image');
            $category->addMedia($request->file('image'))->toMediaCollection('vehicle_category_image');
        }
    }
}
