@php $location = $location ?? null; @endphp

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-xl p-6 space-y-6 max-w-4xl">

    {{-- Bilingual fields --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach([
            ['name',    'اسم الفرع', true],
            ['city',    'المدينة',   true],
            ['address', 'العنوان',   false],
        ] as [$field, $label, $required])
            <div>
                <label for="{{ $field }}_ar" class="block text-sm font-semibold mb-2">
                    {{ $label }} (عربي) @if($required)<span class="text-red-500">*</span>@endif
                </label>
                <input type="text" id="{{ $field }}_ar" name="{{ $field }}[ar]" @required($required)
                       value="{{ old("$field.ar", $location?->getTranslation($field, 'ar', false)) }}"
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label for="{{ $field }}_en" class="block text-sm font-semibold mb-2">{{ $label }} (إنجليزي)</label>
                <input type="text" id="{{ $field }}_en" name="{{ $field }}[en]" dir="ltr"
                       value="{{ old("$field.en", $location?->getTranslation($field, 'en', false)) }}"
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-gray-100">
        <div>
            <label for="type" class="block text-sm font-semibold mb-2">النوع <span class="text-red-500">*</span></label>
            <select id="type" name="type" required class="w-full rounded-lg border-gray-300 text-sm">
                @foreach($types as $key => $type)
                    <option value="{{ $key }}" @selected(old('type', $location?->type ?? 'branch') === $key)>{{ $type['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="code" class="block text-sm font-semibold mb-2">الرمز</label>
            <input type="text" id="code" name="code" dir="ltr" value="{{ old('code', $location?->code) }}"
                   placeholder="RUH_AIRPORT" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="phone" class="block text-sm font-semibold mb-2">الهاتف</label>
            <input type="text" id="phone" name="phone" dir="ltr" value="{{ old('phone', $location?->phone) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>

    {{-- Fees --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-5 border-t border-gray-100">
        <div>
            <label for="pickup_fee" class="block text-sm font-semibold mb-2">رسوم الاستلام (د.أ)</label>
            <input type="number" step="0.01" min="0" id="pickup_fee" name="pickup_fee"
                   value="{{ old('pickup_fee', $location?->pickup_fee ?? 0) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
            <p class="mt-1 text-xs text-gray-500">تُضاف مرة واحدة عند اختيار هذا الفرع للاستلام.</p>
        </div>
        <div>
            <label for="one_way_fee" class="block text-sm font-semibold mb-2">رسوم التسليم في موقع مختلف (د.أ)</label>
            <input type="number" step="0.01" min="0" id="one_way_fee" name="one_way_fee"
                   value="{{ old('one_way_fee', $location?->one_way_fee ?? 0) }}"
                   class="w-full rounded-lg border-gray-300 text-sm">
            <p class="mt-1 text-xs text-gray-500">تُضاف عندما يُسلَّم في هذا الفرع بعد استلامه من فرع آخر.</p>
        </div>
    </div>

    {{-- Hours --}}
    <div x-data="{ is24h: {{ old('is_24h', $location?->is_24h) ? 'true' : 'false' }} }"
         class="pt-5 border-t border-gray-100">
        <label class="flex items-center gap-3 cursor-pointer mb-4">
            <input type="checkbox" name="is_24h" value="1" x-model="is24h" class="rounded border-gray-300 text-brand">
            <span class="text-sm font-semibold">مفتوح ٢٤ ساعة</span>
        </label>

        <div class="grid grid-cols-2 gap-5 max-w-md" x-show="!is24h" x-cloak>
            <div>
                <label for="opens_at" class="block text-sm font-semibold mb-2">وقت الفتح</label>
                <input type="time" id="opens_at" name="opens_at"
                       value="{{ old('opens_at', $location?->opens_at ? substr((string) $location->opens_at, 0, 5) : '08:00') }}"
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label for="closes_at" class="block text-sm font-semibold mb-2">وقت الإغلاق</label>
                <input type="time" id="closes_at" name="closes_at"
                       value="{{ old('closes_at', $location?->closes_at ? substr((string) $location->closes_at, 0, 5) : '22:00') }}"
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
        </div>
    </div>

    {{-- Map + flags --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-gray-100">
        <div>
            <label for="latitude" class="block text-sm font-semibold mb-2">خط العرض</label>
            <input type="number" step="0.0000001" id="latitude" name="latitude" dir="ltr"
                   value="{{ old('latitude', $location?->latitude) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="longitude" class="block text-sm font-semibold mb-2">خط الطول</label>
            <input type="number" step="0.0000001" id="longitude" name="longitude" dir="ltr"
                   value="{{ old('longitude', $location?->longitude) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label for="sort_order" class="block text-sm font-semibold mb-2">ترتيب العرض</label>
            <input type="number" min="0" id="sort_order" name="sort_order"
                   value="{{ old('sort_order', $location?->sort_order ?? 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>

    <label class="flex items-center gap-3 cursor-pointer pt-5 border-t border-gray-100">
        <input type="checkbox" name="is_active" value="1"
               @checked(old('is_active', $location?->is_active ?? true)) class="rounded border-gray-300 text-brand">
        <span class="text-sm font-semibold">مفعّل</span>
    </label>

    <div class="flex gap-3 pt-5 border-t border-gray-100">
        <button type="submit" class="bg-brand text-white font-bold px-8 py-3 rounded-lg hover:opacity-90 transition">
            <i class="fa-solid fa-floppy-disk"></i> حفظ
        </button>
        <a href="{{ route('admin.locations.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm">إلغاء</a>
    </div>
</div>
