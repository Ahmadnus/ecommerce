{{-- @include('partials.hide-store') in layouts/app.blade.php — remove to restore --}}
<style>
/* ── 1. Bottom nav — cart tab + shop tab + all badges ────────── */
a[href*="cart"].bb-item,
a[href*="products"].bb-item,
.bb-cart-bubble,
.bb-badge { display: none !important; }

/* ── 2. All prices ───────────────────────────────────────────── */
#price-wrapper,
#price-current,
.price-sale,
.price-normal,
.tabular-nums { display: none !important; }

/* ── 3. Sale / discount / featured badges & ribbons ─────────── */
.ribbon,
.fc-ribbon,
.bg-red-100.text-red-700,
.text-xs.text-red-500.font-bold.bg-red-50,
.bg-amber-100.text-amber-700,
.text-sm.text-red-500.font-semibold.mb-1 { display: none !important; }

/* ── 4. Strikethrough original prices ───────────────────────── */
.line-through { display: none !important; }

/* ── 5. Add-to-cart button + qty stepper ────────────────────── */
#add-to-cart-btn,
#qty-input,
[onclick="adjustQty(-1)"],
[onclick="adjustQty(1)"],
.flex.items-center.gap-3.mb-6.mt-2 { display: none !important; }

/* ── 6. Cart error / validation banners ─────────────────────── */


/* ── 7. Product variants & attribute selectors ──────────────── */
#variant-selectors,
#variant-sku,
.attr-block,
.variant-btn,
.color-swatch { display: none !important; }

/* ── 8. Stock indicators ────────────────────────────────────── */
#stock-status,
.bg-gray-100.text-gray-500.text-center.py-4.rounded-xl.mb-6 { display: none !important; }

/* ── 9. SKU line ─────────────────────────────────────────────── */
.text-xs.text-gray-400.mt-4 { display: none !important; }

/* ── 10. Site features grid (shipping / returns / etc.) ─────── */
.features-grid { display: none !important; }

/* ── 11. Wishlist / heart buttons ───────────────────────────── */
.heart-btn,
.wishlist-btn { display: none !important; }

/* ── 12. Hero banners ───────────────────────────────────────── */


/* ── 13. Announcement bar ───────────────────────────────────── */


/* ── 14. Category pills, grid & banners ─────────────────────── */


/* ── 15. Sort drawer & toolbar ──────────────────────────────── */
.sort-drawer-overlay,
.sort-drawer,
button[onclick="openSortDrawer()"],
select[onchange*="sort"] { display: none !important; }

/* ── 16. Live search bar ─────────────────────────────────────── */


/* ── 17. Products count line ────────────────────────────────── */
p.text-xs.text-gray-400 { display: none !important; }

/* ── 18. Product grids & featured lists ─────────────────────── */


/* ── 19. Related products section ───────────────────────────── */


/* ── 20. Breadcrumb on product detail page ──────────────────── */


/* ── 21. Regular product grid ───────────────────────────────── */


/* ── 22. Toolbar row (search + sort) ────────────────────────── */

</style>