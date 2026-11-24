<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TNO_CLUB_OPTION_KEY = 'tno_club_settings';

/**
 * Default tiers matching the Customer Club design.
 *
 * @return array<int, array<string, mixed>>
 */
function tno_club_default_tiers(): array {
	return [
		[
			'id'              => 'tier_100',
			'enabled'         => 1,
			'title'           => 'کد تخفیف ۵ درصدی',
			'points_cost'     => 100,
			'discount_type'   => 'percent',
			'discount_amount' => 5,
			'min_spend'       => 0,
			'theme'           => 'dark',
			'custom_image'    => '',
		],
		[
			'id'              => 'tier_500',
			'enabled'         => 1,
			'title'           => 'کد تخفیف ۳۰ درصدی',
			'points_cost'     => 500,
			'discount_type'   => 'percent',
			'discount_amount' => 30,
			'min_spend'       => 0,
			'theme'           => 'blue',
			'custom_image'    => '',
		],
		[
			'id'              => 'tier_700',
			'enabled'         => 1,
			'title'           => 'کد تخفیف ۶۰ درصدی',
			'points_cost'     => 700,
			'discount_type'   => 'percent',
			'discount_amount' => 60,
			'min_spend'       => 0,
			'theme'           => 'pink',
			'custom_image'    => '',
		],
	];
}

/**
 * Default rules matching the Customer Club design.
 */
function tno_club_default_rules_text(): string {
	return implode( "\n", [
		'هر ۱۰۰,۰۰۰ تومان از مبلغ کالاها پس از تخفیف، بدون هزینه ارسال، ۱ امتیاز دارد.',
		'امتیاز خرید پس از پایان مهلت مرجوعی قابل استفاده می‌شود.',
		'کد یک‌بارمصرف و مخصوص حساب شماست و با کد دیگری ترکیب نمی‌شود.',
		'اگر کد بدون استفاده منقضی شود، امتیازش به موجودی برمی‌گردد.',
	] );
}

/**
 * Default settings for the Customer Club module.
 *
 * @return array<string, mixed>
 */
function tno_club_default_settings(): array {
	return [
		// General & UI Labels
		'enabled'               => 1,
		'page_title'            => 'باشگاه مشتریان',
		'balance_title'         => 'موجودی قابل استفاده',
		'offers_title'          => 'کدهای تخفیف من',
		'rules_title'           => 'قوانین کسب و استفاده از امتیاز',
		'ledger_title'          => 'گردش امتیاز',
		'coupons_title'         => 'کد تخفیف های من',
		'empty_coupons_text'    => 'هنوز کد تخفیفی دریافت نکرده اید',
		'empty_ledger_text'     => 'هنوز تراکنش امتیازی ثبت نشده است',

		// Earning Formula & Order Rules
		'points_mode'           => 'amount', // amount | product | hybrid
		'amount_step'           => 100000,
		'points_per_step'       => 1,
		'after_discount'        => 1,
		'exclude_shipping'      => 1,
		'award_status'          => 'completed', // completed | shipped | processing
		'return_period_days'    => 0,
		'revoke_on_cancel'      => 1,

		// Bonus Points
		'signup_bonus_points'   => 0,
		'review_bonus_points'   => 0,

		// Generated Coupon Rules
		'coupon_prefix'         => 'CLUB-',
		'coupon_expiry_days'    => 30,
		'coupon_single_use'     => 1,
		'coupon_individual_use' => 1,
		'coupon_bind_user'      => 1,
		'refund_on_expire'      => 1,

		// Rules & Reward Tiers
		'rules_lines'           => tno_club_default_rules_text(),
		'tiers'                 => tno_club_default_tiers(),
	];
}

/**
 * Retrieve all club settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function tno_get_club_settings(): array {
	$saved = get_option( TNO_CLUB_OPTION_KEY, [] );
	if ( ! is_array( $saved ) ) {
		$saved = [];
	}

	$merged = wp_parse_args( $saved, tno_club_default_settings() );
	if ( ! isset( $merged['tiers'] ) || ! is_array( $merged['tiers'] ) ) {
		$merged['tiers'] = tno_club_default_tiers();
	}

	return $merged;
}

/**
 * Retrieve a single club setting key.
 *
 * @param mixed $fallback
 * @return mixed
 */
function tno_get_club_option( string $key, $fallback = null ) {
	$settings = tno_get_club_settings();
	return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
}

/**
 * Register dedicated top-level WordPress admin menu for Customer Club.
 */
add_action( 'admin_menu', function (): void {
	add_menu_page(
		'تنظیمات باشگاه مشتریان',
		'باشگاه مشتریان',
		'manage_options',
		'tno-club-settings',
		'tno_render_club_admin_page',
		'dashicons-awards',
		59
	);
} );

/**
 * Enqueue Trendino admin assets & media uploader on Customer Club settings page.
 */
add_action( 'admin_enqueue_scripts', function ( string $hook ): void {
	if ( 'toplevel_page_tno-club-settings' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'tno-settings',
		get_template_directory_uri() . '/assets/css/admin-settings.css',
		[],
		file_exists( get_template_directory() . '/assets/css/admin-settings.css' )
			? (string) filemtime( get_template_directory() . '/assets/css/admin-settings.css' )
			: '1.0'
	);
} );

