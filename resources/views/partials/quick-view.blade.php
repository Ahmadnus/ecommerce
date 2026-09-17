{{--
    partials/quick-view.blade.php — product quick-view popup shell.

    Included once per page from layouts/app. The panel starts empty; clicking
    any [data-quickview] link fetches products/{slug}/quick-view and drops the
    markup in.

    Progressive enhancement throughout: the cards carry ordinary hrefs to the
    product page, so with JavaScript off (or if the fetch fails) the click just
    navigates there as it always did. Ctrl/cmd/middle clicks are left alone so
    "open in new tab" keeps working.
--}}

<div class="sf-modal" id="sf-quickview" hidden>
    <div class="sf-modal__overlay" data-quickview-close></div>

    <div class="sf-modal__panel" role="dialog" aria-modal="true"
         aria-label="{{ __('app.product_details') }}" tabindex="-1">
        <div class="sf-modal__content" id="sf-quickview-content">
            {{-- filled by fetch --}}
        </div>

        <div class="sf-modal__loading" id="sf-quickview-loading">
            <span class="sf-modal__spinner" aria-hidden="true"></span>
        </div>
    </div>
</div>

<script>
(function () {
    const modal   = document.getElementById('sf-quickview');
    if (!modal) return;

    const content = document.getElementById('sf-quickview-content');
    const loading = document.getElementById('sf-quickview-loading');
    const panel   = modal.querySelector('.sf-modal__panel');

    let lastFocus = null;
    let controller = null;

    function open() {
        lastFocus = document.activeElement;
        modal.hidden = false;
        document.body.classList.add('sf-modal-open');
        panel.focus();
    }

    function close() {
        modal.hidden = true;
        document.body.classList.remove('sf-modal-open');
        content.innerHTML = '';
        if (controller) { controller.abort(); controller = null; }
        if (lastFocus) lastFocus.focus();
    }

    async function load(slug) {
        content.innerHTML = '';
        loading.hidden = false;
        open();

        if (controller) controller.abort();
        controller = new AbortController();

        try {
            const res = await fetch('/products/' + encodeURIComponent(slug) + '/quick-view', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller.signal,
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            content.innerHTML = await res.text();
            loading.hidden = true;
            panel.scrollTop = 0;
        } catch (err) {
            if (err.name === 'AbortError') return;
            // Never strand the shopper in a broken popup: fall back to the
            // real product page, which is where the link pointed anyway.
            close();
            window.location.href = '/products/' + encodeURIComponent(slug);
        }
    }

    // ── Open ────────────────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-quickview]');
        if (!trigger) return;
        // Leave modified clicks to the browser so "open in new tab" works.
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;

        e.preventDefault();
        load(trigger.dataset.quickview);
    });

    // ── Close ───────────────────────────────────────────────────────────────
    modal.addEventListener('click', function (e) {
        if (e.target.closest('[data-quickview-close]')) close();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) close();
    });

    // ── Quantity stepper + add to cart inside the popup ─────────────────────
    modal.addEventListener('click', function (e) {
        const step = e.target.closest('[data-qty-step]');
        if (step) {
            const input = document.getElementById('sf-qv-qty');
            if (!input) return;
            const min = parseInt(input.min || '1', 10);
            const max = parseInt(input.max || '99', 10);
            const next = (parseInt(input.value, 10) || min) + parseInt(step.dataset.qtyStep, 10);
            input.value = Math.min(max, Math.max(min, next));
            return;
        }

        const add = e.target.closest('[data-quickview-add]');
        if (add) {
            const input = document.getElementById('sf-qv-qty');
            const qty   = Math.max(1, parseInt(input && input.value, 10) || 1);
            // Reuses the same Cart helper as the rest of the storefront, so
            // the header badge and toast behave identically.
            Cart.add(parseInt(add.dataset.quickviewAdd, 10), qty, add);
        }
    });
})();
</script>
