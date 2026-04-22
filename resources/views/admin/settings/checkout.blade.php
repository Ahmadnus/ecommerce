@extends('layouts.admin')
@section('title', 'إعدادات الدفع والشراء')

@push('head')
<style>
/* Toggle switch */
.toggle-track {
    width: 52px; height: 28px;
    border-radius: 99px;
    background: #e5e7eb;
    position: relative;
    cursor: pointer;
    transition: background .2s;
    flex-shrink: 0;
}
.toggle-track.on { background: var(--brand-color, #0ea5e9); }
.toggle-thumb {
    position: absolute; top: 3px; left: 3px;
    width: 22px; height: 22px;
    border-radius: 50%; background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
    transition: transform .2s;
}
.toggle-track.on .toggle-thumb { transform: translateX(24px); }

.setting-card {
    background: #fff; border: 1.5px solid #f1f5f9;
    border-radius: 18px; padding: 28px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
</style>
@endpush

@section('admin-content')
<div dir="rtl">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xl">🛒</span>
                <h1 class="text-xl font-black text-gray-900">إعدادات الدفع والشراء</h1>
            </div>
            <p class="text-sm text-gray-400">تحكم في سلوك صفحة الدفع وخيارات الزوار.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-emerald-700 text-sm font-semibold">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.settings.checkout.update') }}" method="POST">
        @csrf

        <div class="setting-card">
            <h2 class="font-bold text-gray-500 mb-6 text-sm uppercase tracking-wider border-b border-gray-100 pb-4">
                خيارات الشراء
            </h2>

            {{-- Guest Checkout Toggle --}}
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <p class="font-bold text-gray-900 text-sm mb-1">الشراء كزائر (Guest Checkout)</p>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-lg">
                        عند التفعيل، يمكن للزوار إضافة منتجات للسلة وإتمام الطلب مباشرةً بدون إنشاء حساب أو تسجيل دخول.
                        عند التعطيل، يُشترط تسجيل الدخول قبل الوصول لصفحة الدفع.
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full
                              {{ $guestEnabled ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $guestEnabled ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $guestEnabled ? 'مفعّل حالياً' : 'معطّل حالياً' }}
                        </span>
                    </div>
                </div>

                {{-- Visual toggle (cosmetic — the hidden checkbox does the real work) --}}
                <div x-data="{ on: {{ $guestEnabled ? 'true' : 'false' }} }"
                     class="flex-shrink-0 pt-0.5">
                    <label class="cursor-pointer flex items-center gap-3 select-none">
                        {{-- Hidden real checkbox submitted to Laravel --}}
                        <input type="checkbox" name="guest_checkout_enabled" value="1"
                               {{ $guestEnabled ? 'checked' : '' }}
                               x-ref="chk"
                               class="sr-only">

                        {{-- Visual pill --}}
                        <div class="toggle-track" :class="on ? 'on' : ''"
                             @click="on = !on; $refs.chk.checked = on">
                            <div class="toggle-thumb"></div>
                        </div>

                        <span class="text-sm font-bold" :class="on ? 'text-emerald-600' : 'text-gray-400'"
                              x-text="on ? 'مفعّل' : 'معطّل'"></span>
                    </label>
                </div>
            </div>

            {{-- Explanation boxes --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <p class="text-xs font-bold text-blue-700 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        عند التفعيل
                    </p>
                    <ul class="text-xs text-blue-600 space-y-1 leading-relaxed">
                        <li>• الزوار يمكنهم الشراء مباشرةً</li>
                        <li>• يملأون بيانات التوصيل في صفحة الدفع</li>
                        <li>• الطلب يُحفظ بدون مرتبط بحساب</li>
                        <li>• يمكن إدخال بريد إلكتروني اختياري</li>
                    </ul>
                </div>
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                    <p class="text-xs font-bold text-amber-700 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        عند التعطيل
                    </p>
                    <ul class="text-xs text-amber-600 space-y-1 leading-relaxed">
                        <li>• السلة متاحة للجميع</li>
                        <li>• صفحة الدفع تتطلب تسجيل دخول</li>
                        <li>• الزوار يُعادون لصفحة الدخول</li>
                        <li>• السلة تُحفظ بعد الدخول</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-5">
            <button type="submit"
                    class="inline-flex items-center gap-2 text-white text-sm font-black px-8 py-3
                           rounded-xl shadow-lg hover:opacity-90 hover:scale-[1.02] transition-all active:scale-95"
                    style="background:var(--brand-color,#0ea5e9)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ الإعدادات
            </button>
        </div>
    </form>
</div>
@endsection