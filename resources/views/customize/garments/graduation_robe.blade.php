{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/garments/graduation_robe.blade.php                              ║
║                                                                            ║
║  EXACT replica of "مصمم أثواب التخرج | Placement Zones" reference HTML.   ║
║  Full yoke bands (3 layers), wide sleeves, front/back, fold lines.         ║
║                                                                            ║
║  Color variable mapping (different from jacket/hoodie):                    ║
║    --c-main   → body + sleeves (main robe colour)                          ║
║    --c-yoke1  → outer yoke stripe (lowest layer)                           ║
║    --c-yoke2  → middle yoke stripe                                         ║
║    --c-yoke3  → inner yoke stripe (highest / collar)                       ║
║    --c-line   → outline / stroke colour                                    ║
║                                                                            ║
║  Wired for the Live Preview Engine:                                        ║
║    • CSS vars drive all fills/strokes.                                     ║
║    • Zone groups dispatch zone:open custom events.                         ║
║    • .zone-outline / .zone-badge driven by CSS in show.blade.php.          ║
║                                                                            ║
║  Variables:                                                                ║
║    $zones       – zone defs array [['key','label','type'], …]              ║
║    $zoneCoords  – coordinate map passed from show.blade.php                ║
║    $defaults    – ['main'=>'#hex','yoke1'=>'#hex',…,'line'=>'#hex']        ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $activeZoneKeys = array_column($zones, 'key');
    $isZone = fn(string $k) => in_array($k, $activeZoneKeys, true);
@endphp

<style>
#garment-wrapper {
    --c-main:  {{ $defaults['main']  ?? '#ffffff' }};
    --c-yoke1: {{ $defaults['yoke1'] ?? '#ffffff' }};
    --c-yoke2: {{ $defaults['yoke2'] ?? '#ffffff' }};
    --c-yoke3: {{ $defaults['yoke3'] ?? '#ffffff' }};
    --c-line:  {{ $defaults['line']  ?? '#111111' }};
}
</style>

