<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TNO_CLUB_META_BALANCE = '_tno_club_points_balance';
const TNO_CLUB_META_LEDGER  = '_tno_club_ledger';
const TNO_CLUB_META_COUPONS = '_tno_club_claimed_coupons';

/**
 * Get user's available (usable) club points.
 */
function tno_club_get_user_points( int $user_id ): int {
	if ( $user_id <= 0 ) {
		return 0;
	}
	return max( 0, (int) get_user_meta( $user_id, TNO_CLUB_META_BALANCE, true ) );
}

/**
 * Get user's ledger entries (newest first).
 *
 * @return array<int, array<string, mixed>>
 */
function tno_club_get_user_ledger( int $user_id ): array {
	if ( $user_id <= 0 ) {
		return [];
	}
	$raw = get_user_meta( $user_id, TNO_CLUB_META_LEDGER, true );
	return is_array( $raw ) ? array_values( $raw ) : [];
}

/**
 * Sum user's pending points awaiting return period completion.
 */
function tno_club_get_user_pending_points( int $user_id ): int {
	$total = 0;
	foreach ( tno_club_get_user_ledger( $user_id ) as $entry ) {
		if ( ( $entry['status'] ?? 'available' ) === 'pending' && (int) ( $entry['points'] ?? 0 ) > 0 ) {
			$total += (int) $entry['points'];
		}
	}
	return $total;
}

/**
 * Get user's claimed discount coupons list (newest first).
 *
 * @return array<int, array<string, mixed>>
 */
function tno_club_get_user_coupons( int $user_id ): array {
	if ( $user_id <= 0 ) {
		return [];
	}
	$raw = get_user_meta( $user_id, TNO_CLUB_META_COUPONS, true );
	return is_array( $raw ) ? array_values( $raw ) : [];
}

/**
 * Append a transaction entry to user's points ledger and optionally update usable balance.
 */
function tno_club_adjust_user_points( int $user_id, int $delta, string $description, string $type = 'manual', array $extra = [] ): int {
	if ( $user_id <= 0 || $delta === 0 ) {
		return tno_club_get_user_points( $user_id );
	}

	$status    = (string) ( $extra['status'] ?? 'available' );
	$unlock_at = (int) ( $extra['unlock_at'] ?? 0 );
	$order_id  = (int) ( $extra['order_id'] ?? 0 );
	$coupon_id = (int) ( $extra['coupon_id'] ?? 0 );

	$balance = tno_club_get_user_points( $user_id );
	if ( $status === 'available' ) {
		$balance = max( 0, $balance + $delta );
		update_user_meta( $user_id, TNO_CLUB_META_BALANCE, $balance );
	}

	$ledger = tno_club_get_user_ledger( $user_id );
	array_unshift( $ledger, [
		'id'          => 'lg_' . wp_generate_password( 8, false ),
		'type'        => $type,
		'points'      => $delta,
		'description' => $description,
		'order_id'    => $order_id,
		'coupon_id'   => $coupon_id,
		'status'      => $status,
		'unlock_at'   => $unlock_at,
		'created_at'  => time(),
	] );

	// Keep recent 200 entries
	if ( count( $ledger ) > 200 ) {
		$ledger = array_slice( $ledger, 0, 200 );
	}
	update_user_meta( $user_id, TNO_CLUB_META_LEDGER, $ledger );

	return $balance;
}

/**
 * Synchronize pending order points (return period matured) and expired unused coupon refunds.
 */
