@extends('layouts.admin')
@section('title', 'منشئ مكعبات الصفحة الرئيسية')

@section('admin-content')
@php
    // Visual identity per cube type for the layout map.
    $cubeStyles = [
        'hero_banner'     => ['bg' => 'bg-violet-100 border-violet-300 text-violet-800',   'icon' => '🎬', 'short' => 'بانر رئيسي'],
        'banner'          => ['bg' => 'bg-violet-50 border-violet-200 text-violet-700',    'icon' => '🖼', 'short' => 'بانر مقسم'],
        'portrait_media'  => ['bg' => 'bg-rose-100 border-rose-300 text-rose-800',         'icon' => '📱', 'short' => 'بلوك طولي'],
        'custom_media'    => ['bg' => 'bg-amber-100 border-amber-300 text-amber-800',      'icon' => '🖼', 'short' => 'وسائط حرة'],
        'custom_image'    => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700',       'icon' => '🖼', 'short' => 'صورة مستقلة'],
        'pure_text_cta'   => ['bg' => 'bg-sky-100 border-sky-300 text-sky-800',            'icon' => '✍️', 'short' => 'نص / زر حر'],
        'text_block'      => ['bg' => 'bg-sky-50 border-sky-200 text-sky-700',             'icon' => '✍️', 'short' => 'كتلة نصية'],
        'categories_grid' => ['bg' => 'bg-emerald-100 border-emerald-300 text-emerald-800','icon' => '🗂', 'short' => 'التصنيفات'],
        'product_grid'    => ['bg' => 'bg-orange-100 border-orange-300 text-orange-800',   'icon' => '🛍', 'short' => 'شبكة منتجات'],
    ];
    $cubeStyleFor = fn ($t) => $cubeStyles[$t] ?? ['bg' => 'bg-gray-100 border-gray-300 text-gray-700', 'icon' => '◻️', 'short' => $t];
@endphp

