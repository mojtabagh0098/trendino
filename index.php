<?php
/**
 * Main template.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

get_header();

$trendino_layout = trendino_get_option( 'blog_layout', 'sidebar-right' );
?>
<div class="trendino-container trendino-archive layout-<?php echo esc_attr( $trendino_layout ); ?>">
	<main id="primary" class="trendino-archive__main">
		<?php trendino_page_header(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="trendino-grid trendino-post-grid" style="--trendino-columns:3;">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<div class="trendino-grid__item"><?php trendino_post_card( get_the_ID() ); ?></div>
					<?php
				endwhile;
				?>
			</div>

			<?php trendino_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
		<?php endif; ?>
	</main>

	<?php get_sidebar(); ?>
</div>
<?php
get_footer();
