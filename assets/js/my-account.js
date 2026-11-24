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

	/* ----------------------------------------------------------------------
	   Customer Club (باشگاه مشتریان) -> Claim Coupon & Copy Code
	   ---------------------------------------------------------------------- */
	const clubApp = document.getElementById('tno-club-app');
	if (clubApp) {
		const balanceEl = document.getElementById('tno-club-balance-value');
		const feedbackEl = document.getElementById('tno-club-feedback');
		const couponsList = document.getElementById('tno-club-coupons-list');
		const ledgerList = document.getElementById('tno-club-ledger-list');

		function showClubFeedback(msg, isError = false) {
			if (!feedbackEl) return;
			feedbackEl.hidden = false;
			feedbackEl.className = 'tno-club-feedback ' + (isError ? 'is-error' : 'is-success');
			feedbackEl.textContent = msg;
		}

		function refreshTierButtons(newBalance) {
			clubApp.querySelectorAll('.tno-club-tier').forEach((card) => {
				const cost = Number(card.dataset.tierCost || 0);
				const btn = card.querySelector('.tno-club-tier-btn');
				if (!btn) return;
				if (newBalance >= cost) {
					btn.disabled = false;
					btn.classList.add('is-available');
					btn.textContent = cfg.i18n.clubClaimBtn || 'دریافت کد تخفیف';
				} else {
					btn.disabled = true;
					btn.classList.remove('is-available');
					btn.textContent = cfg.i18n.clubNoPoints || 'امتیاز کافی ندارید';
				}
			});
		}

		function toFaDigits(str) {
			const map = { 0: '۰', 1: '۱', 2: '۲', 3: '۳', 4: '۴', 5: '۵', 6: '۶', 7: '۷', 8: '۸', 9: '۹' };
			return String(str).replace(/\d/g, (d) => map[d] || d);
		}

		clubApp.addEventListener('click', async (e) => {
			const copyBtn = e.target.closest('[data-copy-coupon]');
			if (copyBtn) {
				const code = copyBtn.dataset.copyCoupon || '';
				if (!code) return;
				try {
					await navigator.clipboard.writeText(code);
					showClubFeedback((cfg.i18n.clubCopied || 'کد تخفیف کپی شد!') + ' (' + code + ')');
				} catch (_) {
					showClubFeedback(code);
				}
				return;
			}

			const claimBtn = e.target.closest('[data-claim-tier]');
			if (!claimBtn || claimBtn.disabled) return;

			const tierId = claimBtn.dataset.claimTier;
			if (!tierId) return;

			claimBtn.disabled = true;
			const origText = claimBtn.textContent;
			claimBtn.textContent = cfg.i18n.clubClaiming || 'در حال صدور کد...';

			try {
				const body = new URLSearchParams({
					action: 'tno_club_claim_tier',
					nonce: cfg.nonce,
					tier_id: tierId,
				});

				const res = await fetch(cfg.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString(),
				});
				const json = await res.json();

				if (!json.success) {
					claimBtn.disabled = false;
					claimBtn.textContent = origText;
					showClubFeedback(json.data?.message || cfg.i18n.errorGeneric, true);
					return;
				}

				const data = json.data;
				if (balanceEl && data.balanceLabel) {
					balanceEl.textContent = data.balanceLabel;
				}
				refreshTierButtons(Number(data.balance || 0));
				showClubFeedback(data.message || 'کد تخفیف با موفقیت صادر شد.');

				if (couponsList && data.coupon) {
					const emptyCoupons = document.getElementById('tno-club-coupons-empty');
					if (emptyCoupons) emptyCoupons.remove();

					const row = document.createElement('div');
					row.className = 'tno-club-coupon-row';
					row.innerHTML = `
						<div class="tno-club-coupon-info">
							<strong class="tno-club-coupon-title"></strong>
							<span class="tno-club-coupon-exp"></span>
						</div>
						<div class="tno-club-coupon-actions">
							<button type="button" class="tno-club-code-pill" data-copy-coupon="">
								<span dir="ltr"></span>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
							</button>
							<span class="tno-club-pts-pill tno-club-pts-pill--green"></span>
						</div>
					`;
					row.querySelector('.tno-club-coupon-title').textContent = data.coupon.title;
					row.querySelector('.tno-club-coupon-exp').textContent = 'اعتبار تا ' + data.coupon.expires;
					const pill = row.querySelector('.tno-club-code-pill');
					pill.dataset.copyCoupon = data.coupon.code;
					pill.querySelector('span').textContent = data.coupon.code;
					row.querySelector('.tno-club-pts-pill').textContent = data.coupon.status;
					couponsList.prepend(row);
				}

				if (ledgerList && data.coupon) {
					const emptyLedger = document.getElementById('tno-club-ledger-empty');
					if (emptyLedger) emptyLedger.remove();

					const tierCard = claimBtn.closest('.tno-club-tier');
					const cost = Number(tierCard?.dataset.tierCost || 0);
					const newRow = document.createElement('div');
					newRow.className = 'tno-club-ledger-row';
					newRow.innerHTML = `
						<span class="tno-club-ledger-desc">
							<span class="tno-club-ledger-num">۱.</span>
							<span class="tno-club-ledger-text"></span>
						</span>
						<span class="tno-club-pts-pill tno-club-pts-pill--red"></span>
					`;
					newRow.querySelector('.tno-club-ledger-text').textContent = 'دریافت ' + data.coupon.title + ' (' + data.coupon.code + ')';
					newRow.querySelector('.tno-club-pts-pill').textContent = '-' + toFaDigits(cost.toLocaleString('en-US')) + ' امتیاز';
					ledgerList.prepend(newRow);

					ledgerList.querySelectorAll('.tno-club-ledger-num').forEach((el, idx) => {
						el.textContent = toFaDigits(idx + 1) + '.';
					});
				}
			} catch (_) {
				claimBtn.disabled = false;
				claimBtn.textContent = origText;
				showClubFeedback(cfg.i18n.errorGeneric, true);
			}
		});
	}
})();