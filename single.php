<?php
/**
 * Single post.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

get_header();

$trendino_layout = trendino_get_option( 'blog_layout', 'sidebar-right' );
?>
<div class="trendino-container trendino-singular layout-<?php echo esc_attr( $trendino_layout ); ?>">
	<main id="primary" class="trendino-singular__main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'trendino-entry' ); ?>>
				<header class="trendino-entry__header">
					<?php trendino_breadcrumbs(); ?>
					<h1 class="trendino-entry__title"><?php the_title(); ?></h1>

					<div class="trendino-entry__meta">
						<span class="trendino-entry__meta-item">
							<?php trendino_icon( 'clock', array( 'size' => 16 ) ); ?>
							<span><?php echo esc_html( trendino_number( get_the_date() ) ); ?></span>
						</span>
						<span class="trendino-entry__meta-item">
							<?php trendino_icon( 'user', array( 'size' => 16 ) ); ?>
							<span><?php the_author(); ?></span>
						</span>
						<span class="trendino-entry__meta-item">
							<?php trendino_icon( 'eye', array( 'size' => 16 ) ); ?>
							<span><?php echo esc_html( trendino_number( get_comments_number() ) ); ?></span>
						</span>
					</div>
				</header>

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

				<?php if ( has_tag() ) : ?>
					<div class="trendino-entry__tags"><?php the_tags( '', '', '' ); ?></div>
				<?php endif; ?>
			</article>

			<?php
			the_post_navigation(
				array(
					'class'              => 'trendino-post-nav',
					'prev_text'          => '<span class="trendino-post-nav__label">' . esc_html__( 'Previous post', 'trendino' ) . '</span><span class="trendino-post-nav__title">%title</span>',
					'next_text'          => '<span class="trendino-post-nav__label">' . esc_html__( 'Next post', 'trendino' ) . '</span><span class="trendino-post-nav__title">%title</span>',
					'screen_reader_text' => esc_html__( 'Post navigation', 'trendino' ),
				)
			);

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</main>

	<?php get_sidebar(); ?>
</div>
<?php
get_footer();
