{{--
    resources/views/components/currency-switcher.blade.php
    ─────────────────────────────────────────────────────────────────────────────
    Renders a currency dropdown for the navbar.
    Switching reloads the current page with ?currency=USD appended.

    Usage:
        <x-currency-switcher />

    The component reads $activeCurrency from the view share.
    It fetches all active currencies from CurrencyService::allActive().

    Design: matches the existing navbar style (see partials/navbar.blade.php).
    Uses AlpineJS x-data dropdown (already loaded in layouts/app.blade.php).
--}}

@php
    $currencies  = app(\App\Services\CurrencyService::class)->allActive();
    $active      = $activeCurrency;
@endphp

@if($currencies->count() > 1)
<div class="relative" x-data="{ open: false }">

    {{-- Trigger button --}}
    <button type="button"
            @click="open = !open"
            @click.outside="open = false"
            class="flex items-center gap-1.5 text-sm font-semibold text-gray-600
                   hover:text-gray-900 transition-colors px-2 py-1 rounded-lg
                   hover:bg-gray-100 select-none">

        {{-- Active currency code --}}
        <span class="text-xs font-black tabular-nums" style="color:var(--brand-color,#0ea5e9)">
            {{ $active->code }}
        </span>

        {{-- Active symbol --}}
        <span class="text-gray-400 text-xs">{{ $active->symbol }}</span>

        {{-- Chevron --}}
        <svg class="w-3 h-3 text-gray-400 transition-transform duration-150"
             :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute top-full mt-1 left-0 z-50 w-44
                bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/8
                overflow-hidden py-1"
         style="display:none">

        @foreach($currencies as $currency)
        @php
            $isActive = $currency->code === $active->code;
            // Append ?currency=CODE to the current URL, preserving other query params
            $switchUrl = request()->fullUrlWithQuery(['currency' => $currency->code]);
        @endphp
        <a href="{{ $switchUrl }}"
           class="flex items-center justify-between px-3.5 py-2.5 text-sm
                  hover:bg-gray-50 transition-colors
                  {{ $isActive ? 'bg-gray-50' : '' }}"
           @click="open = false">

            <div class="flex items-center gap-2">
                {{-- Symbol badge --}}
                <span class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center flex-shrink-0
                              {{ $isActive ? 'text-white' : 'bg-gray-100 text-gray-600' }}"
                      style="{{ $isActive ? 'background:var(--brand-color,#0ea5e9)' : '' }}">
                    {{ Str::limit($currency->symbol, 3) }}
                </span>
                <div>
                    <p class="font-semibold text-gray-800 text-xs leading-tight">{{ $currency->code }}</p>
                    <p class="text-[10px] text-gray-400 leading-none mt-0.5">{{ Str::limit($currency->name, 18) }}</p>
                </div>
            </div>

            {{-- Active checkmark --}}
            @if($isActive)
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="color:var(--brand-color,#0ea5e9)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            @endif

        </a>
        @endforeach
    </div>
</div>
@endif