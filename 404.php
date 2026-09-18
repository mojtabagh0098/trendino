<?php
/**
 * 404 page.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="trendino-container trendino-404">
	<main id="primary" class="trendino-404__inner">
		<div class="trendino-404__code"><?php echo esc_html( trendino_number( '404' ) ); ?></div>
		<h1 class="trendino-404__title"><?php esc_html_e( 'Page not found', 'trendino' ); ?></h1>
		<p class="trendino-404__text"><?php esc_html_e( 'The page you are looking for has been moved or no longer exists.', 'trendino' ); ?></p>

		<div class="trendino-404__search"><?php get_search_form(); ?></div>

		<a class="trendino-button trendino-button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to home', 'trendino' ); ?>
		</a>
	</main>
</div>
<?php
get_footer();
