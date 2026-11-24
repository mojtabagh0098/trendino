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
</div>