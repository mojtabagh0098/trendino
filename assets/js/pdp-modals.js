(() => {
	'use strict';

	const cfg = window.tnoPdpModalsConfig;
	if (!cfg) return;

	class TnoPdpModals {
		constructor() {
			this.activeModal = null;
			this.lastTrigger = null;
			this.selectedRating = 0;

			this.modals = {
				review: document.getElementById('tno-modal-review'),
				question: document.getElementById('tno-modal-question'),
				answer: document.getElementById('tno-modal-answer'),
				auth: document.getElementById('tno-modal-auth-gate'),
			};

			this.authTitleEl = document.getElementById('tno-modal-auth-title');
			this.answerQuestionIdInput = document.getElementById('tno-answer-question-id');
			this.answerQuestionPreviewEl = document.getElementById('tno-answer-question-preview');

			this.bindTriggers();
			this.bindCloseHandlers();
			this.initStarRating();
			this.bindForms();
		}

		bindTriggers() {
			document.addEventListener('click', (e) => {
				const trigger = e.target.closest('[data-tno-modal]');
				if (!trigger) return;

				e.preventDefault();
				const targetType = trigger.dataset.tnoModal; // review | question | answer
				this.lastTrigger = trigger;

				// گیت احراز هویت برای هر سه عملیات (بدون بررسی خرید قبلی)
				if (!cfg.isLoggedIn) {
					this.openAuthGate(targetType === 'review' ? 'review' : 'qa');
					return;
				}

				if (targetType === 'answer') {
					const questionId = parseInt(trigger.dataset.questionId || '0', 10);
					const questionText = (trigger.dataset.questionText || '').trim();
					if (!questionId) return;

					this.answerQuestionIdInput.value = String(questionId);
					this.answerQuestionPreviewEl.textContent = questionText;
				}

				this.open(targetType);
			});
		}

		bindCloseHandlers() {
			document.addEventListener('click', (e) => {
				if (e.target.closest('[data-tno-modal-close]')) {
					e.preventDefault();
					this.closeActive();
				}
			});

			document.addEventListener('keydown', (e) => {
				if (e.key === 'Escape' && this.activeModal) {
					this.closeActive();
				}
			});
		}

		openAuthGate(context) {
			if (this.authTitleEl) {
				this.authTitleEl.textContent =
					context === 'review' ? cfg.i18n.authReviewTitle : cfg.i18n.authQaTitle;
			}
			this.open('auth');
		}

		open(type) {
			const modal = this.modals[type];
			if (!modal) return;

			if (this.activeModal && this.activeModal !== modal) {
				this.closeActive(false);
			}

			this.clearAlert(modal);
			modal.hidden = false;
			document.body.classList.add('tno-modal-open');

			requestAnimationFrame(() => {
				modal.classList.add('is-open');
				const focusTarget = modal.querySelector('textarea, .tno-modal-btn, .tno-modal__close');
				if (focusTarget) focusTarget.focus();
			});

			this.activeModal = modal;
		}

		closeActive(restoreFocus = true) {
			if (!this.activeModal) return;

			const closingModal = this.activeModal;
			closingModal.classList.remove('is-open');
			document.body.classList.remove('tno-modal-open');

			window.setTimeout(() => {
				closingModal.hidden = true;
			}, 200);

			this.activeModal = null;

			if (restoreFocus && this.lastTrigger) {
				this.lastTrigger.focus();
			}
		}

		initStarRating() {
			const form = document.getElementById('tno-form-review');
			if (!form) return;

			const container = form.querySelector('.tno-stars');
			const buttons = Array.from(form.querySelectorAll('.tno-star-btn'));
			const ratingInput = form.querySelector('input[name="rating"]');
			if (!container || !buttons.length || !ratingInput) return;

			const paint = (hoverVal) => {
				buttons.forEach((btn) => {
					const val = parseInt(btn.dataset.starValue, 10);
					btn.classList.toggle('is-hovered', hoverVal > 0 && val <= hoverVal);
					btn.classList.toggle('is-active', hoverVal === 0 && val <= this.selectedRating);
					btn.setAttribute('aria-checked', String(val === this.selectedRating));
				});
			};

			buttons.forEach((btn) => {
				btn.addEventListener('mouseenter', () => paint(parseInt(btn.dataset.starValue, 10)));
				btn.addEventListener('click', () => {
					this.selectedRating = parseInt(btn.dataset.starValue, 10);
					ratingInput.value = String(this.selectedRating);
					paint(0);
				});
			});

			container.addEventListener('mouseleave', () => paint(0));

			// متد کمکی برای ریست ستاره‌ها پس از ارسال موفق
			this.resetStars = () => {
				this.selectedRating = 0;
				ratingInput.value = '0';
				paint(0);
			};
		}

		bindForms() {
			this.attachFormHandler('tno-form-review', 'tno_modal_submit_review', (form) => {
				const rating = parseInt(form.querySelector('input[name="rating"]').value, 10);
				const content = form.querySelector('textarea[name="content"]').value.trim();

				if (rating < 1 || rating > 5) {
					return { error: cfg.i18n.ratingRequired };
				}
				if (content.length < 5) {
					return { error: cfg.i18n.contentTooShort };
				}
				return { payload: { rating, content } };
			});

			this.attachFormHandler('tno-form-question', 'tno_modal_submit_question', (form) => {
				const content = form.querySelector('textarea[name="content"]').value.trim();
				if (content.length < 5) {
					return { error: cfg.i18n.contentTooShort };
				}
				return { payload: { content } };
			});

			this.attachFormHandler('tno-form-answer', 'tno_modal_submit_answer', (form) => {
				const questionId = parseInt(form.querySelector('input[name="question_id"]').value, 10);
				const content = form.querySelector('textarea[name="content"]').value.trim();
				if (!questionId) {
					return { error: cfg.i18n.networkError };
				}
				if (content.length < 5) {
					return { error: cfg.i18n.contentTooShort };
				}
				return { payload: { question_id: questionId, content } };
			});
		}

		attachFormHandler(formId, ajaxAction, buildPayload) {
			const form = document.getElementById(formId);
			if (!form) return;

			const modal = form.closest('.tno-modal');
			const submitBtn = form.querySelector('button[type="submit"]');
			const btnTextEl = submitBtn.querySelector('.tno-modal-btn__text');
			const originalBtnText = btnTextEl.textContent;

			form.addEventListener('submit', async (e) => {
				e.preventDefault();
				this.clearAlert(modal);

				const validation = buildPayload(form);
				if (validation.error) {
					this.showAlert(modal, validation.error, 'error');
					return;
				}

				const formData = new FormData();
				formData.append('action', ajaxAction);
				formData.append('nonce', cfg.nonce);
				formData.append('product_id', String(cfg.productId));

				Object.entries(validation.payload).forEach(([k, v]) => {
					formData.append(k, String(v));
				});

				submitBtn.disabled = true;
				submitBtn.classList.add('is-loading');
				btnTextEl.textContent = cfg.i18n.submitting;

				try {
					const response = await fetch(cfg.ajaxUrl, {
						method: 'POST',
						credentials: 'same-origin',
						body: formData,
					});

					const result = await response.json();

					if (response.status === 401 || result?.data?.code === 'auth_required') {
                        cfg.isLoggedIn = false;
                        this.openAuthGate(ajaxAction === 'tno_modal_submit_review' ? 'review' : 'qa');
                        return;
                    }

					if (!response.ok || !result.success) {
						this.showAlert(modal, result?.data?.message || cfg.i18n.networkError, 'error');
						return;
					}

					this.showAlert(modal, result.data.message, 'success');
					form.reset();
					if (formId === 'tno-form-review' && this.resetStars) {
						this.resetStars();
					}

					// رویداد سفارشی برای رفرش لیست دیدگاه‌ها/پرسش‌ها توسط pdp.js در صورت نیاز
					document.dispatchEvent(
						new CustomEvent('tno:pdpModalSubmitted', {
							detail: { action: ajaxAction, data: result.data },
						})
					);

					window.setTimeout(() => {
						this.closeActive();
						if (result.data.approved) {
							window.location.reload();
						}
					}, 1400);
				} catch {
					this.showAlert(modal, cfg.i18n.networkError, 'error');
				} finally {
					submitBtn.disabled = false;
					submitBtn.classList.remove('is-loading');
					btnTextEl.textContent = originalBtnText;
				}
			});
		}

		showAlert(modal, message, type) {
			const el = modal.querySelector('.tno-modal-alert');
			if (!el) return;
			el.textContent = message;
			el.classList.remove('is-error', 'is-success');
			el.classList.add(type === 'success' ? 'is-success' : 'is-error');
			el.hidden = false;
		}

		clearAlert(modal) {
			const el = modal.querySelector('.tno-modal-alert');
			if (!el) return;
			el.textContent = '';
			el.classList.remove('is-error', 'is-success');
			el.hidden = true;
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => new TnoPdpModals());
	} else {
		new TnoPdpModals();
	}
})();