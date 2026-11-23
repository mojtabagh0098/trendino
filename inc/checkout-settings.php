<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TNO_CHECKOUT_OPTION_KEY = 'tno_checkout_settings';

/**
 * Default values for all customizable checkout & thank-you elements.
 *
 * @return array<string, mixed>
 */
function tno_checkout_default_settings(): array {
	return [
		// Delivery schedule
		'enable_delivery_schedule' => 1,
		'delivery_start_offset'    => 1,
		'delivery_days_count'      => 6,
		'delivery_time_slots'      => "ساعت ۹ تا ۱۱\nساعت ۱۱ تا ۱۳\nساعت ۱۳ تا ۱۵",

		// Payment step
		'coupon_title'             => 'کد تخفیف',
		'coupon_desc'              => 'کد تخفیف خود را وارد کنید تا تخفیف روی سفارش شما اعمال شود !',
		'coupon_placeholder'       => 'Discount 20%',
		'invoice_notice_enabled'   => 1,
		'invoice_notice_text'      => 'برای دریافت فاکتور به حساب کاربری بروید.',
		'installment_gateway_id'   => 'snapppay',
		'installment_count'        => 4,

		// Thank-you: Success
		'success_subtitle'         => 'جهت دریافت جزئیات بیشتر لطفاً ایمیل یا پیامک خود را بررسی کنید',
		'invoice_btn_enabled'      => 1,
		'invoice_btn_label'        => 'دریافت فاکتور',
		'invoice_btn_url'          => '', // Empty = view-order / print fallback
		'home_btn_label'           => 'بازگشت به صفحه اصلی',

		// Next-purchase coupon ticket
		'coupon_ticket_enabled'    => 1,
		'coupon_ticket_wc_id'      => 0, // Optional: bind to an actual WC coupon
		'coupon_ticket_percent'    => '۴۰%',
		'coupon_ticket_label'      => 'کوپن تخفیف خرید بعدی شما',
		'coupon_ticket_code'       => '@Discount',

		// Thank-you: Failed
		'failed_subtitle'          => 'لطفا سفارش خودرا چک کرده و دوباره تلاش کنید',

		// Social cards on thank-you page
		'social_section_enabled'   => 1,
		'social_section_title'     => 'ما در شبکه های اجتماعی دنبال کنید',
		'social_bale_enabled'      => 1,
		'social_bale_label'        => 'بله',
		'social_bale_url'          => '#',
		'social_instagram_enabled' => 1,
		'social_instagram_label'   => 'اینستاگرام',
		'social_instagram_url'     => '#',
		'social_telegram_enabled'  => 1,
		'social_telegram_label'    => 'تلگرام',
		'social_telegram_url'      => '#',
	];
}

/**
 * Retrieve all checkout settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function tno_get_checkout_settings(): array {
	$saved = get_option( TNO_CHECKOUT_OPTION_KEY, [] );
	if ( ! is_array( $saved ) ) {
		$saved = [];
	}
	return wp_parse_args( $saved, tno_checkout_default_settings() );
}

/**
 * Retrieve a single checkout setting key.
 *
 * @param mixed $fallback
 * @return mixed
 */
function tno_get_checkout_option( string $key, $fallback = null ) {
	$settings = tno_get_checkout_settings();
	if ( array_key_exists( $key, $settings ) ) {
		return $settings[ $key ];
	}
	return $fallback;
}

/**
 * Register submenu under existing Trendino settings menu (or Appearance fallback).
 */
add_action( 'admin_menu', function (): void {
	global $menu;

	$parent_slug = 'themes.php';
	$candidates  = [ 'trendino-settings', 'tno-settings', 'tno-theme-options', 'trendino-options' ];

	if ( is_array( $menu ) ) {
		foreach ( $menu as $item ) {
			if ( isset( $item[2] ) && in_array( $item[2], $candidates, true ) ) {
				$parent_slug = (string) $item[2];
				break;
			}
		}
	}

	add_submenu_page(
		$parent_slug,
		'تنظیمات تسویه حساب و نتیجه سفارش',
		'تسویه حساب و کوپن',
		'manage_options',
		'tno-checkout-settings',
		'tno_render_checkout_settings_page'
	);
}, 99 );

