{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/garments/varsity_jacket.blade.php                               ║
║                                                                            ║
║  EXACT replica of the "Varsity Studio · Placement Zone Configurator"       ║
║  reference HTML — full textures, shading, snap buttons, seams, back view.  ║
║                                                                            ║
║  Wired for the Live Preview Engine (show.blade.php / Alpine.js):           ║
║    • CSS vars  --c-body / --c-sleeve / --c-rib / --c-stripe                ║
║      drive all fill colours — inherited from #garment-wrapper.             ║
║    • Zone groups dispatch  zone:open  custom events (caught by Alpine).    ║
║    • .zone-outline / .zone-badge  classes driven by CSS in show.blade.php. ║
║    • Content layers (text/image) injected at runtime by designEngine().    ║
║                                                                            ║
║  Variables:                                                                ║
║    $zones       – zone defs array [['key','label','type'], …]              ║
║    $zoneCoords  – coordinate map passed from show.blade.php                ║
║    $defaults    – ['body'=>'#hex', 'sleeve'=>'#hex', …]                   ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $activeZoneKeys = array_column($zones, 'key');
    $isZone = fn(string $k) => in_array($k, $activeZoneKeys, true);
@endphp

{{-- Seed CSS vars as fallbacks (JS overwrites these on init) --}}
<style>
#garment-wrapper {
    --c-body:   {{ $defaults['body']   ?? '#141414' }};
    --c-sleeve: {{ $defaults['sleeve'] ?? '#f3f3f1' }};
    --c-rib:    {{ $defaults['rib']    ?? '#141414' }};
    --c-stripe: {{ $defaults['stripe'] ?? '#ffffff' }};
}
/* Mirror the .outline / .seam classes from the reference */
#garment-wrapper .outline {
    stroke: #474747; stroke-width: 2.2; stroke-linejoin: round; fill: none;
}
#garment-wrapper .seam {
    fill: none; stroke: rgba(0,0,0,0.28); stroke-width: 1.5;
}
#garment-wrapper .pocketLine { stroke-width: 4; }
#garment-wrapper #collarFrontStripes path,
#garment-wrapper #collarBackStripesB path { stroke-width: 5; }
</style>

