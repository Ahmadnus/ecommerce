{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/garments/stole.blade.php                                        ║
║                                                                            ║
║  Graduation Stole — flat 2D SVG.                                           ║
║  Shape: straight horizontal top edge, vertical outer edges,                ║
║  V-neck opening from the top, sharp pointed tips at the bottom.            ║
║                                                                            ║
║  CSS variables:                                                            ║
║    --c-main    → body fill (both panels)                                   ║
║    --c-border  → all border/trim strokes                                   ║
║                                                                            ║
║  Zones:                                                                    ║
║    A — left panel upper   (text + image)                                   ║
║    B — left panel lower   (text + image)                                   ║
║    C — right panel upper  (text + image)                                   ║
║    D — right panel lower  (text + image)                                   ║
║                                                                            ║
║  Variables passed in:                                                      ║
║    $zones      – zone defs [['key','label','type'],…]                      ║
║    $zoneCoords – coordinate map from show.blade.php                        ║
║    $defaults   – ['main'=>'#hex','border'=>'#hex']                         ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@php
    $activeZoneKeys = array_column($zones, 'key');
    $isZone = fn(string $k) => in_array($k, $activeZoneKeys, true);
@endphp

<style>
#garment-wrapper {
    --c-main:   {{ $defaults['main']   ?? '#111111' }};
    --c-border: {{ $defaults['border'] ?? '#d4a017' }};
}
</style>

