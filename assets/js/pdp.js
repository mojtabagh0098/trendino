/**
 * Trendino Single Product Page (PDP) Interactive Controller
 * Pure Vanilla JavaScript (Zero jQuery dependency)
 */
(function () {
    'use strict';

    const mainImg = document.getElementById('tno-main-product-img');

    // 1. Thumbnail Gallery Switcher & Lightbox
    document.querySelectorAll('.js-switch-thumb').forEach((btn) => {
        btn.addEventListener('click', function () {
            const largeUrl = this.getAttribute('data-large');
            if (largeUrl && mainImg) {
                mainImg.src = largeUrl;
            }
            document.querySelectorAll('.tno-thumb-item').forEach((el) => el.classList.remove('is-active'));
            this.classList.add('is-active');
        });
    });

    const lightboxModal = document.getElementById('tno-lightbox-modal');
    const lightboxImg = document.getElementById('tno-lightbox-img');

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('js-open-lightbox')) {
            lightboxImg.src = e.target.src;
            lightboxModal.hidden = false;
        }
        if (e.target.closest('.js-open-gallery-modal') && mainImg) {
            lightboxImg.src = mainImg.src;
            lightboxModal.hidden = false;
        }
    });

    // 2. Variation Swatch Selector
    document.querySelectorAll('.js-select-swatch').forEach((swatch) => {
        swatch.addEventListener('click', function () {
            const wrapper = this.closest('.tno-variation-selector');
            wrapper.querySelectorAll('.js-select-swatch').forEach((s) => s.classList.remove('is-selected'));
            this.classList.add('is-selected');
            const labelEl = wrapper.querySelector('.js-selected-var-label');
            if (labelEl) {
                labelEl.textContent = this.getAttribute('data-label');
            }
        });
    });

    // 3. Expand / Collapse Content & Specifications
    document.querySelectorAll('.js-toggle-collapse').forEach((btn) => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const box = document.getElementById(targetId);
            if (!box) return;
            const isCollapsed = box.classList.toggle('is-collapsed');
            this.querySelector('span').textContent = isCollapsed ? 'مشاهده بیشتر' : 'بستن';
        });
    });

    // 4. Sticky Tabs ScrollSpy
    const tabLinks = document.querySelectorAll('.tno-sticky-tabs .tno-tab-pill');
    const sections = ['#tno-sec-review-content', '#tno-sec-specs', '#tno-sec-reviews', '#tno-sec-qa']
        .map((sel) => document.querySelector(sel))
        .filter(Boolean);

    window.addEventListener('scroll', () => {
        let currentId = '';
        sections.forEach((sec) => {
            if (window.scrollY >= sec.offsetTop - 140) {
                currentId = '#' + sec.id;
            }
        });
        if (currentId) {
            tabLinks.forEach((link) => {
                link.classList.toggle('is-active', link.getAttribute('href') === currentId);
            });
        }
    });

    

    // 10. AJAX Sort Reviews & Questions
    document.querySelectorAll('.js-sort-reviews').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.js-sort-reviews').forEach((b) => b.classList.remove('is-active'));
            this.classList.add('is-active');

            const fd = new FormData();
            fd.append('action', 'tno_filter_comments');
            fd.append('nonce', tnoPdpData.nonce);
            fd.append('product_id', tnoPdpData.productId);
            fd.append('sort', this.getAttribute('data-sort'));

            fetch(tnoPdpData.ajaxUrl, { method: 'POST', body: fd })
                .then((r) => r.json())
                .then((res) => {
                    if (res.success) {
                        document.getElementById('tno-reviews-list-container').innerHTML = res.data.html;
                    }
                });
        });
    });

    document.querySelectorAll('.js-sort-questions').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.js-sort-questions').forEach((b) => b.classList.remove('is-active'));
            this.classList.add('is-active');

            const fd = new FormData();
            fd.append('action', 'tno_filter_questions');
            fd.append('nonce', tnoPdpData.nonce);
            fd.append('product_id', tnoPdpData.productId);
            fd.append('sort', this.getAttribute('data-sort'));

            fetch(tnoPdpData.ajaxUrl, { method: 'POST', body: fd })
                .then((r) => r.json())
                .then((res) => {
                    if (res.success) {
                        document.getElementById('tno-qa-list-container').innerHTML = res.data.html;
                    }
                });
        });
    });

    // 11. Wishlist & Share & Price Report
    document.querySelector('.js-toggle-wishlist')?.addEventListener('click', function () {
        const fd = new FormData();
        fd.append('action', 'tno_toggle_wishlist');
        fd.append('nonce', tnoPdpData.nonce);
        fd.append('product_id', tnoPdpData.productId);

        fetch(tnoPdpData.ajaxUrl, { method: 'POST', body: fd })
            .then((r) => r.json())
            .then((res) => {
                if (res.success) {
                    this.classList.toggle('is-active', res.data.active);
                }
            });
    });

    document.querySelector('.js-share-product')?.addEventListener('click', () => {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href);
            alert('لینک محصول در کلیپ‌بورد کپی شد.');
        }
    });

    document.getElementById('tno-price-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'tno_price_report');
        fd.append('nonce', tnoPdpData.nonce);
        fd.append('product_id', tnoPdpData.productId);

        fetch(tnoPdpData.ajaxUrl, { method: 'POST', body: fd })
            .then((r) => r.json())
            .then((res) => {
                alert(res.data?.message || 'ثبت شد.');
                if (res.success) {
                    closeModals();
                    this.reset();
                }
            });
    });
})();