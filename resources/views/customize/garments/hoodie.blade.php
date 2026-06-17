{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/garments/hoodie.blade.php                                       ║
║                                                                            ║
║  EXACT replica of the "Studio Hoodie · Placement Zone Configurator"        ║
║  reference HTML — full fleece texture, hood, drawstrings, kangaroo pocket, ║
║  back hood, seams, stitches, cuffs, waistband.                             ║
║                                                                            ║
║  Wired for the Live Preview Engine (show.blade.php / Alpine.js):           ║
║    • CSS vars --c-body / --c-sleeve / --c-rib / --c-stripe                 ║
║      drive all fill/stroke colours.                                        ║
║    • Zone groups dispatch zone:open custom events (caught by Alpine).      ║
║    • .zone-outline / .zone-badge driven by CSS in show.blade.php.          ║
║    • Content layers injected at runtime by designEngine().                 ║
║                                                                            ║
║  Variables:                                                                ║
║    $zones       – zone defs array [['key','label','type'], …]              ║
║    $zoneCoords  – coordinate map passed from show.blade.php                ║
║    $defaults    – ['body'=>'#hex','sleeve'=>'#hex','rib'=>'#hex',…]        ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $activeZoneKeys = array_column($zones, 'key');
    $isZone = fn(string $k) => in_array($k, $activeZoneKeys, true);
@endphp

<style>
#garment-wrapper {
    --c-body:   {{ $defaults['body']   ?? '#2b3a4a' }};
    --c-sleeve: {{ $defaults['sleeve'] ?? '#2b3a4a' }};
    --c-rib:    {{ $defaults['rib']    ?? '#23303e' }};
    --c-stripe: {{ $defaults['stripe'] ?? '#ffffff' }};
}
/* Mirror the .outline / .seam / .stitch classes from the reference */
#garment-wrapper .outline {
    stroke: #1e293b; stroke-width: 2;
    stroke-linejoin: round; stroke-linecap: round; fill: none;
}
#garment-wrapper .seam {
    fill: none; stroke: rgba(0,0,0,0.20);
    stroke-width: 1.5; stroke-linecap: round;
}
#garment-wrapper .stitch {
    fill: none; stroke: rgba(0,0,0,0.14);
    stroke-width: 1.2; stroke-dasharray: 3 3;
}
</style>

