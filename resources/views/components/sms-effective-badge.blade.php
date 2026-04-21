{{--
    components/sms-effective-badge.blade.php
    Shows whether the effective value comes from DB or config fallback.

    Usage:
        <x-sms-effective-badge :effective="$effective['sms_url']" :db-val="$settings->get('sms_url')?->value" />
--}}

@php
    $hasDbValue = !is_null($dbVal) && $dbVal !== '';
    $label      = $hasDbValue ? 'من قاعدة البيانات' : 'افتراضي (config/sms.php)';
    $cls        = $hasDbValue ? 'source-db' : 'source-config';
    $icon       = $hasDbValue ? '🗄️' : '⚙️';
    $displayVal = $effective ?? '—';
@endphp

@if($effective)
<div class="mt-2 flex items-center gap-2 flex-wrap">
    <span class="effective-badge {{ $cls }}">{{ $icon }} {{ $label }}</span>
    @if(!($settings->get(last(explode('_', $key ?? '')))?->is_secret ?? false))
    <span class="text-[11px] text-gray-400 font-mono truncate max-w-[260px]" dir="ltr">
        {{ Str::limit($displayVal, 60) }}
    </span>
    @endif
</div>
@endif