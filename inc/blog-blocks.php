<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register "ترندینو" Gutenberg block category.
 */
add_filter( 'block_categories_all', function ( array $categories ): array {
	array_unshift( $categories, array(
		'slug'  => 'trendino',
		'title' => 'ترندینو (Trendino)',
		'icon'  => null,
	) );
	return $categories;
} );

/**
 * Register custom Gutenberg blocks for Single Blog Posts:
 * 1. trendino/highlight-box (باکس نکته / جهش‌های هیجانی)
 * 2. trendino/ad-banner     (باکس محل تبلیغات)
 */
add_action( 'init', function (): void {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	$js_path  = $theme_path . '/assets/js/tno-gutenberg-blocks.js';
	$css_path = $theme_path . '/assets/css/tno-gutenberg-blocks.css';

	wp_register_script(
		'tno-gutenberg-blocks',
		$theme_uri . '/assets/js/tno-gutenberg-blocks.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0',
		true
	);

	wp_register_style(
		'tno-gutenberg-blocks',
		$theme_uri . '/assets/css/tno-gutenberg-blocks.css',
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : '1.0.0'
	);

	register_block_type( 'trendino/highlight-box', array(
		'editor_script' => 'tno-gutenberg-blocks',
		'editor_style'  => 'tno-gutenberg-blocks',
		'style'         => 'tno-gutenberg-blocks',
	) );

	register_block_type( 'trendino/ad-banner', array(
		'editor_script' => 'tno-gutenberg-blocks',
		'editor_style'  => 'tno-gutenberg-blocks',
		'style'         => 'tno-gutenberg-blocks',
	) );
} );
