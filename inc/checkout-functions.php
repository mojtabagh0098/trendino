<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue checkout & thankyou assets exclusively on checkout endpoints.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}

	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	$css_ver = file_exists( $theme_path . '/assets/css/checkout.css' )
		? (string) filemtime( $theme_path . '/assets/css/checkout.css' )
		: '1.0.0';

	$js_ver = file_exists( $theme_path . '/assets/js/checkout.js' )
		? (string) filemtime( $theme_path . '/assets/js/checkout.js' )
		: '1.0.0';

	wp_enqueue_style( 'tno-checkout', $theme_uri . '/assets/css/checkout.css', [], $css_ver );
	wp_enqueue_script( 'tno-checkout', $theme_uri . '/assets/js/checkout.js', [ 'wc-checkout' ], $js_ver, true );

	wp_localize_script( 'tno-checkout', 'tnoCheckoutConfig', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tno_checkout_nonce' ),
		'i18n'    => [
			'fillRequired'  => 'لطفاً نام، نام خانوادگی، شماره تماس و آدرس را کامل وارد کنید.',
			'selectSlot'    => 'لطفاً روز و بازه زمانی ارسال را انتخاب کنید.',
			'couponEmpty'   => 'لطفاً کد تخفیف را وارد کنید.',
			'couponApplied' => 'کد تخفیف با موفقیت اعمال شد.',
			'couponRemoved' => 'کد تخفیف حذف شد.',
			'copied'        => 'کد تخفیف کپی شد!',
			'errorGeneric'  => 'خطایی رخ داد، لطفاً مجدداً تلاش کنید.',
		],
	] );
} );

/**
 * Make billing email optional on custom checkout form (matches design without email field)
 * while keeping logged-in user email automatically populated.
 */
add_filter( 'woocommerce_checkout_fields', function ( array $fields ): array {
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['required'] = false;
	}
	return $fields;
} );

/**
 * Pure PHP Gregorian to Jalali conversion (zero plugin dependency).
 *
 * @return array{year: int, month: int, day: int, month_name: string, weekday: string}
 */
function tno_gregorian_to_jalali( int $timestamp ): array {
	$g_y = (int) wp_date( 'Y', $timestamp );
	$g_m = (int) wp_date( 'n', $timestamp );
	$g_d = (int) wp_date( 'j', $timestamp );
	$w   = (int) wp_date( 'w', $timestamp );

	$g_d_m = [ 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 ];
	$gy2   = ( $g_m > 2 ) ? ( $g_y + 1 ) : $g_y;
	$days  = 355666 + ( 365 * $g_y ) + (int) ( ( $gy2 + 3 ) / 4 ) - (int) ( ( $gy2 + 99 ) / 100 ) + (int) ( ( $gy2 + 399 ) / 400 ) + $g_d + $g_d_m[ $g_m - 1 ];

	$jy   = -1595 + ( 33 * (int) ( $days / 12053 ) );
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

	$months = [
		1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
		5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
		9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
	];

	$weekdays = [
		0 => 'یکشنبه', 1 => 'دوشنبه', 2 => 'سه شنبه', 3 => 'چهارشنبه',
		4 => 'پنجشنبه', 5 => 'جمعه', 6 => 'شنبه',
	];

	return [
		'year'       => $jy,
		'month'      => $jm,
		'day'        => $jd,
		'month_name' => $months[ $jm ] ?? '',
		'weekday'    => $weekdays[ $w ] ?? '',
	];
}

/**
 * Convert Latin digits to Persian digits.
 */
function tno_fa_digits( string $value ): string {
	return strtr( $value, [
		'0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
		'5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
	] );
}

/**
 * Format numeric price with commas and Persian digits.
 */
function tno_format_toman( float $amount ): string {
	return tno_fa_digits( number_format( round( $amount ) ) );
}

/**
 * Generate upcoming delivery dates based on Theme Settings.
 *
 * @return array<int, array{value: string, label: string}>
 */
