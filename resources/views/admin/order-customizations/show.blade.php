@extends('layouts.admin')

@section('title', 'تخصيص #' . $customization->id)

@section('admin-content')
@php
    use Illuminate\Support\Facades\Storage;

    $uploadsByZone = $customization->uploads->groupBy('zone_key');

    $statusMeta = [
        'pending'    => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'label' => 'قيد الانتظار'],
        'processing' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200',       'label' => 'جارٍ المعالجة'],
        'ready'      => ['class' => 'bg-green-100 text-green-800 border-green-200',    'label' => 'جاهز'],
        'error'      => ['class' => 'bg-red-100 text-red-800 border-red-200',          'label' => 'خطأ'],
    ];
    $sm = $statusMeta[$customization->status] ?? ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $customization->status];

    // Build a zone label lookup from $config
    $zoneLabels = [];
    foreach ($config->zones() as $z) {
        $zoneLabels[$z['key']] = $z['label'] ?? $z['key'];
    }

    // Color area labels — covers both jacket/hoodie and robe
    $colorAreaLabels = [
        'body'   => 'الجسم',
        'sleeve' => 'الأكمام',
        'rib'    => 'الأطواق',
        'stripe' => 'الخطوط',
        'main'   => 'لون الثوب',
        'yoke1'  => 'الشريط الأول',
        'yoke2'  => 'الشريط الثاني',
        'yoke3'  => 'الشريط الثالث',
        'line'   => 'لون التحديد',
    ];

    // Product image
    $product = $customization->product;
    $image = null;
    if ($product) {
        if (method_exists($product, 'getFirstMediaUrl')) {
            $image = $product->getFirstMediaUrl('images') ?: null;
        } elseif (! empty($product->image)) {
            $image = \Illuminate\Support\Str::startsWith($product->image, 'http')
                ? $product->image
                : Storage::disk('public')->url($product->image);
        }
    }
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">تخصيص #{{ $customization->id }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $product->name ?? 'منتج تجريبي' }}
            @if($customization->order_id)
                · طلب #{{ $customization->order_id }}
            @endif
        </p>
    </div>

    <div class="flex items-center gap-3">
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $sm['class'] }}">
            {{ $sm['label'] }}
        </span>
        <a href="{{ route('admin.customizations.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors border border-gray-200 rounded-xl px-3 py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            رجوع
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ════════════════════════════════════════════════════════════════════
         LEFT: Product info + Colors + Zones
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="xl:col-span-2 flex flex-col gap-5">

        {{-- ── Product card ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                @if($image)
                    <img src="{{ $image }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <p class="font-bold text-gray-900">{{ $product->name ?? 'منتج تجريبي' }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    نوع المنتج: {{ $config->garmentType() === 'varsity_jacket' ? 'جاكيت رياضي' : ($config->garmentType() === 'hoodie' ? 'هودي' : 'ثوب تخرج') }}
                </p>
            </div>
            <div class="text-right text-xs text-gray-400">
                <p>أُنشئ</p>
                <p class="font-medium text-gray-700 mt-0.5">{{ $customization->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        {{-- ── Colors ──────────────────────────────────────────────────── --}}
        @if(! empty($customization->colors))
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">الألوان المختارة</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($customization->colors as $area => $hex)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3 border border-gray-100">
                    <div class="w-9 h-9 rounded-lg border border-gray-200 shadow-inner flex-shrink-0"
                         style="background: {{ $hex }};
                                {{ $hex === '#ffffff' || strtolower($hex) === '#fff' ? 'border-color:#e5e7eb;' : '' }}">
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-700">{{ $colorAreaLabels[$area] ?? $area }}</p>
                        <p class="text-[10px] text-gray-400 font-mono" dir="ltr">{{ $hex }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Zones ───────────────────────────────────────────────────── --}}
        @if(! empty($customization->selected_zones))
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">
                المناطق المستخدمة
                <span class="font-normal normal-case text-gray-400 mr-1">({{ count($customization->selected_zones) }} منطقة)</span>
            </h4>

            <div class="flex flex-col gap-3">
                @foreach($customization->selected_zones as $zoneKey)
                @php
                    $zoneText  = $customization->textForZone($zoneKey);
                    $zoneStyle = $customization->textStyleForZone($zoneKey);
                    $hasUpload = $uploadsByZone->has($zoneKey);
                @endphp

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">

                    {{-- Zone header --}}
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                         bg-blue-600 text-white text-[10px] font-bold flex-shrink-0">
                                {{ $zoneKey }}
                            </span>
                            <span class="text-sm font-semibold text-gray-800">
                                {{ $zoneLabels[$zoneKey] ?? 'المنطقة ' . $zoneKey }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($zoneText)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">نص</span>
                            @endif
                            @if($hasUpload)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">صورة</span>
                            @endif
                        </div>
                    </div>

                    {{-- Text content --}}
                    @if($zoneText)
                    <div class="mb-3 flex items-start gap-3">
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-400 mb-1 uppercase tracking-wide">النص المُدخَل</p>
                            <p class="text-base font-bold tracking-widest text-gray-900"
                               style="color: {{ $zoneStyle['color'] }}; font-size: {{ min(24, $zoneStyle['fontSize']) }}px; font-style: {{ $zoneStyle['fontStyle'] }}; background: #1a1a1a; padding: 6px 12px; border-radius: 8px; display: inline-block;">
                                {{ $zoneText }}
                            </p>
                        </div>
                        <div class="text-right text-[10px] text-gray-400 flex-shrink-0">
                            <p>اللون: <span class="font-mono">{{ $zoneStyle['color'] }}</span></p>
                            <p>الحجم: {{ $zoneStyle['fontSize'] }}px</p>
                            @if($zoneStyle['fontStyle'] !== 'normal')
                            <p>مائل</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Image uploads --}}
                    @if($hasUpload)
                    <div>
                        <p class="text-[10px] text-gray-400 mb-2 uppercase tracking-wide">الصورة المرفوعة</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($uploadsByZone[$zoneKey] as $upload)
                            <div class="relative group">
                                <img src="{{ $upload->url() }}"
                                     alt="صورة {{ $zoneKey }}"
                                     class="w-20 h-20 object-cover rounded-xl border border-gray-200 shadow-sm">
                                <a href="{{ $upload->url() }}"
                                   target="_blank"
                                   class="absolute inset-0 flex flex-col items-center justify-center
                                          bg-black bg-opacity-60 rounded-xl opacity-0 group-hover:opacity-100
                                          transition-opacity text-white text-[10px] gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    فتح
                                </a>
                                <p class="text-[9px] text-gray-400 mt-0.5 text-center">
                                    {{ $upload->formattedSize() }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Customer notes ────────────────────────────────────────── --}}
        @if($customization->notes)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <h4 class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">ملاحظات العميل</h4>
            <p class="text-sm text-gray-800 leading-relaxed">{{ $customization->notes }}</p>
        </div>
        @endif

    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         RIGHT: Sidebar — meta, preview, quick actions
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-5">

        {{-- ── Order meta ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">معلومات الطلب</h4>
            <dl class="flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">رقم التخصيص</dt>
                    <dd class="text-xs font-bold text-gray-900 font-mono">#{{ $customization->id }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">رقم الطلب</dt>
                    <dd class="text-xs font-bold text-gray-900 font-mono">
                        {{ $customization->order_id ?: '—' }}
                    </dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">المناطق المفعّلة</dt>
                    <dd class="text-xs font-bold text-gray-900">
                        {{ count($customization->selected_zones ?? []) }}
                    </dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">الصور المرفوعة</dt>
                    <dd class="text-xs font-bold text-gray-900">
                        {{ $customization->uploads->count() }}
                    </dd>
                </div>
                <div class="border-t border-gray-100 pt-2 mt-1">
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-gray-400">تاريخ الإنشاء</dt>
                        <dd class="text-xs text-gray-700">{{ $customization->created_at->format('Y-m-d') }}</dd>
                    </div>
                    <div class="flex justify-between items-center mt-1.5">
                        <dt class="text-xs text-gray-400">آخر تحديث</dt>
                        <dd class="text-xs text-gray-700">{{ $customization->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </div>
            </dl>
        </div>

        {{-- ── Final render preview ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">المعاينة النهائية</h4>

            @if($customization->rendered_preview_path)
                <img src="{{ Storage::disk('public')->url($customization->rendered_preview_path) }}"
                     alt="المعاينة النهائية"
                     class="w-full rounded-xl border border-gray-200 shadow-sm">
                <a href="{{ Storage::disk('public')->url($customization->rendered_preview_path) }}"
                   target="_blank"
                   class="mt-3 flex items-center justify-center gap-1.5 text-xs text-blue-600 hover:text-blue-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    تحميل النسخة الكاملة
                </a>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-gray-300">
                    <svg class="w-10 h-10 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs text-center text-gray-400">
                        المعاينة النهائية ستظهر هنا<br>
                        بعد معالجة التصميم (المرحلة الثانية)
                    </p>
                </div>
            @endif
        </div>

        {{-- ── Phase 2 action placeholder ─────────────────────────────── --}}
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-5 text-center">
            <p class="text-xs font-medium text-gray-500 mb-1">إجراءات المرحلة الثانية</p>
            <p class="text-[11px] text-gray-400">
                سيظهر هنا زر "إعادة معالجة التصميم" عند إضافة محرك التصيير في المرحلة الثانية.
            </p>
        </div>

    </div>

</div>

@endsection