@php
    /** @var \App\Models\Vehicle|null $vehicle */
    $vehicle  = $vehicle ?? null;
    $features = old('features', $vehicle?->features ?? []);
@endphp

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ══ MAIN ════════════════════════════════════════════════════════════ --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Identity --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-lg mb-5"><i class="fa-solid fa-car text-brand"></i> بيانات السيارة</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="brand" class="block text-sm font-semibold mb-2">الماركة <span class="text-red-500">*</span></label>
                    <input type="text" id="brand" name="brand" required value="{{ old('brand', $vehicle?->brand) }}"
                           placeholder="Toyota" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="model" class="block text-sm font-semibold mb-2">الموديل <span class="text-red-500">*</span></label>
                    <input type="text" id="model" name="model" required value="{{ old('model', $vehicle?->model) }}"
                           placeholder="Camry" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="year" class="block text-sm font-semibold mb-2">سنة الصنع <span class="text-red-500">*</span></label>
                    <input type="number" id="year" name="year" required min="1980" max="{{ date('Y') + 2 }}"
                           value="{{ old('year', $vehicle?->year ?? date('Y')) }}"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="color" class="block text-sm font-semibold mb-2">اللون</label>
                    <input type="text" id="color" name="color" value="{{ old('color', $vehicle?->color) }}"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="registration_number" class="block text-sm font-semibold mb-2">رقم اللوحة</label>
                    <input type="text" id="registration_number" name="registration_number" dir="ltr"
                           value="{{ old('registration_number', $vehicle?->registration_number) }}"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="vehicle_category_id" class="block text-sm font-semibold mb-2">الفئة</label>
                    <select id="vehicle_category_id" name="vehicle_category_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— بدون —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected(old('vehicle_category_id', $vehicle?->vehicle_category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                <div>
                    <label for="desc_ar" class="block text-sm font-semibold mb-2">الوصف (عربي)</label>
                    <textarea id="desc_ar" name="description[ar]" rows="3"
                              class="w-full rounded-lg border-gray-300 text-sm">{{ old('description.ar', $vehicle?->getTranslation('description', 'ar', false)) }}</textarea>
                </div>
                <div>
                    <label for="desc_en" class="block text-sm font-semibold mb-2">الوصف (إنجليزي)</label>
                    <textarea id="desc_en" name="description[en]" rows="3" dir="ltr"
                              class="w-full rounded-lg border-gray-300 text-sm">{{ old('description.en', $vehicle?->getTranslation('description', 'en', false)) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Specs --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-lg mb-5"><i class="fa-solid fa-gears text-brand"></i> المواصفات</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                <div>
                    <label for="transmission" class="block text-sm font-semibold mb-2">ناقل الحركة <span class="text-red-500">*</span></label>
                    <select id="transmission" name="transmission" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($transmissions as $key => $t)
                            <option value="{{ $key }}" @selected(old('transmission', $vehicle?->transmission ?? 'automatic') === $key)>
                                {{ $t['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="fuel_type" class="block text-sm font-semibold mb-2">الوقود <span class="text-red-500">*</span></label>
                    <select id="fuel_type" name="fuel_type" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($fuelTypes as $key => $f)
                            <option value="{{ $key }}" @selected(old('fuel_type', $vehicle?->fuel_type ?? 'petrol') === $key)>
                                {{ $f['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @foreach([
                    ['seats', 'عدد المقاعد', $vehicle?->seats ?? 5],
                    ['doors', 'عدد الأبواب', $vehicle?->doors ?? 4],
                    ['bags',  'عدد الحقائب', $vehicle?->bags ?? 2],
                    ['engine_cc', 'سعة المحرك (cc)', $vehicle?->engine_cc],
                    ['mileage_limit_per_day', 'الحد اليومي (كم)', $vehicle?->mileage_limit_per_day],
                ] as [$name, $label, $default])
                    <div>
                        <label for="{{ $name }}" class="block text-sm font-semibold mb-2">{{ $label }}</label>
                        <input type="number" id="{{ $name }}" name="{{ $name }}" min="0"
                               value="{{ old($name, $default) }}" class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                @endforeach
            </div>

            {{-- Dynamic features --}}
            <div class="mt-6" x-data="{ features: {{ json_encode(array_values($features) ?: ['']) }} }">
                <label class="block text-sm font-semibold mb-2">المميزات</label>
                <template x-for="(feature, index) in features" :key="index">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="features[]" x-model="features[index]"
                               placeholder="مثال: Bluetooth" class="flex-1 rounded-lg border-gray-300 text-sm">
                        <button type="button" @click="features.splice(index, 1)"
                                class="w-10 h-10 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 shrink-0">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                    </div>
                </template>
                <button type="button" @click="features.push('')"
                        class="mt-1 text-sm font-semibold text-brand hover:underline">
                    <i class="fa-solid fa-plus"></i> إضافة ميزة
                </button>
            </div>
        </section>

        {{-- Pricing --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-lg mb-5"><i class="fa-solid fa-tags text-brand"></i> الأسعار</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                @foreach([
                    ['daily_rate',          'السعر اليومي (ر.س)', true,  $vehicle?->daily_rate],
                    ['discount_daily_rate', 'سعر العرض اليومي',   false, $vehicle?->discount_daily_rate],
                    ['weekly_rate',         'السعر الأسبوعي',     false, $vehicle?->weekly_rate],
                    ['monthly_rate',        'السعر الشهري',       false, $vehicle?->monthly_rate],
                    ['security_deposit',    'مبلغ التأمين',       false, $vehicle?->security_deposit],
                ] as [$name, $label, $required, $default])
                    <div>
                        <label for="{{ $name }}" class="block text-sm font-semibold mb-2">
                            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
                        </label>
                        <input type="number" step="0.01" min="0" id="{{ $name }}" name="{{ $name }}"
                               @required($required) value="{{ old($name, $default) }}"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                @endforeach
            </div>

            <p class="mt-4 text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                <i class="fa-solid fa-circle-info text-brand"></i>
                السعر الأسبوعي يُطبَّق تلقائياً على الحجوزات من ٧ أيام فأكثر، والشهري من ٣٠ يوماً فأكثر.
            </p>
        </section>

        {{-- Images --}}
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-lg mb-5"><i class="fa-solid fa-image text-brand"></i> الصور</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="main_image" class="block text-sm font-semibold mb-2">الصورة الرئيسية</label>
                    @if($vehicle?->main_image_url)
                        <img src="{{ $vehicle->main_image_url }}" alt=""
                             class="w-full h-32 object-contain bg-gray-50 rounded-lg mb-2 border border-gray-200">
                    @endif
                    <input type="file" id="main_image" name="main_image" accept="image/*"
                           class="w-full text-sm file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:bg-brand file:text-white file:text-sm file:font-semibold">
                </div>
                <div>
                    <label for="gallery_images" class="block text-sm font-semibold mb-2">معرض الصور (حتى ١٠)</label>
                    @if($vehicle && count($vehicle->gallery_urls))
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($vehicle->gallery_urls as $url)
                                <img src="{{ $url }}" alt="" class="w-16 h-12 object-contain bg-gray-50 rounded border border-gray-200">
                            @endforeach
                        </div>
                    @endif
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple
                           class="w-full text-sm file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:bg-gray-200 file:text-sm file:font-semibold">
                </div>
            </div>
        </section>
    </div>

    {{-- ══ SIDEBAR ═════════════════════════════════════════════════════════ --}}
    <div class="space-y-6">
        <section class="bg-white border border-gray-200 rounded-xl p-6">
            <h2 class="font-bold text-lg mb-5">الإتاحة</h2>

            <div class="space-y-5">
                <div>
                    <label for="availability_status" class="block text-sm font-semibold mb-2">الحالة <span class="text-red-500">*</span></label>
                    <select id="availability_status" name="availability_status" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}"
                                @selected(old('availability_status', $vehicle?->availability_status ?? 'available') === $key)>
                                {{ $status['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="units_available" class="block text-sm font-semibold mb-2">عدد الوحدات <span class="text-red-500">*</span></label>
                    <input type="number" id="units_available" name="units_available" required min="0"
                           value="{{ old('units_available', $vehicle?->units_available ?? 1) }}"
                           class="w-full rounded-lg border-gray-300 text-sm">
                    <p class="mt-1 text-xs text-gray-500">كم سيارة من هذا الطراز يمكن حجزها في نفس الوقت.</p>
                </div>

                <div>
                    <label for="rental_location_id" class="block text-sm font-semibold mb-2">الفرع</label>
                    <select id="rental_location_id" name="rental_location_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">— بدون —</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                                @selected(old('rental_location_id', $vehicle?->rental_location_id) == $location->id)>
                                {{ $location->display_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="min_driver_age" class="block text-sm font-semibold mb-2">أقل عمر</label>
                        <input type="number" id="min_driver_age" name="min_driver_age" required min="16" max="99"
                               value="{{ old('min_driver_age', $vehicle?->min_driver_age ?? 21) }}"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label for="min_rental_days" class="block text-sm font-semibold mb-2">أقل مدة (يوم)</label>
                        <input type="number" id="min_rental_days" name="min_rental_days" required min="1"
                               value="{{ old('min_rental_days', $vehicle?->min_rental_days ?? 1) }}"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-semibold mb-2">ترتيب العرض</label>
                    <input type="number" id="sort_order" name="sort_order" min="0"
                           value="{{ old('sort_order', $vehicle?->sort_order ?? 0) }}"
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>

                <div class="space-y-3 pt-2 border-t border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $vehicle?->is_active ?? true))
                               class="rounded border-gray-300 text-brand">
                        <span class="text-sm font-semibold">مفعّلة</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1"
                               @checked(old('is_featured', $vehicle?->is_featured ?? false))
                               class="rounded border-gray-300 text-brand">
                        <span class="text-sm font-semibold">مميزة (تظهر في الرئيسية)</span>
                    </label>
                </div>
            </div>
        </section>

        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-brand text-white font-bold py-3 rounded-lg hover:opacity-90 transition">
                <i class="fa-solid fa-floppy-disk"></i> حفظ
            </button>
            <a href="{{ route('admin.vehicles.index') }}"
               class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-sm">إلغاء</a>
        </div>
    </div>
</div>
