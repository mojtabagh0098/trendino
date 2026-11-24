<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue My Account styles & scripts strictly on the account page.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}

	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	$css_ver = file_exists( $theme_path . '/assets/css/my-account.css' )
		? (string) filemtime( $theme_path . '/assets/css/my-account.css' )
		: '1.0.0';

	$js_ver = file_exists( $theme_path . '/assets/js/my-account.js' )
		? (string) filemtime( $theme_path . '/assets/js/my-account.js' )
		: '1.0.0';

	wp_enqueue_style( 'tno-my-account', $theme_uri . '/assets/css/my-account.css', [], $css_ver );
	wp_enqueue_script( 'tno-my-account', $theme_uri . '/assets/js/my-account.js', [], $js_ver, true );

	wp_localize_script( 'tno-my-account', 'tnoAccountConfig', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tno_account_nonce' ),
		'i18n'    => [
			'selectRating'   => 'لطفاً امتیاز خود را انتخاب کنید.',
			'enterReview'    => 'لطفاً متن دیدگاه خود را وارد کنید.',
			'reviewSaved'    => 'دیدگاه شما با موفقیت ثبت شد و پس از بررسی نمایش داده می‌شود.',
			'fillAddress'    => 'لطفاً استان، شهر و نشانی پستی را کامل وارد کنید.',
			'confirmDelete'  => 'آیا از حذف این آدرس مطمئن هستید؟',
			'errorGeneric'   => 'خطایی رخ داد، لطفاً دوباره تلاش کنید.',
			'clubClaiming'   => 'در حال صدور کد...',
			'clubClaimBtn'   => 'دریافت کد تخفیف',
			'clubNoPoints'   => 'امتیاز کافی ندارید',
			'clubCopied'     => 'کد تخفیف کپی شد!',
			'savingAccount'  => 'در حال ذخیره...',
			'saveAccountBtn' => 'ذخیره اطلاعات',
			'accountSaved'   => 'اطلاعات حساب کاربری با موفقیت ذخیره شد.',
			'addingToCart'   => 'در حال افزودن...',
			'addedToCart'    => 'به سبد اضافه شد ✓',
			'addToCartBtn'   => 'افزودن به سبد خرید',
		],
	] );
} );

/**
 * Register custom "ارسال شده" (wc-shipped) order status if not already registered by a plugin.
 */
add_action( 'init', function (): void {
	if ( ! isset( $GLOBALS['wp_post_statuses']['wc-shipped'] ) ) {
		register_post_status( 'wc-shipped', [
			'label'                     => 'ارسال شده',
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'ارسال شده (%s)', 'ارسال شده (%s)' ),
		] );
	}

	// Register additional endpoints matching sidebar navigation
	add_rewrite_endpoint( 'tno-reviews', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'tno-wishlist', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'tno-recent-views', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'tno-club', EP_ROOT | EP_PAGES );
} );

add_filter( 'wc_order_statuses', function ( array $statuses ): array {
	if ( isset( $statuses['wc-shipped'] ) ) {
		return $statuses;
	}

	$ordered = [];
	foreach ( $statuses as $key => $label ) {
		$ordered[ $key ] = $label;
		if ( $key === 'wc-processing' ) {
			$ordered['wc-shipped'] = 'ارسال شده';
		}
	}
	return $ordered;
} );

/**
 * Customize WooCommerce My Account sidebar menu items to match design exactly.
 */
add_filter( 'woocommerce_account_menu_items', function (): array {
	return [
		'dashboard'        => 'داشبورد',
		'orders'           => 'سفارش های من',
		'edit-address'     => 'آدرس ها',
		'tno-reviews'      => 'دیدگاه ها و پرسش ها',
		'tno-wishlist'     => 'علاقه مندی ها',
		'tno-recent-views' => 'آخرین کالاهای دیده شده',
		'tno-club'         => 'باشگاه مشتریان',
		'edit-account'     => 'اطلاعات حساب کاربری',
		'customer-logout'  => 'خروج از حساب کاربری',
	];
}, 50 );

/**
 * Fallback renderers for custom sidebar endpoints so no tab hits 404.
 */
foreach ( [
	'tno-reviews'      => 'دیدگاه ها و پرسش ها',
	'tno-wishlist'     => 'علاقه مندی ها',
	'tno-recent-views' => 'آخرین کالاهای دیده شده',
	'tno-club'         => 'باشگاه مشتریان',
] as $endpoint_slug => $endpoint_title ) {
	add_action( "woocommerce_account_{$endpoint_slug}_endpoint", function () use ( $endpoint_slug, $endpoint_title ): void {
		$custom_template = locate_template( "woocommerce/myaccount/{$endpoint_slug}.php" );
		if ( $custom_template !== '' ) {
			include $custom_template;
			return;
		}
		echo '<div class="tno-acc-head"><h1>' . esc_html( $endpoint_title ) . '</h1></div>';
		echo '<div class="tno-acc-card tno-acc-empty-card"><p class="tno-acc-empty-sub">موردی برای نمایش وجود ندارد.</p></div>';
	} );
}

