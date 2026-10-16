<?php
/**
 * Customizer integration — the theme's Global Settings panel.
 *
 * All values are stored in the single `trendino_options` option array
 * (option_type = option) so they can be exported as one unit.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function trendino_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'trendino_options',
		array(
			'title'       => esc_html__( 'Trendino Theme Options', 'trendino' ),
			'description' => esc_html__( 'Global settings for the Trendino theme. Section level design is managed with Elementor.', 'trendino' ),
			'priority'    => 20,
		)
	);

	// --- General -------------------------------------------------------.
	$wp_customize->add_section(
		'trendino_general',
		array(
			'title' => esc_html__( 'General', 'trendino' ),
			'panel' => 'trendino_options',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'container_width',
		array(
			'section'     => 'trendino_general',
			'label'       => esc_html__( 'Container Width (px)', 'trendino' ),
			'type'        => 'number',
			'input_attrs' => array( 'min' => 960, 'max' => 1920, 'step' => 10 ),
			'sanitize'    => 'absint',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'base_font_size',
		array(
			'section'     => 'trendino_general',
			'label'       => esc_html__( 'Base Font Size (px)', 'trendino' ),
			'type'        => 'number',
			'input_attrs' => array( 'min' => 12, 'max' => 20, 'step' => 1 ),
			'sanitize'    => 'absint',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'base_radius',
		array(
			'section'     => 'trendino_general',
			'label'       => esc_html__( 'Base Border Radius (px)', 'trendino' ),
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 32, 'step' => 1 ),
			'sanitize'    => 'absint',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'card_radius',
		array(
			'section'     => 'trendino_general',
			'label'       => esc_html__( 'Card Border Radius (px)', 'trendino' ),
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'max' => 32, 'step' => 1 ),
			'sanitize'    => 'absint',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'persian_digits',
		array(
			'section'  => 'trendino_general',
			'label'    => esc_html__( 'Use Persian Digits', 'trendino' ),
			'type'     => 'checkbox',
			'sanitize' => 'trendino_sanitize_checkbox',
		)
	);

	// --- Colors --------------------------------------------------------.
	$wp_customize->add_section(
		'trendino_colors',
		array(
			'title' => esc_html__( 'Colors', 'trendino' ),
			'panel' => 'trendino_options',
		)
	);

	foreach ( trendino_color_tokens() as $slug => $token ) {
		$key = 'color_' . str_replace( '-', '_', $slug );

		$wp_customize->add_setting(
			'trendino_options[' . $key . ']',
			array(
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'default'           => $token['default'],
				'transport'         => 'postMessage',
				'sanitize_callback' => 'trendino_sanitize_color',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'trendino_' . $key,
				array(
					'label'    => $token['label'],
					'section'  => 'trendino_colors',
					'settings' => 'trendino_options[' . $key . ']',
				)
			)
		);
	}

	// --- Header --------------------------------------------------------.
	$wp_customize->add_section(
		'trendino_header',
		array(
			'title' => esc_html__( 'Header', 'trendino' ),
			'panel' => 'trendino_options',
		)
	);

	$header_toggles = array(
		'header_sticky'   => esc_html__( 'Sticky Header', 'trendino' ),
		'topbar_enabled'  => esc_html__( 'Show Top Bar', 'trendino' ),
		'header_search'   => esc_html__( 'Show Search', 'trendino' ),
		'header_account'  => esc_html__( 'Show Account', 'trendino' ),
		'header_cart'     => esc_html__( 'Show Cart', 'trendino' ),
		'header_wishlist' => esc_html__( 'Show Wishlist', 'trendino' ),
		'header_compare'  => esc_html__( 'Show Compare', 'trendino' ),
	);

	foreach ( $header_toggles as $key => $label ) {
		trendino_add_setting(
			$wp_customize,
			$key,
			array(
				'section'  => 'trendino_header',
				'label'    => $label,
				'type'     => 'checkbox',
				'sanitize' => 'trendino_sanitize_checkbox',
			)
		);
	}

	// --- WooCommerce ---------------------------------------------------.
	if ( trendino_is_woocommerce_active() ) {
		$wp_customize->add_section(
			'trendino_woocommerce',
			array(
				'title' => esc_html__( 'Shop', 'trendino' ),
				'panel' => 'trendino_options',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'shop_columns',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Product Columns', 'trendino' ),
				'type'     => 'select',
				'choices'  => array( 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6' ),
				'sanitize' => 'absint',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'shop_products_count',
			array(
				'section'     => 'trendino_woocommerce',
				'label'       => esc_html__( 'Products Per Page', 'trendino' ),
				'type'        => 'number',
				'input_attrs' => array( 'min' => 4, 'max' => 60, 'step' => 1 ),
				'sanitize'    => 'absint',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'shop_layout',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Shop Layout', 'trendino' ),
				'type'     => 'select',
				'choices'  => array(
					'sidebar-right' => esc_html__( 'Sidebar (start side)', 'trendino' ),
					'sidebar-left'  => esc_html__( 'Sidebar (end side)', 'trendino' ),
					'full-width'    => esc_html__( 'Full Width', 'trendino' ),
				),
				'sanitize' => 'sanitize_key',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'sale_badge',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Show Sale Badge', 'trendino' ),
				'type'     => 'checkbox',
				'sanitize' => 'trendino_sanitize_checkbox',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'sale_badge_type',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Sale Badge Type', 'trendino' ),
				'type'     => 'select',
				'choices'  => array(
					'percent' => esc_html__( 'Discount Percentage', 'trendino' ),
					'text'    => esc_html__( 'Text ("Sale")', 'trendino' ),
				),
				'sanitize' => 'sanitize_key',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'product_wishlist',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Wishlist Button On Cards', 'trendino' ),
				'type'     => 'checkbox',
				'sanitize' => 'trendino_sanitize_checkbox',
			)
		);

		trendino_add_setting(
			$wp_customize,
			'product_quick_view',
			array(
				'section'  => 'trendino_woocommerce',
				'label'    => esc_html__( 'Quick View Button On Cards', 'trendino' ),
				'type'     => 'checkbox',
				'sanitize' => 'trendino_sanitize_checkbox',
			)
		);
	}

	// --- Blog ----------------------------------------------------------.
	$wp_customize->add_section(
		'trendino_blog',
		array(
			'title' => esc_html__( 'Blog', 'trendino' ),
			'panel' => 'trendino_options',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'blog_layout',
		array(
			'section'  => 'trendino_blog',
			'label'    => esc_html__( 'Blog Layout', 'trendino' ),
			'type'     => 'select',
			'choices'  => array(
				'sidebar-right' => esc_html__( 'Sidebar (start side)', 'trendino' ),
				'sidebar-left'  => esc_html__( 'Sidebar (end side)', 'trendino' ),
				'full-width'    => esc_html__( 'Full Width', 'trendino' ),
			),
			'sanitize' => 'sanitize_key',
		)
	);

	// --- Footer --------------------------------------------------------.
	$wp_customize->add_section(
		'trendino_footer',
		array(
			'title' => esc_html__( 'Footer', 'trendino' ),
			'panel' => 'trendino_options',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'footer_copyright',
		array(
			'section'  => 'trendino_footer',
			'label'    => esc_html__( 'Copyright Text', 'trendino' ),
			'type'     => 'textarea',
			'sanitize' => 'wp_kses_post',
		)
	);

	trendino_add_setting(
		$wp_customize,
		'footer_scroll_top',
		array(
			'section'  => 'trendino_footer',
			'label'    => esc_html__( 'Scroll To Top Button', 'trendino' ),
			'type'     => 'checkbox',
			'sanitize' => 'trendino_sanitize_checkbox',
		)
	);
}
add_action( 'customize_register', 'trendino_customize_register' );

/**
 * Helper that registers a setting + control against the trendino_options array.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $key          Option key.
 * @param array                $args         section, label, type, choices, input_attrs, sanitize.
 * @return void
 */
function trendino_add_setting( $wp_customize, $key, array $args ) {
	$defaults = trendino_default_options();

	$wp_customize->add_setting(
		'trendino_options[' . $key . ']',
		array(
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'default'           => $defaults[ $key ] ?? '',
			'transport'         => 'refresh',
			'sanitize_callback' => $args['sanitize'] ?? 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'trendino_' . $key,
		array(
			'label'       => $args['label'],
			'section'     => $args['section'],
			'settings'    => 'trendino_options[' . $key . ']',
			'type'        => $args['type'] ?? 'text',
			'choices'     => $args['choices'] ?? array(),
			'input_attrs' => $args['input_attrs'] ?? array(),
		)
	);
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Value.
 * @return bool
 */
function trendino_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Live preview script for color settings.
 *
 * @return void
 */
function trendino_customize_preview_js() {
	wp_enqueue_script(
		'trendino-customizer-preview',
		TRENDINO_ASSETS . 'js/customizer-preview.js',
		array( 'customize-preview' ),
		TRENDINO_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'trendino_customize_preview_js' );
