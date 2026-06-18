{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/garments/tshirt.blade.php                                       ║
║                                                                            ║
║  EXACT replica of "Studio Builder · Premium Relaxed Fit T-Shirt"          ║
║  reference HTML — fabric gradients, crease shadows, stitching, collar,    ║
║  front + back views.                                                       ║
║                                                                            ║
║  Color variable mapping:                                                   ║
║    --c-body    → main body + back body + inner neck                        ║
║    --c-sleeve  → left + right sleeves                                      ║
║    --c-collar  → front + back collar ribbing                               ║
║    --c-stitch  → all stitching / thread lines                              ║
║                                                                            ║
║  Wired for the Live Preview Engine (show.blade.php / Alpine.js):           ║
║    • CSS vars drive all fills/strokes — inherited from #garment-wrapper.   ║
║    • Zone groups dispatch zone:open custom events (caught by Alpine).      ║
║    • .zone-outline / .zone-badge driven by CSS in show.blade.php.          ║
║    • Content layers injected at runtime by designEngine().                 ║
║                                                                            ║
║  Variables:                                                                ║
║    $zones       – zone defs array [['key','label','type'], …]              ║
║    $zoneCoords  – coordinate map passed from show.blade.php                ║
║    $defaults    – ['body'=>'#hex','sleeve'=>'#hex',…]                      ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $activeZoneKeys = array_column($zones, 'key');
    $isZone = fn(string $k) => in_array($k, $activeZoneKeys, true);
@endphp

<style>
#garment-wrapper {
    --c-body:   {{ $defaults['body']   ?? '#f3f4f6' }};
    --c-sleeve: {{ $defaults['sleeve'] ?? '#f3f4f6' }};
    --c-collar: {{ $defaults['collar'] ?? '#e5e7eb' }};
    --c-stitch: {{ $defaults['stitch'] ?? '#9ca3af' }};
}
/* Mirror reference SVG classes */
#garment-wrapper .t-vector-outline {
    stroke: rgba(0,0,0,0.30); stroke-width: 2;
    stroke-linejoin: round; fill: none;
}
#garment-wrapper .t-vector-line {
    stroke: rgba(0,0,0,0.15); stroke-width: 1.5;
    stroke-linejoin: round; stroke-linecap: round; fill: none;
}
#garment-wrapper .t-stitching {
    fill: none; stroke-width: 1.5;
    stroke-dasharray: 4 4; stroke-linecap: round; opacity: 0.8;
}
#garment-wrapper .t-seam {
    fill: none; stroke: rgba(0,0,0,0.10); stroke-width: 2;
}
</style>

