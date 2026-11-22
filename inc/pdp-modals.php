<?php
/**
 * PDP Modals: Review, Question, Answer & Auth Gate
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

// در صورت استفاده از نوع دیدگاه متفاوت در inc/pdp-functions.php، این دو ثابت را هماهنگ کنید.
if ( ! defined( 'TNO_QA_QUESTION_TYPE' ) ) {
	define( 'TNO_QA_QUESTION_TYPE', 'tno_question' );
}
if ( ! defined( 'TNO_QA_ANSWER_TYPE' ) ) {
	define( 'TNO_QA_ANSWER_TYPE', 'tno_answer' );
}

/**
 * بارگذاری استایل و اسکریپت اختصاصی مودال‌ها فقط در صفحه تکی محصول.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	if ( ! is_product() ) {
		return;
	}

	$product_id = get_queried_object_id();
	$theme_uri  = get_template_directory_uri();
	$theme_dir  = get_template_directory();

	$css_path = '/assets/css/pdp-modals.css';
	$js_path  = '/assets/js/pdp-modals.js';

	wp_enqueue_style(
		'tno-pdp-modals',
		$theme_uri . $css_path,
		[],
		file_exists( $theme_dir . $css_path ) ? (string) filemtime( $theme_dir . $css_path ) : '1.0.0'
	);

	wp_enqueue_script(
		'tno-pdp-modals',
		$theme_uri . $js_path,
		[],
		file_exists( $theme_dir . $js_path ) ? (string) filemtime( $theme_dir . $js_path ) : '1.0.0',
		true
	);

	// لینک صفحه ورود قالب (قابل تنظیم با فیلتر tno_pdp_auth_url برای هماهنگی با inc/login.php)
	$default_auth_url = add_query_arg(
		'redirect_to',
		rawurlencode( get_permalink( $product_id ) ),
		wc_get_page_permalink( 'myaccount' )
	);

	wp_localize_script(
		'tno-pdp-modals',
		'tnoPdpModalsConfig',
		[
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'tno_pdp_modals_nonce' ),
			'productId'  => $product_id,
			'isLoggedIn' => is_user_logged_in(),
			'authUrl'    => esc_url_raw( apply_filters( 'tno_pdp_auth_url', $default_auth_url, $product_id ) ),
			'i18n'       => [
				'authReviewTitle' => 'برای ثبت دیدگاه باید وارد حساب کاربری خود شوید',
				'authQaTitle'     => 'برای ثبت پرسش و پاسخ باید وارد حساب کاربری خود شوید',
				'ratingRequired'  => 'لطفاً امتیاز خود را بین ۱ تا ۵ ستاره انتخاب کنید.',
				'contentTooShort' => 'متن ارسالی باید حداقل ۵ کاراکتر باشد.',
				'networkError'    => 'خطا در برقراری ارتباط با سرور. لطفاً دوباره تلاش کنید.',
				'submitting'      => 'در حال ارسال...',
			],
		]
	);
}, 25 );

/**
 * رندر قالب HTML مودال‌ها در فوتر صفحه محصول.
 */
add_action( 'wp_footer', function (): void {
	if ( ! is_product() ) {
		return;
	}
	get_template_part( 'template-parts/components/pdp-modals' );
}, 30 );

/**
 * جلوگیری از نمایش پرسش و پاسخ‌ها در لیست استاندارد دیدگاه‌های ووکامرس.
 */
add_action( 'pre_get_comments', function ( WP_Comment_Query $query ): void {
	if ( is_admin() ) {
		return;
	}

	$type = $query->query_vars['type'] ?? '';
	if ( '' === $type || 'comment' === $type || 'review' === $type ) {
		$not_in = (array) ( $query->query_vars['type__not_in'] ?? [] );
		$query->query_vars['type__not_in'] = array_unique(
			array_merge( $not_in, [ TNO_QA_QUESTION_TYPE, TNO_QA_ANSWER_TYPE ] )
		);
	}
} );

/**
 * کنترل مشترک احراز هویت، Nonce، اعتبارسنجی محصول و Rate Limit.
 *
 * @return array{user: WP_User, product: WC_Product, content: string}
 */
