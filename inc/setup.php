<?php
/**
 * Theme setup: supports, menus, image sizes, sidebars.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 *
 * @return void
 */
function trendino_setup() {
	load_theme_textdomain( 'trendino', TRENDINO_DIR . 'languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 400,
			'single_image_width'    => 800,
			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 1,
				'default_columns' => 5,
				'min_columns'     => 2,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'      => esc_html__( 'Primary Menu', 'trendino' ),
			'categories'   => esc_html__( 'Category Mega Menu', 'trendino' ),
			'topbar'       => esc_html__( 'Top Bar Menu', 'trendino' ),
			'mobile'       => esc_html__( 'Mobile Menu', 'trendino' ),
			'footer-one'   => esc_html__( 'Footer Column 1', 'trendino' ),
			'footer-two'   => esc_html__( 'Footer Column 2', 'trendino' ),
			'footer-three' => esc_html__( 'Footer Column 3', 'trendino' ),
			'footer-four'  => esc_html__( 'Footer Column 4', 'trendino' ),
		)
	);

	add_image_size( 'trendino-product-card', 400, 400, true );
	add_image_size( 'trendino-story', 200, 200, true );
	add_image_size( 'trendino-banner-lg', 1200, 420, true );
	add_image_size( 'trendino-banner-md', 640, 360, true );
	add_image_size( 'trendino-post-card', 600, 360, true );
	add_image_size( 'trendino-brand', 220, 120, false );

	// Let WooCommerce/Elementor handle the content width but expose a sane default.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1240; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
}
add_action( 'after_setup_theme', 'trendino_setup' );

/**
 * Register widget areas.
 *
 * @return void
 */
function trendino_widgets_init() {
	$defaults = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => esc_html__( 'Blog Sidebar', 'trendino' ),
				'id'          => 'sidebar-blog',
				'description' => esc_html__( 'Sidebar shown on the blog archive and single posts.', 'trendino' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$defaults,
			array(
				'name'        => esc_html__( 'Shop Sidebar', 'trendino' ),
				'id'          => 'sidebar-shop',
				'description' => esc_html__( 'Sidebar shown on WooCommerce archives.', 'trendino' ),
			)
		)
	);
}
add_action( 'widgets_init', 'trendino_widgets_init' );

/**
 * Register the theme's editor color palette using the same design tokens as the CSS.
 *
 * @return void
 */
function trendino_editor_palette() {
	$palette = array();

	foreach ( trendino_color_tokens() as $slug => $token ) {
		$palette[] = array(
			'name'  => $token['label'],
			'slug'  => 'trendino-' . $slug,
			'color' => $token['default'],
		);
	}

	add_theme_support( 'editor-color-palette', $palette );
}
add_action( 'after_setup_theme', 'trendino_editor_palette', 20 );
