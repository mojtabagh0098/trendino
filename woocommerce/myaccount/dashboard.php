<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user         = wp_get_current_user();
$full_name    = trim( $user->first_name . ' ' . $user->last_name );
$display_name = $full_name !== '' ? $full_name : $user->display_name;
$brand_name   = get_bloginfo( 'name' ) ?: 'ترندینو';

$bundle       = tno_account_get_customer_orders_bundle( $user->ID );
$counts       = $bundle['counts'];
$orders_url   = wc_get_account_endpoint_url( 'orders' );

$logo_url     = get_template_directory_uri() . '/assets/img/logo-header.png';
$custom_logo  = get_theme_mod( 'custom_logo' );
if ( $custom_logo ) {
	$custom_src = wp_get_attachment_image_url( (int) $custom_logo, 'full' );
	if ( $custom_src ) {
		$logo_url = $custom_src;
	}
}
?>

<!-- Welcome Banner -->
<section class="tno-dash-banner">
	<div class="tno-dash-banner-text">
		<h2 class="tno-dash-banner-title">
			سلام <?php echo esc_html( $display_name ); ?> عزیز خوش آمدی به داشبورد حساب کاربری <?php echo esc_html( $brand_name ); ?>
		</h2>
		<p class="tno-dash-banner-sub">خریدها، امتیازها و تجربه‌هایت؛ همه در یک جا.</p>
	</div>
	<div class="tno-dash-banner-brand" aria-hidden="true">
		<span class="tno-dash-ring tno-dash-ring--outer">
			<span class="tno-dash-ring tno-dash-ring--inner">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>">
			</span>
		</span>
	</div>
</section>

<div class="tno-acc-head">
	<h1>داشبورد</h1>
</div>

<!-- My Orders Summary Card -->
<section class="tno-acc-card">
	<h2 class="tno-acc-card-title">سفارش های من</h2>

	<div class="tno-stat-grid">
		<a href="<?php echo esc_url( add_query_arg( 'status', 'all', $orders_url ) ); ?>" class="tno-stat-pill">
			<span class="tno-stat-right">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
				<span class="tno-stat-label">سفارشات</span>
			</span>
			<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['all'] ) ); ?> سفارش</strong>
		</a>

		<a href="<?php echo esc_url( add_query_arg( 'status', 'processing', $orders_url ) ); ?>" class="tno-stat-pill">
			<span class="tno-stat-right">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
				<span class="tno-stat-label">در حال پردازش</span>
			</span>
			<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['processing'] ) ); ?> سفارش</strong>
		</a>

		<a href="<?php echo esc_url( add_query_arg( 'status', 'shipped', $orders_url ) ); ?>" class="tno-stat-pill">
			<span class="tno-stat-right">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
				<span class="tno-stat-label">ارسال شده</span>
			</span>
			<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['shipped'] ) ); ?> سفارش</strong>
		</a>

		<a href="<?php echo esc_url( add_query_arg( 'status', 'completed', $orders_url ) ); ?>" class="tno-stat-pill">
			<span class="tno-stat-right">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
				<span class="tno-stat-label">تحویل شده</span>
			</span>
			<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['completed'] ) ); ?> سفارش</strong>
		</a>
	</div>
</section>