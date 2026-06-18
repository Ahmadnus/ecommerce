{{--
╔══════════════════════════════════════════════════════════════════════════════╗
║  customize/show.blade.php  —  Live Preview Engine                          ║
║                                                                            ║
║  Architecture overview:                                                    ║
║                                                                            ║
║  1. Alpine.js x-data holds `designState` — the single source of truth.    ║
║     ┌─────────────────────────────────────────────────────────────┐        ║
║     │ designState = {                                             │        ║
║     │   colors: { body, sleeve, rib, stripe },                   │        ║
║     │   zones: {                                                  │        ║
║     │     A: { active, text, imageDataUrl, imageFile, type },    │        ║
║     │     G: { active, text, imageDataUrl, imageFile, type }, …  │        ║
║     │   },                                                        │        ║
║     │   activeZoneKey: null,                                      │        ║
║     │   view: 'front'                                             │        ║
║     │ }                                                           │        ║
║     └─────────────────────────────────────────────────────────────┘        ║
║                                                                            ║
║  2. The SVG garment partial (varsity_jacket.blade.php) exposes:            ║
║     - Named `<clipPath>` regions per zone (for image clipping)             ║
║     - Empty `<image>` tags per zone  (id="svg-img-{KEY}")                  ║
║     - Empty `<text>` tags per zone   (id="svg-txt-{KEY}")                  ║
║     - Zone hit-areas that dispatch zone:open events                        ║
║                                                                            ║
║  3. Alpine watchers observe designState and call renderZone(key)           ║
║     which directly mutates those SVG elements via getElementById.          ║
║                                                                            ║
║  4. On submit, buildFormData() serializes designState into a               ║
║     FormData object (flat hidden fields + real File objects for images),   ║
║     then POSTs via fetch — no page reload required.                        ║
║                                                                            ║
║  Variables injected by CustomizationController@show:                      ║
║    $product  — Product model                                               ║
║    $config   — ProductCustomization value object                           ║
║    $zones    — array of zone defs  [['key','label','type'], …]             ║
║    $colors   — ['body'=>['#hex',…], 'sleeve'=>[…], …]                     ║
║    $defaults — ['body'=>'#hex', …]                                         ║
╚══════════════════════════════════════════════════════════════════════════════╝
--}}
@extends('layouts.app')

@section('title', 'تخصيص: ' . $product->name)

@push('head')
{{--
  Alpine.js is already loaded in your admin layout (layouts.app).
  If it isn't in your storefront layout, uncomment this line:
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
--}}
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">

<style>
/* ─── Design tokens ──────────────────────────────────────────────────────── */
:root {
    --ink:      #0d0d0d;
    --surface:  #f7f6f3;
    --panel:    #ffffff;
    --border:   #e8e6e1;
    --accent:   #3b5bdb;
    --accent-s: rgba(59,91,219,.12);
    --muted:    #8a8680;
    --radius:   16px;
    --radius-sm:10px;
    --font-display: 'Syne', sans-serif;
    --font-body:    'DM Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--font-body); }

/* ─── Layout ─────────────────────────────────────────────────────────────── */
.customizer {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 28px;
    align-items: start;
}

/* ── Tablet: single column, stage stays sticky ──────────────────────────── */
@media (max-width: 1100px) {
    .customizer {
        grid-template-columns: 1fr;
    }
}

/* ── Mobile: full-page scroll layout ───────────────────────────────────────
   The page body scrolls naturally. The SVG preview sits at the top as a
   compact fixed-height card. The controls panel flows below it and scrolls
   with the page. No overflow clipping anywhere.
────────────────────────────────────────────────────────────────────────── */
@media (max-width: 700px) {

    /* Remove horizontal padding so cards go edge-to-edge */
    .max-w-7xl { padding-left: 12px !important; padding-right: 12px !important; }

    /* Stack: SVG first, controls below — both scroll with the page */
    .customizer {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* SVG preview card: compact height, no sticky, centered SVG */
    .garment-stage {
        position: static !important;    /* cancel sticky on mobile */
        padding: 16px 12px 12px;
        border-radius: 16px;
        max-height: none;
        overflow: visible;
    }

    /* Make the SVG itself smaller so it fits in viewport */
    #garment-wrapper {
        max-width: 240px !important;
        margin: 0 auto;
    }

    /* Controls panel: normal block flow, scrolls with the page */
    .controls-panel {
        gap: 10px;
    }

    /* Cards slightly tighter on mobile */
    .card {
        padding: 16px;
        border-radius: 14px;
    }

    /* Zone buttons wrap cleanly */
    .zone-btn {
        font-size: 11px;
        padding: 7px 10px;
    }

    /* Submit button: full width, easier tap target */
    .submit-btn {
        width: 100%;
        text-align: center;
        bottom: 16px;
        left: 12px;
        right: 12px;
        transform: none;
        border-radius: 14px;
    }

    /* Breadcrumb: smaller on mobile */
    nav.flex.items-center.gap-2 {
        font-size: 12px;
        margin-bottom: 12px !important;
    }

    /* Sub-controls grid: 2 cols instead of 3 */
    .sub-controls {
        grid-template-columns: 1fr 1fr !important;
    }

    /* Image upload preview: contained */
    .upload-preview {
        max-height: 80px;
    }

    /* Color palette: wrap naturally */
    .palette {
        max-width: none !important;
        flex-wrap: wrap;
    }

    /* Color row: tighter */
    .color-row {
        padding: 12px 14px;
    }

    /* py-10 outer wrapper: reduce vertical breathing room on mobile */
    .max-w-7xl.mx-auto {
        padding-top: 12px !important;
        padding-bottom: 80px !important; /* leave room for fixed submit btn */
    }

}

/* ─── Garment stage — desktop/tablet ────────────────────────────────────── */
.garment-stage {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 28px 24px;
    position: sticky;
    top: 80px;
    align-self: start;
}
.garment-stage__toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.garment-stage__title {
    font-family: var(--font-display);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--muted);
}
.view-toggle {
    display: flex;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 3px;
    gap: 3px;
}
.view-toggle__btn {
    font-family: var(--font-body);
    font-size: 12px;
    font-weight: 500;
    border: none;
    padding: 6px 18px;
    border-radius: 999px;
    cursor: pointer;
    background: transparent;
    color: var(--muted);
    transition: background .15s, color .15s;
}
.view-toggle__btn.is-active {
    background: var(--ink);
    color: #fff;
}

/* Zone overlay state */
[data-zone] { cursor: pointer; }
[data-zone] .zone-outline { transition: stroke-dashoffset .3s, opacity .2s; }
[data-zone] .zone-badge   { opacity: 0; transition: opacity .2s; }
[data-zone]:hover .zone-badge    { opacity: 1; }
[data-zone].zone--active .zone-badge   { opacity: 1; }
[data-zone].zone--active .zone-outline {
    stroke: var(--accent) !important;
    stroke-width: 2 !important;
    filter: drop-shadow(0 0 4px rgba(59,91,219,.4));
}
[data-zone].zone--has-content .zone-outline {
    stroke: #16a34a !important;
    stroke-dasharray: none !important;
    stroke-width: 1.5 !important;
}
[data-zone].zone--has-content .zone-badge { opacity: 1; }

