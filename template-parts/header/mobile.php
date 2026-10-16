<?php
/**
 * Mobile drawer + bottom navigation (default header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="trendino-mobile-header">
	<form role="search" method="get" class="trendino-mobile-header__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php if ( trendino_is_woocommerce_active() ) : ?>
			<input type="hidden" name="post_type" value="product" />
		<?php endif; ?>
		<?php trendino_icon( 'search', array( 'size' => 18 ) ); ?>
		<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products…', 'trendino' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>" />
	</form>

	<div class="trendino-drawer" data-trendino-drawer hidden>
		<div class="trendino-drawer__backdrop" data-trendino-drawer-close></div>
		<aside class="trendino-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile menu', 'trendino' ); ?>">
			<div class="trendino-drawer__head">
				<span class="trendino-drawer__title"><?php esc_html_e( 'Menu', 'trendino' ); ?></span>
				<button type="button" class="trendino-drawer__close" data-trendino-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'trendino' ); ?>">
					<?php trendino_icon( 'close', array( 'size' => 22 ) ); ?>
				</button>
			</div>

			<div class="trendino-drawer__account">
				<?php trendino_icon( 'user', array( 'size' => 22 ) ); ?>
				<a href="<?php echo esc_url( trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">
					<?php echo esc_html( is_user_logged_in() ? wp_get_current_user()->display_name : __( 'Login | Register', 'trendino' ) ); ?>
				</a>
			</div>

			<?php if ( has_nav_menu( 'mobile' ) || has_nav_menu( 'primary' ) ) : ?>
				<nav class="trendino-drawer__menu" aria-label="<?php esc_attr_e( 'Mobile navigation', 'trendino' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
							'container'      => false,
							'menu_class'     => 'trendino-drawer__list',
							'depth'          => 3,
							'fallback_cb'    => '__return_empty_string',
							'walker'         => new Trendino_Menu_Walker(),
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php if ( trendino_is_woocommerce_active() ) : ?>
				<div class="trendino-drawer__categories">
					<span class="trendino-drawer__section-title"><?php esc_html_e( 'Shop by category', 'trendino' ); ?></span>
					<ul>
						<?php foreach ( \Trendino\Elementor\Query_Helper::product_categories( array( 'parent' => 0, 'number' => 12 ) ) as $trendino_term ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $trendino_term ) ); ?>">
									<?php echo trendino_category_thumbnail_html( $trendino_term, 'trendino-story', 'trendino-drawer__thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span><?php echo esc_html( $trendino_term->name ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</div>

<nav class="trendino-bottom-nav" aria-label="<?php esc_attr_e( 'Mobile quick navigation', 'trendino' ); ?>">
	<a class="trendino-bottom-nav__item<?php echo is_front_page() ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<span class="trendino-bottom-nav__icon"><?php trendino_icon( 'grid', array( 'size' => 22 ) ); ?></span>
		<span class="trendino-bottom-nav__label"><?php esc_html_e( 'Home', 'trendino' ); ?></span>
	</a>

	<?php if ( trendino_is_woocommerce_active() ) : ?>
		<a class="trendino-bottom-nav__item" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
			<span class="trendino-bottom-nav__icon"><?php trendino_icon( 'category', array( 'size' => 22 ) ); ?></span>
			<span class="trendino-bottom-nav__label"><?php esc_html_e( 'Categories', 'trendino' ); ?></span>
		</a>

		<a class="trendino-bottom-nav__item<?php echo is_cart() ? ' is-active' : ''; ?>" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<span class="trendino-bottom-nav__icon">
				<?php trendino_icon( 'cart', array( 'size' => 22 ) ); ?>
				<span class="trendino-bottom-nav__count" data-trendino-count="cart"><?php echo esc_html( trendino_number( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?></span>
			</span>
			<span class="trendino-bottom-nav__label"><?php esc_html_e( 'Cart', 'trendino' ); ?></span>
		</a>
	<?php endif; ?>

	<a class="trendino-bottom-nav__item" href="<?php echo esc_url( trendino_wishlist_url() ); ?>">
		<span class="trendino-bottom-nav__icon"><?php trendino_icon( 'heart', array( 'size' => 22 ) ); ?></span>
		<span class="trendino-bottom-nav__label"><?php esc_html_e( 'Wishlist', 'trendino' ); ?></span>
	</a>

	<a class="trendino-bottom-nav__item" href="<?php echo esc_url( trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">
		<span class="trendino-bottom-nav__icon"><?php trendino_icon( 'user', array( 'size' => 22 ) ); ?></span>
		<span class="trendino-bottom-nav__label"><?php esc_html_e( 'Account', 'trendino' ); ?></span>
	</a>
</nav>
