<?php
/**
 * Recommended plugins notice (no bundled installer — keeps the theme marketplace safe).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Show an admin notice listing the plugins the theme is built for.
 *
 * @return void
 */
function trendino_recommended_plugins_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'trendino_dismiss_plugins_notice', true ) ) {
		return;
	}

	$missing = array();

	if ( ! trendino_is_woocommerce_active() ) {
		$missing[] = 'WooCommerce';
	}

	if ( ! trendino_is_elementor_active() ) {
		$missing[] = 'Elementor';
	}

	if ( empty( $missing ) ) {
		return;
	}
	?>
	<div class="notice notice-warning is-dismissible trendino-plugins-notice">
		<p>
			<strong><?php esc_html_e( 'Trendino', 'trendino' ); ?>:</strong>
			<?php
			printf(
				/* translators: %s: plugin names */
				esc_html__( 'This theme needs %s to unlock all of its features.', 'trendino' ),
				esc_html( implode( '، ', $missing ) )
			);
			?>
			<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=' . rawurlencode( implode( ' ', $missing ) ) . '&tab=search&type=term' ) ); ?>">
				<?php esc_html_e( 'Install now', 'trendino' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'trendino_recommended_plugins_notice' );

/**
 * Persist the notice dismissal per user.
 *
 * @return void
 */
function trendino_dismiss_plugins_notice() {
	check_ajax_referer( 'trendino_dismiss_notice', 'nonce' );

	update_user_meta( get_current_user_id(), 'trendino_dismiss_plugins_notice', 1 );

	wp_send_json_success();
}
add_action( 'wp_ajax_trendino_dismiss_plugins_notice', 'trendino_dismiss_plugins_notice' );

/**
 * Inline script that hooks the notice dismiss button to the AJAX handler.
 *
 * @return void
 */
function trendino_dismiss_notice_script() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	$nonce = wp_create_nonce( 'trendino_dismiss_notice' );
	?>
	<script>
	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.trendino-plugins-notice .notice-dismiss' );

		if ( ! button ) {
			return;
		}

		var data = new FormData();
		data.append( 'action', 'trendino_dismiss_plugins_notice' );
		data.append( 'nonce', '<?php echo esc_js( $nonce ); ?>' );

		fetch( ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' } );
	} );
	</script>
	<?php
}
add_action( 'admin_print_footer_scripts', 'trendino_dismiss_notice_script' );
