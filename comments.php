<?php
/**
 * Comments template.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="trendino-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="trendino-comments__title">
			<?php
			$trendino_count = get_comments_number();

			printf(
				/* translators: %s: comment count */
				esc_html( _n( '%s comment', '%s comments', $trendino_count, 'trendino' ) ),
				esc_html( trendino_number( number_format_i18n( $trendino_count ) ) )
			);
			?>
		</h2>

		<ol class="trendino-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => esc_html__( 'Older comments', 'trendino' ),
				'next_text' => esc_html__( 'Newer comments', 'trendino' ),
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="trendino-comments__closed"><?php esc_html_e( 'Comments are closed.', 'trendino' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'         => 'trendino-comment-form',
			'class_submit'       => 'trendino-button trendino-button--primary',
			'title_reply'        => esc_html__( 'Leave a comment', 'trendino' ),
			'title_reply_before' => '<h3 class="trendino-comments__form-title">',
			'title_reply_after'  => '</h3>',
		)
	);
	?>
</section>