{{-- ══════════════════════════════════════════════════════════════════════════
     FRONT SVG  — id="view-front"
     viewBox matches reference: 500 × 600
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-front"
     viewBox="0 0 500 600"
     xmlns="http://www.w3.org/2000/svg"
     style="width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="ثوب التخرج — الأمامي">

  <defs>
    {{-- All shape paths reused with <use> — exact mirror of reference --}}
    <path id="rg-l-sleeve" d="M 145 60 C 90 90, 40 140, 15 200 L 70 340 C 90 300, 110 270, 135 240 Z"/>
    <path id="rg-r-sleeve" d="M 355 60 C 410 90, 460 140, 485 200 L 430 340 C 410 300, 390 270, 365 240 Z"/>
    <path id="rg-l-body"   d="M 245 20 C 195 20, 165 35, 145 60 C 135 120, 135 180, 140 240 C 135 320, 130 400, 130 500 C 130 560, 135 585, 155 588 C 185 592, 215 590, 245 590 L 245 20 Z"/>
    <path id="rg-r-body"   d="M 255 20 C 305 20, 335 35, 355 60 C 365 120, 365 180, 360 240 C 365 320, 370 400, 370 500 C 370 560, 365 585, 345 588 C 315 592, 285 590, 255 590 L 255 20 Z"/>
    {{-- Yoke band layer 1 (outermost) --}}
    <path id="rg-l-y1" d="M 145 60 C 165 140, 205 180, 245 180 L 245 140 C 215 140, 180 100, 165 45 L 145 60 Z"/>
    <path id="rg-r-y1" d="M 355 60 C 335 140, 295 180, 255 180 L 255 140 C 285 140, 320 100, 335 45 L 355 60 Z"/>
    {{-- Yoke band layer 2 (middle) --}}
    <path id="rg-l-y2" d="M 165 45 C 180 100, 215 140, 245 140 L 245 100 C 225 100, 195 70, 185 35 L 165 45 Z"/>
    <path id="rg-r-y2" d="M 335 45 C 320 100, 285 140, 255 140 L 255 100 C 275 100, 305 70, 315 35 L 335 45 Z"/>
    {{-- Yoke band layer 3 (innermost / collar) --}}
    <path id="rg-l-y3" d="M 185 35 C 195 70, 225 100, 245 100 L 245 20 C 215 20, 200 25, 185 35 Z"/>
    <path id="rg-r-y3" d="M 315 35 C 305 70, 275 100, 255 100 L 255 20 C 285 20, 300 25, 315 35 Z"/>
    {{-- Hem fold lines --}}
    <path id="rg-simple-folds"
          d="M 165 589 C 165 480, 170 380, 180 280
             M 215 591 C 205 490, 210 390, 220 290
             M 335 589 C 335 480, 330 380, 320 280
             M 285 591 C 295 490, 290 390, 280 290"/>
    <filter id="rg-shadow" x="-8%" y="-5%" width="116%" height="120%">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.18"/>
    </filter>
  </defs>

  <g filter="url(#rg-shadow)">
    {{-- ── Fills ──────────────────────────────────────────────────────────── --}}
    <use href="#rg-l-sleeve" fill="var(--c-main)"/>
    <use href="#rg-r-sleeve" fill="var(--c-main)"/>
    <use href="#rg-l-body"   fill="var(--c-main)"/>
    <use href="#rg-r-body"   fill="var(--c-main)"/>
    {{-- Yoke stripes --}}
    <use href="#rg-l-y1" fill="var(--c-yoke1)"/>
    <use href="#rg-r-y1" fill="var(--c-yoke1)"/>
    <use href="#rg-l-y2" fill="var(--c-yoke2)"/>
    <use href="#rg-r-y2" fill="var(--c-yoke2)"/>
    <use href="#rg-l-y3" fill="var(--c-yoke3)"/>
    <use href="#rg-r-y3" fill="var(--c-yoke3)"/>

    {{-- ── Outlines (stroke only, same paths) ────────────────────────────── --}}
    <g stroke="var(--c-line)" stroke-width="6" fill="none"
       stroke-linejoin="round" stroke-linecap="round"
       style="pointer-events:none;">
      <use href="#rg-l-sleeve"/>
      <use href="#rg-r-sleeve"/>
      <use href="#rg-l-body"/>
      <use href="#rg-r-body"/>
      <use href="#rg-simple-folds"/>
      <use href="#rg-l-y1"/>
      <use href="#rg-r-y1"/>
      <use href="#rg-l-y2"/>
      <use href="#rg-r-y2"/>
      <use href="#rg-l-y3"/>
      <use href="#rg-r-y3"/>
    </g>
  </g>

  {{-- ══ PLACEMENT ZONES — FRONT ══════════════════════════════════════════════
       Zone numbers match the reference HTML: 1 (right chest), 2 (left chest),
       5 (left sleeve), 6 (right sleeve).
       Keys are strings '1','2','5','6' to match GARMENT_CONFIGS in controller.
  ══════════════════════════════════════════════════════════════════════════════ --}}

  {{-- Zone 1: Right chest (translate 285,220) --}}
  @if($isZone('1'))
  <g data-zone="1"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'1'}}))"
     role="button" tabindex="0" aria-label="المنطقة 1 – الصدر الأيمن"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(285,220)">
    <rect x="0" y="0" width="50" height="50" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="0" y="0" width="50" height="50" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="0" y1="0" x2="50" y2="50" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="50" y1="0" x2="0" y2="50" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="25" cy="25" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="25" y="27" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">1</text>
    </g>
  </g>
  @endif

  {{-- Zone 2: Left chest (translate 165,220) --}}
  @if($isZone('2'))
  <g data-zone="2"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'2'}}))"
     role="button" tabindex="0" aria-label="المنطقة 2 – الصدر الأيسر"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(165,220)">
    <rect x="0" y="0" width="50" height="50" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="0" y="0" width="50" height="50" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="0" y1="0" x2="50" y2="50" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="50" y1="0" x2="0" y2="50" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="25" cy="25" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="25" y="27" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">2</text>
    </g>
  </g>
  @endif

  {{-- Zone 5: Left sleeve (translate 80,210 rotate 25) --}}
  @if($isZone('5'))
  <g data-zone="5"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'5'}}))"
     role="button" tabindex="0" aria-label="المنطقة 5 – الكم الأيسر"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(80,210) rotate(25)">
    <rect x="-25" y="-40" width="50" height="80" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="-25" y="-40" width="50" height="80" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="-25" y1="-40" x2="25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="25"  y1="-40" x2="-25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="0" cy="0" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="0" y="2" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">5</text>
    </g>
  </g>
  @endif

  {{-- Zone 6: Right sleeve (translate 420,210 rotate -25) --}}
  @if($isZone('6'))
  <g data-zone="6"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'6'}}))"
     role="button" tabindex="0" aria-label="المنطقة 6 – الكم الأيمن"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(420,210) rotate(-25)">
    <rect x="-25" y="-40" width="50" height="80" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="-25" y="-40" width="50" height="80" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="-25" y1="-40" x2="25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="25"  y1="-40" x2="-25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="0" cy="0" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="0" y="2" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">6</text>
    </g>
  </g>
  @endif

</svg>

