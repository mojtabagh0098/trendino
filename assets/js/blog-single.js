(() => {
	'use strict';

	const cfg = window.tnoBlogConfig || { ajaxUrl: '', nonce: '', i18n: {} };

	// Copy Article Link Button
	document.addEventListener('click', async (e) => {
		const copyBtn = e.target.closest('[data-copy-url]');
		if (!copyBtn) return;

		const url = copyBtn.dataset.copyUrl || window.location.href;
		const labelEl = copyBtn.querySelector('[data-copy-label]');

		try {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				await navigator.clipboard.writeText(url);
			} else {
				const tmp = document.createElement('input');
				tmp.value = url;
				document.body.appendChild(tmp);
				tmp.select();
				document.execCommand('copy');
				tmp.remove();
			}

			if (labelEl) {
				const prev = labelEl.textContent;
				labelEl.textContent = cfg.i18n.copied || 'کپی شد ✓';
				copyBtn.classList.add('is-copied');
				setTimeout(() => {
					labelEl.textContent = prev;
					copyBtn.classList.remove('is-copied');
				}, 2000);
			}
		} catch (_) {
			// Ignore clipboard failures
		}
	});

	// Blog Newsletter AJAX Form
	const newsletterForm = document.getElementById('tno-blog-newsletter-form');
	if (newsletterForm) {
		const emailInput = document.getElementById('tno-blog-newsletter-email');
		const submitBtn = document.getElementById('tno-blog-newsletter-submit');
		const feedbackEl = document.getElementById('tno-blog-newsletter-feedback');

		newsletterForm.addEventListener('submit', async (e) => {
			e.preventDefault();
			const email = emailInput ? emailInput.value.trim() : '';

			if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
				if (feedbackEl) {
					feedbackEl.hidden = false;
					feedbackEl.className = 'tno-single-newsletter__feedback is-error';
					feedbackEl.textContent = cfg.i18n.invalidEmail || 'لطفاً یک ایمیل معتبر وارد کنید.';
				}
				return;
			}

			if (submitBtn) {
				submitBtn.disabled = true;
				submitBtn.textContent = cfg.i18n.subscribing || 'در حال ثبت...';
			}

			try {
				const body = new URLSearchParams({
					action: 'tno_blog_newsletter',
					nonce: cfg.nonce,
					email,
				});

				const res = await fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				});
				const json = await res.json();

				if (feedbackEl) {
					feedbackEl.hidden = false;
					feedbackEl.className = 'tno-single-newsletter__feedback ' + (json.success ? 'is-success' : 'is-error');
					feedbackEl.textContent = json.data?.message || cfg.i18n.errorGeneric;
				}
				if (json.success && emailInput) {
					emailInput.value = '';
				}
			} catch (_) {
				if (feedbackEl) {
					feedbackEl.hidden = false;
					feedbackEl.className = 'tno-single-newsletter__feedback is-error';
					feedbackEl.textContent = cfg.i18n.errorGeneric;
				}
			} finally {
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = cfg.i18n.subscribeBtn || 'عضویت در خبرنامه';
				}
			}
		});
	}
})();
