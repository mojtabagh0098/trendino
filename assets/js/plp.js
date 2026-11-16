/**
 * Trendino PLP Interactive Script (Pure Vanilla JS)
 * Handles: AJAX Filtering, Dual Range Price Slider, Accordions, Pagination, and SEO Expand
 * Path: assets/js/plp.js
 */
document.addEventListener('DOMContentLoaded', function () {
    const plpWrapper = document.querySelector('.tno-plp-wrapper');

    // تبدیل اعداد انگلیسی به فارسی
    const toPersianNum = function (num) {
        const fa = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return String(num).replace(/\d/g, d => fa[d]);
    };

    const formatPriceFa = function (num) {
        return toPersianNum(Number(num).toLocaleString('en-US'));
    };

    // =========================================
    // ۱. دکمه مشاهده بیشتر باکس سئو پایین صفحه
    // =========================================
    const seoBtn = document.getElementById('tno-seo-toggle-btn');
    const seoContent = document.getElementById('tno-seo-content');
    if (seoBtn && seoContent) {
        seoBtn.addEventListener('click', function () {
            const isCollapsed = seoContent.classList.toggle('is-collapsed');
            const textSpan = seoBtn.querySelector('.tno-seo-btn-text');
            if (textSpan) {
                textSpan.textContent = isCollapsed ? 'مشاهده بیشتر' : 'بستن توضیحات';
            }
        });
    }

    if (!plpWrapper) return;

    const maxLimit = parseInt(plpWrapper.getAttribute('data-max-limit') || '100000000', 10);
    const perPage = parseInt(plpWrapper.getAttribute('data-per-page') || '24', 10);
    const gridEl = document.getElementById('tno-plp-products-grid');
    const paginationWrap = document.getElementById('tno-plp-pagination-wrap');
    const chipsContainer = document.getElementById('tno-applied-filters-container');
    const countNumEl = document.getElementById('tno-plp-count-num');
    const sidebar = document.getElementById('tno-plp-sidebar');

    let currentOrderby = 'date';
    const activePill = plpWrapper.querySelector('.tno-sort-pill.is-active');
    if (activePill) {
        currentOrderby = activePill.getAttribute('data-orderby');
    }
    let currentPage = 1;
    let priceTimeout = null;

    // =========================================
    // ۲. آکاردئون‌های سایدبار و دراور موبایل
    // =========================================
    plpWrapper.addEventListener('click', function (e) {
        const accHead = e.target.closest('.tno-filter-block__head');
        if (accHead) {
            accHead.closest('.tno-filter-block').classList.toggle('is-open');
        }

        if (e.target.closest('.tno-mobile-filter-trigger') && sidebar) {
            sidebar.classList.add('is-mobile-open');
        }
        if (e.target.closest('.tno-close-mobile-sidebar') && sidebar) {
            sidebar.classList.remove('is-mobile-open');
        }
    });

    // =========================================
    // ۳. اسلایدر دوطرفه محدوده قیمت
    // =========================================
    const rangeMin = document.getElementById('tno-range-min');
    const rangeMax = document.getElementById('tno-range-max');
    const fillBar = document.getElementById('tno-slider-fill');
    const minDisplay = document.getElementById('tno-price-min-display');
    const maxDisplay = document.getElementById('tno-price-max-display');

    const updateSliderVisual = function () {
        if (!rangeMin || !rangeMax || !fillBar) return;
        let minVal = parseInt(rangeMin.value, 10);
        let maxVal = parseInt(rangeMax.value, 10);

        if (minVal > maxVal - 50000) {
            minVal = maxVal - 50000;
            rangeMin.value = minVal;
        }

        const pctMin = (minVal / maxLimit) * 100;
        const pctMax = (maxVal / maxLimit) * 100;

        // در چیدمان RTL، سمت راست 0% و سمت چپ 100% است
        fillBar.style.right = pctMin + '%';
        fillBar.style.left = (100 - pctMax) + '%';

        if (minDisplay) minDisplay.value = formatPriceFa(minVal);
        if (maxDisplay) maxDisplay.value = formatPriceFa(maxVal);
    };

    if (rangeMin && rangeMax) {
        updateSliderVisual();
        [rangeMin, rangeMax].forEach(input => {
            input.addEventListener('input', function () {
                updateSliderVisual();
                clearTimeout(priceTimeout);
                priceTimeout = setTimeout(function () {
                    currentPage = 1;
                    executeAjaxFilter();
                }, 400);
            });
        });
    }

    // =========================================
    // ۴. جمع‌آوری وضعیت فیلترها و ارسال درخواست AJAX
    // =========================================
    const executeAjaxFilter = function () {
        if (!gridEl || typeof tnoPlpData === 'undefined') return;

        gridEl.classList.add('is-loading');

        const formData = new FormData();
        formData.append('action', 'tno_filter_plp');
        formData.append('nonce', tnoPlpData.nonce);
        formData.append('orderby', currentOrderby);
        formData.append('paged', currentPage);
        formData.append('per_page', perPage);

        // دسته‌بندی‌ها
        plpWrapper.querySelectorAll('input[name="tno_cats[]"]:checked').forEach(el => {
            formData.append('tno_cats[]', el.value);
        });

        // برندها
        plpWrapper.querySelectorAll('input[name="tno_brands[]"]:checked').forEach(el => {
            formData.append('tno_brands[]', el.value);
        });

        // رنگ‌ها
        plpWrapper.querySelectorAll('input[name="tno_colors[]"]:checked').forEach(el => {
            formData.append('tno_colors[]', el.value);
        });

        // قیمت
        if (rangeMin && rangeMax) {
            formData.append('min_price', rangeMin.value);
            formData.append('max_price', rangeMax.value);
        }

        // سوئیچ‌ها
        const inStockEl = plpWrapper.querySelector('input[name="in_stock"]:checked');
        const onSaleEl = plpWrapper.querySelector('input[name="on_sale"]:checked');
        const fastSendEl = plpWrapper.querySelector('input[name="fast_send"]:checked');

        if (inStockEl) formData.append('in_stock', '1');
        if (onSaleEl) formData.append('on_sale', '1');
        if (fastSendEl) formData.append('fast_send', '1');

        fetch(tnoPlpData.ajaxUrl, {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    gridEl.innerHTML = res.data.products_html;
                    if (paginationWrap) paginationWrap.innerHTML = res.data.pagination_html;
                    if (chipsContainer) chipsContainer.innerHTML = res.data.chips_html;
                    if (countNumEl) countNumEl.textContent = res.data.count;
                }
            })
            .finally(() => {
                gridEl.classList.remove('is-loading');
            });
    };

    // تغییر چک‌باکس‌ها و سوئیچ‌ها
    plpWrapper.addEventListener('change', function (e) {
        if (e.target.matches('input[type="checkbox"]')) {
            if (e.target.closest('.tno-color-item')) {
                e.target.closest('.tno-color-item').classList.toggle('is-selected', e.target.checked);
            }
            currentPage = 1;
            executeAjaxFilter();
        }
    });

    // کلیک روی تب‌های مرتب‌سازی، تگ‌های حذف فیلتر و صفحه‌بندی
    plpWrapper.addEventListener('click', function (e) {
        // ۱. تب مرتب‌سازی
        const sortBtn = e.target.closest('.tno-sort-pill');
        if (sortBtn) {
            e.preventDefault();
            plpWrapper.querySelectorAll('.tno-sort-pill').forEach(b => b.classList.remove('is-active'));
            sortBtn.classList.add('is-active');
            currentOrderby = sortBtn.getAttribute('data-orderby');
            currentPage = 1;
            executeAjaxFilter();
            return;
        }

        // ۲. حذف تکی فیلتر (تگ‌های آبی بالا)
        const chipBtn = e.target.closest('.tno-applied-chip');
        if (chipBtn) {
            e.preventDefault();
            const type = chipBtn.getAttribute('data-filter-type');
            const val = chipBtn.getAttribute('data-filter-val');

            if (type === 'cat') {
                const target = plpWrapper.querySelector(`input[name="tno_cats[]"][value="${val}"]`);
                if (target) target.checked = false;
            } else if (type === 'brand') {
                const target = plpWrapper.querySelector(`input[name="tno_brands[]"][value="${val}"]`);
                if (target) target.checked = false;
            } else if (type === 'color') {
                const target = plpWrapper.querySelector(`input[name="tno_colors[]"][value="${val}"]`);
                if (target) {
                    target.checked = false;
                    target.closest('.tno-color-item')?.classList.remove('is-selected');
                }
            } else if (type === 'price' && rangeMin && rangeMax) {
                rangeMin.value = 0;
                rangeMax.value = maxLimit;
                updateSliderVisual();
            } else if (type === 'toggle') {
                const target = plpWrapper.querySelector(`input[name="${val}"]`);
                if (target) target.checked = false;
            }

            currentPage = 1;
            executeAjaxFilter();
            return;
        }

        // ۳. حذف همه فیلترها
        if (e.target.closest('.tno-clear-all-filters')) {
            e.preventDefault();
            plpWrapper.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
            plpWrapper.querySelectorAll('.tno-color-item').forEach(ci => ci.classList.remove('is-selected'));
            if (rangeMin && rangeMax) {
                rangeMin.value = 0;
                rangeMax.value = maxLimit;
                updateSliderVisual();
            }
            currentPage = 1;
            executeAjaxFilter();
            return;
        }

        // ۴. صفحه‌بندی ایجکس
        const pageLink = e.target.closest('.tno-plp-pagination a.page-numbers');
        if (pageLink) {
            e.preventDefault();
            const href = pageLink.getAttribute('href');
            const match = href.match(/paged=(\d+)|page\/(\d+)/);
            if (match) {
                currentPage = parseInt(match[1] || match[2], 10);
            } else {
                currentPage = 1;
            }
            executeAjaxFilter();
            window.scrollTo({ top: plpWrapper.offsetTop - 80, behavior: 'smooth' });
        }
    });
});