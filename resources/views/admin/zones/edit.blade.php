@extends('layouts.admin')
@section('title', 'تعديل منطقة — ' . $zone->name)

@section('admin-content')
<div class="max-w-xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.countries.zones.index', $country) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            مناطق {{ $country->name }}
        </a>
    </div>

    {{-- Country context pill --}}
    <div class="flex items-center gap-2 mb-5">
        <span class="text-xs font-bold text-gray-400">الدولة:</span>
        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
            {{ $country->name }}
            @if($country->calling_code)
            <span class="text-gray-400 font-mono">+{{ $country->calling_code }}</span>
            @endif
        </span>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.zones.update', $zone) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-7 space-y-5">
            <h2 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-4">
                تعديل: {{ $zone->name }}
            </h2>

            {{-- Name + Name EN --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        الاسم بالعربية <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $zone->name) }}"
                           required placeholder="دمشق"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('name') border-red-400 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en"
                           value="{{ old('name_en', $zone->name_en) }}"
                           placeholder="Damascus"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
            </div>

            {{-- ── NEW: Calling Code ── --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    رمز الاتصال للمنطقة
                    <span class="text-gray-400 font-normal text-xs mr-1">(اختياري)</span>
                </label>
                <div class="relative max-w-xs">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 text-sm font-bold pointer-events-none select-none">+</span>
                    <input type="text" name="calling_code"
                           value="{{ old('calling_code', $zone->calling_code) }}"
                           maxlength="10"
                           placeholder="{{ $country->calling_code ?? '963' }}"
                           dir="ltr"
                           inputmode="numeric"
                           class="w-full border border-gray-200 rounded-xl p-3 pl-3 pr-8 bg-gray-50 text-sm font-mono
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all
                                  @error('calling_code') border-red-400 @enderror">
                </div>

                {{-- Show the effective code currently in use --}}
                @php
                    $zone->setRelation('country', $country);
                    $effective = $zone->effectiveCallingCodeFormatted();
                @endphp
                @if($effective)
                <p class="mt-1.5 text-[11px] text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    الرمز الفعلي المستخدم حالياً: <span class="font-bold font-mono text-gray-600">{{ $effective }}</span>
                    @if(!$zone->calling_code)
                    <span class="text-gray-400">(موروث من الدولة)</span>
                    @endif
                </p>
                @endif
                @error('calling_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Sort order + Active --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order"
                           value="{{ old('sort_order', $zone->sort_order) }}"
                           min="0"
                           class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 text-sm
                                  focus:bg-white focus:outline-none focus:ring-2 focus:border-brand transition-all">
                </div>
                <div class="flex items-end pb-0.5">
                    <label class="flex items-center gap-3 cursor-pointer p-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white transition-colors w-full">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $zone->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-brand/30">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">تفعيل المنطقة</p>
                            <p class="text-xs text-gray-400">تظهر في قائمة الشحن</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.countries.zones.index', $country) }}"
               class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors">إلغاء</a>
            <button type="submit"
                    class="bg-brand text-white px-8 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection