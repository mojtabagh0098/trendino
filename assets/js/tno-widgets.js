// ==========================================
// ۰. سوالات متداول دسته‌بندی‌شده (مستقل از jQuery)
// ==========================================
(function () {
    'use strict';

    if (window.__tnoFaqBound) return;
    window.__tnoFaqBound = true;

    document.addEventListener('click', function (e) {
        /* ۱) تغییر تب دسته‌بندی */
        const tabBtn = e.target.closest('.tno-faq__tab');
        if (tabBtn) {
            e.preventDefault();
            const root = tabBtn.closest('.tno-faq');
            if (!root) return;

            const selectedCat = (tabBtn.dataset.cat || 'all').trim();
            const openFirst = root.dataset.openFirst === 'true';

            root.querySelectorAll('.tno-faq__tab').forEach(function (btn) {
                const isActive = btn === tabBtn;
                btn.classList.toggle('is-active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });

            let firstOpened = false;
            root.querySelectorAll('.tno-faq__item').forEach(function (item) {
                const cats = (item.dataset.cats || '')
                    .split(',')
                    .map(function (c) { return c.trim(); })
                    .filter(Boolean);

                const isMatch = selectedCat === 'all' || cats.includes(selectedCat);
                item.hidden = !isMatch;
                item.classList.toggle('is-hidden', !isMatch);

                const shouldOpen = isMatch && openFirst && !firstOpened;
                if (shouldOpen) firstOpened = true;

                item.classList.toggle('is-open', shouldOpen);
                const trigger = item.querySelector('.tno-faq__trigger');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                }
            });
            return;
        }

        /* ۲) باز و بسته کردن آکاردئون سوال */
        const trigger = e.target.closest('.tno-faq__trigger');
        if (trigger) {
            e.preventDefault();
            const item = trigger.closest('.tno-faq__item');
            const root = trigger.closest('.tno-faq');
            if (!item || !root) return;

            const willOpen = !item.classList.contains('is-open');
            const singleOpen = root.dataset.singleOpen === 'true';

            if (singleOpen && willOpen) {
                root.querySelectorAll('.tno-faq__item.is-open').forEach(function (openItem) {
                    if (openItem === item) return;
                    openItem.classList.remove('is-open');
                    const openTrigger = openItem.querySelector('.tno-faq__trigger');
                    if (openTrigger) {
                        openTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            item.classList.toggle('is-open', willOpen);
            trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        }
    });
})();
jQuery(window).on('elementor/frontend/init', function () {

    // ==========================================
    // ۱. شمارنده زمان معکوس
    // ==========================================
    var TNO_Countdown = function ($scope, $) {
        var $countdown = $scope.find('.tno-countdown');
        if (!$countdown.length) return;

        var endTime = $countdown.data('end-time');
        if (!endTime) return;

        var countDownDate = new Date(endTime).getTime();

        var x = setInterval(function () {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            if (distance < 0) {
                clearInterval(x);
                $countdown.html('<span class="expired">زمان به پایان رسیده است</span>');
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $countdown.find('.days').text(days < 10 ? "0" + days : days);
            $countdown.find('.hours').text(hours < 10 ? "0" + hours : hours);
            $countdown.find('.minutes').text(minutes < 10 ? "0" + minutes : minutes);
            $countdown.find('.seconds').text(seconds < 10 ? "0" + seconds : seconds);
        }, 1000);
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_box_title.default', TNO_Countdown);

    // ==========================================
    // ۲. ویجت هیرو
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_hero.default', function ($scope, $) {
        var $heroCarousel = $scope.find('.tno-hero-swiper');
        if (!$heroCarousel.length) return;

        var loopAttr = $heroCarousel.data('loop');
        var isLoop = loopAttr === true || loopAttr === "true";

        var swiperOptions = {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: isLoop,
        };

        var $nextEl = $scope.find('.swiper-button-next');
        var $prevEl = $scope.find('.swiper-button-prev');
        if ($nextEl.length && $prevEl.length) {
            swiperOptions.navigation = {
                nextEl: $nextEl[0],
                prevEl: $prevEl[0],
            };
        }

        var $paginationEl = $scope.find('.swiper-pagination');
        if ($paginationEl.length) {
            swiperOptions.pagination = {
                el: $paginationEl[0],
                type: 'bullets',
                clickable: true,
            };
        }

        new Swiper($heroCarousel[0], swiperOptions);
    });

    // ==========================================
    // ۳. تب‌های محصولات (سازگاری با ویرایشگر المنتور)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/trendino_products.default', function () {
        initTrendinoTabs();
        initTrendinoBestSellersTabs();
    });

    // ==========================================
    // ۴. پیشنهاد شگفت‌انگیز (اسلایدر)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_flash_deal.default', function ($scope) {
        const swiperElement = $scope[0].querySelector('.tno-flash-swiper');

        if (swiperElement) {
            const settings = JSON.parse(swiperElement.getAttribute('data-settings'));
            const wrapperId = swiperElement.closest('.tno-flash-deal-wrapper').id;

            const swiperOptions = {
                slidesPerView: settings.breakpoints['768'] ? settings.breakpoints['768'].slidesPerView : 2,
                spaceBetween: settings.spaceBetween,
                loop: settings.loop,
                breakpoints: settings.breakpoints
            };

            if (settings.autoplay) {
                swiperOptions.autoplay = settings.autoplay;
            }

            const prevBtn = document.querySelector(`#${wrapperId} .tno-swiper-prev`);
            const nextBtn = document.querySelector(`#${wrapperId} .tno-swiper-next`);
            if (prevBtn && nextBtn) {
                swiperOptions.navigation = {
                    nextEl: nextBtn,
                    prevEl: prevBtn,
                };
            }

            const paginationEl = document.querySelector(`#${wrapperId} .swiper-pagination`);
            if (paginationEl) {
                swiperOptions.pagination = {
                    el: paginationEl,
                    clickable: true,
                };
            }

            new Swiper(swiperElement, swiperOptions);
        }
    });

    // ==========================================
    // ۵. مقالات (اسلایدر)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_articles.default', function ($scope) {
        const swiperElement = $scope[0].querySelector('.tno-articles-swiper');

        if (swiperElement) {
            const settings = JSON.parse(swiperElement.getAttribute('data-settings'));
            const wrapperId = swiperElement.closest('.tno-articles-wrapper').id;

            const swiperOptions = {
                slidesPerView: settings.breakpoints['768'] ? settings.breakpoints['768'].slidesPerView : 1,
                spaceBetween: settings.spaceBetween,
                loop: settings.loop,
                breakpoints: settings.breakpoints
            };

            if (settings.autoplay) {
                swiperOptions.autoplay = settings.autoplay;
            }

            const prevBtn = document.querySelector(`#${wrapperId} .tno-swiper-prev`);
            const nextBtn = document.querySelector(`#${wrapperId} .tno-swiper-next`);
            if (prevBtn && nextBtn) {
                swiperOptions.navigation = {
                    nextEl: nextBtn,
                    prevEl: prevBtn,
                };
            }

            const paginationEl = document.querySelector(`#${wrapperId} .swiper-pagination`);
            if (paginationEl) {
                swiperOptions.pagination = {
                    el: paginationEl,
                    clickable: true,
                };
            }

            new Swiper(swiperElement, swiperOptions);
        }
    });

    // ==========================================
    // ۶. نظرات مشتریان درباره ما (کاروسل)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_about_testimonials.default', function ($scope) {
        initAboutTestimonialsCarousel($scope[0]);
    });

});

