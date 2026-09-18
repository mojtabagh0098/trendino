<?php
/**
 * Theme hooks: body classes, AJAX endpoints, fragments, misc filters.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra body classes.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function trendino_body_classes( $classes ) {
	$classes[] = 'trendino-theme';

	if ( trendino_get_option( 'header_sticky' ) ) {
		$classes[] = 'has-sticky-header';
	}

	if ( trendino_has_sidebar() ) {
		$classes[] = 'has-sidebar';
	} else {
		$classes[] = 'no-sidebar';
	}

	if ( trendino_is_elementor_active() ) {
		$classes[] = 'trendino-elementor';
	}

	return $classes;
}
add_filter( 'body_class', 'trendino_body_classes' );

/**
 * Allowed HTML for trust-badge embeds.
 *
 * @return array
 */
function trendino_badge_allowed_html() {
	return array(
		'a'      => array(
			'href'     => true,
			'target'   => true,
			'rel'      => true,
			'referrerpolicy' => true,
		),
		'img'    => array(
			'src'    => true,
			'alt'    => true,
			'width'  => true,
			'height' => true,
			'style'  => true,
			'id'     => true,
			'class'  => true,
			'loading'=> true,
		),
		'div'    => array( 'class' => true, 'id' => true, 'style' => true ),
		'span'   => array( 'class' => true, 'id' => true, 'style' => true ),
	);
}

/**
 * Excerpt length.
 *
 * @param int $length Length.
 * @return int
 */
function trendino_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'trendino_excerpt_length' );

/**
 * Excerpt more string.
 *
 * @return string
 */
function trendino_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'trendino_excerpt_more' );

/**
 * Register the REST/AJAX endpoints used by the front-end JS.
 *
 * @return void
 */
function trendino_register_ajax() {
	add_action( 'wp_ajax_trendino_search', 'trendino_ajax_search' );
	add_action( 'wp_ajax_nopriv_trendino_search', 'trendino_ajax_search' );

	add_action( 'wp_ajax_trendino_newsletter', 'trendino_ajax_newsletter' );
	add_action( 'wp_ajax_nopriv_trendino_newsletter', 'trendino_ajax_newsletter' );

	add_action( 'wp_ajax_trendino_wishlist', 'trendino_ajax_wishlist' );
	add_action( 'wp_ajax_nopriv_trendino_wishlist', 'trendino_ajax_wishlist' );
}
add_action( 'init', 'trendino_register_ajax' );

/**
 * AJAX: live product search suggestions.
 *
 * @return void
 */
function trendino_ajax_search() {
	check_ajax_referer( 'trendino_nonce', 'nonce' );

	$term  = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
	$scope = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'product';

	if ( mb_strlen( $term ) < 2 ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	$post_type = ( 'product' === $scope && trendino_is_woocommerce_active() ) ? 'product' : 'any';

	$query = new WP_Query(
		array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			's'                   => $term,
			'posts_per_page'      => 6,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
		)
	);

	$items = array();

	foreach ( $query->posts as $post ) {
		$item = array(
			'title' => get_the_title( $post ),
			'url'   => get_permalink( $post ),
			'image' => get_the_post_thumbnail_url( $post, 'thumbnail' ),
			'price' => '',
		);

		if ( 'product' === $post->post_type && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post->ID );

			if ( $product ) {
				$item['price'] = wp_strip_all_tags( $product->get_price_html() );
			}
		}

		$items[] = $item;
	}

	wp_send_json_success( array( 'items' => $items ) );
}

/**
 * AJAX: newsletter subscribe.
 *
 * @return void
 */
function trendino_ajax_newsletter() {
	check_ajax_referer( 'trendino_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Please enter a valid email address.', 'trendino' ) ) );
	}

	$subscribers = get_option( 'trendino_subscribers', array() );
	$subscribers = is_array( $subscribers ) ? $subscribers : array();

	if ( in_array( $email, $subscribers, true ) ) {
		wp_send_json_success( array( 'message' => esc_html__( 'You are already subscribed.', 'trendino' ) ) );
	}

	$subscribers[] = $email;
	update_option( 'trendino_subscribers', array_slice( $subscribers, -5000 ), false );

	/**
	 * Fires after a visitor subscribes to the newsletter.
	 *
	 * @param string $email Email address.
	 */
	do_action( 'trendino/newsletter/subscribed', $email );

	wp_send_json_success( array( 'message' => esc_html__( 'Thanks! Your subscription is confirmed.', 'trendino' ) ) );
}

/**
 * AJAX: toggle a product in the built-in cookie wishlist.
 *
 * Third party wishlist plugins keep their own handlers; this is only the fallback.
 *
 * @return void
 */
function trendino_ajax_wishlist() {
	check_ajax_referer( 'trendino_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

	if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid product.', 'trendino' ) ) );
	}

	$cookie = isset( $_COOKIE['trendino_wishlist'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['trendino_wishlist'] ) ) : '';
	$ids    = array_filter( array_map( 'absint', explode( ',', $cookie ) ) );

	if ( in_array( $product_id, $ids, true ) ) {
		$ids     = array_diff( $ids, array( $product_id ) );
		$active  = false;
		$message = esc_html__( 'Removed from wishlist.', 'trendino' );
	} else {
		$ids[]   = $product_id;
		$active  = true;
		$message = esc_html__( 'Added to wishlist.', 'trendino' );
	}

	$value = implode( ',', array_unique( $ids ) );

	setcookie( 'trendino_wishlist', $value, time() + MONTH_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), false );

	wp_send_json_success(
		array(
			'active'  => $active,
			'count'   => count( $ids ),
			'message' => $message,
		)
	);
}

/**
 * Add the cart counter to WooCommerce AJAX fragments so it stays in sync.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function trendino_cart_fragments( $fragments ) {
	if ( ! trendino_is_woocommerce_active() || ! WC()->cart ) {
		return $fragments;
	}

	ob_start();
	?>
	<span class="trendino-header-action__count" data-trendino-count="cart"><?php echo esc_html( trendino_number( WC()->cart->get_cart_contents_count() ) ); ?></span>
	<?php
	$fragments['[data-trendino-count="cart"]'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'trendino_cart_fragments' );

/**
 * Register the Trendino shortcodes.
 *
 * @return void
 */
function trendino_register_shortcodes() {
	add_shortcode( 'trendino_year', 'trendino_shortcode_year' );
}
add_action( 'init', 'trendino_register_shortcodes' );

/**
 * [trendino_year] shortcode.
 *
 * @return string
 */
function trendino_shortcode_year() {
	return esc_html( trendino_number( gmdate( 'Y' ) ) );
}

/**
 * Remove the default WooCommerce "shop page" H1 duplication when Elementor builds the header.
 *
 * @param array $args Arguments.
 * @return array
 */
function trendino_wc_breadcrumb_defaults( $args ) {
	$args['home'] = esc_html__( 'Home', 'trendino' );

	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'trendino_wc_breadcrumb_defaults' );

/**
 * Make sure Persian digits are also applied to WooCommerce prices when enabled.
 *
 * @param string $formatted Formatted price.
 * @return string
 */
function trendino_localize_price( $formatted ) {
	if ( ! trendino_get_option( 'persian_digits', is_rtl() ) ) {
		return $formatted;
	}

	return trendino_convert_digits( $formatted );
}
add_filter( 'wc_price', 'trendino_localize_price', 20 );
