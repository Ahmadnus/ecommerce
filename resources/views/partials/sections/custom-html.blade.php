{{--
    partials/sections/custom-html.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Receives: $section  (HomeSection model)
    Config keys: content (raw HTML), css_class
    ─────────────────────────────────────────────────────────────────────────────
--}}
@php
    $cssClass = $section->cfg('css_class', '');
    $content  = $section->cfg('content', '');
@endphp

@if($content)
<div class="mb-6 reveal {{ $cssClass }}">
    {!! $content !!}
</div>
@endif