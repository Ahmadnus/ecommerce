{{--
  customize/index-svgs/tshirt-thumb.blade.php
  Compact SVG thumbnail of the Studio T-Shirt for the product picker card.
  Matches the reference HTML proportions and style.
--}}
<svg viewBox="0 0 200 230" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
    <defs>
        <linearGradient id="tt-fabricGrad" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%"   stop-color="#000" stop-opacity="0.12"/>
            <stop offset="15%"  stop-color="#fff" stop-opacity="0.06"/>
            <stop offset="50%"  stop-color="#fff" stop-opacity="0.18"/>
            <stop offset="85%"  stop-color="#fff" stop-opacity="0.00"/>
            <stop offset="100%" stop-color="#000" stop-opacity="0.16"/>
        </linearGradient>
        <linearGradient id="tt-collarGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"   stop-color="#000" stop-opacity="0.18"/>
            <stop offset="100%" stop-color="#fff" stop-opacity="0.05"/>
        </linearGradient>
        <linearGradient id="tt-neckGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"   stop-color="#000" stop-opacity="0.28"/>
            <stop offset="100%" stop-color="#000" stop-opacity="0.08"/>
        </linearGradient>
        <filter id="tt-shadow" x="-12%" y="-8%" width="124%" height="126%">
            <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="#000" flood-opacity="0.18"/>
        </filter>
        <filter id="tt-sleeveShadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="3" stdDeviation="3" flood-color="#000" flood-opacity="0.10"/>
        </filter>
    </defs>

    <g filter="url(#tt-shadow)">

        {{-- Left sleeve --}}
        <g filter="url(#tt-sleeveShadow)">
            <path d="M 42 42 C 32 50, 22 60, 12 72 C 15 85, 20 105, 25 117 C 35 112, 45 107, 52 105 C 50 87, 47 67, 42 42 Z"
                  fill="#f3f4f6" stroke="rgba(0,0,0,0.28)" stroke-width="1.2"
                  stroke-linejoin="round"/>
            <path d="M 42 42 C 32 50, 22 60, 12 72 C 15 85, 20 105, 25 117 C 35 112, 45 107, 52 105 C 50 87, 47 67, 42 42 Z"
                  fill="url(#tt-fabricGrad)" style="pointer-events:none;"/>
            <path d="M 16 82 C 19 92, 24 107, 27 114"
                  fill="none" stroke="#9ca3af" stroke-width="1"
                  stroke-dasharray="3 3" stroke-linecap="round" opacity="0.8"/>
        </g>

        {{-- Right sleeve --}}
        <g filter="url(#tt-sleeveShadow)">
            <path d="M 158 42 C 168 50, 178 60, 188 72 C 185 85, 180 105, 175 117 C 165 112, 155 107, 148 105 C 150 87, 153 67, 158 42 Z"
                  fill="#f3f4f6" stroke="rgba(0,0,0,0.28)" stroke-width="1.2"
                  stroke-linejoin="round"/>
            <path d="M 158 42 C 168 50, 178 60, 188 72 C 185 85, 180 105, 175 117 C 165 112, 155 107, 148 105 C 150 87, 153 67, 158 42 Z"
                  fill="url(#tt-fabricGrad)" style="pointer-events:none;"/>
            <path d="M 184 82 C 181 92, 176 107, 173 114"
                  fill="none" stroke="#9ca3af" stroke-width="1"
                  stroke-dasharray="3 3" stroke-linecap="round" opacity="0.8"/>
        </g>

        {{-- Inner neck shadow --}}
        <path d="M 74 30 C 87 37, 113 37, 126 30 C 113 45, 87 45, 74 30 Z"
              fill="#f3f4f6" stroke="rgba(0,0,0,0.28)" stroke-width="1.2"
              stroke-linejoin="round"/>
        <path d="M 74 30 C 87 37, 113 37, 126 30 C 113 45, 87 45, 74 30 Z"
              fill="url(#tt-neckGrad)" style="pointer-events:none;"/>

        {{-- Main body --}}
        <path id="tt-body"
              d="M 74 30 C 87 45, 113 45, 126 30
                 C 138 32, 148 37, 158 42
                 C 153 67, 150 87, 148 105
                 C 145 145, 142 190, 140 230
                 C 115 235, 85 235, 60 230
                 C 58 190, 55 145, 52 105
                 C 50 87, 47 67, 42 42
                 C 52 37, 62 32, 74 30 Z"
              fill="#f3f4f6" stroke="rgba(0,0,0,0.28)" stroke-width="1.2"
              stroke-linejoin="round"/>
        <path d="M 74 30 C 87 45, 113 45, 126 30
                 C 138 32, 148 37, 158 42
                 C 153 67, 150 87, 148 105
                 C 145 145, 142 190, 140 230
                 C 115 235, 85 235, 60 230
                 C 58 190, 55 145, 52 105
                 C 50 87, 47 67, 42 42
                 C 52 37, 62 32, 74 30 Z"
              fill="url(#tt-fabricGrad)" style="pointer-events:none;"/>

        {{-- Front collar --}}
        <path d="M 74 30 C 87 45, 113 45, 126 30 L 128 36 C 113 52, 87 52, 72 36 Z"
              fill="#e5e7eb" stroke="rgba(0,0,0,0.26)" stroke-width="1.1"
              stroke-linejoin="round"/>
        <path d="M 74 30 C 87 45, 113 45, 126 30 L 128 36 C 113 52, 87 52, 72 36 Z"
              fill="url(#tt-collarGrad)" style="pointer-events:none;"/>

        {{-- Collar inner stitch --}}
        <path d="M 74 34 C 87 50, 113 50, 126 34"
              fill="none" stroke="#9ca3af" stroke-width="0.9"
              stroke-dasharray="3 3" stroke-linecap="round" opacity="0.8"/>

        {{-- Shoulder seams --}}
        <path d="M 42 42 C 47 67, 50 87, 52 105"
              fill="none" stroke="rgba(0,0,0,0.10)" stroke-width="1.5"/>
        <path d="M 158 42 C 153 67, 150 87, 148 105"
              fill="none" stroke="rgba(0,0,0,0.10)" stroke-width="1.5"/>

        {{-- Chest zone hint --}}
        <rect x="60" y="58" width="32" height="32" rx="3"
              fill="none" stroke="rgba(59,130,246,0.22)" stroke-width="0.8" stroke-dasharray="3 2"/>
        <text x="76" y="76" text-anchor="middle" dominant-baseline="central"
              font-size="8" font-weight="700" fill="rgba(59,130,246,0.30)"
              font-family="system-ui,sans-serif">A</text>

        {{-- Center front panel hint --}}
        <rect x="70" y="110" width="60" height="60" rx="3"
              fill="none" stroke="rgba(59,130,246,0.18)" stroke-width="0.8" stroke-dasharray="3 2"/>
        <text x="100" y="143" text-anchor="middle" dominant-baseline="central"
              font-size="8" fill="rgba(59,130,246,0.25)"
              font-family="system-ui,sans-serif">C</text>

        {{-- Hem stitching --}}
        <path d="M 61 224 C 85 229, 115 229, 139 224"
              fill="none" stroke="#9ca3af" stroke-width="0.9"
              stroke-dasharray="3 3" stroke-linecap="round" opacity="0.7"/>

    </g>
</svg>