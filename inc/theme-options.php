<?php
/**
 * Trendino global settings: design tokens + option accessors.
 *
 * Options are stored in a single `trendino_options` array so the theme keeps a
 * small database footprint and can be exported/imported as a unit.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Design color tokens. These become CSS custom properties and Customizer controls.
 *
 * @return array<string,array{label:string,default:string,description?:string}>
 */
function trendino_color_tokens() {
	$tokens = array(
		'primary'        => array(
			'label'   => esc_html__( 'Primary', 'trendino' ),
			'default' => '#ef4056',
		),
		'primary-dark'   => array(
			'label'   => esc_html__( 'Primary Dark', 'trendino' ),
			'default' => '#d32f48',
		),
		'primary-soft'   => array(
			'label'   => esc_html__( 'Primary Soft', 'trendino' ),
			'default' => '#fdecef',
		),
		'secondary'      => array(
			'label'   => esc_html__( 'Secondary', 'trendino' ),
			'default' => '#0f1b33',
		),
		'accent'         => array(
			'label'   => esc_html__( 'Accent', 'trendino' ),
			'default' => '#19bfd3',
		),
		'success'        => array(
			'label'   => esc_html__( 'Success', 'trendino' ),
			'default' => '#00a049',
		),
		'warning'        => array(
			'label'   => esc_html__( 'Warning', 'trendino' ),
			'default' => '#f5a623',
		),
		'text'           => array(
			'label'   => esc_html__( 'Text', 'trendino' ),
			'default' => '#1a1a1a',
		),
		'muted'          => array(
			'label'   => esc_html__( 'Muted Text', 'trendino' ),
			'default' => '#7f7f7f',
		),
		'border'         => array(
			'label'   => esc_html__( 'Border', 'trendino' ),
			'default' => '#e8e8ee',
		),
		'background'     => array(
			'label'   => esc_html__( 'Page Background', 'trendino' ),
			'default' => '#f5f6fa',
		),
		'surface'        => array(
			'label'   => esc_html__( 'Surface / Card', 'trendino' ),
			'default' => '#ffffff',
		),
		'footer-bg'      => array(
			'label'   => esc_html__( 'Footer Background', 'trendino' ),
			'default' => '#ffffff',
		),
		'footer-text'    => array(
			'label'   => esc_html__( 'Footer Text', 'trendino' ),
			'default' => '#4d4d4d',
		),
	);

	/**
	 * Filters the theme color tokens.
	 *
	 * @param array $tokens Tokens.
	 */
	return apply_filters( 'trendino/color_tokens', $tokens );
}

/**
 * Default values for every theme option.
 *
 * @return array<string,mixed>
 */
function trendino_default_options() {
	$defaults = array(
		// General.
		'container_width'      => 1240,
		'base_font_size'       => 15,
		'base_radius'          => 12,
		'card_radius'          => 12,
		'persian_digits'       => true,
		'body_font'            => 'vazirmatn',

		// Header.
		'header_sticky'        => true,
		'header_search'        => true,
		'header_account'       => true,
		'header_cart'          => true,
		'header_wishlist'      => true,
		'header_compare'       => true,
		'topbar_enabled'       => true,
		'topbar_text'          => '',

		// WooCommerce.
		'shop_columns'         => 4,
		'shop_products_count'  => 20,
		'shop_layout'          => 'sidebar-right',
		'product_card_style'   => 'default',
		'sale_badge'           => true,
		'sale_badge_type'      => 'percent',
		'product_quick_view'   => true,
		'product_wishlist'     => true,
		'product_compare'      => true,

		// Blog.
		'blog_layout'          => 'sidebar-right',

		// Footer.
		'footer_copyright'     => '',
		'footer_scroll_top'    => true,
	);

	foreach ( trendino_color_tokens() as $slug => $token ) {
		$defaults[ 'color_' . str_replace( '-', '_', $slug ) ] = $token['default'];
	}

	/**
	 * Filters the default theme options.
	 *
	 * @param array $defaults Defaults.
	 */
	return apply_filters( 'trendino/default_options', $defaults );
}

/**
 * Get all stored theme options merged with defaults.
 *
 * @return array<string,mixed>
 */
function trendino_get_options() {
	static $options = null;

	if ( null !== $options ) {
		return $options;
	}

	$stored  = get_option( 'trendino_options', array() );
	$stored  = is_array( $stored ) ? $stored : array();
	$options = wp_parse_args( $stored, trendino_default_options() );

	return $options;
}

/**
 * Get a single theme option.
 *
 * @param string $key     Option key.
 * @param mixed  $default Fallback when the key is unknown.
 * @return mixed
 */
function trendino_get_option( $key, $default = null ) {
	$options = trendino_get_options();

	if ( array_key_exists( $key, $options ) ) {
		/**
		 * Filters a single theme option value.
		 *
		 * @param mixed  $value Value.
		 * @param string $key   Key.
		 */
		return apply_filters( 'trendino/option', $options[ $key ], $key );
	}

	return $default;
}

/**
 * Get a color token value (option override or design default).
 *
 * @param string $slug Token slug, e.g. "primary".
 * @return string
 */
function trendino_color( $slug ) {
	$value = trendino_get_option( 'color_' . str_replace( '-', '_', $slug ) );

	if ( $value ) {
		return $value;
	}

	$tokens = trendino_color_tokens();

	return isset( $tokens[ $slug ] ) ? $tokens[ $slug ]['default'] : '';
}

/**
 * Build the `:root` CSS custom property block from the current options.
 *
 * @return string
 */
function trendino_root_css_variables() {
	$lines = array();

	foreach ( trendino_color_tokens() as $slug => $token ) {
		$value = trendino_sanitize_color( trendino_color( $slug ) );

		if ( $value ) {
			$lines[] = sprintf( '--trendino-%s:%s;', $slug, $value );
		}
	}

	$lines[] = sprintf( '--trendino-container:%dpx;', absint( trendino_get_option( 'container_width', 1240 ) ) );
	$lines[] = sprintf( '--trendino-font-size:%dpx;', absint( trendino_get_option( 'base_font_size', 15 ) ) );
	$lines[] = sprintf( '--trendino-radius:%dpx;', absint( trendino_get_option( 'base_radius', 12 ) ) );
	$lines[] = sprintf( '--trendino-card-radius:%dpx;', absint( trendino_get_option( 'card_radius', 12 ) ) );

	$shop_columns = max( 2, absint( trendino_get_option( 'shop_columns', 4 ) ) );

	$lines[] = sprintf( '--trendino-shop-columns:%d;', $shop_columns );
	$lines[] = sprintf( '--trendino-shop-columns-tablet:%d;', min( 3, $shop_columns ) );
	$lines[] = sprintf( '--trendino-shop-columns-mobile:%d;', min( 2, $shop_columns ) );

	/**
	 * Filters the generated root CSS variable declarations.
	 *
	 * @param array $lines Declarations.
	 */
	$lines = apply_filters( 'trendino/root_css_variables', $lines );

	return ':root{' . implode( '', $lines ) . '}';
}
