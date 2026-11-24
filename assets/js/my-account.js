(() => {
	'use strict';

	const cfg = window.tnoAccountConfig || { ajaxUrl: '', nonce: '', i18n: {} };

	/* ----------------------------------------------------------------------
	   Orders Filter & Accordion
	   ---------------------------------------------------------------------- */
	const ordersApp = document.getElementById('tno-orders-app');
	if (ordersApp) {
		const filterBtns = ordersApp.querySelectorAll('[data-order-filter]');
		const orderCards = ordersApp.querySelectorAll('[data-order-bucket]');
		const emptyBox = document.getElementById('tno-orders-empty');

		function applyOrderFilter(bucket) {
			let visibleCount = 0;

			filterBtns.forEach((btn) => {
				const active = btn.dataset.orderFilter === bucket;
				btn.classList.toggle('is-active', active);
				btn.setAttribute('aria-selected', active ? 'true' : 'false');
			});

			orderCards.forEach((card) => {
				const match = bucket === 'all' || card.dataset.orderBucket === bucket;
				card.hidden = !match;
				if (match) {
					visibleCount += 1;
				}
			});

			if (emptyBox) {
				emptyBox.hidden = visibleCount > 0;
			}

			const url = new URL(window.location.href);
			if (bucket === 'all') {
				url.searchParams.delete('status');
			} else {
				url.searchParams.set('status', bucket);
			}
			window.history.replaceState({}, '', url.toString());
		}

		ordersApp.addEventListener('click', (e) => {
			const filterBtn = e.target.closest('[data-order-filter]');
			if (filterBtn) {
				applyOrderFilter(filterBtn.dataset.orderFilter);
				return;
			}

			const toggleBtn = e.target.closest('[data-toggle-order]');
			if (toggleBtn) {
				const card = toggleBtn.closest('.tno-ord-card');
				const body = card ? card.querySelector('.tno-ord-card-body') : null;
				if (!card || !body) {
					return;
				}
				const isOpen = card.classList.toggle('is-open');
				body.hidden = !isOpen;
				toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			}
		});

		applyOrderFilter(ordersApp.dataset.activeFilter || 'all');
	}

	/* ----------------------------------------------------------------------
	   Invoice Print Fallback
	   ---------------------------------------------------------------------- */
	document.addEventListener('click', (e) => {
		const printBtn = e.target.closest('[data-action="print-invoice"]');
		if (printBtn) {
			e.preventDefault();
			window.print();
		}
	});

	/* ----------------------------------------------------------------------
	   Shared Modal Close Helpers
	   ---------------------------------------------------------------------- */
	function openModal(el) {
		if (!el) return;
		el.hidden = false;
		document.body.style.overflow = 'hidden';
	}

	function closeModal(el) {
		if (!el) return;
		el.hidden = true;
		document.body.style.overflow = '';
	}

	document.addEventListener('click', (e) => {
		const closeBtn = e.target.closest('[data-close-modal]');
		if (closeBtn) {
			closeModal(document.getElementById(closeBtn.dataset.closeModal));
			return;
		}
		if (e.target.classList.contains('tno-acc-modal-backdrop')) {
			closeModal(e.target);
		}
	});

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape') {
			document.querySelectorAll('.tno-acc-modal-backdrop:not([hidden])').forEach(closeModal);
		}
	});

	/* ----------------------------------------------------------------------
	   Order Details -> Product Review Modal
	   ---------------------------------------------------------------------- */
	const reviewModal = document.getElementById('tno-review-modal');
	const reviewForm = document.getElementById('tno-review-form');

	if (reviewModal && reviewForm) {
		const starButtons = reviewModal.querySelectorAll('.tno-rev-star');
		const ratingInput = document.getElementById('tno-rev-rating');
		const feedbackEl = document.getElementById('tno-rev-feedback');
		const submitBtn = document.getElementById('tno-rev-submit');

		function paintStars(value) {
			starButtons.forEach((btn) => {
				btn.classList.toggle('is-filled', Number(btn.dataset.star) <= value);
			});
		}

		starButtons.forEach((btn) => {
			btn.addEventListener('click', () => {
				const val = Number(btn.dataset.star);
				ratingInput.value = String(val);
				paintStars(val);
			});
			btn.addEventListener('mouseenter', () => paintStars(Number(btn.dataset.star)));
		});

		document.getElementById('tno-rev-stars').addEventListener('mouseleave', () => {
			paintStars(Number(ratingInput.value));
		});

		document.addEventListener('click', (e) => {
			const trigger = e.target.closest('[data-open-review]');
			if (!trigger) return;

			document.getElementById('tno-rev-product-id').value = trigger.dataset.productId || '';
			document.getElementById('tno-rev-order-id').value = trigger.dataset.orderId || '';
			document.getElementById('tno-rev-product-title').textContent = trigger.dataset.productTitle || '';
			document.getElementById('tno-rev-product-img').src = trigger.dataset.productImage || '';
			document.getElementById('tno-rev-comment').value = '';
			ratingInput.value = '0';
			paintStars(0);
			feedbackEl.textContent = '';
			feedbackEl.className = 'tno-modal-feedback';

			openModal(reviewModal);
		});

		reviewForm.addEventListener('submit', async (e) => {
			e.preventDefault();

			const rating = Number(ratingInput.value);
			const comment = document.getElementById('tno-rev-comment').value.trim();

			if (rating < 1) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.selectRating;
				return;
			}
			if (!comment) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.enterReview;
				return;
			}

			submitBtn.disabled = true;
			try {
				const body = new URLSearchParams({
					action: 'tno_submit_order_review',
					nonce: cfg.nonce,
					product_id: document.getElementById('tno-rev-product-id').value,
					order_id: document.getElementById('tno-rev-order-id').value,
					rating: String(rating),
					comment,
				});

				const res = await fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				});
				const json = await res.json();

				if (!json.success) {
					feedbackEl.className = 'tno-modal-feedback is-error';
					feedbackEl.textContent = json.data?.message || cfg.i18n.errorGeneric;
					return;
				}

				feedbackEl.className = 'tno-modal-feedback is-success';
				feedbackEl.textContent = cfg.i18n.reviewSaved;
				setTimeout(() => closeModal(reviewModal), 1200);
			} catch (_) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.errorGeneric;
			} finally {
				submitBtn.disabled = false;
			}
		});
	}

	/* ----------------------------------------------------------------------
	   Addresses CRUD Modal
	   ---------------------------------------------------------------------- */
	const addrModal = document.getElementById('tno-address-modal');
	const addrForm = document.getElementById('tno-address-form');

	if (addrModal && addrForm) {
		const modalTitle = document.getElementById('tno-addr-modal-title');
		const feedbackEl = document.getElementById('tno-addr-feedback');
		const submitBtn = document.getElementById('tno-addr-submit');

		document.addEventListener('click', async (e) => {
			if (e.target.closest('[data-open-address-modal]')) {
				modalTitle.textContent = 'ثبت آدرس جدید';
				document.getElementById('tno-addr-id').value = '';
				document.getElementById('tno-addr-state').value = '';
				document.getElementById('tno-addr-city').value = '';
				document.getElementById('tno-addr-postcode').value = '';
				document.getElementById('tno-addr-line').value = '';
				feedbackEl.textContent = '';
				openModal(addrModal);
				return;
			}

			const editBtn = e.target.closest('[data-edit-address]');
			if (editBtn) {
				try {
					const data = JSON.parse(editBtn.dataset.editAddress);
					modalTitle.textContent = 'ویرایش آدرس';
					document.getElementById('tno-addr-id').value = data.id || '';
					document.getElementById('tno-addr-state').value = data.state || '';
					document.getElementById('tno-addr-city').value = data.city || '';
					document.getElementById('tno-addr-postcode').value = data.postcode || '';
					document.getElementById('tno-addr-receiver').value = data.receiver || '';
					document.getElementById('tno-addr-phone').value = data.phone || '';
					document.getElementById('tno-addr-line').value = data.address_1 || '';
					feedbackEl.textContent = '';
					openModal(addrModal);
				} catch (_) {
					// Ignore malformed JSON
				}
				return;
			}

			const delBtn = e.target.closest('[data-delete-address]');
			if (delBtn) {
				if (!window.confirm(cfg.i18n.confirmDelete)) {
					return;
				}
				delBtn.disabled = true;
				try {
					const body = new URLSearchParams({
						action: 'tno_manage_address',
						nonce: cfg.nonce,
						op: 'delete',
						address_id: delBtn.dataset.deleteAddress,
					});
					const res = await fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
						body: body.toString(),
					});
					const json = await res.json();
					if (json.success) {
						window.location.reload();
					}
				} finally {
					delBtn.disabled = false;
				}
			}
		});

		addrForm.addEventListener('submit', async (e) => {
			e.preventDefault();

			const state = document.getElementById('tno-addr-state').value.trim();
			const city = document.getElementById('tno-addr-city').value.trim();
			const addr1 = document.getElementById('tno-addr-line').value.trim();

			if (!state || !city || !addr1) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.fillAddress;
				return;
			}

			submitBtn.disabled = true;
			try {
				const body = new URLSearchParams({
					action: 'tno_manage_address',
					nonce: cfg.nonce,
					op: 'save',
					address_id: document.getElementById('tno-addr-id').value,
					state,
					city,
					postcode: document.getElementById('tno-addr-postcode').value.trim(),
					receiver: document.getElementById('tno-addr-receiver').value.trim(),
					phone: document.getElementById('tno-addr-phone').value.trim(),
					address_1: addr1,
				});

				const res = await fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				});
				const json = await res.json();

				if (!json.success) {
					feedbackEl.className = 'tno-modal-feedback is-error';
					feedbackEl.textContent = json.data?.message || cfg.i18n.errorGeneric;
					return;
				}

				window.location.reload();
			} catch (_) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.errorGeneric;
			} finally {
				submitBtn.disabled = false;
			}
		});
	}
})();