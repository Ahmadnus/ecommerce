@extends('layouts.admin')

@section('title', 'Typography & Text Colors')

@push('styles')
<style>
.typo-section-title {
    font-size: 11px; font-weight: 800; letter-spacing: .08em;
    text-transform: uppercase; color: #6b7280;
    padding-bottom: 10px; border-bottom: 1px solid #f3f4f6; margin-bottom: 20px;
}
.typo-field-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 4px;
}
@media(max-width:640px){ .typo-field-grid { grid-template-columns: 1fr; } }

.typo-field label {
    display: block; font-size: 12px; font-weight: 600;
    color: #374151; margin-bottom: 6px;
}
.typo-field .hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }

.typo-font-input {
    width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 8px 12px; font-size: 13px; color: #111827; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.typo-font-input:focus {
    border-color: var(--brand-color, #364851);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--brand-color,#364851) 15%, transparent);
}

.color-row { display: flex; align-items: center; gap: 10px; }
.color-row input[type="color"] {
    width: 42px; height: 36px; border: 1px solid #e5e7eb;
    border-radius: 8px; padding: 2px; cursor: pointer; flex-shrink: 0;
}
.color-row input[type="text"] {
    flex: 1; border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 8px 12px; font-size: 13px; font-family: monospace;
    color: #111827; outline: none; transition: border-color .15s;
}
.color-row input[type="text"]:focus { border-color: var(--brand-color,#364851); }

.swatch-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 8px 3px 5px; border-radius: 99px;
    border: 1px solid #e5e7eb; font-size: 11px; font-weight: 600;
    white-space: nowrap; flex-shrink: 0;
}
.swatch-dot {
    width: 13px; height: 13px; border-radius: 50%;
    border: 1px solid rgba(0,0,0,.08); display: inline-block;
}
</style>
@endpush

@section('admin-content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Typography &amp; Text Colors</h1>
            <p class="text-sm text-gray-500 mt-1">Control all font sizes and text colors — no code editing needed.</p>
        </div>
        <a href="{{ route('admin.settings') }}"
           class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Settings
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800
                px-4 py-3 rounded-xl text-sm mb-6">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.typography.update') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Font sizes + Colors ──────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Font Sizes ────────────────────────────────────────── --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <p class="typo-section-title">Font Sizes</p>
                    <div class="typo-field-grid">
                        @foreach($fontLabels as $key => $label)
                        <div class="typo-field">
                            <label for="f_{{ $key }}">{{ $label }}</label>
                            <input type="text"
                                   id="f_{{ $key }}"
                                   name="{{ $key }}"
                                   value="{{ $current[$key] ?? $fontKeys[$key] }}"
                                   placeholder="{{ $fontKeys[$key] }}"
                                   class="typo-font-input"
                                   autocomplete="off">
                            <p class="hint">e.g. 16px · 1rem · 1.2em</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Text Colors ───────────────────────────────────────── --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <p class="typo-section-title">Text Colors</p>
                    <div class="space-y-4">
                        @foreach($colorLabels as $key => $label)
                        @php $val = $current[$key] ?? $colorKeys[$key]; @endphp
                        <div class="typo-field">
                            <label>{{ $label }}</label>
                            <div class="color-row">
                                <input type="color"
                                       id="cp_{{ $key }}"
                                       value="{{ $val }}"
                                       oninput="syncText(this,'ct_{{ $key }}','sw_{{ $key }}','sl_{{ $key }}')">

                                <input type="text"
                                       id="ct_{{ $key }}"
                                       name="{{ $key }}"
                                       value="{{ $val }}"
                                       maxlength="50"
                                       placeholder="{{ $colorKeys[$key] }}"
                                       oninput="syncPicker(this,'cp_{{ $key }}','sw_{{ $key }}','sl_{{ $key }}')"
                                       autocomplete="off">

                                <span class="swatch-badge">
                                    <span class="swatch-dot" id="sw_{{ $key }}" style="background:{{ $val }}"></span>
                                    <span id="sl_{{ $key }}">{{ $val }}</span>
                                </span>
                            </div>
                            @error($key)
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- RIGHT: Save + Live Preview ──────────────────────────────── --}}
            <div class="space-y-5">

                {{-- Save ────────────────────────────────────────────────── --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm sticky top-5">
                    <p class="text-sm font-bold text-gray-700 mb-4">Save Changes</p>
                    <button type="submit"
                            class="w-full text-white font-bold text-sm px-5 py-3 rounded-xl
                                   hover:opacity-90 active:scale-[.98] transition-all shadow-sm"
                            style="background: var(--brand-color, #364851)">
                        Save Typography Settings
                    </button>
                    <p class="text-xs text-gray-400 mt-3 text-center">
                        Changes apply instantly to the storefront.
                    </p>
                </div>

                {{-- Live Preview ─────────────────────────────────────────── --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                    <p class="typo-section-title">Live Preview</p>
                    <div class="space-y-3">

                        <p id="pv-heading" class="font-bold leading-tight"
                           style="font-size:{{ $current['heading_font_size'] }};color:{{ $current['heading_text_color'] }}">
                            Product Heading
                        </p>

                        <p id="pv-body"
                           style="font-size:{{ $current['base_font_size'] }};color:{{ $current['body_text_color'] }}">
                            Body text — how paragraphs and descriptions look.
                        </p>

                        <p id="pv-muted"
                           style="font-size:{{ $current['card_font_size'] }};color:{{ $current['muted_text_color'] }}">
                            Muted text — categories, timestamps, hints.
                        </p>

                        <div class="flex items-center gap-3">
                            <span id="pv-price" class="font-black tabular-nums"
                                  style="font-size:{{ $current['product_price_font_size'] }};color:{{ $current['price_text_color'] }}">
                                $49.99
                            </span>
                            <span class="text-xs text-gray-400 line-through">$79.99</span>
                        </div>

                        <div>
                            <span id="pv-badge"
                                  class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full"
                                  style="background:var(--brand-color,#364851);
                                         color:{{ $current['badge_text_color'] }};
                                         font-size:{{ $current['button_font_size'] }}">
                                SALE
                            </span>
                        </div>

                        <input id="pv-input" type="text" value="Input field example" readonly
                               class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs w-full"
                               style="color:{{ $current['input_text_color'] }}">
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// picker → text input + swatch
function syncText(picker, textId, swId, slId) {
    var h = picker.value;
    document.getElementById(textId).value = h;
    document.getElementById(swId).style.background = h;
    document.getElementById(slId).textContent = h;
    updatePreview();
}

// text input → picker + swatch
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
        if (ids.color) el.style.color    = get(ids.color);
        if (ids.size)  el.style.fontSize = get(ids.size);
    });
}

// Attach live preview to font size inputs
document.querySelectorAll('.typo-font-input').forEach(function(el) {
    el.addEventListener('input', updatePreview);
});
</script>
@endpush