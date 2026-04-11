{{-- resources/views/cart/checkout.blade.php --}}

@extends('layouts.app')
@section('title', 'إتمام الطلب')

@push('head')
<style>
    .form-input {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 14px;
        background: #f9fafb;
        color: #111827;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        outline: none;
    }
    .form-input:focus {
        background: #fff;
        border-color: var(--brand-color, #0ea5e9);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color, #0ea5e9) 15%, transparent);
    }
    .form-input.error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .step-badge {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: var(--brand-color, #0ea5e9);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" dir="rtl">

    {{-- Page title + breadcrumb --}}
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            <a href="{{ route('cart.index') }}" class="hover:text-brand-600 transition-colors">السلة</a>
            <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">إتمام الطلب</span>
        </nav>
        <h1 class="font-display text-3xl font-bold text-gray-900">إتمام الطلب</h1>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <ul class="text-red-600 text-sm space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- ════════════════════════════════════════
             LEFT COLUMN: Steps 1 + 2
        ════════════════════════════════════════ --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- ── STEP 1: Cart Review ─────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                    <div class="step-badge">١</div>
                    <h2 class="font-bold text-gray-900">مراجعة المنتجات</h2>
                    <a href="{{ route('cart.index') }}"
                       class="mr-auto text-xs text-brand-600 hover:underline font-medium transition-colors">
                        تعديل السلة
                    </a>
                </div>

                <div class="divide-y divide-gray-50">
                    @foreach($summary['items'] as $itemKey => $item)
                    <div class="flex items-center gap-4 px-6 py-4">
                        {{-- Image --}}
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                            <img src="{{ $item['image'] ?? 'https://picsum.photos/seed/'.$loop->index.'/100/100' }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-full h-full object-cover">
                        </div>

                        {{-- Name + variant --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 line-clamp-1">
                                {{ $item['name'] }}
                            </p>
                            @if(!empty($item['variant_name']))
                            <p class="text-xs text-brand-600 font-medium mt-0.5">
                                {{ $item['variant_name'] }}
                            </p>
                            @endif
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}
                            </p>
                        </div>

                        {{-- Line total --}}
                        <p class="text-sm font-bold text-gray-900 flex-shrink-0">
                            ${{ number_format($item['subtotal'], 2) }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── STEP 2: Shipping Form ───────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                    <div class="step-badge">٢</div>
                    <h2 class="font-bold text-gray-900">بيانات الشحن</h2>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Full Name --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            الاسم الكامل <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="shipping_name"
                            value="{{ old('shipping_name', $user->name ?? '') }}"
                            placeholder="محمد أحمد"
                            required
                            class="form-input @error('shipping_name') error @enderror"
                        >
                        @error('shipping_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            name="shipping_phone"
                            value="{{ old('shipping_phone', $user->phone ?? '') }}"
                            placeholder="05XXXXXXXX"
                            dir="ltr"
                            required
                            class="form-input text-left @error('shipping_phone') error @enderror"
                        >
                        @error('shipping_phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            عنوان التوصيل <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="shipping_address"
                            value="{{ old('shipping_address') }}"
                            placeholder="الشارع، الحي، رقم البناء"
                            required
                            class="form-input @error('shipping_address') error @enderror"
                        >
                        @error('shipping_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            المدينة <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="shipping_city"
                            value="{{ old('shipping_city') }}"
                            placeholder="الرياض"
                            required
                            class="form-input @error('shipping_city') error @enderror"
                        >
                        @error('shipping_city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ZIP --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            الرمز البريدي
                            <span class="text-gray-400 font-normal text-xs">اختياري</span>
                        </label>
                        <input
                            type="text"
                            name="shipping_zip"
                            value="{{ old('shipping_zip') }}"
                            placeholder="12345"
                            dir="ltr"
                            class="form-input text-left"
                        >
                    </div>

                    {{-- Notes --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            ملاحظات إضافية
                            <span class="text-gray-400 font-normal text-xs">اختياري</span>
                        </label>
                        <textarea
                            name="notes"
                            rows="2"
                            placeholder="أي تعليمات خاصة للتوصيل..."
                            class="form-input resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ── STEP 3: Payment Method ──────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
                    <div class="step-badge">٣</div>
                    <h2 class="font-bold text-gray-900">طريقة الدفع</h2>
                </div>

                <div class="p-6">
                    {{-- COD only --}}
                    <label class="flex items-center gap-4 p-4 border-2 border-brand-600 rounded-xl bg-brand-50/40 cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" checked
                               class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-600/30">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">الدفع عند الاستلام</p>
                                <p class="text-xs text-gray-500 mt-0.5">ادفع نقداً عند وصول طلبك</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-brand-600 bg-brand-100 px-2 py-1 rounded-lg">
                            متاح
                        </span>
                    </label>
                </div>
            </div>

        </div>

        {{-- ════════════════════════════════════════
             RIGHT COLUMN: Order Summary + Submit
        ════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-20">

                <h2 class="font-bold text-gray-900 text-lg mb-5">ملخص الطلب</h2>

                {{-- Line items summary --}}
                <div class="space-y-2 mb-4">
                    @foreach($summary['items'] as $item)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex-1 line-clamp-1 ml-2">
                            {{ $item['name'] }}
                            @if(!empty($item['variant_name']))
                                <span class="text-gray-400 text-xs">({{ $item['variant_name'] }})</span>
                            @endif
                            <span class="text-gray-400"> × {{ $item['quantity'] }}</span>
                        </span>
                        <span class="font-medium text-gray-900 flex-shrink-0">
                            ${{ number_format($item['subtotal'], 2) }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="space-y-2.5 text-sm border-t border-gray-100 pt-4 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">المجموع الفرعي</span>
                        <span class="font-medium">${{ number_format($summary['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الضريبة (10%)</span>
                        <span class="font-medium">${{ number_format($summary['tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الشحن</span>
                        <span class="font-medium {{ $summary['shipping'] == 0 ? 'text-green-600' : '' }}">
                            {{ $summary['shipping'] == 0 ? 'مجاني 🎉' : '$' . number_format($summary['shipping'], 2) }}
                        </span>
                    </div>
                </div>

                {{-- Grand total --}}
                <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-6">
                    <span class="font-bold text-gray-900 text-base">الإجمالي</span>
                    <span class="font-bold text-2xl text-gray-900">
                        ${{ number_format($summary['total'], 2) }}
                    </span>
                </div>

                {{-- COD reminder --}}
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-5">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-amber-700 font-medium">الدفع عند الاستلام — ستدفع نقداً عند وصول الطلب</p>
                </div>

                {{-- Place order button --}}
                <button
                    type="submit"
                    id="place-order-btn"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 rounded-xl
                           transition-all active:scale-95 shadow-lg shadow-brand-600/20
                           flex items-center justify-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تأكيد الطلب
                </button>

                <p class="text-center text-xs text-gray-400 mt-3">
                    بالضغط على "تأكيد الطلب" فأنت توافق على شروط الاستخدام
                </p>

            </div>
        </div>

    </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
// Prevent double-submit
document.getElementById('checkout-form')?.addEventListener('submit', function () {
    const btn  = document.getElementById('place-order-btn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        جارٍ تأكيد الطلب...`;
});
</script>
@endpush