/**
 * Sanitize tier definitions from admin submission.
 *
 * @param mixed $raw_tiers
 * @return array<int, array<string, mixed>>
 */
function tno_club_sanitize_tiers( $raw_tiers ): array {
	if ( ! is_array( $raw_tiers ) ) {
		return [];
	}

	$clean = [];
	foreach ( $raw_tiers as $idx => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$title           = sanitize_text_field( (string) ( $item['title'] ?? '' ) );
		$points_cost     = max( 1, absint( $item['points_cost'] ?? 0 ) );
		$discount_type   = in_array( (string) ( $item['discount_type'] ?? 'percent' ), [ 'percent', 'fixed_cart' ], true )
			? (string) $item['discount_type']
			: 'percent';
		$discount_amount = max( 1, (float) ( $item['discount_amount'] ?? 0 ) );
		if ( $discount_type === 'percent' ) {
			$discount_amount = min( 100, $discount_amount );
		}

		if ( $title === '' ) {
			$title = $discount_type === 'percent'
				? sprintf( 'کد تخفیف %s درصدی', tno_fa_digits( (string) $discount_amount ) )
				: sprintf( 'کد تخفیف %s تومانی', tno_format_toman( $discount_amount ) );
		}

		$theme = in_array( (string) ( $item['theme'] ?? 'dark' ), [ 'dark', 'blue', 'pink' ], true )
			? (string) $item['theme']
			: 'dark';

		$raw_id = sanitize_key( (string) ( $item['id'] ?? '' ) );
		$id     = $raw_id !== '' ? $raw_id : 'tier_' . ( $idx + 1 ) . '_' . wp_generate_password( 4, false );

		$clean[] = [
			'id'              => $id,
			'enabled'         => ! empty( $item['enabled'] ) ? 1 : 0,
			'title'           => $title,
			'points_cost'     => $points_cost,
			'discount_type'   => $discount_type,
			'discount_amount' => $discount_amount,
			'min_spend'       => max( 0, absint( $item['min_spend'] ?? 0 ) ),
			'theme'           => $theme,
			'custom_image'    => esc_url_raw( trim( (string) ( $item['custom_image'] ?? '' ) ) ),
		];
	}

	return $clean;
}

/**
 * Save Customer Club General/Rules or Tiers settings.
 */
add_action( 'admin_post_tno_save_club_settings', function (): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'دسترسی غیرمجاز.' );
	}
	check_admin_referer( 'tno_save_club_settings' );

	$tab      = isset( $_POST['club_tab'] ) ? sanitize_key( wp_unslash( $_POST['club_tab'] ) ) : 'general';
	$current  = tno_get_club_settings();
	$defaults = tno_club_default_settings();

	if ( $tab === 'tiers' ) {
		$raw_tiers        = isset( $_POST['tiers'] ) ? wp_unslash( $_POST['tiers'] ) : [];
		$current['tiers'] = tno_club_sanitize_tiers( $raw_tiers );
	} else {
		$input = isset( $_POST['club'] ) && is_array( $_POST['club'] ) ? wp_unslash( $_POST['club'] ) : [];

		$checkboxes = [
			'enabled',
			'after_discount',
			'exclude_shipping',
			'revoke_on_cancel',
			'coupon_single_use',
			'coupon_individual_use',
			'coupon_bind_user',
			'refund_on_expire',
		];
		foreach ( $checkboxes as $cb ) {
			$current[ $cb ] = ! empty( $input[ $cb ] ) ? 1 : 0;
		}

		$texts = [
			'page_title',
			'balance_title',
			'offers_title',
			'rules_title',
			'ledger_title',
			'coupons_title',
			'empty_coupons_text',
			'empty_ledger_text',
			'coupon_prefix',
		];
		foreach ( $texts as $txt ) {
			$val             = sanitize_text_field( (string) ( $input[ $txt ] ?? $defaults[ $txt ] ) );
			$current[ $txt ] = $val !== '' ? $val : (string) $defaults[ $txt ];
		}

		$mode                   = sanitize_key( (string) ( $input['points_mode'] ?? 'amount' ) );
		$current['points_mode'] = in_array( $mode, [ 'amount', 'product', 'hybrid' ], true ) ? $mode : 'amount';

		$award_status            = sanitize_key( (string) ( $input['award_status'] ?? 'completed' ) );
		$current['award_status'] = in_array( $award_status, [ 'completed', 'shipped', 'processing' ], true ) ? $award_status : 'completed';

		$current['amount_step']         = max( 1000, absint( $input['amount_step'] ?? $defaults['amount_step'] ) );
		$current['points_per_step']     = max( 1, absint( $input['points_per_step'] ?? $defaults['points_per_step'] ) );
		$current['return_period_days']  = min( 365, absint( $input['return_period_days'] ?? 0 ) );
		$current['signup_bonus_points'] = absint( $input['signup_bonus_points'] ?? 0 );
		$current['review_bonus_points'] = absint( $input['review_bonus_points'] ?? 0 );
		$current['coupon_expiry_days']  = max( 1, min( 3650, absint( $input['coupon_expiry_days'] ?? $defaults['coupon_expiry_days'] ) ) );
		$current['rules_lines']         = sanitize_textarea_field( (string) ( $input['rules_lines'] ?? $defaults['rules_lines'] ) );
	}

	update_option( TNO_CLUB_OPTION_KEY, $current, false );

	wp_safe_redirect( add_query_arg( [
		'page'  => 'tno-club-settings',
		'tab'   => $tab,
		'saved' => '1',
	], admin_url( 'admin.php' ) ) );
	exit;
} );

