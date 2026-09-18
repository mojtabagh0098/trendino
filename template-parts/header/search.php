<?php
/**
 * Header search form (default header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

$trendino_scope = trendino_is_woocommerce_active() ? 'product' : 'all';
?>
<div class="trendino-search" data-live-search="yes" data-scope="<?php echo esc_attr( $trendino_scope ); ?>">
	<form role="search" method="get" class="trendino-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php if ( 'product' === $trendino_scope ) : ?>
			<input type="hidden" name="post_type" value="product" />
		<?php endif; ?>

		<span class="trendino-search__icon"><?php trendino_icon( 'search', array( 'size' => 20 ) ); ?></span>

		<input
			type="search"
			class="trendino-search__input"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'Search products, brands and categories…', 'trendino' ); ?>"
			autocomplete="off"
			aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>"
		/>

		<button type="submit" class="trendino-search__submit is-icon" aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>">
			<?php trendino_icon( 'search', array( 'size' => 20 ) ); ?>
		</button>
	</form>

	<div class="trendino-search__panel" hidden>
		<div class="trendino-search__results" aria-live="polite"></div>
	</div>
</div>
