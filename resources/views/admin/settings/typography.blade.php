@extends('layouts.admin')

@section('title', 'Typography & Text Colors')

@push('styles')
<style>
    .typo-section-title {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        font-weight: 800;
        color: rgb(100 116 139);
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgb(226 232 240);
    }

    .typo-color-input {
        width: 48px;
        height: 44px;
        border-radius: 14px;
        border: 1px solid rgb(226 232 240);
        padding: 3px;
        background: #fff;
        cursor: pointer;
        flex-shrink: 0;
    }

    .typo-swatch {
        width: 12px;
        height: 12px;
        border-radius: 9999px;
        border: 1px solid rgba(0,0,0,.08);
        display: inline-block;
    }
</style>
@endpush

@section('admin-content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white text-slate-900">
    <div class="max-w-6xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="mb-8 flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Typography &amp; Text Colors</h1>
                    <p class="mt-1 text-sm text-slate-500">Control all font sizes and text colors — no code editing needed.</p>
                </div>

                <a href="{{ route('admin.settings') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Settings
                </a>
            </div>

            @if(session('success'))
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.settings.typography.update') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Left --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Font Sizes --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="typo-section-title">Font Sizes</p>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($fontLabels as $key => $label)
                                <div>
                                    <label for="f_{{ $key }}" class="mb-2 block text-sm font-semibold text-slate-700">
                                        {{ $label }}
                                    </label>

                                    <input type="text"
                                           id="f_{{ $key }}"
                                           name="{{ $key }}"
                                           value="{{ $current[$key] ?? $fontKeys[$key] }}"
                                           placeholder="{{ $fontKeys[$key] }}"
                                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                                           autocomplete="off">

                                    <p class="mt-2 text-xs text-slate-500">e.g. 16px · 1rem · 1.2em</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Text Colors --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="typo-section-title">Text Colors</p>

                        <div class="space-y-4">
                            @foreach($colorLabels as $key => $label)
                                @php $val = $current[$key] ?? $colorKeys[$key]; @endphp

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        {{ $label }}
                                    </label>

                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                        <input type="color"
                                               id="cp_{{ $key }}"
                                               value="{{ $val }}"
                                               class="typo-color-input"
                                               oninput="syncText(this,'ct_{{ $key }}','sw_{{ $key }}','sl_{{ $key }}')">

                                        <input type="text"
                                               id="ct_{{ $key }}"
                                               name="{{ $key }}"
                                               value="{{ $val }}"
                                               maxlength="50"
                                               placeholder="{{ $colorKeys[$key] }}"
                                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                                               oninput="syncPicker(this,'cp_{{ $key }}','sw_{{ $key }}','sl_{{ $key }}')"
                                               autocomplete="off">

                                        <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">
                                            <span class="typo-swatch" id="sw_{{ $key }}" style="background:{{ $val }}"></span>
                                            <span id="sl_{{ $key }}">{{ $val }}</span>
                                        </span>
                                    </div>

                                    @error($key)
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right --}}
                <div class="space-y-6 lg:sticky lg:top-6 lg:self-start">

                    {{-- Save --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="mb-4 text-sm font-bold text-slate-900">Save Changes</p>

                        <button type="submit"
                                class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 transition hover:brightness-110 active:scale-[.99]">
                            Save Typography Settings
                        </button>

                        <p class="mt-3 text-center text-xs text-slate-500">
                            Changes apply instantly to the storefront.
                        </p>
                    </div>

                    {{-- Live Preview --}}
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="typo-section-title">Live Preview</p>

                        <div class="space-y-4">
                            <p id="pv-heading"
                               class="border-b border-slate-200 pb-3 font-bold leading-tight text-slate-900"
                               style="font-size:{{ $current['heading_font_size'] }}; color:{{ $current['heading_text_color'] }}">
                                Product Heading
                            </p>

                            <p id="pv-body"
                               class="border-b border-slate-200 pb-3 text-slate-700"
                               style="font-size:{{ $current['base_font_size'] }}; color:{{ $current['body_text_color'] }}">
                                Body text — how paragraphs and descriptions look.
                            </p>

                            <p id="pv-muted"
                               class="border-b border-slate-200 pb-3"
                               style="font-size:{{ $current['card_font_size'] }}; color:{{ $current['muted_text_color'] }}">
                                Muted text — categories, timestamps, hints.
                            </p>

                            <div class="border-b border-slate-200 pb-3">
                                <span id="pv-price"
                                      class="font-black tabular-nums"
                                      style="font-size:{{ $current['product_price_font_size'] }}; color:{{ $current['price_text_color'] }}">
                                    $49.99
                                </span>
                                <span class="ml-3 text-xs text-slate-400 line-through">$79.99</span>
                            </div>

                            <div class="border-b border-slate-200 pb-3">
                                <span id="pv-badge"
                                      class="inline-block rounded-full px-3 py-1 text-xs font-bold"
                                      style="background:var(--brand-color,#364851); color:{{ $current['badge_text_color'] }}; font-size:{{ $current['button_font_size'] }}">
                                    SALE
                                </span>
                            </div>

                            <div>
                                <input id="pv-input"
                                       type="text"
                                       value="Input field example"
                                       readonly
                                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-900 outline-none"
                                       style="color:{{ $current['input_text_color'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function syncText(picker, textId, swId, slId) {
    var h = picker.value;
    document.getElementById(textId).value = h;
    document.getElementById(swId).style.background = h;
    document.getElementById(slId).textContent = h;
    updatePreview();
}

function syncPicker(input, pickerId, swId, slId) {
    var v = input.value.trim();
    document.getElementById(swId).style.background = v;
    document.getElementById(slId).textContent = v;
    if (/^#[0-9a-fA-F]{3,8}$/.test(v)) {
        document.getElementById(pickerId).value = v;
    }
    updatePreview();
}

function get(id) {
    var el = document.getElementById(id);
    return el ? el.value : '';
}

function updatePreview() {
    var map = {
        'pv-heading': { color: 'ct_heading_text_color',   size: 'f_heading_font_size' },
        'pv-body':    { color: 'ct_body_text_color',       size: 'f_base_font_size' },
        'pv-muted':   { color: 'ct_muted_text_color',      size: 'f_card_font_size' },
        'pv-price':   { color: 'ct_price_text_color',      size: 'f_product_price_font_size' },
        'pv-badge':   { color: 'ct_badge_text_color',      size: 'f_button_font_size' },
        'pv-input':   { color: 'ct_input_text_color',      size: null },
    };

    Object.entries(map).forEach(function([id, ids]) {
        var el = document.getElementById(id);
        if (!el) return;
        if (ids.color) el.style.color = get(ids.color);
        if (ids.size) el.style.fontSize = get(ids.size);
    });
}

document.querySelectorAll('input[id^="f_"], input[id^="ct_"]').forEach(function(el) {
    el.addEventListener('input', updatePreview);
});
</script>
@endpush