{{-- ══════════════════════════════════════════════════════════════════════════
     FRONT SVG  — id="view-front"
     viewBox matches reference: 400 × 500
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-front"
     viewBox="0 0 400 500"
     xmlns="http://www.w3.org/2000/svg"
     style="width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="T-Shirt — Front">

  <defs>
    <linearGradient id="ts-fabricGradient" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%"   stop-color="#000" stop-opacity="0.10"/>
      <stop offset="15%"  stop-color="#fff" stop-opacity="0.05"/>
      <stop offset="50%"  stop-color="#fff" stop-opacity="0.15"/>
      <stop offset="85%"  stop-color="#fff" stop-opacity="0.00"/>
      <stop offset="100%" stop-color="#000" stop-opacity="0.15"/>
    </linearGradient>
    <linearGradient id="ts-collarGradient" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%"   stop-color="#000" stop-opacity="0.15"/>
      <stop offset="100%" stop-color="#fff" stop-opacity="0.05"/>
    </linearGradient>
    <linearGradient id="ts-innerNeckGradient" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%"   stop-color="#000" stop-opacity="0.30"/>
      <stop offset="100%" stop-color="#000" stop-opacity="0.10"/>
    </linearGradient>
    <filter id="ts-sleeveShadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#000" flood-opacity="0.08"/>
    </filter>
    <filter id="ts-creaseBlur" x="-50%" y="-50%" width="200%" height="200%">
      <feGaussianBlur stdDeviation="4"/>
    </filter>
    <filter id="ts-shadow" x="-10%" y="-8%" width="120%" height="126%">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.14"/>
    </filter>
  </defs>

  <g filter="url(#ts-shadow)">

    {{-- ── Sleeves (shared front+back, outside the view groups) ─────────── --}}
    <g filter="url(#ts-sleeveShadow)">
      {{-- Left sleeve --}}
      <path id="ts-sleeveL" class="t-vector-outline"
            d="M 85 85 C 65 100, 45 120, 25 145 C 30 170, 40 210, 50 235 C 70 225, 90 215, 105 210 C 100 175, 95 135, 85 85 Z"
            fill="var(--c-sleeve)"/>
      <use href="#ts-sleeveL" fill="url(#ts-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>
      <path d="M 32 165 C 38 185, 48 215, 55 230"
            class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

      {{-- Right sleeve --}}
      <path id="ts-sleeveR" class="t-vector-outline"
            d="M 315 85 C 335 100, 355 120, 375 145 C 370 170, 360 210, 350 235 C 330 225, 310 215, 295 210 C 300 175, 305 135, 315 85 Z"
            fill="var(--c-sleeve)"/>
      <use href="#ts-sleeveR" fill="url(#ts-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>
      <path d="M 368 165 C 362 185, 352 215, 345 230"
            class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>
    </g>

    {{-- ── Inner neck shadow ───────────────────────────────────────────── --}}
    <path id="ts-innerNeck" class="t-vector-outline"
          d="M 149 60 C 175 75, 225 75, 251 60 C 225 90, 175 90, 149 60 Z"
          fill="var(--c-body)"/>
    <use href="#ts-innerNeck" fill="url(#ts-innerNeckGradient)" style="pointer-events:none;"/>

    {{-- ── Main body front ─────────────────────────────────────────────── --}}
    <path id="ts-bodyFront" class="t-vector-outline"
          d="M 149 60 C 175 90, 225 90, 251 60
             C 275 65, 295 75, 315 85
             C 305 135, 300 175, 295 210
             C 290 290, 285 380, 280 460
             C 230 470, 170 470, 120 460
             C 115 380, 110 290, 105 210
             C 100 175, 95 135, 85 85
             C 105 75, 125 65, 149 60 Z"
          fill="var(--c-body)"/>
    <use href="#ts-bodyFront" fill="url(#ts-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>

    {{-- Fabric creases --}}
    <path d="M 105 210 C 120 250, 125 300, 120 350"
          fill="none" stroke="#000" stroke-width="8" stroke-opacity="0.08"
          filter="url(#ts-creaseBlur)" style="pointer-events:none;"/>
    <path d="M 295 210 C 280 250, 275 300, 280 350"
          fill="none" stroke="#000" stroke-width="8" stroke-opacity="0.08"
          filter="url(#ts-creaseBlur)" style="pointer-events:none;"/>
    <path d="M 160 460 C 180 430, 220 430, 240 460"
          fill="none" stroke="#000" stroke-width="12" stroke-opacity="0.06"
          filter="url(#ts-creaseBlur)" style="pointer-events:none;"/>

    {{-- ── Front collar ────────────────────────────────────────────────── --}}
    <path id="ts-collarFront" class="t-vector-outline"
          d="M 149 60 C 175 90, 225 90, 251 60 L 255 72 C 225 105, 175 105, 145 72 Z"
          fill="var(--c-collar)"/>
    <use href="#ts-collarFront" fill="url(#ts-collarGradient)" style="pointer-events:none;"/>

    {{-- Collar inner stitch --}}
    <path d="M 148 69 C 175 100, 225 100, 252 69"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

    {{-- Shoulder seams --}}
    <path d="M 85 85 C 95 135, 100 175, 105 210"  class="t-seam" style="pointer-events:none;"/>
    <path d="M 315 85 C 305 135, 300 175, 295 210" class="t-seam" style="pointer-events:none;"/>

    {{-- Hem stitching --}}
    <path d="M 119 448 C 170 458, 230 458, 281 448"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>
    <path d="M 121 453 C 170 463, 230 463, 279 453"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

  </g>

  {{-- ══ PLACEMENT ZONES — FRONT ══════════════════════════════════════════════ --}}

  {{-- A: Left chest --}}
  @if($isZone('A'))
  <g data-zone="A"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'A'}}))"
     role="button" tabindex="0" aria-label="Zone A – Left chest"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="118" y="110" width="72" height="72" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="118" y="110" width="72" height="72" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="154" y1="110" x2="154" y2="182" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="118" y1="146" x2="190" y2="146" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="118" y1="110" x2="190" y2="182" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="190" y1="110" x2="118" y2="182" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="154" cy="146" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="154" y1="143" x2="154" y2="149" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="151" y1="146" x2="157" y2="146" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="134" y="178" width="40" height="14" rx="7" fill="#3b82f6"/>
      <text x="154" y="186" text-anchor="middle" dominant-baseline="central"
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
    <rect x="210" y="110" width="72" height="72" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="210" y="110" width="72" height="72" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="5 3" style="pointer-events:none;"/>
    <line x1="246" y1="110" x2="246" y2="182" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="210" y1="146" x2="282" y2="146" stroke="#3b82f6" stroke-width="0.6" opacity="0.35" style="pointer-events:none;"/>
    <line x1="210" y1="110" x2="282" y2="182" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <line x1="282" y1="110" x2="210" y2="182" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="246" cy="146" r="3" fill="none" stroke="#3b82f6" stroke-width="1.2"/>
      <line x1="246" y1="143" x2="246" y2="149" stroke="#3b82f6" stroke-width="1.1"/>
      <line x1="243" y1="146" x2="249" y2="146" stroke="#3b82f6" stroke-width="1.1"/>
      <rect x="226" y="178" width="40" height="14" rx="7" fill="#3b82f6"/>
      <text x="246" y="186" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">B · chest R</text>
    </g>
  </g>
  @endif

  {{-- C: Center front panel --}}
  @if($isZone('C'))
  <g data-zone="C"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'C'}}))"
     role="button" tabindex="0" aria-label="Zone C – Center front"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="140" y="220" width="120" height="120" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="140" y="220" width="120" height="120" rx="4"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 3" style="pointer-events:none;"/>
    <line x1="200" y1="220" x2="200" y2="340" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="140" y1="280" x2="260" y2="280" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="140" y1="220" x2="260" y2="340" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <line x1="260" y1="220" x2="140" y2="340" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      {{-- Corner ticks --}}
      <line x1="140" y1="234" x2="140" y2="220" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="140" y1="220" x2="154" y2="220" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="246" y1="220" x2="260" y2="220" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="260" y1="220" x2="260" y2="234" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="140" y1="326" x2="140" y2="340" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="140" y1="340" x2="154" y2="340" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="246" y1="340" x2="260" y2="340" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="260" y1="340" x2="260" y2="326" stroke="#3b82f6" stroke-width="2.5"/>
      {{-- Crosshair --}}
      <circle cx="200" cy="280" r="6" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="200" y1="271" x2="200" y2="289" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="191" y1="280" x2="209" y2="280" stroke="#3b82f6" stroke-width="1.5"/>
      <rect x="168" y="336" width="64" height="16" rx="8" fill="#3b82f6"/>
      <text x="200" y="345" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">C · front panel</text>
    </g>
  </g>
  @endif

  {{-- D1: Left sleeve --}}
  @if($isZone('D1'))
  <g data-zone="D1" transform="rotate(-20 55 155)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D1'}}))"
     role="button" tabindex="0" aria-label="Zone D1 – Left sleeve"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="34" y="133" width="42" height="42" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="34" y="133" width="42" height="42" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="55" y1="133" x2="55" y2="175" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="34" y1="154" x2="76" y2="154" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="34" y1="133" x2="76" y2="175" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="76" y1="133" x2="34" y2="175" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="55" cy="154" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="55" y1="151.5" x2="55" y2="156.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="52.5" y1="154" x2="57.5" y2="154" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="40" y="171" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="55" y="177" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">D1</text>
    </g>
  </g>
  @endif

  {{-- E1: Right sleeve --}}
  @if($isZone('E1'))
  <g data-zone="E1" transform="rotate(20 345 155)"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'E1'}}))"
     role="button" tabindex="0" aria-label="Zone E1 – Right sleeve"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="324" y="133" width="42" height="42" rx="3" fill="transparent"/>
    <rect class="zone-outline" x="324" y="133" width="42" height="42" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3" style="pointer-events:none;"/>
    <line x1="345" y1="133" x2="345" y2="175" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="324" y1="154" x2="366" y2="154" stroke="#3b82f6" stroke-width="0.5" opacity="0.30" style="pointer-events:none;"/>
    <line x1="324" y1="133" x2="366" y2="175" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <line x1="366" y1="133" x2="324" y2="175" stroke="#3b82f6" stroke-width="0.7" opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="345" cy="154" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
      <line x1="345" y1="151.5" x2="345" y2="156.5" stroke="#3b82f6" stroke-width="0.9"/>
      <line x1="342.5" y1="154" x2="347.5" y2="154" stroke="#3b82f6" stroke-width="0.9"/>
      <rect x="330" y="171" width="30" height="12" rx="6" fill="#3b82f6"/>
      <text x="345" y="177" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="7" font-weight="700" fill="#fff">E1</text>
    </g>
  </g>
  @endif

