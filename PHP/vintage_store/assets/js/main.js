// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile nav toggle ──────────────────────────
    const toggle = document.getElementById('navToggle');
    const nav    = document.querySelector('.nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('open');
            const spans = toggle.querySelectorAll('span');
            if (nav.classList.contains('open')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity   = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
            } else {
                spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
            }
        });
    }

    // ── Auto-dismiss flash after 4s ────────────────
    const flash = document.querySelector('.flash');
    if (flash) setTimeout(() => flash.remove(), 4000);

    // ── Product search/filter (client-side instant) ──
    const searchInput = document.getElementById('searchInput');
    const eraFilter   = document.getElementById('eraFilter');
    const sizeFilter  = document.getElementById('sizeFilter');
    const cards       = document.querySelectorAll('.product-card[data-name]');

    function filterProducts() {
        if (!cards.length) return;
        const q    = (searchInput?.value || '').toLowerCase();
        const era  = (eraFilter?.value  || '').toLowerCase();
        const size = (sizeFilter?.value || '').toLowerCase();
        let visible = 0;

        cards.forEach(card => {
            const name    = (card.dataset.name    || '').toLowerCase();
            const cardEra = (card.dataset.era     || '').toLowerCase();
            const cardSz  = (card.dataset.size    || '').toLowerCase();

            const matchQ    = !q    || name.includes(q);
            const matchEra  = !era  || cardEra === era;
            const matchSize = !size || cardSz  === size;

            const show = matchQ && matchEra && matchSize;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        // Show/hide "no results" message
        let noRes = document.getElementById('noResults');
        if (!noRes) {
            noRes = document.createElement('div');
            noRes.id = 'noResults';
            noRes.className = 'no-results';
            noRes.innerHTML = `
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
                <p>No items match your search.</p>
            `;
            cards[0]?.closest('.product-grid')?.appendChild(noRes);
        }
        noRes.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput?.addEventListener('input', filterProducts);
    eraFilter?.addEventListener('change', filterProducts);
    sizeFilter?.addEventListener('change', filterProducts);

    // ── Cart quantity controls ─────────────────────
    document.querySelectorAll('.qty-control').forEach(ctrl => {
        const minus  = ctrl.querySelector('[data-action="minus"]');
        const plus   = ctrl.querySelector('[data-action="plus"]');
        const display = ctrl.querySelector('span');
        const input   = ctrl.querySelector('input[type="hidden"]');
        const itemId  = ctrl.dataset.id;

        function setQty(n) {
            n = Math.max(1, n);
            display.textContent = n;
            if (input) input.value = n;
            // AJAX update
            fetch(`/cart.php?action=update&id=${itemId}&qty=${n}`, { method: 'POST' })
                .then(r => r.json())
                .then(d => {
                    if (d.total !== undefined) {
                        const totEl = document.getElementById('cartTotal');
                        if (totEl) totEl.textContent = '$' + d.total.toFixed(2);
                    }
                    if (d.badge !== undefined) {
                        document.querySelectorAll('.badge').forEach(b => {
                            b.textContent = d.badge;
                            b.style.display = d.badge > 0 ? '' : 'none';
                        });
                    }
                })
                .catch(() => {});
        }

        minus?.addEventListener('click', () => setQty(parseInt(display.textContent) - 1));
        plus?.addEventListener('click',  () => setQty(parseInt(display.textContent) + 1));
    });

    // ── Confirm delete actions ─────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // ── Password strength hint ─────────────────────
    const pwInput = document.getElementById('password');
    const pwHint  = document.getElementById('pwStrength');
    if (pwInput && pwHint) {
        pwInput.addEventListener('input', () => {
            const v = pwInput.value;
            let strength = 0;
            if (v.length >= 8) strength++;
            if (/[A-Z]/.test(v)) strength++;
            if (/[0-9]/.test(v)) strength++;
            if (/[^A-Za-z0-9]/.test(v)) strength++;
            const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
            const colors = ['', '#B5512A', '#d4a017', '#6b9e5e', '#3B4A3A'];
            pwHint.textContent = v.length ? labels[strength] : '';
            pwHint.style.color = colors[strength];
        });
    }

    // ── Smooth scroll for anchor links ────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

});