function tno_get_delivery_days(): array {
	$offset = (int) tno_get_checkout_option( 'delivery_start_offset', 1 );
	$count  = (int) tno_get_checkout_option( 'delivery_days_count', 6 );
	$now    = current_time( 'timestamp' );
	$days   = [];

	for ( $i = 0; $i < $count; $i++ ) {
		$ts     = strtotime( '+' . ( $offset + $i ) . ' days', $now );
		$jalali = tno_gregorian_to_jalali( (int) $ts );
		$label  = sprintf( '%s %s %s', $jalali['weekday'], tno_fa_digits( (string) $jalali['day'] ), $jalali['month_name'] );
		$days[] = [
			'value' => wp_date( 'Y-m-d', (int) $ts ) . '|' . $label,
			'label' => $label,
		];
	}

	return $days;
}

/**
 * Parse configured delivery time slots from settings.
 *
 * @return string[]
 */
function tno_get_delivery_time_slots(): array {
	$raw   = (string) tno_get_checkout_option( 'delivery_time_slots', '' );
	$lines = preg_split( '/\r\n|\r|\n/', $raw ) ?: [];
	return array_values( array_filter( array_map( 'trim', $lines ) ) );
}

/**
 * Retrieve saved addresses for logged-in user (supports multiple saved entries + billing fallback).
 *
 * @return array<int, array{id: string, state: string, city: string, postcode: string, address_1: string, display: string}>
 */
function tno_get_user_saved_addresses( int $user_id ): array {
	if ( $user_id <= 0 ) {
		return [];
	}

	$saved = get_user_meta( $user_id, '_tno_saved_addresses', true );
	$list  = is_array( $saved ) ? $saved : [];

	if ( empty( $list ) ) {
		$addr1 = (string) get_user_meta( $user_id, 'billing_address_1', true );
		$city  = (string) get_user_meta( $user_id, 'billing_city', true );
		$state = (string) get_user_meta( $user_id, 'billing_state', true );
		$post  = (string) get_user_meta( $user_id, 'billing_postcode', true );

		if ( $addr1 !== '' ) {
			$states     = WC()->countries->get_states( 'IR' ) ?: [];
			$state_name = $states[ $state ] ?? $state;
			$parts      = array_filter( [ $state_name, $city, $addr1 ] );
			$list[]     = [
				'id'        => 'billing_default',
				'state'     => $state,
				'city'      => $city,
				'postcode'  => $post,
				'address_1' => $addr1,
				'display'   => implode( '، ', $parts ),
			];
		}
	}

	return $list;
}

/**
 * Calculate total cart savings (regular vs sale price + applied coupons) and discount percentage.
 *
 * @return array{regular_total: float, discount_total: float, discount_percent: int, item_count: int}
 */
function tno_calculate_cart_totals_breakdown(): array {
	$cart = WC()->cart;
	if ( ! $cart ) {
		return [
			'regular_total'    => 0.0,
			'discount_total'   => 0.0,
			'discount_percent' => 0,
			'item_count'       => 0,
		];
	}

	$regular_total = 0.0;
	$sale_savings  = 0.0;
	$item_count    = 0;

	foreach ( $cart->get_cart() as $item ) {
		$product = $item['data'] ?? null;
		$qty     = (int) ( $item['quantity'] ?? 1 );
		if ( ! $product instanceof WC_Product ) {
			continue;
		}
		$item_count += $qty;

		$reg   = (float) $product->get_regular_price();
		$price = (float) $product->get_price();
		if ( $reg <= 0 ) {
			$reg = $price;
		}

		$regular_total += ( $reg * $qty );
		if ( $reg > $price ) {
			$sale_savings += ( ( $reg - $price ) * $qty );
		}
	}

	$coupon_discount  = (float) $cart->get_discount_total() + (float) $cart->get_discount_tax();
	$discount_total   = $sale_savings + $coupon_discount;
	$discount_percent = $regular_total > 0 ? (int) round( ( $discount_total / $regular_total ) * 100 ) : 0;

	return [
		'regular_total'    => $regular_total,
		'discount_total'   => $discount_total,
		'discount_percent' => min( 100, max( 0, $discount_percent ) ),
		'item_count'       => $item_count,
	];
}

/**
 * Render the left sticky order summary card (used both on initial render and WC AJAX fragment refresh).
 */