{{-- ══════════════════════════════════════════════════════════════════════════
     FRONT SVG  — id="view-front"
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-front"
     viewBox="0 0 400 470"
     xmlns="http://www.w3.org/2000/svg"
     style="width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="Studio Hoodie — Front">

  <defs>
    <linearGradient id="h-bodyShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.05">
      <stop offset="0"    stop-color="#000" stop-opacity="0.32"/>
      <stop offset="0.15" stop-color="#000" stop-opacity="0.07"/>
      <stop offset="0.5"  stop-color="#fff" stop-opacity="0.10"/>
      <stop offset="0.85" stop-color="#000" stop-opacity="0.07"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.36"/>
    </linearGradient>
    <linearGradient id="h-sleeveShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.1">
      <stop offset="0"   stop-color="#000" stop-opacity="0.22"/>
      <stop offset="0.3" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.12"/>
      <stop offset="0.7" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.26"/>
    </linearGradient>
    <linearGradient id="h-ribShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0"   stop-color="#000" stop-opacity="0.28"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.07"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.30"/>
    </linearGradient>
    <linearGradient id="h-hoodDrop" gradientUnits="userSpaceOnUse" x1="200" y1="120" x2="200" y2="180">
      <stop offset="0" stop-color="#000" stop-opacity="0.38"/>
      <stop offset="1" stop-color="#000" stop-opacity="0"/>
    </linearGradient>
    <pattern id="h-ribKnit" width="4" height="10" patternUnits="userSpaceOnUse">
      <rect width="4" height="10" fill="rgba(0,0,0,0)"/>
      <line x1="1"   y1="0" x2="1"   y2="10" stroke="rgba(0,0,0,0.22)" stroke-width="1.2"/>
      <line x1="2.5" y1="0" x2="2.5" y2="10" stroke="rgba(255,255,255,0.08)" stroke-width="0.8"/>
    </pattern>
    <filter id="h-fleece" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.85 0.85" numOctaves="2" seed="5" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.05 0" result="g"/>
      <feComposite in="g" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="h-gShadow" x="-10%" y="-10%" width="120%" height="130%">
      <feDropShadow dx="0" dy="10" stdDeviation="8" flood-color="#141928" flood-opacity="0.14"/>
    </filter>
  </defs>

  <g filter="url(#h-gShadow)">

    {{-- Sleeves --}}
    <path id="h-sleeveL" class="outline"
          d="M 116 142 C 86 165, 54 225, 46 295 C 38 335, 42 368, 52 390 L 96 390 C 90 355, 92 285, 108 215 Z"
          fill="var(--c-sleeve)"/>
    <path id="h-sleeveR" class="outline"
          d="M 284 142 C 314 165, 346 225, 354 295 C 362 335, 358 368, 348 390 L 304 390 C 310 355, 308 285, 292 215 Z"
          fill="var(--c-sleeve)"/>
    <use href="#h-sleeveL" fill="url(#h-sleeveShade)" style="pointer-events:none;"/>
    <use href="#h-sleeveR" fill="url(#h-sleeveShade)" style="pointer-events:none;"/>
    <use href="#h-sleeveL" filter="url(#h-fleece)" style="pointer-events:none;"/>
    <use href="#h-sleeveR" filter="url(#h-fleece)" style="pointer-events:none;"/>
    <path class="seam" d="M 64 255 C 78 262, 88 266, 98 264" opacity="0.6"/>
    <path class="seam" d="M 56 325 C 72 332, 84 334, 94 332" opacity="0.6"/>
    <path class="seam" d="M 336 255 C 322 262, 312 266, 302 264" opacity="0.6"/>
    <path class="seam" d="M 344 325 C 328 332, 316 334, 306 334" opacity="0.6"/>

    {{-- Body --}}
    <path id="h-body" class="outline"
          d="M 124 135 C 150 130, 250 130, 276 135 C 294 142, 300 165, 300 200 C 300 260, 294 330, 292 390 L 108 390 C 106 330, 100 260, 100 200 C 100 165, 106 142, 124 135 Z"
          fill="var(--c-body)"/>
    <use href="#h-body" fill="url(#h-bodyShade)" style="pointer-events:none;"/>
    <use href="#h-body" filter="url(#h-fleece)"  style="pointer-events:none;"/>

    {{-- Kangaroo Pocket --}}
    <path id="h-pocket" class="outline"
          d="M 154 290 L 246 290 C 252 290, 256 293, 258 297 L 278 338 C 281 343, 279 349, 274 352 L 274 390 L 126 390 L 126 352 C 121 349, 119 343, 122 338 L 142 297 C 144 293, 148 290, 154 290 Z"
          fill="var(--c-body)"/>
    <use href="#h-pocket" fill="url(#h-bodyShade)" style="pointer-events:none;"/>
    <path class="stitch" d="M 142 297 L 122 338"/>
    <path class="stitch" d="M 145 300 L 126 339"/>
    <path class="stitch" d="M 258 297 L 278 338"/>
    <path class="stitch" d="M 255 300 L 274 339"/>
    <path class="stitch" d="M 154 294 L 246 294"/>

    {{-- Cuffs --}}
    <path id="h-cuffL" class="outline"
          d="M 52 390 L 96 390 L 94 422 L 54 422 Z"
          fill="var(--c-rib)"/>
    <path id="h-cuffR" class="outline"
          d="M 348 390 L 304 390 L 306 422 L 346 422 Z"
          fill="var(--c-rib)"/>
    <use href="#h-cuffL" fill="url(#h-ribKnit)" style="pointer-events:none;"/>
    <use href="#h-cuffL" fill="url(#h-ribShade)" style="pointer-events:none;"/>
    <use href="#h-cuffR" fill="url(#h-ribKnit)" style="pointer-events:none;"/>
    <use href="#h-cuffR" fill="url(#h-ribShade)" style="pointer-events:none;"/>
    <line class="stitch" x1="52"  y1="392" x2="96"  y2="392"/>
    <line class="stitch" x1="304" y1="392" x2="348" y2="392"/>

    {{-- Waistband --}}
    <path id="h-waistband" class="outline"
          d="M 108 390 L 292 390 C 292 405, 290 424, 286 438 L 114 438 C 110 424, 108 405, 108 390 Z"
          fill="var(--c-rib)"/>
    <use href="#h-waistband" fill="url(#h-ribKnit)" style="pointer-events:none;"/>
    <use href="#h-waistband" fill="url(#h-ribShade)" style="pointer-events:none;"/>
    <path class="stitch" d="M 108 392 L 292 392"/>

    {{-- Hood drop shadow onto body --}}
    <path d="M 124 135 C 150 160, 250 160, 276 135 C 260 175, 140 175, 124 135 Z"
          fill="url(#h-hoodDrop)" opacity="0.55" style="pointer-events:none;"/>

    {{-- Hood outer --}}
    <path id="h-hoodOuterF" class="outline"
          d="M 128 135 C 106 65, 138 20, 200 20 C 262 20, 294 65, 272 135 C 250 148, 222 152, 200 152 C 178 152, 150 148, 128 135 Z"
          fill="var(--c-body)"/>
    <use href="#h-hoodOuterF" fill="url(#h-bodyShade)" style="pointer-events:none;"/>
    <use href="#h-hoodOuterF" filter="url(#h-fleece)"  style="pointer-events:none;"/>

    {{-- Hood inner opening --}}
    <path id="h-hoodInner" class="outline"
          d="M 160 125 C 148 85, 170 50, 200 50 C 230 50, 252 85, 240 125 C 224 140, 208 144, 200 144 C 192 144, 176 140, 160 125 Z"
          fill="var(--c-rib)"/>
    <use href="#h-hoodInner" fill="url(#h-ribShade)" style="pointer-events:none;"/>
    <path d="M 160 125 C 148 85, 170 50, 200 50 C 230 50, 252 85, 240 125 Z"
          fill="#000" opacity="0.22" style="pointer-events:none;"/>
    <path class="stitch" d="M 156 120 C 144 80, 168 46, 200 46 C 232 46, 256 80, 244 120"/>

    {{-- Drawstring eyelets --}}
    <circle cx="182" cy="134" r="3.5" fill="#1a1a1a" style="pointer-events:none;"/>
    <circle cx="182" cy="134" r="2"   fill="#8e929a" style="pointer-events:none;"/>
    <circle cx="218" cy="134" r="3.5" fill="#1a1a1a" style="pointer-events:none;"/>
    <circle cx="218" cy="134" r="2"   fill="#8e929a" style="pointer-events:none;"/>

    {{-- Drawstrings --}}
    <path id="h-drawstringL"
          d="M 182 136 C 180 170, 168 200, 176 245"
          fill="none" stroke="var(--c-stripe)" stroke-width="3.5"
          stroke-linecap="round" stroke-linejoin="round"/>
    <path id="h-drawstringR"
          d="M 218 136 C 220 170, 232 205, 224 250"
          fill="none" stroke="var(--c-stripe)" stroke-width="3.5"
          stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="176" cy="245" r="2.5" fill="#b0b5bc" style="pointer-events:none;"/>
    <circle cx="224" cy="250" r="2.5" fill="#b0b5bc" style="pointer-events:none;"/>

  </g>

  {{-- ══ PLACEMENT ZONES — FRONT ══════════════════════════════════════════════ --}}

  {{-- A: Left chest --}}
  @if($isZone('A'))
  <g data-zone="A"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'A'}}))"
     role="button" tabindex="0" aria-label="Zone A – Left chest"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="112" y="158" width="68" height="68" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="112" y="158" width="68" height="68" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="146" y1="158" x2="146" y2="226" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="112" y1="192" x2="180" y2="192" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="112" y1="158" x2="180" y2="226" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="180" y1="158" x2="112" y2="226" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="146" cy="192" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="146" y1="189" x2="146" y2="195" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="143" y1="192" x2="149" y2="192" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="128" y="222" width="36" height="14" rx="7" fill="#3b82f6"/>
      <text x="146" y="230" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">A · chest L</text>
    </g>
  </g>
  @endif

  {{-- B: Right chest --}}
  @if($isZone('B'))
  <g data-zone="B"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'B'}}))"
     role="button" tabindex="0" aria-label="Zone B – Right chest"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="220" y="158" width="68" height="68" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="220" y="158" width="68" height="68" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="254" y1="158" x2="254" y2="226" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="220" y1="192" x2="288" y2="192" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="220" y1="158" x2="288" y2="226" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="288" y1="158" x2="220" y2="226" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="254" cy="192" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="254" y1="189" x2="254" y2="195" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="251" y1="192" x2="257" y2="192" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="236" y="222" width="36" height="14" rx="7" fill="#3b82f6"/>
      <text x="254" y="230" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">B · chest R</text>
    </g>
  </g>
  @endif

  {{-- C: Kangaroo pocket --}}
  @if($isZone('C'))
  <g data-zone="C"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'C'}}))"
     role="button" tabindex="0" aria-label="Zone C – Kangaroo pocket"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="138" y="303" width="124" height="65" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="138" y="303" width="124" height="65" rx="4"
          fill="rgba(59,130,246,0.12)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="200" y1="303" x2="200" y2="368" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="138" y1="335" x2="262" y2="335" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="138" y1="303" x2="262" y2="368" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <line x1="262" y1="303" x2="138" y2="368" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="200" cy="335" r="4" fill="none" stroke="#3b82f6" stroke-width="1.3"/>
      <line x1="200" y1="331" x2="200" y2="339" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="196" y1="335" x2="204" y2="335" stroke="#3b82f6" stroke-width="1.2"/>
      <rect x="176" y="364" width="48" height="14" rx="7" fill="#3b82f6"/>
      <text x="200" y="372" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">C · pocket</text>
    </g>
  </g>
  @endif

  {{-- D1: Left sleeve upper --}}
  @if($isZone('D1'))
  <g data-zone="D1" transform="rotate(-15 75 182)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D1'}}))"
     role="button" tabindex="0" aria-label="Zone D1 – Left sleeve upper"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="53" y="160" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="53" y="160" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="75" y1="160" x2="75" y2="204" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="53" y1="182" x2="97" y2="182" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="53" y1="160" x2="97" y2="204" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="97" y1="160" x2="53" y2="204" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="75" cy="182" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="75" y1="179.5" x2="75" y2="184.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="72.5" y1="182" x2="77.5" y2="182" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="60" y="199" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="75" y="205" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">D1</text>
    </g>
  </g>
  @endif

  {{-- D2: Left sleeve mid --}}
  @if($isZone('D2'))
  <g data-zone="D2" transform="rotate(-12 72 257)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D2'}}))"
     role="button" tabindex="0" aria-label="Zone D2 – Left sleeve mid"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="50" y="235" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="50" y="235" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="72" y1="235" x2="72" y2="279" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="50" y1="257" x2="94" y2="257" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="50" y1="235" x2="94" y2="279" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="94" y1="235" x2="50" y2="279" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="72" cy="257" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="72" y1="254.5" x2="72" y2="259.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="69.5" y1="257" x2="74.5" y2="257" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="57" y="274" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="72" y="280" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">D2</text>
    </g>
  </g>
  @endif

  {{-- D3: Left sleeve lower --}}
  @if($isZone('D3'))
  <g data-zone="D3" transform="rotate(-7 73 335)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D3'}}))"
     role="button" tabindex="0" aria-label="Zone D3 – Left sleeve lower"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="53" y="315" width="40" height="40" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="53" y="315" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="73" y1="315" x2="73" y2="355" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="53" y1="335" x2="93" y2="335" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="53" y1="315" x2="93" y2="355" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="93" y1="315" x2="53" y2="355" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="73" cy="335" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="73" y1="332.5" x2="73" y2="337.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="70.5" y1="335" x2="75.5" y2="335" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="58" y="350" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="73" y="356" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">D3</text>
    </g>
  </g>
  @endif

  {{-- E1: Right sleeve upper --}}
  @if($isZone('E1'))
  <g data-zone="E1" transform="rotate(15 325 182)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E1'}}))"
     role="button" tabindex="0" aria-label="Zone E1 – Right sleeve upper"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="303" y="160" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="303" y="160" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="325" y1="160" x2="325" y2="204" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="303" y1="182" x2="347" y2="182" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="303" y1="160" x2="347" y2="204" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="347" y1="160" x2="303" y2="204" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="325" cy="182" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="325" y1="179.5" x2="325" y2="184.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="322.5" y1="182" x2="327.5" y2="182" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="310" y="199" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="325" y="205" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E1</text>
    </g>
  </g>
  @endif

  {{-- E2: Right sleeve mid --}}
  @if($isZone('E2'))
  <g data-zone="E2" transform="rotate(12 328 257)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E2'}}))"
     role="button" tabindex="0" aria-label="Zone E2 – Right sleeve mid"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="306" y="235" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="306" y="235" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="328" y1="235" x2="328" y2="279" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="306" y1="257" x2="350" y2="257" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="306" y1="235" x2="350" y2="279" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="350" y1="235" x2="306" y2="279" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="328" cy="257" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="328" y1="254.5" x2="328" y2="259.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="325.5" y1="257" x2="330.5" y2="257" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="313" y="274" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="328" y="280" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E2</text>
    </g>
  </g>
  @endif

  {{-- E3: Right sleeve lower --}}
  @if($isZone('E3'))
  <g data-zone="E3" transform="rotate(7 327 335)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E3'}}))"
     role="button" tabindex="0" aria-label="Zone E3 – Right sleeve lower"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="307" y="315" width="40" height="40" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="307" y="315" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="327" y1="315" x2="327" y2="355" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="307" y1="335" x2="347" y2="335" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="307" y1="315" x2="347" y2="355" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="347" y1="315" x2="307" y2="355" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="327" cy="335" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="327" y1="332.5" x2="327" y2="337.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="324.5" y1="335" x2="329.5" y2="335" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="312" y="350" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="327" y="356" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E3</text>
    </g>
  </g>
  @endif

