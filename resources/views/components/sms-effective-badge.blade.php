{{--
    components/sms-effective-badge.blade.php
    عرض مصدر القيمة (قاعدة البيانات أو ملف الإعدادات) مع مراعاة خصوصية الحقول السرية.
--}}

@props(['effective' => null, 'dbVal' => null, 'key' => null])

@php
    // التحقق إذا كان هناك قيمة في قاعدة البيانات
    $hasDbValue = !is_null($dbVal) && $dbVal !== '';
    $label      = $hasDbValue ? 'من قاعدة البيانات' : 'افتراضي (config/sms.php)';
    $cls        = $hasDbValue ? 'source-db' : 'source-config';
    $icon       = $hasDbValue ? '🗄️' : '⚙️';
    $displayVal = $effective ?? '—';

    // جلب حالة السرية مباشرة من قاعدة البيانات إذا لم يتم تمرير الموديل بالكامل
    // لضمان عدم ظهور خطأ "Undefined variable $settings"
    $isSecret = false;
    if ($key) {
        $isSecret = \DB::table('otpsettings')->where('key', $key)->value('is_secret') ?? false;
    }
@endphp

@if($effective)
<div class="mt-2 flex items-center gap-2 flex-wrap">
    {{-- شارة مصدر البيانات --}}
    <span class="effective-badge {{ $cls }} text-[10px] px-2 py-0.5 rounded border border-gray-200 bg-gray-50">
        {{ $icon }} {{ $label }}
    </span>

    {{-- عرض القيمة الفعلية فقط إذا لم يكن الحقل سرياً --}}
    @if(!$isSecret)
        <span class="text-[11px] text-gray-400 font-mono truncate max-w-[260px]" dir="ltr">
            {{ Str::limit($displayVal, 60) }}
        </span>
    @else
        <span class="text-[11px] text-gray-400 italic">
            (مخفي لأنه حقل سري)
        </span>
    @endif
</div>
@endif

<style>
    .source-db { color: #059669; border-color: #10b981; background-color: #ecfdf5; }
    .source-config { color: #4b5563; border-color: #9ca3af; background-color: #f3f4f6; }
</style>