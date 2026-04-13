@extends('layouts.admin')
@section('title', 'مناطق الشحن — ' . $country->name)

@section('admin-content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
    <a href="{{ route('admin.countries.index') }}" class="hover:text-brand transition-colors font-semibold">الدول</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    <span class="text-gray-700 font-semibold">{{ $country->name }}</span>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    <span class="text-gray-500">المناطق</span>
</div>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">مناطق {{ $country->name }}</h1>
        <p class="text-gray-500 text-sm mt-1">أضف المدن ومناطق التسليم مع أسعار الشحن الخاصة بكل منطقة.</p>
    </div>
    {{-- JSON API link --}}
    <a href="{{ route('api.shipping.zones', $country) }}" target="_blank"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-brand border border-gray-200 px-3 py-2 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        JSON API
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">

    {{-- ── Add New Zone Form ─────────────────────────────────────────── --}}
    <div class="lg:col-span-2">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sticky top-6">
            <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2 text-sm">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white text-xs font-black"
                     style="background:var(--brand-color)">+</div>
                إضافة منطقة جديدة
            </h3>

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.countries.zones.store', $country) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                        اسم المنطقة / المدينة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           required placeholder="مثال: دمشق"
                           class="w-full border border-gray-200 rounded-xl p-2.5 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">
                            سعر الشحن ($) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="shipping_price" value="{{ old('shipping_price', '0.00') }}"
                               step="0.01" min="0" required
                               class="w-full border border-gray-200 rounded-xl p-2.5 bg-gray-50 text-sm tabular-nums
                                      focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">أيام التسليم</label>
                        <input type="number" name="delivery_days" value="{{ old('delivery_days') }}"
                               min="1" max="365" placeholder="3"
                               class="w-full border border-gray-200 rounded-xl p-2.5 bg-gray-50 text-sm
                                      focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand/30">
                        <span class="text-xs font-semibold text-gray-600">منطقة نشطة</span>
                    </label>
                    <button type="submit"
                            class="bg-brand text-white px-5 py-2 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm">
                        إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Zones List ────────────────────────────────────────────────── --}}
    <div class="lg:col-span-3">
        @if($zones->isEmpty())
        <div class="bg-white border border-dashed border-gray-200 rounded-2xl p-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 font-semibold text-sm">لا توجد مناطق بعد</p>
            <p class="text-gray-400 text-xs mt-1">استخدم النموذج على اليسار لإضافة أول منطقة.</p>
        </div>

        @else
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    {{ $zones->count() }} منطقة
                </span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($zones as $zone)
                <div x-data="{ editing: false }" class="group">

                    {{-- View row --}}
                    <div x-show="!editing" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50/60 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm">{{ $zone->name }}</p>
                            @if($zone->delivery_days)
                            <p class="text-xs text-gray-400 mt-0.5">
                                <svg class="w-3 h-3 inline-block ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $zone->delivery_days }} أيام
                            </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="font-black text-gray-900 tabular-nums text-sm">
                                ${{ number_format($zone->shipping_price, 2) }}
                            </span>
                            @if($zone->is_active)
                            <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0" title="نشطة"></span>
                            @else
                            <span class="w-2 h-2 bg-gray-300 rounded-full flex-shrink-0" title="معطلة"></span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                            <button type="button" @click="editing = true"
                                    class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <form action="{{ route('admin.countries.zones.destroy', [$country, $zone]) }}" method="POST"
                                  onsubmit="return confirm('حذف منطقة \'{{ addslashes($zone->name) }}\'؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Inline edit form --}}
                    <div x-show="editing" x-cloak class="bg-blue-50/40 border-t border-blue-100 px-5 py-4">
                        <form action="{{ route('admin.countries.zones.update', [$country, $zone]) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                                <div class="sm:col-span-2">
                                    <input type="text" name="name" value="{{ $zone->name }}" required
                                           placeholder="اسم المنطقة"
                                           class="w-full border border-gray-200 rounded-xl p-2.5 bg-white text-sm
                                                  focus:outline-none focus:ring-2 focus:border-brand transition-all">
                                </div>
                                <div>
                                    <input type="number" name="shipping_price" value="{{ $zone->shipping_price }}"
                                           step="0.01" min="0" required placeholder="السعر"
                                           class="w-full border border-gray-200 rounded-xl p-2.5 bg-white text-sm tabular-nums
                                                  focus:outline-none focus:ring-2 focus:border-brand transition-all">
                                </div>
                                <div>
                                    <input type="number" name="delivery_days" value="{{ $zone->delivery_days }}"
                                           min="1" placeholder="أيام"
                                           class="w-full border border-gray-200 rounded-xl p-2.5 bg-white text-sm
                                                  focus:outline-none focus:ring-2 focus:border-brand transition-all">
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1"
                                           {{ $zone->is_active ? 'checked' : '' }}
                                           class="w-4 h-4 text-brand border-gray-300 rounded">
                                    <span class="text-xs font-semibold text-gray-600">نشطة</span>
                                </label>
                                <div class="flex gap-2">
                                    <button type="button" @click="editing = false"
                                            class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-red-500 transition-colors">
                                        إلغاء
                                    </button>
                                    <button type="submit"
                                            class="bg-brand text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:opacity-90 transition-all">
                                        حفظ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>
@endsection