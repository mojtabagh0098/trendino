<?php
/**
 * Product loop item — overridden so WooCommerce archives use the exact same
 * card markup as the Trendino Elementor product widgets.
 *
 * This is the only WooCommerce template the theme overrides; everything else is
 * handled through hooks so WooCommerce updates stay safe.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Trendino
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'trendino-product', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 * Kept so third-party plugins that inject markup here keep working.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	if ( class_exists( '\Trendino\Elementor\Render_Helper' ) ) {
		\Trendino\Elementor\Render_Helper::product_card( $product, trendino_wc_card_settings() );
	} else {
		// Elementor is not active — fall back to the WooCommerce loop hooks.
		do_action( 'woocommerce_before_shop_loop_item_title' );
		do_action( 'woocommerce_shop_loop_item_title' );
		do_action( 'woocommerce_after_shop_loop_item_title' );
	}

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
