<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

$user_id        = get_current_user_id();
$saved_addrs    = tno_get_user_saved_addresses( $user_id );
$iran_states    = WC()->countries->get_states( 'IR' ) ?: [];
$default_first  = (string) $checkout->get_value( 'billing_first_name' );
$default_last   = (string) $checkout->get_value( 'billing_last_name' );
$default_phone  = (string) $checkout->get_value( 'billing_phone' );
$default_email  = (string) $checkout->get_value( 'billing_email' );
$default_state  = (string) $checkout->get_value( 'billing_state' );
$default_city   = (string) $checkout->get_value( 'billing_city' );
$default_post   = (string) $checkout->get_value( 'billing_postcode' );
$default_addr1  = (string) $checkout->get_value( 'billing_address_1' );

$show_schedule  = (bool) tno_get_checkout_option( 'enable_delivery_schedule', 1 );
$delivery_days  = $show_schedule ? tno_get_delivery_days() : [];
$delivery_slots = $show_schedule ? tno_get_delivery_time_slots() : [];

$coupon_title       = (string) tno_get_checkout_option( 'coupon_title', 'کد تخفیف' );
$coupon_desc        = (string) tno_get_checkout_option( 'coupon_desc', 'کد تخفیف خود را وارد کنید تا تخفیف روی سفارش شما اعمال شود !' );
$coupon_placeholder = (string) tno_get_checkout_option( 'coupon_placeholder', 'Discount 20%' );
$applied_coupons    = WC()->cart ? WC()->cart->get_applied_coupons() : [];
?>