<svg id="view-front"
     viewBox="0 0 500 780"
     xmlns="http://www.w3.org/2000/svg"
     style="width:100%;height:auto;overflow:visible;"
     role="img"
     aria-label="Graduation Stole">

  <g id="stole">

    {{-- ══════════════════════════════════════════════════════════════════
         GROUP: left-panel
         Top edge: y=20 (flat, horizontal, full panel width)
         Outer left edge: vertical from (60,20) down to (60,680)
         Inner edge: vertical from (200,20) down to neck-V then panel
         Bottom: sharp point at (130,755)
    ══════════════════════════════════════════════════════════════════════ --}}
    <g id="left-panel">

      {{-- Body fill --}}
      <polygon id="left-panel-body"
               points="60,20 200,20 200,700 130,755 60,700"
               fill="var(--c-main)"/>

      {{-- Left outer border stripe (14px) --}}
      <polygon id="left-outer-border"
               points="60,20 74,20 74,696 130,749 130,755 60,700"
               fill="var(--c-border)"
               style="pointer-events:none;"/>

      {{-- Left inner border stripe (14px, beside neck gap) --}}
      <polygon id="left-inner-border"
               points="186,20 200,20 200,700 130,755 130,749 186,696"
               fill="var(--c-border)"
               style="pointer-events:none;"/>

      {{-- Panel outline --}}
      <polygon points="60,20 200,20 200,700 130,755 60,700"
               fill="none"
               stroke="var(--c-border)"
               stroke-width="2"
               stroke-linejoin="round"
               style="pointer-events:none;"/>

    </g>

    {{-- ══════════════════════════════════════════════════════════════════
         GROUP: right-panel
         Mirror of left-panel.
         Outer right edge: (440,20)→(440,700)
         Inner edge: (300,20) then down into neck V
    ══════════════════════════════════════════════════════════════════════ --}}
    <g id="right-panel">

      {{-- Body fill --}}
      <polygon id="right-panel-body"
               points="300,20 440,20 440,700 370,755 300,700"
               fill="var(--c-main)"/>

      {{-- Right outer border stripe (14px) --}}
      <polygon id="right-outer-border"
               points="426,20 440,20 440,700 370,755 370,749 426,696"
               fill="var(--c-border)"
               style="pointer-events:none;"/>

      {{-- Right inner border stripe (14px, beside neck gap) --}}
      <polygon id="right-inner-border"
               points="300,20 314,20 314,696 370,749 370,755 300,700"
               fill="var(--c-border)"
               style="pointer-events:none;"/>

      {{-- Panel outline --}}
      <polygon points="300,20 440,20 440,700 370,755 300,700"
               fill="none"
               stroke="var(--c-border)"
               stroke-width="2"
               stroke-linejoin="round"
               style="pointer-events:none;"/>

    </g>

    {{-- ══════════════════════════════════════════════════════════════════
         GROUP: borders
         The V-neck cut between the two panels.
         Both panels share y=20 as the flat top.
         The V opening is cut from (200,20) down to center (250,160)
         and back up to (300,20), matching the reference diagram.

         Outer V outline  — the gold edge visible at front
         Inner V crease   — inner fold line offset ~14px
    ══════════════════════════════════════════════════════════════════════ --}}
    <g id="borders" style="pointer-events:none;">

      {{-- Fill the V gap with main color so it reads as open space --}}
      <polygon id="neck-v-fill"
               points="200,20 250,160 300,20"
               fill="var(--c-main)"/>

      {{-- Outer V left border stripe --}}
      <polygon id="neck-v-left-border"
               points="200,20 186,20 236,158 250,160"
               fill="var(--c-border)"/>

      {{-- Outer V right border stripe --}}
      <polygon id="neck-v-right-border"
               points="300,20 314,20 264,158 250,160"
               fill="var(--c-border)"/>

      {{-- Outer V outline strokes --}}
      <polyline points="200,20 250,160 300,20"
                fill="none"
                stroke="var(--c-border)"
                stroke-width="2.2"
                stroke-linejoin="round"
                stroke-linecap="round"/>

      {{-- Inner V crease line (14px inset) --}}
      <polyline points="186,20 236,158 250,160 264,158 314,20"
                fill="none"
                stroke="var(--c-border)"
                stroke-width="1.5"
                stroke-linejoin="round"
                stroke-linecap="round"
                opacity="0.8"/>

      {{-- Horizontal top edge border stripe across both panels (full width) --}}
      <line x1="60"  y1="20" x2="200" y2="20"
            stroke="var(--c-border)" stroke-width="14" stroke-linecap="butt"/>
      <line x1="300" y1="20" x2="440" y2="20"
            stroke="var(--c-border)" stroke-width="14" stroke-linecap="butt"/>

      {{-- Re-draw the top outlines on top of the thick border line --}}
      <line x1="60"  y1="20" x2="200" y2="20"
            stroke="var(--c-border)" stroke-width="2" stroke-linecap="butt"/>
      <line x1="300" y1="20" x2="440" y2="20"
            stroke="var(--c-border)" stroke-width="2" stroke-linecap="butt"/>

    </g>

  </g>{{-- /stole --}}

  {{-- ══════════════════════════════════════════════════════════════════════
       GROUP: zones
       4 customization zones with dashed outlines, zone letter, label.
  ══════════════════════════════════════════════════════════════════════════ --}}
  <g id="zones">

    {{-- Zone A — left panel upper --}}
    @if($isZone('A'))
    <g id="zone-a"
       class="zone"
       data-zone="A"
       onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'A'}}))"
       role="button" tabindex="0" aria-label="Zone A – Left upper"
       onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
      <rect x="76" y="36" width="108" height="130" rx="4" fill="transparent"/>
      <rect class="zone-outline" x="76" y="36" width="108" height="130" rx="4"
            fill="rgba(59,130,246,0.09)"
            stroke="#3b82f6" stroke-width="1.8" stroke-dasharray="6 3"
            style="pointer-events:none;"/>
      <line x1="130" y1="36"  x2="130" y2="166" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="76"  y1="101" x2="184" y2="101" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="76"  y1="36"  x2="184" y2="166" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <line x1="184" y1="36"  x2="76"  y2="166" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <g class="zone-badge" style="pointer-events:none;">
        <circle cx="130" cy="101" r="16" fill="rgba(59,130,246,0.25)" stroke="#3b82f6" stroke-width="1.5"/>
        <text x="130" y="101"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="14" font-weight="700" fill="#ffffff">A</text>
        <rect x="88" y="158" width="84" height="16" rx="8" fill="#3b82f6"/>
        <text x="130" y="166"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="9" font-weight="700" fill="#ffffff">left upper</text>
      </g>
    </g>
    @endif

    {{-- Zone B — left panel lower --}}
    @if($isZone('B'))
    <g id="zone-b"
       class="zone"
       data-zone="B"
       onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'B'}}))"
       role="button" tabindex="0" aria-label="Zone B – Left lower"
       onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
      <rect x="76" y="480" width="108" height="155" rx="4" fill="transparent"/>
      <rect class="zone-outline" x="76" y="480" width="108" height="155" rx="4"
            fill="rgba(59,130,246,0.09)"
            stroke="#3b82f6" stroke-width="1.8" stroke-dasharray="6 3"
            style="pointer-events:none;"/>
      <line x1="130" y1="480" x2="130" y2="635" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="76"  y1="557" x2="184" y2="557" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="76"  y1="480" x2="184" y2="635" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <line x1="184" y1="480" x2="76"  y2="635" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <g class="zone-badge" style="pointer-events:none;">
        {{-- Corner registration ticks --}}
        <line x1="76"  y1="494" x2="76"  y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="76"  y1="480" x2="90"  y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="170" y1="480" x2="184" y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="184" y1="480" x2="184" y2="494" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="76"  y1="621" x2="76"  y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="76"  y1="635" x2="90"  y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="170" y1="635" x2="184" y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="184" y1="635" x2="184" y2="621" stroke="#3b82f6" stroke-width="2.2"/>
        {{-- Crosshair --}}
        <circle cx="130" cy="557" r="16" fill="rgba(59,130,246,0.25)" stroke="#3b82f6" stroke-width="1.5"/>
        <text x="130" y="557"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="14" font-weight="700" fill="#ffffff">B</text>
        <rect x="88" y="627" width="84" height="16" rx="8" fill="#3b82f6"/>
        <text x="130" y="635"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="9" font-weight="700" fill="#ffffff">left lower</text>
      </g>
    </g>
    @endif

    {{-- Zone C — right panel upper --}}
    @if($isZone('C'))
    <g id="zone-c"
       class="zone"
       data-zone="C"
       onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'C'}}))"
       role="button" tabindex="0" aria-label="Zone C – Right upper"
       onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
      <rect x="316" y="36" width="108" height="130" rx="4" fill="transparent"/>
      <rect class="zone-outline" x="316" y="36" width="108" height="130" rx="4"
            fill="rgba(59,130,246,0.09)"
            stroke="#3b82f6" stroke-width="1.8" stroke-dasharray="6 3"
            style="pointer-events:none;"/>
      <line x1="370" y1="36"  x2="370" y2="166" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="316" y1="101" x2="424" y2="101" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="316" y1="36"  x2="424" y2="166" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <line x1="424" y1="36"  x2="316" y2="166" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <g class="zone-badge" style="pointer-events:none;">
        <circle cx="370" cy="101" r="16" fill="rgba(59,130,246,0.25)" stroke="#3b82f6" stroke-width="1.5"/>
        <text x="370" y="101"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="14" font-weight="700" fill="#ffffff">C</text>
        <rect x="328" y="158" width="84" height="16" rx="8" fill="#3b82f6"/>
        <text x="370" y="166"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="9" font-weight="700" fill="#ffffff">right upper</text>
      </g>
    </g>
    @endif

    {{-- Zone D — right panel lower --}}
    @if($isZone('D'))
    <g id="zone-d"
       class="zone"
       data-zone="D"
       onclick="window.dispatchEvent(new CustomEvent('zone:open',{detail:{key:'D'}}))"
       role="button" tabindex="0" aria-label="Zone D – Right lower"
       onkeydown="if(event.key==='Enter'||event.key===' ')this.click()">
      <rect x="316" y="480" width="108" height="155" rx="4" fill="transparent"/>
      <rect class="zone-outline" x="316" y="480" width="108" height="155" rx="4"
            fill="rgba(59,130,246,0.09)"
            stroke="#3b82f6" stroke-width="1.8" stroke-dasharray="6 3"
            style="pointer-events:none;"/>
      <line x1="370" y1="480" x2="370" y2="635" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="316" y1="557" x2="424" y2="557" stroke="#3b82f6" stroke-width="0.7" opacity="0.35" style="pointer-events:none;"/>
      <line x1="316" y1="480" x2="424" y2="635" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <line x1="424" y1="480" x2="316" y2="635" stroke="#3b82f6" stroke-width="0.8" opacity="0.18" style="pointer-events:none;"/>
      <g class="zone-badge" style="pointer-events:none;">
        <line x1="316" y1="494" x2="316" y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="316" y1="480" x2="330" y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="410" y1="480" x2="424" y2="480" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="424" y1="480" x2="424" y2="494" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="316" y1="621" x2="316" y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="316" y1="635" x2="330" y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="410" y1="635" x2="424" y2="635" stroke="#3b82f6" stroke-width="2.2"/>
        <line x1="424" y1="635" x2="424" y2="621" stroke="#3b82f6" stroke-width="2.2"/>
        <circle cx="370" cy="557" r="16" fill="rgba(59,130,246,0.25)" stroke="#3b82f6" stroke-width="1.5"/>
        <text x="370" y="557"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="14" font-weight="700" fill="#ffffff">D</text>
        <rect x="328" y="627" width="84" height="16" rx="8" fill="#3b82f6"/>
        <text x="370" y="635"
              text-anchor="middle" dominant-baseline="central"
              font-family="system-ui,sans-serif"
              font-size="9" font-weight="700" fill="#ffffff">right lower</text>
      </g>
    </g>
    @endif

  </g>{{-- /zones --}}

</svg>