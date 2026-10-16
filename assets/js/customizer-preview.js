/**
 * Trendino — Customizer live preview for the colour tokens.
 *
 * Every colour setting maps 1:1 to a --trendino-{slug} custom property, so the
 * preview only has to rewrite that property on :root.
 */
(function (wp) {
	'use strict';

	if (!wp || !wp.customize) {
		return;
	}

	var tokens = [
		'primary',
		'primary-dark',
		'primary-soft',
		'secondary',
		'secondary-dark',
		'secondary-soft',
		'accent',
		'success',
		'warning',
		'text',
		'muted',
		'border',
		'background',
		'surface',
		'footer-bg',
		'footer-text'
	];

	tokens.forEach(function (token) {
		var setting = 'trendino_options[color_' + token.replace(/-/g, '_') + ']';

		wp.customize(setting, function (value) {
			value.bind(function (newValue) {
				document.documentElement.style.setProperty('--trendino-' + token, newValue);
			});
		});
	});

	var lengths = {
		container_width: '--trendino-container',
		base_font_size: '--trendino-font-size',
		base_radius: '--trendino-radius',
		card_radius: '--trendino-card-radius'
	};

	Object.keys(lengths).forEach(function (key) {
		wp.customize('trendino_options[' + key + ']', function (value) {
			value.bind(function (newValue) {
				document.documentElement.style.setProperty(
					lengths[key],
					parseInt(newValue, 10) + 'px'
				);
			});
		});
	});
})(window.wp);
