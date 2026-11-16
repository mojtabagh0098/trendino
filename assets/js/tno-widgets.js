jQuery( window ).on( 'elementor/frontend/init', function() {

    // ==========================================
    // ۱. شمارنده زمان معکوس
    // ==========================================
    var TNO_Countdown = function( $scope, $ ) {
        var $countdown = $scope.find('.tno-countdown');
        if ( ! $countdown.length ) return;

        var endTime = $countdown.data('end-time');
        if ( ! endTime ) return;

        var countDownDate = new Date(endTime).getTime();

        var x = setInterval(function() {
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
    elementorFrontend.hooks.addAction( 'frontend/element_ready/tno_box_title.default', TNO_Countdown );

    // ==========================================
    // ۲. ویجت هیرو
    // ==========================================
    elementorFrontend.hooks.addAction( 'frontend/element_ready/tno_hero.default', function( $scope, $ ) {
        var $heroCarousel = $scope.find('.tno-hero-swiper');
        if ( ! $heroCarousel.length ) return;

        var loopAttr = $heroCarousel.data('loop');
        var isLoop = loopAttr === true || loopAttr === "true";

        var swiperOptions = {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: isLoop,
        };

        var $nextEl = $scope.find('.swiper-button-next');
        var $prevEl = $scope.find('.swiper-button-prev');
        if ( $nextEl.length && $prevEl.length ) {
            swiperOptions.navigation = {
                nextEl: $nextEl[0],
                prevEl: $prevEl[0],
            };
        }

        var $paginationEl = $scope.find('.swiper-pagination');
        if ( $paginationEl.length ) {
            swiperOptions.pagination = {
                el: $paginationEl[0],
                type: 'bullets',
                clickable: true,
            };
        }

        new Swiper( $heroCarousel[0], swiperOptions );
    });

    // ==========================================
    // ۳. تب‌های محصولات (سازگاری با ویرایشگر المنتور)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/trendino_products.default', function() {
        initTrendinoTabs();
        initTrendinoBestSellersTabs();
    });

    // ==========================================
    // ۴. پیشنهاد شگفت‌انگیز (اسلایدر)
    // ==========================================
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_flash_deal.default', function($scope) {
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
    elementorFrontend.hooks.addAction('frontend/element_ready/tno_articles.default', function($scope) {
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

});

// ==========================================
// توابع عمومی و اجرای خارج از ویرایشگر المنتور (Front-End)
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    initTrendinoTabs();
    initTrendinoBestSellersTabs();
});

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