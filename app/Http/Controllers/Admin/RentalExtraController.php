<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalExtra;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Optional Extras — إدارة الإضافات الاختيارية
 *
 * Add-ons offered on the booking form (collision waiver, extra driver, …).
 */
class RentalExtraController extends Controller
{
    public function index()
    {
        return view('admin.extras.index', [
            'extras' => RentalExtra::ordered()->paginate(30),
        ]);
    }

    public function create()
    {
        return view('admin.extras.create');
    }

    public function store(Request $request)
    {
        RentalExtra::create($this->validated($request));

        return redirect()->route('admin.extras.index')->with('success', 'تمت إضافة الخدمة');
    }

    public function edit(RentalExtra $extra)
    {
        return view('admin.extras.edit', ['extra' => $extra]);
    }

    public function update(Request $request, RentalExtra $extra)
    {
        $extra->update($this->validated($request, $extra));

        return redirect()->route('admin.extras.index')->with('success', 'تم تحديث الخدمة');
    }

    /**
     * Deactivate rather than delete when the add-on is already attached to
     * bookings — their `extras` JSON snapshot keeps the price and label, but
     * removing the row would still lose the definition for future reporting.
     */
    public function destroy(RentalExtra $extra)
    {
        $extra->delete();

        return back()->with('success', 'تم حذف الخدمة');
    }

    /** Quick enable/disable from the list. */
    public function toggle(RentalExtra $extra)
    {
        $extra->update(['is_active' => ! $extra->is_active]);

        return back()->with('success', 'تم تحديث الحالة');
    }

    private function validated(Request $request, ?RentalExtra $extra = null): array
    {
        $data = $request->validate([
            'code'       => [
                'nullable', 'string', 'max:60', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('rental_extras', 'code')->ignore($extra?->id),
            ],
            'name.en'    => ['required', 'string', 'max:120'],
            'name.ar'    => ['required', 'string', 'max:120'],
            'icon'       => ['nullable', 'string', 'max:100'],
            'price'      => ['required', 'numeric', 'min:0'],
            'per_day'    => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'code.regex' => 'المعرّف يجب أن يحتوي حروفاً إنجليزية صغيرة وأرقاماً وشرطة سفلية فقط.',
        ]);

        /*
         * The code is only settable on create — bookings reference it in their
         * extras JSON, so changing it later would orphan those records.
         */
        if ($extra) {
            unset($data['code']);
        } else {
            // Absent optional fields are missing from the validated array
            // entirely, not present-and-null, so ?? has to come first.
            $data['code'] = ($data['code'] ?? null) ?: Str::slug($data['name']['en'], '_');
        }

        $data['icon']       = ($data['icon'] ?? null) ?: 'fa-solid fa-plus';
        $data['per_day']    = $request->boolean('per_day');
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['price']      = (float) $data['price'];

        return $data;
    }
}
