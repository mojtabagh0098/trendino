<?php
/**
 * Sidebar.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

if ( ! trendino_has_sidebar() ) {
	return;
}

$trendino_sidebar = ( trendino_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() ) ) ? 'sidebar-shop' : 'sidebar-blog';

if ( ! is_active_sidebar( $trendino_sidebar ) ) {
	return;
}
?>
<aside id="secondary" class="trendino-sidebar widget-area">
	<?php dynamic_sidebar( $trendino_sidebar ); ?>
</aside>
