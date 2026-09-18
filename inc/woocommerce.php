<?php
/**
 * WooCommerce integration.
 *
 * The theme keeps template overrides to the minimum: layout wrappers, the product
 * loop card (so archives match the Elementor widgets) and a few hook re-orders.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

if ( ! trendino_is_woocommerce_active() ) {
	return;
}

/**
 * Remove the default WooCommerce wrappers, we use our own.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open the shop content wrapper.
 *
 * @return void
 */
function trendino_wc_wrapper_start() {
	$layout = trendino_get_option( 'shop_layout', 'sidebar-right' );

	echo '<div class="trendino-shop trendino-container layout-' . esc_attr( $layout ) . '">';
	echo '<main id="primary" class="trendino-shop__main">';
}
add_action( 'woocommerce_before_main_content', 'trendino_wc_wrapper_start', 10 );

/**
 * Close the shop content wrapper and print the sidebar.
 *
 * @return void
 */
function trendino_wc_wrapper_end() {
	echo '</main>';

	if ( trendino_has_sidebar() ) {
		echo '<aside class="trendino-shop__sidebar widget-area">';
		dynamic_sidebar( 'sidebar-shop' );
		echo '</aside>';
	}

	echo '</div>';
}
add_action( 'woocommerce_after_main_content', 'trendino_wc_wrapper_end', 10 );

/**
 * Products per row on archives.
 *
 * @return int
 */
function trendino_wc_loop_columns() {
	return max( 2, (int) trendino_get_option( 'shop_columns', 4 ) );
}
add_filter( 'loop_shop_columns', 'trendino_wc_loop_columns', 20 );

/**
 * Products per page on archives.
 *
 * @return int
 */
function trendino_wc_products_per_page() {
	return max( 1, (int) trendino_get_option( 'shop_products_count', 20 ) );
}
add_filter( 'loop_shop_per_page', 'trendino_wc_products_per_page', 20 );

/**
 * Related products count = the archive column count.
 *
 * @param array $args Args.
 * @return array
 */
function trendino_wc_related_args( $args ) {
	$columns = trendino_wc_loop_columns();

	$args['posts_per_page'] = $columns;
	$args['columns']        = $columns;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'trendino_wc_related_args', 20 );

/**
 * Card settings derived from the theme options, used by the loop template.
 *
 * @return array
 */
function trendino_wc_card_settings() {
	return apply_filters(
		'trendino/woocommerce/card_settings',
		array(
			'show_image'     => 'yes',
			'show_badge'     => trendino_get_option( 'sale_badge', true ) ? 'yes' : '',
			'show_discount'  => trendino_get_option( 'sale_badge', true ) ? 'yes' : '',
			'show_title'     => 'yes',
			'show_rating'    => 'yes',
			'show_price'     => 'yes',
			'show_old_price' => 'yes',
			'show_cart'      => 'yes',
			'show_wishlist'  => trendino_get_option( 'product_wishlist', true ) ? 'yes' : '',
			'show_quickview' => trendino_get_option( 'product_quick_view', true ) ? 'yes' : '',
			'show_compare'   => trendino_get_option( 'product_compare', true ) ? 'yes' : '',
			'card_layout'    => trendino_get_option( 'product_card_style', 'default' ) === 'default' ? 'vertical' : trendino_get_option( 'product_card_style' ),
			'image_ratio'    => '1-1',
			'title_length'   => 70,
		)
	);
}

/**
 * Show the cart counter in the WooCommerce "added to cart" message area.
 *
 * @param int $count Count.
 * @return int
 */
function trendino_wc_cart_count( $count ) {
	return $count;
}
add_filter( 'woocommerce_cart_contents_count', 'trendino_wc_cart_count' );

/**
 * Replace the default WooCommerce placeholder image with the theme one.
 *
 * @param string $src Image URL.
 * @return string
 */
function trendino_wc_placeholder( $src ) {
	return trendino_placeholder_image();
}
add_filter( 'woocommerce_placeholder_img_src', 'trendino_wc_placeholder' );

/**
 * Single product layout tweaks: move the price above the excerpt and add the
 * theme's trust row under the add-to-cart form.
 *
 * @return void
 */
function trendino_wc_single_layout() {
	// Show the breadcrumb through the theme instead.
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
}
add_action( 'wp', 'trendino_wc_single_layout' );

/**
 * Number of thumbnails per row in the product gallery.
 *
 * @return int
 */
function trendino_wc_gallery_columns() {
	return 5;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'trendino_wc_gallery_columns' );

/**
 * Change the "Add to cart" text on archives for out-of-stock/variable products.
 *
 * @param string     $text    Text.
 * @param WC_Product $product Product.
 * @return string
 */
function trendino_wc_add_to_cart_text( $text, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $text;
	}

	if ( ! $product->is_in_stock() ) {
		return esc_html__( 'Out of stock', 'trendino' );
	}

	if ( $product->is_type( 'variable' ) ) {
		return esc_html__( 'Select options', 'trendino' );
	}

	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'trendino_wc_add_to_cart_text', 10, 2 );

/**
 * Wrap the archive result count + ordering in a toolbar element.
 *
 * @return void
 */
function trendino_wc_toolbar_open() {
	echo '<div class="trendino-shop-toolbar">';
}
add_action( 'woocommerce_before_shop_loop', 'trendino_wc_toolbar_open', 19 );

/**
 * Close the toolbar element.
 *
 * @return void
 */
function trendino_wc_toolbar_close() {
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'trendino_wc_toolbar_close', 31 );

/**
 * Declare compatibility with WooCommerce HPOS / block features where relevant.
 *
 * @return void
 */
function trendino_wc_declare_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'trendino_wc_declare_compatibility' );