/* --------------------------------------------------------------------------
   Shared Formatting Fallbacks (safe when checkout-functions.php is loaded)
   -------------------------------------------------------------------------- */
if ( ! function_exists( 'tno_fa_digits' ) ) {
	function tno_fa_digits( string $value ): string {
		return strtr( $value, [
			'0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
			'5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
		] );
	}
}

if ( ! function_exists( 'tno_format_toman' ) ) {
	function tno_format_toman( float $amount ): string {
		return tno_fa_digits( number_format( round( $amount ) ) );
	}
}

if ( ! function_exists( 'tno_gregorian_to_jalali' ) ) {
	function tno_gregorian_to_jalali( int $timestamp ): array {
		$g_y   = (int) wp_date( 'Y', $timestamp );
		$g_m   = (int) wp_date( 'n', $timestamp );
		$g_d   = (int) wp_date( 'j', $timestamp );
		$w     = (int) wp_date( 'w', $timestamp );
		$g_d_m = [ 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 ];
		$gy2   = ( $g_m > 2 ) ? ( $g_y + 1 ) : $g_y;
		$days  = 355666 + ( 365 * $g_y ) + (int) ( ( $gy2 + 3 ) / 4 ) - (int) ( ( $gy2 + 99 ) / 100 ) + (int) ( ( $gy2 + 399 ) / 400 ) + $g_d + $g_d_m[ $g_m - 1 ];
		$jy    = -1595 + ( 33 * (int) ( $days / 12053 ) );
		$days %= 12053;
		$jy   += 4 * (int) ( $days / 1461 );
		$days %= 1461;
		if ( $days > 365 ) {
			$jy   += (int) ( ( $days - 1 ) / 365 );
			$days = ( $days - 1 ) % 365;
		}
		if ( $days < 186 ) {
			$jm = 1 + (int) ( $days / 31 );
			$jd = 1 + ( $days % 31 );
		} else {
			$jm = 7 + (int) ( ( $days - 186 ) / 30 );
			$jd = 1 + ( ( $days - 186 ) % 30 );
		}
		$months   = [ 1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان', 9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند' ];
		$weekdays = [ 0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه شنبه', 3 => 'چهارشنبه', 4 => 'پنجشنبه', 5 => 'جمعه', 6 => 'شنبه' ];

		return [
			'year'       => $jy,
			'month'      => $jm,
			'day'        => $jd,
			'month_name' => $months[ $jm ] ?? '',
			'weekday'    => $weekdays[ $w ] ?? '',
		];
	}
}

/**
 * Format order delivery date (prefers saved `_tno_delivery_date`, falls back to Jalali creation date).
 */
function tno_account_get_order_date_label( WC_Order $order ): string {
	$delivery_date = trim( (string) $order->get_meta( '_tno_delivery_date' ) );
	if ( $delivery_date !== '' ) {
		return tno_fa_digits( $delivery_date );
	}

	$ts = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : current_time( 'timestamp' );
	$j  = tno_gregorian_to_jalali( $ts );
	return sprintf( '%s %s %s', tno_fa_digits( (string) $j['day'] ), $j['month_name'], tno_fa_digits( (string) $j['year'] ) );
}

/**
 * Map WooCommerce order status to UI bucket, Persian label, and badge tone.
 *
 * @return array{bucket: string, label: string, tone: string}
 */
function tno_account_get_status_meta( string $status ): array {
	$clean = str_replace( 'wc-', '', $status );

	return match ( $clean ) {
		'processing', 'on-hold', 'pending' => [
			'bucket' => 'processing',
			'label'  => 'در حال پردازش',
			'tone'   => 'amber',
		],
		'shipped' => [
			'bucket' => 'shipped',
			'label'  => 'ارسال شده',
			'tone'   => 'blue',
		],
		'completed' => [
			'bucket' => 'completed',
			'label'  => 'تحویل شده',
			'tone'   => 'green',
		],
		'cancelled', 'failed', 'refunded' => [
			'bucket' => 'cancelled',
			'label'  => wc_get_order_status_name( $clean ),
			'tone'   => 'red',
		],
		default => [
			'bucket' => 'processing',
			'label'  => wc_get_order_status_name( $clean ),
			'tone'   => 'amber',
		],
	};
}

/**
 * Fetch all orders for current customer and compute 4 bucket counts in a single query.
 *
 * @return array{orders: WC_Order[], counts: array{all: int, processing: int, shipped: int, completed: int}}
 */
function tno_account_get_customer_orders_bundle( int $user_id ): array {
	$counts = [
		'all'        => 0,
		'processing' => 0,
		'shipped'    => 0,
		'completed'  => 0,
	];

	if ( $user_id <= 0 || ! function_exists( 'wc_get_orders' ) ) {
		return [ 'orders' => [], 'counts' => $counts ];
	}

	/** @var WC_Order[] $orders */
	$orders = wc_get_orders( [
		'customer_id' => $user_id,
		'limit'       => 100,
		'orderby'     => 'date',
		'order'       => 'DESC',
	] );

	$counts['all'] = count( $orders );
	foreach ( $orders as $order ) {
		$meta = tno_account_get_status_meta( $order->get_status() );
		if ( isset( $counts[ $meta['bucket'] ] ) ) {
			$counts[ $meta['bucket'] ]++;
		}
	}

	return [
		'orders' => $orders,
		'counts' => $counts,
	];
}

/**
 * Resolve hex swatch color for color attributes (supports term meta or Persian color name fallback).
 */
function tno_account_resolve_color_hex( string $label, string $value, ?WC_Product $product = null ): ?string {
	if ( stripos( $label, 'رنگ' ) === false && stripos( $label, 'color' ) === false ) {
		return null;
	}

	$map = [
		'آبی'     => '#3b82f6',
		'بنفش'    => '#8b5cf6',
		'مشکی'    => '#111827',
		'سیاه'    => '#111827',
		'سفید'    => '#f3f4f6',
		'قرمز'    => '#ef4444',
		'سبز'     => '#10b981',
		'زرد'     => '#f59e0b',
		'نارنجی'  => '#f97316',
		'صورتی'   => '#ec4899',
		'خاکستری' => '#6b7280',
		'طوسی'    => '#9ca3af',
		'نقره ای' => '#cbd5e1',
		'طلایی'   => '#eab308',
		'سرمه ای' => '#1e3a8a',
		'قهوه ای' => '#78350f',
	];

	foreach ( $map as $fa => $hex ) {
		if ( mb_strpos( $value, $fa ) !== false ) {
			return $hex;
		}
	}

	return '#6366f1';
}

/**
 * Render a single order line item row (reused in Orders accordion and View Order page).
 */
function tno_account_render_order_item_row( WC_Order_Item_Product $item, WC_Order $order, bool $show_review_btn = false ): void {
	$product    = $item->get_product();
	$product_id = $item->get_product_id();
	$qty        = max( 1, (int) $item->get_quantity() );
	$title      = $item->get_name();
	$url        = $product instanceof WC_Product ? $product->get_permalink() : '#';
	$image_id   = $product instanceof WC_Product ? $product->get_image_id() : 0;
	$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

	$paid_total = (float) $item->get_total() + (float) $item->get_total_tax();
	$reg_unit   = ( $product instanceof WC_Product && (float) $product->get_regular_price() > 0 )
		? (float) $product->get_regular_price()
		: ( $paid_total / $qty );
	$reg_total  = $reg_unit * $qty;

	$has_discount     = $reg_total > $paid_total && $reg_total > 0;
	$discount_percent = $has_discount ? (int) round( ( ( $reg_total - $paid_total ) / $reg_total ) * 100 ) : 0;

	$meta_rows = [];
	foreach ( $item->get_formatted_meta_data( '_', true ) as $meta ) {
		$k = wp_strip_all_tags( (string) $meta->display_key );
		$v = wp_strip_all_tags( (string) $meta->display_value );
		if ( $k !== '' && $v !== '' ) {
			$meta_rows[] = [
				'key'   => $k,
				'val'   => $v,
				'color' => tno_account_resolve_color_hex( $k, $v, $product instanceof WC_Product ? $product : null ),
			];
		}
	}
	?>
	<div class="tno-ord-item">
		<div class="tno-ord-item-right">
			<a href="<?php echo esc_url( $url ); ?>" class="tno-ord-thumb-link">
				<img src="<?php echo esc_url( (string) $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="tno-ord-thumb" loading="lazy">
			</a>
			<div class="tno-ord-item-info">
				<h4 class="tno-ord-item-title">
					<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
				</h4>
				<div class="tno-ord-item-metas">
					<?php foreach ( $meta_rows as $m ) : ?>
						<span class="tno-ord-meta-line">
							<span class="tno-ord-meta-key"><?php echo esc_html( $m['key'] ); ?> :</span>
							<?php if ( $m['color'] !== null ) : ?>
								<span class="tno-ord-color-dot" style="background: <?php echo esc_attr( $m['color'] ); ?>;"></span>
							<?php endif; ?>
							<strong class="tno-ord-meta-val"><?php echo esc_html( tno_fa_digits( $m['val'] ) ); ?></strong>
						</span>
					<?php endforeach; ?>
					<span class="tno-ord-meta-line">
						<span class="tno-ord-meta-key">تعداد :</span>
						<strong class="tno-ord-meta-val"><?php echo esc_html( tno_fa_digits( (string) $qty ) . ' عدد' ); ?></strong>
					</span>
				</div>
			</div>
		</div>

		<div class="tno-ord-item-left">
			<div class="tno-ord-price-block">
				<?php if ( $has_discount && $discount_percent > 0 ) : ?>
					<div class="tno-ord-price-old-row">
						<span class="tno-ord-discount-pill"><?php echo esc_html( tno_fa_digits( (string) $discount_percent ) ); ?>%</span>
						<span class="tno-ord-old-price"><?php echo esc_html( tno_format_toman( $reg_total ) ); ?></span>
					</div>
				<?php endif; ?>
				<div class="tno-ord-price-final">
					<strong><?php echo esc_html( tno_format_toman( $paid_total ) ); ?></strong>
					<small>تومان</small>
				</div>
			</div>

			<?php if ( $show_review_btn && $product_id > 0 ) : ?>
				<button
					type="button"
					class="tno-ord-review-trigger"
					data-open-review="1"
					data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
					data-order-id="<?php echo esc_attr( (string) $order->get_id() ); ?>"
					data-product-title="<?php echo esc_attr( $title ); ?>"
					data-product-image="<?php echo esc_url( (string) $image_url ); ?>"
				>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
					<span>ثبت دیدگاه</span>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Retrieve saved user addresses for My Account Addresses screen (synced with checkout).
 *
 * @return array<int, array{id: string, state: string, city: string, postcode: string, address_1: string, receiver: string, phone: string, display: string}>
 */
function tno_account_get_addresses( int $user_id ): array {
	if ( $user_id <= 0 ) {
		return [];
	}

	$saved = get_user_meta( $user_id, '_tno_saved_addresses', true );
	$list  = is_array( $saved ) ? array_values( $saved ) : [];

	if ( empty( $list ) ) {
		$addr1 = trim( (string) get_user_meta( $user_id, 'billing_address_1', true ) );
		if ( $addr1 !== '' ) {
			$state      = (string) get_user_meta( $user_id, 'billing_state', true );
			$city       = (string) get_user_meta( $user_id, 'billing_city', true );
			$postcode   = (string) get_user_meta( $user_id, 'billing_postcode', true );
			$first      = (string) get_user_meta( $user_id, 'billing_first_name', true );
			$last       = (string) get_user_meta( $user_id, 'billing_last_name', true );
			$phone      = (string) get_user_meta( $user_id, 'billing_phone', true );
			$states     = WC()->countries->get_states( 'IR' ) ?: [];
			$state_name = $states[ $state ] ?? $state;

			$list[] = [
				'id'        => 'billing_default',
				'state'     => $state,
				'city'      => $city,
				'postcode'  => $postcode,
				'address_1' => $addr1,
				'receiver'  => trim( $first . ' ' . $last ),
				'phone'     => $phone,
				'display'   => implode( '، ', array_filter( [ $state_name, $city, $addr1 ] ) ),
			];
		}
	}

	return $list;
}

/**
 * AJAX: Submit verified product review from Order Details modal.
 */
function tno_ajax_submit_order_review(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'برای ثبت دیدگاه باید وارد حساب کاربری شوید.' ], 401 );
	}

	$user_id    = get_current_user_id();
	$user       = wp_get_current_user();
	$product_id = absint( $_POST['product_id'] ?? 0 );
	$order_id   = absint( $_POST['order_id'] ?? 0 );
	$rating     = max( 1, min( 5, absint( $_POST['rating'] ?? 0 ) ) );
	$content    = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );

	if ( $product_id <= 0 || get_post_type( $product_id ) !== 'product' ) {
		wp_send_json_error( [ 'message' => 'محصول معتبر نیست.' ], 400 );
	}

	if ( mb_strlen( trim( $content ) ) < 3 ) {
		wp_send_json_error( [ 'message' => 'متن دیدگاه بسیار کوتاه است.' ], 400 );
	}

	// Verify ownership if order_id passed
	$is_verified = false;
	if ( $order_id > 0 ) {
		$order = wc_get_order( $order_id );
		if ( $order instanceof WC_Order && (int) $order->get_user_id() === $user_id ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item instanceof WC_Order_Item_Product && (int) $item->get_product_id() === $product_id ) {
					$is_verified = true;
					break;
				}
			}
		}
	}

	$comment_id = wp_insert_comment( [
		'comment_post_ID'      => $product_id,
		'comment_author'       => trim( $user->display_name ) ?: $user->user_login,
		'comment_author_email' => $user->user_email,
		'comment_content'      => $content,
		'comment_type'         => 'review',
		'user_id'              => $user_id,
		'comment_approved'     => (int) get_option( 'comment_moderation' ) ? 0 : 1,
	] );

	if ( ! $comment_id || is_wp_error( $comment_id ) ) {
		wp_send_json_error( [ 'message' => 'خطا در ذخیره دیدگاه.' ], 500 );
	}

	add_comment_meta( (int) $comment_id, 'rating', $rating, true );
	if ( $is_verified ) {
		add_comment_meta( (int) $comment_id, 'verified', 1, true );
	}

	// Recalculate WC product rating aggregates
	if ( class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $product_id );
	}

	wp_send_json_success( [ 'commentId' => (int) $comment_id ] );
}
add_action( 'wp_ajax_tno_submit_order_review', 'tno_ajax_submit_order_review' );

