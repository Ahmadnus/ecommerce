{{--
    products/_sort-drawer.blade.php — mobile bottom-sheet sort options.
    Opened/closed via openSortDrawer()/closeSortDrawer() (see _scripts).
    Needs: $isRtl.
--}}
<div class="sort-drawer-overlay" id="sort-overlay" onclick="closeSortDrawer()">
    <div class="sort-drawer" id="sort-drawer" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-2">
            <p class="font-bold text-gray-900">{{ __('app.sort_by') }}</p>
            <button onclick="closeSortDrawer()" class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        @php
        $sortOptions = [
            'featured'   => ['label' => __('app.sort_featured'),   'icon' => '⭐'],
            'price_asc'  => ['label' => __('app.sort_price_asc'),  'icon' => '↑'],
            'price_desc' => ['label' => __('app.sort_price_desc'), 'icon' => '↓'],
            'newest'     => ['label' => __('app.sort_newest'),     'icon' => '🆕'],
        ];
        @endphp

        @foreach($sortOptions as $val => $opt)
        @php $isChosen = request('sort', 'featured') === $val; @endphp
        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}" onclick="closeSortDrawer()"
           class="sort-option {{ $isChosen ? 'chosen' : '' }}">
            <span class="flex items-center gap-2">
                <span class="text-base">{{ $opt['icon'] }}</span>
                {{ $opt['label'] }}
            </span>
            @if($isChosen)
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--brand)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            @endif
        </a>
        @endforeach
    </div>
</div>