/**
 * Register settings & strict sanitizer.
 */
add_action( 'admin_init', function (): void {
	register_setting(
		'tno_checkout_settings_group',
		TNO_CHECKOUT_OPTION_KEY,
		[
			'type'              => 'array',
			'sanitize_callback' => 'tno_sanitize_checkout_settings',
			'default'           => tno_checkout_default_settings(),
		]
	);
} );

/**
 * Sanitize submitted settings payload.
 *
 * @param mixed $input
 * @return array<string, mixed>
 */
function tno_sanitize_checkout_settings( $input ): array {
	$defaults = tno_checkout_default_settings();
	$input    = is_array( $input ) ? $input : [];
	$clean    = [];

	$checkboxes = [
		'enable_delivery_schedule',
		'invoice_notice_enabled',
		'invoice_btn_enabled',
		'coupon_ticket_enabled',
		'social_section_enabled',
		'social_bale_enabled',
		'social_instagram_enabled',
		'social_telegram_enabled',
	];

	foreach ( $checkboxes as $key ) {
		$clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
	}

	$clean['delivery_start_offset'] = max( 0, min( 14, absint( $input['delivery_start_offset'] ?? $defaults['delivery_start_offset'] ) ) );
	$clean['delivery_days_count']   = max( 1, min( 14, absint( $input['delivery_days_count'] ?? $defaults['delivery_days_count'] ) ) );
	$clean['installment_count']     = max( 2, min( 24, absint( $input['installment_count'] ?? $defaults['installment_count'] ) ) );
	$clean['coupon_ticket_wc_id']   = absint( $input['coupon_ticket_wc_id'] ?? 0 );

	$text_fields = [
		'coupon_title',
		'coupon_desc',
		'coupon_placeholder',
		'invoice_notice_text',
		'installment_gateway_id',
		'success_subtitle',
		'invoice_btn_label',
		'home_btn_label',
		'coupon_ticket_percent',
		'coupon_ticket_label',
		'coupon_ticket_code',
		'failed_subtitle',
		'social_section_title',
		'social_bale_label',
		'social_instagram_label',
		'social_telegram_label',
	];

	foreach ( $text_fields as $key ) {
		$clean[ $key ] = sanitize_text_field( (string) ( $input[ $key ] ?? $defaults[ $key ] ) );
	}

	$clean['delivery_time_slots'] = sanitize_textarea_field( (string) ( $input['delivery_time_slots'] ?? $defaults['delivery_time_slots'] ) );

	$url_fields = [
		'invoice_btn_url',
		'social_bale_url',
		'social_instagram_url',
		'social_telegram_url',
	];

	foreach ( $url_fields as $key ) {
		$raw_url       = trim( (string) ( $input[ $key ] ?? '' ) );
		$clean[ $key ] = $raw_url === '#' ? '#' : esc_url_raw( $raw_url );
	}

	return $clean;
}

/**
 * Render admin settings form.
 */
function tno_render_checkout_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$opts    = tno_get_checkout_settings();
	$coupons = get_posts( [
		'post_type'      => 'shop_coupon',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );
	?>
	<div class="wrap" dir="rtl">
		<h1>تنظیمات صفحات تسویه حساب و نتیجه سفارش</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'tno_checkout_settings_group' ); ?>

			<h2 class="title">۱. زمان‌بندی ارسال (مرحله آدرس و ارسال)</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">فعال‌سازی انتخاب زمان ارسال</th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[enable_delivery_schedule]" value="1" <?php checked( 1, (int) $opts['enable_delivery_schedule'] ); ?>>
							نمایش باکس انتخاب روز و بازه ساعتی ارسال
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">شروع از چند روز آینده</th>
					<td>
						<input type="number" min="0" max="14" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[delivery_start_offset]" value="<?php echo esc_attr( (string) $opts['delivery_start_offset'] ); ?>" class="small-text">
						<p class="description">۰ = امروز، ۱ = فردا</p>
					</td>
				</tr>
				<tr>
					<th scope="row">تعداد روزهای قابل انتخاب</th>
					<td>
						<input type="number" min="1" max="14" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[delivery_days_count]" value="<?php echo esc_attr( (string) $opts['delivery_days_count'] ); ?>" class="small-text">
					</td>
				</tr>
				<tr>
					<th scope="row">بازه‌های ساعتی ارسال (هر خط یک بازه)</th>
					<td>
						<textarea name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[delivery_time_slots]" rows="4" class="large-text"><?php echo esc_textarea( (string) $opts['delivery_time_slots'] ); ?></textarea>
					</td>
				</tr>
			</table>

			<hr>
			<h2 class="title">۲. مرحله پرداخت و کد تخفیف</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">عنوان بخش کد تخفیف</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_title]" value="<?php echo esc_attr( (string) $opts['coupon_title'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row">توضیح بخش کد تخفیف</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_desc]" value="<?php echo esc_attr( (string) $opts['coupon_desc'] ); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row">متن راهنمای فیلد کد تخفیف (Placeholder)</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_placeholder]" value="<?php echo esc_attr( (string) $opts['coupon_placeholder'] ); ?>" class="regular-text" dir="ltr"></td>
				</tr>
				<tr>
					<th scope="row">باکس اطلاع‌رسانی فاکتور در سایدبار</th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[invoice_notice_enabled]" value="1" <?php checked( 1, (int) $opts['invoice_notice_enabled'] ); ?>>
							نمایش باکس یادآوری فاکتور
						</label>
						<br><br>
						<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[invoice_notice_text]" value="<?php echo esc_attr( (string) $opts['invoice_notice_text'] ); ?>" class="large-text">
					</td>
				</tr>
				<tr>
					<th scope="row">شناسه درگاه قسطی (مثل اسنپ‌پی)</th>
					<td>
						<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[installment_gateway_id]" value="<?php echo esc_attr( (string) $opts['installment_gateway_id'] ); ?>" class="regular-text" dir="ltr">
						<span style="margin-right:12px;">تعداد اقساط:</span>
						<input type="number" min="2" max="24" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[installment_count]" value="<?php echo esc_attr( (string) $opts['installment_count'] ); ?>" class="small-text">
						<p class="description">هر درگاهی که شناسه‌اش شامل این عبارت باشد (مثلاً <code>snapppay</code>) با برچسب مبلغ هر قسط نمایش داده می‌شود.</p>
					</td>
				</tr>
			</table>

			<hr>
			<h2 class="title">۳. کوپن تخفیف خرید بعدی (صفحه خرید موفق)</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">نمایش بلیط کوپن خرید بعدی</th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_ticket_enabled]" value="1" <?php checked( 1, (int) $opts['coupon_ticket_enabled'] ); ?>>
							فعال‌سازی کارت کوپن تخفیف در صفحه خرید موفق
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">اتصال مستقیم به کوپن ووکامرس (اختیاری)</th>
					<td>
						<select name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_ticket_wc_id]">
							<option value="0">— استفاده از مقادیر دستی زیر —</option>
							<?php foreach ( $coupons as $c_post ) : ?>
								<option value="<?php echo esc_attr( (string) $c_post->ID ); ?>" <?php selected( (int) $opts['coupon_ticket_wc_id'], $c_post->ID ); ?>>
									<?php echo esc_html( $c_post->post_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">در صورت انتخاب، کد و مقدار تخفیف به‌صورت خودکار از کوپن ووکامرس خوانده می‌شود.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">مقدار / درصد تخفیف (دستی)</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_ticket_percent]" value="<?php echo esc_attr( (string) $opts['coupon_ticket_percent'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row">متن توضیح داخل بلیط</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_ticket_label]" value="<?php echo esc_attr( (string) $opts['coupon_ticket_label'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row">کد تخفیف (دستی)</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[coupon_ticket_code]" value="<?php echo esc_attr( (string) $opts['coupon_ticket_code'] ); ?>" class="regular-text" dir="ltr"></td>
				</tr>
			</table>

			<hr>
			<h2 class="title">۴. متون و دکمه‌های صفحات خرید موفق و ناموفق</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">زیرعنوان خرید موفق</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[success_subtitle]" value="<?php echo esc_attr( (string) $opts['success_subtitle'] ); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row">زیرعنوان خرید ناموفق</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[failed_subtitle]" value="<?php echo esc_attr( (string) $opts['failed_subtitle'] ); ?>" class="large-text"></td>
				</tr>
				<tr>
					<th scope="row">دکمه دریافت فاکتور</th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[invoice_btn_enabled]" value="1" <?php checked( 1, (int) $opts['invoice_btn_enabled'] ); ?>>
							نمایش دکمه دریافت فاکتور
						</label>
						<br><br>
						<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[invoice_btn_label]" value="<?php echo esc_attr( (string) $opts['invoice_btn_label'] ); ?>" class="regular-text" placeholder="عنوان دکمه">
						<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[invoice_btn_url]" value="<?php echo esc_attr( (string) $opts['invoice_btn_url'] ); ?>" class="regular-text" dir="ltr" placeholder="لینک سفارشی با {order_id} (اختیاری)">
						<p class="description">اگر لینک خالی باشد، به صفحه جزئیات سفارش در حساب کاربری (یا چاپ فاکتور) هدایت می‌شود. می‌توانید از متغیر <code>{order_id}</code> برای افزونه‌های فاکتور PDF استفاده کنید.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">عنوان دکمه بازگشت به خانه</th>
					<td><input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[home_btn_label]" value="<?php echo esc_attr( (string) $opts['home_btn_label'] ); ?>" class="regular-text"></td>
				</tr>
			</table>

			<hr>
			<h2 class="title">۵. کارت‌های شبکه‌های اجتماعی (صفحه خرید موفق)</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">نمایش بخش شبکه‌های اجتماعی</th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[social_section_enabled]" value="1" <?php checked( 1, (int) $opts['social_section_enabled'] ); ?>>
							نمایش کارت‌های دنبال کردن در شبکه‌های اجتماعی
						</label>
						<br><br>
						<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[social_section_title]" value="<?php echo esc_attr( (string) $opts['social_section_title'] ); ?>" class="regular-text">
					</td>
				</tr>
				<?php
				$socials = [
					'bale'      => 'بله',
					'instagram' => 'اینستاگرام',
					'telegram'  => 'تلگرام',
				];
				foreach ( $socials as $slug => $title ) :
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $title ); ?></th>
						<td>
							<label style="margin-left:12px;">
								<input type="checkbox" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[social_<?php echo esc_attr( $slug ); ?>_enabled]" value="1" <?php checked( 1, (int) $opts[ "social_{$slug}_enabled" ] ); ?>>
								فعال
							</label>
							<input type="text" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[social_<?php echo esc_attr( $slug ); ?>_label]" value="<?php echo esc_attr( (string) $opts[ "social_{$slug}_label" ] ); ?>" class="regular-text" style="width:150px;">
							<input type="url" name="<?php echo esc_attr( TNO_CHECKOUT_OPTION_KEY ); ?>[social_<?php echo esc_attr( $slug ); ?>_url]" value="<?php echo esc_attr( (string) $opts[ "social_{$slug}_url" ] ); ?>" class="regular-text" dir="ltr" placeholder="https://...">
						</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php submit_button( 'ذخیره تنظیمات' ); ?>
		</form>
	</div>
	<?php
}