/**
 * Trendino — product tabs.
 */
(function (window, document) {
	'use strict';

	var Trendino = window.Trendino;

	if (!Trendino) {
		return;
	}

	Trendino.register('[data-trendino-tabs]', function (root) {
		var buttons = Trendino.all('.trendino-tabs__button', root);
		var panels = Trendino.all('.trendino-tabs__panel', root);

		if (!buttons.length || !panels.length) {
			return;
		}

		function activate(index) {
			buttons.forEach(function (button, i) {
				var active = i === index;

				button.classList.toggle('is-active', active);
				button.setAttribute('aria-selected', active ? 'true' : 'false');
				button.setAttribute('tabindex', active ? '0' : '-1');
			});

			panels.forEach(function (panel, i) {
				var active = i === index;

				panel.classList.toggle('is-active', active);
				panel.hidden = !active;
			});

			// Carousels inside a hidden panel measure 0 — refresh once visible.
			window.dispatchEvent(new Event('resize'));
		}

		buttons.forEach(function (button, index) {
			button.addEventListener('click', function () {
				activate(index);
			});

			button.addEventListener('keydown', function (event) {
				if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
					return;
				}

				event.preventDefault();

				var rtl = document.dir === 'rtl';
				var forward = event.key === 'ArrowRight' ? !rtl : rtl;
				var target = (index + (forward ? 1 : -1) + buttons.length) % buttons.length;

				buttons[target].focus();
				activate(target);
			});
		});

		var initial = buttons.findIndex(function (button) {
			return button.classList.contains('is-active');
		});

		activate(initial > -1 ? initial : 0);
	});
})(window, document);