/**
 * Manual admin points adjustment handler for individual users.
 */
add_action( 'admin_post_tno_club_adjust_user', function (): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'دسترسی غیرمجاز.' );
	}
	check_admin_referer( 'tno_club_adjust_user' );

	$user_id = absint( $_POST['user_id'] ?? 0 );
	$delta   = (int) ( $_POST['points_delta'] ?? 0 );
	$note    = sanitize_text_field( wp_unslash( $_POST['points_note'] ?? '' ) );

	if ( $user_id > 0 && $delta !== 0 && get_userdata( $user_id ) ) {
		$desc = $note !== '' ? $note : ( $delta > 0 ? 'افزایش دستی امتیاز توسط مدیر' : 'کسر دستی امتیاز توسط مدیر' );
		tno_club_adjust_user_points( $user_id, $delta, $desc, 'manual' );
	}

	wp_safe_redirect( add_query_arg( [
		'page'     => 'tno-club-settings',
		'tab'      => 'users',
		'adjusted' => '1',
		'user_id'  => $user_id,
	], admin_url( 'admin.php' ) ) );
	exit;
} );

/**
 * Render the dedicated WordPress admin page for Customer Club.
 */
function tno_render_club_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$opts   = tno_get_club_settings();
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
	$tabs   = [
		'general' => [ 'title' => 'تنظیمات و قوانین', 'icon' => 'dashicons-admin-settings', 'desc' => 'فرمول امتیازدهی، مهلت مرجوعی، محدودیت کدها و متن قوانین باشگاه.' ],
		'tiers'   => [ 'title' => 'بسته‌های کد تخفیف', 'icon' => 'dashicons-tickets-alt', 'desc' => 'مدیریت کارت‌های تخفیف قابل دریافت با امتیاز در صفحه باشگاه مشتریان.' ],
		'users'   => [ 'title' => 'مدیریت امتیاز کاربران', 'icon' => 'dashicons-groups', 'desc' => 'مشاهده موجودی، گردش امتیاز و افزایش یا کسر دستی امتیاز مشتریان.' ],
	];
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'general';
	}
	$current_tab = $tabs[ $tab ];
	?>
	<style>
		.tno-club-tiers-admin{display:flex;flex-direction:column;gap:16px}
		.tno-club-tier-admin-row{border:1px solid #e2e8f0;border-radius:14px;background:#f9fbfd;padding:18px 20px}
		.tno-club-tier-admin-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding-bottom:14px;margin-bottom:16px;border-bottom:1px solid #e8edf4}
		.tno-club-tier-admin-head strong{font-size:13px;color:#17213a}
		.tno-club-remove-tier{border:1px solid #fecdd3;background:#fff1f2;color:#be123c;border-radius:8px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer}
		.tno-club-add-tier{border:1px dashed #245cb1;background:#eff6ff;color:#1d4ed8;border-radius:12px;padding:13px 18px;font-size:12.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%}
		.tno-club-add-tier:hover{background:#dbeafe}
		.tno-club-users-table{width:100%;border-collapse:collapse;font-size:12px}
		.tno-club-users-table th,.tno-club-users-table td{padding:12px 14px;border-bottom:1px solid #edf0f5;text-align:right;vertical-align:middle}
		.tno-club-users-table th{background:#f8fafc;color:#475569;font-weight:700}
		.tno-club-badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
		.tno-club-badge--green{background:#e6f8f1;color:#00a878}
		.tno-club-badge--amber{background:#fff6e5;color:#d97706}
		.tno-club-inline-form{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
		.tno-club-inline-form input{min-height:36px!important;padding:5px 10px!important;font-size:12px!important}
		.tno-club-btn-sm{background:#132e5c;color:#fff;border:0;border-radius:8px;padding:8px 14px;font-size:11.5px;font-weight:700;cursor:pointer}
		.tno-club-search-bar{display:flex;gap:10px;padding:18px 24px;border-bottom:1px solid #edf0f5;align-items:center;flex-wrap:wrap}
		.tno-club-search-bar input{flex:1;min-width:220px;border:1px solid #dfe6ef;border-radius:9px;min-height:40px;padding:8px 12px}
	</style>

	<div class="tno-admin" dir="rtl">
		<header class="tno-admin-hero">
			<div>
				<div class="tno-eyebrow"><span class="tno-dot"></span> سیستم وفاداری ترندینو</div>
				<h1>مدیریت باشگاه مشتریان</h1>
				<p>تنظیم قوانین امتیازدهی، تعریف بسته‌های کد تخفیف و مدیریت موجودی امتیاز کاربران.</p>
			</div>
			<a class="tno-site-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'tno-club' ) ); ?>" target="_blank" rel="noopener">
				مشاهده صفحه باشگاه <span aria-hidden="true">↗</span>
			</a>
		</header>

		<div class="tno-workspace">
			<aside class="tno-sidebar">
				<div class="tno-sidebar-label">بخش‌های باشگاه</div>
				<nav aria-label="منوی تنظیمات باشگاه مشتریان">
					<?php foreach ( $tabs as $slug => $item ) : ?>
						<a
							class="tno-nav-item <?php echo $slug === $tab ? 'is-active' : ''; ?>"
							<?php echo $slug === $tab ? 'aria-current="page"' : ''; ?>
							href="<?php echo esc_url( add_query_arg( [ 'page' => 'tno-club-settings', 'tab' => $slug ], admin_url( 'admin.php' ) ) ); ?>"
						>
							<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
							<span><?php echo esc_html( $item['title'] ); ?></span>
							<span class="tno-arrow">‹</span>
						</a>
					<?php endforeach; ?>
				</nav>
				<div class="tno-sidebar-help">
					<span class="dashicons dashicons-lightbulb"></span>
					<strong>یکپارچه با ووکامرس</strong>
					<p>کدهای تخفیف دریافتی به صورت خودکار به عنوان کوپن واقعی ووکامرس برای همان کاربر ساخته می‌شوند.</p>
				</div>
			</aside>

			<main class="tno-main">
				<div class="tno-heading">
					<div>
						<span class="tno-breadcrumb">باشگاه مشتریان / <?php echo esc_html( $current_tab['title'] ); ?></span>
						<h2><?php echo esc_html( $current_tab['title'] ); ?></h2>
						<p><?php echo esc_html( $current_tab['desc'] ); ?></p>
					</div>
					<span class="tno-heading-icon dashicons <?php echo esc_attr( $current_tab['icon'] ); ?>"></span>
				</div>

				<?php if ( isset( $_GET['saved'] ) ) : ?>
					<div class="tno-alert success" role="status">
						<span class="dashicons dashicons-yes-alt"></span> تنظیمات باشگاه مشتریان با موفقیت ذخیره شد.
					</div>
				<?php endif; ?>

				<?php if ( isset( $_GET['adjusted'] ) ) : ?>
					<div class="tno-alert success" role="status">
						<span class="dashicons dashicons-yes-alt"></span> امتیاز کاربر با موفقیت به‌روزرسانی و در گردش امتیاز ثبت شد.
					</div>
				<?php endif; ?>

				<?php if ( $tab === 'general' ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="tno_save_club_settings">
						<input type="hidden" name="club_tab" value="general">
						<?php wp_nonce_field( 'tno_save_club_settings' ); ?>

						<section class="tno-card">
							<div class="tno-card-head">
								<div><span class="tno-card-indicator"></span><h3>وضعیت و فرمول کسب امتیاز</h3></div>
								<span class="tno-card-caption">منطق امتیازدهی سفارش‌ها</span>
							</div>
							<div class="tno-fields">
								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-enabled">فعال‌سازی باشگاه مشتریان</label>
										<p>با غیرفعال کردن، اعطای خودکار امتیاز و دریافت کد جدید متوقف می‌شود.</p>
									</div>
									<label class="tno-switch">
										<input id="club-enabled" type="checkbox" name="club[enabled]" value="1" <?php checked( 1, (int) $opts['enabled'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-points-mode">روش محاسبه امتیاز سفارش</label>
										<p>می‌توانید بر اساس مبلغ خرید، فیلد امتیاز هر محصول، یا هر دو امتیاز دهید.</p>
									</div>
									<select id="club-points-mode" name="club[points_mode]">
										<option value="amount" <?php selected( (string) $opts['points_mode'], 'amount' ); ?>>بر اساس مبلغ سفارش (مطابق فرمول زیر)</option>
										<option value="product" <?php selected( (string) $opts['points_mode'], 'product' ); ?>>بر اساس امتیاز اختصاصی هر محصول</option>
										<option value="hybrid" <?php selected( (string) $opts['points_mode'], 'hybrid' ); ?>>ترکیبی (امتیاز محصول، در نبود آن مبلغ سفارش)</option>
									</select>
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-award-status">وضعیت سفارش برای اعطای امتیاز</label>
										<p>سفارش در چه وضعیتی امتیاز را برای مشتری ثبت کند.</p>
									</div>
									<select id="club-award-status" name="club[award_status]">
										<option value="completed" <?php selected( (string) $opts['award_status'], 'completed' ); ?>>تحویل شده (Completed)</option>
										<option value="shipped" <?php selected( (string) $opts['award_status'], 'shipped' ); ?>>ارسال شده (Shipped)</option>
										<option value="processing" <?php selected( (string) $opts['award_status'], 'processing' ); ?>>در حال پردازش (Processing)</option>
									</select>
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-amount-step">مبلغ مبنا برای هر امتیاز (تومان)</label>
										<p>پیش‌فرض: ۱۰۰,۰۰۰ تومان</p>
									</div>
									<input id="club-amount-step" type="number" min="1000" step="1000" name="club[amount_step]" value="<?php echo esc_attr( (string) $opts['amount_step'] ); ?>">
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-points-per-step">تعداد امتیاز به ازای هر مبلغ مبنا</label>
										<p>پیش‌فرض: ۱ امتیاز به ازای هر ۱۰۰,۰۰۰ تومان</p>
									</div>
									<input id="club-points-per-step" type="number" min="1" name="club[points_per_step]" value="<?php echo esc_attr( (string) $opts['points_per_step'] ); ?>">
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-return-days">مهلت مرجوعی (روز)</label>
										<p>امتیاز پس از گذشت این تعداد روز به موجودی قابل استفاده اضافه می‌شود (۰ = فوری).</p>
									</div>
									<input id="club-return-days" type="number" min="0" max="365" name="club[return_period_days]" value="<?php echo esc_attr( (string) $opts['return_period_days'] ); ?>">
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-signup-bonus">امتیاز هدیه ثبت‌نام</label>
										<p>امتیاز خوش‌آمدگویی برای کاربران تازه‌ثبت‌نام‌کرده (۰ = غیرفعال).</p>
									</div>
									<input id="club-signup-bonus" type="number" min="0" name="club[signup_bonus_points]" value="<?php echo esc_attr( (string) $opts['signup_bonus_points'] ); ?>">
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-review-bonus">امتیاز ثبت دیدگاه تاییدشده</label>
										<p>امتیاز تشویقی پس از تایید دیدگاه کاربر برای محصول (۰ = غیرفعال).</p>
									</div>
									<input id="club-review-bonus" type="number" min="0" name="club[review_bonus_points]" value="<?php echo esc_attr( (string) $opts['review_bonus_points'] ); ?>">
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-after-discount">محاسبه مبلغ کالاها پس از تخفیف</label>
										<p>در صورت فعال بودن، مبلغ پرداختی پس از کسر تخفیف مبنای امتیاز است.</p>
									</div>
									<label class="tno-switch">
										<input id="club-after-discount" type="checkbox" name="club[after_discount]" value="1" <?php checked( 1, (int) $opts['after_discount'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-exclude-shipping">حذف هزینه ارسال از محاسبه امتیاز</label>
										<p>هزینه ارسال در محاسبه امتیاز لحاظ نمی‌شود.</p>
									</div>
									<label class="tno-switch">
										<input id="club-exclude-shipping" type="checkbox" name="club[exclude_shipping]" value="1" <?php checked( 1, (int) $opts['exclude_shipping'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-revoke-cancel">کسر امتیاز در صورت لغو یا مرجوعی سفارش</label>
										<p>اگر سفارش پس از دریافت امتیاز لغو یا مسترد شود، امتیاز مربوطه کسر می‌شود.</p>
									</div>
									<label class="tno-switch">
										<input id="club-revoke-cancel" type="checkbox" name="club[revoke_on_cancel]" value="1" <?php checked( 1, (int) $opts['revoke_on_cancel'] ); ?>>
										<span></span>
									</label>
								</div>
							</div>
						</section>

						<section class="tno-card">
							<div class="tno-card-head">
								<div><span class="tno-card-indicator"></span><h3>محدودیت و انقضای کدهای تخفیف</h3></div>
								<span class="tno-card-caption">کوپن‌های ووکامرس</span>
							</div>
							<div class="tno-fields">
								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-coupon-prefix">پیشوند کد تخفیف</label>
									</div>
									<input id="club-coupon-prefix" type="text" dir="ltr" name="club[coupon_prefix]" value="<?php echo esc_attr( (string) $opts['coupon_prefix'] ); ?>">
								</div>

								<div class="tno-field">
									<div class="tno-field-copy">
										<label for="club-coupon-expiry">مدت اعتبار کد تخفیف (روز)</label>
									</div>
									<input id="club-coupon-expiry" type="number" min="1" max="3650" name="club[coupon_expiry_days]" value="<?php echo esc_attr( (string) $opts['coupon_expiry_days'] ); ?>">
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-single-use">کد یک‌بارمصرف باشد</label>
										<p>هر کد تخفیف فقط یک بار قابل استفاده در سبد خرید است.</p>
									</div>
									<label class="tno-switch">
										<input id="club-single-use" type="checkbox" name="club[coupon_single_use]" value="1" <?php checked( 1, (int) $opts['coupon_single_use'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-individual-use">عدم ترکیب با کد تخفیف دیگر</label>
										<p>کدهای باشگاه با کوپن‌های دیگر در یک سفارش ترکیب نمی‌شوند.</p>
									</div>
									<label class="tno-switch">
										<input id="club-individual-use" type="checkbox" name="club[coupon_individual_use]" value="1" <?php checked( 1, (int) $opts['coupon_individual_use'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-bind-user">مخصوص حساب کاربری دریافت‌کننده</label>
										<p>فقط همان کاربر می‌تواند از کد تخفیف تولیدشده استفاده کند.</p>
									</div>
									<label class="tno-switch">
										<input id="club-bind-user" type="checkbox" name="club[coupon_bind_user]" value="1" <?php checked( 1, (int) $opts['coupon_bind_user'] ); ?>>
										<span></span>
									</label>
								</div>

								<div class="tno-field tno-field-toggle">
									<div class="tno-field-copy">
										<label for="club-refund-expire">بازگشت امتیاز در صورت انقضای کد بدون استفاده</label>
										<p>اگر مهلت کد تمام شود و استفاده نشده باشد، امتیاز به موجودی کاربر برمی‌گردد.</p>
									</div>
									<label class="tno-switch">
										<input id="club-refund-expire" type="checkbox" name="club[refund_on_expire]" value="1" <?php checked( 1, (int) $opts['refund_on_expire'] ); ?>>
										<span></span>
									</label>
								</div>
							</div>
						</section>

						<section class="tno-card">
							<div class="tno-card-head">
								<div><span class="tno-card-indicator"></span><h3>متون و قوانین صفحه باشگاه مشتریان</h3></div>
								<span class="tno-card-caption">سفارشی‌سازی ظاهر</span>
							</div>
							<div class="tno-fields">
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-page-title">عنوان اصلی صفحه</label></div>
									<input id="club-page-title" type="text" name="club[page_title]" value="<?php echo esc_attr( (string) $opts['page_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-balance-title">عنوان باکس موجودی</label></div>
									<input id="club-balance-title" type="text" name="club[balance_title]" value="<?php echo esc_attr( (string) $opts['balance_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-offers-title">عنوان باکس بسته‌های تخفیف</label></div>
									<input id="club-offers-title" type="text" name="club[offers_title]" value="<?php echo esc_attr( (string) $opts['offers_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-rules-title">عنوان باکس قوانین</label></div>
									<input id="club-rules-title" type="text" name="club[rules_title]" value="<?php echo esc_attr( (string) $opts['rules_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-ledger-title">عنوان باکس گردش امتیاز</label></div>
									<input id="club-ledger-title" type="text" name="club[ledger_title]" value="<?php echo esc_attr( (string) $opts['ledger_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-coupons-title">عنوان باکس کدهای دریافت‌شده</label></div>
									<input id="club-coupons-title" type="text" name="club[coupons_title]" value="<?php echo esc_attr( (string) $opts['coupons_title'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-empty-coupons">متن زمانی که کدی دریافت نشده</label></div>
									<input id="club-empty-coupons" type="text" name="club[empty_coupons_text]" value="<?php echo esc_attr( (string) $opts['empty_coupons_text'] ); ?>">
								</div>
								<div class="tno-field">
									<div class="tno-field-copy"><label for="club-empty-ledger">متن زمانی که گردش امتیازی ثبت نشده</label></div>
									<input id="club-empty-ledger" type="text" name="club[empty_ledger_text]" value="<?php echo esc_attr( (string) $opts['empty_ledger_text'] ); ?>">
								</div>
								<div class="tno-field tno-field-wide">
									<div class="tno-field-copy">
										<label for="club-rules-lines">متن قوانین کسب و استفاده از امتیاز (هر خط یک مورد)</label>
										<p>به صورت شماره‌گذاری فارسی (۱، ۲، ۳...) در کارت قوانین نمایش داده می‌شوند.</p>
									</div>
									<textarea id="club-rules-lines" name="club[rules_lines]" rows="5"><?php echo esc_textarea( (string) $opts['rules_lines'] ); ?></textarea>
								</div>
							</div>
						</section>

						<div class="tno-savebar">
							<div><strong>ذخیره تنظیمات باشگاه</strong><span>تغییرات بلافاصله در صفحه باشگاه مشتریان و سفارش‌های جدید اعمال می‌شوند.</span></div>
							<button class="tno-save" type="submit"><span class="dashicons dashicons-saved"></span> ذخیره تنظیمات</button>
						</div>
					</form>

				<?php elseif ( $tab === 'tiers' ) : ?>
					<?php $tiers = is_array( $opts['tiers'] ) ? $opts['tiers'] : []; ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="tno-club-tiers-form">
						<input type="hidden" name="action" value="tno_save_club_settings">
						<input type="hidden" name="club_tab" value="tiers">
						<?php wp_nonce_field( 'tno_save_club_settings' ); ?>

						<section class="tno-card">
							<div class="tno-card-head">
								<div><span class="tno-card-indicator"></span><h3>بسته‌های کد تخفیف باشگاه مشتریان</h3></div>
								<span class="tno-card-caption">تعریف کارت‌های جایزه</span>
							</div>
							<div style="padding: 23px 24px;">
								<div class="tno-club-tiers-admin" id="tno-club-tiers-list">
									<?php foreach ( $tiers as $index => $tier ) : ?>
										<?php tno_club_render_admin_tier_row( (int) $index, $tier ); ?>
									<?php endforeach; ?>
								</div>
								<div style="margin-top: 16px;">
									<button type="button" class="tno-club-add-tier" id="tno-club-add-tier">+ افزودن بسته تخفیف جدید</button>
								</div>
							</div>
						</section>

						<div class="tno-savebar">
							<div><strong>ذخیره بسته‌های تخفیف</strong><span>بسته‌های فعال به ترتیب در گرید «کدهای تخفیف من» نمایش داده می‌شوند.</span></div>
							<button class="tno-save" type="submit"><span class="dashicons dashicons-saved"></span> ذخیره بسته‌ها</button>
						</div>
					</form>

					<template id="tno-club-tier-template">
						<?php
						tno_club_render_admin_tier_row( '__IDX__', [
							'id'              => '',
							'enabled'         => 1,
							'title'           => 'کد تخفیف ۱۰ درصدی',
							'points_cost'     => 200,
							'discount_type'   => 'percent',
							'discount_amount' => 10,
							'min_spend'       => 0,
							'theme'           => 'dark',
							'custom_image'    => '',
						] );
						?>
					</template>

					<script>
					document.addEventListener('DOMContentLoaded', () => {
						const list = document.getElementById('tno-club-tiers-list');
						const addBtn = document.getElementById('tno-club-add-tier');
						const tpl = document.getElementById('tno-club-tier-template');
						let nextIndex = list.querySelectorAll('.tno-club-tier-admin-row').length;

						function bindMedia(scope) {
							scope.querySelectorAll('.tno-media').forEach(container => {
								const input = container.querySelector('input');
								const img = container.querySelector('img');
								const preview = () => { img.src = input.value; img.hidden = !input.value; };
								container.querySelector('.tno-media-select').addEventListener('click', () => {
									if (!window.wp || !wp.media) return;
									const frame = wp.media({ title: 'انتخاب تصویر بنر', button: { text: 'استفاده از تصویر' }, library: { type: 'image' }, multiple: false });
									frame.on('select', () => { input.value = frame.state().get('selection').first().toJSON().url; preview(); });
									frame.open();
								});
								container.querySelector('.tno-media-clear').addEventListener('click', () => { input.value = ''; preview(); });
								input.addEventListener('change', preview);
							});
						}

						bindMedia(list);

						addBtn.addEventListener('click', () => {
							const html = tpl.innerHTML.replaceAll('__IDX__', String(nextIndex));
							const wrapper = document.createElement('div');
							wrapper.innerHTML = html.trim();
							const row = wrapper.firstElementChild;
							list.appendChild(row);
							bindMedia(row);
							nextIndex += 1;
						});

						list.addEventListener('click', (e) => {
							const btn = e.target.closest('.tno-club-remove-tier');
							if (!btn) return;
							btn.closest('.tno-club-tier-admin-row').remove();
						});
					});
					</script>

				<?php elseif ( $tab === 'users' ) : ?>
					<?php
					$search = isset( $_GET['s_user'] ) ? sanitize_text_field( wp_unslash( $_GET['s_user'] ) ) : '';
					$u_args = [
						'number'  => 30,
						'orderby' => 'ID',
						'order'   => 'DESC',
					];
					if ( $search !== '' ) {
						$u_args['search']         = '*' . $search . '*';
						$u_args['search_columns'] = [ 'user_login', 'user_nicename', 'user_email', 'display_name' ];
					}
					$users = get_users( $u_args );
					?>
					<section class="tno-card">
						<div class="tno-card-head">
							<div><span class="tno-card-indicator"></span><h3>موجودی و تعدیل امتیاز مشتریان</h3></div>
							<span class="tno-card-caption">افزایش یا کسر دستی</span>
						</div>

						<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="tno-club-search-bar">
							<input type="hidden" name="page" value="tno-club-settings">
							<input type="hidden" name="tab" value="users">
							<input type="search" name="s_user" value="<?php echo esc_attr( $search ); ?>" placeholder="جستجو بر اساس نام، شماره موبایل یا ایمیل...">
							<button type="submit" class="tno-club-btn-sm">جستجو</button>
							<?php if ( $search !== '' ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=tno-club-settings&tab=users' ) ); ?>" class="button">نمایش همه</a>
							<?php endif; ?>
						</form>

						<div style="overflow-x:auto;">
							<table class="tno-club-users-table">
								<thead>
									<tr>
										<th>کاربر</th>
										<th>شماره / ایمیل</th>
										<th>موجودی قابل استفاده</th>
										<th>در انتظار مهلت مرجوعی</th>
										<th>تغییر دستی امتیاز</th>
									</tr>
								</thead>
								<tbody>
									<?php if ( empty( $users ) ) : ?>
										<tr><td colspan="5">کاربری یافت نشد.</td></tr>
									<?php else : ?>
										<?php foreach ( $users as $u ) :
											tno_club_sync_user_state( (int) $u->ID );
											$balance = tno_club_get_user_points( (int) $u->ID );
											$pending = tno_club_get_user_pending_points( (int) $u->ID );
											$phone   = (string) get_user_meta( $u->ID, 'billing_phone', true ) ?: $u->user_login;
											$name    = trim( $u->first_name . ' ' . $u->last_name ) ?: $u->display_name;
											?>
											<tr>
												<td><strong><?php echo esc_html( $name ); ?></strong></td>
												<td dir="ltr" style="text-align:right;"><?php echo esc_html( $phone ); ?></td>
												<td><span class="tno-club-badge tno-club-badge--green"><?php echo esc_html( tno_fa_digits( (string) $balance ) ); ?> امتیاز</span></td>
												<td>
													<?php if ( $pending > 0 ) : ?>
														<span class="tno-club-badge tno-club-badge--amber"><?php echo esc_html( tno_fa_digits( (string) $pending ) ); ?> امتیاز</span>
													<?php else : ?>
														—
													<?php endif; ?>
												</td>
												<td>
													<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="tno-club-inline-form">
														<input type="hidden" name="action" value="tno_club_adjust_user">
														<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $u->ID ); ?>">
														<?php wp_nonce_field( 'tno_club_adjust_user' ); ?>
														<input type="number" name="points_delta" required placeholder="مثلاً 50 یا -20" style="width:120px;">
														<input type="text" name="points_note" placeholder="توضیح گردش امتیاز (اختیاری)" style="width:190px;">
														<button type="submit" class="tno-club-btn-sm">اعمال</button>
													</form>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</section>
				<?php endif; ?>
			</main>
		</div>

		<footer class="tno-admin-footer">ترندینو <span>•</span> باشگاه مشتریان</footer>
	</div>
	<?php
}

/**
 * Render a single tier editor row for admin UI.
 *
 * @param int|string           $index
 * @param array<string, mixed> $tier
 */
function tno_club_render_admin_tier_row( $index, array $tier ): void {
	$prefix = "tiers[{$index}]";
	$img    = (string) ( $tier['custom_image'] ?? '' );
	?>
	<div class="tno-club-tier-admin-row">
		<div class="tno-club-tier-admin-head">
			<strong>بسته تخفیف: <?php echo esc_html( (string) ( $tier['title'] ?? '' ) ); ?></strong>
			<div style="display:flex;align-items:center;gap:12px;">
				<label class="tno-switch" title="فعال/غیرفعال">
					<input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[enabled]" value="1" <?php checked( 1, (int) ( $tier['enabled'] ?? 1 ) ); ?>>
					<span></span>
				</label>
				<button type="button" class="tno-club-remove-tier">حذف بسته</button>
			</div>
		</div>
		<input type="hidden" name="<?php echo esc_attr( $prefix ); ?>[id]" value="<?php echo esc_attr( (string) ( $tier['id'] ?? '' ) ); ?>">
		<div class="tno-fields" style="padding:0;">
			<div class="tno-field">
				<div class="tno-field-copy"><label>عنوان بسته</label></div>
				<input type="text" name="<?php echo esc_attr( $prefix ); ?>[title]" value="<?php echo esc_attr( (string) ( $tier['title'] ?? '' ) ); ?>" placeholder="مثال: کد تخفیف ۵ درصدی">
			</div>
			<div class="tno-field">
				<div class="tno-field-copy"><label>امتیاز مورد نیاز</label></div>
				<input type="number" min="1" name="<?php echo esc_attr( $prefix ); ?>[points_cost]" value="<?php echo esc_attr( (string) ( $tier['points_cost'] ?? 100 ) ); ?>">
			</div>
			<div class="tno-field">
				<div class="tno-field-copy"><label>نوع تخفیف ووکامرس</label></div>
				<select name="<?php echo esc_attr( $prefix ); ?>[discount_type]">
					<option value="percent" <?php selected( (string) ( $tier['discount_type'] ?? 'percent' ), 'percent' ); ?>>درصدی (Percent)</option>
					<option value="fixed_cart" <?php selected( (string) ( $tier['discount_type'] ?? 'percent' ), 'fixed_cart' ); ?>>مبلغ ثابت سبد خرید (تومان)</option>
				</select>
			</div>
			<div class="tno-field">
				<div class="tno-field-copy"><label>مقدار تخفیف (درصد یا تومان)</label></div>
				<input type="number" min="1" name="<?php echo esc_attr( $prefix ); ?>[discount_amount]" value="<?php echo esc_attr( (string) ( $tier['discount_amount'] ?? 5 ) ); ?>">
			</div>
			<div class="tno-field">
				<div class="tno-field-copy"><label>حداقل مبلغ سبد خرید (تومان، ۰ = بدون محدودیت)</label></div>
				<input type="number" min="0" name="<?php echo esc_attr( $prefix ); ?>[min_spend]" value="<?php echo esc_attr( (string) ( $tier['min_spend'] ?? 0 ) ); ?>">
			</div>
			<div class="tno-field">
				<div class="tno-field-copy"><label>رنگ بنر کارت</label></div>
				<select name="<?php echo esc_attr( $prefix ); ?>[theme]">
					<option value="dark" <?php selected( (string) ( $tier['theme'] ?? 'dark' ), 'dark' ); ?>>مشکی با هاله قرمز (پیش‌فرض ۱۰۰ امتیاز)</option>
					<option value="blue" <?php selected( (string) ( $tier['theme'] ?? 'dark' ), 'blue' ); ?>>آبی آسمانی (پیش‌فرض ۵۰۰ امتیاز)</option>
					<option value="pink" <?php selected( (string) ( $tier['theme'] ?? 'dark' ), 'pink' ); ?>>صورتی ملایم (پیش‌فرض ۷۰۰ امتیاز)</option>
				</select>
			</div>
			<div class="tno-field tno-field-wide">
				<div class="tno-field-copy">
					<label>تصویر سفارشی بنر (اختیاری)</label>
					<p>در صورت خالی بودن، گرافیک اختصاصی کارت باشگاه با هاله رنگی و باکس هدیه نمایش داده می‌شود.</p>
				</div>
				<div class="tno-media">
					<input type="url" name="<?php echo esc_attr( $prefix ); ?>[custom_image]" value="<?php echo esc_attr( $img ); ?>" placeholder="https://">
					<button class="tno-media-select" type="button">انتخاب تصویر</button>
					<button class="tno-media-clear" type="button">حذف</button>
					<?php if ( $img !== '' ) : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="پیش‌نمایش">
					<?php else : ?>
						<img alt="پیش‌نمایش" hidden>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