</svg>

{{-- ══════════════════════════════════════════════════════════════════════════
     BACK SVG  — id="view-back"
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-back"
     viewBox="0 0 400 500"
     xmlns="http://www.w3.org/2000/svg"
     style="display:none;width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="T-Shirt — Back">

  <defs>
    <linearGradient id="tsb-fabricGradient" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%"   stop-color="#000" stop-opacity="0.10"/>
      <stop offset="15%"  stop-color="#fff" stop-opacity="0.05"/>
      <stop offset="50%"  stop-color="#fff" stop-opacity="0.15"/>
      <stop offset="85%"  stop-color="#fff" stop-opacity="0.00"/>
      <stop offset="100%" stop-color="#000" stop-opacity="0.15"/>
    </linearGradient>
    <linearGradient id="tsb-collarGradient" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%"   stop-color="#000" stop-opacity="0.15"/>
      <stop offset="100%" stop-color="#fff" stop-opacity="0.05"/>
    </linearGradient>
    <filter id="tsb-sleeveShadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#000" flood-opacity="0.08"/>
    </filter>
    <filter id="tsb-creaseBlur" x="-50%" y="-50%" width="200%" height="200%">
      <feGaussianBlur stdDeviation="4"/>
    </filter>
    <filter id="tsb-shadow" x="-10%" y="-8%" width="120%" height="126%">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.14"/>
    </filter>
  </defs>

  <g filter="url(#tsb-shadow)">

    {{-- ── Sleeves back ─────────────────────────────────────────────────── --}}
    <g filter="url(#tsb-sleeveShadow)">
      <path id="tsb-sleeveLB" class="t-vector-outline"
            d="M 85 85 C 65 100, 45 120, 25 145 C 30 170, 40 210, 50 235 C 70 225, 90 215, 105 210 C 100 175, 95 135, 85 85 Z"
            fill="var(--c-sleeve)"/>
      <use href="#tsb-sleeveLB" fill="url(#tsb-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>
      <path d="M 32 165 C 38 185, 48 215, 55 230"
            class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

      <path id="tsb-sleeveRB" class="t-vector-outline"
            d="M 315 85 C 335 100, 355 120, 375 145 C 370 170, 360 210, 350 235 C 330 225, 310 215, 295 210 C 300 175, 305 135, 315 85 Z"
            fill="var(--c-sleeve)"/>
      <use href="#tsb-sleeveRB" fill="url(#tsb-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>
      <path d="M 368 165 C 362 185, 352 215, 345 230"
            class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>
    </g>

    {{-- ── Back body ────────────────────────────────────────────────────── --}}
    <path id="tsb-bodyBack" class="t-vector-outline"
          d="M 149 60 C 175 75, 225 75, 251 60
             C 275 65, 295 75, 315 85
             C 305 135, 300 175, 295 210
             C 290 290, 285 380, 280 460
             C 230 470, 170 470, 120 460
             C 115 380, 110 290, 105 210
             C 100 175, 95 135, 85 85
             C 105 75, 125 65, 149 60 Z"
          fill="var(--c-body)"/>
    <use href="#tsb-bodyBack" fill="url(#tsb-fabricGradient)" style="pointer-events:none;mix-blend-mode:multiply;"/>

    {{-- Fabric creases --}}
    <path d="M 105 210 C 120 250, 125 300, 120 350"
          fill="none" stroke="#000" stroke-width="8" stroke-opacity="0.08"
          filter="url(#tsb-creaseBlur)" style="pointer-events:none;"/>
    <path d="M 295 210 C 280 250, 275 300, 280 350"
          fill="none" stroke="#000" stroke-width="8" stroke-opacity="0.08"
          filter="url(#tsb-creaseBlur)" style="pointer-events:none;"/>

    {{-- ── Back collar ──────────────────────────────────────────────────── --}}
    <path id="tsb-collarBack" class="t-vector-outline"
          d="M 149 60 C 175 75, 225 75, 251 60 L 255 48 C 225 63, 175 63, 145 48 Z"
          fill="var(--c-collar)"/>
    <use href="#tsb-collarBack" fill="url(#tsb-collarGradient)" style="pointer-events:none;"/>

    {{-- Collar back stitch --}}
    <path d="M 148 57 C 175 72, 225 72, 252 57"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

    {{-- Shoulder seams --}}
    <path d="M 85 85 C 95 135, 100 175, 105 210"  class="t-seam" style="pointer-events:none;"/>
    <path d="M 315 85 C 305 135, 300 175, 295 210" class="t-seam" style="pointer-events:none;"/>

    {{-- Hem stitching --}}
    <path d="M 119 448 C 170 458, 230 458, 281 448"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>
    <path d="M 121 453 C 170 463, 230 463, 279 453"
          class="t-stitching" stroke="var(--c-stitch)" style="pointer-events:none;"/>

  </g>

  {{-- ══ PLACEMENT ZONES — BACK ════════════════════════════════════════════════ --}}

  {{-- F: Large back panel --}}
  @if($isZone('F'))
  <g data-zone="F"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'F'}}))"
     role="button" tabindex="0" aria-label="Zone F – Back panel"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
    <rect x="130" y="150" width="140" height="160" rx="5" fill="transparent"/>
    <rect class="zone-outline" x="130" y="150" width="140" height="160" rx="5"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 3" style="pointer-events:none;"/>
    <line x1="200" y1="150" x2="200" y2="310" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="130" y1="230" x2="270" y2="230" stroke="#3b82f6" stroke-width="0.7" opacity="0.28" style="pointer-events:none;"/>
    <line x1="130" y1="150" x2="270" y2="310" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <line x1="270" y1="150" x2="130" y2="310" stroke="#3b82f6" stroke-width="1"   opacity="0.16" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      {{-- Corner ticks --}}
      <line x1="130" y1="164" x2="130" y2="150" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="130" y1="150" x2="144" y2="150" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="256" y1="150" x2="270" y2="150" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="270" y1="150" x2="270" y2="164" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="130" y1="296" x2="130" y2="310" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="130" y1="310" x2="144" y2="310" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="256" y1="310" x2="270" y2="310" stroke="#3b82f6" stroke-width="2.5"/>
      <line x1="270" y1="310" x2="270" y2="296" stroke="#3b82f6" stroke-width="2.5"/>
      {{-- Crosshair --}}
      <circle cx="200" cy="230" r="6" fill="none" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="200" y1="221" x2="200" y2="239" stroke="#3b82f6" stroke-width="1.5"/>
      <line x1="191" y1="230" x2="209" y2="230" stroke="#3b82f6" stroke-width="1.5"/>
      <rect x="168" y="306" width="64" height="16" rx="8" fill="#3b82f6"/>
      <text x="200" y="315" text-anchor="middle" dominant-baseline="central"
            font-family="system-ui,sans-serif" font-size="8" font-weight="700" fill="#fff">F · back panel</text>
    </g>
  </g>
  @endif

  {{-- D1-back: left sleeve indicator (decorative, no click) --}}
  @if($isZone('D1'))
  <g style="pointer-events:none;" transform="rotate(-20 55 155)">
    <rect x="34" y="133" width="42" height="42" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="55" y1="133" x2="55" y2="175" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="34" y1="154" x2="76" y2="154" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="55" cy="154" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="55" y1="151.5" x2="55" y2="156.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="52.5" y1="154" x2="57.5" y2="154" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif

  {{-- E1-back: right sleeve indicator (decorative, no click) --}}
  @if($isZone('E1'))
  <g style="pointer-events:none;" transform="rotate(20 345 155)">
    <rect x="324" y="133" width="42" height="42" rx="3"
          fill="rgba(59,130,246,0.10)" stroke="#3b82f6" stroke-width="1.3" stroke-dasharray="4 3"/>
    <line x1="345" y1="133" x2="345" y2="175" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <line x1="324" y1="154" x2="366" y2="154" stroke="#3b82f6" stroke-width="0.5" opacity="0.30"/>
    <circle cx="345" cy="154" r="2.5" fill="none" stroke="#3b82f6" stroke-width="1"/>
    <line x1="345" y1="151.5" x2="345" y2="156.5" stroke="#3b82f6" stroke-width="0.9"/>
    <line x1="342.5" y1="154" x2="347.5" y2="154" stroke="#3b82f6" stroke-width="0.9"/>
  </g>
  @endif

</svg>