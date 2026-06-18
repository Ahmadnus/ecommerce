{{--
  customize/index-svgs/stole-thumb.blade.php
  Compact SVG thumbnail of the graduation stole for the product picker.
  Matches the corrected stole shape: flat top, straight sides, V-neck, sharp tips.
--}}
<svg viewBox="0 0 200 280" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">

  <defs>
    <clipPath id="sth-l-clip">
      <polygon points="22,52 78,52 78,252 50,270 22,252"/>
    </clipPath>
    <clipPath id="sth-r-clip">
      <polygon points="122,52 178,52 178,252 150,270 122,252"/>
    </clipPath>
  </defs>

  {{-- Left panel body --}}
  <polygon points="22,52 78,52 78,252 50,270 22,252"
           fill="#111111"/>

  {{-- Left panel left border stripe --}}
  <polygon points="22,52 30,52 30,249 50,266 50,270 22,252"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- Left panel right border stripe (beside V gap) --}}
  <polygon points="70,52 78,52 78,252 50,270 50,266 70,249"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- Left panel top border --}}
  <line x1="22" y1="52" x2="78" y2="52"
        stroke="#d4a017" stroke-width="7" stroke-linecap="butt"/>

  {{-- Left panel outline --}}
  <polygon points="22,52 78,52 78,252 50,270 22,252"
           fill="none"
           stroke="#d4a017" stroke-width="1.5" stroke-linejoin="round"/>

  {{-- Right panel body --}}
  <polygon points="122,52 178,52 178,252 150,270 122,252"
           fill="#111111"/>

  {{-- Right panel left border stripe (beside V gap) --}}
  <polygon points="122,52 130,52 130,249 150,266 150,270 122,252"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- Right panel right border stripe --}}
  <polygon points="170,52 178,52 178,252 150,270 150,266 170,249"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- Right panel top border --}}
  <line x1="122" y1="52" x2="178" y2="52"
        stroke="#d4a017" stroke-width="7" stroke-linecap="butt"/>

  {{-- Right panel outline --}}
  <polygon points="122,52 178,52 178,252 150,270 122,252"
           fill="none"
           stroke="#d4a017" stroke-width="1.5" stroke-linejoin="round"/>

  {{-- V-neck gap fill (transparent/white so background shows) --}}
  <polygon points="78,52 100,118 122,52"
           fill="white"/>

  {{-- V-neck left gold stripe --}}
  <polygon points="78,52 70,52 92,116 100,118"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- V-neck right gold stripe --}}
  <polygon points="122,52 130,52 108,116 100,118"
           fill="#d4a017"
           style="pointer-events:none;"/>

  {{-- V-neck outer outline --}}
  <polyline points="78,52 100,118 122,52"
            fill="none"
            stroke="#d4a017" stroke-width="1.8"
            stroke-linejoin="round" stroke-linecap="round"/>

  {{-- V-neck inner crease --}}
  <polyline points="70,52 92,116 100,118 108,116 130,52"
            fill="none"
            stroke="#d4a017" stroke-width="1.2"
            stroke-linejoin="round" stroke-linecap="round"
            opacity="0.7"/>

  {{-- Zone A hint (left upper) --}}
  <rect x="31" y="60" width="40" height="45" rx="2"
        fill="none" stroke="rgba(59,130,246,0.35)" stroke-width="0.9" stroke-dasharray="3 2"/>
  <text x="51" y="84" text-anchor="middle" dominant-baseline="central"
        font-size="9" font-weight="700" fill="rgba(59,130,246,0.5)"
        font-family="system-ui,sans-serif">A</text>

  {{-- Zone C hint (right upper) --}}
  <rect x="129" y="60" width="40" height="45" rx="2"
        fill="none" stroke="rgba(59,130,246,0.35)" stroke-width="0.9" stroke-dasharray="3 2"/>
  <text x="149" y="84" text-anchor="middle" dominant-baseline="central"
        font-size="9" font-weight="700" fill="rgba(59,130,246,0.5)"
        font-family="system-ui,sans-serif">C</text>

</svg>