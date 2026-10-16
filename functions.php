<?php
/**
 * Trendino theme bootstrap.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

define( 'TRENDINO_VERSION', '1.0.0' );
define( 'TRENDINO_DIR', trailingslashit( get_template_directory() ) );
define( 'TRENDINO_URI', trailingslashit( get_template_directory_uri() ) );
define( 'TRENDINO_ASSETS', TRENDINO_URI . 'assets/' );

/**
 * Require a theme file relative to the template directory.
 *
 * @param string $relative_path Relative path, without the leading slash.
 * @return void
 */
function trendino_require( $relative_path ) {
	$file = TRENDINO_DIR . ltrim( $relative_path, '/' );

	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

$trendino_core_files = array(
	'inc/helpers.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/template-tags.php',
	'inc/hooks.php',
	'inc/customizer.php',
	'inc/theme-options.php',
	'inc/elementor.php',
	'inc/woocommerce.php',
	'inc/tgm-plugins.php',
);

foreach ( $trendino_core_files as $trendino_core_file ) {
	trendino_require( $trendino_core_file );
}

unset( $trendino_core_files, $trendino_core_file );
