{{--
  customize/index-svgs/jacket-thumb.blade.php
  Compact SVG thumbnail of the Varsity Jacket for the product picker card.
  No variables needed — purely decorative, uses fixed colours.
--}}
<svg viewBox="0 0 200 240" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
    <defs>
        <linearGradient id="jt-body-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".28"/>
            <stop offset=".45" stop-color="#fff" stop-opacity=".08"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".30"/>
        </linearGradient>
        <linearGradient id="jt-sleeve-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".20"/>
            <stop offset=".4"  stop-color="#fff" stop-opacity=".18"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".22"/>
        </linearGradient>
        <linearGradient id="jt-rib-shade" x1="0" y1="0" x2="1" y2="0" gradientUnits="objectBoundingBox">
            <stop offset="0"   stop-color="#000" stop-opacity=".32"/>
            <stop offset=".5"  stop-color="#fff" stop-opacity=".06"/>
            <stop offset="1"   stop-color="#000" stop-opacity=".34"/>
        </linearGradient>
        <pattern id="jt-knit" width="4" height="8" patternUnits="userSpaceOnUse">
            <rect width="4" height="8" fill="transparent"/>
            <line x1=".8" y1="0" x2=".8" y2="8"   stroke="rgba(0,0,0,.22)" stroke-width="1"/>
            <line x1="2.4" y1="0" x2="2.4" y2="8" stroke="rgba(255,255,255,.10)" stroke-width=".8"/>
        </pattern>
        <filter id="jt-shadow" x="-15%" y="-10%" width="130%" height="130%">
            <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="#000" flood-opacity=".22"/>
        </filter>
    </defs>

    <g filter="url(#jt-shadow)">
        {{-- Left sleeve --}}
        <path d="M 56 62 C 38 74,22 112,18 148 C 16 164,19 180,24 194 L 54 194
                 C 56 174,58 122,64 84 C 66 74,62 66,58 63 Z"
              fill="#1d2b53" stroke="#1a1a2e" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 56 62 C 38 74,22 112,18 148 C 16 164,19 180,24 194 L 54 194
                 C 56 174,58 122,64 84 C 66 74,62 66,58 63 Z"
              fill="url(#jt-sleeve-shade)"/>

        {{-- Right sleeve --}}
        <path d="M 144 62 C 162 74,178 112,182 148 C 184 164,181 180,176 194 L 146 194
                 C 144 174,142 122,136 84 C 134 74,138 66,142 63 Z"
              fill="#1d2b53" stroke="#1a1a2e" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 144 62 C 162 74,178 112,182 148 C 184 164,181 180,176 194 L 146 194
                 C 144 174,142 122,136 84 C 134 74,138 66,142 63 Z"
              fill="url(#jt-sleeve-shade)"/>

        {{-- Body --}}
        <path d="M 64 60 C 74 55,126 55,136 60 C 144 63,148 70,150 76
                 C 153 88,154 125,151 164 C 150 178,149 188,148 196 L 52 196
                 C 51 188,50 178,49 164 C 46 125,47 88,50 76
                 C 52 70,56 63,64 60 Z"
              fill="#141414" stroke="#0a0a0a" stroke-width=".9" stroke-linejoin="round"/>
        <path d="M 64 60 C 74 55,126 55,136 60 C 144 63,148 70,150 76
                 C 153 88,154 125,151 164 C 150 178,149 188,148 196 L 52 196
                 C 51 188,50 178,49 164 C 46 125,47 88,50 76
                 C 52 70,56 63,64 60 Z"
              fill="url(#jt-body-shade)"/>

        {{-- Collar --}}
        <path d="M 72 60 C 76 51,84 48,100 48 C 116 48,124 51,128 60
                 C 124 65,118 69,112 71 C 107 74,103 75,100 75
                 C 97 75,93 74,88 71 C 82 69,76 65,72 60 Z"
              fill="#c8102e" stroke="#a00024" stroke-width=".8"/>
        <path d="M 72 60 C 76 51,84 48,100 48 C 116 48,124 51,128 60
                 C 124 65,118 69,112 71 C 107 74,103 75,100 75
                 C 97 75,93 74,88 71 C 82 69,76 65,72 60 Z"
              fill="url(#jt-knit)" opacity=".5"/>
        <path d="M 72 60 C 76 51,84 48,100 48 C 116 48,124 51,128 60
                 C 124 65,118 69,112 71 C 107 74,103 75,100 75
                 C 97 75,93 74,88 71 C 82 69,76 65,72 60 Z"
              fill="url(#jt-rib-shade)"/>

        {{-- Left cuff --}}
        <path d="M 22 194 L 54 194 L 53 210 L 23 210 Z"
              fill="#c8102e" stroke="#a00024" stroke-width=".7"/>
        <path d="M 22 194 L 54 194 L 53 210 L 23 210 Z"
              fill="url(#jt-knit)" opacity=".45"/>
        <line x1="23" y1="197" x2="53" y2="197" stroke="#f3f3f1" stroke-width="1.2"/>
        <line x1="23" y1="202" x2="53" y2="202" stroke="#f3f3f1" stroke-width="1.2"/>
        <line x1="23" y1="207" x2="53" y2="207" stroke="#f3f3f1" stroke-width="1.2"/>

        {{-- Right cuff --}}
        <path d="M 178 194 L 146 194 L 147 210 L 177 210 Z"
              fill="#c8102e" stroke="#a00024" stroke-width=".7"/>
        <path d="M 178 194 L 146 194 L 147 210 L 177 210 Z"
              fill="url(#jt-knit)" opacity=".45"/>
        <line x1="147" y1="197" x2="177" y2="197" stroke="#f3f3f1" stroke-width="1.2"/>
        <line x1="147" y1="202" x2="177" y2="202" stroke="#f3f3f1" stroke-width="1.2"/>
        <line x1="147" y1="207" x2="177" y2="207" stroke="#f3f3f1" stroke-width="1.2"/>

        {{-- Waistband --}}
        <path d="M 51 196 L 149 196 L 147 215 L 53 215 Z"
              fill="#c8102e" stroke="#a00024" stroke-width=".8"/>
        <path d="M 51 196 L 149 196 L 147 215 L 53 215 Z"
              fill="url(#jt-knit)" opacity=".45"/>
        <line x1="53" y1="199" x2="147" y2="199" stroke="#f3f3f1" stroke-width="1.4"/>
        <line x1="54" y1="205" x2="146" y2="205" stroke="#f3f3f1" stroke-width="1.4"/>
        <line x1="55" y1="211" x2="145" y2="211" stroke="#f3f3f1" stroke-width="1.4"/>

        {{-- Chest letter / badge area hint --}}
        <rect x="72" y="82" width="26" height="26" rx="3"
              fill="none" stroke="rgba(255,255,255,.20)" stroke-width="1" stroke-dasharray="3 2"/>
        <text x="85" y="98" text-anchor="middle" dominant-baseline="central"
              font-size="11" font-weight="800" fill="rgba(255,255,255,.30)"
              font-family="Georgia, serif">V</text>

        {{-- Center placket --}}
        <line x1="100" y1="75" x2="100" y2="196"
              stroke="rgba(255,255,255,.07)" stroke-width="1" stroke-dasharray="3 2"/>
    </g>
</svg>