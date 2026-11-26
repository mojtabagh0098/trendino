<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Blog styles exclusively on Blog / Archive / Category / Blog Page Template.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	$is_blog_view = is_home()
		|| is_category()
		|| is_tag()
		|| is_page_template( 'page-blog.php' )
		|| ( is_search() && get_query_var( 'post_type' ) === 'post' );

	if ( ! $is_blog_view ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/css/blog.css';
	if ( file_exists( $css_path ) ) {
		wp_enqueue_style(
			'tno-blog-style',
			get_template_directory_uri() . '/assets/css/blog.css',
			array( 'style' ),
			(string) filemtime( $css_path )
		);
	}
}, 30 );

/**
 * Format post publish date in Jalali with Persian digits.
 */
function tno_blog_jalali_date( int $post_id ): string {
	$ts = (int) get_post_time( 'U', false, $post_id );
	if ( $ts <= 0 ) {
		$ts = current_time( 'timestamp' );
	}

	if ( function_exists( 'tno_gregorian_to_jalali' ) && function_exists( 'tno_fa_digits' ) ) {
		$j = tno_gregorian_to_jalali( $ts );
		return sprintf(
			'%s %s %s',
			tno_fa_digits( (string) $j['day'] ),
			$j['month_name'],
			tno_fa_digits( (string) $j['year'] )
		);
	}

	return get_the_date( '', $post_id );
}

/**
 * Get primary category name & URL for a post.
 *
 * @return array{name: string, url: string}
 */
function tno_blog_primary_category( int $post_id ): array {
	$cats = get_the_category( $post_id );
	if ( ! empty( $cats ) && $cats[0] instanceof WP_Term ) {
		return array(
			'name' => $cats[0]->name,
			'url'  => (string) get_category_link( $cats[0]->term_id ),
		);
	}

	return array(
		'name' => 'مقالات',
		'url'  => get_post_type_archive_link( 'post' ) ?: home_url( '/' ),
	);
}

/**
 * Get post thumbnail URL with clean fallback placeholder.
 */
function tno_blog_thumb_url( int $post_id, string $size = 'medium_large' ): string {
	$url = get_the_post_thumbnail_url( $post_id, $size );
	if ( $url ) {
		return (string) $url;
	}

	return get_template_directory_uri() . '/assets/img/placeholder.png';
}

/**
 * Route blog post searches (?s=...&post_type=post) to home.php instead of storefront index.php.
 */
add_filter( 'template_include', function ( string $template ): string {
	if ( is_search() && get_query_var( 'post_type' ) === 'post' ) {
		$blog_tpl = locate_template( 'home.php' );
		if ( $blog_tpl !== '' ) {
			return $blog_tpl;
		}
	}
	return $template;
}, 99 );