function tno_pdp_validate_modal_request( string $rate_key_prefix ): array {
	if ( ! check_ajax_referer( 'tno_pdp_modals_nonce', 'nonce', false ) ) {
		wp_send_json_error(
			[
				'code'    => 'invalid_nonce',
				'message' => 'نشست امنیتی منقضی شده است. صفحه را بازنشانی کنید.',
			],
			403
		);
	}

	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			[
				'code'    => 'auth_required',
				'message' => 'برای انجام این عملیات باید وارد حساب کاربری خود شوید.',
			],
			401
		);
	}

	$user_id  = get_current_user_id();
	$rate_key = sprintf( 'tno_rate_%s_%d', $rate_key_prefix, $user_id );
	if ( get_transient( $rate_key ) ) {
		wp_send_json_error(
			[
				'code'    => 'rate_limited',
				'message' => 'لطفاً چند ثانیه صبر کرده و سپس دوباره ارسال کنید.',
			],
			429
		);
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || 'publish' !== get_post_status( $product_id ) ) {
		wp_send_json_error(
			[
				'code'    => 'invalid_product',
				'message' => 'محصول مورد نظر معتبر نیست.',
			],
			404
		);
	}

	$content = isset( $_POST['content'] )
		? trim( sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) )
		: '';

	$length = mb_strlen( $content, 'UTF-8' );
	if ( $length < 5 || $length > 2000 ) {
		wp_send_json_error(
			[
				'code'    => 'invalid_content',
				'message' => 'متن ارسالی باید بین ۵ تا ۲۰۰۰ کاراکتر باشد.',
			],
			422
		);
	}

	set_transient( $rate_key, 1, 15 );

	return [
		'user'    => wp_get_current_user(),
		'product' => $product,
		'content' => $content,
	];
}

/**
 * ذخیره ایمن دیدگاه با بررسی Duplicate/Moderation وردپرس.
 *
 * @param array<string, mixed> $commentdata
 * @return array{comment_id: int, approved: int}
 */
function tno_pdp_insert_moderated_comment( array $commentdata ): array {
	$approved = wp_allow_comment( $commentdata, true );

	if ( is_wp_error( $approved ) ) {
		$code = $approved->get_error_code();
		$msg  = ( 'comment_duplicate' === $code )
			? 'شما قبلاً دقیقاً همین متن را ارسال کرده‌اید.'
			: 'ارسال متوالی مجاز نیست. لطفاً کمی بعد تلاش کنید.';

		wp_send_json_error(
			[
				'code'    => $code,
				'message' => $msg,
			],
			409
		);
	}

	$commentdata['comment_approved'] = ( 'spam' === $approved ) ? 'spam' : (int) $approved;
	$comment_id                      = wp_insert_comment( $commentdata );

	if ( ! $comment_id ) {
		wp_send_json_error(
			[
				'code'    => 'insert_failed',
				'message' => 'خطا در ذخیره‌سازی اطلاعات. لطفاً دوباره تلاش کنید.',
			],
			500
		);
	}

	return [
		'comment_id' => (int) $comment_id,
		'approved'   => (int) $commentdata['comment_approved'],
	];
}

/**
 * 1) AJAX: ثبت دیدگاه محصول (بدون محدودیت خرید قبلی؛ فقط نیازمند لاگین).
 */
function tno_ajax_submit_product_review(): void {
	$ctx = tno_pdp_validate_modal_request( 'review' );

	$rating = isset( $_POST['rating'] ) ? absint( wp_unslash( $_POST['rating'] ) ) : 0;
	if ( $rating < 1 || $rating > 5 ) {
		wp_send_json_error(
			[
				'code'    => 'invalid_rating',
				'message' => 'لطفاً امتیازی بین ۱ تا ۵ ستاره انتخاب کنید.',
			],
			422
		);
	}

	$user       = $ctx['user'];
	$product_id = $ctx['product']->get_id();

	$result = tno_pdp_insert_moderated_comment(
		[
			'comment_post_ID'      => $product_id,
			'comment_author'       => $user->display_name ?: $user->user_login,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => $ctx['content'],
			'comment_type'         => 'review',
			'comment_parent'       => 0,
			'user_id'              => $user->ID,
			'comment_author_IP'    => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
			'comment_agent'        => substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ), 0, 254 ),
		]
	);

	update_comment_meta( $result['comment_id'], 'rating', $rating );

	// ثبت دقیق وضعیت خریدار واقعی بدون جلوگیری از ثبت دیدگاه کاربران غیرخریدار
	$is_verified = wc_customer_bought_product( $user->user_email, $user->ID, $product_id ) ? 1 : 0;
	update_comment_meta( $result['comment_id'], 'verified', $is_verified );

	if ( 1 === $result['approved'] && class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $product_id );
	}

	$message = ( 1 === $result['approved'] )
		? 'دیدگاه شما با موفقیت ثبت و منتشر شد.'
		: 'دیدگاه شما با موفقیت ثبت شد و پس از تایید منتشر خواهد شد.';

	wp_send_json_success(
		[
			'comment_id' => $result['comment_id'],
			'approved'   => ( 1 === $result['approved'] ),
			'message'    => $message,
		]
	);
}
add_action( 'wp_ajax_tno_modal_submit_review', 'tno_ajax_submit_product_review' );
add_action( 'wp_ajax_nopriv_tno_modal_submit_review', 'tno_ajax_submit_product_review' );