{{-- ══════════════════════════════════════════════════════════════════════════
     BACK SVG  — id="view-back"
══════════════════════════════════════════════════════════════════════════════ --}}
<svg id="view-back"
     viewBox="0 0 500 600"
     xmlns="http://www.w3.org/2000/svg"
     style="display:none;width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="ثوب التخرج — الخلفي">

  <defs>
    <path id="rgb-l-sleeve"  d="M 145 60 C 90 90, 40 140, 15 200 L 70 340 C 90 300, 110 270, 135 240 Z"/>
    <path id="rgb-r-sleeve"  d="M 355 60 C 410 90, 460 140, 485 200 L 430 340 C 410 300, 390 270, 365 240 Z"/>
    <path id="rgb-b-body"    d="M 250 20 C 305 20, 335 35, 355 60 C 365 120, 365 180, 360 240 C 365 320, 370 400, 370 500 C 370 560, 365 585, 345 588 C 315 592, 285 590, 250 590 C 215 590, 185 592, 155 588 C 135 585, 130 560, 130 500 C 130 400, 135 320, 140 240 C 135 180, 135 120, 145 60 C 165 35, 195 20, 250 20 Z"/>
    <path id="rgb-b-yoke"    d="M 145 60 C 185 140, 315 140, 355 60 C 335 35, 305 20, 250 20 C 195 20, 165 35, 145 60 Z"/>
    <path id="rgb-b-folds"   d="M 165 589 C 165 480, 170 380, 180 280 M 215 591 C 205 490, 210 390, 220 290 M 335 589 C 335 480, 330 380, 320 280 M 285 591 C 295 490, 290 390, 280 290"/>
    <filter id="rgb-shadow" x="-8%" y="-5%" width="116%" height="120%">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#000" flood-opacity="0.18"/>
    </filter>
  </defs>

  <g filter="url(#rgb-shadow)">
    {{-- Fills --}}
    <use href="#rgb-l-sleeve" fill="var(--c-main)"/>
    <use href="#rgb-r-sleeve" fill="var(--c-main)"/>
    <use href="#rgb-b-body"   fill="var(--c-main)"/>
    {{-- Back yoke uses yoke1 colour --}}
    <use href="#rgb-b-yoke"   fill="var(--c-yoke1)"/>

    {{-- Outlines --}}
    <g stroke="var(--c-line)" stroke-width="6" fill="none"
       stroke-linejoin="round" stroke-linecap="round"
       style="pointer-events:none;">
      <use href="#rgb-l-sleeve"/>
      <use href="#rgb-r-sleeve"/>
      <use href="#rgb-b-body"/>
      <use href="#rgb-b-folds"/>
      <use href="#rgb-b-yoke"/>
    </g>
  </g>

  {{-- ══ PLACEMENT ZONES — BACK ════════════════════════════════════════════════
       Zone 4: Large back panel (translate 180,210)
       Zones 5 & 6: Same sleeve positions as front.
  ══════════════════════════════════════════════════════════════════════════════ --}}

  {{-- Zone 4: Large back panel --}}
  @if($isZone('4'))
  <g data-zone="4"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'4'}}))"
     role="button" tabindex="0" aria-label="المنطقة 4 – الظهر الكبير"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(180,210)">
    <rect x="0" y="0" width="140" height="180" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="0" y="0" width="140" height="180" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    {{-- Corner registration marks --}}
    <g style="pointer-events:none;" class="zone-badge">
      <path d="M 15 0 L 0 0 L 0 15" stroke="#3b82f6" stroke-width="3" fill="none"/>
      <path d="M 125 0 L 140 0 L 140 15" stroke="#3b82f6" stroke-width="3" fill="none"/>
      <path d="M 140 165 L 140 180 L 125 180" stroke="#3b82f6" stroke-width="3" fill="none"/>
      <path d="M 15 180 L 0 180 L 0 165" stroke="#3b82f6" stroke-width="3" fill="none"/>
      <line x1="0" y1="0" x2="140" y2="180" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7"/>
      <line x1="140" y1="0" x2="0" y2="180" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7"/>
      <circle cx="70" cy="90" r="18" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="70" y="92" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="18" font-weight="700" fill="#1e3a8a">4</text>
    </g>
  </g>
  @endif

  {{-- Zone 5: Left sleeve back (same position as front) --}}
  @if($isZone('5'))
  <g data-zone="5"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'5'}}))"
     role="button" tabindex="0" aria-label="المنطقة 5 – الكم الأيسر"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(80,210) rotate(25)">
    <rect x="-25" y="-40" width="50" height="80" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="-25" y="-40" width="50" height="80" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="-25" y1="-40" x2="25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="25"  y1="-40" x2="-25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="0" cy="0" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="0" y="2" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">5</text>
    </g>
  </g>
  @endif

  {{-- Zone 6: Right sleeve back (same position as front) --}}
  @if($isZone('6'))
  <g data-zone="6"
     onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'6'}}))"
     role="button" tabindex="0" aria-label="المنطقة 6 – الكم الأيمن"
     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
     transform="translate(420,210) rotate(-25)">
    <rect x="-25" y="-40" width="50" height="80" rx="4" fill="transparent"/>
    <rect class="zone-outline" x="-25" y="-40" width="50" height="80" rx="4"
          fill="rgba(59,130,246,0.08)" stroke="#3b82f6" stroke-width="2" stroke-dasharray="6 4" style="pointer-events:none;"/>
    <line x1="-25" y1="-40" x2="25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <line x1="25"  y1="-40" x2="-25" y2="40" stroke="#3b82f6" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.7" style="pointer-events:none;"/>
    <g class="zone-badge" style="pointer-events:none;">
      <circle cx="0" cy="0" r="14" fill="#fff" stroke="#3b82f6" stroke-width="2"/>
      <text x="0" y="2" text-anchor="middle" dominant-baseline="central"
            font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#1e3a8a">6</text>
    </g>
  </g>
  @endif

</svg>