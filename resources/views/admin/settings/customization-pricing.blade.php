@extends('layouts.admin')

@section('title', 'أسعار تخصيص الملابس')

@section('admin-content')

<div class="max-w-2xl">

    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">أسعار تخصيص الملابس</h1>
        <p class="text-sm text-gray-500 mt-1">
            تحكم بسعر التيشيرت الأساسي ورسوم الصور والنصوص المُضافة عند التخصيص.
            جميع القيم بالدينار الأردني (JOD) — العملة الأساسية للنظام.
        </p>
    </div>

    @if(session('success'))
    <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.customization-pricing.update') }}"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')

        <div class="divide-y divide-gray-100">

            @foreach($settings as $key => $meta)
            <div class="p-5 flex items-center justify-between gap-4">
                <div class="flex-1">
                    <label for="{{ $key }}" class="text-sm font-semibold text-gray-800">
                        {{ $meta['label'] }}
                    </label>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @if($key === 'customization_tshirt_base_price')
                            السعر الأساسي للتيشيرت قبل أي تخصيص (بدون صور أو نصوص).
                        @elseif($key === 'customization_price_per_image')
                            يُضاف هذا المبلغ مرة واحدة عن كل صورة أو شعار يرفعه العميل.
                        @else
                            يُضاف هذا المبلغ مرة واحدة عن كل نص يكتبه العميل في أي منطقة.
                        @endif
                    </p>
                    @error($key)
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative flex-shrink-0">
                    <input type="number"
                           id="{{ $key }}"
                           name="{{ $key }}"
                           value="{{ old($key, $values[$key]) }}"
                           step="0.01" min="0" max="9999.99"
                           required
                           class="w-32 text-left ltr text-sm font-bold text-gray-900 rounded-xl border
                                  border-gray-200 px-3 py-2.5 pr-12 focus:outline-none focus:ring-2
                                  focus:ring-black focus:border-transparent @error($key) border-red-300 @enderror"
                           dir="ltr">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-bold">
                        JOD
                    </span>
                </div>
            </div>
            @endforeach

        </div>

        <div class="p-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">
                مثال: تيشيرت بصورة واحدة ونص واحد =
                {{ number_format((float)$values['customization_tshirt_base_price'] + (float)$values['customization_price_per_image'] + (float)$values['customization_price_per_text'], 2) }}
                JOD
            </p>
            <button type="submit"
                    class="text-sm font-bold px-6 py-2.5 rounded-xl bg-black text-white hover:bg-gray-800 transition-colors">
                حفظ التغييرات
            </button>
        </div>

    </form>

    <div class="mt-4 flex items-start gap-2 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
        <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-blue-700 leading-relaxed">
            هذه القيم تُحسب بالدينار الأردني دائماً. عند عرض السعر للعميل بعملة أخرى
            (دولار، يورو، إلخ) يقوم النظام بتحويل السعر تلقائياً حسب سعر الصرف الحالي —
            لا حاجة لتعديل أي شيء هنا عند تغيير العملة.
        </p>
    </div>

</div>

@endsection