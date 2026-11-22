<?php
/** Product gallery and price history support. */
defined( 'ABSPATH' ) || exit;

/** Record only actual price changes, using WooCommerce's saved product values. */
function tno_record_price_history( $product ) {
    if ( ! $product instanceof WC_Product || $product->is_type( 'variation' ) ) return;
    $regular = $product->is_type( 'variable' ) ? $product->get_variation_regular_price( 'min' ) : $product->get_regular_price();
    $price = $product->is_type( 'variable' ) ? $product->get_variation_price( 'min' ) : $product->get_price();
    if ( '' === (string) $regular && '' === (string) $price ) return;
    if ( '' === (string) $regular ) $regular = $price;
    if ( '' === (string) $price ) $price = $regular;
    $history = get_post_meta( $product->get_id(), '_tno_price_history', true );
    $history = is_array( $history ) ? $history : array();
    $last = end( $history );
    $currency = get_woocommerce_currency();
    if ( is_array( $last ) && (string) $last['regular'] === (string) $regular && (string) $last['price'] === (string) $price && ( $last['currency'] ?? '' ) === $currency ) return;
    $history[] = array( 'time' => time(), 'regular' => (string) $regular, 'price' => (string) $price, 'currency' => $currency );
    update_post_meta( $product->get_id(), '_tno_price_history', array_slice( $history, -100 ) );
}
add_action( 'woocommerce_after_product_object_save', 'tno_record_price_history', 30 );
/** Variation saves synchronize the parent first, then snapshot the parent's displayed minimum prices. */
add_action( 'woocommerce_update_product_variation', function ( $variation_id ) {
    $parent_id = wp_get_post_parent_id( $variation_id );
    if ( ! $parent_id ) return;
    WC_Product_Variable::sync( $parent_id );
    $parent = wc_get_product( $parent_id );
    if ( $parent ) tno_record_price_history( $parent );
}, 30 );

/** Seed one real current price for products that existed before history was enabled. */
add_action( 'template_redirect', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) return;
    $id = get_queried_object_id();
    if ( ! $id || metadata_exists( 'post', $id, '_tno_price_history' ) ) return;
    $product = wc_get_product( $id );
    if ( $product ) tno_record_price_history( $product );
}, 30 );

/** Mark successful native form submissions on this request (variable/grouped products). */
add_action( 'woocommerce_add_to_cart', function ( $cart_item_key, $product_id ) {
    if ( ! wp_doing_ajax() && isset( $_POST['add-to-cart'] ) && absint( wp_unslash( $_POST['add-to-cart'] ) ) === (int) $product_id ) {
        $GLOBALS['tno_cart_added_product'] = (int) $product_id;
    }
}, 10, 2 );
