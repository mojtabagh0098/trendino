(function () {
    'use strict';
    const data = document.getElementById('tno-gallery-data');
    if (!data) return;
    const images = JSON.parse(data.dataset.images || '[]');
    const history = JSON.parse(data.dataset.history || '[]');
    const product = JSON.parse(data.dataset.compare || '{}');
    const gallery = document.getElementById('tno-lightbox-modal');
    const image = document.getElementById('tno-lightbox-img');
    const filmstrip = gallery.querySelector('.tno-gallery-filmstrip');
    const count = document.getElementById('tno-gallery-count');
    let index = 0, zoom = 1, shownImages = images, lastFocus = null;

    function open(modal, trigger) {
        if (!modal) return;
        lastFocus = trigger || document.activeElement;
        modal.hidden = false;
        document.body.classList.add('tno-action-modal-open');
        const focus = modal.querySelector('.js-close-modal');
        if (focus) focus.focus();
    }
    function close(modal) {
        if (!modal || modal.hidden) return;
        modal.hidden = true;
        if (modal === gallery && document.fullscreenElement === gallery && document.exitFullscreen) document.exitFullscreen().catch(() => {});
        modal.querySelectorAll('video').forEach((video) => video.pause());
        if (!document.querySelector('.tno-action-backdrop:not([hidden]), .tno-full-gallery:not([hidden])')) document.body.classList.remove('tno-action-modal-open');
        if (lastFocus && document.contains(lastFocus)) lastFocus.focus();
    }
    function showImage(next) {
        if (!shownImages.length) return;
        index = (next + shownImages.length) % shownImages.length;
        zoom = 1; image.style.transform = '';
        image.src = shownImages[index].full; image.alt = shownImages[index].alt || '';
        count.textContent = `${index + 1} / ${shownImages.length}`;
        filmstrip.querySelectorAll('button').forEach((button, i) => {
            button.classList.toggle('is-active', i === index);
            button.setAttribute('aria-current', i === index ? 'true' : 'false');
        });
        filmstrip.querySelector('.is-active')?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        gallery.querySelectorAll('.tno-gallery-arrow').forEach((button) => { button.hidden = shownImages.length < 2; });
    }
    function renderFilmstrip() {
        filmstrip.replaceChildren();
        shownImages.forEach((item, i) => {
            const button = document.createElement('button'); button.type = 'button';
            button.setAttribute('aria-label', `تصویر ${i + 1}`);
            const thumb = document.createElement('img'); thumb.src = item.thumb; thumb.alt = '';
            button.append(thumb); button.addEventListener('click', () => showImage(i)); filmstrip.append(button);
        });
    }
    function openGallery(trigger, reviewImage) {
        shownImages = reviewImage ? [{ full: reviewImage.src, thumb: reviewImage.src, alt: reviewImage.alt }] : images;
        renderFilmstrip();
        const selected = document.querySelector('.tno-thumb-item.is-active');
        showImage(reviewImage ? 0 : Number(trigger.dataset.index ?? selected?.dataset.index ?? 0));
        open(gallery, trigger);
    }
    document.addEventListener('click', (event) => {
        const closeButton = event.target.closest('.js-close-modal');
        if (closeButton) { close(closeButton.closest('.tno-modal-backdrop')); return; }
        const backdrop = event.target.closest('.tno-modal-backdrop');
        if (backdrop && event.target === backdrop) { close(backdrop); return; }
        const trigger = event.target.closest('.js-open-lightbox, .js-open-gallery-modal');
        if (trigger) { openGallery(trigger, trigger.matches('.tno-review-thumb') ? trigger : null); return; }
        const report = event.target.closest('.js-open-price-modal');
        if (report) { open(document.getElementById('tno-price-modal'), report); return; }
        const share = event.target.closest('.js-share-product');
        if (share) { open(document.getElementById('tno-share-modal'), share); return; }
        const chart = event.target.closest('.js-open-price-chart');
        if (chart) { renderChart(); open(document.getElementById('tno-chart-modal'), chart); return; }
        const compare = event.target.closest('.js-open-compare');
        if (compare) { addCompare(); renderCompare(); open(document.getElementById('tno-compare-modal'), compare); return; }
        const video = event.target.closest('.js-open-video');
        if (video && !video.disabled) { open(document.getElementById('tno-video-modal'), video); return; }
        const catalog = event.target.closest('.js-open-catalog');
        if (catalog && data.dataset.catalog) window.open(data.dataset.catalog, '_blank', 'noopener,noreferrer');
    });
    gallery.querySelector('.js-gallery-prev').addEventListener('click', () => showImage(index - 1));
    gallery.querySelector('.js-gallery-next').addEventListener('click', () => showImage(index + 1));
    gallery.querySelector('.js-zoom-in').addEventListener('click', () => { zoom = Math.min(3, zoom + .25); image.style.transform = `scale(${zoom})`; });
    gallery.querySelector('.js-zoom-out').addEventListener('click', () => { zoom = Math.max(1, zoom - .25); image.style.transform = `scale(${zoom})`; });
    gallery.querySelector('.tno-gallery-fullscreen').addEventListener('click', async () => {
        try { if (document.fullscreenElement) await document.exitFullscreen(); else if (gallery.requestFullscreen) await gallery.requestFullscreen(); } catch (_) { /* Fullscreen can be denied by the browser. */ }
    });
    document.addEventListener('keydown', (event) => {
        const modal = document.querySelector('.tno-action-backdrop:not([hidden]), .tno-full-gallery:not([hidden]), #tno-price-modal:not([hidden])');
        if (!modal) return;
        if (event.key === 'Escape') { event.preventDefault(); close(modal); }
        if (modal === gallery && event.key === 'ArrowLeft') showImage(index - 1);
        if (modal === gallery && event.key === 'ArrowRight') showImage(index + 1);
        if (event.key === 'Tab') {
            const focusable = Array.from(modal.querySelectorAll('button:not([disabled]):not([hidden]), a[href], input:not([disabled]), video[controls]')).filter((el) => el.getClientRects().length);
            if (!focusable.length) return;
            const first = focusable[0], last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });

    const url = encodeURIComponent(window.location.href), title = encodeURIComponent(document.title);
    const shareUrls = { telegram: `https://t.me/share/url?url=${url}&text=${title}`, facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`, x: `https://twitter.com/intent/tweet?url=${url}&text=${title}` };
    document.querySelectorAll('.js-share-link').forEach((link) => { link.href = shareUrls[link.dataset.network]; });
    document.querySelector('.js-copy-link').addEventListener('click', async () => {
        const status = document.querySelector('.tno-share-status');
        try { await navigator.clipboard.writeText(window.location.href); status.textContent = 'لینک محصول کپی شد.'; }
        catch (_) { status.textContent = 'کپی خودکار ممکن نیست؛ آدرس صفحه را از مرورگر کپی کنید.'; }
    });

    function renderChart() {
        const box = document.getElementById('tno-price-chart'); box.replaceChildren();
        const rows = history.filter((row) => Number.isFinite(Number(row.regular)) && Number.isFinite(Number(row.price)) && Number(row.time) > 0).slice(-100);
        if (!rows.length) { box.textContent = 'هنوز تاریخچه قیمتی برای این محصول ثبت نشده است. تغییرات قیمت پس از ذخیره محصول نمایش داده می‌شود.'; box.classList.add('is-empty'); return; }
        box.classList.remove('is-empty');
        const ns = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(ns, 'svg'); svg.setAttribute('viewBox', '0 0 720 380'); svg.setAttribute('role', 'img'); svg.setAttribute('aria-label', 'تاریخچه قیمت محصول');
        const max = Math.max(1, ...rows.flatMap((row) => [Number(row.regular), Number(row.price)]));
        const top = 15, bottom = 325, left = 80, right = 705;
        const make = (tag, attrs, text) => { const el = document.createElementNS(ns, tag); Object.entries(attrs).forEach(([key, val]) => el.setAttribute(key, val)); if (text !== undefined) el.textContent = text; svg.appendChild(el); return el; };
        const fmt = new Intl.NumberFormat('fa-IR', { maximumFractionDigits: 0 });
        for (let i = 0; i <= 5; i++) {
            const y = top + (bottom - top) * i / 5;
            make('line', { x1: left, x2: right, y1: y, y2: y, stroke: '#dedede' });
            make('text', { x: left - 9, y: y + 4, 'text-anchor': 'end', class: 'tno-chart-label' }, fmt.format(max * (5 - i) / 5));
        }
        const x = (i) => rows.length === 1 ? (left + right) / 2 : left + (right - left) * i / (rows.length - 1);
        const y = (value) => bottom - Number(value) / max * (bottom - top);
        ['regular', 'price'].forEach((key) => {
            const color = key === 'regular' ? '#12aeba' : '#f13d57';
            if (rows.length > 1) make('polyline', { points: rows.map((row, i) => `${x(i)},${y(row[key])}`).join(' '), fill: 'none', stroke: color, 'stroke-width': 4, 'vector-effect': 'non-scaling-stroke' });
            rows.forEach((row, i) => { const circle = make('circle', { cx: x(i), cy: y(row[key]), r: rows.length === 1 ? 5 : 2.5, fill: color }); const tip = document.createElementNS(ns, 'title'); tip.textContent = `${new Date(Number(row.time) * 1000).toLocaleString('fa-IR')} — ${fmt.format(row[key])} ${data.dataset.currency}`; circle.append(tip); });
        });
        const step = Math.max(1, Math.ceil(rows.length / 5));
        rows.forEach((row, i) => { if (i % step === 0 || i === rows.length - 1) make('text', { x: x(i), y: 350, 'text-anchor': 'middle', class: 'tno-chart-label' }, new Date(Number(row.time) * 1000).toLocaleDateString('fa-IR')); });
        box.append(svg);
        if (rows.length === 1) { const hint = document.createElement('p'); hint.textContent = 'یک قیمت ثبت شده است؛ با تغییرات بعدی، نمودار کامل‌تر می‌شود.'; box.append(hint); }
    }

    const compareKey = 'tno_compare_products';
    function getCompare() { try { const items = JSON.parse(localStorage.getItem(compareKey) || '[]'); return Array.isArray(items) ? items : []; } catch (_) { return []; } }
    function addCompare() { const items = getCompare().filter((item) => item && item.id !== product.id); items.push(product); try { localStorage.setItem(compareKey, JSON.stringify(items.slice(-4))); } catch (_) {} }
    function renderCompare() {
        const list = document.querySelector('.tno-compare-list'); list.replaceChildren();
        getCompare().forEach((item) => {
            const card = document.createElement('div'); card.className = 'tno-compare-item';
            const img = document.createElement('img'); img.src = item.image || ''; img.alt = '';
            const name = document.createElement('strong'); name.textContent = item.title || '';
            const price = document.createElement('span'); price.textContent = item.price || '';
            card.append(img, name, price);
            if (Array.isArray(item.features)) item.features.forEach((feature) => { const line = document.createElement('small'); line.textContent = `${feature.label || ''}: ${feature.value || ''}`; card.append(line); });
            list.append(card);
        });
    }
    document.querySelector('.tno-compare-clear').addEventListener('click', () => { try { localStorage.removeItem(compareKey); } catch (_) {} renderCompare(); });
    if (data.dataset.cartAdded === '1') open(document.getElementById('tno-cart-success-modal'), document.querySelector('.single_add_to_cart_button'));
    const form = document.querySelector('.js-tno-cart-form');
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('button[type=submit]'), error = form.querySelector('.tno-cart-error');
        button.disabled = true; error.hidden = true;
        const body = new FormData(); body.append('product_id', tnoPdpData.productId); body.append('quantity', '1');
        try {
            const response = await fetch(tnoPdpData.cartUrl, { method: 'POST', body, credentials: 'same-origin' });
            const result = await response.json();
            if (!response.ok || result.error || !result.fragments) throw new Error('افزودن به سبد خرید انجام نشد. لطفاً دوباره تلاش کنید.');
            if (window.jQuery) {
                window.jQuery.each(result.fragments, (selector, html) => window.jQuery(selector).replaceWith(html));
                window.jQuery(document.body).trigger('added_to_cart', [result.fragments, result.cart_hash, window.jQuery(button)]);
            }
            open(document.getElementById('tno-cart-success-modal'), button);
        } catch (err) { error.textContent = err.message || 'ارتباط قطع شد؛ پیش از تلاش دوباره سبد خرید را بررسی کنید.'; error.hidden = false; }
        finally { button.disabled = false; }
    });
})();
