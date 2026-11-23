<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $order instanceof WC_Order ) {
	return;
}

$is_failed       = $order->has_status( 'failed' );
$order_number    = '#' . tno_fa_digits( (string) $order->get_order_number() );
$order_ts        = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : current_time( 'timestamp' );
$jalali          = tno_gregorian_to_jalali( $order_ts );
$formatted_date  = sprintf( '%s %s %s', tno_fa_digits( (string) $jalali['day'] ), $jalali['month_name'], tno_fa_digits( (string) $jalali['year'] ) );
$items_count     = $order->get_item_count();
$total_formatted = tno_format_toman( (float) $order->get_total() );
$payment_title   = $order->get_payment_method_title();

$home_label      = (string) tno_get_checkout_option( 'home_btn_label', 'بازگشت به صفحه اصلی' );
$success_sub     = (string) tno_get_checkout_option( 'success_subtitle', 'جهت دریافت جزئیات بیشتر لطفاً ایمیل یا پیامک خود را بررسی کنید' );
$failed_sub      = (string) tno_get_checkout_option( 'failed_subtitle', 'لطفا سفارش خودرا چک کرده و دوباره تلاش کنید' );

$invoice_enabled = (bool) tno_get_checkout_option( 'invoice_btn_enabled', 1 );
$invoice_label   = (string) tno_get_checkout_option( 'invoice_btn_label', 'دریافت فاکتور' );
$invoice_url_tpl = trim( (string) tno_get_checkout_option( 'invoice_btn_url', '' ) );
$invoice_url     = $invoice_url_tpl !== ''
	? str_replace( '{order_id}', (string) $order->get_id(), $invoice_url_tpl )
	: ( is_user_logged_in() ? $order->get_view_order_url() : '#print-invoice' );

$ticket          = tno_get_thankyou_coupon_ticket();
$social_enabled  = (bool) tno_get_checkout_option( 'social_section_enabled', 1 );
$social_title    = (string) tno_get_checkout_option( 'social_section_title', 'ما در شبکه های اجتماعی دنبال کنید' );

$social_cards = [
	'bale'      => [
		'enabled' => (bool) tno_get_checkout_option( 'social_bale_enabled', 1 ),
		'label'   => (string) tno_get_checkout_option( 'social_bale_label', 'بله' ),
		'url'     => (string) tno_get_checkout_option( 'social_bale_url', '#' ),
	],
	'instagram' => [
		'enabled' => (bool) tno_get_checkout_option( 'social_instagram_enabled', 1 ),
		'label'   => (string) tno_get_checkout_option( 'social_instagram_label', 'اینستاگرام' ),
		'url'     => (string) tno_get_checkout_option( 'social_instagram_url', '#' ),
	],
	'telegram'  => [
		'enabled' => (bool) tno_get_checkout_option( 'social_telegram_enabled', 1 ),
		'label'   => (string) tno_get_checkout_option( 'social_telegram_label', 'تلگرام' ),
		'url'     => (string) tno_get_checkout_option( 'social_telegram_url', '#' ),
	],
];
?>

