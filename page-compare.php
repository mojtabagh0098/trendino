<?php
/**
 * Template Name: مقایسه محصولات
 */
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

echo tno_render_compare_page(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

get_footer();