/**
 * Trendino — header behaviours: sticky, live search, mega menu, mobile drawer, mini cart.
 */
(function (window, document) {
	'use strict';

	var Trendino = window.Trendino;

	if (!Trendino) {
		return;
	}

	var config = Trendino.config;

	/* --------------------------------------------------------- Sticky header */

	Trendino.register('[data-trendino-header]', function (header) {
		if (!header.classList.contains('is-sticky')) {
			return;
		}

		var lastY = 0;

		var onScroll = function () {
			var y = window.pageYOffset;

			header.classList.toggle('is-stuck', y > 120);
			header.classList.toggle('is-hidden', y > 400 && y > lastY);

			lastY = y;
		};

		window.addEventListener('scroll', Trendino.debounce(onScroll, 60), { passive: true });
		onScroll();
	});

	/* ------------------------------------------------------ Announcement bar */

	Trendino.register('[data-trendino-announce]', function (bar) {
		var key = 'trendino_announce_' + bar.getAttribute('data-trendino-announce');
		var until = 0;

		try {
			until = parseInt(window.localStorage.getItem(key), 10) || 0;
		} catch (error) {
			until = 0;
		}

		// Still within the dismissal window — leave it hidden.
		if (until > Date.now()) {
			return;
		}

		bar.hidden = false;

		var close = bar.querySelector('[data-trendino-announce-close]');

		if (!close) {
			return;
		}

		close.addEventListener('click', function () {
			bar.hidden = true;

			try {
				window.localStorage.setItem(key, String(Date.now() + 7 * 24 * 60 * 60 * 1000));
			} catch (error) {
				/* private mode — the bar simply returns next visit */
			}
		});
	});

	/* ------------------------------------------------------------ Live search */

	Trendino.register('.trendino-search', function (search) {
		if (search.getAttribute('data-live-search') !== 'yes') {
			return;
		}

		var input = search.querySelector('.trendino-search__input');
		var panel = search.querySelector('.trendino-search__panel');
		var results = search.querySelector('.trendino-search__results');

		if (!input || !panel || !results) {
			return;
		}

		var scope = search.getAttribute('data-scope') || 'product';
		var controller = null;

		function close() {
			panel.hidden = true;
			search.classList.remove('is-open');
		}

		function open() {
			panel.hidden = false;
			search.classList.add('is-open');
		}

		function render(items) {
			if (!items.length) {
				results.innerHTML =
					'<p class="trendino-search__empty">' +
					((config.i18n && config.i18n.noResults) || '—') +
					'</p>';
				return;
			}

			results.innerHTML = items
				.map(function (item) {
					return (
						'<a class="trendino-search__result" href="' +
						item.url +
						'">' +
						(item.image
							? '<img class="trendino-search__result-img" src="' +
							  item.image +
							  '" alt="" loading="lazy" width="48" height="48" />'
							: '<span class="trendino-search__result-img is-placeholder"></span>') +
						'<span class="trendino-search__result-text">' +
						'<span class="trendino-search__result-title">' +
						item.title +
						'</span>' +
						(item.price
							? '<span class="trendino-search__result-price">' + item.price + '</span>'
							: '') +
						'</span></a>'
					);
				})
				.join('');
		}

		var search$ = Trendino.debounce(function () {
			var term = input.value.trim();

			if (term.length < 2) {
				close();
				return;
			}

			open();
			results.innerHTML =
				'<p class="trendino-search__loading">' +
				((config.i18n && config.i18n.loading) || '…') +
				'</p>';

			if (controller) {
				controller.abort();
			}

			controller = new AbortController();

			var body = new FormData();
			body.append('action', 'trendino_search');
			body.append('nonce', config.nonce || '');
			body.append('term', term);
			body.append('scope', scope);

			window
				.fetch(config.ajaxUrl, {
					method: 'POST',
					body: body,
					credentials: 'same-origin',
					signal: controller.signal
				})
				.then(function (response) {
					return response.json();
				})
				.then(function (response) {
					if (response && response.success) {
						render(response.data.items || []);
					}
				})
				.catch(function () {
					/* aborted or offline — keep the last state */
				});
		}, 280);

		input.addEventListener('input', search$);
		input.addEventListener('focus', function () {
			if (input.value.trim().length >= 2) {
				open();
			}
		});

		document.addEventListener('click', function (event) {
			if (!search.contains(event.target)) {
				close();
			}
		});

		input.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				close();
				input.blur();
			}
		});
	});

	/* ------------------------------------------------------------- Mega menu */

	Trendino.register('.trendino-catmenu', function (menu) {
		var trigger = menu.querySelector('.trendino-catmenu__trigger');
		var openOn = menu.getAttribute('data-open-on') || 'hover';

		if (!trigger) {
			return;
		}

		function setOpen(open) {
			menu.classList.toggle('is-open', open);
			trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		if (openOn === 'click') {
			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				setOpen(!menu.classList.contains('is-open'));
			});

			document.addEventListener('click', function (event) {
				if (!menu.contains(event.target)) {
					setOpen(false);
				}
			});
		} else {
			menu.addEventListener('mouseenter', function () {
				setOpen(true);
			});
			menu.addEventListener('mouseleave', function () {
				setOpen(false);
			});
			trigger.addEventListener('focus', function () {
				setOpen(true);
			});
		}

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen(false);
			}
		});
	});

	/* --------------------------------------------------------- Mobile drawer */

	Trendino.register('[data-trendino-drawer]', function (drawer) {
		function setOpen(open) {
			drawer.hidden = !open;
			drawer.classList.toggle('is-open', open);
			document.body.classList.toggle('trendino-no-scroll', open);

			Trendino.all('[data-trendino-drawer-open]').forEach(function (button) {
				button.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
		}

		document.addEventListener('click', function (event) {
			if (event.target.closest('[data-trendino-drawer-open]')) {
				event.preventDefault();
				setOpen(true);
				return;
			}

			if (event.target.closest('[data-trendino-drawer-close]')) {
				event.preventDefault();
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
				setOpen(false);
			}
		});

		// Nested submenu toggles inside the drawer.
		Trendino.all('.trendino-drawer__list .menu-item-has-children > a', drawer).forEach(function (link) {
			var toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = 'trendino-drawer__expand';
			toggle.setAttribute('aria-expanded', 'false');
			toggle.innerHTML = '<span aria-hidden="true">+</span>';

			toggle.addEventListener('click', function () {
				var item = link.parentNode;
				var open = item.classList.toggle('is-open');

				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
				toggle.innerHTML = '<span aria-hidden="true">' + (open ? '−' : '+') + '</span>';
			});

			link.parentNode.insertBefore(toggle, link.nextSibling);
		});
	});

	/* ------------------------------------------------------------- Mini cart */

	Trendino.register('[data-trendino-mini-cart]', function (cart) {
		var wrap = cart.closest('.trendino-header-action-wrap');

		if (!wrap) {
			return;
		}

		var timer = null;

		wrap.addEventListener('mouseenter', function () {
			window.clearTimeout(timer);
			wrap.classList.add('is-open');
		});

		wrap.addEventListener('mouseleave', function () {
			timer = window.setTimeout(function () {
				wrap.classList.remove('is-open');
			}, 180);
		});
	});

	/* ------------------------------------ Desktop menu: flip overflowing submenus */

	Trendino.register('.trendino-menu__submenu', function (submenu) {
		var reposition = function () {
			submenu.classList.remove('is-flipped');

			var box = submenu.getBoundingClientRect();

			if (document.dir === 'rtl' ? box.left < 0 : box.right > window.innerWidth) {
				submenu.classList.add('is-flipped');
			}
		};

		var parent = submenu.parentNode;

		if (parent) {
			parent.addEventListener('mouseenter', reposition);
		}
	});
})(window, document);
