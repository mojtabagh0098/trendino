(() => {
	'use strict';

	const cfg = window.tnoCheckoutConfig || { ajaxUrl: '', nonce: '', i18n: {} };

	/* ----------------------------------------------------------------------
	   Thank-You Page Interactions (Coupon Copy & Invoice Print Fallback)
	   ---------------------------------------------------------------------- */
	const ticketBtn = document.querySelector('[data-copy-coupon]');
	if (ticketBtn) {
		const statusEl = document.getElementById('tno-ty-copy-status');
		ticketBtn.addEventListener('click', async () => {
			const code = ticketBtn.getAttribute('data-copy-coupon') || '';
			if (!code) {
				return;
			}
			try {
				await navigator.clipboard.writeText(code);
				if (statusEl) {
					statusEl.textContent = cfg.i18n.copied || 'کد تخفیف کپی شد!';
					setTimeout(() => {
						statusEl.textContent = '';
					}, 3000);
				}
			} catch (_) {
				const temp = document.createElement('input');
				temp.value = code;
				document.body.appendChild(temp);
				temp.select();
				document.execCommand('copy');
				document.body.removeChild(temp);
				if (statusEl) {
					statusEl.textContent = cfg.i18n.copied || 'کد تخفیف کپی شد!';
				}
			}
		});
	}

	const printBtn = document.querySelector('[data-action="print-invoice"]');
	if (printBtn) {
		printBtn.addEventListener('click', (e) => {
			e.preventDefault();
			window.print();
		});
	}

	/* ----------------------------------------------------------------------
	   Multi-Step Checkout Logic
	   ---------------------------------------------------------------------- */
	const app = document.getElementById('tno-checkout-app');
	if (!app) {
		return;
	}

	const stepInput = document.getElementById('tno_active_step');
	const noticesBox = document.getElementById('tno-chk-notices');

	const requiredFieldIds = [
		'billing_first_name',
		'billing_last_name',
		'billing_phone',
		'billing_state',
		'billing_city',
		'billing_address_1',
	];

	function triggerWcUpdate() {
		if (window.jQuery) {
			window.jQuery(document.body).trigger('update_checkout');
		}
	}

	function isAddressStepValid() {
		return requiredFieldIds.every((id) => {
			const el = document.getElementById(id);
			return el && el.value.trim() !== '';
		});
	}

	function syncNextButtonState() {
		const nextBtn = document.getElementById('tno-chk-go-payment');
		if (!nextBtn) {
			return;
		}
		nextBtn.classList.toggle('is-ready', isAddressStepValid());
	}

	function showStepError(message) {
		if (!noticesBox) {
			return;
		}
		noticesBox.innerHTML = `<div class="woocommerce-error" role="alert">${message}</div>`;
		noticesBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
	}

	function clearStepError() {
		if (noticesBox) {
			noticesBox.innerHTML = '';
		}
	}

	function setActiveStep(step) {
		if (step === 'payment' && !isAddressStepValid()) {
			showStepError(cfg.i18n.fillRequired || 'لطفاً فیلدهای الزامی را تکمیل کنید.');
			return;
		}

		clearStepError();
		app.dataset.step = step;
		if (stepInput) {
			stepInput.value = step;
		}

		app.querySelectorAll('[data-pane]').forEach((pane) => {
			const active = pane.dataset.pane === step;
			pane.hidden = !active;
			pane.classList.toggle('is-active', active);
		});

		app.querySelectorAll('[data-step-nav]').forEach((nav) => {
			nav.classList.toggle('is-active', nav.dataset.stepNav === step);
		});

		window.scrollTo({ top: 0, behavior: 'smooth' });
	}

	/* ----------------------------------------------------------------------
	   Sync Saved Address Selection into Billing Inputs
	   ---------------------------------------------------------------------- */
	function applySavedAddressRadio(radio) {
		if (!radio) {
			return;
		}
		const stateEl = document.getElementById('billing_state');
		const cityEl = document.getElementById('billing_city');
		const postEl = document.getElementById('billing_postcode');
		const addrEl = document.getElementById('billing_address_1');

		if (stateEl && radio.dataset.state) stateEl.value = radio.dataset.state;
		if (cityEl && radio.dataset.city) cityEl.value = radio.dataset.city;
		if (postEl && radio.dataset.postcode) postEl.value = radio.dataset.postcode;
		if (addrEl && radio.dataset.address) addrEl.value = radio.dataset.address;

		syncNextButtonState();
		triggerWcUpdate();
	}

	/* ----------------------------------------------------------------------
	   Delegated Event Handlers (Fragment-Safe)
	   ---------------------------------------------------------------------- */
	app.addEventListener('click', (e) => {
		const gotoBtn = e.target.closest('[data-goto-step]');
		if (gotoBtn) {
			setActiveStep(gotoBtn.dataset.gotoStep);
			return;
		}

		if (e.target.closest('#tno-chk-go-payment')) {
			setActiveStep('payment');
			return;
		}

		const removeCouponBtn = e.target.closest('[data-remove-coupon]');
		if (removeCouponBtn) {
			handleCouponRequest('remove', removeCouponBtn.dataset.removeCoupon);
			return;
		}

		if (e.target.closest('#tno-chk-coupon-apply')) {
			const input = document.getElementById('tno-chk-coupon-input');
			handleCouponRequest('apply', input ? input.value.trim() : '');
		}
	});

	app.addEventListener('change', (e) => {
		const target = e.target;

		if (target.matches('input[type="radio"]')) {
			const groupName = target.name;
			app.querySelectorAll(`input[type="radio"][name="${CSS.escape(groupName)}"]`).forEach((input) => {
				const wrapper = input.closest('.tno-chk-pill-radio, .tno-chk-payment-item');
				if (wrapper) {
					wrapper.classList.toggle('is-selected', input.checked);
				}
			});

			if (groupName === 'tno_saved_address') {
				applySavedAddressRadio(target);
			} else if (groupName.startsWith('shipping_method')) {
				triggerWcUpdate();
			}
		}

		if (requiredFieldIds.includes(target.id)) {
			// Clear saved-address highlight when user manually edits address fields
			if (['billing_state', 'billing_city', 'billing_postcode', 'billing_address_1'].includes(target.id)) {
				app.querySelectorAll('input[name="tno_saved_address"]').forEach((r) => {
					r.checked = false;
					const w = r.closest('.tno-chk-pill-radio');
					if (w) w.classList.remove('is-selected');
				});
			}
			syncNextButtonState();
		}
	});

	app.addEventListener('input', (e) => {
		if (requiredFieldIds.includes(e.target.id)) {
			syncNextButtonState();
		}
	});

	/* ----------------------------------------------------------------------
	   AJAX Coupon Apply / Remove
	   ---------------------------------------------------------------------- */
	async function handleCouponRequest(mode, code) {
		const feedbackEl = document.getElementById('tno-chk-coupon-feedback');
		const applyBtn = document.getElementById('tno-chk-coupon-apply');
		const inputEl = document.getElementById('tno-chk-coupon-input');
		const chipsWrap = document.getElementById('tno-chk-applied-coupons');

		if (!code) {
			if (feedbackEl) {
				feedbackEl.className = 'tno-chk-coupon-feedback is-error';
				feedbackEl.textContent = cfg.i18n.couponEmpty || 'لطفاً کد تخفیف را وارد کنید.';
			}
			return;
		}

		if (applyBtn) applyBtn.disabled = true;

		try {
			const body = new URLSearchParams({
				action: 'tno_checkout_coupon',
				nonce: cfg.nonce,
				mode,
				coupon_code: code,
			});

			const res = await fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
			});
			const json = await res.json();

			if (!json.success) {
				if (feedbackEl) {
					feedbackEl.className = 'tno-chk-coupon-feedback is-error';
					feedbackEl.textContent = json.data?.message || cfg.i18n.errorGeneric;
				}
				return;
			}

			if (feedbackEl) {
				feedbackEl.className = 'tno-chk-coupon-feedback is-success';
				feedbackEl.textContent = json.data?.message || '';
			}

			if (mode === 'apply' && inputEl) {
				inputEl.value = '';
			}

			if (chipsWrap && Array.isArray(json.data?.coupons)) {
				chipsWrap.innerHTML = json.data.coupons
					.map(
						(c) => `<span class="tno-chk-coupon-chip"><span>${c}</span><button type="button" data-remove-coupon="${c}" aria-label="حذف کد تخفیف">×</button></span>`
					)
					.join('');
			}

			triggerWcUpdate();
		} catch (_) {
			if (feedbackEl) {
				feedbackEl.className = 'tno-chk-coupon-feedback is-error';
				feedbackEl.textContent = cfg.i18n.errorGeneric;
			}
		} finally {
			if (applyBtn) applyBtn.disabled = false;
		}
	}

	// Re-sync CTA ready state after WooCommerce replaces `#tno-chk-summary` fragment
	if (window.jQuery) {
		window.jQuery(document.body).on('updated_checkout', () => {
			syncNextButtonState();
		});
	}

	// Hydrate initial saved address if pre-selected
	const preselectedAddr = app.querySelector('input[name="tno_saved_address"]:checked');
	if (preselectedAddr && !document.getElementById('billing_address_1').value.trim()) {
		applySavedAddressRadio(preselectedAddr);
	}

	syncNextButtonState();
})();