/* SVG content layers */
.svg-zone-image { pointer-events: none; }
.svg-zone-text  {
    font-family: var(--font-display), sans-serif;
    pointer-events: none;
    dominant-baseline: central;
    text-anchor: middle;
    font-weight: 700;
    letter-spacing: .04em;
}

/* ─── Controls panel ──────────────────────────────────────────────────────── */
.controls-panel { display: flex; flex-direction: column; gap: 14px; }

.card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
}
.card__label {
    font-family: var(--font-display);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 14px;
    display: block;
}

/* ─── Color picker ────────────────────────────────────────────────────────── */
.color-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.color-row:last-child { border-bottom: none; }
.color-row__name  { font-size: 13px; font-weight: 500; color: var(--ink); }
.color-row__hex   { font-size: 11px; color: var(--muted); font-variant-numeric: tabular-nums; margin-top: 1px; }
.palette          { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; justify-content: flex-end; max-width: 170px; }
.swatch {
    width: 26px; height: 26px; border-radius: 50%;
    border: 2px solid transparent;
    cursor: pointer; transition: transform .15s, box-shadow .15s;
    position: relative; flex-shrink: 0;
}
.swatch:hover { transform: scale(1.15); }
.swatch.is-selected {
    box-shadow: 0 0 0 2px var(--panel), 0 0 0 4px var(--accent);
}
.swatch--custom {
    background: none !important;
    border: 1.5px dashed var(--border) !important;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted); border-radius: 50%;
    overflow: hidden;
}

/* ─── Zone tabs ───────────────────────────────────────────────────────────── */
.zone-tabs {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.zone-tab {
    font-family: var(--font-body);
    font-size: 12px;
    font-weight: 500;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--muted);
    padding: 5px 12px;
    border-radius: 999px;
    cursor: pointer;
    transition: all .15s;
    position: relative;
}
.zone-tab:hover { border-color: #aaa; color: var(--ink); }
.zone-tab.is-active {
    background: var(--ink); color: #fff;
    border-color: var(--ink);
}
.zone-tab.has-content::after {
    content: '';
    position: absolute; top: 2px; right: 2px;
    width: 6px; height: 6px;
    background: #16a34a;
    border-radius: 50%;
    border: 1px solid #fff;
}

/* ─── Zone editor ─────────────────────────────────────────────────────────── */
.zone-editor { display: none; }
.zone-editor.is-visible { display: block; }

.zone-empty-state {
    text-align: center;
    padding: 32px 16px;
    color: var(--muted);
}
.zone-empty-state svg { opacity: .25; margin: 0 auto 10px; display: block; }

.field-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 6px;
    display: block;
}
.text-input {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 10px 14px;
    font-family: var(--font-body);
    font-size: 14px;
    color: var(--ink);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    background: var(--surface);
}
.text-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-s);
    background: var(--panel);
}

/* ─── Font / size controls ────────────────────────────────────────────────── */
.sub-controls {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-top: 10px;
}
.mini-select {
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 7px 8px;
    font-family: var(--font-body);
    font-size: 12px;
    color: var(--ink);
    background: var(--surface);
    outline: none;
    cursor: pointer;
    width: 100%;
}
.mini-select:focus { border-color: var(--accent); }

/* ─── Image upload ────────────────────────────────────────────────────────── */
.upload-zone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-sm);
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
    overflow: hidden;
}
.upload-zone:hover, .upload-zone.is-dragging {
    border-color: var(--accent);
    background: var(--accent-s);
}
.upload-zone__preview {
    width: 100%; max-height: 120px; object-fit: contain;
    border-radius: 8px; display: block; margin: 0 auto 8px;
}
.upload-zone__clear {
    font-size: 11px; color: #dc2626; cursor: pointer;
    text-decoration: underline; display: block; margin-top: 4px;
}