/**
 * AJAX: Save (add/edit) or delete a customer address.
 */
function tno_ajax_manage_address(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'دسترسی غیرمجاز.' ], 401 );
	}

	$user_id = get_current_user_id();
	$op      = sanitize_key( wp_unslash( $_POST['op'] ?? 'save' ) );
	$list    = tno_account_get_addresses( $user_id );

	if ( $op === 'delete' ) {
		$target_id = sanitize_text_field( wp_unslash( $_POST['address_id'] ?? '' ) );
		$list      = array_values( array_filter( $list, fn( array $a ): bool => ( $a['id'] ?? '' ) !== $target_id ) );
		update_user_meta( $user_id, '_tno_saved_addresses', $list );

		if ( empty( $list ) ) {
			delete_user_meta( $user_id, 'billing_address_1' );
			delete_user_meta( $user_id, 'billing_city' );
			delete_user_meta( $user_id, 'billing_state' );
			delete_user_meta( $user_id, 'billing_postcode' );
		}

		wp_send_json_success( [ 'count' => count( $list ) ] );
	}

	$edit_id  = sanitize_text_field( wp_unslash( $_POST['address_id'] ?? '' ) );
	$state    = sanitize_text_field( wp_unslash( $_POST['state'] ?? '' ) );
	$city     = sanitize_text_field( wp_unslash( $_POST['city'] ?? '' ) );
	$postcode = sanitize_text_field( wp_unslash( $_POST['postcode'] ?? '' ) );
	$addr1    = sanitize_text_field( wp_unslash( $_POST['address_1'] ?? '' ) );
	$receiver = sanitize_text_field( wp_unslash( $_POST['receiver'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );

	if ( $state === '' || $city === '' || $addr1 === '' ) {
		wp_send_json_error( [ 'message' => 'استان، شهر و نشانی پستی الزامی هستند.' ], 400 );
	}

	$states     = WC()->countries->get_states( 'IR' ) ?: [];
	$state_name = $states[ $state ] ?? $state;
	$entry_id   = $edit_id !== '' ? $edit_id : 'addr_' . wp_generate_password( 8, false );

	$entry = [
		'id'        => $entry_id,
		'state'     => $state,
		'city'      => $city,
		'postcode'  => $postcode,
		'address_1' => $addr1,
		'receiver'  => $receiver,
		'phone'     => $phone,
		'display'   => implode( '، ', array_filter( [ $state_name, $city, $addr1 ] ) ),
	];

	$updated = false;
	foreach ( $list as $i => $existing ) {
		if ( ( $existing['id'] ?? '' ) === $entry_id ) {
			$list[ $i ] = $entry;
			$updated    = true;
			break;
		}
	}
	if ( ! $updated ) {
		$list[] = $entry;
	}

	update_user_meta( $user_id, '_tno_saved_addresses', $list );

	// Sync latest address to standard WC billing meta for seamless checkout prefill
	update_user_meta( $user_id, 'billing_country', 'IR' );
	update_user_meta( $user_id, 'billing_state', $state );
	update_user_meta( $user_id, 'billing_city', $city );
	update_user_meta( $user_id, 'billing_postcode', $postcode );
	update_user_meta( $user_id, 'billing_address_1', $addr1 );
	if ( $phone !== '' ) {
		update_user_meta( $user_id, 'billing_phone', $phone );
	}

	wp_send_json_success( [ 'address' => $entry ] );
}
add_action( 'wp_ajax_tno_manage_address', 'tno_ajax_manage_address' );

/**
 * Normalize Persian/Arabic digits to Latin ASCII digits.
 */
function tno_account_to_latin_digits( string $value ): string {
	return strtr( $value, [
		'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
		'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
		'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
		'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
	] );
}

/**
 * Validate Iranian 10-digit National ID (کد ملی).
 */
function tno_account_is_valid_iran_national_code( string $code ): bool {
	if ( ! preg_match( '/^\d{10}$/', $code ) ) {
		return false;
	}
	if ( preg_match( '/^(\d)\1{9}$/', $code ) ) {
		return false;
	}

	$sum = 0;
	for ( $i = 0; $i < 9; $i++ ) {
		$sum += (int) $code[ $i ] * ( 10 - $i );
	}
	$rem   = $sum % 11;
	$check = (int) $code[9];

	return $rem < 2 ? $check === $rem : $check === ( 11 - $rem );
}

/**
 * AJAX: Save user profile details from My Account -> Edit Account form.
 */
function tno_ajax_save_account_details(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'دسترسی غیرمجاز.' ], 401 );
	}

	$user_id       = get_current_user_id();
	$user          = get_userdata( $user_id );
	$first_name    = sanitize_text_field( wp_unslash( $_POST['first_name'] ?? '' ) );
	$last_name     = sanitize_text_field( wp_unslash( $_POST['last_name'] ?? '' ) );
	$raw_phone     = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$raw_email     = sanitize_text_field( wp_unslash( $_POST['email'] ?? '' ) );
	$raw_national  = sanitize_text_field( wp_unslash( $_POST['national_code'] ?? '' ) );

	$phone         = preg_replace( '/\s+/', '', tno_account_to_latin_digits( $raw_phone ) ) ?? '';
	$email         = trim( tno_account_to_latin_digits( $raw_email ) );
	$national_code = preg_replace( '/\D+/', '', tno_account_to_latin_digits( $raw_national ) ) ?? '';

	if ( $first_name === '' || $last_name === '' ) {
		wp_send_json_error( [ 'message' => 'لطفاً نام و نام خانوادگی خود را وارد کنید.' ], 400 );
	}

	if ( $phone !== '' && ! preg_match( '/^09\d{9}$/', $phone ) ) {
		wp_send_json_error( [ 'message' => 'شماره همراه باید ۱۱ رقم و با ۰۹ شروع شود.' ], 400 );
	}

	if ( $email !== '' ) {
		if ( ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => 'فرمت ایمیل واردشده معتبر نیست.' ], 400 );
		}
		$existing_owner = email_exists( $email );
		if ( $existing_owner && (int) $existing_owner !== $user_id ) {
			wp_send_json_error( [ 'message' => 'این ایمیل قبلاً توسط کاربر دیگری ثبت شده است.' ], 400 );
		}
	}

	if ( $national_code !== '' && ! tno_account_is_valid_iran_national_code( $national_code ) ) {
		wp_send_json_error( [ 'message' => 'کد ملی واردشده معتبر نیست.' ], 400 );
	}

	$display_name = trim( $first_name . ' ' . $last_name );
	$update_args  = [
		'ID'           => $user_id,
		'first_name'   => $first_name,
		'last_name'    => $last_name,
		'display_name' => $display_name,
	];
	if ( $email !== '' ) {
		$update_args['user_email'] = sanitize_email( $email );
	} elseif ( $user && $user->user_email !== '' && $raw_email === '' ) {
		$update_args['user_email'] = '';
	}

	$result = wp_update_user( $update_args );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => 'خطا در ذخیره‌سازی اطلاعات کاربری.' ], 500 );
	}

	update_user_meta( $user_id, 'billing_first_name', $first_name );
	update_user_meta( $user_id, 'billing_last_name', $last_name );
	if ( $phone !== '' ) {
		update_user_meta( $user_id, 'billing_phone', $phone );
	} else {
		delete_user_meta( $user_id, 'billing_phone' );
	}
	if ( $email !== '' ) {
		update_user_meta( $user_id, 'billing_email', sanitize_email( $email ) );
	}
	if ( $national_code !== '' ) {
		update_user_meta( $user_id, '_tno_national_code', $national_code );
		update_user_meta( $user_id, 'billing_national_code', $national_code );
	} else {
		delete_user_meta( $user_id, '_tno_national_code' );
		delete_user_meta( $user_id, 'billing_national_code' );
	}

	wp_send_json_success( [
		'message'      => 'اطلاعات حساب کاربری با موفقیت ذخیره شد.',
		'displayName'  => $display_name,
		'phoneDisplay' => $phone !== '' ? tno_fa_digits( $phone ) : '',
	] );
}
add_action( 'wp_ajax_tno_save_account_details', 'tno_ajax_save_account_details' );

