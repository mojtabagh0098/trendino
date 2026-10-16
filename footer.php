<?php
/**
 * Site footer.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
	</div><!-- #content -->

	<?php
	$trendino_footer_rendered = false;

	if ( function_exists( 'elementor_theme_do_location' ) ) {
		$trendino_footer_rendered = elementor_theme_do_location( 'footer' );
	}

	if ( ! $trendino_footer_rendered ) {
		get_template_part( 'template-parts/footer/footer', 'default' );
	}
	?>
</div><!-- #page -->

<?php if ( trendino_get_option( 'footer_scroll_top', true ) ) : ?>
	<button type="button" class="trendino-scroll-top is-floating" data-trendino-scroll-top hidden aria-label="<?php esc_attr_e( 'Scroll to top', 'trendino' ); ?>">
		<?php trendino_icon( 'chevron-down', array( 'size' => 20 ) ); ?>
	</button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