/* ─── Submit button ───────────────────────────────────────────────────────── */
.btn-submit {
    width: 100%;
    background: var(--ink);
    color: #fff;
    font-family: var(--font-display);
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .04em;
    padding: 16px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    transition: background .15s, transform .1s;
    position: relative;
    overflow: hidden;
}
.btn-submit:hover { background: #222; }
.btn-submit:active { transform: scale(.99); }
.btn-submit:disabled { background: #aaa; cursor: not-allowed; }

/* ─── Toast ───────────────────────────────────────────────────────────────── */
.toast {
    position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%);
    max-width: calc(100vw - 24px);  /* don't overflow on tiny screens */
    background: var(--ink); color: #fff;
    font-size: 13px; font-weight: 500;
    padding: 12px 24px; border-radius: 999px;
    box-shadow: 0 8px 32px rgba(0,0,0,.25);
    z-index: 9999;
    opacity: 0; pointer-events: none;
    transition: opacity .3s;
}
.toast.is-visible { opacity: 1; pointer-events: auto; }
.toast.is-error   { background: #dc2626; }

/* ─── Progress indicator ──────────────────────────────────────────────────── */
.progress-bar {
    position: fixed; top: 0; left: 0; height: 3px;
    background: var(--accent); width: 0%;
    z-index: 9999; transition: width .4s;
}
</style>
@endpush

@section('content')
{{--
  ══════════════════════════════════════════════════════════════════════════════
  PHP: Build the initial Alpine state from Blade variables.
  This runs once on page load; Alpine owns all state after that.
  ══════════════════════════════════════════════════════════════════════════════
--}}
@php
    /*
     * Zone metadata passed to Alpine.
     * We key by zone key so Alpine can do O(1) lookups.
     */
    $zonesMeta = [];
    foreach ($zones as $z) {
        $zonesMeta[$z['key']] = [
            'key'   => $z['key'],
            'label' => $z['label'],
            'type'  => $z['type'], // 'text' | 'image' | 'both'
        ];
    }

    /*
     * SVG coordinate map — tells the JS renderer where to place content
     * inside each named zone.
     *
     * cx, cy  = center of the zone rect  (text anchor point)
     * imgX,Y  = top-left of clip region  (image placement)
     * imgW,H  = clip region dimensions
     * rotate  = transform for sleeved zones
     * svgs    = which SVG views this zone appears in ('front','back','both')
     *
     * Extend this map when you add new zones or garments.
     */
    /*
     * COMBINED coordinate map for all garment types.
     * Keys must be unique across garments — hoodie uses D1/D2/D3/E1/E2/E3/F/G,
     * varsity jacket uses A/B/C/D/E1/E2/E3/F1/F2/F3/G/H.
     * Shared keys (A,B,G,E1,E2,E3) have the same coords so they work for both.
     */
    $zoneCoords = [
        // ── Shared / Varsity Jacket ──────────────────────────────────────────
        'A'  => ['cx'=>146,'cy'=>192,'imgX'=>112,'imgY'=>158,'imgW'=>68, 'imgH'=>68, 'rotate'=>null,                  'svgs'=>['front']],
        'B'  => ['cx'=>254,'cy'=>192,'imgX'=>220,'imgY'=>158,'imgW'=>68, 'imgH'=>68, 'rotate'=>null,                  'svgs'=>['front']],
        // Varsity jacket pocket zones
        'C'  => ['cx'=>147,'cy'=>332,'imgX'=>128,'imgY'=>296,'imgW'=>38, 'imgH'=>72, 'rotate'=>null,                  'svgs'=>['front']],
        'D'  => ['cx'=>253,'cy'=>332,'imgX'=>234,'imgY'=>296,'imgW'=>38, 'imgH'=>72, 'rotate'=>null,                  'svgs'=>['front']],
        // Varsity jacket sleeve zones (left E, right F)
        'E1' => ['cx'=>74, 'cy'=>185,'imgX'=>52, 'imgY'=>163,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-18 74 185)', 'svgs'=>['front']],
        'E2' => ['cx'=>70, 'cy'=>268,'imgX'=>48, 'imgY'=>246,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-14 70 268)', 'svgs'=>['front']],
        'E3' => ['cx'=>74, 'cy'=>348,'imgX'=>54, 'imgY'=>328,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(-8 74 348)',  'svgs'=>['front']],
        'F1' => ['cx'=>326,'cy'=>185,'imgX'=>304,'imgY'=>163,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(18 326 185)', 'svgs'=>['front']],
        'F2' => ['cx'=>330,'cy'=>268,'imgX'=>308,'imgY'=>246,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(14 330 268)', 'svgs'=>['front']],
        'F3' => ['cx'=>326,'cy'=>348,'imgX'=>306,'imgY'=>328,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(8 326 348)',  'svgs'=>['front']],
        // Varsity jacket back zones
        'G'  => ['cx'=>200,'cy'=>244,'imgX'=>122,'imgY'=>160,'imgW'=>156,'imgH'=>168,'rotate'=>null,                  'svgs'=>['back']],
        'H'  => ['cx'=>200,'cy'=>177,'imgX'=>146,'imgY'=>152,'imgW'=>108,'imgH'=>50, 'rotate'=>null,                  'svgs'=>['back']],

        // ── Hoodie-specific zones ────────────────────────────────────────────
        // Hoodie kangaroo pocket (larger than varsity C/D)
        'C_hoodie' => ['cx'=>200,'cy'=>335,'imgX'=>138,'imgY'=>303,'imgW'=>124,'imgH'=>65,'rotate'=>null,             'svgs'=>['front']],
        // Hoodie left sleeve (D1/D2/D3)
        'D1' => ['cx'=>75, 'cy'=>182,'imgX'=>53, 'imgY'=>160,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-15 75 182)','svgs'=>['front']],
        'D2' => ['cx'=>72, 'cy'=>257,'imgX'=>50, 'imgY'=>235,'imgW'=>44, 'imgH'=>44, 'rotate'=>'rotate(-12 72 257)','svgs'=>['front']],
        'D3' => ['cx'=>73, 'cy'=>335,'imgX'=>53, 'imgY'=>315,'imgW'=>40, 'imgH'=>40, 'rotate'=>'rotate(-7 73 335)', 'svgs'=>['front']],
        // Hoodie right sleeve (E1/E2/E3) — reuse keys, same positions
        // (E1/E2/E3 already defined above with same coords — hoodie right sleeve matches)
        // Hoodie back zones
        'F'  => ['cx'=>200,'cy'=>181,'imgX'=>152,'imgY'=>148,'imgW'=>96, 'imgH'=>66, 'rotate'=>null,                  'svgs'=>['back']],
        // G back panel — hoodie G is slightly different position
        'G_hoodie' => ['cx'=>200,'cy'=>298,'imgX'=>126,'imgY'=>232,'imgW'=>148,'imgH'=>132,'rotate'=>null,            'svgs'=>['back']],
    ];

    /*
     * Runtime correction: map garment-specific zone keys to their coords.
     * The garment type is known from $config->garmentType().
     */
    $garmentType = $config->garmentType();
    if ($garmentType === 'hoodie') {
        // Remap hoodie zones to their actual coordinates
        $zoneCoords['C'] = $zoneCoords['C_hoodie'];  // kangaroo pocket
        $zoneCoords['G'] = $zoneCoords['G_hoodie'];  // back panel
    }

    if ($garmentType === 'stole') {
        /*
         * Stole — 500×780 viewBox
         * Both panels: straight top at y=20, vertical sides, sharp tip bottom.
         * V-neck opens from (200,20)→(250,160)→(300,20)
         *
         * Zone rects (from stole.blade.php):
         *   A: left upper  — x=76,  y=36,  w=108, h=130  → svgs=['front']
         *   B: left lower  — x=76,  y=480, w=108, h=155  → svgs=['front']
         *   C: right upper — x=316, y=36,  w=108, h=130  → svgs=['front']
         *   D: right lower — x=316, y=480, w=108, h=155  → svgs=['front']
         *
         * cx/cy = center of each rect for text placement
         */
        $zoneCoords['A'] = ['cx'=>130,'cy'=>101,'imgX'=>76, 'imgY'=>36, 'imgW'=>108,'imgH'=>130,'rotate'=>null,'svgs'=>['front']];
        $zoneCoords['B'] = ['cx'=>130,'cy'=>557,'imgX'=>76, 'imgY'=>480,'imgW'=>108,'imgH'=>155,'rotate'=>null,'svgs'=>['front']];
        $zoneCoords['C'] = ['cx'=>370,'cy'=>101,'imgX'=>316,'imgY'=>36, 'imgW'=>108,'imgH'=>130,'rotate'=>null,'svgs'=>['front']];
        $zoneCoords['D'] = ['cx'=>370,'cy'=>557,'imgX'=>316,'imgY'=>480,'imgW'=>108,'imgH'=>155,'rotate'=>null,'svgs'=>['front']];
    }

    if ($garmentType === 'tshirt') {
        /*
         * T-shirt zones — 400×500 viewBox, same as hoodie coordinate space.
         *
         * Zone SVG coords (from tshirt.blade.php):
         *   A  — left chest:   rect(118,110, 72×72),   front only
         *   B  — right chest:  rect(210,110, 72×72),   front only
         *   C  — front panel:  rect(140,220, 120×120), front only  (large print area)
         *   D1 — left sleeve:  rect(34,133,  42×42),   rotate(-20 55 155), front+back
         *   E1 — right sleeve: rect(324,133, 42×42),   rotate(20 345 155), front+back
         *   F  — back panel:   rect(130,150, 140×160), back only  (large print area)
         */
        $zoneCoords['A']  = ['cx'=>154,'cy'=>146,'imgX'=>118,'imgY'=>110,'imgW'=>72, 'imgH'=>72, 'rotate'=>null,                    'svgs'=>['front']];
        $zoneCoords['B']  = ['cx'=>246,'cy'=>146,'imgX'=>210,'imgY'=>110,'imgW'=>72, 'imgH'=>72, 'rotate'=>null,                    'svgs'=>['front']];
        $zoneCoords['C']  = ['cx'=>200,'cy'=>280,'imgX'=>140,'imgY'=>220,'imgW'=>120,'imgH'=>120,'rotate'=>null,                    'svgs'=>['front']];
        $zoneCoords['D1'] = ['cx'=>55, 'cy'=>154,'imgX'=>34, 'imgY'=>133,'imgW'=>42, 'imgH'=>42, 'rotate'=>'rotate(-20 55 154)',   'svgs'=>['front']];
        $zoneCoords['E1'] = ['cx'=>345,'cy'=>154,'imgX'=>324,'imgY'=>133,'imgW'=>42, 'imgH'=>42, 'rotate'=>'rotate(20 345 154)',    'svgs'=>['front']];
        $zoneCoords['F']  = ['cx'=>200,'cy'=>230,'imgX'=>130,'imgY'=>150,'imgW'=>140,'imgH'=>160,'rotate'=>null,                    'svgs'=>['back']];
    }

    if ($garmentType === 'graduation_robe') {
        /*
         * Robe zones use SVG transform="translate(x,y) rotate(deg)" on their <g> groups.
         * We set localTransform=true so the engine places images in LOCAL coordinate space:
         *   - The layer <g> inherits the zone's transform via groupTransform
         *   - imgX/imgY are relative to the translated origin (so zones 1,2,4 use 0,0)
         *   - cx/cy are the text anchor in local space
         *
         * Zone SVG transforms (from graduation_robe.blade.php):
         *   Zone 1: translate(285,220)            → 50×50 box at local 0,0
         *   Zone 2: translate(165,220)            → 50×50 box at local 0,0
         *   Zone 4: translate(180,210)            → 140×180 box at local 0,0
         *   Zone 5: translate(80,210) rotate(25)  → 50×80 box at local -25,-40
         *   Zone 6: translate(420,210) rotate(-25)→ 50×80 box at local -25,-40
         */
        $zoneCoords['1'] = ['cx'=>25,'cy'=>25,'imgX'=>0,   'imgY'=>0,  'imgW'=>50, 'imgH'=>50, 'rotate'=>null,
                             'svgs'=>['front'], 'localTransform'=>true, 'groupTransform'=>'translate(285,220)'];
        $zoneCoords['2'] = ['cx'=>25,'cy'=>25,'imgX'=>0,   'imgY'=>0,  'imgW'=>50, 'imgH'=>50, 'rotate'=>null,
                             'svgs'=>['front'], 'localTransform'=>true, 'groupTransform'=>'translate(165,220)'];
        $zoneCoords['4'] = ['cx'=>70,'cy'=>90,'imgX'=>0,   'imgY'=>0,  'imgW'=>140,'imgH'=>180,'rotate'=>null,
                             'svgs'=>['back'],  'localTransform'=>true, 'groupTransform'=>'translate(180,210)'];
        $zoneCoords['5'] = ['cx'=>0, 'cy'=>0, 'imgX'=>-25,'imgY'=>-40,'imgW'=>50, 'imgH'=>80, 'rotate'=>null,
                             'svgs'=>['front','back'], 'localTransform'=>true, 'groupTransform'=>'translate(80,210) rotate(25)'];
        $zoneCoords['6'] = ['cx'=>0, 'cy'=>0, 'imgX'=>-25,'imgY'=>-40,'imgW'=>50, 'imgH'=>80, 'rotate'=>null,
                             'svgs'=>['front','back'], 'localTransform'=>true, 'groupTransform'=>'translate(420,210) rotate(-25)'];
    }

    $colorLabels = [
        // Jacket / Hoodie
        'body'   => 'الجسم',
        'sleeve' => 'الأكمام',
        'rib'    => 'الأطواق',
        'stripe' => 'الخطوط',
        // Graduation Robe
        'main'   => 'لون الثوب الأساسي',
        'yoke1'  => 'الشريط الأول (الخارجي)',
        'yoke2'  => 'الشريط الثاني (الأوسط)',
        'yoke3'  => 'الشريط الثالث (الداخلي)',
        'line'   => 'لون التحديد',
        // T-Shirt
        'collar' => 'الياقة',
        'stitch' => 'الخيط',
        // Stole
        'border' => 'الحدود',
    ];

    // CSS var prefix differs per garment:
    //   jacket/hoodie → --c-{area}   (--c-body, --c-sleeve, …)
    //   robe          → --c-{area}   (--c-main, --c-yoke1, …)
    // Both are already handled by garmentCSSVars() in Alpine — keys match exactly.
@endphp

{{-- Alpine root ─────────────────────────────────────────────────────────── --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     style="padding-bottom: max(80px, env(safe-area-inset-bottom, 80px));"
     x-data="designEngine()"
     x-init="init()"
     @zone-open.window="openZone($event.detail.key)">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm mb-8" style="color: var(--muted);">
        <a href="{{ route('customize.index') }}"
           class="hover:text-black transition-colors">تخصيص</a>
        <span>/</span>
        <span style="color:var(--ink); font-weight:500;">{{ $product->name }}</span>
    </nav>

    <div class="customizer">

        {{-- ════════════════════════════════════════════════════════════════
             LEFT COLUMN: Garment stage
        ════════════════════════════════════════════════════════════════ --}}
        <div class="garment-stage">
            <div class="garment-stage__toolbar">
                <span class="garment-stage__title">معاينة مباشرة</span>

                <div class="view-toggle" role="group" aria-label="تبديل العرض">
                    <button type="button"
                            class="view-toggle__btn"
                            :class="{ 'is-active': designState.view === 'front' }"
                            @click="designState.view = 'front'">
                        الأمامي
                    </button>
                    <button type="button"
                            class="view-toggle__btn"
                            :class="{ 'is-active': designState.view === 'back' }"
                            @click="designState.view = 'back'">
                        الخلفي
                    </button>
                </div>
            </div>

            {{-- SVG wrapper — CSS vars are set here so both SVGs inherit them --}}
            <div id="garment-wrapper"
                 class="w-full max-w-sm mx-auto"
                 :style="garmentCSSVars()">

                {{--
                    The garment partial defines:
                      - <svg id="view-front"> and <svg id="view-back">
                      - Zone hit-areas with data-zone attributes
                      - Empty content layers: #svg-img-{KEY}, #svg-txt-{KEY}
                      - ClipPaths: #clip-{KEY}
                --}}
                @include('customize.garments.' . $config->garmentType(), [
                    'zones'       => $zones,
                    'zoneCoords'  => $zoneCoords,
                    'defaults'    => $defaults,
                ])
            </div>

            <p class="text-center text-xs mt-4" style="color:var(--muted);">
                انقر على منطقة مضللة لتخصيصها
            </p>
        </div>

        {{-- ════════════════════════════════════════════════════════════════
             RIGHT COLUMN: Controls
        ════════════════════════════════════════════════════════════════ --}}
        <div class="controls-panel">

            {{-- Product info --}}
            <div class="card">
                <p class="card__label">المنتج</p>
                <h1 style="font-family:var(--font-display);font-size:18px;font-weight:700;
                           color:var(--ink);margin:0 0 4px;">
                    {{ $product->name }}
                </h1>
                <p style="color:var(--muted);font-size:13px;margin:0 0 10px;">
                    {{ $product->short_description ?? '' }}
                </p>
                <span style="font-size:20px;font-weight:700;font-family:var(--font-display);color:var(--ink);">
                    {{ $product->formatted_price ?? number_format($product->price, 2) }}
                </span>
            </div>

            {{-- ── Color pickers ──────────────────────────────────────────── --}}
            @if(! empty($colors))
            <div class="card">
                <span class="card__label">الألوان</span>

                @foreach($colors as $area => $palette)
                <div class="color-row">
                    <div>
                        <p class="color-row__name">{{ $colorLabels[$area] ?? $area }}</p>
                        <p class="color-row__hex"
                           :class="'hex-display-{{ $area }}'"
                           x-text="designState.colors['{{ $area }}']">
                        </p>
                    </div>
                    <div class="palette">
                        @foreach($palette as $hex)
                        <button type="button"
                                class="swatch"
                                :class="{ 'is-selected': designState.colors['{{ $area }}'] === '{{ $hex }}' }"
                                style="background: {{ $hex }};"
                                title="{{ $hex }}"
                                @click="setColor('{{ $area }}', '{{ $hex }}')">
                        </button>
                        @endforeach

                        {{-- Free-pick --}}
                        <label class="swatch swatch--custom" title="لون حر">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <input type="color"
                                   class="sr-only"
                                   :value="designState.colors['{{ $area }}']"
                                   @input="setColor('{{ $area }}', $event.target.value)">
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── Size selector ──────────────────────────────────────────── --}}
            @include('customize.partials.size-selector', ['garmentType' => $config->garmentType()])

            {{-- ── Zone editor ────────────────────────────────────────────── --}}
            <div class="card">
                <span class="card__label">مناطق التخصيص</span>

                {{-- Zone tabs --}}
                <div class="zone-tabs" role="tablist">
                    @foreach($zones as $zone)
                    <button type="button"
                            class="zone-tab"
                            role="tab"
                            :class="{
                                'is-active':   designState.activeZoneKey === '{{ $zone['key'] }}',
                                'has-content': zoneHasContent('{{ $zone['key'] }}')
                            }"
                            @click="openZone('{{ $zone['key'] }}')"
                            :aria-selected="designState.activeZoneKey === '{{ $zone['key'] }}'">
                        {{ $zone['key'] }}
                        <span x-show="designState.activeZoneKey !== '{{ $zone['key'] }}'"
                              style="color:var(--muted);font-size:10px;display:block;margin-top:1px;">
                            {{ Str::limit($zone['label'], 8) }}
                        </span>
                    </button>
                    @endforeach
                </div>

                {{-- Empty state --}}
                <div class="zone-empty-state" x-show="! designState.activeZoneKey">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M15 15l-2 5L9 9l11 6-5 2zm0 0l5 5"/>
                    </svg>
                    <p style="font-size:13px;">انقر على منطقة في الصورة أو اختر تبويبًا</p>
                </div>

                {{-- Per-zone editor panels --}}
                @foreach($zones as $zone)
                @php $key = $zone['key']; $type = $zone['type']; @endphp
                <div class="zone-editor"
                     :class="{ 'is-visible': designState.activeZoneKey === '{{ $key }}' }"
                     role="tabpanel">

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <div>
                            <p style="font-weight:600;color:var(--ink);font-size:14px;">{{ $zone['label'] }}</p>
                            <p style="font-size:11px;color:var(--muted);">
                                @if($type === 'text') نص فقط
                                @elseif($type === 'image') صورة فقط
                                @else نص وصورة @endif
                            </p>
                        </div>
                        {{-- Zone active toggle --}}
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <span style="font-size:12px;color:var(--muted);">تفعيل</span>
                            <div style="position:relative;width:36px;height:20px;">
                                <input type="checkbox"
                                       class="sr-only"
                                       :checked="designState.zones['{{ $key }}']?.active"
                                       @change="toggleZone('{{ $key }}', $event.target.checked)">
                                <div style="width:36px;height:20px;border-radius:999px;transition:background .2s;"
                                     :style="designState.zones['{{ $key }}']?.active
                                             ? 'background:var(--ink)'
                                             : 'background:var(--border)'">
                                </div>
                                <div style="position:absolute;top:2px;width:16px;height:16px;background:#fff;
                                            border-radius:50%;transition:left .2s;"
                                     :style="designState.zones['{{ $key }}']?.active
                                             ? 'left:18px' : 'left:2px'">
                                </div>
                            </div>
                        </label>
                    </div>

                    {{-- ── Text input ──────────────────────────────────────── --}}
                    @if(in_array($type, ['text', 'both']))
                    <div style="margin-bottom:16px;">
                        <label class="field-label" for="txt-{{ $key }}">النص على المنتج</label>
                        <input type="text"
                               id="txt-{{ $key }}"
                               class="text-input"
                               maxlength="30"
                               placeholder="مثال: SMITH 23"
                               :value="designState.zones['{{ $key }}']?.text ?? ''"
                               @input="onTextInput('{{ $key }}', $event.target.value)">
                        <div style="display:flex;justify-content:space-between;margin-top:5px;">
                            <span style="font-size:11px;color:var(--muted);">يظهر مباشرة على الصورة</span>
                            <span style="font-size:11px;color:var(--muted);" dir="ltr">
                                <span x-text="(designState.zones['{{ $key }}']?.text ?? '').length"></span>/30
                            </span>
                        </div>

                        {{-- Text style controls --}}
                        <div class="sub-controls" style="margin-top:10px;">
                            <div>
                                <label class="field-label">اللون</label>
                                <input type="color"
                                       style="width:100%;height:34px;border:1px solid var(--border);border-radius:var(--radius-sm);cursor:pointer;padding:2px;"
                                       :value="designState.zones['{{ $key }}']?.textColor ?? '#ffffff'"
                                       @input="onStyleChange('{{ $key }}', 'textColor', $event.target.value)">
                            </div>
                            <div>
                                <label class="field-label">الحجم</label>
                                <select class="mini-select"
                                        @change="onStyleChange('{{ $key }}', 'fontSize', $event.target.value)">
                                    <option value="16">صغير</option>
                                    <option value="22" selected>وسط</option>
                                    <option value="30">كبير</option>
                                    <option value="40">كبير جداً</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">الأسلوب</label>
                                <select class="mini-select"
                                        @change="onStyleChange('{{ $key }}', 'fontStyle', $event.target.value)">
                                    <option value="normal">عادي</option>
                                    <option value="italic">مائل</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ── Image upload ─────────────────────────────────────── --}}
                    @if(in_array($type, ['image', 'both']))
                    <div>
                        <label class="field-label">صورة مخصصة</label>

                        {{-- No image yet --}}
                        <label class="upload-zone"
                               :class="{ 'is-dragging': dragZone === '{{ $key }}' }"
                               x-show="! designState.zones['{{ $key }}']?.imageDataUrl"
                               @dragover.prevent="dragZone = '{{ $key }}'"
                               @dragleave="dragZone = null"
                               @drop.prevent="handleDrop('{{ $key }}', $event)">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.3" style="margin:0 auto 8px;display:block;opacity:.4;">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p style="font-size:12px;color:var(--muted);margin:0;">اسحب أو انقر لرفع صورة</p>
                            <p style="font-size:11px;color:var(--border);margin:4px 0 0;">PNG, JPG, WebP — حد أقصى 10 ميغا</p>
                            <input type="file"
                                   class="sr-only"
                                   accept="image/*"
                                   @change="handleFileInput('{{ $key }}', $event)">
                        </label>

                        {{-- Image preview --}}
                        <div x-show="designState.zones['{{ $key }}']?.imageDataUrl" style="position:relative;">
                            <img :src="designState.zones['{{ $key }}']?.imageDataUrl"
                                 class="upload-zone__preview"
                                 alt="معاينة">
                            <button type="button"
                                    class="upload-zone__clear"
                                    @click="clearImage('{{ $key }}')">
                                حذف الصورة
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
                @endforeach
            </div>

            {{-- ── Notes ──────────────────────────────────────────────────── --}}
            <div class="card">
                <label class="card__label" for="field-notes">ملاحظات للفريق</label>
                <textarea id="field-notes"
                          rows="3"
                          maxlength="1000"
                          placeholder="أي تعليمات خاصة..."
                          style="width:100%;border:1px solid var(--border);border-radius:var(--radius-sm);
                                 padding:10px 14px;font-family:var(--font-body);font-size:13px;
                                 color:var(--ink);resize:none;outline:none;background:var(--surface);"
                          x-model="designState.notes"
                ></textarea>
            </div>

            {{-- ── Submit ──────────────────────────────────────────────────── --}}
            <button type="button"
                    class="btn-submit"
                    :disabled="isSubmitting"
                    @click="submitDesign()">
                <span x-show="!isSubmitting">حفظ التصميم وإضافة للسلة</span>
                <span x-show="isSubmitting">جارٍ الحفظ…</span>
            </button>

            @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);
                        padding:14px 16px;font-size:13px;color:#991b1b;">
                <ul style="margin:0;padding:0 16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>{{-- /controls-panel --}}
    </div>{{-- /customizer --}}
</div>{{-- /Alpine root --}}

{{-- Toast --}}
<div id="toast" class="toast"></div>
{{-- Progress --}}
<div id="progress-bar" class="progress-bar"></div>

@endsection

@push('scripts')
<script>
{{--
  ═══════════════════════════════════════════════════════════════════════════════
  designEngine() — Alpine.js component
  ═══════════════════════════════════════════════════════════════════════════════
--}}
function designEngine() {

    // ── PHP → JS: zone metadata and coordinate map ─────────────────────────────
    const ZONE_META   = @json($zonesMeta);
    const ZONE_COORDS = @json($zoneCoords);
    const NS = 'http://www.w3.org/2000/svg';

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /** Build initial per-zone state entry */
    function makeZoneState(meta) {
        return {
            key:          meta.key,
            active:       false,
            text:         '',
            textColor:    '#ffffff',
            fontSize:     22,
            fontStyle:    'normal',
            imageDataUrl: null,
            imageFile:    null,   // actual File object — serialized on submit
            type:         meta.type,
        };
    }

    /** Ensure an SVG element exists; create it if not */
    function ensureSVGEl(parentId, tagName, id, attrs = {}) {
        let el = document.getElementById(id);
        if (!el) {
            const parent = document.getElementById(parentId);
            if (!parent) return null;
            el = document.createElementNS(NS, tagName);
            el.setAttribute('id', id);
            for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v);
            parent.appendChild(el);
        }
        return el;
    }

    /** Safely set multiple SVG attributes */
    function setAttrs(el, attrs) {
        if (!el) return;
        for (const [k, v] of Object.entries(attrs)) {
            if (v === null || v === undefined) el.removeAttribute(k);
            else el.setAttribute(k, v);
        }
    }

    return {
        // ─── State ───────────────────────────────────────────────────────────────
        designState: {
            colors: @json($defaults),   // { body:'#hex', sleeve:'#hex', ... }
            zones:  {},                  // populated in init()
            activeZoneKey: null,
            view: 'front',
            notes: '{{ old('notes') }}',
            size:  '{{ old('size') }}',
        },
        isSubmitting: false,
        dragZone: null,

        // ─── Lifecycle ───────────────────────────────────────────────────────────

        init() {
            // Populate zones from PHP metadata
            for (const [key, meta] of Object.entries(ZONE_META)) {
                this.designState.zones[key] = makeZoneState(meta);
            }

            // Watch colors → update SVG CSS vars immediately
            this.$watch('designState.colors', (colors) => {
                const wrapper = document.getElementById('garment-wrapper');
                if (!wrapper) return;
                for (const [area, hex] of Object.entries(colors)) {
                    wrapper.style.setProperty(`--c-${area}`, hex);
                }
            }, { deep: true });

            // Watch view toggle → show/hide SVGs
            this.$watch('designState.view', (v) => {
                const front = document.getElementById('view-front');
                const back  = document.getElementById('view-back');
                if (front) front.style.display = v === 'front' ? '' : 'none';
                if (back)  back.style.display  = v === 'back'  ? '' : 'none';
            });

            // Apply initial defaults to the SVG wrapper
            const wrapper = document.getElementById('garment-wrapper');
            if (wrapper) {
                for (const [area, hex] of Object.entries(this.designState.colors)) {
                    wrapper.style.setProperty(`--c-${area}`, hex);
                }
            }
        },

        // ─── Computed helpers ────────────────────────────────────────────────────

        /** Returns inline style string for the garment wrapper CSS vars */
        garmentCSSVars() {
            return Object.entries(this.designState.colors)
                .map(([k, v]) => `--c-${k}: ${v}`)
                .join(';');
        },

        zoneHasContent(key) {
            const z = this.designState.zones[key];
            if (!z) return false;
            return z.active && (z.text.trim().length > 0 || !!z.imageDataUrl);
        },

        // ─── Color ───────────────────────────────────────────────────────────────

        setColor(area, hex) {
            this.designState.colors[area] = hex;
            // CSS var is updated by the $watch above immediately
        },

        // ─── Zone management ─────────────────────────────────────────────────────

        openZone(key) {
            this.designState.activeZoneKey = key;

            // Highlight in both SVGs
            document.querySelectorAll('[data-zone]').forEach(el => {
                el.classList.toggle('zone--active', el.dataset.zone === key);
            });

            // Auto-activate
            if (!this.designState.zones[key].active) {
                this.toggleZone(key, true);
            }
        },

        toggleZone(key, active) {
            this.designState.zones[key].active = active;
            document.querySelectorAll(`[data-zone="${key}"]`).forEach(el => {
                el.classList.toggle('zone--active', active);
            });
            if (!active) {
                // Clear content layers from the SVG
                this._clearSVGContent(key);
            }
        },

        // ─── Text input ──────────────────────────────────────────────────────────

        onTextInput(key, value) {
            this.designState.zones[key].text = value;
            if (value.trim().length > 0 && !this.designState.zones[key].active) {
                this.toggleZone(key, true);
            }
            this._renderZoneText(key);
        },

        onStyleChange(key, prop, value) {
            this.designState.zones[key][prop] = prop === 'fontSize' ? Number(value) : value;
            this._renderZoneText(key);
        },

        // ─── Image upload ────────────────────────────────────────────────────────

        handleFileInput(key, event) {
            const file = event.target.files?.[0];
            if (file) this._loadImageFile(key, file);
        },

        handleDrop(key, event) {
            this.dragZone = null;
            const file = event.dataTransfer.files?.[0];
            if (file && file.type.startsWith('image/')) this._loadImageFile(key, file);
        },

        _loadImageFile(key, file) {
            if (file.size > 10 * 1024 * 1024) {
                this._toast('حجم الملف يتجاوز 10 ميغابايت', true);
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                this.designState.zones[key].imageDataUrl = e.target.result;
                this.designState.zones[key].imageFile    = file;
                if (!this.designState.zones[key].active) this.toggleZone(key, true);
                this._renderZoneImage(key);
                // Mark zone in SVG
                document.querySelectorAll(`[data-zone="${key}"]`)
                    .forEach(el => el.classList.add('zone--has-content'));
            };
            reader.readAsDataURL(file);
        },

        clearImage(key) {
            this.designState.zones[key].imageDataUrl = null;
            this.designState.zones[key].imageFile    = null;
            this._clearSVGImage(key);
            document.querySelectorAll(`[data-zone="${key}"]`)
                .forEach(el => el.classList.remove('zone--has-content'));
        },

        // ═══════════════════════════════════════════════════════════════════════
        // SVG DOM MUTATION ENGINE
        //
        // Each function targets the <g id="zone-content-{KEY}"> layer
        // that the garment partial pre-renders inside each named zone.
        // ═══════════════════════════════════════════════════════════════════════

        _getCoords(key) { return ZONE_COORDS[key] || null; },

        /** Render / update the text node for a zone inside the SVG */
        _renderZoneText(key) {
            const z   = this.designState.zones[key];
            const c   = this._getCoords(key);
            if (!c || !z) return;

            // Determine which SVG(s) this zone lives in
            const svgIds = (c.svgs || ['front']).map(v => `view-${v}`);

            svgIds.forEach(svgId => {
                const svgEl = document.getElementById(svgId);
                if (!svgEl) return;

                const layerId = `zone-content-${svgId}-${key}`;
                const layer   = this._getOrCreateLayer(svgId, key, c);
                if (!layer) return;

                // Find or create the <text> element
                let textEl = layer.querySelector('text.svg-zone-text');
                if (!textEl) {
                    textEl = document.createElementNS(NS, 'text');
                    textEl.classList.add('svg-zone-text');
                    layer.appendChild(textEl);
                }

                const text = z.text.trim();
                setAttrs(textEl, {
                    x:             c.cx,
                    y:             c.cy,
                    fill:          z.textColor || '#ffffff',
                    'font-size':   z.fontSize || 22,
                    'font-style':  z.fontStyle || 'normal',
                    transform:     c.rotate || null,
                });
                textEl.textContent = text;
                textEl.style.display = text.length > 0 ? '' : 'none';

                if (text.length > 0) {
                    document.querySelectorAll(`[data-zone="${key}"]`)
                        .forEach(el => el.classList.add('zone--has-content'));
                } else {
                    // Only remove has-content if image is also absent
                    if (!z.imageDataUrl) {
                        document.querySelectorAll(`[data-zone="${key}"]`)
                            .forEach(el => el.classList.remove('zone--has-content'));
                    }
                }
            });
        },

        /** Render / update the <image> element inside the SVG zone */
        _renderZoneImage(key) {
            const z = this.designState.zones[key];
            const c = this._getCoords(key);
            if (!c || !z?.imageDataUrl) return;

            const svgIds = (c.svgs || ['front']).map(v => `view-${v}`);

            svgIds.forEach(svgId => {
                const svgEl = document.getElementById(svgId);
                if (!svgEl) return;

                const layer = this._getOrCreateLayer(svgId, key, c);
                if (!layer) return;

                // Find or create the <image> element
                let imgEl = layer.querySelector('image.svg-zone-image');
                if (!imgEl) {
                    imgEl = document.createElementNS(NS, 'image');
                    imgEl.classList.add('svg-zone-image');
                    // Clip to the zone bounds
                    imgEl.setAttribute('clip-path', `url(#clip-${key})`);
                    // Place image before text so text renders on top
                    const textEl = layer.querySelector('text');
                    layer.insertBefore(imgEl, textEl || null);
                }

                setAttrs(imgEl, {
                    href:               z.imageDataUrl,
                    x:                  c.imgX,
                    y:                  c.imgY,
                    width:              c.imgW,
                    height:             c.imgH,
                    preserveAspectRatio:'xMidYMid meet',
                    transform:          c.rotate || null,
                });
                imgEl.style.display = '';
            });
        },

        /** Hide image element */
        _clearSVGImage(key) {
            document.querySelectorAll(`image.svg-zone-image[data-key="${key}"]`)
                .forEach(el => { el.setAttribute('href', ''); el.style.display = 'none'; });

            // Also target within layers
            ['front', 'back'].forEach(side => {
                const layer = document.getElementById(`zone-layer-view-${side}-${key}`);
                if (!layer) return;
                const img = layer.querySelector('image.svg-zone-image');
                if (img) { img.setAttribute('href', ''); img.style.display = 'none'; }
            });
        },

        /** Remove all content for a zone from the SVG */
        _clearSVGContent(key) {
            ['front', 'back'].forEach(side => {
                const layer = document.getElementById(`zone-layer-view-${side}-${key}`);
                if (!layer) return;
                layer.querySelectorAll('text, image').forEach(el => {
                    if (el.tagName === 'text') el.textContent = '';
                    if (el.tagName === 'image') el.setAttribute('href', '');
                    el.style.display = 'none';
                });
            });
            document.querySelectorAll(`[data-zone="${key}"]`)
                .forEach(el => el.classList.remove('zone--has-content'));
        },

        /**
         * Get or create the transparent <g> layer that sits above the
         * zone hit-area inside the SVG. This is where we inject content.
         *
         * Layer ID pattern: zone-layer-{svgId}-{key}
         * It is appended to the <svg> element itself (not inside the zone hit group)
         * so it renders above zone overlays but doesn't interfere with pointer events.
         */
        _getOrCreateLayer(svgId, key, coords) {
            const id  = `zone-layer-${svgId}-${key}`;
            const svg = document.getElementById(svgId);
            if (!svg) return null;

            let layer = document.getElementById(id);
            if (!layer) {
                layer = document.createElementNS(NS, 'g');
                layer.setAttribute('id', id);
                layer.setAttribute('pointer-events', 'none');

                /*
                 * For robe zones (and any zone with localTransform=true):
                 * the zone SVG uses transform="translate(x,y) rotate(deg)"
                 * so our content layer must inherit the SAME transform so that
                 * imgX/imgY/cx/cy coords are in local space, not global SVG space.
                 */
                if (coords.localTransform && coords.groupTransform) {
                    layer.setAttribute('transform', coords.groupTransform);
                }

                svg.appendChild(layer);

                // Ensure the clipPath exists in the SVG defs
                this._ensureClipPath(svg, key, coords);
            }
            return layer;
        },

        /**
         * Add a <clipPath> to the SVG's <defs> if it isn't already there.
         * This bounds the customer's image to the zone rectangle.
         */
        _ensureClipPath(svgEl, key, coords) {
            const clipId = `clip-${key}`;
            if (svgEl.querySelector(`#${clipId}`)) return;

            let defs = svgEl.querySelector('defs');
            if (!defs) {
                defs = document.createElementNS(NS, 'defs');
                svgEl.insertBefore(defs, svgEl.firstChild);
            }

            const clip = document.createElementNS(NS, 'clipPath');
            clip.setAttribute('id', clipId);

            /*
             * localTransform zones (robe): the clipPath is referenced from inside
             * a <g transform="..."> layer, so the clip rect coords are already in
             * local space — no additional transform needed on the rect itself.
             *
             * Non-localTransform zones (jacket, hoodie): coords are in global SVG
             * space, so apply coords.rotate if present.
             */
            if (coords.localTransform && coords.groupTransform) {
                // Clip must be in the SAME coordinate space as the layer.
                // We set clipPathUnits="userSpaceOnUse" and apply the group
                // transform to the clipPath itself so it maps correctly.
                clip.setAttribute('clipPathUnits', 'userSpaceOnUse');
                clip.setAttribute('transform', coords.groupTransform);
            }

            const rect = document.createElementNS(NS, 'rect');
            setAttrs(rect, {
                x:      coords.imgX,
                y:      coords.imgY,
                width:  coords.imgW,
                height: coords.imgH,
            });

            // For non-local zones with a rotation (e.g. sleeved jacket zones)
            if (!coords.localTransform && coords.rotate) {
                rect.setAttribute('transform', coords.rotate);
            }

            clip.appendChild(rect);
            defs.appendChild(clip);
        },

        // ═══════════════════════════════════════════════════════════════════════
        // FORM SUBMISSION
        //
        // Serializes designState into a FormData object and POSTs via fetch.
        // Files (imageFile) are attached directly as Blob — no base64 to server.
        // ═══════════════════════════════════════════════════════════════════════

        async submitDesign() {
            this.isSubmitting = true;
            this._setProgress(20);

            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content
                              || '{{ csrf_token() }}');

            // Colors
            for (const [area, hex] of Object.entries(this.designState.colors)) {
                fd.append(`colors[${area}]`, hex);
            }

            // Notes
            fd.append('notes', this.designState.notes || '');

            // Size
            fd.append('size', this.designState.size || '');

            // Zones
            // NOTE: key is always a String from Object.entries(), e.g. '1','2','A','G'
            // We explicitly convert to ensure PHP receives string keys in $_FILES
            for (const [key, zone] of Object.entries(this.designState.zones)) {
                if (!zone.active) continue;

                const k = String(key); // guarantee string — robe zones '1','2','4','5','6'

                fd.append('selected_zones[]', k);

                if (zone.text?.trim()) {
                    fd.append(`texts[${k}]`, zone.text.trim());
                    fd.append(`text_styles[${k}][color]`,     zone.textColor  || '#ffffff');
                    fd.append(`text_styles[${k}][fontSize]`,  String(zone.fontSize   || 22));
                    fd.append(`text_styles[${k}][fontStyle]`, zone.fontStyle  || 'normal');
                }

                if (zone.imageFile) {
                    fd.append(`images[${k}]`, zone.imageFile, zone.imageFile.name);
                }
            }

            // Also send the full JSON snapshot for easy Phase 2 consumption
            const snapshot = this._buildSnapshot();
            fd.append('design_snapshot', JSON.stringify(snapshot));

            this._setProgress(50);

            try {
                const resp = await fetch('{{ route('customize.store', $garmentSlug) }}', {
                    method: 'POST',
                    body: fd,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                this._setProgress(90);

                if (resp.ok) {
                    const data = await resp.json().catch(() => ({}));
                    this._setProgress(100);
                    this._toast(data.message || 'تم حفظ التصميم بنجاح!');
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('cart.index') }}';
                    }, 800);
                } else {
                    const err = await resp.json().catch(() => ({}));
                    this._setProgress(0);
                    const msgs = err.errors
                        ? Object.values(err.errors).flat().join(' | ')
                        : (err.message || 'حدث خطأ. يرجى المحاولة مجدداً.');
                    this._toast(msgs, true);
                    this.isSubmitting = false;
                }
            } catch (e) {
                this._setProgress(0);
                this._toast('فشل الاتصال. تحقق من الإنترنت وحاول مجدداً.', true);
                this.isSubmitting = false;
            }
        },

        /**
         * Build a clean JSON snapshot of the design — used by Phase 2 renderer.
         * Image data URLs are included so the snapshot is self-contained.
         */
        _buildSnapshot() {
            const snap = {
                product_id: {{ is_object($product) && isset($product->id) ? (int)$product->id : 0 }},
                colors: { ...this.designState.colors },
                size:   this.designState.size || null,
                zones: {},
                notes: this.designState.notes,
                generated_at: new Date().toISOString(),
            };
            for (const [key, zone] of Object.entries(this.designState.zones)) {
                if (!zone.active) continue;
                snap.zones[key] = {
                    text:      zone.text?.trim() || null,
                    textColor: zone.textColor,
                    fontSize:  zone.fontSize,
                    fontStyle: zone.fontStyle,
                    hasImage:  !!zone.imageDataUrl,
                    // Note: imageDataUrl omitted from snapshot to keep it compact.
                    // The server already has the file via multipart upload.
                };
            }
            return snap;
        },

        // ─── UI helpers ──────────────────────────────────────────────────────────

        _toast(msg, isError = false) {
            const el = document.getElementById('toast');
            if (!el) return;
            el.textContent = msg;
            el.className   = 'toast is-visible' + (isError ? ' is-error' : '');
            clearTimeout(this._toastTimer);
            this._toastTimer = setTimeout(() => el.classList.remove('is-visible'), 3200);
        },

        _setProgress(pct) {
            const bar = document.getElementById('progress-bar');
            if (bar) bar.style.width = pct + '%';
            if (pct >= 100) setTimeout(() => { if (bar) bar.style.width = '0%'; }, 600);
        },

        _toastTimer: null,
    };
}
</script>
@endpush