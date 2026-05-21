<style>
/* ── 1. Add-to-cart button + qty stepper ─────────────────────── */
#add-to-cart-btn,
#qty-input,
[onclick="adjustQty(-1)"],
[onclick="adjustQty(1)"],
.flex.items-center.gap-3.mb-6.mt-2 { display: none !important; }

/* ── 2. Cart icon in the bottom nav bar ──────────────────────── */
a[href*="cart"].bb-item { display: none !important; }

/* ── 3. Cart badge / count bubbles ───────────────────────────── */
.bb-cart-bubble,
.bb-badge { display: none !important; }

/* ── 4. Price wrappers (product detail page) ─────────────────── */
#price-wrapper,
#price-current,
.price-sale,
.price-normal { display: none !important; }

/* ── 5. Sale / discount badges ───────────────────────────────── */
.bg-red-100.text-red-700,
.text-xs.text-red-500.font-bold.bg-red-50,
.ribbon,
.fc-ribbon { display: none !important; }

/* ── 6. "You save X" line ────────────────────────────────────── */
.text-sm.text-red-500.font-semibold.mb-1 { display: none !important; }

/* ── 7. Strikethrough original prices ────────────────────────── */
.line-through { display: none !important; }

/* ── 8. Price rows inside product cards ──────────────────────── */


/* ── 9. Related-products price block ────────────────────────── */
.grid.grid-cols-2.md\:grid-cols-4 .p-3 div { display: none !important; }

/* ── 10. Cart validation error banner ───────────────────────── */
#cart-error-banner { display: none !important; }

/* ── 11. Stock / in-stock indicator (optional) ───────────────── */
/* #stock-status { display: none !important; } */
</style>