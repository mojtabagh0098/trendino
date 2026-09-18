<?php
/**
 * Single page.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="trendino-container trendino-singular">
	<main id="primary" class="trendino-singular__main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'trendino-entry' ); ?>>
				<?php if ( ! is_front_page() ) : ?>
					<header class="trendino-entry__header">
						<?php trendino_breadcrumbs(); ?>
						<h1 class="trendino-entry__title"><?php the_title(); ?></h1>
					</header>
				<?php endif; ?>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="trendino-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>

				<div class="trendino-entry__content">
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="trendino-page-links">',
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</main>
</div>
<?php
get_footer();
