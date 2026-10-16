/**
 * Trendino — newsletter AJAX form.
 */
(function (window) {
	'use strict';

	var Trendino = window.Trendino;

	if (!Trendino) {
		return;
	}

	var config = Trendino.config;

	Trendino.register('[data-trendino-newsletter]', function (form) {
		var input = form.querySelector('.trendino-newsletter__input');
		var submit = form.querySelector('.trendino-newsletter__submit');
		var message = form.parentNode
			? form.parentNode.querySelector('.trendino-newsletter__message')
			: null;

		if (!input) {
			return;
		}

		function setMessage(text, state) {
			if (!message) {
				Trendino.toast(text, state);
				return;
			}

			message.textContent = text;
			message.className = 'trendino-newsletter__message is-' + state;
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var email = input.value.trim();

			if (!email) {
				return;
			}

			if (submit) {
				submit.classList.add('is-loading');
				submit.disabled = true;
			}

			setMessage((config.i18n && config.i18n.loading) || '…', 'loading');

			Trendino.post('trendino_newsletter', { email: email })
				.then(function (response) {
					if (submit) {
						submit.classList.remove('is-loading');
						submit.disabled = false;
					}

					if (response && response.success) {
						setMessage(response.data.message, 'success');
						form.reset();
					} else {
						setMessage(
							(response && response.data && response.data.message) ||
								(config.i18n && config.i18n.error),
							'error'
						);
					}
				})
				.catch(function () {
					if (submit) {
						submit.classList.remove('is-loading');
						submit.disabled = false;
					}

					setMessage((config.i18n && config.i18n.error) || 'Error', 'error');
				});
		});
	});
})(window);