/**
 * Retrieve user's wishlist product IDs (newest first, synced with PDP `_tno_wishlist`).
 *
 * @return int[]
 */
function tno_account_get_wishlist_ids( int $user_id ): array {
	if ( $user_id <= 0 ) {
		return [];
	}

	$raw = get_user_meta( $user_id, '_tno_wishlist', true );
	if ( ! is_array( $raw ) ) {
		return [];
	}

	$ids = array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
	return array_reverse( $ids );
}

/**
 * Retrieve recently viewed product IDs from user meta & cookie (newest first).
 *
 * @return int[]
 */
function tno_account_get_recent_view_ids( int $user_id ): array {
	$cookie_ids = [];
	if ( ! empty( $_COOKIE['tno_recent_views'] ) ) {
		$parts      = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['tno_recent_views'] ) ) );
		$cookie_ids = array_values( array_filter( array_map( 'absint', $parts ) ) );
	}

	$meta_ids = [];
	if ( $user_id > 0 ) {
		$raw = get_user_meta( $user_id, '_tno_recent_views', true );
		if ( is_array( $raw ) ) {
			$meta_ids = array_values( array_filter( array_map( 'absint', $raw ) ) );
		}
	}

	$merged = array_values( array_unique( array_merge( $meta_ids, $cookie_ids ) ) );
	return array_slice( $merged, 0, 24 );
}