function tno_render_checkout_summary_html( string $active_step = 'address' ): string {
	$cart = WC()->cart;
	if ( ! $cart ) {
		return '';
	}

	$breakdown      = tno_calculate_cart_totals_breakdown();
	$shipping_total = (float) $cart->get_shipping_total() + (float) $cart->get_shipping_tax();
	$final_total    = (float) $cart->get_total( 'edit' );

	// Active shipping method label
	$shipping_label = '';
	$chosen_methods = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods', [] ) : [];
	$packages       = WC()->shipping()->get_packages();
	if ( ! empty( $packages[0]['rates'] ) && ! empty( $chosen_methods[0] ) && isset( $packages[0]['rates'][ $chosen_methods[0] ] ) ) {
		$shipping_label = (string) $packages[0]['rates'][ $chosen_methods[0] ]->get_label();
	}

	$show_invoice_notice = (bool) tno_get_checkout_option( 'invoice_notice_enabled', 1 );
	$invoice_notice_text = (string) tno_get_checkout_option( 'invoice_notice_text', 'برای دریافت فاکتور به حساب کاربری بروید.' );

	ob_start();
	?>
	<aside class="tno-chk-summary" id="tno-chk-summary" data-active-step="<?php echo esc_attr( $active_step ); ?>">
		<h2 class="tno-chk-summary-title">مجموع قابل پرداخت</h2>

		<div class="tno-chk-summary-items">
			<?php foreach ( $cart->get_cart() as $cart_item ) :
				$product = $cart_item['data'] ?? null;
				if ( ! $product instanceof WC_Product ) {
					continue;
				}
				$qty        = (int) $cart_item['quantity'];
				$line_total = (float) $cart_item['line_subtotal'] + (float) $cart_item['line_subtotal_tax'];
				?>
				<div class="tno-chk-summary-item">
					<span class="tno-chk-summary-item-name">
						<?php echo esc_html( $product->get_name() ); ?>
						<?php if ( $qty > 1 ) : ?>
							<em class="tno-chk-qty">×<?php echo esc_html( tno_fa_digits( (string) $qty ) ); ?></em>
						<?php endif; ?>
					</span>
					<span class="tno-chk-summary-item-price">
						<strong><?php echo esc_html( tno_format_toman( $line_total ) ); ?></strong>
						<small>تومان</small>
					</span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="tno-chk-summary-breakdown">
			<div class="tno-chk-summary-row">
				<span class="tno-chk-row-label">قیمت کالاها (<?php echo esc_html( tno_fa_digits( (string) $breakdown['item_count'] ) ); ?>)</span>
				<span class="tno-chk-row-val">
					<strong><?php echo esc_html( tno_format_toman( $breakdown['regular_total'] ) ); ?></strong>
					<small>تومان</small>
				</span>
			</div>

			<div class="tno-chk-summary-row">
				<span class="tno-chk-row-label">
					<span class="tno-chk-ship-label-step1">نحوه ارسال</span>
					<span class="tno-chk-ship-label-step2">هزینه ارسال</span>
					<?php if ( $shipping_label !== '' ) : ?>
						<em class="tno-chk-ship-method-name"><?php echo esc_html( $shipping_label ); ?></em>
					<?php endif; ?>
				</span>
				<span class="tno-chk-row-val">
					<?php if ( $shipping_total > 0 ) : ?>
						<strong><?php echo esc_html( tno_format_toman( $shipping_total ) ); ?></strong>
						<small>تومان</small>
					<?php else : ?>
						<strong>رایگان</strong>
					<?php endif; ?>
				</span>
			</div>

			<?php if ( $breakdown['discount_total'] > 0 ) : ?>
				<div class="tno-chk-summary-row tno-chk-summary-row--discount">
					<span class="tno-chk-row-label">مجموع تخفیف کالاها (<?php echo esc_html( tno_fa_digits( (string) $breakdown['discount_percent'] ) ); ?>%)</span>
					<span class="tno-chk-row-val">
						<strong><?php echo esc_html( tno_format_toman( $breakdown['discount_total'] ) ); ?></strong>
						<small>تومان</small>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<div class="tno-chk-summary-total">
			<span class="tno-chk-total-label">مبلغ قابل پرداخت</span>
			<span class="tno-chk-total-val">
				<strong><?php echo esc_html( tno_format_toman( $final_total ) ); ?></strong>
				<small>تومان</small>
			</span>
		</div>

		<?php if ( $show_invoice_notice && $invoice_notice_text !== '' ) : ?>
			<div class="tno-chk-invoice-notice">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
				<span><?php echo esc_html( $invoice_notice_text ); ?></span>
			</div>
		<?php endif; ?>

		<div class="tno-chk-summary-actions">
			<button type="button" class="tno-chk-cta-btn tno-chk-next-step-btn" id="tno-chk-go-payment">
				پرداخت و خرید محصول
			</button>

			<button type="submit" class="tno-chk-cta-btn tno-chk-cta-btn--dark tno-chk-place-order-btn" name="woocommerce_checkout_place_order" id="place_order" value="پرداخت و خرید محصول">
				پرداخت و خرید محصول
			</button>
		</div>
	</aside>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render shipping methods pill selector HTML (also refreshed via fragments).
 */
function tno_render_shipping_methods_html(): string {
	WC()->cart->calculate_shipping();
	$packages       = WC()->shipping()->get_packages();
	$chosen_methods = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods', [] ) : [];

	ob_start();
	?>
	<div class="tno-chk-shipping-grid" id="tno-chk-shipping-methods">
		<?php
		if ( ! empty( $packages ) ) {
			foreach ( $packages as $i => $package ) {
				$rates  = $package['rates'] ?? [];
				$chosen = $chosen_methods[ $i ] ?? '';
				if ( $chosen === '' && ! empty( $rates ) ) {
					$first  = reset( $rates );
					$chosen = $first ? $first->get_id() : '';
				}
				foreach ( $rates as $rate ) {
					$rate_id  = $rate->get_id();
					$checked  = ( $chosen === $rate_id );
					?>
					<label class="tno-chk-pill-radio <?php echo $checked ? 'is-selected' : ''; ?>">
						<input
							type="radio"
							name="shipping_method[<?php echo esc_attr( (string) $i ); ?>]"
							data-index="<?php echo esc_attr( (string) $i ); ?>"
							value="<?php echo esc_attr( $rate_id ); ?>"
							class="shipping_method"
							<?php checked( $checked ); ?>
						>
						<span class="tno-chk-radio-dot"></span>
						<span class="tno-chk-pill-text"><?php echo esc_html( $rate->get_label() ); ?></span>
					</label>
					<?php
				}
			}
		} else {
			echo '<p class="tno-chk-muted-note">روش ارسالی برای این آدرس تعریف نشده است.</p>';
		}
		?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render payment gateways list HTML (also refreshed via fragments for live installment amount).
 */
function tno_render_payment_gateways_html(): string {
	$gateways        = WC()->payment_gateways()->get_available_payment_gateways();
	$chosen_gateway  = WC()->session ? (string) WC()->session->get( 'chosen_payment_method', '' ) : '';
	$installment_key = strtolower( trim( (string) tno_get_checkout_option( 'installment_gateway_id', 'snapppay' ) ) );
	$installment_cnt = max( 2, (int) tno_get_checkout_option( 'installment_count', 4 ) );
	$cart_total      = WC()->cart ? (float) WC()->cart->get_total( 'edit' ) : 0.0;
	$per_installment = $cart_total > 0 ? ( $cart_total / $installment_cnt ) : 0.0;

	if ( $chosen_gateway === '' && ! empty( $gateways ) ) {
		$first          = reset( $gateways );
		$chosen_gateway = $first ? $first->id : '';
	}

	ob_start();
	?>
	<div class="tno-chk-payment-list" id="tno-chk-payment-methods">
		<?php if ( ! empty( $gateways ) ) : ?>
			<?php foreach ( $gateways as $gateway ) :
				$is_selected    = ( $chosen_gateway === $gateway->id );
				$is_installment = $installment_key !== '' && stripos( $gateway->id . ' ' . $gateway->get_title(), $installment_key ) !== false;
				$icon_html      = $gateway->get_icon();
				?>
				<label class="tno-chk-payment-item <?php echo $is_selected ? 'is-selected' : ''; ?>">
					<input
						type="radio"
						class="input-radio"
						name="payment_method"
						value="<?php echo esc_attr( $gateway->id ); ?>"
						<?php checked( $is_selected ); ?>
					>
					<span class="tno-chk-radio-dot tno-chk-radio-dot--lg"></span>

					<span class="tno-chk-payment-icon <?php echo $is_installment ? 'tno-chk-payment-icon--blue' : 'tno-chk-payment-icon--pink'; ?>">
						<?php if ( trim( $icon_html ) !== '' ) : ?>
							<?php echo wp_kses_post( $icon_html ); ?>
						<?php elseif ( $is_installment ) : ?>
							<span class="tno-chk-snapp-fallback">Snapp!<br>Pay</span>
						<?php else : ?>
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L4 6v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V6l-8-4z"/><path d="M9 12l2 2 4-4"/></svg>
						<?php endif; ?>
					</span>

					<span class="tno-chk-payment-meta">
						<strong class="tno-chk-payment-title"><?php echo esc_html( $gateway->get_title() ); ?></strong>
						<?php if ( $is_installment && $per_installment > 0 ) : ?>
							<span class="tno-chk-payment-sub">
								پرداخت در <?php echo esc_html( tno_fa_digits( (string) $installment_cnt ) ); ?> قسط
								<span class="tno-chk-installment-pill">
									<strong><?php echo esc_html( tno_format_toman( $per_installment ) ); ?></strong>
									<small>تومان</small>
								</span>
							</span>
						<?php elseif ( $gateway->get_description() ) : ?>
							<span class="tno-chk-payment-sub"><?php echo wp_kses_post( wp_strip_all_tags( $gateway->get_description() ) ); ?></span>
						<?php endif; ?>
					</span>
				</label>
			<?php endforeach; ?>
		<?php else : ?>
			<p class="tno-chk-muted-note">درگاه پرداختی فعال نیست. لطفاً از تنظیمات ووکامرس درگاه پرداخت را فعال کنید.</p>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Hook custom checkout blocks into WooCommerce native `update_order_review` fragments.
 */
add_filter( 'woocommerce_update_order_review_fragments', function ( array $fragments ): array {
	$step = isset( $_POST['tno_active_step'] ) ? sanitize_key( wp_unslash( $_POST['tno_active_step'] ) ) : 'address';
	$fragments['#tno-chk-summary']          = tno_render_checkout_summary_html( $step );
	$fragments['#tno-chk-shipping-methods'] = tno_render_shipping_methods_html();
	$fragments['#tno-chk-payment-methods']  = tno_render_payment_gateways_html();
	return $fragments;
} );

/**
 * AJAX: Apply or remove coupon cleanly from Step 3.
 */
function tno_ajax_checkout_coupon(): void {
	check_ajax_referer( 'tno_checkout_nonce', 'nonce' );

	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'apply';
	$code = isset( $_POST['coupon_code'] ) ? wc_format_coupon_code( sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) ) : '';

	if ( $code === '' ) {
		wp_send_json_error( [ 'message' => 'لطفاً کد تخفیف را وارد کنید.' ], 400 );
	}

	wc_clear_notices();

	if ( $mode === 'remove' ) {
		WC()->cart->remove_coupon( $code );
		WC()->cart->calculate_totals();
		wp_send_json_success( [
			'message' => 'کد تخفیف حذف شد.',
			'coupons' => WC()->cart->get_applied_coupons(),
		] );
	}

	$applied = WC()->cart->apply_coupon( $code );
	WC()->cart->calculate_totals();

	if ( ! $applied ) {
		$errors  = wc_get_notices( 'error' );
		$message = ! empty( $errors[0]['notice'] ) ? wp_strip_all_tags( (string) $errors[0]['notice'] ) : 'کد تخفیف نامعتبر است.';
		wc_clear_notices();
		wp_send_json_error( [ 'message' => $message ], 400 );
	}

	wc_clear_notices();
	wp_send_json_success( [
		'message' => 'کد تخفیف با موفقیت اعمال شد.',
		'coupons' => WC()->cart->get_applied_coupons(),
	] );
}
add_action( 'wp_ajax_tno_checkout_coupon', 'tno_ajax_checkout_coupon' );
add_action( 'wp_ajax_nopriv_tno_checkout_coupon', 'tno_ajax_checkout_coupon' );

/**
 * Persist delivery schedule meta (HPOS-compatible) and save newly added address to user profile.
 */
add_action( 'woocommerce_checkout_create_order', function ( WC_Order $order ): void {
	if ( isset( $_POST['tno_delivery_date'] ) ) {
		$raw_date = sanitize_text_field( wp_unslash( $_POST['tno_delivery_date'] ) );
		if ( $raw_date !== '' ) {
			$parts = explode( '|', $raw_date );
			$order->update_meta_data( '_tno_delivery_date', $parts[1] ?? $parts[0] );
		}
	}

	if ( isset( $_POST['tno_delivery_slot'] ) ) {
		$slot = sanitize_text_field( wp_unslash( $_POST['tno_delivery_slot'] ) );
		if ( $slot !== '' ) {
			$order->update_meta_data( '_tno_delivery_slot', $slot );
		}
	}

	// Store new address into user saved address book when logged in
	$user_id = $order->get_user_id();
	if ( $user_id > 0 && $order->get_billing_address_1() !== '' ) {
		$saved = get_user_meta( $user_id, '_tno_saved_addresses', true );
		$saved = is_array( $saved ) ? $saved : [];

		$state      = $order->get_billing_state();
		$city       = $order->get_billing_city();
		$postcode   = $order->get_billing_postcode();
		$addr1      = $order->get_billing_address_1();
		$states     = WC()->countries->get_states( 'IR' ) ?: [];
		$state_name = $states[ $state ] ?? $state;
		$display    = implode( '، ', array_filter( [ $state_name, $city, $addr1 ] ) );
		$hash       = md5( $state . '|' . $city . '|' . $postcode . '|' . $addr1 );

		$exists = false;
		foreach ( $saved as $item ) {
			if ( ( $item['id'] ?? '' ) === $hash ) {
				$exists = true;
				break;
			}
		}

		if ( ! $exists ) {
			$saved[] = [
				'id'        => $hash,
				'state'     => $state,
				'city'      => $city,
				'postcode'  => $postcode,
				'address_1' => $addr1,
				'display'   => $display,
			];
			update_user_meta( $user_id, '_tno_saved_addresses', array_slice( $saved, -5 ) );
		}
	}
}, 10, 1 );

/**
 * Display delivery schedule inside WooCommerce Admin Order screen (HPOS & legacy compatible).
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', function ( WC_Order $order ): void {
	$date = (string) $order->get_meta( '_tno_delivery_date' );
	$slot = (string) $order->get_meta( '_tno_delivery_slot' );

	if ( $date !== '' || $slot !== '' ) {
		echo '<div class="tno-admin-delivery-meta"><p><strong>زمان ارسال انتخابی مشتری:</strong><br>';
		echo esc_html( trim( $date . ' — ' . $slot, ' —' ) );
		echo '</p></div>';
	}
} );

/**
 * Resolve next-purchase coupon ticket data (WC Coupon binding or manual Theme Settings).
 *
 * @return array{enabled: bool, percent: string, label: string, code: string}
 */
function tno_get_thankyou_coupon_ticket(): array {
	$enabled = (bool) tno_get_checkout_option( 'coupon_ticket_enabled', 1 );
	$wc_id   = (int) tno_get_checkout_option( 'coupon_ticket_wc_id', 0 );

	$percent = (string) tno_get_checkout_option( 'coupon_ticket_percent', '۴۰%' );
	$label   = (string) tno_get_checkout_option( 'coupon_ticket_label', 'کوپن تخفیف خرید بعدی شما' );
	$code    = (string) tno_get_checkout_option( 'coupon_ticket_code', '@Discount' );

	if ( $wc_id > 0 && class_exists( 'WC_Coupon' ) ) {
		$coupon = new WC_Coupon( $wc_id );
		if ( $coupon->get_id() > 0 ) {
			$code = $coupon->get_code();
			$amt  = (float) $coupon->get_amount();
			if ( $coupon->is_type( 'percent' ) ) {
				$percent = tno_fa_digits( (string) round( $amt ) ) . '%';
			} else {
				$percent = tno_format_toman( $amt ) . ' تومان';
			}
		}
	}

	return [
		'enabled' => $enabled && $code !== '',
		'percent' => $percent,
		'label'   => $label,
		'code'    => $code,
	];
}