<div class="tno-ty-page <?php echo $is_failed ? 'tno-ty-page--failed' : 'tno-ty-page--success'; ?>" dir="rtl">
	<!-- Top Gradient Banner & Floating Status Seal -->
	<div class="tno-ty-hero">
		<div class="tno-ty-badge-wrap">
			<?php if ( $is_failed ) : ?>
				<span class="tno-ty-status-icon tno-ty-status-icon--failed" aria-hidden="true">
					<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</span>
			<?php else : ?>
				<span class="tno-ty-status-icon tno-ty-status-icon--success" aria-hidden="true">
					<svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.1 1.7 2.7-.2 1.2 2.4 2.4 1.2-.2 2.7L22 12l-1.7 2.1.2 2.7-2.4 1.2-1.2 2.4-2.7-.2L12 22l-2.1-1.7-2.7.2-1.2-2.4-2.4-1.2.2-2.7L2 12l1.7-2.1-.2-2.7 2.4-1.2 1.2-2.4 2.7.2L12 2zm-1.1 12.6l5-5-1.4-1.4-3.6 3.6-1.5-1.5-1.4 1.4 2.9 2.9z"/></svg>
				</span>
			<?php endif; ?>
		</div>
	</div>

	<div class="tno-ty-container">
		<?php if ( $is_failed ) : ?>
			<h1 class="tno-ty-title">خرید شما متاسفانه <span class="tno-ty-accent--red">ناموفق</span> بود !</h1>
			<p class="tno-ty-subtitle"><?php echo esc_html( $failed_sub ); ?></p>
		<?php else : ?>
			<h1 class="tno-ty-title">خرید شما با <span class="tno-ty-accent--green">موفقیت</span> انجام شد</h1>
			<p class="tno-ty-subtitle"><?php echo esc_html( $success_sub ); ?></p>
		<?php endif; ?>

		<!-- Order Details Table -->
		<div class="tno-ty-summary-table">
			<div class="tno-ty-row">
				<span class="tno-ty-label">شماره سفارش</span>
				<span class="tno-ty-value" dir="ltr"><?php echo esc_html( $order_number ); ?></span>
			</div>
			<div class="tno-ty-row">
				<span class="tno-ty-label">تاریخ</span>
				<span class="tno-ty-value"><?php echo esc_html( $formatted_date ); ?></span>
			</div>
			<div class="tno-ty-row">
				<span class="tno-ty-label">تعداد سفارشات</span>
				<span class="tno-ty-value"><?php echo esc_html( tno_fa_digits( (string) $items_count ) . ' سفارش' ); ?></span>
			</div>

			<?php if ( ! $is_failed ) : ?>
				<div class="tno-ty-row">
					<span class="tno-ty-label">قیمت نهایی</span>
					<span class="tno-ty-value">
						<strong><?php echo esc_html( $total_formatted ); ?></strong>
						<small>تومان</small>
					</span>
				</div>
				<div class="tno-ty-row">
					<span class="tno-ty-label">روش پرداخت</span>
					<span class="tno-ty-value tno-ty-value--gateway">
						<span class="tno-ty-gw-dot" aria-hidden="true">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L4 6v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-4z"/></svg>
						</span>
						<span><?php echo esc_html( $payment_title ?: 'پرداخت اینترنتی' ); ?></span>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<!-- Next-Purchase Coupon Ticket (Success only, driven by Theme Settings) -->
		<?php if ( ! $is_failed && $ticket['enabled'] ) : ?>
			<div class="tno-ty-ticket-wrap">
				<button type="button" class="tno-ty-ticket" data-copy-coupon="<?php echo esc_attr( $ticket['code'] ); ?>" title="کلیک برای کپی کد تخفیف">
					<span class="tno-ty-ticket-stub">
						<span class="tno-ty-ticket-code" dir="ltr"><?php echo esc_html( $ticket['code'] ); ?></span>
					</span>
					<span class="tno-ty-ticket-divider" aria-hidden="true"></span>
					<span class="tno-ty-ticket-body">
						<strong class="tno-ty-ticket-percent"><?php echo esc_html( $ticket['percent'] ); ?></strong>
						<span class="tno-ty-ticket-caption"><?php echo esc_html( $ticket['label'] ); ?></span>
					</span>
				</button>
				<span class="tno-ty-copy-status" id="tno-ty-copy-status" aria-live="polite"></span>
			</div>
		<?php endif; ?>

		<!-- Action Buttons -->
		<div class="tno-ty-actions <?php echo $is_failed ? 'tno-ty-actions--single' : ''; ?>">
			<?php if ( ! $is_failed && $invoice_enabled ) : ?>
				<a
					href="<?php echo esc_url( $invoice_url ); ?>"
					class="tno-ty-btn tno-ty-btn--dark"
					<?php echo $invoice_url === '#print-invoice' ? 'data-action="print-invoice"' : ''; ?>
				>
					<?php echo esc_html( $invoice_label ); ?>
				</a>
			<?php endif; ?>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tno-ty-btn tno-ty-btn--outline">
				<?php echo esc_html( $home_label ); ?>
			</a>
		</div>

		<!-- Social Follow Cards (Success only, driven by Theme Settings) -->
		<?php
		$active_socials = array_filter( $social_cards, fn( array $s ): bool => $s['enabled'] && $s['url'] !== '' );
		if ( ! $is_failed && $social_enabled && ! empty( $active_socials ) ) :
			?>
			<section class="tno-ty-socials">
				<h2 class="tno-ty-socials-title"><?php echo esc_html( $social_title ); ?></h2>
				<div class="tno-ty-socials-grid">
					<?php foreach ( $active_socials as $slug => $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="tno-ty-social-card tno-ty-social-card--<?php echo esc_attr( $slug ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="tno-ty-social-icon">
								<?php if ( $slug === 'bale' ) : ?>
									<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L4 5v6.1c0 5.1 3.4 9.8 8 10.9 4.6-1.1 8-5.8 8-10.9V5l-8-3zm-1.1 13.2l-3.1-3.1 1.4-1.4 1.7 1.7 4-4 1.4 1.4-5.4 5.4z"/></svg>
								<?php elseif ( $slug === 'instagram' ) : ?>
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
								<?php else : ?>
									<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M21.5 4.2L2.9 11.4c-1.2.5-1.2 1.2-.2 1.5l4.8 1.5 11.1-7c.5-.3 1-.1.6.2l-9 8.1-.3 4.8c.5 0 .7-.2 1-.5l2.3-2.2 4.8 3.5c.9.5 1.5.2 1.7-.8l3.1-14.6c.3-1.3-.5-1.9-1.3-1.7z"/></svg>
								<?php endif; ?>
							</span>
							<span class="tno-ty-social-bottom">
								<strong class="tno-ty-social-name"><?php echo esc_html( $item['label'] ); ?></strong>
								<svg class="tno-ty-social-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</div>