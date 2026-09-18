/**
 * Trendino — core front-end runtime.
 *
 * Exposes a tiny shared namespace (window.Trendino) that the component scripts
 * hook into, plus the behaviours that are needed on every page:
 * wishlist toggles, scroll-to-top, SEO text collapse, footer accordions.
 *
 * No dependencies. ES5-compatible syntax where it costs nothing.
 */
(function (window, document) {
	'use strict';

	var config = window.trendinoConfig || {};

	var Trendino = {
		config: config,

		/**
		 * querySelectorAll as a real array.
		 */
		all: function (selector, context) {
			return Array.prototype.slice.call((context || document).querySelectorAll(selector));
		},

		/**
		 * Parse a JSON data attribute safely.
		 */
		data: function (element, attribute, fallback) {
			var raw = element.getAttribute(attribute);

			if (!raw) {
				return fallback || {};
			}

			try {
				return JSON.parse(raw);
			} catch (error) {
				return fallback || {};
			}
		},

		/**
		 * Debounce helper.
		 */
		debounce: function (fn, wait) {
			var timer;

			return function () {
				var context = this;
				var args = arguments;

				window.clearTimeout(timer);
				timer = window.setTimeout(function () {
					fn.apply(context, args);
				}, wait || 150);
			};
		},

		/**
		 * POST to admin-ajax with the theme nonce.
		 */
		post: function (action, payload) {
			var body = new FormData();

			body.append('action', action);
			body.append('nonce', config.nonce || '');

			Object.keys(payload || {}).forEach(function (key) {
				body.append(key, payload[key]);
			});

			return window
				.fetch(config.ajaxUrl, {
					method: 'POST',
					body: body,
					credentials: 'same-origin'
				})
				.then(function (response) {
					return response.json();
				});
		},

		/**
		 * Register an initialiser that runs now and again when Elementor
		 * re-renders a widget in the editor.
		 */
		register: function (selector, callback) {
			var run = function (root) {
				Trendino.all(selector, root || document).forEach(function (element) {
					if (element.trendinoInit) {
						return;
					}

					element.trendinoInit = true;
					callback(element);
				});
			};

			run();

			Trendino.hooks.push(run);
		},

		hooks: [],

		/**
		 * Re-run every registered initialiser inside a scope.
		 */
		refresh: function (root) {
			Trendino.hooks.forEach(function (hook) {
				hook(root);
			});
		},

		/**
		 * Convert latin digits to Persian when the option is on.
		 */
		digits: function (value) {
			if (!config.isRtl) {
				return String(value);
			}

			var persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

			return String(value).replace(/\d/g, function (digit) {
				return persian[digit];
			});
		},

		/**
		 * Minimal toast notification.
		 */
		toast: function (message, type) {
			var host = document.querySelector('.trendino-toasts');

			if (!host) {
				host = document.createElement('div');
				host.className = 'trendino-toasts';
				document.body.appendChild(host);
			}

			var toast = document.createElement('div');
			toast.className = 'trendino-toast is-' + (type || 'success');
			toast.setAttribute('role', 'status');
			toast.textContent = message;
			host.appendChild(toast);

			window.setTimeout(function () {
				toast.classList.add('is-leaving');
				window.setTimeout(function () {
					toast.remove();
				}, 300);
			}, 3200);
		}
	};

	window.Trendino = Trendino;

	/* ------------------------------------------------------------ Wishlist */

	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-trendino-wishlist]');

		if (!button) {
			return;
		}

		event.preventDefault();

		var productId = button.getAttribute('data-trendino-wishlist');

		button.classList.add('is-loading');

		Trendino.post('trendino_wishlist', { product_id: productId })
			.then(function (response) {
				button.classList.remove('is-loading');

				if (!response || !response.success) {
					Trendino.toast((config.i18n && config.i18n.error) || 'Error', 'error');
					return;
				}

				button.classList.toggle('is-active', response.data.active);

				Trendino.all('[data-trendino-count="wishlist"]').forEach(function (counter) {
					counter.textContent = Trendino.digits(response.data.count);
				});

				Trendino.toast(response.data.message);
			})
			.catch(function () {
				button.classList.remove('is-loading');
				Trendino.toast((config.i18n && config.i18n.error) || 'Error', 'error');
			});
	});

	/* ------------------------------------------------------- Scroll to top */

	var scrollTopButtons = Trendino.all('[data-trendino-scroll-top]');

	if (scrollTopButtons.length) {
		var toggleScrollTop = function () {
			var visible = window.pageYOffset > 400;

			scrollTopButtons.forEach(function (button) {
				if (button.classList.contains('is-floating')) {
					button.hidden = !visible;
				}
			});
		};

		window.addEventListener('scroll', Trendino.debounce(toggleScrollTop, 120), { passive: true });
		toggleScrollTop();

		document.addEventListener('click', function (event) {
			if (!event.target.closest('[data-trendino-scroll-top]')) {
				return;
			}

			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	/* ------------------------------------------------- Collapsible SEO text */

	Trendino.register('[data-trendino-seo-text]', function (element) {
		var toggle = element.querySelector('.trendino-seo-text__toggle');

		if (!toggle) {
			return;
		}

		toggle.addEventListener('click', function () {
			var collapsed = element.classList.toggle('is-collapsed');

			toggle.textContent = collapsed
				? toggle.getAttribute('data-more')
				: toggle.getAttribute('data-less');

			toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
		});
	});

	/* ------------------------------------------------- Footer accordions */

	Trendino.register('[data-trendino-footer-toggle]', function (button) {
		button.addEventListener('click', function () {
			var wrapper = button.closest('.trendino-footer-links');

			if (!wrapper || !wrapper.classList.contains('is-collapsible')) {
				return;
			}

			var open = wrapper.classList.toggle('is-open');
			button.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	});

	/* ---------------------------------------------- Horizontal scrollers */

	Trendino.register('[data-trendino-scroller]', function (track) {
		var shell = track.closest('.trendino-carousel');

		if (!shell) {
			return;
		}

		shell.classList.add('is-scroller-ready');
	});

	/* ------------------------------------------------------ Elementor hook */

	window.addEventListener('elementor/frontend/init', function () {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			Trendino.refresh($scope && $scope[0] ? $scope[0] : document);
		});
	});
})(window, document);
