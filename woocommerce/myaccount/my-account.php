<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="tno-acc-page" dir="rtl">
	<div class="tno-acc-container">
		<div class="tno-acc-layout">
			<aside class="tno-acc-sidebar">
				<?php do_action( 'woocommerce_account_navigation' ); ?>
			</aside>

			<main class="tno-acc-content">
				<?php do_action( 'woocommerce_account_content' ); ?>
			</main>
		</div>
	</div>

	<!-- Logout Confirmation Modal -->
	<div class="tno-acc-modal-backdrop tno-logout-backdrop" id="tno-logout-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-logout-modal-title">
		<div class="tno-logout-modal">
			<div class="tno-logout-modal-head">
				<div class="tno-logout-icon-ring" aria-hidden="true">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
						<polyline points="16 17 21 12 16 7"/>
						<line x1="21" y1="12" x2="9" y2="12"/>
					</svg>
				</div>
			</div>
			<div class="tno-logout-modal-body">
				<h2 id="tno-logout-modal-title">از حساب کاربری خارج می‌شوید؟</h2>
				<p>با خارج شدن، سبد خرید فعلی شما ذخیره نخواهد شد. اما نگران نباشید؛ هر زمان که دوباره وارد شوید، می‌توانید خریدتان را از همان جا ادامه دهید.</p>
				<div class="tno-logout-modal-actions">
					<button type="button" class="tno-logout-cancel-btn" data-close-modal="tno-logout-modal">انصراف</button>
					<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="tno-logout-confirm-btn">خروج از حساب</a>
				</div>
			</div>
		</div>
	</div>

	<?php
	$tno_terms_url  = function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'terms' ) > 0
		? get_permalink( wc_get_page_id( 'terms' ) )
		: '#';
	$tno_brand_name = get_bloginfo( 'name' ) ?: 'ترندینو';
	?>
	<!-- Submit Product Review Modal (Shared between View Order & Reviews/Q&A) -->
	<div class="tno-acc-modal-backdrop" id="tno-review-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-review-modal-title">
		<div class="tno-acc-modal">
			<header class="tno-acc-modal-head">
				<h2 id="tno-review-modal-title">ثبت دیدگاه</h2>
				<button type="button" class="tno-acc-modal-close" data-close-modal="tno-review-modal" aria-label="بستن">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
			</header>

			<form id="tno-review-form" class="tno-review-form">
				<input type="hidden" name="product_id" id="tno-rev-product-id" value="">
				<input type="hidden" name="order_id" id="tno-rev-order-id" value="">
				<input type="hidden" name="rating" id="tno-rev-rating" value="0">

				<div class="tno-rev-product-preview">
					<div class="tno-rev-thumb-box">
						<img src="" alt="" id="tno-rev-product-img">
					</div>
					<div class="tno-rev-product-meta">
						<h3 class="tno-rev-product-title" id="tno-rev-product-title"></h3>
						<div class="tno-rev-rating-row">
							<span>امتیاز دهید به این محصول</span>
							<div class="tno-rev-stars" id="tno-rev-stars" role="radiogroup" aria-label="امتیاز محصول">
								<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
									<button type="button" class="tno-rev-star" data-star="<?php echo esc_attr( (string) $s ); ?>" aria-label="<?php echo esc_attr( (string) $s ); ?> ستاره">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
									</button>
								<?php endfor; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="tno-acc-field">
					<label for="tno-rev-comment">متن دیدگاه</label>
					<textarea id="tno-rev-comment" name="comment" rows="5" placeholder="تجربه و نظر خود درباره این محصول را با دیگران به اشتراک بگذارید..." required></textarea>
				</div>

				<div class="tno-modal-feedback" id="tno-rev-feedback" aria-live="polite"></div>

				<button type="submit" class="tno-acc-dark-btn tno-acc-dark-btn--full" id="tno-rev-submit">ثبت دیدگاه</button>

				<p class="tno-rev-terms">
					ثبت دیدگاه به معنای موافقت با <a href="<?php echo esc_url( $tno_terms_url ); ?>" target="_blank" rel="noopener">قوانین <?php echo esc_html( $tno_brand_name ); ?></a> می باشد.
				</p>
			</form>
		</div>
	</div>

	<!-- Review Submitted Success Modal -->
	<div class="tno-acc-modal-backdrop tno-rev-success-backdrop" id="tno-review-success-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-rev-success-title">
		<div class="tno-rev-success-modal">
			<div class="tno-rev-success-head">
				<div class="tno-rev-success-ring" aria-hidden="true">
					<span class="tno-rev-success-circle">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="20 6 9 17 4 12"></polyline>
						</svg>
					</span>
				</div>
			</div>
			<div class="tno-rev-success-body">
				<h2 id="tno-rev-success-title">دیدگاه شما با موفقیت ثبت شد.</h2>
				<p>از اینکه تجربه خود را با دیگران به اشتراک گذاشتید سپاسگزاریم.</p>
				<button type="button" class="tno-rev-success-btn" id="tno-rev-success-close">
					بازگشت تا <span id="tno-rev-success-count">۳</span> ثانیه دیگر
				</button>
			</div>
		</div>
	</div>
</div>