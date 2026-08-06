{{--
  customize/index-svgs/robe-thumb.blade.php
  Compact SVG thumbnail of the Graduation Robe for the product picker card.
  Based on the full SVG from the "مصمم أثواب التخرج" design.
--}}
<svg viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
    <defs>
        <linearGradient id="rt-body-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".22"/>
            <stop offset=".5"  stop-color="#fff" stop-opacity=".12"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".24"/>
        </linearGradient>
        <linearGradient id="rt-yoke-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".16"/>
            <stop offset=".5"  stop-color="#fff" stop-opacity=".14"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".18"/>
        </linearGradient>
        <linearGradient id="rt-sleeve-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".20"/>
            <stop offset=".5"  stop-color="#fff" stop-opacity=".10"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".22"/>
        </linearGradient>
        <filter id="rt-shadow" x="-12%" y="-8%" width="124%" height="126%">
            <feDropShadow dx="0" dy="7" stdDeviation="7" flood-color="#000" flood-opacity=".20"/>
        </filter>
    </defs>

    <g filter="url(#rt-shadow)">
        {{-- Left wide sleeve --}}
        <path d="M 72 30 C 36 44,8 70,4 100 L 28 168 C 40 148,52 134,66 118 Z"
              fill="#1d2b53" stroke="#141d38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 72 30 C 36 44,8 70,4 100 L 28 168 C 40 148,52 134,66 118 Z"
              fill="url(#rt-sleeve-shade)"/>

        {{-- Right wide sleeve --}}
        <path d="M 128 30 C 164 44,192 70,196 100 L 172 168 C 160 148,148 134,134 118 Z"
              fill="#1d2b53" stroke="#141d38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 128 30 C 164 44,192 70,196 100 L 172 168 C 160 148,148 134,134 118 Z"
              fill="url(#rt-sleeve-shade)"/>

        {{-- Main body (left half) --}}
        <path d="M 100 10 C 76 10,63 16,72 30 C 66 60,65 90,66 118
                 C 64 158,62 198,62 240 C 62 252,65 256,72 257
                 C 82 258,91 257,100 257 L 100 10 Z"
              fill="#1d2b53" stroke="#141d38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 100 10 C 76 10,63 16,72 30 C 66 60,65 90,66 118
                 C 64 158,62 198,62 240 C 62 252,65 256,72 257
                 C 82 258,91 257,100 257 L 100 10 Z"
              fill="url(#rt-body-shade)"/>

        {{-- Main body (right half) --}}
        <path d="M 100 10 C 124 10,137 16,128 30 C 134 60,135 90,134 118
                 C 136 158,138 198,138 240 C 138 252,135 256,128 257
                 C 118 258,109 257,100 257 L 100 10 Z"
              fill="#1d2b53" stroke="#141d38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 100 10 C 124 10,137 16,128 30 C 134 60,135 90,134 118
                 C 136 158,138 198,138 240 C 138 252,135 256,128 257
                 C 118 258,109 257,100 257 L 100 10 Z"
              fill="url(#rt-body-shade)"/>

        {{-- Yoke band 1 (outermost, widest — from shoulder seam) --}}
        <path d="M 72 30 C 82 72,96 88,100 88 L 100 68 C 97 68,86 52,80 22 Z"
              fill="#c8102e" stroke="#9e0a22" stroke-width=".6"/>
        <path d="M 128 30 C 118 72,104 88,100 88 L 100 68 C 103 68,114 52,120 22 Z"
              fill="#c8102e" stroke="#9e0a22" stroke-width=".6"/>
        <path d="M 72 30 C 82 72,96 88,100 88 L 100 68 C 97 68,86 52,80 22 Z"
              fill="url(#rt-yoke-shade)"/>
        <path d="M 128 30 C 118 72,104 88,100 88 L 100 68 C 103 68,114 52,120 22 Z"
              fill="url(#rt-yoke-shade)"/>

        {{-- Yoke band 2 (middle) --}}
        <path d="M 80 22 C 88 56,96 70,100 70 L 100 50 C 98 50,90 38,86 10 Z"
              fill="#c9a227" stroke="#a07a10" stroke-width=".6"/>
        <path d="M 120 22 C 112 56,104 70,100 70 L 100 50 C 102 50,110 38,114 10 Z"
              fill="#c9a227" stroke="#a07a10" stroke-width=".6"/>
        <path d="M 80 22 C 88 56,96 70,100 70 L 100 50 C 98 50,90 38,86 10 Z"
              fill="url(#rt-yoke-shade)"/>
        <path d="M 120 22 C 112 56,104 70,100 70 L 100 50 C 102 50,110 38,114 10 Z"
              fill="url(#rt-yoke-shade)"/>

        {{-- Yoke band 3 (innermost, narrowest) --}}
        <path d="M 86 10 C 90 35,96 48,100 50 L 100 10 C 92 10,88 10,86 10 Z"
              fill="#fff" stroke="#e0e0e0" stroke-width=".6"/>
        <path d="M 114 10 C 110 35,104 48,100 50 L 100 10 C 108 10,112 10,114 10 Z"
              fill="#fff" stroke="#e0e0e0" stroke-width=".6"/>
        <path d="M 86 10 C 90 35,96 48,100 50 L 100 10 C 92 10,88 10,86 10 Z"
              fill="url(#rt-yoke-shade)"/>
        <path d="M 114 10 C 110 35,104 48,100 50 L 100 10 C 108 10,112 10,114 10 Z"
              fill="url(#rt-yoke-shade)"/>

        {{-- Outline strokes on top of yoke bands --}}
        <path d="M 72 30 C 82 72,96 88,100 88 M 128 30 C 118 72,104 88,100 88"
              fill="none" stroke="#141d38" stroke-width=".6"/>
        <path d="M 80 22 C 88 56,96 70,100 70 M 120 22 C 112 56,104 70,100 70"
              fill="none" stroke="#141d38" stroke-width=".6"/>
        <path d="M 86 10 C 90 35,96 48,100 50 M 114 10 C 110 35,104 48,100 50"
              fill="none" stroke="#141d38" stroke-width=".6"/>

        {{-- Center front opening --}}
        <line x1="100" y1="88" x2="100" y2="257"
              stroke="#141d38" stroke-width="1.4"/>

        {{-- Hem fold lines --}}
        <path d="M 79 257 C 79 210,82 178,86 138"
              fill="none" stroke="rgba(255,255,255,.12)" stroke-width=".8"/>
        <path d="M 107 257 C 103 214,103 182,105 142"
              fill="none" stroke="rgba(255,255,255,.10)" stroke-width=".8"/>
        <path d="M 121 257 C 121 210,118 178,114 138"
              fill="none" stroke="rgba(255,255,255,.12)" stroke-width=".8"/>

        {{-- Back zone hint on front body --}}
        <rect x="75" y="108" width="50" height="64" rx="4"
              fill="none" stroke="rgba(255,255,255,.14)" stroke-width=".9" stroke-dasharray="3 2"/>
        <text x="100" y="143" text-anchor="middle" dominant-baseline="central"
              font-size="9" font-weight="700" fill="rgba(255,255,255,.20)"
              font-family="Georgia, serif">التصميم هنا</text>
    </g>
</svg>