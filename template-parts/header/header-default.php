<?php
/**
 * Default (non Elementor) site header.
 *
 * Uses exactly the same markup/classes as the Trendino header widgets, so a site
 * looks identical before the user builds a custom header with Elementor.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

$trendino_sticky = trendino_get_option( 'header_sticky', true );
?>
<header id="masthead" class="trendino-header<?php echo $trendino_sticky ? ' is-sticky' : ''; ?>" data-trendino-header>
	<?php if ( trendino_get_option( 'topbar_enabled', true ) ) : ?>
		<div class="trendino-header__topbar">
			<div class="trendino-container">
				<?php get_template_part( 'template-parts/header/topbar' ); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="trendino-header__main">
		<div class="trendino-container trendino-header__row">
			<div class="trendino-header__start">
				<button type="button" class="trendino-header__burger" data-trendino-drawer-open aria-label="<?php esc_attr_e( 'Open menu', 'trendino' ); ?>" aria-expanded="false">
					<?php trendino_icon( 'menu', array( 'size' => 24 ) ); ?>
				</button>

				<div class="trendino-logo">
					<a class="trendino-logo__link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php if ( has_custom_logo() ) : ?>
							<span class="trendino-logo__image"><?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?></span>
						<?php else : ?>
							<span class="trendino-logo__text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				</div>
			</div>

			<?php if ( trendino_get_option( 'header_search', true ) ) : ?>
				<div class="trendino-header__search">
					<?php get_template_part( 'template-parts/header/search' ); ?>
				</div>
			<?php endif; ?>

			<div class="trendino-header__end">
				<?php get_template_part( 'template-parts/header/actions' ); ?>
			</div>
		</div>
	</div>

	<div class="trendino-header__nav">
		<div class="trendino-container trendino-header__nav-row">
			<?php get_template_part( 'template-parts/header/category-menu' ); ?>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav class="trendino-menu trendino-menu--horizontal has-indicator" aria-label="<?php esc_attr_e( 'Main navigation', 'trendino' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'trendino-menu__list',
							'depth'          => 3,
							'fallback_cb'    => '__return_empty_string',
							'walker'         => new Trendino_Menu_Walker(),
						)
					);
					?>
				</nav>
			<?php endif; ?>
		</div>
	</div>

	<?php get_template_part( 'template-parts/header/mobile' ); ?>
</header>
