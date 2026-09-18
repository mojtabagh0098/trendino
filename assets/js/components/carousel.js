/**
 * Trendino — carousel.
 *
 * A dependency-free, RTL-aware, scroll-snap based carousel. Instead of
 * transforming a track it drives native horizontal scrolling, which keeps
 * touch/trackpad behaviour perfect on mobile and removes the need for a
 * slider library (smaller payload, no jQuery).
 */
(function (window, document) {
	'use strict';

	var Trendino = window.Trendino;

	if (!Trendino) {
		return;
	}

	function Carousel(root) {
		var options = Trendino.data(root, 'data-trendino-carousel', {});
		var track = root.querySelector('.trendino-track');

		if (!track) {
			return;
		}

		var items = Trendino.all('.trendino-track__item', track);

		if (!items.length) {
			return;
		}

		var prev = root.querySelector('[data-carousel-prev]');
		var next = root.querySelector('[data-carousel-next]');
		var dotsHost = root.querySelector('[data-carousel-dots]');
		var rtl = typeof options.rtl === 'boolean' ? options.rtl : document.dir === 'rtl';
		var autoplayTimer = null;

		/**
		 * The scrollLeft value is negative (or reversed) in RTL depending on the
		 * engine. Normalising it keeps the maths identical in both directions.
		 */
		function normalisedScroll() {
			return Math.abs(track.scrollLeft);
		}

		function maxScroll() {
			return track.scrollWidth - track.clientWidth;
		}

		function pageWidth() {
			var first = items[0];

			if (!first) {
				return track.clientWidth;
			}

			var styles = window.getComputedStyle(track);
			var gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
			var perView = Math.max(1, Math.round(track.clientWidth / (first.offsetWidth + gap)));

			return (first.offsetWidth + gap) * perView;
		}

		function scrollBy(direction) {
			var delta = pageWidth() * direction * (rtl ? -1 : 1);

			track.scrollBy({ left: delta, behavior: 'smooth' });
		}

		function atStart() {
			return normalisedScroll() <= 2;
		}

		function atEnd() {
			return normalisedScroll() >= maxScroll() - 2;
		}

		function updateArrows() {
			if (options.loop) {
				return;
			}

			if (prev) {
				prev.disabled = atStart();
				prev.classList.toggle('is-disabled', atStart());
			}

			if (next) {
				next.disabled = atEnd();
				next.classList.toggle('is-disabled', atEnd());
			}
		}

		function buildDots() {
			if (!dotsHost) {
				return;
			}

			var pages = Math.max(1, Math.ceil(track.scrollWidth / Math.max(1, track.clientWidth)));

			dotsHost.innerHTML = '';

			for (var index = 0; index < pages; index++) {
				var dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'trendino-carousel__dot';
				dot.setAttribute('data-carousel-dot', String(index));
				dot.setAttribute('aria-label', String(index + 1));
				dotsHost.appendChild(dot);
			}

			updateDots();
		}

		function updateDots() {
			if (!dotsHost) {
				return;
			}

			var dots = Trendino.all('[data-carousel-dot]', dotsHost);

			if (!dots.length) {
				return;
			}

			var active = Math.round(normalisedScroll() / Math.max(1, track.clientWidth));

			dots.forEach(function (dot, index) {
				dot.classList.toggle('is-active', index === active);
			});
		}

		function goToPage(index) {
			var target = index * track.clientWidth * (rtl ? -1 : 1);

			track.scrollTo({ left: target, behavior: 'smooth' });
		}

		function startAutoplay() {
			if (!options.autoplay) {
				return;
			}

			stopAutoplay();

			autoplayTimer = window.setInterval(function () {
				if (atEnd()) {
					track.scrollTo({ left: 0, behavior: 'smooth' });
					return;
				}

				scrollBy(1);
			}, options.autoplayDelay || 5000);
		}

		function stopAutoplay() {
			if (autoplayTimer) {
				window.clearInterval(autoplayTimer);
				autoplayTimer = null;
			}
		}

		if (prev) {
			prev.addEventListener('click', function () {
				scrollBy(-1);
				stopAutoplay();
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				scrollBy(1);
				stopAutoplay();
			});
		}

		if (dotsHost) {
			dotsHost.addEventListener('click', function (event) {
				var dot = event.target.closest('[data-carousel-dot]');

				if (!dot) {
					return;
				}

				goToPage(parseInt(dot.getAttribute('data-carousel-dot'), 10) || 0);
				stopAutoplay();
			});
		}

		track.addEventListener(
			'scroll',
			Trendino.debounce(function () {
				updateArrows();
				updateDots();
			}, 80),
			{ passive: true }
		);

		root.addEventListener('mouseenter', stopAutoplay);
		root.addEventListener('mouseleave', startAutoplay);

		// Keyboard support on the track itself.
		track.setAttribute('tabindex', '0');
		track.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowRight') {
				scrollBy(rtl ? -1 : 1);
			} else if (event.key === 'ArrowLeft') {
				scrollBy(rtl ? 1 : -1);
			}
		});

		window.addEventListener(
			'resize',
			Trendino.debounce(function () {
				buildDots();
				updateArrows();
			}, 200)
		);

		buildDots();
		updateArrows();
		startAutoplay();

		root.classList.add('is-ready');
	}

	Trendino.register('[data-trendino-carousel]', Carousel);

	/* ------------------------------------------------------- Hero slider */

	Trendino.register('[data-trendino-slider]', function (root) {
		var options = Trendino.data(root, 'data-trendino-slider', {});
		var slides = Trendino.all('.trendino-hero__slide', root);

		if (slides.length < 1) {
			return;
		}

		var dots = Trendino.all('[data-carousel-dot]', root);
		var prev = root.querySelector('[data-carousel-prev]');
		var next = root.querySelector('[data-carousel-next]');
		var current = 0;
		var timer = null;

		function show(index) {
			current = (index + slides.length) % slides.length;

			slides.forEach(function (slide, i) {
				slide.classList.toggle('is-active', i === current);
				slide.setAttribute('aria-hidden', i === current ? 'false' : 'true');
			});

			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === current);
				dot.setAttribute('aria-current', i === current ? 'true' : 'false');
			});
		}

		function start() {
			if (!options.autoplay || slides.length < 2) {
				return;
			}

			stop();
			timer = window.setInterval(function () {
				show(current + 1);
			}, options.autoplayDelay || 6000);
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		if (prev) {
			prev.addEventListener('click', function () {
				show(current - 1);
				start();
			});
		}

		if (next) {
			next.addEventListener('click', function () {
				show(current + 1);
				start();
			});
		}

		dots.forEach(function (dot, index) {
			dot.addEventListener('click', function () {
				show(index);
				start();
			});
		});

		if (options.pauseOnHover !== false) {
			root.addEventListener('mouseenter', stop);
			root.addEventListener('mouseleave', start);
		}

		// Touch swipe.
		var startX = 0;

		root.addEventListener(
			'touchstart',
			function (event) {
				startX = event.touches[0].clientX;
			},
			{ passive: true }
		);

		root.addEventListener(
			'touchend',
			function (event) {
				var delta = event.changedTouches[0].clientX - startX;

				if (Math.abs(delta) < 40) {
					return;
				}

				var forward = options.rtl ? delta > 0 : delta < 0;
				show(current + (forward ? 1 : -1));
				start();
			},
			{ passive: true }
		);

		show(0);
		start();

		root.classList.add('is-ready');
	});
})(window, document);
