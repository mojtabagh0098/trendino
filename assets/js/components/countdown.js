/**
 * Trendino — deal countdown.
 *
 * Reads the end timestamp (seconds, site time) from data-trendino-countdown.
 * With data-daily="1" the timer restarts at the next midnight once it expires,
 * which is what an "amazing offers of the day" panel needs.
 */
(function (window) {
	'use strict';

	var Trendino = window.Trendino;

	if (!Trendino) {
		return;
	}

	function pad(value) {
		return (value < 10 ? '0' : '') + value;
	}

	Trendino.register('[data-trendino-countdown]', function (root) {
		var end = parseInt(root.getAttribute('data-trendino-countdown'), 10) * 1000;
		var daily = root.getAttribute('data-daily') === '1';

		if (!end || isNaN(end)) {
			return;
		}

		var fields = {};

		Trendino.all('[data-unit]', root).forEach(function (element) {
			fields[element.getAttribute('data-unit')] = element;
		});

		function nextMidnight() {
			var date = new Date();
			date.setHours(24, 0, 0, 0);

			return date.getTime();
		}

		function tick() {
			var remaining = end - Date.now();

			if (remaining <= 0) {
				if (daily) {
					end = nextMidnight();
					return;
				}

				remaining = 0;
				root.classList.add('is-expired');
			}

			var total = Math.floor(remaining / 1000);
			var days = Math.floor(total / 86400);
			var hours = Math.floor((total % 86400) / 3600);
			var minutes = Math.floor((total % 3600) / 60);
			var seconds = total % 60;

			if (!fields.days) {
				hours += days * 24;
			}

			var values = {
				days: days,
				hours: hours,
				minutes: minutes,
				seconds: seconds
			};

			Object.keys(fields).forEach(function (unit) {
				fields[unit].textContent = Trendino.digits(pad(values[unit]));
			});
		}

		tick();
		window.setInterval(tick, 1000);
	});
})(window);
