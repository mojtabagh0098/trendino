<?php
/**
 * Header top bar (default header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

$trendino_message = trendino_get_option( 'topbar_text' );
?>
<div class="trendino-topbar hide-links-mobile">
	<?php if ( has_nav_menu( 'topbar' ) ) : ?>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'topbar',
				'container'      => false,
				'menu_class'     => 'trendino-topbar__links',
				'depth'          => 1,
				'fallback_cb'    => '__return_empty_string',
			)
		);
		?>
	<?php else : ?>
		<ul class="trendino-topbar__links">
			<?php if ( trendino_is_woocommerce_active() ) : ?>
				<li>
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
						<?php trendino_icon( 'truck', array( 'size' => 16 ) ); ?>
						<span><?php esc_html_e( 'Order tracking', 'trendino' ); ?></span>
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php trendino_icon( 'headset', array( 'size' => 16 ) ); ?>
					<span><?php esc_html_e( 'Contact us', 'trendino' ); ?></span>
				</a>
			</li>
		</ul>
	<?php endif; ?>

	<div class="trendino-topbar__end">
		<?php if ( $trendino_message ) : ?>
			<span class="trendino-topbar__message">
				<?php trendino_icon( 'truck', array( 'size' => 16 ) ); ?>
				<span><?php echo esc_html( $trendino_message ); ?></span>
			</span>
		<?php endif; ?>
	</div>
</div>