</svg>

{{-- ══════════════════════════════════════════════════════════════════════════
     BACK SVG  — id="view-back"
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-back"
     viewBox="0 0 400 470"
     xmlns="http://www.w3.org/2000/svg"
     style="display:none;width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="Studio Hoodie — Back">

  <defs>
    <linearGradient id="hb-bodyShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.05">
      <stop offset="0"    stop-color="#000" stop-opacity="0.32"/>
      <stop offset="0.15" stop-color="#000" stop-opacity="0.07"/>
      <stop offset="0.5"  stop-color="#fff" stop-opacity="0.10"/>
      <stop offset="0.85" stop-color="#000" stop-opacity="0.07"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.36"/>
    </linearGradient>
    <linearGradient id="hb-sleeveShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.1">
      <stop offset="0"   stop-color="#000" stop-opacity="0.22"/>
      <stop offset="0.3" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.12"/>
      <stop offset="0.7" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.26"/>
    </linearGradient>
    <linearGradient id="hb-ribShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0"   stop-color="#000" stop-opacity="0.28"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.07"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.30"/>
    </linearGradient>
    <pattern id="hb-ribKnit" width="4" height="10" patternUnits="userSpaceOnUse">
      <rect width="4" height="10" fill="rgba(0,0,0,0)"/>
      <line x1="1"   y1="0" x2="1"   y2="10" stroke="rgba(0,0,0,0.22)" stroke-width="1.2"/>
      <line x1="2.5" y1="0" x2="2.5" y2="10" stroke="rgba(255,255,255,0.08)" stroke-width="0.8"/>
    </pattern>
    <filter id="hb-fleece" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.85 0.85" numOctaves="2" seed="7" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.05 0" result="g"/>
      <feComposite in="g" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="hb-gShadow" x="-10%" y="-10%" width="120%" height="130%">
      <feDropShadow dx="0" dy="10" stdDeviation="8" flood-color="#141928" flood-opacity="0.14"/>
    </filter>
  </defs>

  <g filter="url(#hb-gShadow)">

    {{-- Sleeves back --}}
    <path id="hb-sleeveLB" class="outline"
          d="M 116 142 C 86 165, 54 225, 46 295 C 38 335, 42 368, 52 390 L 96 390 C 90 355, 92 285, 108 215 Z"
          fill="var(--c-sleeve)"/>
    <path id="hb-sleeveRB" class="outline"
          d="M 284 142 C 314 165, 346 225, 354 295 C 362 335, 358 368, 348 390 L 304 390 C 310 355, 308 285, 292 215 Z"
          fill="var(--c-sleeve)"/>
    <use href="#hb-sleeveLB" fill="url(#hb-sleeveShade)" style="pointer-events:none;"/>
    <use href="#hb-sleeveRB" fill="url(#hb-sleeveShade)" style="pointer-events:none;"/>
    <use href="#hb-sleeveLB" filter="url(#hb-fleece)" style="pointer-events:none;"/>
    <use href="#hb-sleeveRB" filter="url(#hb-fleece)" style="pointer-events:none;"/>
    <path class="seam" d="M 64 255 C 78 262, 88 266, 98 264" opacity="0.6"/>
    <path class="seam" d="M 56 325 C 72 332, 84 334, 94 332" opacity="0.6"/>
    <path class="seam" d="M 336 255 C 322 262, 312 266, 302 264" opacity="0.6"/>
    <path class="seam" d="M 344 325 C 328 332, 316 334, 306 334" opacity="0.6"/>

    {{-- Back hood --}}
    <path id="hb-hoodOuterB" class="outline"
          d="M 132 135 C 110 65, 140 18, 200 18 C 260 18, 290 65, 268 135 C 260 185, 235 205, 200 205 C 165 205, 140 185, 132 135 Z"
          fill="var(--c-body)"/>
    <use href="#hb-hoodOuterB" fill="url(#hb-bodyShade)" style="pointer-events:none;"/>
    <use href="#hb-hoodOuterB" filter="url(#hb-fleece)"  style="pointer-events:none;"/>
    <path class="seam" d="M 152 90 C 180 115, 220 115, 248 90"/>
    <path class="seam" d="M 138 125 C 170 150, 230 150, 262 125"/>
    <path class="seam" d="M 165 155 C 185 170, 215 170, 235 155"/>

    {{-- Body back --}}
    <path id="hb-bodyB" class="outline"
          d="M 124 135 C 150 130, 250 130, 276 135 C 294 142, 300 165, 300 200 C 300 260, 294 330, 292 390 L 108 390 C 106 330, 100 260, 100 200 C 100 165, 106 142, 124 135 Z"
          fill="var(--c-body)"/>
    <use href="#hb-bodyB" fill="url(#hb-bodyShade)" style="pointer-events:none;"/>
    <use href="#hb-bodyB" filter="url(#hb-fleece)"  style="pointer-events:none;"/>
    <line x1="200" y1="215" x2="200" y2="390"
          stroke="rgba(0,0,0,0.07)" stroke-width="1.5" stroke-dasharray="4 4"
          style="pointer-events:none;"/>

    {{-- Cuffs back --}}
    <path id="hb-cuffLB" class="outline"
          d="M 52 390 L 96 390 L 94 422 L 54 422 Z"
          fill="var(--c-rib)"/>
    <path id="hb-cuffRB" class="outline"
          d="M 348 390 L 304 390 L 306 422 L 346 422 Z"
          fill="var(--c-rib)"/>
    <use href="#hb-cuffLB" fill="url(#hb-ribKnit)" style="pointer-events:none;"/>
    <use href="#hb-cuffLB" fill="url(#hb-ribShade)" style="pointer-events:none;"/>
    <use href="#hb-cuffRB" fill="url(#hb-ribKnit)" style="pointer-events:none;"/>
    <use href="#hb-cuffRB" fill="url(#hb-ribShade)" style="pointer-events:none;"/>
    <line class="stitch" x1="52"  y1="392" x2="96"  y2="392"/>
    <line class="stitch" x1="304" y1="392" x2="348" y2="392"/>

    {{-- Waistband back --}}
    <path id="hb-waistbandB" class="outline"
          d="M 108 390 L 292 390 C 292 405, 290 424, 286 438 L 114 438 C 110 424, 108 405, 108 390 Z"
          fill="var(--c-rib)"/>
    <use href="#hb-waistbandB" fill="url(#hb-ribKnit)" style="pointer-events:none;"/>
    <use href="#hb-waistbandB" fill="url(#hb-ribShade)" style="pointer-events:none;"/>
    <path class="stitch" d="M 108 392 L 292 392"/>

  </g>

  {{-- ══ PLACEMENT ZONES — BACK ════════════════════════════════════════════════ --}}

  {{-- F: Under-hood back --}}
  @if($isZone('F'))
  <g data-zone="F"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'F'}}))"
     role="button" tabindex="0" aria-label="Zone F – Under hood back"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="152" y="148" width="96" height="66" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="152" y="148" width="96" height="66" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="200" y1="148" x2="200" y2="214" stroke="#3b82f6" stroke-width="0.6" opacity="0.32" style="pointer-events:none;"/>
    <line x1="152" y1="181" x2="248" y2="181" stroke="#3b82f6" stroke-width="0.6" opacity="0.32" style="pointer-events:none;"/>
    <line x1="152" y1="148" x2="248" y2="214" stroke="#3b82f6" stroke-width="0.8" opacity="0.17" style="pointer-events:none;"/>
    <line x1="248" y1="148" x2="152" y2="214" stroke="#3b82f6" stroke-width="0.8" opacity="0.17" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="200" cy="181" r="3.5" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="200" y1="177.5" x2="200" y2="184.5" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="196.5" y1="181" x2="203.5" y2="181" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="174" y="210" width="52" height="14" rx="7" fill="#3b82f6"/>
      <text x="200" y="218" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">F · under hood</text>
    </g>
  </g>
  @endif

  {{-- G: Large back panel --}}
  @if($isZone('G'))
  <g data-zone="G"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'G'}}))"
     role="button" tabindex="0" aria-label="Zone G – Back panel"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="126" y="232" width="148" height="132" rx="5" fill="transparent"/>
    <rect class="zone-outline" x="126" y="232" width="148" height="132" rx="5"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 3" style="pointer-events:none;"/>
    <line x1="200" y1="232" x2="200" y2="364" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="126" y1="298" x2="274" y2="298" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="126" y1="232" x2="274" y2="364" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <line x1="274" y1="232" x2="126" y2="364" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      {{-- Corner ticks --}}
      <line x1="126" y1="246" x2="126" y2="232" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="126" y1="232" x2="142" y2="232" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="258" y1="232" x2="274" y2="232" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="274" y1="232" x2="274" y2="246" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="126" y1="350" x2="126" y2="364" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="126" y1="364" x2="142" y2="364" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="258" y1="364" x2="274" y2="364" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="274" y1="364" x2="274" y2="350" stroke="#3b82f6" stroke-width="2.5"/>
      {{-- Crosshair --}}
      <circle cx="200" cy="298" r="6" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="200" y1="289" x2="200" y2="307" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="191" y1="298" x2="209" y2="298" stroke="#3b82f6" stroke-width="1.5"/>
      <rect x="172" y="360" width="56" height="16" rx="8" fill="#3b82f6"/>
      <text x="200" y="369" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">G · back panel</text>
    </g>
  </g>
  @endif

  {{-- D2-back: left sleeve mid indicator (decorative, no click) --}}
  @if($isZone('D2'))
  <g style="pointer-events:none;" transform="rotate(-12 73 255)">
    <rect x="53" y="235" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="73" y1="235" x2="73" y2="275" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="53" y1="255" x2="93" y2="255" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="73" cy="255" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="73" y1="252.5" x2="73" y2="257.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="70.5" y1="255" x2="75.5" y2="255" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif

  {{-- E2-back: right sleeve mid indicator (decorative, no click) --}}
  @if($isZone('E2'))
  <g style="pointer-events:none;" transform="rotate(12 327 255)">
    <rect x="307" y="235" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="327" y1="235" x2="327" y2="275" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="307" y1="255" x2="347" y2="255" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="327" cy="255" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="327" y1="252.5" x2="327" y2="257.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="324.5" y1="255" x2="329.5" y2="255" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif

</svg>