{{--
    products/_scripts.blade.php — page-level JS for the storefront index:
    sort drawer, scroll reveal, product sharing, Alpine live search.
--}}
<script>
/* ── Sort drawer ──────────────────────────────────────────────────── */
function openSortDrawer() {
    document.getElementById('sort-overlay').classList.add('open');
    document.getElementById('sort-drawer').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSortDrawer() {
    document.getElementById('sort-overlay').classList.remove('open');
    document.getElementById('sort-drawer').classList.remove('open');
    document.body.style.overflow = '';
}

/* ── Scroll reveal ────────────────────────────────────────────────── */
(function () {
    var targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;
    var io = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
    targets.forEach(function(el) { io.observe(el); });
})();

document.querySelectorAll('.pcard button, .featured-card button').forEach(function(btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); });
});

/* ── Share product ────────────────────────────────────────────────── */
function shareProduct(url, title) {
    var shareData = { title: title, text: '{{ addslashes(__('app.share_text', ['name' => ''])) }}'.replace(':name', title), url: url };
    if (navigator.share) {
        navigator.share(shareData).catch(function(err) { if (err.name !== 'AbortError') console.warn('Share failed:', err); });
        return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() { showShareToast('{{ __('app.share_copied') }}'); }).catch(function() { showSharePrompt(url); });
        return;
    }
    showSharePrompt(url);
}
function showShareToast(message) {
    if (typeof Cart !== 'undefined' && Cart.toast) { Cart.toast(message, 'success'); return; }
    var toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#111827;color:#fff;font-size:13px;font-weight:600;padding:10px 18px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.2);transition:opacity .3s;pointer-events:none';
    document.body.appendChild(toast);
    setTimeout(function() { toast.style.opacity = '0'; }, 2000);
    setTimeout(function() { document.body.removeChild(toast); }, 2400);
}
function showSharePrompt(url) { window.prompt('{{ __('app.share_prompt') }}', url); }

/* ── Alpine.js liveSearch ─────────────────────────────────────────── */
document.addEventListener('alpine:init', function () {
    Alpine.data('liveSearch', function () {
        return {
            query: '', results: [], isOpen: false,
            loading: false, activeIndex: -1, timer: null,
            init() {},
            onInput() {
                clearTimeout(this.timer);
                this.activeIndex = -1;
                if (this.query.length < 2) { this.results = []; this.isOpen = false; return; }
                this.timer = setTimeout(() => { this.fetch(); }, 280);
            },
            async fetch() {
                this.loading = true; this.isOpen = true;
                try {
                    var res  = await window.fetch('/api/search?q=' + encodeURIComponent(this.query), { headers: { 'Accept': 'application/json' } });
                    var data = await res.json();
                    this.results = data.results || [];
                } catch (e) { console.warn('Live search error:', e); this.results = []; }
                finally { this.loading = false; }
            },
            open()  { this.isOpen = true; },
            close() { this.isOpen = false; this.activeIndex = -1; },
            moveDown() { if (this.activeIndex < this.results.length - 1) this.activeIndex++; },
            moveUp()   { if (this.activeIndex > 0) this.activeIndex--; },
            followActive() {
                if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
                    window.location.href = this.results[this.activeIndex].url;
                } else if (this.query.length >= 2) {
                    window.location.href = '{{ route('products.index') }}?search=' + encodeURIComponent(this.query);
                }
            },
        };
    });
});
</script>