// ==========================================
// توابع عمومی و اجرای خارج از ویرایشگر المنتور (Front-End)
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    initTrendinoTabs();
    initTrendinoBestSellersTabs();
    initAboutTestimonialsCarousel(document);
});

function initAboutTestimonialsCarousel(root) {
    const containers = (root || document).querySelectorAll('[data-tno-testi-carousel]');
    containers.forEach((carousel) => {
        if (carousel.dataset.tnoTestiBound === '1') return;
        carousel.dataset.tnoTestiBound = '1';

        const track = carousel.querySelector('[data-testi-track]');
        const slides = track ? Array.from(track.querySelectorAll('.tno-about-testi__slide')) : [];
        const prevBtn = carousel.querySelector('[data-testi-prev]');
        const nextBtn = carousel.querySelector('[data-testi-next]');

        if (!track || slides.length === 0) return;

        let index = 0;

        function getVisibleCount() {
            if (window.innerWidth <= 767) return 1;
            if (window.innerWidth <= 1024) return 2;
            return 3;
        }

        function update() {
            const visible = getVisibleCount();
            const maxIndex = Math.max(0, slides.length - visible);
            if (index > maxIndex) index = maxIndex;
            if (index < 0) index = 0;

            const isRtl = getComputedStyle(carousel).direction === 'rtl';
            const stepPct = 100 / visible;
            const offset = index * stepPct;
            track.style.transform = `translateX(${isRtl ? offset : -offset}%)`;

            if (prevBtn) prevBtn.disabled = (index <= 0);
            if (nextBtn) nextBtn.disabled = (index >= maxIndex);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const visible = getVisibleCount();
                const maxIndex = Math.max(0, slides.length - visible);
                index = index > 0 ? index - 1 : maxIndex;
                update();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const visible = getVisibleCount();
                const maxIndex = Math.max(0, slides.length - visible);
                index = index < maxIndex ? index + 1 : 0;
                update();
            });
        }

        window.addEventListener('resize', update);
        update();
    });
}

function initTrendinoTabs() {
    const tabButtons = document.querySelectorAll('.tr-tab-btn');
    tabButtons.forEach(btn => {
        btn.removeEventListener('click', tabClickHandler);
        btn.addEventListener('click', tabClickHandler);
    });
}

function tabClickHandler(e) {
    const btn = e.currentTarget;
    const targetId = btn.getAttribute('data-target');
    const wrapper = btn.closest('.trendino-widget-wrapper');

    wrapper.querySelectorAll('.tr-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    wrapper.querySelectorAll('.tr-tab-pane').forEach(pane => pane.classList.remove('active'));
    wrapper.querySelector(`#${targetId}`).classList.add('active');
}

function initTrendinoBestSellersTabs() {
    const tabButtons = document.querySelectorAll('.tno-bestsellers-wrapper .tno-tab-btn');
    tabButtons.forEach(btn => {
        btn.removeEventListener('click', handleBestSellerTabClick);
        btn.addEventListener('click', handleBestSellerTabClick);
    });
}

function handleBestSellerTabClick(e) {
    const btn = e.currentTarget;
    const targetId = btn.getAttribute('data-target');
    const wrapper = btn.closest('.tno-bestsellers-wrapper');

    wrapper.querySelectorAll('.tno-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    wrapper.querySelectorAll('.tno-tab-pane').forEach(pane => pane.classList.remove('active'));
    const targetPane = wrapper.querySelector(`#${targetId}`);
    if (targetPane) {
        targetPane.classList.add('active');
    }
}