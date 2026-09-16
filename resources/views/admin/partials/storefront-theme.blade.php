{{--
    admin/partials/storefront-theme.blade.php

    Editor for the storefront design tokens. The field list is generated from
    StorefrontThemeHelper rather than written out by hand, so adding a token
    to the helper makes it appear here (and get saved by SettingService)
    without touching this file.
--}}

@php
    use App\Helpers\StorefrontThemeHelper;

    $tokens = StorefrontThemeHelper::all();

    // Arabic labels for the admin UI, matching the rest of this page.
    $labels = [
        'accent_color'         => 'لون التخفيضات والتنبيهات',
        'border_color'         => 'لون الحدود والفواصل',
        'subtle_bg_color'      => 'الخلفية الهادئة (الأزرار والشرائح)',
        'badge_bg_color'       => 'خلفية الشارات',
        'footer_bottom_bg'     => 'خلفية شريط حقوق النشر',

        'radius_card'          => 'انحناء زوايا الكروت',
        'radius_button'        => 'انحناء زوايا الأزرار',
        'radius_input'         => 'انحناء زوايا الحقول',
        'radius_badge'         => 'انحناء زوايا الشارات',
        'card_image_height'    => 'ارتفاع صورة المنتج (سطح المكتب)',
        'card_image_height_sm' => 'ارتفاع صورة المنتج (الجوال)',
        'card_border_width'    => 'سماكة حدود الكرت',
        'container_max_width'  => 'أقصى عرض للمحتوى',
        'section_gap'          => 'المسافة بين الأقسام',
        'header_height'        => 'ارتفاع الهيدر',

        'card_image_fit'       => 'طريقة عرض صورة المنتج',
        'header_brand_mode'    => 'شعار المتجر في الهيدر',

        'shadow_card'          => 'ظل الكرت',
        'shadow_card_hover'    => 'ظل الكرت عند المرور',
        'shadow_overlay'       => 'ظل النوافذ المنبثقة',
    ];

    $label = fn (string $key) => $labels[$key] ?? $key;
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-sm">
                <i class="fas fa-palette"></i>
            </span>
            هوية واجهة المتجر
        </h2>
        <p class="text-xs text-gray-400 mt-2">
            تتحكم هذه القيم بشكل الواجهة بالكامل (الكروت، الأزرار، الحدود، الظلال، الصور).
            يتم تطبيقها فوراً على كل صفحات المتجر دون تعديل أي ملف.
        </p>
    </div>

    <div class="p-8 space-y-8">

        {{-- Colours --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">الألوان</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach(StorefrontThemeHelper::colorKeys() as $key => $default)
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/30 space-y-3">
                        <label for="tk-{{ $key }}"
                               class="block text-xs font-bold text-gray-500 uppercase tracking-wider">
                            {{ $label($key) }}
                        </label>
                        <input type="color"
                               id="tk-{{ $key }}"
                               name="{{ $key }}"
                               value="{{ $tokens[$key] }}"
                               class="h-12 w-full rounded-lg cursor-pointer border-0 shadow-sm">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Geometry --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">الأبعاد والانحناءات</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach(StorefrontThemeHelper::sizeKeys() as $key => $default)
                    <div class="space-y-2">
                        <label for="tk-{{ $key }}" class="block text-xs font-bold text-gray-500">
                            {{ $label($key) }}
                        </label>
                        <input type="text"
                               id="tk-{{ $key }}"
                               name="{{ $key }}"
                               value="{{ $tokens[$key] }}"
                               placeholder="{{ $default }}"
                               dir="ltr"
                               class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand">
                        <p class="text-[11px] text-gray-400">الافتراضي: {{ $default }}</p>
                    </div>
                @endforeach

                @foreach(StorefrontThemeHelper::enumKeys() as $key => [$default, $allowed])
                    <div class="space-y-2">
                        <label for="tk-{{ $key }}" class="block text-xs font-bold text-gray-500">
                            {{ $label($key) }}
                        </label>
                        <select id="tk-{{ $key }}" name="{{ $key }}" dir="ltr"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand">
                            @foreach($allowed as $option)
                                <option value="{{ $option }}" @selected($tokens[$key] === $option)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400">
                            @if($key === 'card_image_fit')
                                contain: تُظهر المنتج كاملاً — cover: تملأ الإطار
                            @elseif($key === 'header_brand_mode')
                                auto: الشعار إن وُجد وإلا الاسم — logo: الشعار دائماً —
                                text: اسم المتجر دائماً — both: الاثنان معاً.
                                (ارفع الشعار من قسم "الشعار" أعلى الصفحة، وغيّر الاسم من "اسم المتجر")
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Shadows --}}
        <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">الظلال</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach(StorefrontThemeHelper::shadowKeys() as $key => $default)
                    <div class="space-y-2">
                        <label for="tk-{{ $key }}" class="block text-xs font-bold text-gray-500">
                            {{ $label($key) }}
                        </label>
                        <input type="text"
                               id="tk-{{ $key }}"
                               name="{{ $key }}"
                               value="{{ $tokens[$key] }}"
                               placeholder="{{ $default }}"
                               dir="ltr"
                               class="w-full rounded-xl border-gray-200 text-sm font-mono focus:border-brand focus:ring-brand">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
