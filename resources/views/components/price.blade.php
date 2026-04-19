{{--
    resources/views/components/price.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Rendered by App\View\Components\Price.

    Available variables (set in the component class constructor):
      $converted  (float)   — The converted amount
      $formatted  (string)  — e.g. "12.50"
      $symbol     (string)  — e.g. "د.أ" or "$"
      $code       (string)  — e.g. "JOD"
      $display    (string)  — Full string: "12.50 د.أ" or "$12.50"
      $isPrefix   (bool)    — Symbol before or after the number
      $tag        (string)  — HTML tag to wrap with (default: "span")

    The outer element merges any extra classes/attributes passed via the
    component tag (e.g. class="text-red-500 font-black tabular-nums").

    Example outputs:
      JOD: <span class="tabular-nums">12.50 <span>د.أ</span></span>
      USD: <span class="tabular-nums"><span>$</span>12.50</span>
--}}

<{{ $tag }} {{ $attributes->merge(['class' => 'tabular-nums']) }}>


    @if($isPrefix)
        <span class="currency-symbol text-inherit">{{ $symbol }}</span>{{ $formatted }}

    {{-- Suffix symbol (د.أ, ر.س …) --}}
    @else
        {{ $formatted }} <span class="currency-symbol text-inherit">{{ $symbol }}</span>
    @endif

</{{ $tag }}>