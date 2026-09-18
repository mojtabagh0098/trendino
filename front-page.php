<?php
/**
 * Front page.
 *
 * If a static page is assigned it is rendered (so an Elementor built homepage works),
 * otherwise the blog index is used.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'index' );
	return;
}

get_header();
?>
<main id="primary" class="trendino-front-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
