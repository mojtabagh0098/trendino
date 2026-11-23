(() => {
	'use strict';

	const cfg = window.tnoCompareConfig;
	if (!cfg) {
		return;
	}

	const MAX_ITEMS = Number(cfg.maxItems) || 4;
	const STORAGE_KEY = 'tno_compare_ids';

	const dom = {
		sticky: document.getElementById('tno-cmp-sticky'),
		stickyGrid: document.getElementById('tno-cmp-sticky-grid'),
		cardsShell: document.getElementById('tno-cmp-cards-shell'),
		cardsGrid: document.getElementById('tno-cmp-cards-grid'),
		specs: document.getElementById('tno-cmp-specs'),
		modal: document.getElementById('tno-cmp-modal'),
		modalClose: document.getElementById('tno-cmp-modal-close'),
		modalGrid: document.getElementById('tno-cmp-modal-grid'),
		modalMoreWrap: document.getElementById('tno-cmp-modal-more-wrap'),
		modalLoadMore: document.getElementById('tno-cmp-load-more'),
		modalSubmit: document.getElementById('tno-cmp-modal-submit'),
		searchInput: document.getElementById('tno-cmp-search-input'),
		selectedStrip: document.getElementById('tno-cmp-selected-strip'),
		remainingText: document.getElementById('tno-cmp-remaining-text'),
		totalCount: document.getElementById('tno-cmp-total-count'),
		toast: document.getElementById('tno-cmp-toast'),
		toastMsg: document.getElementById('tno-cmp-toast-msg'),
		toastClose: document.getElementById('tno-cmp-toast-close'),
	};

	const state = {
		/** @type {Map<number, Object>} */
		productsMap: new Map(),
		/** @type {number[]} */
		activeIds: [],
		/** @type {number[]} */
		draftIds: [],
		/** @type {Map<number, Object>} */
		modalProductsMap: new Map(),
		/** @type {Object[]} */
		modalList: [],
		modalTotal: 0,
		modalPage: 1,
		modalMaxPages: 1,
		searchQuery: '',
		searchTimer: null,
		searchAbort: null,
		toastTimer: null,
	};

	/* ----------------------------------------------------------------------
	   Utilities
	   ---------------------------------------------------------------------- */
	const FA_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

	function toFaDigits(value) {
		return String(value).replace(/\d/g, (d) => FA_DIGITS[Number(d)]);
	}

	function formatPrice(num) {
		const n = Number(num) || 0;
		return toFaDigits(Math.round(n).toLocaleString('en-US'));
	}

	function escapeHtml(str) {
		return String(str ?? '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function persistIds(ids) {
		const clean = ids.slice(0, MAX_ITEMS);
		const joined = clean.join(',');

		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(clean));
		} catch (_) {
			// Ignore storage quota / private mode errors.
		}

		document.cookie = `${cfg.cookieName}=${encodeURIComponent(joined)}; path=/; max-age=2592000; SameSite=Lax`;

		const url = new URL(window.location.href);
		if (joined) {
			url.searchParams.set('ids', joined);
		} else {
			url.searchParams.delete('ids');
		}
		window.history.replaceState({}, '', url.toString());
	}

	function readFallbackLocalIds() {
		try {
			const raw = localStorage.getItem(STORAGE_KEY);
			const parsed = raw ? JSON.parse(raw) : [];
			if (Array.isArray(parsed)) {
				return parsed.map(Number).filter((id) => Number.isInteger(id) && id > 0).slice(0, MAX_ITEMS);
			}
		} catch (_) {
			return [];
		}
		return [];
	}

	/* ----------------------------------------------------------------------
	   Toast
	   ---------------------------------------------------------------------- */
	function showToast(message) {
		if (!dom.toast || !dom.toastMsg) {
			return;
		}
		dom.toastMsg.textContent = message;
		dom.toast.hidden = false;

		clearTimeout(state.toastTimer);
		state.toastTimer = setTimeout(hideToast, 4000);
	}

	function hideToast() {
		if (!dom.toast) {
			return;
		}
		dom.toast.hidden = true;
		clearTimeout(state.toastTimer);
	}

	/* ----------------------------------------------------------------------
	   Template Builders
	   ---------------------------------------------------------------------- */
	function renderBadge(badge) {
		if (!badge || !badge.text) {
			return '<span></span>';
		}
		const styleClass = badge.style === 'pink' ? 'tno-cmp-badge--pink' : 'tno-cmp-badge--blue';
		return `<span class="tno-cmp-badge ${styleClass}">${escapeHtml(badge.text)}</span>`;
	}

	function renderPriceBlock(p) {
		const discountHtml = p.onSale && p.discountPercent > 0
			? `<span class="tno-cmp-discount-pill">${toFaDigits(p.discountPercent)}٪</span>`
			: '';

		const oldPriceHtml = p.onSale && p.regularPrice > p.price
			? `<span class="tno-cmp-old-price">${formatPrice(p.regularPrice)}</span>`
			: '';

		const finalHtml = p.price > 0
			? `<span class="tno-cmp-final-price"><span>${formatPrice(p.price)}</span><span class="tno-cmp-currency">${escapeHtml(cfg.i18n.currency)}</span></span>`
			: `<span class="tno-cmp-currency">ناموجود</span>`;

		return `
			<div class="tno-cmp-card-price-row">
				${discountHtml}
				<div class="tno-cmp-price-stack">
					${oldPriceHtml}
					${finalHtml}
				</div>
			</div>
		`;
	}

	function renderMainCard(p) {
		return `
			<article class="tno-cmp-card" data-product-id="${p.id}">
				<div class="tno-cmp-card-top">
					${renderBadge(p.badge)}
					<button type="button" class="tno-cmp-remove-btn" data-action="remove" data-id="${p.id}" aria-label="حذف از مقایسه">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
					</button>
				</div>
				<a class="tno-cmp-card-img-link" href="${escapeHtml(p.url)}">
					<img class="tno-cmp-card-img" src="${escapeHtml(p.image)}" alt="${escapeHtml(p.title)}" loading="lazy">
				</a>
				<h3 class="tno-cmp-card-title">
					<a href="${escapeHtml(p.url)}">${escapeHtml(p.title)}</a>
				</h3>
				${renderPriceBlock(p)}
			</article>
		`;
	}

	function renderStickyCard(p) {
		return `
			<div class="tno-cmp-sticky-card" data-product-id="${p.id}">
				<img class="tno-cmp-sticky-thumb" src="${escapeHtml(p.image)}" alt="${escapeHtml(p.title)}">
				<h4 class="tno-cmp-sticky-title">
					<a href="${escapeHtml(p.url)}">${escapeHtml(p.title)}</a>
				</h4>
				<button type="button" class="tno-cmp-sticky-remove" data-action="remove" data-id="${p.id}" aria-label="حذف از مقایسه">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
			</div>
		`;
	}

	function renderAddButton() {
		return `
			<button type="button" class="tno-cmp-add-btn" data-action="open-modal">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
				<span>${escapeHtml(cfg.i18n.addProduct)}</span>
			</button>
		`;
	}

	/* ----------------------------------------------------------------------
	   Main Page Rendering (Cards + Sticky + Aligned Specs Matrix)
	   ---------------------------------------------------------------------- */
	function renderCompareView() {
		const activeProducts = state.activeIds
			.map((id) => state.productsMap.get(id))
			.filter(Boolean);

		// 1. Main top cards
		if (activeProducts.length === 0) {
			dom.cardsGrid.innerHTML = `
				<div class="tno-cmp-add-slot tno-cmp-add-slot--full">
					<p class="tno-cmp-empty-msg">${escapeHtml(cfg.i18n.emptyCompare)}</p>
					${renderAddButton()}
				</div>
			`;
			dom.stickyGrid.innerHTML = '';
			dom.sticky.classList.remove('is-visible');
			dom.specs.innerHTML = '';
			return;
		}

		let mainHtml = activeProducts.map(renderMainCard).join('');
		if (activeProducts.length < MAX_ITEMS) {
			mainHtml += `<div class="tno-cmp-add-slot">${renderAddButton()}</div>`;
		}
		dom.cardsGrid.innerHTML = mainHtml;

		// 2. Sticky bar cards
		let stickyHtml = activeProducts.map(renderStickyCard).join('');
		if (activeProducts.length < MAX_ITEMS) {
			stickyHtml += `<div class="tno-cmp-sticky-add">${renderAddButton()}</div>`;
		}
		dom.stickyGrid.innerHTML = stickyHtml;

		// 3. Build unified group -> attribute matrix preserving insertion order
		/** @type {Map<string, Set<string>>} */
		const groupMatrix = new Map();

		activeProducts.forEach((p) => {
			const specs = p.specs && typeof p.specs === 'object' ? p.specs : {};
			Object.entries(specs).forEach(([groupName, attrs]) => {
				if (!groupMatrix.has(groupName)) {
					groupMatrix.set(groupName, new Set());
				}
				const attrSet = groupMatrix.get(groupName);
				if (attrs && typeof attrs === 'object') {
					Object.keys(attrs).forEach((attrKey) => attrSet.add(attrKey));
				}
			});
		});

		if (groupMatrix.size === 0) {
			dom.specs.innerHTML = '';
			return;
		}

		const emptyColsCount = MAX_ITEMS - activeProducts.length;
		let specsHtml = '';

		groupMatrix.forEach((attrSet, groupName) => {
			let rowsHtml = '';

			attrSet.forEach((attrName) => {
				let cellsHtml = '';

				activeProducts.forEach((p, idx) => {
					const rawVal = p.specs?.[groupName]?.[attrName];
					const val = rawVal && String(rawVal).trim() !== '' ? String(rawVal) : '—';
					const labelClass = idx === 0 ? 'tno-cmp-spec-label' : 'tno-cmp-spec-label tno-cmp-spec-label--spacer';
					const ariaHidden = idx === 0 ? '' : ' aria-hidden="true"';

					cellsHtml += `
						<div class="tno-cmp-spec-cell">
							<div class="${labelClass}"${ariaHidden}>${escapeHtml(attrName)}</div>
							<div class="tno-cmp-spec-value">${escapeHtml(val)}</div>
						</div>
					`;
				});

				for (let i = 0; i < emptyColsCount; i++) {
					cellsHtml += '<div class="tno-cmp-spec-cell tno-cmp-spec-cell--empty"></div>';
				}

				rowsHtml += `<div class="tno-cmp-spec-row">${cellsHtml}</div>`;
			});

			specsHtml += `
				<section class="tno-cmp-spec-group">
					<div class="tno-cmp-group-header">${escapeHtml(groupName)}</div>
					<div class="tno-cmp-spec-rows">${rowsHtml}</div>
				</section>
			`;
		});

		dom.specs.innerHTML = specsHtml;
	}

	/* ----------------------------------------------------------------------
	   Modal Rendering & Interactions
	   ---------------------------------------------------------------------- */
	function renderModalToolbar() {
		const remaining = Math.max(0, MAX_ITEMS - state.draftIds.length);
		dom.remainingText.textContent = cfg.i18n.remainingTpl.replace('{n}', String(remaining));
		dom.totalCount.textContent = cfg.i18n.totalTpl.replace('{n}', toFaDigits(state.modalTotal));

		dom.selectedStrip.innerHTML = state.draftIds
			.map((id) => {
				const item = state.productsMap.get(id) || state.modalProductsMap.get(id);
				if (!item) {
					return '';
				}
				return `
					<div class="tno-cmp-mini-thumb" title="${escapeHtml(item.title)}">
						<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}">
						<button type="button" class="tno-cmp-mini-remove" data-action="draft-remove" data-id="${item.id}" aria-label="حذف">
							<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
						</button>
					</div>
				`;
			})
			.join('');
	}

	function renderModalCards() {
		if (state.modalList.length === 0) {
			dom.modalGrid.innerHTML = `<div class="tno-cmp-modal-status">${escapeHtml(cfg.i18n.emptySearch)}</div>`;
			dom.modalMoreWrap.hidden = true;
			return;
		}

		dom.modalGrid.innerHTML = state.modalList
			.map((p) => {
				const isSelected = state.draftIds.includes(p.id);
				return `
					<article class="tno-cmp-modal-card ${isSelected ? 'is-selected' : ''}" data-action="draft-toggle" data-id="${p.id}" role="checkbox" aria-checked="${isSelected ? 'true' : 'false'}" tabindex="0">
						<span class="tno-cmp-modal-check" aria-hidden="true">
							<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
						</span>
						<div class="tno-cmp-card-top">
							${renderBadge(p.badge)}
						</div>
						<div class="tno-cmp-card-img-link">
							<img class="tno-cmp-card-img" src="${escapeHtml(p.image)}" alt="${escapeHtml(p.title)}" loading="lazy">
						</div>
						<h4 class="tno-cmp-card-title">${escapeHtml(p.title)}</h4>
						${renderPriceBlock(p)}
					</article>
				`;
			})
			.join('');

		dom.modalMoreWrap.hidden = state.modalPage >= state.modalMaxPages;
	}

	async function fetchModalProducts(append = false) {
		if (state.searchAbort) {
			state.searchAbort.abort();
		}
		state.searchAbort = new AbortController();

		if (!append) {
			dom.modalGrid.innerHTML = `<div class="tno-cmp-modal-status">${escapeHtml(cfg.i18n.loading)}</div>`;
			dom.modalMoreWrap.hidden = true;
		}

		const params = new URLSearchParams({
			action: 'tno_compare_search_products',
			nonce: cfg.nonce,
			search: state.searchQuery,
			paged: String(state.modalPage),
		});

		try {
			const res = await fetch(`${cfg.ajaxUrl}?${params.toString()}`, {
				credentials: 'same-origin',
				signal: state.searchAbort.signal,
			});
			const json = await res.json();
			if (!json.success) {
				return;
			}

			const items = Array.isArray(json.data.products) ? json.data.products : [];
			items.forEach((item) => state.modalProductsMap.set(item.id, item));

			state.modalList = append ? state.modalList.concat(items) : items;
			state.modalTotal = Number(json.data.total) || 0;
			state.modalMaxPages = Number(json.data.maxPages) || 1;

			renderModalToolbar();
			renderModalCards();
		} catch (err) {
			if (err.name !== 'AbortError') {
				dom.modalGrid.innerHTML = `<div class="tno-cmp-modal-status">${escapeHtml(cfg.i18n.emptySearch)}</div>`;
			}
		}
	}

	async function fetchFullProductsByIds(ids) {
		const missing = ids.filter((id) => !state.productsMap.has(id));
		if (missing.length === 0) {
			return;
		}

		const body = new URLSearchParams({
			action: 'tno_compare_get_products',
			nonce: cfg.nonce,
			ids: missing.join(','),
		});

		const res = await fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString(),
		});
		const json = await res.json();

		if (json.success && Array.isArray(json.data.products)) {
			json.data.products.forEach((p) => state.productsMap.set(p.id, p));
		}
	}

	function openModal() {
		state.draftIds = [...state.activeIds];
		state.modalPage = 1;
		dom.modal.hidden = false;
		document.body.style.overflow = 'hidden';

		renderModalToolbar();
		fetchModalProducts(false);

		requestAnimationFrame(() => dom.searchInput.focus());
	}

	function closeModal() {
		dom.modal.hidden = true;
		document.body.style.overflow = '';
		hideToast();
	}

	function toggleDraftProduct(id) {
		const idx = state.draftIds.indexOf(id);
		if (idx > -1) {
			state.draftIds.splice(idx, 1);
		} else {
			if (state.draftIds.length >= MAX_ITEMS) {
				showToast(cfg.i18n.limitExceeded);
				return;
			}
			state.draftIds.push(id);
		}

		renderModalToolbar();
		renderModalCards();
	}

	async function commitModalSelection() {
		dom.modalSubmit.disabled = true;
		try {
			await fetchFullProductsByIds(state.draftIds);
			state.activeIds = state.draftIds.filter((id) => state.productsMap.has(id));
			persistIds(state.activeIds);
			renderCompareView();
			closeModal();
		} finally {
			dom.modalSubmit.disabled = false;
		}
	}

	function removeActiveProduct(id) {
		state.activeIds = state.activeIds.filter((itemId) => itemId !== id);
		persistIds(state.activeIds);
		renderCompareView();
	}

	/* ----------------------------------------------------------------------
	   Sticky Header Observer
	   ---------------------------------------------------------------------- */
	function initStickyObserver() {
		if (!('IntersectionObserver' in window) || !dom.cardsShell || !dom.sticky) {
			return;
		}

		const observer = new IntersectionObserver(
			(entries) => {
				const entry = entries[0];
				const shouldShow = !entry.isIntersecting && entry.boundingClientRect.bottom < 0 && state.activeIds.length > 0;
				dom.sticky.classList.toggle('is-visible', shouldShow);
				dom.sticky.setAttribute('aria-hidden', shouldShow ? 'false' : 'true');
			},
			{ threshold: 0 }
		);

		observer.observe(dom.cardsShell);
	}

	/* ----------------------------------------------------------------------
	   Event Listeners
	   ---------------------------------------------------------------------- */
	function bindEvents() {
		document.getElementById('tno-compare-app').addEventListener('click', (e) => {
			const target = e.target.closest('[data-action]');
			if (!target) {
				return;
			}

			const action = target.dataset.action;
			const id = Number(target.dataset.id);

			if (action === 'open-modal') {
				openModal();
			} else if (action === 'remove' && id) {
				removeActiveProduct(id);
			} else if (action === 'draft-toggle' && id) {
				toggleDraftProduct(id);
			} else if (action === 'draft-remove' && id) {
				e.stopPropagation();
				state.draftIds = state.draftIds.filter((itemId) => itemId !== id);
				renderModalToolbar();
				renderModalCards();
			}
		});

		dom.modalGrid.addEventListener('keydown', (e) => {
			if (e.key !== 'Enter' && e.key !== ' ') {
				return;
			}
			const card = e.target.closest('[data-action="draft-toggle"]');
			if (card) {
				e.preventDefault();
				toggleDraftProduct(Number(card.dataset.id));
			}
		});

		dom.modalClose.addEventListener('click', closeModal);
		dom.modal.addEventListener('click', (e) => {
			if (e.target === dom.modal) {
				closeModal();
			}
		});

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && !dom.modal.hidden) {
				closeModal();
			}
		});

		dom.searchInput.addEventListener('input', (e) => {
			const value = e.target.value.trim();
			clearTimeout(state.searchTimer);
			state.searchTimer = setTimeout(() => {
				state.searchQuery = value;
				state.modalPage = 1;
				fetchModalProducts(false);
			}, 300);
		});

		dom.modalLoadMore.addEventListener('click', () => {
			if (state.modalPage < state.modalMaxPages) {
				state.modalPage += 1;
				fetchModalProducts(true);
			}
		});

		dom.modalSubmit.addEventListener('click', commitModalSelection);
		dom.toastClose.addEventListener('click', hideToast);
	}

	/* ----------------------------------------------------------------------
	   Bootstrap
	   ---------------------------------------------------------------------- */
	async function init() {
		const initial = Array.isArray(cfg.initialProducts) ? cfg.initialProducts : [];
		initial.forEach((p) => {
			state.productsMap.set(p.id, p);
			state.activeIds.push(p.id);
		});

		// Fallback to localStorage if URL/Cookie had no IDs
		if (state.activeIds.length === 0) {
			const storedIds = readFallbackLocalIds();
			if (storedIds.length > 0) {
				await fetchFullProductsByIds(storedIds);
				state.activeIds = storedIds.filter((id) => state.productsMap.has(id));
				if (state.activeIds.length > 0) {
					persistIds(state.activeIds);
				}
			}
		}

		bindEvents();
		initStickyObserver();
		renderCompareView();
	}

	init();
})();