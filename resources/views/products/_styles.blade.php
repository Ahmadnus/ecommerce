{{--
    products/_styles.blade.php — page-level CSS for the storefront index
    (homepage builder + category/search/sort views). Pushed into <head>
    from products/index.blade.php.
--}}
<style>
/* ═══════════════════════════════════════════════════════════════════
   LOCAL CSS VARIABLES
   ═══════════════════════════════════════════════════════════════════ */
:root {
    --brand:       var(--brand-color, #0ea5e9);
    --brand-dark:  color-mix(in srgb, var(--brand) 75%, #000);
    --brand-light: color-mix(in srgb, var(--brand) 12%, #fff);
    --surface:     var(--nav-bg-color, #ffffff);
    --surface-2:   var(--bg-color, #f8f8f8);
    --sale-red:    #ff3366;
    --border:      #efefef;
    --radius-card: 16px;

    --ui-text:        var(--text-body);
    --ui-text-strong: var(--text-heading);
    --ui-text-soft:   var(--text-muted);

    --card-bg:           var(--card-bg, #ffffff);
    --card-font-color:   var(--text-card);
    --card-font-strong:  var(--text-heading);
    --card-font-muted:   var(--text-muted);
    --card-price-color:  var(--text-price);
    --card-sale-color:   var(--text-price);

    --text-1: var(--text-heading);
    --text-2: var(--text-body);
}

/* ── Font application ───────────────────────────────────────────── */
html[lang="ar"], [dir="rtl"] { font-family: var(--font-ar) !important; }
html[lang="en"], [dir="ltr"] { font-family: var(--font-en) !important; }
.page-shop,
.page-shop * { font-family: var(--app-font) !important; }

/* ── Page-level text ────────────────────────────────────────────── */
.page-shop { color: var(--text-body); }

/* ── Gray utility overrides ─────────────────────────────────────── */
.page-shop :is(.text-gray-900, .text-gray-800, .text-slate-900, .text-slate-800, .text-black) {
    color: var(--text-heading) !important;
}
.page-shop :is(.text-gray-700, .text-gray-600, .text-slate-700, .text-slate-600) {
    color: var(--text-body) !important;
}
.page-shop :is(.text-gray-500, .text-gray-400, .text-gray-300, .text-slate-500, .text-slate-400) {
    color: var(--text-muted) !important;
}

/* ── Cards ──────────────────────────────────────────────────────── */
.page-shop :is(.pcard, .featured-card) { color: var(--text-card); }
.page-shop :is(.pcard, .featured-card) :is(.text-gray-900, .text-gray-800, .text-slate-900, .text-slate-800, .text-black) {
    color: var(--text-product-title) !important;
}
.page-shop :is(.pcard, .featured-card) :is(.text-gray-500, .text-gray-400, .text-gray-300, .text-slate-500, .text-slate-400) {
    color: var(--text-muted) !important;
}

/* ── PRICE — highest specificity, applied last ──────────────────── */
/* All elements with class "price-val" render the actual price amount */
.page-shop .price-val {
    color: var(--text-price) !important;
    font-size: var(--product-price-font-size) !important;
}
/* Strikethrough original prices are always muted */
.page-shop .price-original {
    color: var(--text-muted) !important;
    text-decoration: line-through;
}

/* ── Search dropdown + sort drawer ──────────────────────────────── */
.page-shop :is(.search-dropdown, .sort-drawer) { color: var(--text-body); }
.page-shop :is(.search-dropdown, .sort-drawer) :is(.text-gray-900, .text-gray-800) {
    color: var(--text-heading) !important;
}
.page-shop :is(.search-dropdown, .sort-drawer) :is(.text-gray-500, .text-gray-400, .text-gray-300) {
    color: var(--text-muted) !important;
}

/* ── Announcement bar ───────────────────────────────────────────── */
.announce-bar {
    background: var(--brand); color: #fff;
    font-size: 12px; font-weight: 700; letter-spacing: .04em;
    padding: 9px 16px; display: flex; align-items: center; justify-content: center;
    gap: 10px; overflow: hidden; position: relative;
}
.announce-bar::before, .announce-bar::after {
    content:''; position:absolute; top:50%; transform:translateY(-50%);
    width:120px; height:120px; border-radius:50%;
    background:rgba(255,255,255,.06); pointer-events:none;
}
.announce-bar::before{left:-30px} .announce-bar::after{right:-30px}
.announce-ticker {
    display:flex; align-items:center; gap:28px;
    animation:ticker 18s linear infinite; white-space:nowrap;
}
@media(min-width:768px){.announce-ticker{animation:none;gap:40px}}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
.announce-dot { width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.55);flex-shrink:0; }

/* ── Hero banner (legacy HeroBanner cards, kept for reuse) ───────── */
.hero-banner {
    background: linear-gradient(135deg,
        color-mix(in srgb, var(--brand-color) 40%, #000) 0%,
        color-mix(in srgb, var(--brand-color) 20%, #111) 55%,
        var(--bg-color) 100%
    ) !important;
    border-radius: 20px; overflow: hidden; position: relative;
}
.hero-banner::before{background:transparent!important}
.hero-banner::after{
    content:''; position:absolute; inset:0;
    background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events:none;
}
@keyframes heroFloat{0%,100%{transform:translateY(0) rotate(-2deg) scale(1)}50%{transform:translateY(-10px) rotate(1deg) scale(1.02)}}
.hero-img{animation:heroFloat 6s ease-in-out infinite}

/* ── Category pills ─────────────────────────────────────────────── */
.cat-pill {
    white-space:nowrap; padding:7px 17px; border-radius:99px;
    font-size:12px; font-weight:700; border:1.5px solid var(--border);
    background:var(--surface); color:var(--text-body); cursor:pointer;
    flex-shrink:0; text-decoration:none; display:inline-block;
    transition:all .15s;
}
.cat-pill:hover { border-color:var(--brand); color:var(--brand); }
.cat-pill.active {
    background:var(--brand); border-color:var(--brand); color:#fff;
    box-shadow:0 3px 12px color-mix(in srgb, var(--brand) 35%, transparent);
}
.scrollbar-hide::-webkit-scrollbar{display:none}
.scrollbar-hide{-ms-overflow-style:none;scrollbar-width:none}

/* ── Featured list (product_grid section cards) ─────────────────── */
.featured-list {
    display:grid; grid-template-columns:repeat(2, 1fr);
    column-gap:32px; row-gap:40px; margin-top:30px; padding:0 10px;
}
@media (min-width:768px) {
    .featured-list { grid-template-columns:repeat(4, 1fr); column-gap:24px; }
}
@media (min-width:1024px) {
    .featured-list { grid-template-columns:repeat(6, 1fr); column-gap:20px; }
}
@media (min-width:1280px) {
    .featured-list { grid-template-columns:repeat(8, 1fr); column-gap:24px; }
}
.featured-card {
    cursor:pointer; position:relative;
}
.fc-ribbon{
    position:absolute;top:0;right:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:3px 9px 3px 7px;
    border-bottom-left-radius:9px;z-index:5;letter-spacing:.04em;
}

/* ── Product card (paginated grid) ──────────────────────────────── */
.pcard {
    cursor:pointer; position:relative;
}
.ribbon{
    position:absolute;top:0;left:0;background:var(--sale-red);
    color:#fff;font-size:9px;font-weight:800;padding:2px 8px 2px 5px;
    border-bottom-right-radius:8px;letter-spacing:.04em;line-height:1.7;z-index:5;
}

/* ── Shimmer ────────────────────────────────────────────────────── */
@keyframes shimmer{0%{background-position:-900px 0}100%{background-position:900px 0}}
.shimmer{
    background:linear-gradient(90deg,#f4f4f4 25%,#ececec 50%,#f4f4f4 75%);
    background-size:1800px 100%;animation:shimmer 1.8s ease-in-out infinite;
}

/* ── Heart / wishlist ───────────────────────────────────────────── */
.heart-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.heart-btn:hover{transform:scale(1.18);background:#fff}
.heart-btn svg{width:15px;height:15px}

/* ── Share button ───────────────────────────────────────────────── */
.share-btn {
    width:30px;height:30px;background:rgba(255,255,255,.93);
    border-radius:50%;display:flex;align-items:center;justify-content:center;
    backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);
    box-shadow:0 1px 7px rgba(0,0,0,.13);border:none;cursor:pointer;flex-shrink:0;
    transition:transform .15s, background .15s;
}
.share-btn:hover{transform:scale(1.18);background:#fff}
.share-btn svg{width:14px;height:14px}

/* ── Scroll reveal ──────────────────────────────────────────────── */
.reveal{
    opacity:0;transform:translateY(22px);
    transition:opacity .55s cubic-bezier(.22,1,.36,1),transform .55s cubic-bezier(.22,1,.36,1);
    will-change:opacity,transform;
}
.reveal.visible{opacity:1;transform:translateY(0)}
.reveal{transition-delay:calc(var(--i,0) * 60ms)}

/* ── Sort drawer ────────────────────────────────────────────────── */
.sort-drawer-overlay{
    position:fixed;inset:0;background:rgba(0,0,0,.42);
    z-index:60;opacity:0;pointer-events:none;transition:opacity .25s;
}
.sort-drawer-overlay.open{opacity:1;pointer-events:auto}
.sort-drawer{
    position:fixed;bottom:0;left:0;right:0;background:var(--surface);
    border-radius:22px 22px 0 0;
    padding:20px 20px calc(env(safe-area-inset-bottom,0px) + 20px);
    z-index:61;transform:translateY(100%);
    transition:transform .32s cubic-bezier(.16,1,.3,1);
}
.sort-drawer.open{transform:translateY(0)}
.sort-option{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 0;border-bottom:1px solid #f5f5f5;
    font-size:13.5px;font-weight:600;color:var(--text-body);
    cursor:pointer;transition:color .15s;text-decoration:none;
}
.sort-option:hover,.sort-option.chosen{color:var(--brand)}
.pb-bar{padding-bottom:calc(68px + env(safe-area-inset-bottom,0px))}

/* ── Live search dropdown ───────────────────────────────────────── */
.search-dropdown {
    position:absolute;top:calc(100% + 6px);left:0;right:0;
    background:#fff;border:1px solid #e5e7eb;border-radius:16px;
    box-shadow:0 16px 40px rgba(0,0,0,.12);z-index:100;
    overflow:hidden;max-height:420px;overflow-y:auto;
}
[dir="rtl"] .search-dropdown{left:auto}
.search-result-item {
    display:flex;align-items:center;gap:12px;
    padding:10px 14px;cursor:pointer;
    transition:background .12s;text-decoration:none;
    border-bottom:1px solid #f7f6f3;
}
.search-result-item:last-child{border-bottom:none}
.search-result-item:hover{background:#f9fafb}
.search-result-img {
    width:44px;height:44px;border-radius:10px;object-fit:cover;
    flex-shrink:0;background:#f3f4f6;
}

/* Container must stay overflow-visible so full-bleed hero sections
   (.homepage-fullbleed) can break out of the max-width wrapper. */
.max-w-screen-2xl { overflow: visible !important; }

/* ── Full-bleed sections (hero banner) — break out of the padded,
   max-width page container so a hero can be truly edge-to-edge while
   still living inside the single sequential builder loop. ─────────── */
.homepage-fullbleed {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
}

/* ── Homepage divider / intro blocks (tall-media redesign) ───────── */
.home-block { text-align: center; max-width: 640px; margin: 0 auto; padding: 40px 16px; }
/* Higher specificity than plain Tailwind .text-left/.text-center/.text-right
   utilities so admin-chosen alignment reliably wins regardless of CSS load order. */
.home-block.text-left   { text-align: left; }
.home-block.text-center { text-align: center; }
.home-block.text-right  { text-align: right; }
.home-block h1 {
    font-family: var(--font-display, inherit);
    font-size: clamp(1.6rem, 4vw, 2.75rem);
    font-weight: 800; line-height: 1.15; letter-spacing: -.01em;
    color: var(--text-heading, #111827); margin-bottom: 14px;
}
.home-block p {
    font-size: 14.5px; line-height: 1.7; color: var(--text-muted, #6b7280);
    margin-bottom: 26px;
}
/* ── Category title — hardcoded Didone / Modern Serif (Bodoni Moda) ── */
.cat-title-didone {
    font-family: 'Bodoni Moda', 'Playfair Display', serif;
    font-weight: 700;
    letter-spacing: -.01em;
    color: #111827;
}

.home-cta-btn {
    background: var(--brand); color: #fff;
    border-radius: 0 !important; /* sharp corners, per client spec */
    box-shadow: 0 10px 28px color-mix(in srgb, var(--brand) 35%, transparent);
    transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    text-decoration: none;
}
.home-cta-btn:hover { transform: translateY(-2px); opacity: .93; box-shadow: 0 14px 34px color-mix(in srgb, var(--brand) 45%, transparent); }

/* ── Category banner ────────────────────────────────────────────── */
.cat-banner { border-radius: 20px; overflow: hidden; position: relative; margin-bottom: 20px; }
.cat-banner-inner { display: flex; align-items: center; gap: 24px; padding: 36px 28px; position: relative; z-index: 10; }
@media(min-width: 768px) { .cat-banner-inner { padding: 48px 56px; gap: 40px; } }
.cat-banner-img {
    width: 120px; height: 120px; flex-shrink: 0;
    border-radius: 16px; object-fit: cover;
    box-shadow: 0 12px 32px rgba(0,0,0,.25);
    animation: heroFloat 6s ease-in-out infinite;
}
@media(min-width: 640px) { .cat-banner-img { width: 160px; height: 160px; } }
@media(min-width: 768px) { .cat-banner-img { width: 200px; height: 200px; } }
.cat-banner-img-wrap {
    width: 100%; max-height: 320px; overflow: hidden;
    border-radius: 20px; margin-bottom: 20px; background: #f3f4f6;
}
.cat-banner-img-wrap img { width: 100%; height: 100%; max-height: 320px; object-fit: cover; display: block; }
@media (max-width: 640px) {
    .cat-banner-img-wrap { max-height: 160px; border-radius: 14px; }
    .cat-banner-img-wrap img { max-height: 160px; }
}

/* ── Homepage builder vertical rhythm ───────────────────────────────
   Uniform spacing between EVERY modular section (hero, categories,
   product grids, custom images, text blocks) so alternating block
   types always reads balanced and premium. ─────────────────────────── */
.homepage-builder > * + * { margin-top: 3rem; }
@media (min-width: 768px) { .homepage-builder > * + * { margin-top: 4rem; } }
</style>
