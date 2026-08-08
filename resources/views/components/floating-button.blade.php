{{--
    resources/views/components/floating-button.blade.php
    ────────────────────────────────────────────────────────
    Floating WhatsApp contact button.

    Visibility is a data decision, not a styling one: the admin turns it on by
    ticking "floating" on a social link in the dashboard, and the layout only
    renders this component when such a link exists and is active. The component
    re-checks the number itself so it can never emit a dead wa.me link.

    It was previously `md:hidden`, i.e. merely hidden by CSS on desktop while
    still present in the markup. It now shows on every viewport.

    Props:
        $number  string|null  WhatsApp number (raw, from the DB)
        $label   string|null  Tooltip text; falls back to the contact string
--}}

@props([
    'number' => null,
    'label'  => null,
])

@php
    // Digits only, so a number stored as "+962 79 000 0000" still links.
    $cleanNumber = preg_replace('/\D+/', '', (string) $number);

    /*
     * Jordanian mobiles are 9 digits national (7XXXXXXXX) or 10 with the trunk
     * zero. Normalise both to the +962 international form wa.me expects.
     */
    if (str_starts_with($cleanNumber, '00962')) {
        $cleanNumber = substr($cleanNumber, 2);
    } elseif (str_starts_with($cleanNumber, '0')) {
        $cleanNumber = '962' . ltrim($cleanNumber, '0');
    } elseif (strlen($cleanNumber) === 9 && str_starts_with($cleanNumber, '7')) {
        $cleanNumber = '962' . $cleanNumber;
    }

    $hasNumber = strlen($cleanNumber) >= 10;
    $tooltip   = $label ?: __('rental.whatsapp_cta');
    $rtl       = app()->getLocale() === 'ar';
@endphp

@if($hasNumber)
    <a href="https://wa.me/{{ $cleanNumber }}"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="{{ $tooltip }}"
       class="group fixed bottom-6 {{ $rtl ? 'left-4' : 'right-4' }} z-50
              flex items-center justify-center w-14 h-14
              bg-[#25D366] hover:bg-[#1fbb58] text-white rounded-full
              shadow-2xl shadow-green-500/40 ring-2 ring-white/30
              hover:scale-110 active:scale-95 transition-all duration-300">

        <svg class="w-7 h-7 relative z-10" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15
                     -.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075
                     -.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059
                     -.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52
                     .149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52
                     -.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51
                     -.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372
                     -.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074
                     .149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625
                     .712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413
                     .248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z
                     M12.004 2.003C6.479 2.003 2 6.481 2 12.005c0 1.762.461 3.476 1.34 4.988
                     L2 22l5.134-1.321A10.006 10.006 0 0012.004 22c5.525 0 10.004-4.478
                     10.004-10.004 0-5.524-4.479-10.003-10.004-10.003z"/>
        </svg>

        {{-- Tooltip opens toward the middle of the screen in both directions. --}}
        <span class="absolute {{ $rtl ? 'left-16' : 'right-16' }} top-1/2 -translate-y-1/2
                     bg-gray-900/90 text-white text-[11px] font-bold
                     px-3 py-1.5 rounded-lg whitespace-nowrap pointer-events-none shadow-lg
                     opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            {{ $tooltip }}
        </span>

        <span class="absolute inset-0 rounded-full bg-green-400/30 animate-ping pointer-events-none"
              aria-hidden="true"></span>
    </a>
@endif
