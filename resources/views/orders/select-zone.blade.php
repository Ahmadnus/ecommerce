{{--
    resources/views/orders/select-zone.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Post-checkout zone selection page.

    Flow:
      1. User fills checkout form (name/address/payment)
      2. Order is created + stock decremented → redirect here
      3. User picks country + delivery zone
      4. POST confirmZone() → order updated → redirect to success.blade.php

    Variables:
      $order     — the freshly created Order (zone_id still null)
      $countries — Country collection with eager-loaded activeZones

    $activeCurrency shared by ResolveCurrency middleware.
    ─────────────────────────────────────────────────────────────────────────────
--}}
@extends('layouts.app')
@section('title', 'اختر منطقة التوصيل')

@push('head')
<style>
/* ── Field base ─────────────────────────────────────────────────────────── */
.field {
    width:100%; padding:13px 15px;
    border:1.5px solid #e5e3df; border-radius:14px;
    background:#faf9f7; font-size:13.5px; color:#1a1917;
    font-family:inherit; outline:none;
    transition:border-color .18s, box-shadow .18s, background .18s;
}
.field::placeholder { color:#b5b2ab; }
.field:focus {
    background:#fff;
    border-color:var(--brand-color,#0ea5e9);
    box-shadow:0 0 0 3px color-mix(in srgb, var(--brand-color,#0ea5e9) 12%, transparent);
}
.field.has-error { border-color:#ef4444; background:#fef9f9; }

/* ── Zone cards ─────────────────────────────────────────────────────────── */
.zone-option {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 16px;
    border:1.5px solid #e5e3df; border-radius:12px;
    cursor:pointer; transition:all .15s; background:#faf9f7;
}
.zone-option:has(input:checked) {
    border-color:var(--brand-color,#0ea5e9);
    background:color-mix(in srgb, var(--brand-color,#0ea5e9) 5%, #fff);
}
.zone-option:hover { border-color:var(--brand-color,#0ea5e9); }
.zone-radio-ring {
    width:22px; height:22px; border-radius:50%;
    border:2px solid #d1cdc7; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    transition:border-color .15s;
}
.zone-radio-dot {
    width:10px; height:10px; border-radius:50%;
    background:transparent; transition:background .15s;
}

/* ── Spinner ────────────────────────────────────────────────────────────── */
.zones-loading { display:none; }
.zones-loading.active { display:flex; }

/* ── Entrance animations ────────────────────────────────────────────────── */
@keyframes up {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.u1 { animation:up .35s ease .05s both; }
.u2 { animation:up .35s ease .15s both; }
.u3 { animation:up .35s ease .25s both; }
</style>
@endpush

@section('content')

@php
    $cur  = $activeCurrency;
    $rate = (float) $cur->exchange_rate;
    $sym  = $cur->symbol;
    $cv   = fn(float $jod): string => number_format(round($jod * $rate, 2), 2);
@endphp

<script>
window.ZONE_PAGE = {
    subtotalJod:  {{ (float) $order->subtotal }},
    rate:         {{ $rate }},
    symbol:       '{{ $sym }}',
    zonesApiBase: '{{ url('/api/shipping/zones') }}',
};
</script>

<div class="min-h-screen bg-[#f7f6f3]" dir="rtl">
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10 lg:py-14">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="u1 text-center mb-8">
        {{-- Step indicator --}}
        <div class="inline-flex items-center gap-2 bg-white border border-[#ece9e4] rounded-full px-4 py-2 shadow-sm mb-5">
            <span class="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
            <span class="text-xs font-semibold text-[#6b6966]">تم استلام الطلب</span>
            <span class="w-8 h-px bg-[#e5e3df]"></span>
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black text-white"
                  style="background:var(--brand-color,#0ea5e9)">٢</span>
            <span class="text-xs font-bold text-[#1a1917]">منطقة التوصيل</span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-[#1a1917] tracking-tight mb-2">
            أين نوصّل طلبك؟
        </h1>
        <p class="text-sm text-[#9a9793] max-w-sm mx-auto leading-relaxed">
            الطلب <span class="font-semibold text-[#1a1917]">{{ $order->order_number }}</span>
            جاهز — اختر منطقتك لحساب رسوم التوصيل وتأكيد الطلب نهائياً.
        </p>
    </div>

    {{-- ── Validation errors ───────────────────────────────────────────────── --}}
    @if($errors->any())
    <div class="u1 mb-5 flex items-start gap-3 bg-red-50 border border-red-100 rounded-2xl px-5 py-4">
        <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <ul class="text-red-600 text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ── Order summary strip ─────────────────────────────────────────────── --}}
    <div class="u2 bg-white border border-[#ece9e4] rounded-2xl p-5 mb-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#9a9793] mb-3">ملخص طلبك</p>
        <div class="divide-y divide-[#f7f6f3]">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-2.5">
                @php $img = $item->product?->getFirstMediaUrl('products'); @endphp
                @if($img)
                <div class="w-9 h-9 rounded-lg overflow-hidden bg-[#f7f6f3] flex-shrink-0">
                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="">
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1a1917] line-clamp-1">{{ $item->product_name }}</p>
                    {{-- Variant attributes ─────────────────────────────────── --}}
                    @if($item->productVariant?->attributeValues->isNotEmpty())
                    <p class="text-[10px] text-[#9a9793] mt-0.5 flex items-center gap-1 flex-wrap">
                        @foreach($item->productVariant->attributeValues as $av)
                            @if($av->color_hex)
                            <span class="inline-block w-3 h-3 rounded-full border border-white shadow-sm flex-shrink-0"
                                  style="background:{{ $av->color_hex }}"></span>
                            @endif
                            <span>{{ $av->attribute->name }}: {{ $av->display_label }}</span>
                            @if(! $loop->last)<span class="text-[#d1cdc7]">·</span>@endif
                        @endforeach
                    </p>
                    @endif
                    <p class="text-[10px] text-[#b5b2ab] mt-0.5">× {{ $item->quantity }}</p>
                </div>
                <p class="text-sm font-bold text-[#1a1917] tabular-nums flex-shrink-0">
                    {{ $cv($item->total_price) }} {{ $sym }}
                </p>
            </div>
            @endforeach
        </div>
        <div class="flex justify-between items-center pt-3 mt-1 border-t border-[#f0ede8]">
            <span class="text-xs text-[#9a9793]">المجموع الفرعي</span>
            <span class="text-sm font-bold text-[#1a1917] tabular-nums">
                {{ $cv($order->subtotal) }} {{ $sym }}
            </span>
        </div>
    </div>

    {{-- ── Zone selection form ─────────────────────────────────────────────── --}}
    <div class="u3 bg-white border border-[#ece9e4] rounded-2xl overflow-hidden shadow-sm">

        <div class="flex items-center gap-3 px-5 py-4 border-b border-[#f0ede8]">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:color-mix(in srgb,var(--brand-color,#0ea5e9) 12%,#fff)">
                <svg class="w-4 h-4" style="color:var(--brand-color,#0ea5e9)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-[#1a1917]">منطقة التوصيل</h2>
                <p class="text-[10px] text-[#9a9793] mt-0.5">اختر دولتك ثم المنطقة الأقرب إليك</p>
            </div>
        </div>

        <form action="{{ route('checkout.confirm-zone') }}" method="POST" id="zone-form">
        @csrf

        <div class="p-5 space-y-4">

            {{-- Country selector --}}
            <div>
                <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                    الدولة <span class="text-red-400 normal-case">*</span>
                </label>
                <select name="country_id" id="country-select"
                        class="field @error('country_id') has-error @enderror"
                        onchange="ZoneSelect.load(this.value)">
                    <option value="">اختر الدولة...</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}"
                            {{ old('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                        @if($country->name_en) ({{ $country->name_en }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('country_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Zone cards (appear after country is chosen) --}}
            <div id="zone-wrapper" class="{{ old('country_id') ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-[#6b6966] mb-2 uppercase tracking-wide">
                    المنطقة / المدينة <span class="text-red-400 normal-case">*</span>
                </label>

                {{-- Spinner --}}
                <div id="zones-loading" class="zones-loading items-center gap-2 text-sm text-[#9a9793] py-3">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    جاري تحميل المناطق...
                </div>

                {{-- Zone card list --}}
                <div id="zones-container" class="space-y-2"></div>

                {{-- Hidden inputs submitted with the form --}}
                <input type="hidden" name="zone_id" id="zone-id-input" value="{{ old('zone_id') }}">

                @error('zone_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Live cost preview (visible once zone is chosen) --}}
            <div id="cost-preview" class="hidden border-t border-[#f0ede8] pt-4 space-y-2.5 text-xs">
                <div class="flex justify-between text-[#9a9793]">
                    <span>المجموع الفرعي</span>
                    <span class="font-semibold text-[#1a1917] tabular-nums">
                        {{ $cv($order->subtotal) }} {{ $sym }}
                    </span>
                </div>
                <div class="flex justify-between text-[#9a9793]">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        رسوم التوصيل
                        <span id="preview-zone-name" class="text-[#b5b2ab]"></span>
                    </span>
                    <span id="preview-delivery" class="font-semibold tabular-nums text-[#1a1917]">—</span>
                </div>
                <div class="flex justify-between items-center border-t border-[#f0ede8] pt-2.5">
                    <span class="font-bold text-[#1a1917] text-sm">الإجمالي الكلي</span>
                    <span id="preview-total" class="text-xl font-black text-[#1a1917] tabular-nums">—</span>
                </div>
                <div id="preview-days-badge" class="hidden flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-3.5 py-2.5">
                    <svg class="w-3.5 h-3.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-blue-700 font-medium" id="preview-days-text"></p>
                </div>
            </div>

        </div>

        {{-- Submit button --}}
        <div class="px-5 pb-5">
            <button type="submit"
                    id="confirm-btn"
                    class="w-full py-4 rounded-xl text-white font-bold text-sm tracking-wide
                           flex items-center justify-center gap-2
                           shadow-lg shadow-black/15 active:scale-[.98]
                           disabled:opacity-40 disabled:cursor-not-allowed
                           transition-all"
                    style="background:var(--brand-color,#0ea5e9)"
                    disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                تأكيد الطلب نهائياً
            </button>
            <p id="btn-hint" class="text-center text-[10px] text-[#b5b2ab] mt-2">
                اختر منطقة التوصيل أولاً
            </p>
        </div>

        </form>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
/*
 * ZoneSelect — handles country change, AJAX zone fetch, cost preview updates.
 * Same pattern as the Shipping object in checkout.blade.php.
 */
var ZoneSelect = {

    fmt: function(jod) {
        var val = Math.round((jod || 0) * window.ZONE_PAGE.rate * 100) / 100;
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }).format(val) + ' ' + window.ZONE_PAGE.symbol;
    },

    load: async function(countryId) {
        var wrapper   = document.getElementById('zone-wrapper');
        var container = document.getElementById('zones-container');
        var spinner   = document.getElementById('zones-loading');
        var input     = document.getElementById('zone-id-input');

        input.value = '';
        this.updatePreview(null, null, null);

        if (!countryId) { wrapper.classList.add('hidden'); return; }

        wrapper.classList.remove('hidden');
        spinner.classList.add('active');
        container.innerHTML = '';

        try {
            var res  = await fetch(window.ZONE_PAGE.zonesApiBase + '/' + countryId, {
                headers: { 'Accept': 'application/json' }
            });
            var data = await res.json();
            var zones = data.zones || [];

            spinner.classList.remove('active');

            if (!zones.length) {
                container.innerHTML =
                    '<p class="text-sm text-[#9a9793] text-center py-3">لا توجد مناطق توصيل متاحة لهذه الدولة.</p>';
                return;
            }

            zones.forEach(function(zone) {
                var label = document.createElement('label');
                label.className = 'zone-option';
                label.innerHTML =
                    '<input type="radio" name="_zone_radio" value="' + zone.id + '" ' +
                    'class="sr-only" data-id="' + zone.id + '" data-price="' + zone.shipping_price + '" ' +
                    'data-days="' + (zone.delivery_days ?? '') + '" data-name="' + zone.name + '" ' +
                    'onchange="ZoneSelect.select(this)">' +
                    '<div class="flex items-center gap-3 flex-1">' +
                    '<div class="zone-radio-ring"><div class="zone-radio-dot"></div></div>' +
                    '<div>' +
                    '<p class="text-sm font-semibold text-[#1a1917]">' + zone.name + '</p>' +
                    (zone.delivery_days
                        ? '<p class="text-[10px] text-[#9a9793] mt-0.5">التوصيل خلال ' + zone.delivery_days + ' أيام عمل</p>'
                        : '') +
                    '</div></div>' +
                    '<div class="flex items-center gap-2 flex-shrink-0">' +
                    '<span class="text-sm font-black tabular-nums text-[#1a1917]">' + ZoneSelect.fmt(zone.shipping_price) + '</span>' +
                    (parseFloat(zone.shipping_price) === 0
                        ? '<span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">مجاني</span>'
                        : '') +
                    '</div>';
                container.appendChild(label);
            });

            /* Restore old() selection after validation failure */
            var oldZone = '{{ old("zone_id") }}';
            if (oldZone) {
                var radio = container.querySelector('input[data-id="' + oldZone + '"]');
                if (radio) { radio.checked = true; this.select(radio); }
            }

        } catch(e) {
            spinner.classList.remove('active');
            container.innerHTML =
                '<p class="text-sm text-red-500 text-center py-3">تعذّر تحميل المناطق. يرجى المحاولة مجدداً.</p>';
        }
    },

    select: function(radio) {
        var zoneId    = radio.dataset.id;
        var priceJod  = parseFloat(radio.dataset.price);
        var days      = radio.dataset.days;
        var name      = radio.dataset.name;

        document.getElementById('zone-id-input').value = zoneId;

        /* Update radio ring styles */
        document.querySelectorAll('.zone-radio-ring').forEach(function(r) {
            r.style.borderColor = '#d1cdc7';
        });
        document.querySelectorAll('.zone-radio-dot').forEach(function(d) {
            d.style.background = 'transparent';
        });
        var ring = radio.closest('.zone-option').querySelector('.zone-radio-ring');
        var dot  = radio.closest('.zone-option').querySelector('.zone-radio-dot');
        ring.style.borderColor = 'var(--brand-color,#0ea5e9)';
        dot.style.background   = 'var(--brand-color,#0ea5e9)';

        this.updatePreview(priceJod, days, name);
    },

    updatePreview: function(priceJod, days, zoneName) {
        var preview    = document.getElementById('cost-preview');
        var deliveryEl = document.getElementById('preview-delivery');
        var totalEl    = document.getElementById('preview-total');
        var nameEl     = document.getElementById('preview-zone-name');
        var daysBadge  = document.getElementById('preview-days-badge');
        var daysText   = document.getElementById('preview-days-text');
        var btn        = document.getElementById('confirm-btn');
        var hint       = document.getElementById('btn-hint');

        if (priceJod === null) {
            preview.classList.add('hidden');
            btn.disabled    = true;
            hint.textContent = 'اختر منطقة التوصيل أولاً';
            return;
        }

        var total = window.ZONE_PAGE.subtotalJod + priceJod;

        preview.classList.remove('hidden');
        deliveryEl.textContent = priceJod === 0 ? 'مجاني 🎉' : this.fmt(priceJod);
        deliveryEl.className   = 'font-semibold tabular-nums ' +
            (priceJod === 0 ? 'text-emerald-600' : 'text-[#1a1917]');
        totalEl.textContent    = this.fmt(total);
        nameEl.textContent     = zoneName ? '— ' + zoneName : '';

        if (days) {
            daysBadge.classList.remove('hidden');
            daysText.textContent = 'التوصيل إلى ' + zoneName + ' خلال ' + days + ' أيام عمل';
        } else {
            daysBadge.classList.add('hidden');
        }

        btn.disabled     = false;
        hint.textContent = '';
    }
};

/* On page load: if old() country is set (validation failure), reload zones */
document.addEventListener('DOMContentLoaded', function() {
    var oldCountry = document.getElementById('country-select').value;
    if (oldCountry) ZoneSelect.load(oldCountry);
});

/* Prevent double-submit */
document.getElementById('zone-form').addEventListener('submit', function() {
    var btn = document.getElementById('confirm-btn');
    if (btn.disabled) return false;
    btn.disabled  = true;
    btn.innerHTML =
        '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">' +
        '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>' +
        '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>' +
        ' جاري التأكيد...';
});
</script>
@endpush