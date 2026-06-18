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
    $sm = $statusMeta[$customization->status]
        ?? ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => $customization->status];

    // Zone label lookup from $config
    $zoneLabels = [];
    foreach ($config->zones() as $z) {
        $zoneLabels[$z['key']] = $z['label'] ?? $z['key'];
    }

    // All color area labels — covers all 4 garment types
    $colorAreaLabels = [
        'body'   => 'الجسم',
        'sleeve' => 'الأكمام',
        'rib'    => 'الأطواق',
        'stripe' => 'الخطوط',
        'collar' => 'الياقة',
        'stitch' => 'الخيط',
        'border' => 'الحدود',
        'main'   => 'لون الثوب',
        'yoke1'  => 'الشريط الأول',
        'yoke2'  => 'الشريط الثاني',
        'yoke3'  => 'الشريط الثالث',
        'line'   => 'لون التحديد',
    ];

    // Human-readable garment type
    $garmentLabels = [
        'varsity_jacket'  => 'جاكيت رياضي',
        'hoodie'          => 'هودي',
        'graduation_robe' => 'ثوب تخرج',
        'tshirt'          => 'تيشيرت',
        'stole'           => 'وشاح التخرج',
    ];
    $garmentType  = $config->garmentType();
    $garmentLabel = $garmentLabels[$garmentType] ?? $garmentType;

    // ── Size data from config ──────────────────────────────────────────────────
    $sizeChart         = config("garment_sizes.charts.{$garmentType}", []);
    $measurementLabels = config('garment_sizes.measurement_labels', []);
    $selectedSize      = $customization->size;
    $sizeMeasurements  = ($selectedSize && isset($sizeChart[$selectedSize]))
                         ? $sizeChart[$selectedSize]
                         : [];

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

    // Saved colors — fall back to $config defaults when a key is missing
    $savedColors  = $customization->colors ?? [];
    $defaultColors = $config->defaultColors();
    $previewColors = array_merge($defaultColors, $savedColors);

    // Build the saved zones list (for the garment partial — show ALL config zones
    // so zone outlines are visible, not just the ones the customer activated)
    $allZones = $config->zones();

    // ── Zone coordinate map (same as show.blade.php — used for image injection) ──
    $zoneCoords = [
        // Jacket / hoodie shared
        'A'  => ['cx'=>146,'cy'=>192,'imgX'=>112,'imgY'=>158,'imgW'=>68, 'imgH'=>68, 'rotate'=>null,                  'svgs'=>['front']],
        'B'  => ['cx'=>254,'cy'=>192,'imgX'=>220,'imgY'=>158,'imgW'=>68, 'imgH'=>68, 'rotate'=>null,                  'svgs'=>['front']],
        'C'  => ['cx'=>147,'cy'=>332,'imgX'=>128,'imgY'=>296,'imgW'=>38, 'imgH'=>72, 'rotate'=>null,                  'svgs'=>['front']],
        'D'  => ['cx'=>253,'cy'=>332,'imgX'=>234,'imgY'=>296,'imgW'=>38, 'imgH'=>72, 'rotate'=>null,                  'svgs'=>['front']],
        'E1' => ['cx'=>74, 'cy'=>185,'imgX'=>52, 'imgY'=>163,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-18 74 185)', 'svgs'=>['front']],
        'E2' => ['cx'=>70, 'cy'=>268,'imgX'=>48, 'imgY'=>246,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-14 70 268)', 'svgs'=>['front']],
        'E3' => ['cx'=>74, 'cy'=>348,'imgX'=>54, 'imgY'=>328,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(-8 74 348)',  'svgs'=>['front']],
        'F1' => ['cx'=>326,'cy'=>185,'imgX'=>304,'imgY'=>163,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(18 326 185)', 'svgs'=>['front']],
        'F2' => ['cx'=>330,'cy'=>268,'imgX'=>308,'imgY'=>246,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(14 330 268)', 'svgs'=>['front']],
        'F3' => ['cx'=>326,'cy'=>348,'imgX'=>306,'imgY'=>328,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(8 326 348)',  'svgs'=>['front']],
        'G'  => ['cx'=>200,'cy'=>244,'imgX'=>122,'imgY'=>160,'imgW'=>156,'imgH'=>168,'rotate'=>null,                  'svgs'=>['back']],
        'H'  => ['cx'=>200,'cy'=>177,'imgX'=>146,'imgY'=>152,'imgW'=>108,'imgH'=>50, 'rotate'=>null,                  'svgs'=>['back']],
        // Hoodie-specific
        'D1' => ['cx'=>75, 'cy'=>182,'imgX'=>53, 'imgY'=>160,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-15 75 182)','svgs'=>['front']],
        'D2' => ['cx'=>72, 'cy'=>257,'imgX'=>50, 'imgY'=>235,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-12 72 257)','svgs'=>['front']],
        'D3' => ['cx'=>73, 'cy'=>335,'imgX'=>53, 'imgY'=>315,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(-7 73 335)', 'svgs'=>['front']],
        'F'  => ['cx'=>200,'cy'=>181,'imgX'=>152,'imgY'=>148,'imgW'=>96, 'imgH'=>66, 'rotate'=>null,                  'svgs'=>['back']],
        // T-shirt
        // (A, B, D1, E1, F already defined above — C and F overridden below per garment)
        // Robe — localTransform zones
        '1'  => ['cx'=>25,'cy'=>25,'imgX'=>0,  'imgY'=>0,  'imgW'=>50, 'imgH'=>50, 'rotate'=>null,
                  'svgs'=>['front'],'localTransform'=>true,'groupTransform'=>'translate(285,220)'],
        '2'  => ['cx'=>25,'cy'=>25,'imgX'=>0,  'imgY'=>0,  'imgW'=>50, 'imgH'=>50, 'rotate'=>null,
                  'svgs'=>['front'],'localTransform'=>true,'groupTransform'=>'translate(165,220)'],
        '4'  => ['cx'=>70,'cy'=>90,'imgX'=>0,  'imgY'=>0,  'imgW'=>140,'imgH'=>180,'rotate'=>null,
                  'svgs'=>['back'], 'localTransform'=>true,'groupTransform'=>'translate(180,210)'],
        '5'  => ['cx'=>0,'cy'=>0,  'imgX'=>-25,'imgY'=>-40,'imgW'=>50, 'imgH'=>80, 'rotate'=>null,
                  'svgs'=>['front','back'],'localTransform'=>true,'groupTransform'=>'translate(80,210) rotate(25)'],
        '6'  => ['cx'=>0,'cy'=>0,  'imgX'=>-25,'imgY'=>-40,'imgW'=>50, 'imgH'=>80, 'rotate'=>null,
                  'svgs'=>['front','back'],'localTransform'=>true,'groupTransform'=>'translate(420,210) rotate(-25)'],
    ];

    // Per-garment overrides for zones that share keys but differ in coords
    if ($garmentType === 'stole') {
        // Stole uses 500×780 viewBox, straight panels, no rotation
        $zoneCoords['A'] = ['cx'=>130,'cy'=>101,'imgX'=>76, 'imgY'=>36, 'imgW'=>108,'imgH'=>130,'rotate'=>null,'svgs'=>['front'],'localTransform'=>false,'groupTransform'=>null];
        $zoneCoords['B'] = ['cx'=>130,'cy'=>557,'imgX'=>76, 'imgY'=>480,'imgW'=>108,'imgH'=>155,'rotate'=>null,'svgs'=>['front'],'localTransform'=>false,'groupTransform'=>null];
        $zoneCoords['C'] = ['cx'=>370,'cy'=>101,'imgX'=>316,'imgY'=>36, 'imgW'=>108,'imgH'=>130,'rotate'=>null,'svgs'=>['front'],'localTransform'=>false,'groupTransform'=>null];
        $zoneCoords['D'] = ['cx'=>370,'cy'=>557,'imgX'=>316,'imgY'=>480,'imgW'=>108,'imgH'=>155,'rotate'=>null,'svgs'=>['front'],'localTransform'=>false,'groupTransform'=>null];
    }

    if ($garmentType === 'hoodie') {
        $zoneCoords['C'] = ['cx'=>200,'cy'=>335,'imgX'=>138,'imgY'=>303,'imgW'=>124,'imgH'=>65,
                             'rotate'=>null,'svgs'=>['front']];
        $zoneCoords['G'] = ['cx'=>200,'cy'=>298,'imgX'=>126,'imgY'=>232,'imgW'=>148,'imgH'=>132,
                             'rotate'=>null,'svgs'=>['back']];
    }
    if ($garmentType === 'tshirt') {
        $zoneCoords['C']  = ['cx'=>200,'cy'=>280,'imgX'=>140,'imgY'=>220,'imgW'=>120,'imgH'=>120,
                              'rotate'=>null,'svgs'=>['front']];
        $zoneCoords['D1'] = ['cx'=>55,'cy'=>154,'imgX'=>34,'imgY'=>133,'imgW'=>42,'imgH'=>42,
                              'rotate'=>'rotate(-20 55 154)','svgs'=>['front']];
        $zoneCoords['E1'] = ['cx'=>345,'cy'=>154,'imgX'=>324,'imgY'=>133,'imgW'=>42,'imgH'=>42,
                              'rotate'=>'rotate(20 345 154)','svgs'=>['front']];
        $zoneCoords['F']  = ['cx'=>200,'cy'=>230,'imgX'=>130,'imgY'=>150,'imgW'=>140,'imgH'=>160,
                              'rotate'=>null,'svgs'=>['back']];
    }

    // ── Build per-SVG image injection data ────────────────────────────────────
    // For each zone that has an upload, prepare the data the inline script needs.
    $svgImageData = [];   // ['zoneKey' => ['url', 'svgs', coords...]]
    $svgTextData  = [];   // ['zoneKey' => ['text', 'style', coords...]]

    foreach ($customization->selected_zones ?? [] as $zoneKey) {
        $c = $zoneCoords[$zoneKey] ?? null;
        if (! $c) continue;

        // Images
        if ($uploadsByZone->has($zoneKey)) {
            $upload = $uploadsByZone[$zoneKey]->first();
            $svgImageData[$zoneKey] = [
                'url'            => $upload->url(),
                'svgs'           => $c['svgs'],
                'imgX'           => $c['imgX'],
                'imgY'           => $c['imgY'],
                'imgW'           => $c['imgW'],
                'imgH'           => $c['imgH'],
                'rotate'         => $c['rotate'] ?? null,
                'localTransform' => $c['localTransform'] ?? false,
                'groupTransform' => $c['groupTransform'] ?? null,
            ];
        }

        // Text
        $text = $customization->textForZone($zoneKey);
        if ($text !== '') {
            $style = $customization->textStyleForZone($zoneKey);
            $svgTextData[$zoneKey] = [
                'text'           => $text,
                'color'          => $style['color']     ?? '#ffffff',
                'fontSize'       => $style['fontSize']  ?? 22,
                'fontStyle'      => $style['fontStyle'] ?? 'normal',
                'svgs'           => $c['svgs'],
                'cx'             => $c['cx'],
                'cy'             => $c['cy'],
                'rotate'         => $c['rotate'] ?? null,
                'localTransform' => $c['localTransform'] ?? false,
                'groupTransform' => $c['groupTransform'] ?? null,
            ];
        }
    }
@endphp

{{-- ── Page header ──────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">تخصيص #{{ $customization->id }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $product->name ?? 'منتج تجريبي' }}
            @if($customization->order_id) · طلب #{{ $customization->order_id }} @endif
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full border {{ $sm['class'] }}">
            {{ $sm['label'] }}
        </span>
        <a href="{{ route('admin.customizations.index') }}"
           class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors
                  border border-gray-200 rounded-xl px-3 py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            رجوع
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ════════════════════════════════════════════════════════════════════
         LEFT COL: SVG Preview + Product info + Colors + Zones
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="xl:col-span-2 flex flex-col gap-5">

        {{-- ══════════════════════════════════════════════════════════════
             LIVE SVG GARMENT PREVIEW
             Renders the exact same SVG the customer sees, with their
             saved color choices applied via inline CSS variables.
             No Alpine.js or JS required — pure static SVG.
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header with front/back toggle --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">معاينة التصميم</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">
                        {{ $garmentLabel }}
                    </span>
                </div>
                {{-- Simple toggle — no Alpine needed, plain JS below --}}
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    <button onclick="adminPreviewToggle('front', this)"
                            id="btn-front"
                            class="text-xs font-semibold px-3 py-1.5 rounded-md bg-white shadow-sm text-gray-900 transition-all">
                        الأمامي
                    </button>
                    <button onclick="adminPreviewToggle('back', this)"
                            id="btn-back"
                            class="text-xs font-semibold px-3 py-1.5 rounded-md text-gray-500 transition-all">
                        الخلفي
                    </button>
                </div>
            </div>

            {{-- SVG wrapper — inline style applies the customer's saved colors --}}
            <div class="p-6 flex items-center justify-center bg-gradient-to-b from-gray-50 to-white min-h-[360px]">
                <div id="admin-garment-wrapper"
                     class="w-full max-w-xs"
                     style="
                        {{-- Apply every saved color as a CSS variable --}}
                        @foreach($previewColors as $area => $hex)
                        --c-{{ $area }}: {{ $hex }};
                        @endforeach
                     ">
                    {{--
                        Include the SAME garment partial the customer sees.
                        We pass:
                          $zones       = all config zones (so zone outlines show)
                          $zoneCoords  = empty (admin view is static, no click handling)
                          $defaults    = the saved colors (already merged above)
                        The partial's onclick handlers do nothing in admin context
                        since there's no Alpine designEngine() on this page.
                    --}}
                    @include('customize.garments.' . $garmentType, [
                        'zones'      => $allZones,
                        'zoneCoords' => $zoneCoords,
                        'defaults'   => $previewColors,
                    ])
                </div>
            </div>

            {{-- Color chips row under the SVG --}}
            @if(! empty($savedColors))
            <div class="px-5 py-3 border-t border-gray-100 flex flex-wrap gap-2">
                @foreach($savedColors as $area => $hex)
                <div class="flex items-center gap-1.5 bg-gray-50 rounded-lg px-2.5 py-1.5 border border-gray-100">
                    <div class="w-4 h-4 rounded flex-shrink-0 border border-gray-200"
                         style="background: {{ $hex }};"></div>
                    <span class="text-[10px] text-gray-500">{{ $colorAreaLabels[$area] ?? $area }}</span>
                    <span class="text-[10px] font-mono text-gray-400" dir="ltr">{{ $hex }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Customer notes banner if present --}}
            @if($customization->notes)
            <div class="px-5 py-3 border-t border-amber-100 bg-amber-50 flex items-start gap-2">
                <svg class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <p class="text-xs text-amber-800">{{ $customization->notes }}</p>
            </div>
            @endif
        </div>

        {{-- ── Size & Measurements ─────────────────────────────────────── --}}
        @if($selectedSize)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                    المقاس والقياسات
                </h4>
                <span class="text-sm font-black text-white bg-gray-900 px-3 py-1 rounded-lg">
                    {{ $selectedSize }}
                </span>
            </div>

            @if(! empty($sizeMeasurements))
            <div class="grid grid-cols-2 gap-3">
                @foreach($sizeMeasurements as $key => $value)
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">
                        {{ $measurementLabels[$key]['ar'] ?? $key }}
                    </p>
                    <p class="text-base font-bold text-gray-900 font-variant-numeric tabular-nums">
                        {{ $value }}
                        @if($key !== 'height_range') <span class="text-xs font-normal text-gray-400">سم</span>
                        @else <span class="text-xs font-normal text-gray-400">سم</span>
                        @endif
                    </p>
                </div>
                @endforeach
            </div>

            {{-- Full size chart for this garment ──────────────────────────── --}}
            @if(! empty($sizeChart))
            <details class="mt-4">
                <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-700
                               select-none flex items-center gap-1.5 transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h18v18H3z M3 9h18 M3 15h18 M9 3v18"/>
                    </svg>
                    عرض جدول المقاسات الكامل
                </summary>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr>
                                <th class="bg-gray-50 text-gray-500 font-bold text-[10px] uppercase
                                           tracking-wide text-center px-3 py-2 border border-gray-200">
                                    المقاس
                                </th>
                                @foreach(array_keys(reset($sizeChart)) as $mKey)
                                <th class="bg-gray-50 text-gray-500 font-bold text-[10px] uppercase
                                           tracking-wide text-center px-3 py-2 border border-gray-200">
                                    {{ $measurementLabels[$mKey]['ar'] ?? $mKey }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sizeChart as $sz => $measurements)
                            <tr class="{{ $sz === $selectedSize ? 'bg-blue-50 font-bold' : 'hover:bg-gray-50' }}">
                                <td class="text-center px-3 py-2 border border-gray-200 font-black
                                           {{ $sz === $selectedSize ? 'text-blue-700' : 'text-gray-900' }}">
                                    {{ $sz }}
                                    @if($sz === $selectedSize)
                                    <span class="text-[9px] block text-blue-500 font-normal">← المختار</span>
                                    @endif
                                </td>
                                @foreach(array_keys(reset($sizeChart)) as $mKey)
                                <td class="text-center px-3 py-2 border border-gray-200 text-gray-700
                                           font-variant-numeric tabular-nums">
                                    {{ $measurements[$mKey] ?? '—' }}
                                    @if(isset($measurements[$mKey]) && $mKey !== 'height_range')
                                    <span class="text-gray-400">سم</span>
                                    @elseif(isset($measurements[$mKey]) && $mKey === 'height_range')
                                    <span class="text-gray-400">سم</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-[10px] text-gray-400 text-center mt-2">جميع القياسات بالسنتيمتر</p>
                </div>
            </details>
            @endif
            @else
            <p class="text-sm text-gray-400">لا توجد قياسات متاحة لهذا المقاس.</p>
            @endif
        </div>
        @endif

        {{-- ── Zone details ─────────────────────────────────────────── --}}
        @if(! empty($customization->selected_zones))
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">
                المناطق المستخدمة
                <span class="font-normal normal-case text-gray-400 mr-1">
                    ({{ count($customization->selected_zones) }} منطقة)
                </span>
            </h4>

            <div class="flex flex-col gap-3">
                @foreach($customization->selected_zones as $zoneKey)
                @php
                    $zoneText  = $customization->textForZone($zoneKey);
                    $zoneStyle = $customization->textStyleForZone($zoneKey);
                    $hasUpload = $uploadsByZone->has($zoneKey);
                @endphp
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">

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

                    @if($zoneText)
                    <div class="mb-3 flex items-start gap-3">
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-400 mb-1 uppercase tracking-wide">النص المُدخَل</p>
                            <p class="font-bold tracking-widest"
                               style="color: {{ $zoneStyle['color'] }};
                                      font-size: {{ min(22, $zoneStyle['fontSize']) }}px;
                                      font-style: {{ $zoneStyle['fontStyle'] }};
                                      background: #1a1a1a;
                                      padding: 6px 12px;
                                      border-radius: 8px;
                                      display: inline-block;">
                                {{ $zoneText }}
                            </p>
                        </div>
                        <div class="text-right text-[10px] text-gray-400 flex-shrink-0">
                            <p>اللون: <span class="font-mono">{{ $zoneStyle['color'] }}</span></p>
                            <p>الحجم: {{ $zoneStyle['fontSize'] }}px</p>
                            @if($zoneStyle['fontStyle'] !== 'normal')<p>مائل</p>@endif
                        </div>
                    </div>
                    @endif

                    @if($hasUpload)
                    <div>
                        <p class="text-[10px] text-gray-400 mb-2 uppercase tracking-wide">الصورة المرفوعة</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($uploadsByZone[$zoneKey] as $upload)
                            <div class="relative group">
                                <img src="{{ $upload->url() }}"
                                     alt="صورة {{ $zoneKey }}"
                                     class="w-20 h-20 object-cover rounded-xl border border-gray-200 shadow-sm">
                                <a href="{{ $upload->url() }}" target="_blank"
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

    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         RIGHT COL: Order meta + Final render + Phase 2 placeholder
    ════════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-5">

        {{-- ── Order meta ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">معلومات الطلب</h4>
            <dl class="flex flex-col gap-2.5">
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">رقم التخصيص</dt>
                    <dd class="text-xs font-bold text-gray-900 font-mono">#{{ $customization->id }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">رقم الطلب</dt>
                    <dd class="text-xs font-bold text-gray-900 font-mono">{{ $customization->order_id ?: '—' }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">نوع المنتج</dt>
                    <dd class="text-xs font-bold text-gray-900">{{ $garmentLabel }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">المقاس</dt>
                    <dd>
                        @if($selectedSize)
                        <span class="text-xs font-bold text-white bg-gray-800 px-2 py-0.5 rounded-md">
                            {{ $selectedSize }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400 italic">غير محدد</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">المناطق المفعّلة</dt>
                    <dd class="text-xs font-bold text-gray-900">{{ count($customization->selected_zones ?? []) }}</dd>
                </div>
                <div class="flex justify-between items-center">
                    <dt class="text-xs text-gray-400">الصور المرفوعة</dt>
                    <dd class="text-xs font-bold text-gray-900">{{ $customization->uploads->count() }}</dd>
                </div>
                <div class="border-t border-gray-100 pt-2 mt-1">
                    <div class="flex justify-between items-center">
                        <dt class="text-xs text-gray-400">تاريخ الإنشاء</dt>
                        <dd class="text-xs text-gray-700">{{ $customization->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="flex justify-between items-center mt-1.5">
                        <dt class="text-xs text-gray-400">آخر تحديث</dt>
                        <dd class="text-xs text-gray-700">{{ $customization->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </div>
            </dl>
        </div>

        {{-- ── Final rendered image (Phase 2) ──────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4">المعاينة النهائية المُصيَّرة</h4>

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
                <div class="flex flex-col items-center justify-center py-6 text-gray-300">
                    <svg class="w-9 h-9 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs text-center text-gray-400">
                        ستظهر هنا بعد معالجة التصميم<br>(المرحلة الثانية)
                    </p>
                </div>
            @endif
        </div>

        {{-- Phase 2 placeholder --}}
        <div class="bg-gray-50 border border-dashed border-gray-300 rounded-2xl p-5 text-center">
            <p class="text-xs font-medium text-gray-500 mb-1">إجراءات المرحلة الثانية</p>
            <p class="text-[11px] text-gray-400">
                سيظهر هنا زر "إعادة معالجة التصميم" عند إضافة محرك التصيير.
            </p>
        </div>

    </div>

</div>

{{-- ── Admin preview script ───────────────────────────────────────────────── --}}
<script>
(function() {
    var NS = 'http://www.w3.org/2000/svg';

    // ── Image + text data from PHP ─────────────────────────────────────────
    var IMAGE_DATA = @json($svgImageData);
    var TEXT_DATA  = @json($svgTextData);

    // ── Inject images & text into the SVG once DOM is ready ───────────────
    document.addEventListener('DOMContentLoaded', function () {

        injectAll();
        disableZoneClicks();

    });

    function injectAll() {

        // ── Images ────────────────────────────────────────────────────────
        Object.entries(IMAGE_DATA).forEach(function(entry) {
            var key  = entry[0];
            var data = entry[1];

            data.svgs.forEach(function(side) {
                var svgEl = document.getElementById('view-' + side);
                if (!svgEl) return;

                // Create / ensure defs + clipPath
                var clipId = 'admin-clip-' + key + '-' + side;
                ensureClipPath(svgEl, clipId, data);

                // Create a <g> layer to hold the image
                var layerId = 'admin-layer-' + key + '-' + side;
                var layer = document.getElementById(layerId);
                if (!layer) {
                    layer = document.createElementNS(NS, 'g');
                    layer.setAttribute('id', layerId);
                    layer.setAttribute('pointer-events', 'none');
                    // For localTransform zones (robe) the layer gets the group transform
                    if (data.localTransform && data.groupTransform) {
                        layer.setAttribute('transform', data.groupTransform);
                    }
                    svgEl.appendChild(layer);
                }

                // Remove any existing image
                var existing = layer.querySelector('image');
                if (existing) layer.removeChild(existing);

                var imgEl = document.createElementNS(NS, 'image');
                imgEl.setAttribute('href',               data.url);
                imgEl.setAttribute('x',                  data.imgX);
                imgEl.setAttribute('y',                  data.imgY);
                imgEl.setAttribute('width',              data.imgW);
                imgEl.setAttribute('height',             data.imgH);
                imgEl.setAttribute('preserveAspectRatio','xMidYMid meet');
                imgEl.setAttribute('clip-path',          'url(#' + clipId + ')');
                // For non-local zones that have a rotation (sleeved jacket/hoodie zones)
                if (!data.localTransform && data.rotate) {
                    imgEl.setAttribute('transform', data.rotate);
                }
                layer.appendChild(imgEl);
            });
        });

        // ── Texts ─────────────────────────────────────────────────────────
        Object.entries(TEXT_DATA).forEach(function(entry) {
            var key  = entry[0];
            var data = entry[1];

            data.svgs.forEach(function(side) {
                var svgEl = document.getElementById('view-' + side);
                if (!svgEl) return;

                var layerId = 'admin-txt-layer-' + key + '-' + side;
                var layer = document.getElementById(layerId);
                if (!layer) {
                    layer = document.createElementNS(NS, 'g');
                    layer.setAttribute('id', layerId);
                    layer.setAttribute('pointer-events', 'none');
                    if (data.localTransform && data.groupTransform) {
                        layer.setAttribute('transform', data.groupTransform);
                    }
                    svgEl.appendChild(layer);
                }

                var existing = layer.querySelector('text');
                if (existing) layer.removeChild(existing);

                var textEl = document.createElementNS(NS, 'text');
                textEl.setAttribute('x',                 data.cx);
                textEl.setAttribute('y',                 data.cy);
                textEl.setAttribute('fill',              data.color);
                textEl.setAttribute('font-size',         data.fontSize);
                textEl.setAttribute('font-style',        data.fontStyle);
                textEl.setAttribute('font-weight',       '700');
                textEl.setAttribute('font-family',       'system-ui,sans-serif');
                textEl.setAttribute('text-anchor',       'middle');
                textEl.setAttribute('dominant-baseline', 'central');
                textEl.setAttribute('letter-spacing',    '0.04em');
                if (!data.localTransform && data.rotate) {
                    textEl.setAttribute('transform', data.rotate);
                }
                textEl.textContent = data.text;
                layer.appendChild(textEl);
            });
        });
    }

    function ensureClipPath(svgEl, clipId, data) {
        if (svgEl.querySelector('#' + clipId)) return;

        var defs = svgEl.querySelector('defs');
        if (!defs) {
            defs = document.createElementNS(NS, 'defs');
            svgEl.insertBefore(defs, svgEl.firstChild);
        }

        var clip = document.createElementNS(NS, 'clipPath');
        clip.setAttribute('id', clipId);
        clip.setAttribute('clipPathUnits', 'userSpaceOnUse');
        if (data.localTransform && data.groupTransform) {
            clip.setAttribute('transform', data.groupTransform);
        }

        var rect = document.createElementNS(NS, 'rect');
        rect.setAttribute('x',      data.imgX);
        rect.setAttribute('y',      data.imgY);
        rect.setAttribute('width',  data.imgW);
        rect.setAttribute('height', data.imgH);
        if (!data.localTransform && data.rotate) {
            rect.setAttribute('transform', data.rotate);
        }
        clip.appendChild(rect);
        defs.appendChild(clip);
    }

    // ── Front / Back toggle ───────────────────────────────────────────────
    window.adminPreviewToggle = function(view, clickedBtn) {
        var front = document.getElementById('view-front');
        var back  = document.getElementById('view-back');
        if (front) front.style.display = view === 'front' ? '' : 'none';
        if (back)  back.style.display  = view === 'back'  ? '' : 'none';

        ['btn-front','btn-back'].forEach(function(id) {
            var b = document.getElementById(id);
            if (!b) return;
            b.classList.remove('bg-white','shadow-sm','text-gray-900');
            b.classList.add('text-gray-500');
        });
        clickedBtn.classList.add('bg-white','shadow-sm','text-gray-900');
        clickedBtn.classList.remove('text-gray-500');
    };

    // ── Disable zone clicks — admin is read-only ──────────────────────────
    function disableZoneClicks() {
        document.querySelectorAll('[data-zone]').forEach(function(el) {
            el.style.cursor = 'default';
            el.setAttribute('onclick', '');
            el.removeAttribute('tabindex');
            el.removeAttribute('onkeydown');
        });
    }

})();
</script>

@endsection