function tno_club_sync_user_state( int $user_id ): void {
	if ( $user_id <= 0 ) {
		return;
	}

	$now     = time();
	$opts    = tno_get_club_settings();
	$ledger  = tno_club_get_user_ledger( $user_id );
	$balance = tno_club_get_user_points( $user_id );
	$changed = false;

	// 1. Release matured pending points whose return period has elapsed
	foreach ( $ledger as $idx => $entry ) {
		if ( ( $entry['status'] ?? 'available' ) === 'pending' ) {
			$unlock_at = (int) ( $entry['unlock_at'] ?? 0 );
			if ( $unlock_at <= $now ) {
				$ledger[ $idx ]['status'] = 'available';
				$balance                 += max( 0, (int) ( $entry['points'] ?? 0 ) );
				$changed                  = true;
			}
		}
	}

	if ( $changed ) {
		update_user_meta( $user_id, TNO_CLUB_META_BALANCE, $balance );
		update_user_meta( $user_id, TNO_CLUB_META_LEDGER, $ledger );
	}

	// 2. Check expired unused coupons for automatic points refund
	if ( ! empty( $opts['refund_on_expire'] ) && class_exists( 'WC_Coupon' ) ) {
		$coupons         = tno_club_get_user_coupons( $user_id );
		$coupons_changed = false;

		foreach ( $coupons as $idx => $item ) {
			if ( ! empty( $item['refunded'] ) ) {
				continue;
			}

			$coupon_id  = (int) ( $item['coupon_id'] ?? 0 );
			$expires_at = (int) ( $item['expires_at'] ?? 0 );
			$cost       = (int) ( $item['points_cost'] ?? 0 );

			if ( $coupon_id <= 0 || $cost <= 0 || $expires_at <= 0 || $expires_at > $now ) {
				continue;
			}

			$wc_coupon = new WC_Coupon( $coupon_id );
			if ( $wc_coupon->get_id() > 0 && (int) $wc_coupon->get_usage_count() === 0 ) {
				$coupons[ $idx ]['refunded'] = 1;
				$coupons_changed             = true;

				$code = (string) ( $item['code'] ?? $wc_coupon->get_code() );
				tno_club_adjust_user_points(
					$user_id,
					$cost,
					sprintf( 'بازگشت امتیاز کد منقضی‌شده %s', strtoupper( $code ) ),
					'refund',
					[ 'coupon_id' => $coupon_id ]
				);
			}
		}

		if ( $coupons_changed ) {
			update_user_meta( $user_id, TNO_CLUB_META_COUPONS, $coupons );
		}
	}
}

/**
 * Compute earned points and ledger description for a WooCommerce order.
 *
 * @return array{points: int, description: string}
 */
