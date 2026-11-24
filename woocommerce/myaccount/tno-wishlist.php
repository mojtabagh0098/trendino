<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id       = get_current_user_id();
$product_ids   = tno_account_get_wishlist_ids( $user_id );
$products      = [];
$dashboard_url = wc_get_account_endpoint_url( 'dashboard' );
$shop_url      = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

if ( ! empty( $product_ids ) && function_exists( 'wc_get_product' ) ) {
	foreach ( $product_ids as $pid ) {
		$prod = wc_get_product( $pid );
		if ( $prod instanceof WC_Product && $prod->get_status() === 'publish' ) {
			$products[] = $prod;
		}
	}
}
?>

<div class="tno-acc-head tno-acc-head--mobile-bar">
	<a href="<?php echo esc_url( $dashboard_url ); ?>" class="tno-acc-back-mobile" aria-label="بازگشت">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1>علاقه مندی ها</h1>
</div>

<?php if ( empty( $products ) ) : ?>
	<section class="tno-acc-card tno-acc-empty-card">
		<div class="tno-addr-empty-icon" aria-hidden="true">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
		</div>
		<h2 class="tno-acc-empty-title">لیست علاقه‌مندی‌های شما خالی است!</h2>
		<p class="tno-acc-empty-sub">با کلیک روی آیکون قلب در صفحه هر محصول، آن را به علاقه‌مندی‌های خود اضافه کنید.</p>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="tno-acc-dark-btn" style="text-decoration:none;">مشاهده محصولات</a>
	</section>
<?php else : ?>
	<section class="tno-acc-card tno-acc-prod-section" id="tno-acc-prod-section">
		<div class="tno-acc-prod-grid" id="tno-acc-prod-grid" data-prod-context="wishlist">
			<?php foreach ( $products as $product ) : ?>
				<?php tno_account_render_product_card( $product, 'wishlist' ); ?>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="tno-acc-card tno-acc-empty-card" id="tno-acc-prod-empty" hidden>
		<div class="tno-addr-empty-icon" aria-hidden="true">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
		</div>
		<h2 class="tno-acc-empty-title">لیست علاقه‌مندی‌های شما خالی است!</h2>
		<p class="tno-acc-empty-sub">با کلیک روی آیکون قلب در صفحه هر محصول، آن را به علاقه‌مندی‌های خود اضافه کنید.</p>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="tno-acc-dark-btn" style="text-decoration:none;">مشاهده محصولات</a>
	</section>
<?php endif; ?>