/**
 * Track product page visits for "آخرین کالاهای دیده شده".
 */
add_action( 'template_redirect', function (): void {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	$product_id = get_queried_object_id();
	if ( $product_id <= 0 || get_post_type( $product_id ) !== 'product' ) {
		return;
	}

	$cookie_ids = [];
	if ( ! empty( $_COOKIE['tno_recent_views'] ) ) {
		$parts      = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['tno_recent_views'] ) ) );
		$cookie_ids = array_values( array_filter( array_map( 'absint', $parts ) ) );
	}

	$cookie_ids = array_values( array_diff( $cookie_ids, [ $product_id ] ) );
	array_unshift( $cookie_ids, $product_id );
	$cookie_ids = array_slice( $cookie_ids, 0, 24 );

	if ( ! headers_sent() ) {
		setcookie(
			'tno_recent_views',
			implode( ',', $cookie_ids ),
			time() + ( 30 * DAY_IN_SECONDS ),
			COOKIEPATH ?: '/',
			COOKIE_DOMAIN ?: '',
			is_ssl(),
			true
		);
	}
	$_COOKIE['tno_recent_views'] = implode( ',', $cookie_ids );

	if ( is_user_logged_in() ) {
		$user_id  = get_current_user_id();
		$meta_ids = get_user_meta( $user_id, '_tno_recent_views', true );
		$meta_ids = is_array( $meta_ids ) ? array_values( array_filter( array_map( 'absint', $meta_ids ) ) ) : [];
		$merged   = array_values( array_unique( array_merge( [ $product_id ], $meta_ids, $cookie_ids ) ) );
		update_user_meta( $user_id, '_tno_recent_views', array_slice( $merged, 0, 24 ) );
	}
} );