<div class="tno-chk-page" dir="rtl" id="tno-checkout-app" data-step="address">
	<!-- Top 3-Step Indicator -->
	<nav class="tno-chk-stepper-bar" aria-label="مراحل خرید">
		<div class="tno-chk-container">
			<ol class="tno-chk-stepper">
				<li class="tno-chk-step tno-chk-step--done">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="tno-chk-step-link">سبد خرید</a>
				</li>
				<li class="tno-chk-step-line tno-chk-step-line--solid" aria-hidden="true"></li>
				<li class="tno-chk-step is-active" data-step-nav="address">
					<button type="button" class="tno-chk-step-btn" data-goto-step="address">
						<span class="tno-chk-step-dot"></span>
						<span>آدرس و ارسال</span>
					</button>
				</li>
				<li class="tno-chk-step-line tno-chk-step-line--dynamic" id="tno-chk-line-2" aria-hidden="true"></li>
				<li class="tno-chk-step" data-step-nav="payment">
					<button type="button" class="tno-chk-step-btn" data-goto-step="payment">
						<span class="tno-chk-step-dot"></span>
						<span>پرداخت</span>
					</button>
				</li>
			</ol>
		</div>
	</nav>

	<div class="tno-chk-container">
		<div class="tno-chk-notices" id="tno-chk-notices">
			<?php wc_print_notices(); ?>
		</div>

		<form name="checkout" method="post" class="checkout woocommerce-checkout tno-chk-layout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="billing_country" id="billing_country" value="IR">
			<input type="hidden" name="billing_email" id="billing_email" value="<?php echo esc_attr( $default_email ); ?>">
			<input type="hidden" name="tno_active_step" id="tno_active_step" value="address">
			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

			<!-- Right Main Column -->
			<div class="tno-chk-main">
				<!-- STEP 2: Address & Shipping -->
				<div class="tno-chk-pane is-active" data-pane="address">
					<div class="tno-chk-section-head tno-chk-section-head--main">
						<span class="tno-chk-dot-halo"></span>
						<h1>اطلاعات گیرنده</h1>
					</div>

					<!-- Receiver Info -->
					<section class="tno-chk-block">
						<h2 class="tno-chk-subhead">
							<span class="tno-chk-dot"></span>
							<span>اطلاعات تحویل گیرنده</span>
						</h2>
						<div class="tno-chk-grid-3">
							<div class="tno-chk-field">
								<label for="billing_first_name">نام</label>
								<input type="text" id="billing_first_name" name="billing_first_name" value="<?php echo esc_attr( $default_first ); ?>" placeholder="مثال : رضا" required>
							</div>
							<div class="tno-chk-field">
								<label for="billing_last_name">نام خانوادگی</label>
								<input type="text" id="billing_last_name" name="billing_last_name" value="<?php echo esc_attr( $default_last ); ?>" placeholder="مثال : جنیدی" required>
							</div>
							<div class="tno-chk-field">
								<label for="billing_phone">شماره تماس گیرنده</label>
								<input type="tel" id="billing_phone" name="billing_phone" value="<?php echo esc_attr( $default_phone ); ?>" placeholder="مثال : ۰۹۱۲۴۸۷۳۴۶۲۷" required>
							</div>
						</div>
					</section>

					<!-- Saved Addresses -->
					<?php if ( ! empty( $saved_addrs ) ) : ?>
						<section class="tno-chk-block">
							<h2 class="tno-chk-subhead">
								<span class="tno-chk-dot"></span>
								<span>ارسال به آدرس انتخاب شده</span>
							</h2>
							<div class="tno-chk-stack" id="tno-chk-saved-addresses">
								<?php foreach ( $saved_addrs as $idx => $addr ) :
									$is_first = ( $idx === count( $saved_addrs ) - 1 );
									?>
									<label class="tno-chk-pill-radio tno-chk-pill-radio--full <?php echo $is_first ? 'is-selected' : ''; ?>">
										<input
											type="radio"
											name="tno_saved_address"
											value="<?php echo esc_attr( $addr['id'] ); ?>"
											data-state="<?php echo esc_attr( $addr['state'] ); ?>"
											data-city="<?php echo esc_attr( $addr['city'] ); ?>"
											data-postcode="<?php echo esc_attr( $addr['postcode'] ); ?>"
											data-address="<?php echo esc_attr( $addr['address_1'] ); ?>"
											<?php checked( $is_first ); ?>
										>
										<span class="tno-chk-radio-dot"></span>
										<span class="tno-chk-pill-text"><?php echo esc_html( $addr['display'] ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</section>
						<hr class="tno-chk-divider">
					<?php endif; ?>

					<!-- Add / Edit Address -->
					<section class="tno-chk-block">
						<h2 class="tno-chk-subhead">
							<span class="tno-chk-dot"></span>
							<span>افزودن آدرس جدید</span>
						</h2>
						<div class="tno-chk-grid-2">
							<div class="tno-chk-field">
								<label for="billing_state">استان</label>
								<div class="tno-chk-select-wrap">
									<select id="billing_state" name="billing_state" required>
										<option value="">انتخاب کنید</option>
										<?php foreach ( $iran_states as $code => $name ) : ?>
											<option value="<?php echo esc_attr( (string) $code ); ?>" <?php selected( $default_state, (string) $code ); ?>>
												<?php echo esc_html( (string) $name ); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<svg class="tno-chk-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
								</div>
							</div>
							<div class="tno-chk-field">
								<label for="billing_city">شهر</label>
								<input type="text" id="billing_city" name="billing_city" value="<?php echo esc_attr( $default_city ); ?>" placeholder="انتخاب کنید" required>
							</div>
							<div class="tno-chk-field">
								<label for="billing_postcode">کدپستی</label>
								<input type="text" id="billing_postcode" name="billing_postcode" value="<?php echo esc_attr( $default_post ); ?>" placeholder="مثال : ۳۲۱۲۵۶۳۷۴۹۰" inputmode="numeric">
							</div>
							<div class="tno-chk-field">
								<label for="billing_address_1">نشانی پستی</label>
								<input type="text" id="billing_address_1" name="billing_address_1" value="<?php echo esc_attr( $default_addr1 ); ?>" placeholder="مثال : تهران، خیابان ولیعصر، منطقه ۱۲، بلوار کاوه، کوچه ابوذر، پلاک ۱۵" required>
							</div>
						</div>
					</section>

					<!-- Shipping Methods -->
					<section class="tno-chk-block">
						<h2 class="tno-chk-subhead">
							<span class="tno-chk-dot"></span>
							<span>انتخاب روش ارسال</span>
						</h2>
						<?php echo tno_render_shipping_methods_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</section>

					<!-- Delivery Schedule -->
					<?php if ( $show_schedule && ! empty( $delivery_days ) ) : ?>
						<section class="tno-chk-block">
							<h2 class="tno-chk-subhead">
								<span class="tno-chk-dot"></span>
								<span>زمان ارسال</span>
							</h2>

							<div class="tno-chk-days-row" id="tno-chk-delivery-days">
								<?php foreach ( $delivery_days as $idx => $day ) :
									$checked = ( $idx === 0 );
									?>
									<label class="tno-chk-pill-radio tno-chk-pill-radio--day <?php echo $checked ? 'is-selected' : ''; ?>">
										<input type="radio" name="tno_delivery_date" value="<?php echo esc_attr( $day['value'] ); ?>" <?php checked( $checked ); ?>>
										<span class="tno-chk-radio-dot"></span>
										<span class="tno-chk-pill-text"><?php echo esc_html( $day['label'] ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>

							<?php if ( ! empty( $delivery_slots ) ) : ?>
								<div class="tno-chk-stack tno-chk-slots-stack" id="tno-chk-delivery-slots">
									<?php foreach ( $delivery_slots as $idx => $slot ) :
										$checked = ( $idx === 0 );
										?>
										<label class="tno-chk-pill-radio tno-chk-pill-radio--full <?php echo $checked ? 'is-selected' : ''; ?>">
											<input type="radio" name="tno_delivery_slot" value="<?php echo esc_attr( $slot ); ?>" <?php checked( $checked ); ?>>
											<span class="tno-chk-radio-dot"></span>
											<span class="tno-chk-pill-text"><?php echo esc_html( $slot ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<!-- Order Comments -->
					<section class="tno-chk-block">
						<h2 class="tno-chk-subhead">
							<span class="tno-chk-dot"></span>
							<span>توضیحات تکمیلی</span>
						</h2>
						<textarea id="order_comments" name="order_comments" class="tno-chk-textarea" rows="4" placeholder="در صورت تمایل، توضیحات یا درخواست‌های ویژه مربوط به سفارش خود را وارد کنید (اختیاری)"><?php echo esc_textarea( (string) $checkout->get_value( 'order_comments' ) ); ?></textarea>
					</section>
				</div>

				<!-- STEP 3: Payment & Coupon -->
				<div class="tno-chk-pane" data-pane="payment" hidden>
					<div class="tno-chk-section-head tno-chk-section-head--main">
						<span class="tno-chk-dot-halo"></span>
						<h2>انتخاب روش پرداخت</h2>
					</div>

					<?php echo tno_render_payment_gateways_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

					<!-- Coupon Section -->
					<?php if ( wc_coupons_enabled() ) : ?>
						<section class="tno-chk-coupon-section">
							<div class="tno-chk-coupon-info">
								<h3><?php echo esc_html( $coupon_title ); ?></h3>
								<p><?php echo esc_html( $coupon_desc ); ?></p>
							</div>
							<div class="tno-chk-coupon-control">
								<div class="tno-chk-coupon-pill">
									<input type="text" id="tno-chk-coupon-input" placeholder="<?php echo esc_attr( $coupon_placeholder ); ?>" autocomplete="off">
									<button type="button" class="tno-chk-coupon-btn" id="tno-chk-coupon-apply">ثبت کد تخفیف</button>
								</div>
								<div class="tno-chk-coupon-feedback" id="tno-chk-coupon-feedback" aria-live="polite"></div>
								<div class="tno-chk-applied-coupons" id="tno-chk-applied-coupons">
									<?php foreach ( $applied_coupons as $code ) : ?>
										<span class="tno-chk-coupon-chip">
											<span><?php echo esc_html( $code ); ?></span>
											<button type="button" data-remove-coupon="<?php echo esc_attr( $code ); ?>" aria-label="حذف کد تخفیف">×</button>
										</span>
									<?php endforeach; ?>
								</div>
							</div>
						</section>
					<?php endif; ?>
				</div>
			</div>

			<!-- Left Sticky Order Summary Column -->
			<div class="tno-chk-sidebar">
				<?php echo tno_render_checkout_summary_html( 'address' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</form>
	</div>
</div>