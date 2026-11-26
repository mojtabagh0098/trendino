<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Blog styles exclusively on Blog / Archive / Category / Blog Page Template.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	$is_single_post = is_singular( 'post' );
	$is_blog_view   = $is_single_post
		|| is_home()
		|| is_category()
		|| is_tag()
		|| is_page_template( 'page-blog.php' )
		|| ( is_search() && get_query_var( 'post_type' ) === 'post' );

	if ( ! $is_blog_view ) {
		return;
	}

	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	$css_path = $theme_path . '/assets/css/blog.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'tno-blog-style',
			$theme_uri . '/assets/css/blog.css',
			array( 'style' ),
			(string) filemtime( $css_path )
		);
	}

	if ( $is_single_post ) {
		$js_path = $theme_path . '/assets/js/blog-single.js';
		if ( file_exists( $js_path ) ) {
			wp_enqueue_script(
				'tno-blog-single',
				$theme_uri . '/assets/js/blog-single.js',
				array(),
				(string) filemtime( $js_path ),
				true
			);

			wp_localize_script( 'tno-blog-single', 'tnoBlogConfig', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tno_blog_nonce' ),
				'i18n'    => array(
					'copied'       => 'کپی شد ✓',
					'copyBtn'      => 'کپی لینک',
					'invalidEmail' => 'لطفاً یک ایمیل معتبر وارد کنید.',
					'subscribing'  => 'در حال ثبت...',
					'subscribeBtn' => 'عضویت در خبرنامه',
					'errorGeneric' => 'خطایی رخ داد، لطفاً دوباره تلاش کنید.',
				),
			) );
		}
	}
}, 30 );

/**
 * Format post publish date in Jalali with Persian digits.
 */
function tno_blog_jalali_date( int $post_id ): string {
	$ts = (int) get_post_time( 'U', false, $post_id );
	if ( $ts <= 0 ) {
		$ts = current_time( 'timestamp' );
	}

	if ( function_exists( 'tno_gregorian_to_jalali' ) && function_exists( 'tno_fa_digits' ) ) {
		$j = tno_gregorian_to_jalali( $ts );
		return sprintf(
			'%s %s %s',
			tno_fa_digits( (string) $j['day'] ),
			$j['month_name'],
			tno_fa_digits( (string) $j['year'] )
		);
	}

	return get_the_date( '', $post_id );
}

/**
 * Get primary category name & URL for a post.
 *
 * @return array{name: string, url: string}
 */
function tno_blog_primary_category( int $post_id ): array {
	$cats = get_the_category( $post_id );
	if ( ! empty( $cats ) && $cats[0] instanceof WP_Term ) {
		return array(
			'name' => $cats[0]->name,
			'url'  => (string) get_category_link( $cats[0]->term_id ),
		);
	}

	return array(
		'name' => 'مقالات',
		'url'  => get_post_type_archive_link( 'post' ) ?: home_url( '/' ),
	);
}

/**
 * Get post thumbnail URL with clean fallback placeholder.
 */
function tno_blog_thumb_url( int $post_id, string $size = 'medium_large' ): string {
	$url = get_the_post_thumbnail_url( $post_id, $size );
	if ( $url ) {
		return (string) $url;
	}

	return get_template_directory_uri() . '/assets/img/placeholder.png';
}

/**
 * Route blog post searches (?s=...&post_type=post) to home.php instead of storefront index.php.
 */
add_filter( 'template_include', function ( string $template ): string {
	if ( is_search() && get_query_var( 'post_type' ) === 'post' ) {
		$blog_tpl = locate_template( 'home.php' );
		if ( $blog_tpl !== '' ) {
			return $blog_tpl;
		}
	}
	return $template;
}, 99 );

/**
 * Estimate article reading time in minutes (Persian digits).
 */
function tno_blog_reading_time( int $post_id ): string {
	$content    = (string) get_post_field( 'post_content', $post_id );
	$plain      = trim( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	$word_count = $plain !== '' ? count( preg_split( '/\s+/u', $plain ) ?: array() ) : 0;
	$minutes    = max( 1, (int) ceil( $word_count / 200 ) );
	$num_str    = function_exists( 'tno_fa_digits' ) ? tno_fa_digits( (string) $minutes ) : (string) $minutes;

	return $num_str . ' دقیقه زمان مطالعه';
}

/**
 * AJAX: Subscribe email to Blog Newsletter.
 */
function tno_ajax_blog_newsletter(): void {
	check_ajax_referer( 'tno_blog_nonce', 'nonce' );

	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'لطفاً یک ایمیل معتبر وارد کنید.' ), 400 );
	}

	$subscribers = get_option( 'tno_blog_newsletter_subscribers', array() );
	if ( ! is_array( $subscribers ) ) {
		$subscribers = array();
	}

	$email_lc = strtolower( $email );
	if ( ! in_array( $email_lc, $subscribers, true ) ) {
		$subscribers[] = $email_lc;
		update_option( 'tno_blog_newsletter_subscribers', $subscribers, false );
	}

	wp_send_json_success( array( 'message' => 'ایمیل شما با موفقیت در خبرنامه بلاگ ثبت شد.' ) );
}
add_action( 'wp_ajax_tno_blog_newsletter', 'tno_ajax_blog_newsletter' );
add_action( 'wp_ajax_nopriv_tno_blog_newsletter', 'tno_ajax_blog_newsletter' );
