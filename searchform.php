<?php
/**
 * Search form.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

$trendino_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="trendino-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $trendino_id ); ?>"><?php esc_html_e( 'Search for:', 'trendino' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $trendino_id ); ?>" class="trendino-searchform__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'trendino' ); ?>" />
	<button type="submit" class="trendino-searchform__submit" aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>">
		<?php trendino_icon( 'search', array( 'size' => 18 ) ); ?>
	</button>
</form>