function tno_club_calculate_order_points( WC_Order $order ): array {
	$opts            = tno_get_club_settings();
	$mode            = (string) ( $opts['points_mode'] ?? 'amount' );
	$amount_step     = max( 1000, (int) ( $opts['amount_step'] ?? 100000 ) );
	$points_per_step = max( 1, (int) ( $opts['points_per_step'] ?? 1 ) );
	$after_discount  = ! empty( $opts['after_discount'] );
	$exclude_ship    = ! empty( $opts['exclude_shipping'] );

	// Calculate monetary base
	$base_total = (float) $order->get_total();
	if ( $exclude_ship ) {
		$base_total -= (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
	}
	if ( ! $after_discount ) {
		$base_total += (float) $order->get_discount_total() + (float) $order->get_discount_tax();
	}
	$base_total = max( 0.0, $base_total );

	// Calculate product-level points sum
	$product_points_sum = 0;
	$has_product_points = false;
	$first_item_name    = '';
	$item_count         = 0;

	foreach ( $order->get_items() as $item ) {
		if ( ! ( $item instanceof WC_Order_Item_Product ) ) {
			continue;
		}
		$item_count++;
		if ( $first_item_name === '' ) {
			$first_item_name = $item->get_name();
		}
		$raw_meta = (string) get_post_meta( $item->get_product_id(), '_tno_club_points', true );
		$digits   = strtr( $raw_meta, [
			'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
			'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
		] );
		$pts = (int) preg_replace( '/\D+/', '', $digits );
		if ( $pts > 0 ) {
			$has_product_points  = true;
			$product_points_sum += $pts * max( 1, (int) $item->get_quantity() );
		}
	}

	$amount_points = (int) floor( $base_total / $amount_step ) * $points_per_step;

	$final_points = match ( $mode ) {
		'product' => $product_points_sum,
		'hybrid'  => $has_product_points ? $product_points_sum : $amount_points,
		default   => $amount_points,
	};

	if ( $first_item_name !== '' ) {
		$suffix = $item_count > 1
			? sprintf( ' و %s کالای دیگر', tno_fa_digits( (string) ( $item_count - 1 ) ) )
			: '';
		$desc   = sprintf(
			'خرید %s%s به مبلغ %s تومان',
			$first_item_name,
			$suffix,
			tno_format_toman( $base_total )
		);
	} else {
		$desc = sprintf(
			'خرید سفارش #%s به مبلغ %s تومان',
			tno_fa_digits( (string) $order->get_order_number() ),
			tno_format_toman( $base_total )
		);
	}

	return [
		'points'      => max( 0, $final_points ),
		'description' => $desc,
	];
}

/**
 * Award or revoke points automatically on WooCommerce order status transitions.
 */
add_action( 'woocommerce_order_status_changed', function ( int $order_id, string $old_status, string $new_status, WC_Order $order ): void {
	$opts = tno_get_club_settings();
	if ( empty( $opts['enabled'] ) ) {
		return;
	}

	$user_id = (int) $order->get_user_id();
	if ( $user_id <= 0 ) {
		return;
	}

	$clean_new    = str_replace( 'wc-', '', $new_status );
	$award_status = (string) ( $opts['award_status'] ?? 'completed' );
	$awarded      = (int) $order->get_meta( '_tno_club_points_awarded' );

	// Define which statuses count as reaching or exceeding the award milestone
	$eligible_statuses = match ( $award_status ) {
		'processing' => [ 'processing', 'shipped', 'completed' ],
		'shipped'    => [ 'shipped', 'completed' ],
		default      => [ 'completed' ],
	};

	if ( $awarded <= 0 && in_array( $clean_new, $eligible_statuses, true ) ) {
		$calc = tno_club_calculate_order_points( $order );
		if ( $calc['points'] > 0 ) {
			$return_days = max( 0, (int) ( $opts['return_period_days'] ?? 0 ) );
			$status      = $return_days > 0 ? 'pending' : 'available';
			$unlock_at   = $return_days > 0 ? ( time() + ( $return_days * DAY_IN_SECONDS ) ) : 0;

			tno_club_adjust_user_points(
				$user_id,
				$calc['points'],
				$calc['description'],
				'earn',
				[
					'order_id'  => $order_id,
					'status'    => $status,
					'unlock_at' => $unlock_at,
				]
			);

			$order->update_meta_data( '_tno_club_points_awarded', $calc['points'] );
			$order->save_meta_data();
		}
		return;
	}

	// Revoke points if order is cancelled or refunded
	if ( $awarded > 0 && ! empty( $opts['revoke_on_cancel'] ) && in_array( $clean_new, [ 'cancelled', 'refunded', 'failed' ], true ) ) {
		$ledger      = tno_club_get_user_ledger( $user_id );
		$was_pending = false;

		foreach ( $ledger as $idx => $entry ) {
			if ( (int) ( $entry['order_id'] ?? 0 ) === $order_id && ( $entry['type'] ?? '' ) === 'earn' && ( $entry['status'] ?? '' ) === 'pending' ) {
				$ledger[ $idx ]['status'] = 'cancelled';
				$was_pending              = true;
				break;
			}
		}

		if ( $was_pending ) {
			update_user_meta( $user_id, TNO_CLUB_META_LEDGER, $ledger );
		} else {
			tno_club_adjust_user_points(
				$user_id,
				-$awarded,
				sprintf( 'بازگشت امتیاز بابت لغو/مرجوعی سفارش #%s', tno_fa_digits( (string) $order->get_order_number() ) ),
				'revoke',
				[ 'order_id' => $order_id ]
			);
		}

		$order->delete_meta_data( '_tno_club_points_awarded' );
		$order->save_meta_data();
	}
}, 10, 4 );

/**
 * Award optional signup bonus points when a new user registers.
 */
add_action( 'user_register', function ( int $user_id ): void {
	$opts  = tno_get_club_settings();
	$bonus = absint( $opts['signup_bonus_points'] ?? 0 );
	if ( empty( $opts['enabled'] ) || $bonus <= 0 ) {
		return;
	}

	tno_club_adjust_user_points(
		$user_id,
		$bonus,
		'هدیه عضویت در باشگاه مشتریان',
		'bonus'
	);
} );

/**
 * Award optional bonus points when a product review is approved.
 */
function tno_club_maybe_award_review_points( WP_Comment $comment ): void {
	if ( (int) $comment->comment_approved !== 1 || (int) $comment->user_id <= 0 ) {
		return;
	}
	if ( get_post_type( (int) $comment->comment_post_ID ) !== 'product' ) {
		return;
	}
	if ( get_comment_meta( (int) $comment->comment_ID, '_tno_club_review_awarded', true ) ) {
		return;
	}

	$opts  = tno_get_club_settings();
	$bonus = absint( $opts['review_bonus_points'] ?? 0 );
	if ( empty( $opts['enabled'] ) || $bonus <= 0 ) {
		return;
	}

	$product_title = get_the_title( (int) $comment->comment_post_ID );
	tno_club_adjust_user_points(
		(int) $comment->user_id,
		$bonus,
		sprintf( 'ثبت دیدگاه برای محصول %s', $product_title ),
		'bonus'
	);
	add_comment_meta( (int) $comment->comment_ID, '_tno_club_review_awarded', 1, true );
}

add_action( 'wp_insert_comment', function ( int $comment_id, WP_Comment $comment ): void {
	tno_club_maybe_award_review_points( $comment );
}, 10, 2 );

add_action( 'transition_comment_status', function ( string $new_status, string $old_status, WP_Comment $comment ): void {
	if ( $new_status === 'approved' ) {
		tno_club_maybe_award_review_points( $comment );
	}
}, 10, 3 );

/**
 * Enforce user binding on Customer Club WooCommerce coupons (even for phone-only accounts).
 */
add_filter( 'woocommerce_coupon_is_valid', function ( bool $valid, WC_Coupon $coupon ): bool {
	if ( ! $valid ) {
		return false;
	}

	$owner_id = (int) $coupon->get_meta( '_tno_club_user_id' );
	if ( $owner_id <= 0 ) {
		return $valid;
	}

	$opts = tno_get_club_settings();
	if ( empty( $opts['coupon_bind_user'] ) ) {
		return $valid;
	}

	if ( ! is_user_logged_in() || get_current_user_id() !== $owner_id ) {
		throw new Exception( 'این کد تخفیف اختصاصی است و تنها با حساب کاربری دریافت‌کننده قابل استفاده می‌باشد.' );
	}

	return true;
}, 10, 2 );

/**
 * Resolve live status of a claimed coupon record.
 *
 * @param array<string, mixed> $item
 * @return array{status: string, label: string, tone: string}
 */
function tno_club_get_coupon_status_meta( array $item ): array {
	$coupon_id  = (int) ( $item['coupon_id'] ?? 0 );
	$expires_at = (int) ( $item['expires_at'] ?? 0 );
	$refunded   = ! empty( $item['refunded'] );
	$now        = time();

	if ( $coupon_id > 0 && class_exists( 'WC_Coupon' ) ) {
		$wc_coupon = new WC_Coupon( $coupon_id );
		if ( $wc_coupon->get_id() > 0 && (int) $wc_coupon->get_usage_count() > 0 ) {
			return [
				'status' => 'used',
				'label'  => 'استفاده شده',
				'tone'   => 'muted',
			];
		}
	}

	if ( $refunded || ( $expires_at > 0 && $expires_at <= $now ) ) {
		return [
			'status' => 'expired',
			'label'  => $refunded ? 'منقضی (امتیاز برگشت خورد)' : 'منقضی شده',
			'tone'   => 'red',
		];
	}

	return [
		'status' => 'active',
		'label'  => 'قابل استفاده',
		'tone'   => 'green',
	];
}

/**
 * Format Jalali short date for club coupons/ledger.
 */
function tno_club_format_jalali_date( int $timestamp ): string {
	$j = tno_gregorian_to_jalali( $timestamp );
	return sprintf(
		'%s %s %s',
		tno_fa_digits( (string) $j['day'] ),
		$j['month_name'],
		tno_fa_digits( (string) $j['year'] )
	);
}

/**
 * AJAX: Exchange points for a real WooCommerce coupon from a configured club tier.
 */
function tno_ajax_club_claim_tier(): void {
	check_ajax_referer( 'tno_account_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( [ 'message' => 'لطفاً ابتدا وارد حساب کاربری شوید.' ], 401 );
	}

	$opts = tno_get_club_settings();
	if ( empty( $opts['enabled'] ) ) {
		wp_send_json_error( [ 'message' => 'باشگاه مشتریان در حال حاضر غیرفعال است.' ], 403 );
	}

	$user_id = get_current_user_id();
	tno_club_sync_user_state( $user_id );

	$tier_id = sanitize_key( wp_unslash( $_POST['tier_id'] ?? '' ) );
	$tiers   = is_array( $opts['tiers'] ) ? $opts['tiers'] : [];
	$target  = null;

	foreach ( $tiers as $tier ) {
		if ( ! empty( $tier['enabled'] ) && sanitize_key( (string) ( $tier['id'] ?? '' ) ) === $tier_id ) {
			$target = $tier;
			break;
		}
	}

	if ( ! $target ) {
		wp_send_json_error( [ 'message' => 'بسته تخفیف انتخاب‌شده معتبر نیست.' ], 404 );
	}

	$cost    = max( 1, (int) ( $target['points_cost'] ?? 0 ) );
	$balance = tno_club_get_user_points( $user_id );

	if ( $balance < $cost ) {
		wp_send_json_error( [ 'message' => 'امتیاز کافی برای دریافت این کد تخفیف ندارید.' ], 400 );
	}

	if ( ! class_exists( 'WC_Coupon' ) ) {
		wp_send_json_error( [ 'message' => 'ووکامرس فعال نیست.' ], 500 );
	}

	// Generate unique coupon code
	$prefix = strtoupper( preg_replace( '/[^A-Za-z0-9\-]/', '', (string) ( $opts['coupon_prefix'] ?? 'CLUB-' ) ) ?: 'CLUB-' );
	$code   = $prefix . strtoupper( wp_generate_password( 6, false, false ) );
	while ( wc_get_coupon_id_by_code( $code ) > 0 ) {
		$code = $prefix . strtoupper( wp_generate_password( 6, false, false ) );
	}

	$expiry_days = max( 1, (int) ( $opts['coupon_expiry_days'] ?? 30 ) );
	$expires_at  = time() + ( $expiry_days * DAY_IN_SECONDS );
	$user        = get_userdata( $user_id );

	$coupon = new WC_Coupon();
	$coupon->set_code( $code );
	$coupon->set_description( sprintf( 'کد تخفیف باشگاه مشتریان برای کاربر #%d (%s)', $user_id, (string) $target['title'] ) );
	$coupon->set_discount_type( (string) ( $target['discount_type'] ?? 'percent' ) );
	$coupon->set_amount( (float) ( $target['discount_amount'] ?? 0 ) );
	$coupon->set_date_expires( $expires_at );

	if ( ! empty( $opts['coupon_single_use'] ) ) {
		$coupon->set_usage_limit( 1 );
		$coupon->set_usage_limit_per_user( 1 );
	}
	if ( ! empty( $opts['coupon_individual_use'] ) ) {
		$coupon->set_individual_use( true );
	}
	if ( (int) ( $target['min_spend'] ?? 0 ) > 0 ) {
		$coupon->set_minimum_amount( (float) $target['min_spend'] );
	}
	if ( ! empty( $opts['coupon_bind_user'] ) && $user && is_email( $user->user_email ) ) {
		$coupon->set_email_restrictions( [ $user->user_email ] );
	}

	$coupon->update_meta_data( '_tno_club_user_id', $user_id );
	$coupon->update_meta_data( '_tno_club_tier_id', $tier_id );
	$coupon->update_meta_data( '_tno_club_points_cost', $cost );

	$coupon_id = $coupon->save();
	if ( ! $coupon_id ) {
		wp_send_json_error( [ 'message' => 'خطا در ایجاد کد تخفیف، لطفاً دوباره تلاش کنید.' ], 500 );
	}

	$new_balance = tno_club_adjust_user_points(
		$user_id,
		-$cost,
		sprintf( 'دریافت %s (%s)', (string) $target['title'], $code ),
		'spend',
		[ 'coupon_id' => $coupon_id ]
	);

	$claimed = tno_club_get_user_coupons( $user_id );
	$record  = [
		'coupon_id'       => $coupon_id,
		'code'            => $code,
		'title'           => (string) $target['title'],
		'points_cost'     => $cost,
		'discount_type'   => (string) ( $target['discount_type'] ?? 'percent' ),
		'discount_amount' => (float) ( $target['discount_amount'] ?? 0 ),
		'created_at'      => time(),
		'expires_at'      => $expires_at,
		'refunded'        => 0,
	];
	array_unshift( $claimed, $record );
	update_user_meta( $user_id, TNO_CLUB_META_COUPONS, $claimed );

	wp_send_json_success( [
		'message'        => sprintf( 'کد تخفیف %s با موفقیت برای شما صادر شد.', $code ),
		'balance'        => $new_balance,
		'balanceLabel'   => tno_fa_digits( number_format( $new_balance ) ) . ' امتیاز',
		'coupon'         => [
			'code'       => $code,
			'title'      => $record['title'],
			'expires'    => tno_club_format_jalali_date( $expires_at ),
			'status'     => 'قابل استفاده',
			'statusTone' => 'green',
		],
	] );
}
add_action( 'wp_ajax_tno_club_claim_tier', 'tno_ajax_club_claim_tier' );