/**
 * Render a product card for Wishlist and Recently Viewed pages matching Trendino design.
 */
function tno_account_render_product_card( WC_Product $product, string $context = 'wishlist' ): void {
	$product_id = $product->get_id();
	$title      = $product->get_name();
	$permalink  = $product->get_permalink();
	$image_id   = $product->get_image_id();
	$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

	$custom_label = trim( (string) get_post_meta( $product_id, 'label', true ) );
	$badge_style  = (string) get_post_meta( $product_id, '_tno_badge_style', true ) === 'blue' ? 'blue' : 'pink';

	$regular_price = (float) $product->get_regular_price();
	$active_price  = (float) $product->get_price();
	if ( $product->is_type( 'variable' ) ) {
		$regular_price = (float) $product->get_variation_regular_price( 'min', true );
		$active_price  = (float) $product->get_variation_price( 'min', true );
	}

	$is_on_sale   = $product->is_on_sale() && $regular_price > 0 && $active_price > 0 && $active_price < $regular_price;
	$discount_pct = $is_on_sale ? (int) round( ( ( $regular_price - $active_price ) / $regular_price ) * 100 ) : 0;

	$can_ajax_cart = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
	$is_in_stock   = $product->is_in_stock();
	?>
	<article class="tno-acc-prod-card" data-acc-product-id="<?php echo esc_attr( (string) $product_id ); ?>">
		<div class="tno-acc-prod-top">
			<?php if ( $custom_label !== '' ) : ?>
				<span class="tno-acc-prod-badge tno-acc-prod-badge--<?php echo esc_attr( $badge_style ); ?>">
					<span class="tno-acc-prod-badge-dot" aria-hidden="true"></span>
					<span><?php echo esc_html( $custom_label ); ?></span>
				</span>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $permalink ); ?>" class="tno-acc-prod-thumb">
			<img src="<?php echo esc_url( (string) $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
		</a>

		<h3 class="tno-acc-prod-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
		</h3>

		<div class="tno-acc-prod-price-row">
			<div class="tno-acc-prod-prices">
				<?php if ( $is_on_sale ) : ?>
					<del class="tno-acc-prod-old-price"><?php echo esc_html( tno_format_toman( $regular_price ) ); ?></del>
				<?php endif; ?>
				<?php if ( $active_price > 0 ) : ?>
					<div class="tno-acc-prod-cur-price">
						<strong><?php echo esc_html( tno_format_toman( $active_price ) ); ?></strong>
						<small>تومان</small>
					</div>
				<?php else : ?>
					<div class="tno-acc-prod-cur-price">
						<strong>ناموجود</strong>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $is_on_sale && $discount_pct > 0 ) : ?>
				<span class="tno-acc-prod-discount"><?php echo esc_html( tno_fa_digits( (string) $discount_pct ) ); ?>%</span>
			<?php endif; ?>
		</div>

		<div class="tno-acc-prod-actions">
			<?php if ( ! $is_in_stock ) : ?>
				<button type="button" class="tno-acc-prod-cart-btn is-disabled" disabled>ناموجود</button>
			<?php elseif ( $can_ajax_cart ) : ?>
				<button type="button" class="tno-acc-prod-cart-btn" data-acc-add-cart="<?php echo esc_attr( (string) $product_id ); ?>">افزودن به سبد خرید</button>
			<?php else : ?>
				<a href="<?php echo esc_url( $permalink ); ?>" class="tno-acc-prod-cart-btn">افزودن به سبد خرید</a>
			<?php endif; ?>

			<button
				type="button"
				class="tno-acc-prod-del-btn"
				data-remove-acc-product="<?php echo esc_attr( (string) $product_id ); ?>"
				data-remove-context="<?php echo esc_attr( $context ); ?>"
				aria-label="حذف از لیست"
				title="حذف"
			>
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polyline points="3 6 5 6 21 6"/>
					<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
					<path d="M10 11v6M14 11v6"/>
					<path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
				</svg>
			</button>
		</div>
	</article>
	<?php
}

