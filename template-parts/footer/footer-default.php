<?php
/**
 * Default (non Elementor) site footer.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
<footer id="colophon" class="trendino-footer">
	<div class="trendino-container">
		<div class="trendino-footer__top">
			<div class="trendino-footer-about">
				<div class="trendino-footer-about__logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php if ( has_custom_logo() ) : ?>
							<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
						<?php else : ?>
							<span class="trendino-footer-about__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				</div>

				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="trendino-footer-about__desc"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php endif; ?>
			</div>

			<?php
			$trendino_footer_menus = array(
				'footer-one'   => esc_html__( 'Customer service', 'trendino' ),
				'footer-two'   => esc_html__( 'Shopping guide', 'trendino' ),
				'footer-three' => esc_html__( 'About Trendino', 'trendino' ),
			);

			foreach ( $trendino_footer_menus as $trendino_location => $trendino_title ) :
				if ( ! has_nav_menu( $trendino_location ) ) {
					continue;
				}
				?>
				<div class="trendino-footer-links is-collapsible">
					<button type="button" class="trendino-footer-links__title" data-trendino-footer-toggle aria-expanded="false">
						<span><?php echo esc_html( $trendino_title ); ?></span>
						<?php trendino_icon( 'chevron-down', array( 'size' => 16, 'class' => 'trendino-footer-links__caret' ) ); ?>
					</button>
					<div class="trendino-footer-links__body">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $trendino_location,
								'container'      => false,
								'menu_class'     => 'trendino-footer-links__list',
								'depth'          => 1,
								'fallback_cb'    => '__return_empty_string',
							)
						);
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="trendino-footer-bottom">
			<div class="trendino-footer-bottom__start">
				<span class="trendino-footer-bottom__copyright">
					<?php
					$trendino_copyright = trendino_get_option( 'footer_copyright' );

					if ( $trendino_copyright ) {
						echo wp_kses_post( str_replace( '{year}', trendino_number( gmdate( 'Y' ) ), $trendino_copyright ) );
					} else {
						printf(
							/* translators: 1: year, 2: site name */
							esc_html__( '© %1$s all rights reserved for %2$s.', 'trendino' ),
							esc_html( trendino_number( gmdate( 'Y' ) ) ),
							esc_html( get_bloginfo( 'name' ) )
						);
					}
					?>
				</span>
			</div>
		</div>
	</div>
</footer>
