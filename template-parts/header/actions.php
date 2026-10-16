<?php
/**
 * Header action icons (default header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="trendino-header-actions has-dividers">
	<?php if ( trendino_get_option( 'header_account', true ) ) : ?>
		<div class="trendino-header-action-wrap">
			<a class="trendino-header-action trendino-header-action--account is-outline"
				href="<?php echo esc_url( trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">
				<span class="trendino-header-action__icon"><?php trendino_icon( 'user', array( 'size' => 22 ) ); ?></span>
				<span class="trendino-header-action__label">
					<?php echo esc_html( is_user_logged_in() ? __( 'My account', 'trendino' ) : __( 'Login | Register', 'trendino' ) ); ?>
				</span>
			</a>
		</div>
	<?php endif; ?>

	<?php if ( trendino_get_option( 'header_wishlist', true ) ) : ?>
		<div class="trendino-header-action-wrap">
			<a class="trendino-header-action trendino-header-action--wishlist is-icon" href="<?php echo esc_url( trendino_wishlist_url() ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'trendino' ); ?>">
				<span class="trendino-header-action__icon">
					<?php trendino_icon( 'heart', array( 'size' => 22 ) ); ?>
					<span class="trendino-header-action__count" data-trendino-count="wishlist"><?php echo esc_html( trendino_number( trendino_wishlist_count() ) ); ?></span>
				</span>
			</a>
		</div>
	<?php endif; ?>

	<?php if ( trendino_get_option( 'header_cart', true ) && trendino_is_woocommerce_active() ) : ?>
		<div class="trendino-header-action-wrap">
			<a class="trendino-header-action trendino-header-action--cart is-icon has-dropdown" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'trendino' ); ?>">
				<span class="trendino-header-action__icon">
					<?php trendino_icon( 'cart', array( 'size' => 22 ) ); ?>
					<span class="trendino-header-action__count" data-trendino-count="cart"><?php echo esc_html( trendino_number( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?></span>
				</span>
			</a>

			<div class="trendino-mini-cart" data-trendino-mini-cart>
				<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
			</div>
		</div>
	<?php endif; ?>
</div>
