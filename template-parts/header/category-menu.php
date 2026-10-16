<?php
/**
 * Category mega menu trigger (default header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

if ( ! trendino_is_woocommerce_active() ) {
	return;
}

$trendino_parents = \Trendino\Elementor\Query_Helper::product_categories(
	array(
		'parent' => 0,
		'number' => 12,
	)
);

if ( empty( $trendino_parents ) ) {
	return;
}
?>
<div class="trendino-catmenu" data-open-on="hover">
	<button type="button" class="trendino-catmenu__trigger" aria-expanded="false" aria-haspopup="true">
		<?php trendino_icon( 'category', array( 'size' => 20 ) ); ?>
		<span><?php esc_html_e( 'All categories', 'trendino' ); ?></span>
		<?php trendino_icon( 'chevron-down', array( 'size' => 14, 'class' => 'trendino-catmenu__caret' ) ); ?>
	</button>

	<div class="trendino-catmenu__panel">
		<ul class="trendino-catmenu__list">
			<?php foreach ( $trendino_parents as $trendino_parent ) : ?>
				<?php
				$trendino_children = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $trendino_parent->term_id,
						'hide_empty' => false,
						'number'     => 8,
					)
				);
				$trendino_children = is_wp_error( $trendino_children ) ? array() : $trendino_children;
				?>
				<li class="trendino-catmenu__item<?php echo $trendino_children ? ' has-children' : ''; ?>">
					<a class="trendino-catmenu__link" href="<?php echo esc_url( get_term_link( $trendino_parent ) ); ?>">
						<?php echo trendino_category_thumbnail_html( $trendino_parent, 'trendino-story', 'trendino-catmenu__thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( $trendino_parent->name ); ?></span>
						<?php if ( $trendino_children ) : ?>
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14, 'class' => 'trendino-catmenu__arrow' ) ); ?>
						<?php endif; ?>
					</a>

					<?php if ( $trendino_children ) : ?>
						<div class="trendino-catmenu__flyout">
							<ul class="trendino-catmenu__children">
								<?php foreach ( $trendino_children as $trendino_child ) : ?>
									<li class="trendino-catmenu__child">
										<a class="trendino-catmenu__child-link" href="<?php echo esc_url( get_term_link( $trendino_child ) ); ?>"><?php echo esc_html( $trendino_child->name ); ?></a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
