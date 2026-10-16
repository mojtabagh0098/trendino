<?php
/**
 * Elementor integration bootstrap.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/*
 * The query + render helpers are plain static classes with no Elementor dependency.
 * The theme's own templates (category menu, WooCommerce loop) use them too, so they
 * are loaded unconditionally rather than on `elementor/loaded`.
 */
require_once TRENDINO_DIR . 'elementor/helpers/class-query-helper.php';
require_once TRENDINO_DIR . 'elementor/helpers/class-render-helper.php';

/**
 * Load the Elementor manager once Elementor itself is ready.
 *
 * @return void
 */
function trendino_init_elementor() {
	if ( ! trendino_is_elementor_active() ) {
		return;
	}

	require_once TRENDINO_DIR . 'elementor/managers/class-widgets-manager.php';
	require_once TRENDINO_DIR . 'elementor/managers/class-categories-manager.php';
	require_once TRENDINO_DIR . 'elementor/controls/trait-carousel-controls.php';
	require_once TRENDINO_DIR . 'elementor/controls/trait-query-controls.php';
	require_once TRENDINO_DIR . 'elementor/controls/trait-product-card-controls.php';
	require_once TRENDINO_DIR . 'elementor/controls/trait-section-header-controls.php';
	require_once TRENDINO_DIR . 'elementor/widgets/class-base-widget.php';

	Trendino\Elementor\Categories_Manager::instance();
	Trendino\Elementor\Widgets_Manager::instance();
}
add_action( 'elementor/loaded', 'trendino_init_elementor' );

/**
 * Register Elementor theme locations (Elementor Pro Theme Builder support).
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 * @return void
 */
function trendino_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'trendino_register_elementor_locations' );

/**
 * Tell Elementor the theme handles its own container width etc.
 *
 * @return void
 */
function trendino_elementor_kit_defaults() {
	if ( ! trendino_is_elementor_active() ) {
		return;
	}

	add_filter(
		'elementor/frontend/print_google_fonts',
		function ( $print ) {
			// Persian sites are better served by the self-hosted Vazirmatn family.
			return apply_filters( 'trendino/elementor/print_google_fonts', $print );
		}
	);
}
add_action( 'init', 'trendino_elementor_kit_defaults' );

/**
 * Add the theme's fonts to the Elementor font picker.
 *
 * @param array $fonts Fonts.
 * @return array
 */
function trendino_elementor_fonts( $fonts ) {
	$fonts['Vazirmatn']   = 'system';
	$fonts['Trendino UI'] = 'system';

	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'trendino_elementor_fonts' );