/**
 * AJAX: Remove a product from user's Wishlist (`_tno_wishlist`).
 */
function tno_ajax_account_remove_wishlist(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'دسترسی غیرمجاز.' ], 401 );
	}

	$user_id    = get_current_user_id();
	$product_id = absint( $_POST['product_id'] ?? 0 );
	$list       = get_user_meta( $user_id, '_tno_wishlist', true );
	$list       = is_array( $list ) ? array_values( array_filter( array_map( 'absint', $list ) ) ) : [];
	$list       = array_values( array_diff( $list, [ $product_id ] ) );

	update_user_meta( $user_id, '_tno_wishlist', $list );
	wp_send_json_success( [ 'remaining' => count( $list ) ] );
}
add_action( 'wp_ajax_tno_account_remove_wishlist', 'tno_ajax_account_remove_wishlist' );

/**
 * AJAX: Remove a product from user's Recently Viewed list (`_tno_recent_views` & cookie).
 */
function tno_ajax_account_remove_recent_view(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'دسترسی غیرمجاز.' ], 401 );
	}

	$user_id    = get_current_user_id();
	$product_id = absint( $_POST['product_id'] ?? 0 );

	$meta_ids = get_user_meta( $user_id, '_tno_recent_views', true );
	$meta_ids = is_array( $meta_ids ) ? array_values( array_filter( array_map( 'absint', $meta_ids ) ) ) : [];
	$meta_ids = array_values( array_diff( $meta_ids, [ $product_id ] ) );
	update_user_meta( $user_id, '_tno_recent_views', $meta_ids );

	if ( ! empty( $_COOKIE['tno_recent_views'] ) ) {
		$parts      = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['tno_recent_views'] ) ) );
		$cookie_ids = array_values( array_diff( array_filter( array_map( 'absint', $parts ) ), [ $product_id ] ) );
		if ( ! headers_sent() ) {
			setcookie(
				'tno_recent_views',
				implode( ',', $cookie_ids ),
				time() + ( 30 * DAY_IN_SECONDS ),
				COOKIEPATH ?: '/',
				COOKIE_DOMAIN ?: '',
				is_ssl(),
				true
			);
		}
	}

	wp_send_json_success( [ 'remaining' => count( $meta_ids ) ] );
}
add_action( 'wp_ajax_tno_account_remove_recent_view', 'tno_ajax_account_remove_recent_view' );

/**
 * AJAX: Add simple product to WooCommerce cart from Wishlist or Recently Viewed card.
 */
function tno_ajax_account_add_to_cart(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	$product_id = absint( $_POST['product_id'] ?? 0 );
	if ( $product_id <= 0 || ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( [ 'message' => 'محصول معتبر نیست.' ], 400 );
	}

	$added = WC()->cart->add_to_cart( $product_id, 1 );
	if ( ! $added ) {
		wp_send_json_error( [ 'message' => 'امکان افزودن این محصول به سبد خرید وجود ندارد.' ], 400 );
	}

	$count = WC()->cart->get_cart_contents_count();
	wp_send_json_success( [
		'cartCount'      => $count,
		'cartCountLabel' => tno_fa_digits( (string) $count ),
	] );
}
add_action( 'wp_ajax_tno_account_add_to_cart', 'tno_ajax_account_add_to_cart' );