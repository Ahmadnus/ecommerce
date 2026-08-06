{{--
  customize/index-svgs/hoodie-thumb.blade.php
  Compact SVG thumbnail of the Studio Hoodie for the product picker card.
--}}
<svg viewBox="0 0 200 240" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
    <defs>
        <linearGradient id="ht-body-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".28"/>
            <stop offset=".5"  stop-color="#fff" stop-opacity=".09"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".30"/>
        </linearGradient>
        <linearGradient id="ht-sleeve-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".22"/>
            <stop offset=".4"  stop-color="#fff" stop-opacity=".14"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".24"/>
        </linearGradient>
        <linearGradient id="ht-hood-drop" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
            <stop offset="0" stop-color="#000" stop-opacity=".30"/>
            <stop offset="1" stop-color="#000" stop-opacity="0"/>
        </linearGradient>
        <pattern id="ht-fleece" width="3" height="6" patternUnits="userSpaceOnUse">
            <rect width="3" height="6" fill="transparent"/>
            <line x1=".7" y1="0" x2=".7" y2="6" stroke="rgba(0,0,0,.15)" stroke-width=".8"/>
        </pattern>
        <filter id="ht-shadow" x="-15%" y="-10%" width="130%" height="135%">
            <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="#000" flood-opacity=".20"/>
        </filter>
    </defs>

    <g filter="url(#ht-shadow)">
        {{-- Left sleeve --}}
        <path d="M 58 70 C 40 82,22 114,18 148 C 15 166,18 182,24 194 L 50 194
                 C 47 174,48 140,56 106 Z"
              fill="#2b3a4a" stroke="#1e2b38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 58 70 C 40 82,22 114,18 148 C 15 166,18 182,24 194 L 50 194
                 C 47 174,48 140,56 106 Z"
              fill="url(#ht-sleeve-shade)"/>
        <path d="M 58 70 C 40 82,22 114,18 148 C 15 166,18 182,24 194 L 50 194
                 C 47 174,48 140,56 106 Z"
              fill="url(#ht-fleece)" opacity=".4"/>

        {{-- Right sleeve --}}
        <path d="M 142 70 C 160 82,178 114,182 148 C 185 166,182 182,176 194 L 150 194
                 C 153 174,152 140,144 106 Z"
              fill="#2b3a4a" stroke="#1e2b38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 142 70 C 160 82,178 114,182 148 C 185 166,182 182,176 194 L 150 194
                 C 153 174,152 140,144 106 Z"
              fill="url(#ht-sleeve-shade)"/>
        <path d="M 142 70 C 160 82,178 114,182 148 C 185 166,182 182,176 194 L 150 194
                 C 153 174,152 140,144 106 Z"
              fill="url(#ht-fleece)" opacity=".4"/>

        {{-- Body --}}
        <path d="M 60 67 C 74 63,126 63,140 67 C 150 71,153 82,153 98
                 C 153 128,150 163,149 194 L 51 194
                 C 50 163,47 128,47 98 C 47 82,50 71,60 67 Z"
              fill="#2b3a4a" stroke="#1e2b38" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 60 67 C 74 63,126 63,140 67 C 150 71,153 82,153 98
                 C 153 128,150 163,149 194 L 51 194
                 C 50 163,47 128,47 98 C 47 82,50 71,60 67 Z"
              fill="url(#ht-body-shade)"/>
        <path d="M 60 67 C 74 63,126 63,140 67 C 150 71,153 82,153 98
                 C 153 128,150 163,149 194 L 51 194
                 C 50 163,47 128,47 98 C 47 82,50 71,60 67 Z"
              fill="url(#ht-fleece)" opacity=".35"/>

        {{-- Kangaroo pocket --}}
        <path d="M 74 144 L 126 144 C 129 144,131 146,132 148 L 142 168 C 143 171,142 175,139 176
                 L 139 194 L 61 194 L 61 176 C 58 175,57 171,58 168 L 68 148 C 69 146,71 144,74 144 Z"
              fill="#233038" stroke="#1a252c" stroke-width=".7" stroke-linejoin="round"/>
        <path d="M 74 144 L 126 144 C 129 144,131 146,132 148 L 142 168 C 143 171,142 175,139 176
                 L 139 194 L 61 194 L 61 176 C 58 175,57 171,58 168 L 68 148 C 69 146,71 144,74 144 Z"
              fill="url(#ht-body-shade)" opacity=".7"/>

        {{-- Hood outer --}}
        <path d="M 62 68 C 52 32,68 10,100 10 C 132 10,148 32,138 68
                 C 128 75,114 78,100 78 C 86 78,72 75,62 68 Z"
              fill="#2b3a4a" stroke="#1e2b38" stroke-width=".9"/>
        <path d="M 62 68 C 52 32,68 10,100 10 C 132 10,148 32,138 68
                 C 128 75,114 78,100 78 C 86 78,72 75,62 68 Z"
              fill="url(#ht-body-shade)"/>
        <path d="M 62 68 C 52 32,68 10,100 10 C 132 10,148 32,138 68
                 C 128 75,114 78,100 78 C 86 78,72 75,62 68 Z"
              fill="url(#ht-fleece)" opacity=".35"/>

        {{-- Hood drop shadow onto body --}}
        <path d="M 62 68 C 72 84,128 84,138 68 C 128 90,72 90,62 68 Z"
              fill="url(#ht-hood-drop)" opacity=".5"/>

        {{-- Hood inner opening --}}
        <path d="M 80 62 C 74 42,84 25,100 25 C 116 25,126 42,120 62
                 C 112 70,106 73,100 73 C 94 73,88 70,80 62 Z"
              fill="#1e2b38" stroke="#162029" stroke-width=".7"/>
        <path d="M 80 62 C 74 42,84 25,100 25 C 116 25,126 42,120 62 Z"
              fill="#000" opacity=".20"/>

        {{-- Drawstring eyelets --}}
        <circle cx="88" cy="68" r="2.5" fill="#0d1a22" stroke="#2b3a4a" stroke-width=".8"/>
        <circle cx="88" cy="68" r="1.2" fill="#3d5060"/>
        <circle cx="112" cy="68" r="2.5" fill="#0d1a22" stroke="#2b3a4a" stroke-width=".8"/>
        <circle cx="112" cy="68" r="1.2" fill="#3d5060"/>

        {{-- Drawstrings --}}
        <path d="M 88 70 C 87 84,84 98,86 120" fill="none" stroke="#4a6070" stroke-width="1.8" stroke-linecap="round"/>
        <path d="M 112 70 C 113 84,116 98,114 120" fill="none" stroke="#4a6070" stroke-width="1.8" stroke-linecap="round"/>

        {{-- Left cuff --}}
        <path d="M 22 194 L 50 194 L 49 210 L 23 210 Z"
              fill="#1a2530" stroke="#111c24" stroke-width=".7"/>
        <line x1="23" y1="196" x2="49" y2="196" stroke="rgba(255,255,255,.08)" stroke-width=".8" stroke-dasharray="2 1.5"/>

        {{-- Right cuff --}}
        <path d="M 178 194 L 150 194 L 151 210 L 177 210 Z"
              fill="#1a2530" stroke="#111c24" stroke-width=".7"/>
        <line x1="151" y1="196" x2="177" y2="196" stroke="rgba(255,255,255,.08)" stroke-width=".8" stroke-dasharray="2 1.5"/>

        {{-- Waistband --}}
        <path d="M 51 194 L 149 194 C 149 204,147 214,144 218 L 56 218 C 53 214,51 204,51 194 Z"
              fill="#1a2530" stroke="#111c24" stroke-width=".8"/>
        <line x1="52" y1="197" x2="148" y2="197" stroke="rgba(255,255,255,.08)" stroke-width=".8" stroke-dasharray="2 1.5"/>

        {{-- Chest zone hint --}}
        <rect x="62" y="82" width="30" height="30" rx="3"
              fill="none" stroke="rgba(255,255,255,.15)" stroke-width="1" stroke-dasharray="3 2"/>
        <text x="77" y="99" text-anchor="middle" dominant-baseline="central"
              font-size="10" font-weight="800" fill="rgba(255,255,255,.22)"
              font-family="Georgia, serif">H</text>
    </g>
</svg>