{{-- ══════════════════════════════════════════════════════════════════════════
     FRONT SVG  — id="view-front"
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-front"
     viewBox="0 0 400 470"
     xmlns="http://www.w3.org/2000/svg"
     style="width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="Varsity Jacket — Front">

  <defs>
    <linearGradient id="bodyShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.15">
      <stop offset="0"    stop-color="#000" stop-opacity="0.42"/>
      <stop offset="0.14" stop-color="#000" stop-opacity="0.12"/>
      <stop offset="0.42" stop-color="#fff" stop-opacity="0.10"/>
      <stop offset="0.5"  stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.58" stop-color="#fff" stop-opacity="0.08"/>
      <stop offset="0.86" stop-color="#000" stop-opacity="0.12"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.46"/>
    </linearGradient>
    <linearGradient id="sleeveShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.1">
      <stop offset="0"    stop-color="#000" stop-opacity="0.22"/>
      <stop offset="0.22" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="0.44" stop-color="#fff" stop-opacity="0.30"/>
      <stop offset="0.56" stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.8"  stop-color="#000" stop-opacity="0.06"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.26"/>
    </linearGradient>
    <linearGradient id="ribShade" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0"   stop-color="#000" stop-opacity="0.34"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.08"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.36"/>
    </linearGradient>
    <linearGradient id="topLight" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0"    stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.22" stop-color="#fff" stop-opacity="0"/>
      <stop offset="0.8"  stop-color="#000" stop-opacity="0"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.16"/>
    </linearGradient>
    <pattern id="ribKnit" width="6" height="14" patternUnits="userSpaceOnUse">
      <rect width="6" height="14" fill="rgba(0,0,0,0)"/>
      <line x1="1"   y1="0" x2="1"   y2="14" stroke="rgba(0,0,0,0.30)" stroke-width="1.6"/>
      <line x1="3.4" y1="0" x2="3.4" y2="14" stroke="rgba(255,255,255,0.16)" stroke-width="1.2"/>
      <line x1="5.2" y1="0" x2="5.2" y2="14" stroke="rgba(0,0,0,0.18)" stroke-width="1"/>
    </pattern>
    <radialGradient id="metalSnap" cx="0.35" cy="0.3" r="0.85">
      <stop offset="0"    stop-color="#ffffff"/>
      <stop offset="0.35" stop-color="#e4e6ea"/>
      <stop offset="0.7"  stop-color="#aeb2b8"/>
      <stop offset="1"    stop-color="#6f747b"/>
    </radialGradient>
    <filter id="woolTex" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.9 0.9" numOctaves="2" seed="7" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.06 0" result="grain"/>
      <feComposite in="grain" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="leatherTex" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.7 0.7" numOctaves="2" seed="3" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.05 0" result="grain"/>
      <feComposite in="grain" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="soft" x="-20%" y="-20%" width="140%" height="150%">
      <feDropShadow dx="0" dy="7" stdDeviation="7" flood-color="#000" flood-opacity="0.28"/>
    </filter>
  </defs>

  <g id="art-front" filter="url(#soft)">

    {{-- Sleeves --}}
    <path id="sleeveL" class="outline"
          d="M 112 126 C 82 150, 58 224, 50 300 C 47 332, 52 362, 62 392 L 122 392 C 124 352, 126 250, 134 172 C 136 150, 130 136, 120 128 Z"
          fill="var(--c-sleeve)"/>
    <path id="sleeveR" class="outline"
          d="M 288 126 C 318 150, 342 224, 350 300 C 353 332, 348 362, 338 392 L 278 392 C 276 352, 274 250, 266 172 C 264 150, 270 136, 280 128 Z"
          fill="var(--c-sleeve)"/>
    <use href="#sleeveL" fill="url(#sleeveShade)" style="pointer-events:none;"/>
    <use href="#sleeveR" fill="url(#sleeveShade)" style="pointer-events:none;"/>
    <use href="#sleeveL" filter="url(#leatherTex)" style="pointer-events:none;"/>
    <use href="#sleeveR" filter="url(#leatherTex)" style="pointer-events:none;"/>
    <path d="M 120 128 C 118 200, 120 320, 122 388 L 138 388 C 134 300, 134 200, 132 132 Z"
          fill="#000" opacity="0.12" style="pointer-events:none;"/>
    <path d="M 280 128 C 282 200, 280 320, 278 388 L 262 388 C 266 300, 266 200, 268 132 Z"
          fill="#000" opacity="0.12" style="pointer-events:none;"/>
    <g fill="none" stroke="#000" stroke-opacity="0.10" stroke-width="3" stroke-linecap="round" style="pointer-events:none;">
      <path d="M 72 250 C 88 256, 100 260, 110 262"/>
      <path d="M 66 320 C 84 326, 98 330, 110 330"/>
      <path d="M 328 250 C 312 256, 300 260, 290 262"/>
      <path d="M 334 320 C 316 326, 302 330, 290 330"/>
    </g>
    <path class="seam" d="M 120 134 C 122 234, 122 320, 122 388"/>
    <path class="seam" d="M 280 134 C 278 234, 278 320, 278 388"/>

    {{-- Body --}}
    <path id="body" class="outline"
          d="M 130 122 C 150 112, 250 112, 270 122 C 286 128, 294 140, 298 152 C 306 178, 308 252, 302 332 C 300 360, 298 380, 296 394 L 104 394 C 102 380, 100 360, 98 332 C 92 252, 94 178, 102 152 C 106 140, 114 128, 130 122 Z"
          fill="var(--c-body)"/>
    <use href="#body" fill="url(#bodyShade)" style="pointer-events:none;"/>
    <use href="#body" fill="url(#topLight)"  style="pointer-events:none;"/>
    <use href="#body" filter="url(#woolTex)" style="pointer-events:none;"/>
    {{-- V-split shadow --}}
    <path d="M 150 124 C 164 136, 177 147, 189 160 C 193 165, 197 171, 200 178 C 203 171, 207 165, 211 160 C 223 147, 236 136, 250 124 C 242 133, 233 142, 224 150 C 217 157, 211 164, 200 176 C 189 164, 183 157, 176 150 C 167 142, 158 133, 150 124 Z"
          fill="#000" opacity="0.10" style="pointer-events:none;"/>
    {{-- Placket seams --}}
    <path id="placketL"   class="seam" d="M 192 178 L 192 394"/>
    <path id="placketR"   class="seam" d="M 208 178 L 208 394"/>
    <path id="centerLine" class="seam" d="M 200 178 L 200 394"/>
    <path class="seam" d="M 146 124 C 156 136, 164 147, 168 160"/>
    <path class="seam" d="M 254 124 C 244 136, 236 147, 232 160"/>

    {{-- Pockets --}}
    <g id="g-pockets" fill="none" stroke-linecap="round" style="pointer-events:none;">
      <path d="M 156 301 L 148 363" stroke="#000" stroke-opacity="0.5" stroke-width="3"/>
      <path d="M 244 301 L 252 363" stroke="#000" stroke-opacity="0.5" stroke-width="3"/>
      <path class="pocketLine" d="M 150 300 L 142 364" stroke="var(--c-stripe)"/>
      <path class="pocketLine" d="M 162 300 L 154 364" stroke="var(--c-stripe)"/>
      <path class="pocketLine" d="M 250 300 L 258 364" stroke="var(--c-stripe)"/>
      <path class="pocketLine" d="M 238 300 L 246 364" stroke="var(--c-stripe)"/>
    </g>

    {{-- Collar --}}
    <g id="g-collar">
      <path id="collar" class="outline"
            d="M 148 122 C 156 104, 172 98, 200 98 C 228 98, 244 104, 252 122 C 246 132, 236 140, 224 146 C 214 151, 207 154, 200 154 C 193 154, 186 151, 176 146 C 164 140, 154 132, 148 122 Z"
            fill="var(--c-rib)"/>
      <use href="#collar" fill="url(#ribKnit)" style="pointer-events:none;"/>
      <use href="#collar" fill="url(#ribShade)" style="pointer-events:none;"/>
      <g id="collarFrontStripes" fill="none" stroke="var(--c-stripe)" stroke-linecap="round" style="pointer-events:none;">
        <path d="M 156 124 C 168 136, 180 145, 194 153"/>
        <path d="M 167 118 C 178 129, 188 138, 198 145"/>
        <path d="M 244 124 C 232 136, 220 145, 206 153"/>
        <path d="M 233 118 C 222 129, 212 138, 202 145"/>
      </g>
    </g>

    {{-- Snap buttons — built by JS (same as reference) --}}
    <g id="g-buttons-front" aria-hidden="true"></g>

    {{-- Cuffs --}}
    <path id="cuffL" class="outline"
          d="M 60 392 L 124 392 L 121 426 L 65 426 Z"
          fill="var(--c-rib)"/>
    <path id="cuffR" class="outline"
          d="M 340 392 L 276 392 L 279 426 L 335 426 Z"
          fill="var(--c-rib)"/>
    <use href="#cuffL" fill="url(#ribKnit)" style="pointer-events:none;"/>
    <use href="#cuffL" fill="url(#ribShade)" style="pointer-events:none;"/>
    <use href="#cuffR" fill="url(#ribKnit)" style="pointer-events:none;"/>
    <use href="#cuffR" fill="url(#ribShade)" style="pointer-events:none;"/>
    <g id="cuffStripes" style="pointer-events:none;">
      <rect x="62"  y="398" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="62"  y="408" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="62"  y="418" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="398" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="408" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="418" width="60" height="4" fill="var(--c-stripe)"/>
    </g>

    {{-- Waistband --}}
    <path id="waistband" class="outline"
          d="M 104 394 L 296 394 L 292 438 L 108 438 Z"
          fill="var(--c-rib)"/>
    <use href="#waistband" fill="url(#ribKnit)" style="pointer-events:none;"/>
    <use href="#waistband" fill="url(#ribShade)" style="pointer-events:none;"/>
    <g id="waistStripes" style="pointer-events:none;">
      <rect x="106" y="401" width="188" height="5" fill="var(--c-stripe)"/>
      <rect x="106" y="414" width="186" height="5" fill="var(--c-stripe)"/>
      <rect x="106" y="427" width="184" height="5" fill="var(--c-stripe)"/>
    </g>

  </g>{{-- /art-front --}}

  {{-- ══ PLACEMENT ZONES — FRONT ══════════════════════════════════════════════ --}}

  {{-- A: Left chest --}}
  @if($isZone('A'))
  <g data-zone="A"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'A'}}))"
     role="button" tabindex="0" aria-label="Zone A – Left chest"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="110" y="168" width="68" height="68" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="110" y="168" width="68" height="68" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="144" y1="168" x2="144" y2="236" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="110" y1="202" x2="178" y2="202" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="110" y1="168" x2="178" y2="236" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="178" y1="168" x2="110" y2="236" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="144" cy="202" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="144" y1="199" x2="144" y2="205" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="141" y1="202" x2="147" y2="202" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="126" y="232" width="36" height="14" rx="7" fill="#3b82f6"/>
      <text x="144" y="240" text-anchor="middle" dominant-baseline="central"
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
    <rect x="222" y="168" width="68" height="68" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="222" y="168" width="68" height="68" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="256" y1="168" x2="256" y2="236" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="222" y1="202" x2="290" y2="202" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="222" y1="168" x2="290" y2="236" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="290" y1="168" x2="222" y2="236" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="256" cy="202" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="256" y1="199" x2="256" y2="205" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="253" y1="202" x2="259" y2="202" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="238" y="232" width="36" height="14" rx="7" fill="#3b82f6"/>
      <text x="256" y="240" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">B · chest R</text>
    </g>
  </g>
  @endif

  {{-- C: Left pocket --}}
  @if($isZone('C'))
  <g data-zone="C"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'C'}}))"
     role="button" tabindex="0" aria-label="Zone C – Left pocket"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="128" y="292" width="38" height="78" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="128" y="292" width="38" height="78" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="147" y1="292" x2="147" y2="370" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="128" y1="331" x2="166" y2="331" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="128" y1="292" x2="166" y2="370" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <line x1="166" y1="292" x2="128" y2="370" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="147" cy="331" r="3" fill="none" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="147" y1="328" x2="147" y2="334" stroke="#3b82f6" stroke-width="1"/>
      <line x1="144" y1="331" x2="150" y2="331" stroke="#3b82f6" stroke-width="1"/>
      <rect x="129" y="367" width="36" height="13" rx="6.5" fill="#3b82f6"/>
      <text x="147" y="374" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#fff">C · pocket</text>
    </g>
  </g>
  @endif

  {{-- D: Right pocket --}}
  @if($isZone('D'))
  <g data-zone="D"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D'}}))"
     role="button" tabindex="0" aria-label="Zone D – Right pocket"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="234" y="292" width="38" height="78" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="234" y="292" width="38" height="78" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="253" y1="292" x2="253" y2="370" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="234" y1="331" x2="272" y2="331" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="234" y1="292" x2="272" y2="370" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <line x1="272" y1="292" x2="234" y2="370" stroke="#3b82f6" stroke-width="0.8" opacity="0.15" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="253" cy="331" r="3" fill="none" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="253" y1="328" x2="253" y2="334" stroke="#3b82f6" stroke-width="1"/>
      <line x1="250" y1="331" x2="256" y2="331" stroke="#3b82f6" stroke-width="1"/>
      <rect x="235" y="367" width="36" height="13" rx="6.5" fill="#3b82f6"/>
      <text x="253" y="374" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#fff">D · pocket</text>
    </g>
  </g>
  @endif

  {{-- E1: Left sleeve upper --}}
  @if($isZone('E1'))
  <g data-zone="E1" transform="rotate(-18 74 185)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E1'}}))"
     role="button" tabindex="0" aria-label="Zone E1 – Left sleeve upper"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="52" y="163" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="52" y="163" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="74" y1="163" x2="74" y2="207" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="52" y1="185" x2="96" y2="185" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="52" y1="163" x2="96" y2="207" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="96" y1="163" x2="52" y2="207" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="74" cy="185" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="74" y1="182.5" x2="74" y2="187.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="71.5" y1="185" x2="76.5" y2="185" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="59" y="202" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="74" y="208" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E1</text>
    </g>
  </g>
  @endif

  {{-- E2: Left sleeve mid --}}
  @if($isZone('E2'))
  <g data-zone="E2" transform="rotate(-14 70 268)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E2'}}))"
     role="button" tabindex="0" aria-label="Zone E2 – Left sleeve mid"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="48" y="246" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="48" y="246" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="70" y1="246" x2="70" y2="290" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="48" y1="268" x2="92" y2="268" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="48" y1="246" x2="92" y2="290" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="92" y1="246" x2="48" y2="290" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="70" cy="268" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="70" y1="265.5" x2="70" y2="270.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="67.5" y1="268" x2="72.5" y2="268" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="55" y="285" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="70" y="291" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E2</text>
    </g>
  </g>
  @endif

  {{-- E3: Left sleeve lower --}}
  @if($isZone('E3'))
  <g data-zone="E3" transform="rotate(-8 74 348)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E3'}}))"
     role="button" tabindex="0" aria-label="Zone E3 – Left sleeve lower"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="54" y="328" width="40" height="40" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="54" y="328" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="74" y1="328" x2="74" y2="368" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="54" y1="348" x2="94" y2="348" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="54" y1="328" x2="94" y2="368" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="94" y1="328" x2="54" y2="368" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="74" cy="348" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="74" y1="345.5" x2="74" y2="350.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="71.5" y1="348" x2="76.5" y2="348" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="59" y="363" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="74" y="369" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E3</text>
    </g>
  </g>
  @endif

  {{-- F1: Right sleeve upper --}}
  @if($isZone('F1'))
  <g data-zone="F1" transform="rotate(18 326 185)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'F1'}}))"
     role="button" tabindex="0" aria-label="Zone F1 – Right sleeve upper"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="304" y="163" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="304" y="163" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="326" y1="163" x2="326" y2="207" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="304" y1="185" x2="348" y2="185" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="304" y1="163" x2="348" y2="207" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="348" y1="163" x2="304" y2="207" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="326" cy="185" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="326" y1="182.5" x2="326" y2="187.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="323.5" y1="185" x2="328.5" y2="185" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="311" y="202" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="326" y="208" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">F1</text>
    </g>
  </g>
  @endif

  {{-- F2: Right sleeve mid --}}
  @if($isZone('F2'))
  <g data-zone="F2" transform="rotate(14 330 268)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'F2'}}))"
     role="button" tabindex="0" aria-label="Zone F2 – Right sleeve mid"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="308" y="246" width="44" height="44" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="308" y="246" width="44" height="44" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="330" y1="246" x2="330" y2="290" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="308" y1="268" x2="352" y2="268" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="308" y1="246" x2="352" y2="290" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="352" y1="246" x2="308" y2="290" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="330" cy="268" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="330" y1="265.5" x2="330" y2="270.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="327.5" y1="268" x2="332.5" y2="268" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="315" y="285" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="330" y="291" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">F2</text>
    </g>
  </g>
  @endif

  {{-- F3: Right sleeve lower --}}
  @if($isZone('F3'))
  <g data-zone="F3" transform="rotate(8 326 348)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'F3'}}))"
     role="button" tabindex="0" aria-label="Zone F3 – Right sleeve lower"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="306" y="328" width="40" height="40" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="306" y="328" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="326" y1="328" x2="326" y2="368" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="306" y1="348" x2="346" y2="348" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="306" y1="328" x2="346" y2="368" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="346" y1="328" x2="306" y2="368" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="326" cy="348" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="326" y1="345.5" x2="326" y2="350.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="323.5" y1="348" x2="328.5" y2="348" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="311" y="363" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="326" y="369" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">F3</text>
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
     aria-label="Varsity Jacket — Back">

  <defs>
    <linearGradient id="bodyShadeB" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.15">
      <stop offset="0"    stop-color="#000" stop-opacity="0.42"/>
      <stop offset="0.14" stop-color="#000" stop-opacity="0.12"/>
      <stop offset="0.42" stop-color="#fff" stop-opacity="0.10"/>
      <stop offset="0.5"  stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.58" stop-color="#fff" stop-opacity="0.08"/>
      <stop offset="0.86" stop-color="#000" stop-opacity="0.12"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.46"/>
    </linearGradient>
    <linearGradient id="sleeveShadeB" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0.1">
      <stop offset="0"    stop-color="#000" stop-opacity="0.22"/>
      <stop offset="0.22" stop-color="#000" stop-opacity="0.04"/>
      <stop offset="0.44" stop-color="#fff" stop-opacity="0.30"/>
      <stop offset="0.56" stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.8"  stop-color="#000" stop-opacity="0.06"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.26"/>
    </linearGradient>
    <linearGradient id="ribShadeB" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0"   stop-color="#000" stop-opacity="0.34"/>
      <stop offset="0.5" stop-color="#fff" stop-opacity="0.08"/>
      <stop offset="1"   stop-color="#000" stop-opacity="0.36"/>
    </linearGradient>
    <linearGradient id="topLightB" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0"    stop-color="#fff" stop-opacity="0.14"/>
      <stop offset="0.22" stop-color="#fff" stop-opacity="0"/>
      <stop offset="0.8"  stop-color="#000" stop-opacity="0"/>
      <stop offset="1"    stop-color="#000" stop-opacity="0.16"/>
    </linearGradient>
    <pattern id="ribKnitB" width="6" height="14" patternUnits="userSpaceOnUse">
      <rect width="6" height="14" fill="rgba(0,0,0,0)"/>
      <line x1="1"   y1="0" x2="1"   y2="14" stroke="rgba(0,0,0,0.30)" stroke-width="1.6"/>
      <line x1="3.4" y1="0" x2="3.4" y2="14" stroke="rgba(255,255,255,0.16)" stroke-width="1.2"/>
      <line x1="5.2" y1="0" x2="5.2" y2="14" stroke="rgba(0,0,0,0.18)" stroke-width="1"/>
    </pattern>
    <filter id="woolTexB" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.9 0.9" numOctaves="2" seed="9" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.06 0" result="grain"/>
      <feComposite in="grain" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="leatherTexB" x="-5%" y="-5%" width="110%" height="110%">
      <feTurbulence type="fractalNoise" baseFrequency="0.7 0.7" numOctaves="2" seed="5" result="n"/>
      <feColorMatrix in="n" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 0.05 0" result="grain"/>
      <feComposite in="grain" in2="SourceGraphic" operator="in"/>
    </filter>
    <filter id="softB" x="-20%" y="-20%" width="140%" height="150%">
      <feDropShadow dx="0" dy="7" stdDeviation="7" flood-color="#000" flood-opacity="0.28"/>
    </filter>
  </defs>

  <g id="art-back" filter="url(#softB)">

    {{-- Sleeves back --}}
    <path id="sleeveLB" class="outline"
          d="M 112 126 C 82 150, 58 224, 50 300 C 47 332, 52 362, 62 392 L 122 392 C 124 352, 126 250, 134 172 C 136 150, 130 136, 120 128 Z"
          fill="var(--c-sleeve)"/>
    <path id="sleeveRB" class="outline"
          d="M 288 126 C 318 150, 342 224, 350 300 C 353 332, 348 362, 338 392 L 278 392 C 276 352, 274 250, 266 172 C 264 150, 270 136, 280 128 Z"
          fill="var(--c-sleeve)"/>
    <use href="#sleeveLB" fill="url(#sleeveShadeB)" style="pointer-events:none;"/>
    <use href="#sleeveRB" fill="url(#sleeveShadeB)" style="pointer-events:none;"/>
    <use href="#sleeveLB" filter="url(#leatherTexB)" style="pointer-events:none;"/>
    <use href="#sleeveRB" filter="url(#leatherTexB)" style="pointer-events:none;"/>
    <path d="M 120 128 C 118 200, 120 320, 122 388 L 138 388 C 134 300, 134 200, 132 132 Z"
          fill="#000" opacity="0.12" style="pointer-events:none;"/>
    <path d="M 280 128 C 282 200, 280 320, 278 388 L 262 388 C 266 300, 266 200, 268 132 Z"
          fill="#000" opacity="0.12" style="pointer-events:none;"/>
    <g fill="none" stroke="#000" stroke-opacity="0.10" stroke-width="3" stroke-linecap="round" style="pointer-events:none;">
      <path d="M 72 250 C 88 256, 100 260, 110 262"/>
      <path d="M 66 320 C 84 326, 98 330, 110 330"/>
      <path d="M 328 250 C 312 256, 300 260, 290 262"/>
      <path d="M 334 320 C 316 326, 302 330, 290 330"/>
    </g>
    <path class="seam" d="M 120 134 C 122 234, 122 320, 122 388"/>
    <path class="seam" d="M 280 134 C 278 234, 278 320, 278 388"/>

    {{-- Body back --}}
    <path id="bodyB" class="outline"
          d="M 130 122 C 150 112, 250 112, 270 122 C 286 128, 294 140, 298 152 C 306 178, 308 252, 302 332 C 300 360, 298 380, 296 394 L 104 394 C 102 380, 100 360, 98 332 C 92 252, 94 178, 102 152 C 106 140, 114 128, 130 122 Z"
          fill="var(--c-body)"/>
    <use href="#bodyB" fill="url(#bodyShadeB)" style="pointer-events:none;"/>
    <use href="#bodyB" fill="url(#topLightB)"  style="pointer-events:none;"/>
    <use href="#bodyB" filter="url(#woolTexB)" style="pointer-events:none;"/>
    <g fill="none" stroke="#000" stroke-opacity="0.08" stroke-width="3" stroke-linecap="round" style="pointer-events:none;">
      <path d="M 120 350 C 140 360, 160 366, 178 368"/>
      <path d="M 280 350 C 260 360, 240 366, 222 368"/>
    </g>
    <path class="seam" d="M 146 124 C 156 136, 164 147, 168 160"/>
    <path class="seam" d="M 254 124 C 244 136, 236 147, 232 160"/>

    {{-- Back collar --}}
    <path id="backCollar" class="outline"
          d="M 148 122 C 156 104, 172 98, 200 98 C 228 98, 244 104, 252 122 C 244 130, 232 136, 218 140 C 212 142, 206 143, 200 143 C 194 143, 188 142, 182 140 C 168 136, 156 130, 148 122 Z"
          fill="var(--c-rib)"/>
    <use href="#backCollar" fill="url(#ribKnitB)" style="pointer-events:none;"/>
    <use href="#backCollar" fill="url(#ribShadeB)" style="pointer-events:none;"/>
    <g id="collarBackStripesB" fill="none" stroke="var(--c-stripe)" stroke-linecap="round" style="pointer-events:none;">
      <path d="M 160 112 C 176 110, 224 110, 240 112"/>
      <path d="M 156 120 C 175 118, 225 118, 244 120"/>
      <path d="M 160 128 C 178 127, 222 127, 240 128"/>
      <path d="M 166 136 C 182 136, 218 136, 234 136"/>
    </g>
    <path class="seam" d="M 128 122 C 138 104, 156 96, 200 96 C 244 96, 262 104, 272 122"/>
    <path class="seam" d="M 144 131 C 162 142, 180 148, 200 148 C 220 148, 238 142, 256 131"/>

    {{-- Cuffs back --}}
    <path id="cuffLB" class="outline"
          d="M 60 392 L 124 392 L 121 426 L 65 426 Z"
          fill="var(--c-rib)"/>
    <path id="cuffRB" class="outline"
          d="M 340 392 L 276 392 L 279 426 L 335 426 Z"
          fill="var(--c-rib)"/>
    <use href="#cuffLB" fill="url(#ribKnitB)" style="pointer-events:none;"/>
    <use href="#cuffLB" fill="url(#ribShadeB)" style="pointer-events:none;"/>
    <use href="#cuffRB" fill="url(#ribKnitB)" style="pointer-events:none;"/>
    <use href="#cuffRB" fill="url(#ribShadeB)" style="pointer-events:none;"/>
    <g id="cuffStripesB" style="pointer-events:none;">
      <rect x="62"  y="398" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="62"  y="408" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="62"  y="418" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="398" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="408" width="60" height="4" fill="var(--c-stripe)"/>
      <rect x="278" y="418" width="60" height="4" fill="var(--c-stripe)"/>
    </g>

    {{-- Waistband back --}}
    <path id="waistbandB" class="outline"
          d="M 104 394 L 296 394 L 292 438 L 108 438 Z"
          fill="var(--c-rib)"/>
    <use href="#waistbandB" fill="url(#ribKnitB)" style="pointer-events:none;"/>
    <use href="#waistbandB" fill="url(#ribShadeB)" style="pointer-events:none;"/>
    <g id="waistStripesB" style="pointer-events:none;">
      <rect x="106" y="401" width="188" height="5" fill="var(--c-stripe)"/>
      <rect x="106" y="414" width="186" height="5" fill="var(--c-stripe)"/>
      <rect x="106" y="427" width="184" height="5" fill="var(--c-stripe)"/>
    </g>

  </g>{{-- /art-back --}}

  {{-- ══ PLACEMENT ZONES — BACK ════════════════════════════════════════════════ --}}

  {{-- G: Large center back panel --}}
  @if($isZone('G'))
  <g data-zone="G"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'G'}}))"
     role="button" tabindex="0" aria-label="Zone G – Back panel"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="122" y="160" width="156" height="168" rx="5" fill="transparent"/>
    <rect class="zone-outline" x="122" y="160" width="156" height="168" rx="5"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 3" style="pointer-events:none;"/>
    <line x1="200" y1="160" x2="200" y2="328" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="122" y1="244" x2="278" y2="244" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="122" y1="160" x2="278" y2="328" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <line x1="278" y1="160" x2="122" y2="328" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      {{-- Corner ticks --}}
      <line x1="122" y1="174" x2="122" y2="160" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="122" y1="160" x2="138" y2="160" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="262" y1="160" x2="278" y2="160" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="278" y1="160" x2="278" y2="174" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="122" y1="314" x2="122" y2="328" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="122" y1="328" x2="138" y2="328" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="262" y1="328" x2="278" y2="328" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="278" y1="328" x2="278" y2="314" stroke="#3b82f6" stroke-width="2.5"/>
      {{-- Crosshair --}}
      <circle cx="200" cy="244" r="6" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="200" y1="234" x2="200" y2="254" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="190" y1="244" x2="210" y2="244" stroke="#3b82f6" stroke-width="1.5"/>
      <rect x="166" y="324" width="68" height="16" rx="8" fill="#3b82f6"/>
      <text x="200" y="333" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">G · back panel</text>
    </g>
  </g>
  @endif

  {{-- H: Upper back yoke --}}
  @if($isZone('H'))
  <g data-zone="H"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'H'}}))"
     role="button" tabindex="0" aria-label="Zone H – Back yoke"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="146" y="152" width="108" height="50" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="146" y="152" width="108" height="50" rx="4"
          fill="rgba(59,130,246,0.09)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="200" y1="152" x2="200" y2="202" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="146" y1="177" x2="254" y2="177" stroke="#3b82f6" stroke-width="0.6" opacity="0.30" style="pointer-events:none;"/>
    <line x1="146" y1="152" x2="254" y2="202" stroke="#3b82f6" stroke-width="0.8" opacity="0.14" style="pointer-events:none;"/>
    <line x1="254" y1="152" x2="146" y2="202" stroke="#3b82f6" stroke-width="0.8" opacity="0.14" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="200" cy="177" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="200" y1="174" x2="200" y2="180" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="197" y1="177" x2="203" y2="177" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="174" y="198" width="52" height="14" rx="7" fill="#3b82f6"/>
      <text x="200" y="206" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">H · yoke</text>
    </g>
  </g>
  @endif

  {{-- Back sleeve mid zones (continuation of E2/F2) --}}
  @if($isZone('E2'))
  <g data-zone="E2" transform="rotate(-14 70 268)" style="pointer-events:none;">
    <rect x="48" y="246" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="68" y1="246" x2="68" y2="286" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="48" y1="266" x2="88" y2="266" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="68" cy="266" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="68" y1="263.5" x2="68" y2="268.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="65.5" y1="266" x2="70.5" y2="266" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif
  @if($isZone('F2'))
  <g data-zone="F2" transform="rotate(14 330 268)" style="pointer-events:none;">
    <rect x="310" y="246" width="40" height="40" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="330" y1="246" x2="330" y2="286" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="310" y1="266" x2="350" y2="266" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="330" cy="266" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="330" y1="263.5" x2="330" y2="268.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="327.5" y1="266" x2="332.5" y2="266" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif

</svg>

{{-- ══ Snap buttons script ═══════════════════════════════════════════════════ --}}
{{-- Runs once on include; builds the same metallic snaps as the reference HTML --}}
<script>
(function buildSnaps() {
    const g = document.getElementById('g-buttons-front');
    if (!g) return;
    // Clear any previously injected buttons (safe on HMR)
    g.innerHTML = '';
    [196, 238, 280, 322, 364].forEach(cy => {
        g.insertAdjacentHTML('beforeend', `
            <circle cx="200" cy="${cy + 1.2}" r="6" fill="#000" opacity="0.35"/>
            <circle cx="200" cy="${cy}"       r="6" fill="url(#metalSnap)" stroke="#5b6066" stroke-width="0.8"/>
            <circle cx="200" cy="${cy}"       r="2.4" fill="none" stroke="#7d828a" stroke-width="0.7"/>
            <circle cx="198.4" cy="${cy - 1.6}" r="1.5" fill="#ffffff" opacity="0.85"/>
        `);
    });
})();
</script>