/**
 * 2) AJAX: ثبت پرسش جدید برای محصول.
 */
function tno_ajax_submit_product_question(): void {
	$ctx        = tno_pdp_validate_modal_request( 'question' );
	$user       = $ctx['user'];
	$product_id = $ctx['product']->get_id();

	$result = tno_pdp_insert_moderated_comment(
		[
			'comment_post_ID'      => $product_id,
			'comment_author'       => $user->display_name ?: $user->user_login,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => $ctx['content'],
			'comment_type'         => TNO_QA_QUESTION_TYPE,
			'comment_parent'       => 0,
			'user_id'              => $user->ID,
			'comment_author_IP'    => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
			'comment_agent'        => substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ), 0, 254 ),
		]
	);

	$message = ( 1 === $result['approved'] )
		? 'پرسش شما با موفقیت ثبت و منتشر شد.'
		: 'پرسش شما با موفقیت ثبت شد و پس از تایید نمایش داده می‌شود.';

	wp_send_json_success(
		[
			'comment_id' => $result['comment_id'],
			'approved'   => ( 1 === $result['approved'] ),
			'message'    => $message,
		]
	);
}
add_action( 'wp_ajax_tno_modal_submit_question', 'tno_ajax_submit_product_question' );
add_action( 'wp_ajax_nopriv_tno_modal_submit_question', 'tno_ajax_submit_product_question' );

/**
 * 3) AJAX: ثبت پاسخ به یک پرسش مشخص.
 */
function tno_ajax_submit_product_answer(): void {
	$ctx         = tno_pdp_validate_modal_request( 'answer' );
	$user        = $ctx['user'];
	$product_id  = $ctx['product']->get_id();
	$question_id = isset( $_POST['question_id'] ) ? absint( wp_unslash( $_POST['question_id'] ) ) : 0;

	$parent_comment = $question_id ? get_comment( $question_id ) : null;
	if (
		! $parent_comment
		|| (int) $parent_comment->comment_post_ID !== $product_id
		|| (int) $parent_comment->comment_parent !== 0
		|| $parent_comment->comment_type !== TNO_QA_QUESTION_TYPE
		|| '1' !== (string) $parent_comment->comment_approved
	) {
		wp_send_json_error(
			[
				'code'    => 'invalid_question',
				'message' => 'پرسش مورد نظر یافت نشد یا مجاز به پاسخ‌دهی نیست.',
			],
			404
		);
	}

	$result = tno_pdp_insert_moderated_comment(
		[
			'comment_post_ID'      => $product_id,
			'comment_author'       => $user->display_name ?: $user->user_login,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => '',
			'comment_content'      => $ctx['content'],
			'comment_type'         => TNO_QA_ANSWER_TYPE,
			'comment_parent'       => $question_id,
			'user_id'              => $user->ID,
			'comment_author_IP'    => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
			'comment_agent'        => substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ), 0, 254 ),
		]
	);

	$message = ( 1 === $result['approved'] )
		? 'پاسخ شما با موفقیت ثبت و منتشر شد.'
		: 'پاسخ شما با موفقیت ثبت شد و پس از تایید نمایش داده می‌شود.';

	wp_send_json_success(
		[
			'comment_id' => $result['comment_id'],
			'approved'   => ( 1 === $result['approved'] ),
			'message'    => $message,
		]
	);
}
add_action( 'wp_ajax_tno_modal_submit_answer', 'tno_ajax_submit_product_answer' );
add_action( 'wp_ajax_nopriv_tno_modal_submit_answer', 'tno_ajax_submit_product_answer' );