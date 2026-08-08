@props(['class' => 'w-6 h-4'])

{{--
    Flag of the Hashemite Kingdom of Jordan. Drawn inline rather than using the
    🇯🇴 emoji, which Windows does not render as a flag at all — it falls back to
    the letters "JO". Three equal bands (black / white / green), a red hoist
    chevron, and the white seven-pointed star.
--}}
<svg {{ $attributes->merge(['class' => $class]) }}
     viewBox="0 0 12 6" role="img" aria-label="{{ __('rental.country_jordan') }}"
     xmlns="http://www.w3.org/2000/svg">
    <rect width="12" height="2" y="0" fill="#000000"/>
    <rect width="12" height="2" y="2" fill="#FFFFFF"/>
    <rect width="12" height="2" y="4" fill="#007A3D"/>
    <path d="M0 0 L6 3 L0 6 Z" fill="#CE1126"/>
    <polygon fill="#FFFFFF" points="2.4,2.45 2.504,2.784 2.83,2.657 2.634,2.947 2.936,3.122 2.588,3.15 2.639,3.496 2.4,3.24 2.161,3.496 2.212,3.15 1.864,3.122 2.166,2.947 1.97,2.657 2.296,2.784"/>
</svg>
