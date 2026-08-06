{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/partials/size-selector.blade.php                                ║
║                                                                            ║
║  Self-contained size selector + size chart modal.                          ║
║  All data comes from config('garment_sizes') — never hardcoded here.       ║
║                                                                            ║
║  Variables:                                                                ║
║    $garmentType  – e.g. 'tshirt', 'hoodie', 'varsity_jacket', ...         ║
║  Integration:                                                              ║
║    Alpine.js x-data="designEngine()" on the parent must include           ║
║    designState.size (string). The selected size chip sets it via           ║
║    x-model / @click, and submitDesign() sends it as fd.append('size', …)  ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $chart  = config("garment_sizes.charts.{$garmentType}", []);
    $labels = config('garment_sizes.measurement_labels', []);
    $sizes  = array_keys($chart);

    // Which measurement keys exist for this garment?
    $measurementKeys = $chart ? array_keys(reset($chart)) : [];
@endphp

@if(empty($chart))
{{-- Silently skip if no chart defined for this garment type --}}
@else

<div class="card" x-data="{ showChart: false }">
    <span class="card__label">المقاس</span>

    {{-- ── Size chips ────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-2 mb-4" role="radiogroup" aria-label="اختيار المقاس">
        @foreach($sizes as $size)
        <button type="button"
                role="radio"
                :aria-checked="designState.size === '{{ $size }}'"
                :class="designState.size === '{{ $size }}'
                    ? 'size-chip size-chip--active'
                    : 'size-chip'"
                @click="designState.size = '{{ $size }}'">
            {{ $size }}
        </button>
        @endforeach
    </div>

    {{-- ── Selected size measurements ───────────────────────────────────── --}}
    {{-- Show inline measurements for whichever size is selected --}}
    @foreach($chart as $size => $measurements)
    <div x-show="designState.size === '{{ $size }}'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         style="display:none;">
        <div class="size-measurements">
            @foreach($measurements as $key => $value)
            <div class="size-measurement-item">
                <span class="size-measurement-label">
                    {{ $labels[$key]['ar'] ?? $key }}
                </span>
                <span class="size-measurement-value">
                    @if($key === 'height_range')
                        {{ $value }} سم
                    @else
                        {{ $value }} سم
                    @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- ── No size selected hint ─────────────────────────────────────────── --}}
    <p class="text-xs mt-2" style="color:var(--muted);"
       x-show="! designState.size">
        اختر مقاسك من الأزرار أعلاه
    </p>

    {{-- ── Size chart toggle ──────────────────────────────────────────────── --}}
    <button type="button"
            class="size-chart-toggle"
            @click="showChart = !showChart"
            :aria-expanded="showChart">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <path d="M3 3h18v18H3z M3 9h18 M3 15h18 M9 3v18"/>
        </svg>
        <span x-text="showChart ? 'إخفاء جدول المقاسات' : 'عرض جدول المقاسات الكامل'">
            عرض جدول المقاسات الكامل
        </span>
    </button>

    {{-- ── Full size chart table ──────────────────────────────────────────── --}}
    <div x-show="showChart"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display:none; margin-top:12px; overflow-x:auto;">
        <table class="size-chart-table">
            <thead>
                <tr>
                    <th>المقاس</th>
                    @foreach($measurementKeys as $key)
                    <th>{{ $labels[$key]['ar'] ?? $key }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($chart as $size => $measurements)
                <tr :class="designState.size === '{{ $size }}' ? 'size-chart-row--active' : ''">
                    <td class="size-chart-size-cell">{{ $size }}</td>
                    @foreach($measurementKeys as $key)
                    <td>
                        {{ $measurements[$key] ?? '—' }}
                        @if(isset($measurements[$key]) && $key !== 'height_range') سم @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:10px; color:var(--muted); margin-top:6px; text-align:center;">
            جميع القياسات بالسنتيمتر (سم)
        </p>
    </div>
</div>

{{-- ── Scoped styles ───────────────────────────────────────────────────────── --}}
<style>
.size-chip {
    font-family: var(--font-body, system-ui);
    font-size: 13px;
    font-weight: 600;
    padding: 7px 16px;
    border-radius: 10px;
    border: 1.5px solid var(--border, #e8e6e1);
    background: var(--surface, #f7f6f3);
    color: var(--ink-light, #3d3d3d);
    cursor: pointer;
    transition: all .15s;
    line-height: 1;
}
.size-chip:hover {
    border-color: var(--ink, #0a0a0a);
    color: var(--ink, #0a0a0a);
}
.size-chip--active {
    background: var(--ink, #0a0a0a);
    color: #fff;
    border-color: var(--ink, #0a0a0a);
}

.size-measurements {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}
@media (max-width: 400px) {
    .size-measurements { grid-template-columns: 1fr; }
}
.size-measurement-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
    background: var(--surface, #f7f6f3);
    border: 1px solid var(--border, #e8e6e1);
    border-radius: 10px;
    padding: 8px 12px;
}
.size-measurement-label {
    font-size: 10px;
    font-weight: 600;
    color: var(--muted, #8a8680);
    text-transform: uppercase;
    letter-spacing: .06em;
}
.size-measurement-value {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink, #0a0a0a);
    font-variant-numeric: tabular-nums;
}

.size-chart-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: var(--font-body, system-ui);
    font-size: 12px;
    font-weight: 500;
    color: var(--muted, #8a8680);
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color .15s;
}
.size-chart-toggle:hover { color: var(--ink, #0a0a0a); }

.size-chart-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.size-chart-table th {
    background: var(--surface, #f7f6f3);
    color: var(--muted, #8a8680);
    font-weight: 700;
    font-size: 10px;
    letter-spacing: .06em;
    text-transform: uppercase;
    text-align: center;
    padding: 8px 10px;
    border: 1px solid var(--border, #e8e6e1);
}
.size-chart-table td {
    text-align: center;
    padding: 7px 10px;
    border: 1px solid var(--border, #e8e6e1);
    color: var(--ink, #0a0a0a);
    font-variant-numeric: tabular-nums;
}
.size-chart-table tbody tr:hover td {
    background: var(--surface, #f7f6f3);
}
.size-chart-row--active td {
    background: rgba(59,130,246,0.06) !important;
    font-weight: 700;
}
.size-chart-size-cell {
    font-weight: 800 !important;
    color: var(--ink, #0a0a0a) !important;
}
</style>

@endif