<div class="space-y-8">

    {{-- ══════════════════════════════════════════════════════
         VISUAL LAYOUT MAP — the page as ordered cubes
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-lg font-bold">خريطة الصفحة الرئيسية — المكعبات بترتيب الظهور</h3>
            <a href="{{ url('/products') }}" target="_blank"
               class="text-xs font-bold text-brand hover:underline">معاينة الصفحة ↗</a>
        </div>
        <p class="text-xs text-gray-400 mb-4">
            كل بطاقة أدناه = مكعب مستقل في الصفحة. الرقم هو "ترتيب الظهور" — عدّله من نموذج المكعب لتحريكه لأي مكان.
        </p>

        @if($items->isEmpty())
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center text-gray-400 text-sm">
                لا توجد مكعبات بعد — أضف أول مكعب من النموذج بالأسفل.
            </div>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($items->sortBy([['sort_order','asc'],['id','asc']]) as $cube)
                    @php $cs = $cubeStyleFor($cube->section_type); @endphp
                    <button type="button" onclick="toggleEdit({{ $cube->id }})"
                            class="relative border {{ $cs['bg'] }} {{ $cube->is_active ? '' : 'opacity-40 grayscale' }}
                                   rounded-xl px-3 py-2.5 text-right hover:scale-[1.03] transition-transform cursor-pointer"
                            title="{{ $cube->typeLabel() }}">
                        <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-gray-900 text-white text-[10px]
                                     font-black flex items-center justify-center shadow">{{ $cube->sort_order }}</span>
                        <span class="block text-base leading-none mb-1">{{ $cs['icon'] }}</span>
                        <span class="block text-[11px] font-black whitespace-nowrap">{{ $cs['short'] }}</span>
                        <span class="block text-[10px] max-w-[110px] truncate {{ $cube->title ? '' : 'opacity-40' }}">
                            {{ $cube->title ?: 'بدون عنوان' }}
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════
         ADD CUBE
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold mb-4">➕ إضافة مكعب جديد</h3>

        <form action="{{ route('admin.homepage-sections.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.homepage-sections._form', ['item' => null])
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════
         CUBES TABLE (details + inline edit)
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b text-gray-500 text-sm">
                <tr>
                    <th class="px-6 py-4">المعاينة</th>
                    <th class="px-6 py-4">العنوان</th>
                    <th class="px-6 py-4">نوع المكعب</th>
                    <th class="px-6 py-4">الترتيب</th>
                    <th class="px-6 py-4">الوسائط</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراء</th>
                </tr>
            </thead>

            <tbody class="divide-y text-sm">
                @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        @if($item->media_type === 'video' && $item->effectiveVideoUrl())
                            <video class="w-16 h-20 object-cover rounded-lg shadow-sm" muted preload="metadata">
                                <source src="{{ $item->effectiveVideoUrl() }}">
                            </video>
                        @elseif($item->hasMedia() && $item->media_path)
                            <img src="{{ $item->media_url }}"
                                 class="w-16 h-20 object-cover rounded-lg shadow-sm" alt="">
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 max-w-[220px] truncate">{{ $item->title ?: '—' }}</td>

                    <td class="px-6 py-4">
                        @php $cs = $cubeStyleFor($item->section_type); @endphp
                        <span class="text-xs px-2 py-1 rounded border whitespace-nowrap {{ $cs['bg'] }}">
                            {{ $cs['icon'] }} {{ $cs['short'] }}
                        </span>
                        @if($item->isProductGrid() && $item->productSourceLabel())
                        <span class="block text-[10px] text-gray-400 mt-1">{{ $item->productSourceLabel() }}</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-gray-700">{{ $item->sort_order }}</span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-100">
                            {{ ['image' => 'صورة', 'video' => 'فيديو', 'none' => 'بدون'][$item->media_type] ?? $item->media_type }}
                        </span>
                        @if($item->media_type === 'video' && ! $item->media_path && $item->video_url)
                        <span class="block text-[10px] text-gray-400 mt-1">رابط خارجي</span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <span class="{{ $item->is_active ? 'text-green-600' : 'text-red-400' }}">
                            {{ $item->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-4 items-center">
                            <button type="button" onclick="toggleEdit({{ $item->id }})"
                                    class="text-blue-500 hover:underline">تعديل</button>

                            <form action="{{ route('admin.homepage-sections.destroy', $item) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المكعب؟')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline" type="submit">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- ── Inline edit row (same shared form) ───────────── --}}
                <tr id="edit-{{ $item->id }}" class="hidden bg-gray-50">
                    <td colspan="7" class="p-6">
                        <form action="{{ route('admin.homepage-sections.update', $item) }}"
                              method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf @method('PUT')
                            @include('admin.homepage-sections._form', ['item' => $item])
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">لا توجد مكعبات بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    if (!el) return;
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

/*
 * Show only the fields relevant to the chosen cube type:
 *   hero_banner / portrait_media / custom_media (+ legacy banner,
 *   custom_image) → media file/URL + frame + text-position controls
 *   product_grid  → product source
 *   pure_text_cta / text_block / categories_grid → text-only surface
 * A video media_type additionally reveals the external video-URL field.
 */
const MEDIA_TYPES = ['hero_banner', 'portrait_media', 'custom_media', 'custom_image', 'banner'];

function applySectionType(select) {
    const form = select.closest('form');
    if (!form) return;
    const type = select.value;
    const usesMedia = MEDIA_TYPES.includes(type);
    const mediaTypeSel = form.querySelector('[name="media_type"]');
    const isVideo = usesMedia && mediaTypeSel && mediaTypeSel.value === 'video';

    const setVisible = (selector, visible) => {
        form.querySelectorAll(selector).forEach(el => { el.style.display = visible ? '' : 'none'; });
    };

    setVisible('.js-media-type', usesMedia);
    setVisible('.js-media-file', usesMedia);
    setVisible('.js-media-layout', usesMedia);
    setVisible('.js-video-url', isVideo);
    setVisible('.js-product-source', type === 'product_grid');
}

document.querySelectorAll('.section-type-select').forEach(sel => {
    applySectionType(sel);
    sel.addEventListener('change', () => {
        applySectionType(sel);
        // Portrait cube convenience: preselect the tall magazine frame
        // (still freely overridable by the admin).
        if (sel.value === 'portrait_media') {
            const ratio = sel.closest('form')?.querySelector('[name="aspect_ratio"]');
            if (ratio) ratio.value = 'portrait';
        }
    });
});
document.querySelectorAll('.section-media-type').forEach(sel => {
    sel.addEventListener('change', () => {
        const typeSel = sel.closest('form')?.querySelector('.section-type-select');
        if (typeSel) applySectionType(typeSel);
    });
});

// Background color: checkbox enables the picker; a disabled input is not
// submitted, which keeps the stored value NULL (transparent background).
document.querySelectorAll('.js-bg-toggle').forEach(cb => {
    cb.addEventListener('change', () => {
        const color = cb.closest('div')?.querySelector('.js-bg-color');
        if (color) color.disabled = !cb.checked;
    });
});
</script>
@endsection
