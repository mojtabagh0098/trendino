<?php
/**
 * "No results" state.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="trendino-empty">
	<span class="trendino-empty__icon"><?php trendino_icon( 'search', array( 'size' => 40 ) ); ?></span>
	<p class="trendino-empty__text">
		<?php
		if ( is_search() ) {
			esc_html_e( 'No results found for your search. Try different keywords.', 'trendino' );
		} else {
			esc_html_e( 'Nothing has been published here yet.', 'trendino' );
		}
		?>
	</p>

	<?php if ( is_search() ) : ?>
		<div class="trendino-empty__search"><?php get_search_form(); ?></div>
	<?php endif; ?>
</div>
