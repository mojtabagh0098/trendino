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
	   Order Details & Reviews Page -> Product Review Modal + Success Countdown
	   ---------------------------------------------------------------------- */
	const reviewModal = document.getElementById('tno-review-modal');
	const reviewForm = document.getElementById('tno-review-form');
	const reviewSuccessModal = document.getElementById('tno-review-success-modal');
	const reviewSuccessCount = document.getElementById('tno-rev-success-count');
	const reviewSuccessClose = document.getElementById('tno-rev-success-close');

	function toFaNum(val) {
		const map = { 0: '۰', 1: '۱', 2: '۲', 3: '۳', 4: '۴', 5: '۵', 6: '۶', 7: '۷', 8: '۸', 9: '۹' };
		return String(val).replace(/\d/g, (d) => map[d] || d);
	}

	if (reviewModal && reviewForm) {
		const starButtons = reviewModal.querySelectorAll('.tno-rev-star');
		const ratingInput = document.getElementById('tno-rev-rating');
		const feedbackEl = document.getElementById('tno-rev-feedback');
		const submitBtn = document.getElementById('tno-rev-submit');
		let successTimer = null;

		function paintStars(value) {
			starButtons.forEach((btn) => {
				btn.classList.toggle('is-filled', Number(btn.dataset.star) <= value);
			});
		}

		function finishSuccessFlow() {
			if (successTimer) {
				clearInterval(successTimer);
				successTimer = null;
			}
			closeModal(reviewSuccessModal);
		}

		function showReviewSuccess(reviewData) {
			closeModal(reviewModal);

			// Update Reviews & Q&A page DOM live if present
			if (reviewData && reviewData.productId) {
				const pendingCard = document.querySelector(`.tno-rev-pending-card[data-pending-product-id="${reviewData.productId}"]`);
				if (pendingCard) {
					pendingCard.remove();
					const pendingGrid = document.getElementById('tno-rev-pending-grid');
					const pendingEmpty = document.getElementById('tno-rev-pending-empty');
					if (pendingGrid && pendingGrid.querySelectorAll('.tno-rev-pending-card').length === 0) {
						pendingGrid.hidden = true;
						if (pendingEmpty) pendingEmpty.hidden = false;
					}
				}

				const myReviewsGrid = document.getElementById('tno-rev-my-reviews-grid');
				const myReviewsEmpty = document.getElementById('tno-rev-my-reviews-empty');
				if (myReviewsGrid) {
					myReviewsGrid.hidden = false;
					if (myReviewsEmpty) myReviewsEmpty.hidden = true;

					let starsHtml = '';
					for (let s = 1; s <= 5; s++) {
						const filled = s <= Number(reviewData.rating || 0) ? '#f5b942' : 'none';
						starsHtml += `<svg width="14" height="14" viewBox="0 0 24 24" fill="${filled}" stroke="#f5b942" stroke-width="1.8" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
					}

					const card = document.createElement('article');
					card.className = 'tno-rev-history-card';
					card.innerHTML = `
						<div class="tno-rev-card-prod">
							<a class="tno-rev-card-thumb"><img alt="" loading="lazy"></a>
							<div class="tno-rev-card-head-info">
								<h3 class="tno-rev-card-title"><a></a></h3>
								<div class="tno-rev-readonly-stars">${starsHtml}</div>
							</div>
						</div>
						<p class="tno-rev-history-text"></p>
						<footer class="tno-rev-history-foot">
							<span class="tno-rev-history-date"></span>
							<span class="tno-status-badge"></span>
						</footer>
					`;
					const thumbLink = card.querySelector('.tno-rev-card-thumb');
					thumbLink.href = reviewData.productUrl || '#';
					thumbLink.querySelector('img').src = reviewData.productImage || '';
					thumbLink.querySelector('img').alt = reviewData.productTitle || '';
					const titleLink = card.querySelector('.tno-rev-card-title a');
					titleLink.href = reviewData.productUrl || '#';
					titleLink.textContent = reviewData.productTitle || '';
					card.querySelector('.tno-rev-history-text').textContent = reviewData.content || '';
					card.querySelector('.tno-rev-history-date').textContent = reviewData.dateLabel || '';
					const badge = card.querySelector('.tno-status-badge');
					badge.className = 'tno-status-badge tno-status-badge--' + (reviewData.statusTone || 'amber');
					badge.textContent = reviewData.statusLabel || 'در انتظار تایید';
					myReviewsGrid.prepend(card);
				}
			}

			if (!reviewSuccessModal) return;

			let remaining = 3;
			if (reviewSuccessCount) {
				reviewSuccessCount.textContent = toFaNum(remaining);
			}
			openModal(reviewSuccessModal);

			if (successTimer) clearInterval(successTimer);
			successTimer = setInterval(() => {
				remaining -= 1;
				if (remaining <= 0) {
					finishSuccessFlow();
					return;
				}
				if (reviewSuccessCount) {
					reviewSuccessCount.textContent = toFaNum(remaining);
				}
			}, 1000);
		}

		if (reviewSuccessClose) {
			reviewSuccessClose.addEventListener('click', finishSuccessFlow);
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

		// Pending review card star hover preview
		document.querySelectorAll('[data-card-stars]').forEach((group) => {
			const cardStars = group.querySelectorAll('.tno-rev-card-star');
			cardStars.forEach((btn) => {
				btn.addEventListener('mouseenter', () => {
					const val = Number(btn.dataset.star);
					cardStars.forEach((b) => b.classList.toggle('is-filled', Number(b.dataset.star) <= val));
				});
			});
			group.addEventListener('mouseleave', () => {
				cardStars.forEach((b) => b.classList.remove('is-filled'));
			});
		});

		document.addEventListener('click', (e) => {
			const trigger = e.target.closest('[data-open-review]');
			if (!trigger) return;

			const initialRating = Number(trigger.dataset.initialRating || 0);
			document.getElementById('tno-rev-product-id').value = trigger.dataset.productId || '';
			document.getElementById('tno-rev-order-id').value = trigger.dataset.orderId || '';
			document.getElementById('tno-rev-product-title').textContent = trigger.dataset.productTitle || '';
			document.getElementById('tno-rev-product-img').src = trigger.dataset.productImage || '';
			document.getElementById('tno-rev-comment').value = '';
			ratingInput.value = String(initialRating);
			paintStars(initialRating);
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

				showReviewSuccess(json.data?.review || null);
			} catch (_) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.errorGeneric;
			} finally {
				submitBtn.disabled = false;
			}
		});
	}

	/* ----------------------------------------------------------------------
	   Reviews & Questions Tabs Switching
	   ---------------------------------------------------------------------- */
	const reviewsApp = document.getElementById('tno-reviews-app');
	if (reviewsApp) {
		const revTabs = reviewsApp.querySelectorAll('[data-rev-tab]');
		const revPanels = reviewsApp.querySelectorAll('[data-rev-panel]');

		function activateReviewTab(targetTab) {
			revTabs.forEach((btn) => {
				const active = btn.dataset.revTab === targetTab;
				btn.classList.toggle('is-active', active);
				btn.setAttribute('aria-selected', active ? 'true' : 'false');
			});
			revPanels.forEach((panel) => {
				panel.hidden = panel.dataset.revPanel !== targetTab;
			});
			const url = new URL(window.location.href);
			if (targetTab === 'pending') {
				url.searchParams.delete('tab');
			} else {
				url.searchParams.set('tab', targetTab);
			}
			window.history.replaceState({}, '', url.toString());
		}

		reviewsApp.addEventListener('click', (e) => {
			const tabBtn = e.target.closest('[data-rev-tab]');
			if (!tabBtn) return;
			activateReviewTab(tabBtn.dataset.revTab);
		});
	}

	/* ----------------------------------------------------------------------
	   Addresses CRUD Modal & Cascading Province -> City Select
	   ---------------------------------------------------------------------- */
	const addrModal = document.getElementById('tno-address-modal');
	const addrForm = document.getElementById('tno-address-form');

	if (addrModal && addrForm) {
		const modalTitle = document.getElementById('tno-addr-modal-title');
		const idInput = document.getElementById('tno-addr-id');
		const stateSelect = document.getElementById('tno-addr-state');
		const citySelect = document.getElementById('tno-addr-city');
		const lineInput = document.getElementById('tno-addr-line');
		const plaqueInput = document.getElementById('tno-addr-plaque');
		const unitInput = document.getElementById('tno-addr-unit');
		const postcodeInput = document.getElementById('tno-addr-postcode');
		const postcodeErrEl = document.getElementById('tno-addr-postcode-error');
		const feedbackEl = document.getElementById('tno-addr-feedback');
		const submitBtn = document.getElementById('tno-addr-submit');
		const modalDeleteBtn = document.getElementById('tno-addr-modal-delete');
		const citiesMap = cfg.iranCities || {};

		function normalizeDigits(val) {
			return String(val || '')
				.replace(/[۰-۹]/g, (d) => String('۰۱۲۳۴۵۶۷۸۹'.indexOf(d)))
				.replace(/[٠-٩]/g, (d) => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));
		}

		function isValidIranPostcode(rawVal) {
			const clean = normalizeDigits(rawVal).replace(/\s+/g, '');
			return /^\d{10}$/.test(clean) && !/^(\d)\1{9}$/.test(clean);
		}

		function populateCities(stateCode, selectedCity = '') {
			citySelect.innerHTML = '';

			const defaultOpt = document.createElement('option');
			defaultOpt.value = '';
			defaultOpt.textContent = (cfg.i18n && cfg.i18n.selectCity) ? 'انتخاب کنید' : 'انتخاب کنید';
			citySelect.appendChild(defaultOpt);

			const list = stateCode && Array.isArray(citiesMap[stateCode]) ? [...citiesMap[stateCode]] : [];

			if (selectedCity && list.length > 0 && !list.includes(selectedCity)) {
				list.unshift(selectedCity);
			}

			if (stateCode && list.length > 0) {
				list.forEach((cityName) => {
					const opt = document.createElement('option');
					opt.value = cityName;
					opt.textContent = cityName;
					citySelect.appendChild(opt);
				});
				citySelect.disabled = false;
				citySelect.value = selectedCity || '';
			} else if (stateCode && selectedCity) {
				const opt = document.createElement('option');
				opt.value = selectedCity;
				opt.textContent = selectedCity;
				citySelect.appendChild(opt);
				citySelect.disabled = false;
				citySelect.value = selectedCity;
			} else {
				citySelect.disabled = true;
				citySelect.value = '';
			}
		}

		function showPostcodeError(show) {
			if (postcodeErrEl) {
				postcodeErrEl.hidden = !show;
			}
			postcodeInput.classList.toggle('is-invalid', Boolean(show));
		}

		function updateSubmitAvailability() {
			const hasState = stateSelect.value.trim() !== '';
			const hasCity = citySelect.value.trim() !== '';
			const hasLine = lineInput.value.trim() !== '';
			const hasPostcode = postcodeInput.value.trim() !== '';
			submitBtn.disabled = !(hasState && hasCity && hasLine && hasPostcode);
		}

		stateSelect.addEventListener('change', () => {
			populateCities(stateSelect.value.trim(), '');
			updateSubmitAvailability();
		});

		[citySelect, lineInput, plaqueInput, unitInput].forEach((el) => {
			if (!el) return;
			el.addEventListener('input', updateSubmitAvailability);
			el.addEventListener('change', updateSubmitAvailability);
		});

		postcodeInput.addEventListener('input', () => {
			const clean = normalizeDigits(postcodeInput.value).replace(/\s+/g, '');
			if (clean.length === 0) {
				showPostcodeError(false);
			} else if (/[^0-9]/.test(clean) || clean.length >= 10) {
				showPostcodeError(!isValidIranPostcode(clean));
			} else {
				showPostcodeError(false);
			}
			updateSubmitAvailability();
		});

		postcodeInput.addEventListener('blur', () => {
			const val = postcodeInput.value.trim();
			if (val !== '') {
				showPostcodeError(!isValidIranPostcode(val));
			} else {
				showPostcodeError(false);
			}
		});

		async function deleteAddressById(addressId, triggerBtn) {
			if (!addressId || !window.confirm(cfg.i18n.confirmDelete)) {
				return;
			}
			if (triggerBtn) triggerBtn.disabled = true;
			try {
				const body = new URLSearchParams({
					action: 'tno_manage_address',
					nonce: cfg.nonce,
					op: 'delete',
					address_id: addressId,
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
				if (triggerBtn) triggerBtn.disabled = false;
			}
		}

		if (modalDeleteBtn) {
			modalDeleteBtn.addEventListener('click', () => {
				deleteAddressById(idInput.value, modalDeleteBtn);
			});
		}

		document.addEventListener('click', async (e) => {
			if (e.target.closest('[data-open-address-modal]')) {
				modalTitle.textContent = 'افزودن آدرس جدید';
				idInput.value = '';
				stateSelect.value = '';
				populateCities('', '');
				lineInput.value = '';
				if (plaqueInput) plaqueInput.value = '';
				if (unitInput) unitInput.value = '';
				postcodeInput.value = '';
				showPostcodeError(false);
				feedbackEl.textContent = '';
				feedbackEl.className = 'tno-modal-feedback';
				if (modalDeleteBtn) modalDeleteBtn.hidden = true;
				updateSubmitAvailability();
				openModal(addrModal);
				return;
			}

			const editBtn = e.target.closest('[data-edit-address]');
			if (editBtn) {
				try {
					const data = JSON.parse(editBtn.dataset.editAddress);
					modalTitle.textContent = 'ویرایش آدرس';
					idInput.value = data.id || '';
					stateSelect.value = data.state || '';
					populateCities(data.state || '', data.city || '');
					lineInput.value = data.address_1 || '';
					if (plaqueInput) plaqueInput.value = data.plaque || '';
					if (unitInput) unitInput.value = data.unit || '';
					postcodeInput.value = data.postcode || '';
					showPostcodeError(false);
					feedbackEl.textContent = '';
					feedbackEl.className = 'tno-modal-feedback';
					if (modalDeleteBtn) modalDeleteBtn.hidden = !data.id;
					updateSubmitAvailability();
					openModal(addrModal);
				} catch (_) {
					// Ignore malformed JSON
				}
				return;
			}

			const defaultBtn = e.target.closest('[data-set-default-address]');
			if (defaultBtn && !defaultBtn.classList.contains('is-active')) {
				const addrId = defaultBtn.dataset.setDefaultAddress || '';
				if (!addrId) return;
				defaultBtn.disabled = true;
				try {
					const body = new URLSearchParams({
						action: 'tno_manage_address',
						nonce: cfg.nonce,
						op: 'set_default',
						address_id: addrId,
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
					defaultBtn.disabled = false;
				}
				return;
			}

			const delBtn = e.target.closest('[data-delete-address]');
			if (delBtn) {
				await deleteAddressById(delBtn.dataset.deleteAddress, delBtn);
			}
		});

		addrForm.addEventListener('submit', async (e) => {
			e.preventDefault();

			const state = stateSelect.value.trim();
			const city = citySelect.value.trim();
			const addr1 = lineInput.value.trim();
			const plaque = plaqueInput ? plaqueInput.value.trim() : '';
			const unit = unitInput ? unitInput.value.trim() : '';
			const postcodeRaw = postcodeInput.value.trim();

			if (!state || !city || !addr1) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.fillAddress;
				return;
			}

			if (!isValidIranPostcode(postcodeRaw)) {
				showPostcodeError(true);
				postcodeInput.focus();
				return;
			}
			showPostcodeError(false);

			submitBtn.disabled = true;
			try {
				const body = new URLSearchParams({
					action: 'tno_manage_address',
					nonce: cfg.nonce,
					op: 'save',
					address_id: idInput.value,
					state,
					city,
					postcode: normalizeDigits(postcodeRaw).replace(/\s+/g, ''),
					plaque,
					unit,
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
					if (json.data?.field === 'postcode') {
						showPostcodeError(true);
					} else {
						feedbackEl.className = 'tno-modal-feedback is-error';
						feedbackEl.textContent = json.data?.message || cfg.i18n.errorGeneric;
					}
					return;
				}

				window.location.reload();
			} catch (_) {
				feedbackEl.className = 'tno-modal-feedback is-error';
				feedbackEl.textContent = cfg.i18n.errorGeneric;
			} finally {
				updateSubmitAvailability();
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

	/* ----------------------------------------------------------------------
	   Edit Account Details (اطلاعات حساب کاربری)
	   ---------------------------------------------------------------------- */
	const editAccountForm = document.getElementById('tno-edit-account-form');
	if (editAccountForm) {
		const feedbackEl = document.getElementById('tno-edit-account-feedback');
		const submitBtn = document.getElementById('tno-edit-account-submit');
		const submitLabel = submitBtn ? submitBtn.querySelector('span') : null;

		editAccountForm.addEventListener('submit', async (e) => {
			e.preventDefault();
			if (!submitBtn) return;

			submitBtn.disabled = true;
			if (submitLabel) {
				submitLabel.textContent = cfg.i18n.savingAccount || 'در حال ذخیره...';
			}
			if (feedbackEl) {
				feedbackEl.hidden = true;
				feedbackEl.textContent = '';
			}

			try {
				const body = new URLSearchParams({
					action: 'tno_save_account_details',
					nonce: cfg.nonce,
					first_name: document.getElementById('tno-acc-first-name')?.value.trim() || '',
					last_name: document.getElementById('tno-acc-last-name')?.value.trim() || '',
					phone: document.getElementById('tno-acc-phone')?.value.trim() || '',
					email: document.getElementById('tno-acc-email')?.value.trim() || '',
					national_code: document.getElementById('tno-acc-national-code')?.value.trim() || '',
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
					feedbackEl.className = 'tno-edit-account-feedback ' + (json.success ? 'is-success' : 'is-error');
					feedbackEl.textContent = json.data?.message || (json.success ? cfg.i18n.accountSaved : cfg.i18n.errorGeneric);
				}

				if (json.success && json.data) {
					const navName = document.querySelector('.tno-acc-profile-name');
					const navPhone = document.querySelector('.tno-acc-profile-phone');
					if (navName && json.data.displayName) {
						navName.textContent = json.data.displayName;
					}
					if (navPhone && typeof json.data.phoneDisplay === 'string') {
						navPhone.textContent = json.data.phoneDisplay;
					}
				}
			} catch (_) {
				if (feedbackEl) {
					feedbackEl.hidden = false;
					feedbackEl.className = 'tno-edit-account-feedback is-error';
					feedbackEl.textContent = cfg.i18n.errorGeneric;
				}
			} finally {
				submitBtn.disabled = false;
				if (submitLabel) {
					submitLabel.textContent = cfg.i18n.saveAccountBtn || 'ذخیره اطلاعات';
				}
			}
		});
	}

	/* ----------------------------------------------------------------------
	   Logout Confirmation Modal
	   ---------------------------------------------------------------------- */
	const logoutModal = document.getElementById('tno-logout-modal');
	if (logoutModal) {
		document.addEventListener('click', (e) => {
			const trigger = e.target.closest('[data-open-logout-modal]');
			if (!trigger) return;
			e.preventDefault();
			openModal(logoutModal);
		});
	}

	/* ----------------------------------------------------------------------
	   Wishlist & Recently Viewed -> Add to Cart & Remove Card
	   ---------------------------------------------------------------------- */
	const prodGrid = document.getElementById('tno-acc-prod-grid');
	if (prodGrid) {
		const prodSection = document.getElementById('tno-acc-prod-section');
		const prodEmpty = document.getElementById('tno-acc-prod-empty');

		prodGrid.addEventListener('click', async (e) => {
			const delBtn = e.target.closest('[data-remove-acc-product]');
			if (delBtn) {
				const productId = delBtn.dataset.removeAccProduct;
				const context = delBtn.dataset.removeContext || 'wishlist';
				const card = delBtn.closest('.tno-acc-prod-card');
				if (!productId || !card) return;

				delBtn.disabled = true;
				try {
					const body = new URLSearchParams({
						action: context === 'recent' ? 'tno_account_remove_recent_view' : 'tno_account_remove_wishlist',
						nonce: cfg.nonce,
						product_id: productId,
					});
					const res = await fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
						body: body.toString(),
					});
					const json = await res.json();
					if (json.success) {
						card.remove();
						if (prodGrid.querySelectorAll('.tno-acc-prod-card').length === 0) {
							if (prodSection) prodSection.hidden = true;
							if (prodEmpty) prodEmpty.hidden = false;
						}
					} else {
						delBtn.disabled = false;
					}
				} catch (_) {
					delBtn.disabled = false;
				}
				return;
			}

			const addBtn = e.target.closest('[data-acc-add-cart]');
			if (addBtn && !addBtn.disabled) {
				const productId = addBtn.dataset.accAddCart;
				if (!productId) return;

				addBtn.disabled = true;
				const origText = addBtn.textContent;
				addBtn.textContent = cfg.i18n.addingToCart || 'در حال افزودن...';

				try {
					const body = new URLSearchParams({
						action: 'tno_account_add_to_cart',
						nonce: cfg.nonce,
						product_id: productId,
					});
					const res = await fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
						body: body.toString(),
					});
					const json = await res.json();

					if (json.success) {
						addBtn.classList.add('is-added');
						addBtn.textContent = cfg.i18n.addedToCart || 'به سبد اضافه شد ✓';

						if (window.jQuery) {
							window.jQuery(document.body).trigger('wc_fragment_refresh');
						}
						if (json.data?.cartCountLabel) {
							document.querySelectorAll('.tno-cart-badge, .tno-header-cart-count, .cart-contents-count').forEach((el) => {
								el.textContent = json.data.cartCountLabel;
							});
						}

						setTimeout(() => {
							addBtn.classList.remove('is-added');
							addBtn.textContent = cfg.i18n.addToCartBtn || origText;
							addBtn.disabled = false;
						}, 1600);
					} else {
						addBtn.textContent = origText;
						addBtn.disabled = false;
					}
				} catch (_) {
					addBtn.textContent = origText;
					addBtn.disabled = false;
				}
			}
		});
	}
})();