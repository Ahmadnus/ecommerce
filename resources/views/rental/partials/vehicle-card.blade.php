@php
    /**
     * @var \App\Models\Vehicle $vehicle
     * @var array $search  Search state carried through so dates survive the click
     */
    $search = $search ?? [];
    $query  = array_filter([
        'pickup_location_id' => $search['pickup_location_id'] ?? null,
        'return_location_id' => $search['return_location_id'] ?? null,
        'pickup_date'        => $search['pickup_date'] ?? null,
        'pickup_time'        => $search['pickup_time'] ?? null,
        'return_date'        => $search['return_date'] ?? null,
        'return_time'        => $search['return_time'] ?? null,
        'different_location' => ($search['different_location'] ?? false) ? 1 : null,
    ], fn($v) => $v !== null && $v !== '');

    $showUrl = route('rental.vehicles.show', array_merge(['slug' => $vehicle->slug], $query));
    $bookUrl = route('rental.booking.create', array_merge(['slug' => $vehicle->slug], $query));
    $image   = $vehicle->main_image_url;
@endphp

<article class="group bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col
                hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

    {{-- Image --}}
    <a href="{{ $showUrl }}" class="relative block aspect-[16/10] bg-gray-50 overflow-hidden">
        @if($image)
            {{-- object-cover: fleet listings are photographs, so filling the
                 frame reads better than letterboxing a contained image. --}}
            <img src="{{ $image }}" alt="{{ $vehicle->full_title }}" loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <i class="fa-solid fa-car text-6xl"></i>
            </div>
        @endif

        @if($vehicle->is_on_sale)
            <span class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }}
                         bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                -{{ $vehicle->discount_percentage }}%
            </span>
        @endif

        @if($vehicle->category)
            <span class="absolute top-3 {{ app()->getLocale() === 'ar' ? 'left-3' : 'right-3' }}
                         bg-white/95 text-ink text-[11px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                {{ $vehicle->category->name }}
            </span>
        @endif
    </a>

    {{-- Body --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ $showUrl }}" class="block">
            <h3 class="font-bold text-base text-ink leading-snug hover:text-accent transition-colors">
                {{ $vehicle->title }}
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ $vehicle->year }}</p>
        </a>

        {{-- Spec badges --}}
        <div class="flex flex-wrap gap-1.5 mt-3">
            @foreach([
                ['fa-solid fa-user-group', __('rental.seats', ['count' => $vehicle->seats])],
                ['fa-solid fa-gear',       $vehicle->transmission_label],
                ['fa-solid fa-gas-pump',   $vehicle->fuel_label],
                ['fa-solid fa-suitcase',   __('rental.bags', ['count' => $vehicle->bags])],
            ] as [$icon, $label])
                <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-100
                             text-gray-600 text-[11px] font-medium px-2 py-1 rounded-md">
                    <i class="{{ $icon }} text-accent text-[10px]"></i>{{ $label }}
                </span>
            @endforeach
        </div>

        {{-- Price + CTA --}}
        <div class="mt-auto pt-4 flex items-end justify-between gap-3">
            <div>
                @if($vehicle->is_on_sale)
                    <span class="block text-xs text-gray-400 line-through">
                        {{ number_format((float) $vehicle->daily_rate, 0) }}
                    </span>
                @endif
                <span class="text-xl font-black text-accent">
                    {{ number_format($vehicle->effective_daily_rate, 0) }}
                </span>
                <span class="text-xs font-semibold text-gray-500">
                    {{ __('rental.currency') }} {{ __('rental.per_day') }}
                </span>
            </div>

            <a href="{{ $bookUrl }}"
               class="shrink-0 bg-accent hover:bg-accent-600 text-white text-sm font-bold
                      px-5 py-2.5 rounded-md transition-colors">
                {{ __('rental.book_now') }}
            </a>
        </div>
    </div>
</article>
