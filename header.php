<?php
/**
 * Site header.
 *
 * When Elementor Pro's Theme Builder provides a header template it takes over;
 * otherwise the theme's own header template parts are rendered (which are themselves
 * built from the Trendino header widgets when the user saves an Elementor header).
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'trendino' ); ?></a>

<div id="page" class="trendino-site">
	<?php
	$trendino_header_rendered = false;

	if ( function_exists( 'elementor_theme_do_location' ) ) {
		$trendino_header_rendered = elementor_theme_do_location( 'header' );
	}

	if ( ! $trendino_header_rendered ) {
		get_template_part( 'template-parts/header/header', 'default' );
	}
	?>

	<div id="content" class="trendino-content">
