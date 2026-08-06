{{--
    admin/orders/partials/customization.blade.php

    Inline panel showing full customization data for an order.
    Include this inside your existing admin order detail page:

        @include('admin.orders.partials.customization', [
            'customization' => $customization,
            'config'        => $config,
            'orderId'       => $order->id,
        ])

    Or load it via the route:  admin/orders/{orderId}/customization
--}}

@php
    use Illuminate\Support\Facades\Storage;
    /** @var \App\Models\OrderCustomization $customization */
    /** @var \App\Models\ProductCustomization $config */
    $uploadsByZone = $customization->uploads->groupBy('zone_key');
    $statusColors  = [
        'pending'    => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
        'ready'      => 'bg-green-100 text-green-800 border-green-200',
        'error'      => 'bg-red-100 text-red-800 border-red-200',
    ];
    $statusClass = $statusColors[$customization->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

    {{-- ── Panel header ──────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
         style="background:#f9fafb;">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-sm">تفاصيل التخصيص</h3>
                <p class="text-xs text-gray-400">#{{ $customization->id }} · {{ $customization->product?->name ?? 'منتج تجريبي' }}</p>
            </div>
        </div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full border {{ $statusClass }}">
            {{ $customization->statusLabel() }}
        </span>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ── Colors ────────────────────────────────────────────────────── --}}
        @if(! empty($customization->colors))
        <div>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">الألوان المختارة</h4>
            <div class="flex flex-col gap-2">
                @php
                    $colorAreaLabels = [
                        'body'   => 'الجسم',
                        'sleeve' => 'الأكمام',
                        'rib'    => 'الأطواق',
                        'stripe' => 'الخطوط',
                    ];
                @endphp
                @foreach($customization->colors as $area => $hex)
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-md border border-gray-200 shadow-sm flex-shrink-0"
                         style="background: {{ $hex }};"></div>
                    <div>
                        <span class="text-sm text-gray-700">{{ $colorAreaLabels[$area] ?? $area }}</span>
                        <span class="text-xs text-gray-400 ltr font-mono mr-2" dir="ltr">{{ $hex }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Selected zones + texts ──────────────────────────────────────── --}}
        @if(! empty($customization->selected_zones))
        <div>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">المناطق المستخدمة</h4>
            <div class="flex flex-col gap-2">
                @foreach($customization->selected_zones as $zoneKey)
                    @php $zoneDef = $config->zoneByKey($zoneKey); @endphp
                    <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-block bg-blue-600 text-white text-[10px] font-bold
                                             px-2 py-0.5 rounded-full">{{ $zoneKey }}</span>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $zoneDef['label'] ?? $zoneKey }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">
                                @php $zoneType = $zoneDef['type'] ?? 'both'; @endphp
                                {{ $zoneType === 'text' ? 'نص' : ($zoneType === 'image' ? 'صورة' : 'نص وصورة') }}
                            </span>
                        </div>

                        {{-- Text for this zone --}}
                        @php $zoneText = $customization->textForZone($zoneKey); @endphp
                        @if($zoneText)
                        <p class="text-sm text-gray-900 font-semibold mt-1">
                            "{{ $zoneText }}"
                        </p>
                        @endif

                        {{-- Uploads for this zone --}}
                        @if($uploadsByZone->has($zoneKey))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($uploadsByZone[$zoneKey] as $upload)
                            <div class="relative group">
                                <img src="{{ $upload->url() }}"
                                     alt="صورة المنطقة {{ $zoneKey }}"
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                <a href="{{ $upload->url() }}"
                                   target="_blank"
                                   class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50
                                          rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                                   title="عرض كاملاً">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <p class="text-[10px] text-gray-400 mt-0.5 text-center truncate w-16">
                                    {{ $upload->formattedSize() }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Customer notes ──────────────────────────────────────────────── --}}
        @if($customization->notes)
        <div class="md:col-span-2">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">ملاحظات العميل</h4>
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-gray-800">
                {{ $customization->notes }}
            </div>
        </div>
        @endif

        {{-- ── Phase 2 rendered preview (empty in Phase 1) ─────────────────── --}}
        @if($customization->rendered_preview_path)
        <div class="md:col-span-2">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">المعاينة النهائية</h4>
            <img src="{{ Storage::disk('public')->url($customization->rendered_preview_path) }}"
                 alt="المعاينة النهائية"
                 class="max-w-xs rounded-xl border border-gray-200 shadow-sm">
        </div>
        @else
        <div class="md:col-span-2 bg-gray-50 border border-dashed border-gray-300 rounded-xl px-4 py-5 text-center">
            <p class="text-xs text-gray-400">
                المعاينة النهائية ستظهر هنا بعد معالجة التصميم (المرحلة الثانية)
            </p>
        </div>
        @endif

    </div>

    {{-- ── Footer: timestamps ──────────────────────────────────────────────── --}}
    <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400"
         style="background:#f9fafb;">
        <span>أُنشئ: {{ $customization->created_at->format('Y-m-d H:i') }}</span>
        <span>آخر تحديث: {{ $customization->updated_at->format('Y-m-d H:i') }}</span>
    </div>
</div>