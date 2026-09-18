<?php
/**
 * Generic helper utilities shared across the theme and its Elementor widgets.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function trendino_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Whether Elementor is active.
 *
 * @return bool
 */
function trendino_is_elementor_active() {
	return did_action( 'elementor/loaded' ) > 0;
}

/**
 * Whether Elementor Pro is active.
 *
 * @return bool
 */
function trendino_is_elementor_pro_active() {
	return defined( 'ELEMENTOR_PRO_VERSION' );
}

/**
 * Whether the current request is rendered inside the Elementor editor / preview.
 *
 * @return bool
 */
function trendino_is_elementor_preview() {
	if ( ! trendino_is_elementor_active() ) {
		return false;
	}

	$plugin = \Elementor\Plugin::instance();

	return ( isset( $plugin->preview ) && $plugin->preview->is_preview_mode() )
		|| ( isset( $plugin->editor ) && $plugin->editor->is_edit_mode() );
}

/**
 * Sanitize a CSS length value coming from user input (e.g. "16px", "1.5rem", "100%").
 *
 * @param string $value Raw value.
 * @return string
 */
function trendino_sanitize_css_length( $value ) {
	$value = trim( (string) $value );

	return preg_match( '/^-?\d*\.?\d+(px|rem|em|%|vw|vh)?$/', $value ) ? $value : '';
}

/**
 * Sanitize a hex / rgba color string.
 *
 * @param string $value Raw value.
 * @return string
 */
function trendino_sanitize_color( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
		return $value;
	}

	if ( preg_match( '/^rgba?\(\s*[\d.]+\s*,\s*[\d.]+\s*,\s*[\d.]+\s*(,\s*[\d.]+\s*)?\)$/', $value ) ) {
		return $value;
	}

	return '';
}

/**
 * Convert a Persian/Arabic-Indic digit string into western digits and vice versa.
 *
 * @param string|int $number Number to convert.
 * @param bool       $to_persian Whether to output Persian digits.
 * @return string
 */
function trendino_convert_digits( $number, $to_persian = true ) {
	$western = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );

	$number = (string) $number;

	return $to_persian
		? str_replace( $western, $persian, $number )
		: str_replace( $persian, $western, $number );
}

/**
 * Localised number output, respecting the "Persian digits" theme option.
 *
 * @param string|int $number Number.
 * @return string
 */
function trendino_number( $number ) {
	if ( trendino_get_option( 'persian_digits', is_rtl() ) ) {
		return trendino_convert_digits( $number );
	}

	return (string) $number;
}

/**
 * Render an inline SVG icon from the theme icon set.
 *
 * @param string $name  Icon slug.
 * @param array  $args  Optional. size (int), class (string), aria-label (string).
 * @return string
 */
function trendino_get_icon( $name, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'  => 24,
			'class' => '',
			'label' => '',
		)
	);

	$icons = trendino_icon_library();

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$classes = trim( 'trendino-icon trendino-icon--' . $name . ' ' . $args['class'] );
	$hidden  = $args['label'] ? '' : ' aria-hidden="true" focusable="false"';
	$label   = $args['label'] ? ' role="img" aria-label="' . esc_attr( $args['label'] ) . '"' : '';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"%3$s%4$s>%5$s</svg>',
		esc_attr( $classes ),
		absint( $args['size'] ),
		$hidden,
		$label,
		$icons[ $name ]
	);
}

/**
 * Echo helper for trendino_get_icon().
 *
 * @param string $name Icon slug.
 * @param array  $args Icon args.
 * @return void
 */
function trendino_icon( $name, $args = array() ) {
	echo trendino_get_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup from a controlled library.
}

/**
 * The theme's inline SVG icon library (stroke based, 24x24 grid).
 *
 * @return array<string,string>
 */
function trendino_icon_library() {
	static $icons = null;

	if ( null !== $icons ) {
		return $icons;
	}

	$s = 'stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"';

	$icons = array(
		'search'      => '<circle cx="11" cy="11" r="7" ' . $s . '/><path d="m20 20-3.2-3.2" ' . $s . '/>',
		'user'        => '<circle cx="12" cy="8" r="3.6" ' . $s . '/><path d="M4.8 19.5c1.1-3.3 3.8-5 7.2-5s6.1 1.7 7.2 5" ' . $s . '/>',
		'cart'        => '<path d="M2.8 3.5h2.1l2.2 10.2a2 2 0 0 0 2 1.6h7.3a2 2 0 0 0 2-1.5l1.5-6H6" ' . $s . '/><circle cx="9.4" cy="19.3" r="1.5" ' . $s . '/><circle cx="17.2" cy="19.3" r="1.5" ' . $s . '/>',
		'heart'       => '<path d="M12 20s-7.5-4.4-7.5-9.4A4.1 4.1 0 0 1 12 7.8a4.1 4.1 0 0 1 7.5 2.8c0 5-7.5 9.4-7.5 9.4Z" ' . $s . '/>',
		'compare'     => '<path d="M4 7h11m0 0-3-3m3 3-3 3M20 17H9m0 0 3-3m-3 3 3 3" ' . $s . '/>',
		'eye'         => '<path d="M2.5 12S6 5.9 12 5.9 21.5 12 21.5 12 18 18.1 12 18.1 2.5 12 2.5 12Z" ' . $s . '/><circle cx="12" cy="12" r="2.8" ' . $s . '/>',
		'menu'        => '<path d="M3.5 6.5h17M3.5 12h17M3.5 17.5h17" ' . $s . '/>',
		'close'       => '<path d="m6 6 12 12M18 6 6 18" ' . $s . '/>',
		'chevron-down'=> '<path d="m6 9.5 6 5.5 6-5.5" ' . $s . '/>',
		'chevron-left'=> '<path d="M14.5 5.5 8.5 12l6 6.5" ' . $s . '/>',
		'chevron-right'=> '<path d="m9.5 5.5 6 6.5-6 6.5" ' . $s . '/>',
		'star'        => '<path d="m12 3.8 2.5 5.1 5.6.8-4 3.9 1 5.6-5.1-2.7-5 2.7 1-5.6-4.1-4 5.6-.7 2.5-5.1Z" ' . $s . '/>',
		'phone'       => '<path d="M6.3 3.8h3l1.5 3.7-1.9 1.4a11.4 11.4 0 0 0 5.2 5.2l1.4-1.9 3.7 1.5v3a1.8 1.8 0 0 1-2 1.8C10.6 18 6 13.4 4.5 5.8a1.8 1.8 0 0 1 1.8-2Z" ' . $s . '/>',
		'truck'       => '<path d="M2.8 6.5h10.4v9.8H2.8zM13.2 9.8h3.5l2.5 2.6v3.9h-6z" ' . $s . '/><circle cx="7" cy="18" r="1.7" ' . $s . '/><circle cx="16.8" cy="18" r="1.7" ' . $s . '/>',
		'shield'      => '<path d="M12 3.4 5 6v5.6c0 4 2.9 7.5 7 8.9 4.1-1.4 7-4.9 7-8.9V6l-7-2.6Z" ' . $s . '/><path d="m9.2 12 2 2 3.6-3.8" ' . $s . '/>',
		'refresh'     => '<path d="M20 11.5A8 8 0 0 0 6.2 6.6L4 8.8M4 12.5a8 8 0 0 0 13.8 4.9l2.2-2.2" ' . $s . '/><path d="M4 4.8v4h4M20 19.2v-4h-4" ' . $s . '/>',
		'headset'     => '<path d="M4.5 14v-2a7.5 7.5 0 0 1 15 0v2" ' . $s . '/><path d="M4.5 13.4h1.8a1.2 1.2 0 0 1 1.2 1.2v2.6a1.2 1.2 0 0 1-1.2 1.2H5.7A1.2 1.2 0 0 1 4.5 17.2Zm15 0h-1.8a1.2 1.2 0 0 0-1.2 1.2v2.6a1.2 1.2 0 0 0 1.2 1.2h.6a1.2 1.2 0 0 0 1.2-1.2Z" ' . $s . '/>',
		'wallet'      => '<path d="M3.6 7.6A2 2 0 0 1 5.6 5.6h11a2 2 0 0 1 2 2v1.2" ' . $s . '/><path d="M3.6 7.6v9a2 2 0 0 0 2 2h13a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-15" ' . $s . '/><circle cx="16.4" cy="13.6" r="1.1" fill="currentColor"/>',
		'location'    => '<path d="M12 21s6.5-5.6 6.5-10.3A6.5 6.5 0 0 0 5.5 10.7C5.5 15.4 12 21 12 21Z" ' . $s . '/><circle cx="12" cy="10.5" r="2.4" ' . $s . '/>',
		'mail'        => '<rect x="3" y="5.5" width="18" height="13" rx="2.2" ' . $s . '/><path d="m4 7.5 8 5.4 8-5.4" ' . $s . '/>',
		'clock'       => '<circle cx="12" cy="12" r="8.3" ' . $s . '/><path d="M12 7.4V12l3 1.8" ' . $s . '/>',
		'grid'        => '<rect x="3.6" y="3.6" width="7" height="7" rx="1.8" ' . $s . '/><rect x="13.4" y="3.6" width="7" height="7" rx="1.8" ' . $s . '/><rect x="3.6" y="13.4" width="7" height="7" rx="1.8" ' . $s . '/><rect x="13.4" y="13.4" width="7" height="7" rx="1.8" ' . $s . '/>',
		'fire'        => '<path d="M12 21c3.6 0 6-2.4 6-5.6 0-3.8-3.4-5.1-3.4-8.6-2 .8-3 2.4-3 4.2-1-.5-1.6-1.5-1.7-2.7C7.7 9.4 6 12 6 15.4 6 18.6 8.4 21 12 21Z" ' . $s . '/>',
		'tag'         => '<path d="M11 3.5H20.5V13L11.8 21.7a1.6 1.6 0 0 1-2.3 0l-7.2-7.2a1.6 1.6 0 0 1 0-2.3L11 3.5Z" ' . $s . '/><circle cx="16.4" cy="7.6" r="1.4" ' . $s . '/>',
		'gift'        => '<rect x="3.2" y="8.6" width="17.6" height="4.2" rx="1" ' . $s . '/><path d="M5 12.8v6.4a1.4 1.4 0 0 0 1.4 1.4h11.2a1.4 1.4 0 0 0 1.4-1.4v-6.4M12 8.6v12" ' . $s . '/><path d="M12 8.6S10.8 4 8.6 4a2.3 2.3 0 0 0 0 4.6Zm0 0S13.2 4 15.4 4a2.3 2.3 0 0 1 0 4.6Z" ' . $s . '/>',
		'plus'        => '<path d="M12 5.5v13M5.5 12h13" ' . $s . '/>',
		'minus'       => '<path d="M5.5 12h13" ' . $s . '/>',
		'instagram'   => '<rect x="3.6" y="3.6" width="16.8" height="16.8" rx="5" ' . $s . '/><circle cx="12" cy="12" r="3.8" ' . $s . '/><circle cx="17" cy="7" r="1.1" fill="currentColor"/>',
		'telegram'    => '<path d="m21 4.5-3 15-5.3-3.9-2.6 2.5.4-4.1L19 6.6 7.9 12.6 3 11l18-6.5Z" ' . $s . '/>',
		'whatsapp'    => '<path d="M20 12a8 8 0 0 1-11.9 7L3.8 20.3l1.3-4.2A8 8 0 1 1 20 12Z" ' . $s . '/><path d="M9.2 9c.3 2.2 2.4 4.3 4.6 4.7l1.2-1.3 1.8.8v1.6c-3 .5-6.8-2.8-7.5-6.3h1.4l.7 1.7-1 1" ' . $s . '/>',
		'twitter'     => '<path d="M21 5.9c-.7.3-1.4.5-2.2.6a3.7 3.7 0 0 0 1.7-2 7.6 7.6 0 0 1-2.4.9 3.8 3.8 0 0 0-6.5 3.5A10.8 10.8 0 0 1 3.8 4.9a3.8 3.8 0 0 0 1.2 5.1c-.6 0-1.2-.2-1.7-.5a3.8 3.8 0 0 0 3 3.8c-.5.2-1.1.2-1.7.1a3.8 3.8 0 0 0 3.6 2.6A7.7 7.7 0 0 1 3 17.6a10.8 10.8 0 0 0 5.8 1.7c7 0 10.9-5.9 10.9-11v-.5c.7-.5 1.4-1.2 1.9-2Z" ' . $s . '/>',
		'youtube'     => '<rect x="3" y="6" width="18" height="12" rx="3.4" ' . $s . '/><path d="m10.5 9.4 4.6 2.6-4.6 2.6V9.4Z" ' . $s . '/>',
		'linkedin'    => '<rect x="3.6" y="3.6" width="16.8" height="16.8" rx="3.4" ' . $s . '/><path d="M8 10.6v6M8 7.4v.1M12 16.6v-3.4a2 2 0 0 1 4 0v3.4M12 16.6v-6" ' . $s . '/>',
		'aparat'      => '<circle cx="12" cy="12" r="8.4" ' . $s . '/><circle cx="12" cy="12" r="3" ' . $s . '/><path d="M6.2 6.2 8 8m10 10-1.8-1.8M17.8 6.2 16 8M6.2 17.8 8 16" ' . $s . '/>',
		'arrow-left'  => '<path d="M19 12H5m0 0 5.5-5.5M5 12l5.5 5.5" ' . $s . '/>',
		'arrow-right' => '<path d="M5 12h14m0 0-5.5-5.5M19 12l-5.5 5.5" ' . $s . '/>',
		'check'       => '<path d="m5 12.6 4.4 4.4L19 7.4" ' . $s . '/>',
		'percent'     => '<path d="m6 18 12-12" ' . $s . '/><circle cx="7.8" cy="7.8" r="2.3" ' . $s . '/><circle cx="16.2" cy="16.2" r="2.3" ' . $s . '/>',
		'bell'        => '<path d="M18 9a6 6 0 0 0-12 0c0 5-2 6.5-2 6.5h16S18 14 18 9Z" ' . $s . '/><path d="M13.7 19a2 2 0 0 1-3.4 0" ' . $s . '/>',
		'category'    => '<path d="M3.5 6.5h17M3.5 12h17M3.5 17.5h11" ' . $s . '/>',
		'basket'      => '<path d="M3 9.5h18l-1.6 8.3a2 2 0 0 1-2 1.7H6.6a2 2 0 0 1-2-1.7L3 9.5Z" ' . $s . '/><path d="m8 9.5 2.4-5m5.6 5-2.4-5" ' . $s . '/>',
	);

	/**
	 * Filters the theme SVG icon library.
	 *
	 * @param array $icons Icon slug => inner SVG markup.
	 */
	return apply_filters( 'trendino/icons', $icons );
}

/**
 * Return a placeholder image URL used when no thumbnail exists.
 *
 * @return string
 */
function trendino_placeholder_image() {
	return TRENDINO_ASSETS . 'images/placeholder.svg';
}

/**
 * Safe wrapper around wp_kses for widget rich text.
 *
 * @param string $content Content.
 * @return string
 */
function trendino_kses( $content ) {
	return wp_kses_post( $content );
}

/**
 * Build an "attribute string" from an array.
 *
 * @param array $attributes Key => value pairs.
 * @return string
 */
function trendino_build_attributes( array $attributes ) {
	$output = array();

	foreach ( $attributes as $key => $value ) {
		if ( null === $value || false === $value || '' === $value ) {
			continue;
		}

		if ( true === $value ) {
			$output[] = esc_attr( $key );
			continue;
		}

		$output[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
	}

	return implode( ' ', $output );
}

/**
 * Get the thumbnail URL of a WooCommerce product category term.
 *
 * @param \WP_Term|int $term Term or term ID.
 * @param string       $size Image size.
 * @return string
 */
function trendino_category_thumbnail_url( $term, $size = 'trendino-story' ) {
	$term_id = is_object( $term ) ? $term->term_id : absint( $term );

	$thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );

	if ( ! $thumbnail_id ) {
		$thumbnail_id = get_term_meta( $term_id, 'category_image_id', true );
	}

	if ( $thumbnail_id ) {
		$url = wp_get_attachment_image_url( (int) $thumbnail_id, $size );

		if ( $url ) {
			return $url;
		}
	}

	return trendino_placeholder_image();
}

/**
 * Category thumbnail <img> markup.
 *
 * @param \WP_Term|int $term  Term.
 * @param string       $size  Image size.
 * @param string       $class CSS class.
 * @return string
 */
function trendino_category_thumbnail_html( $term, $size = 'trendino-story', $class = '' ) {
	$term_id      = is_object( $term ) ? $term->term_id : absint( $term );
	$name         = is_object( $term ) ? $term->name : get_term_field( 'name', $term_id );
	$thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );

	if ( $thumbnail_id ) {
		return wp_get_attachment_image(
			(int) $thumbnail_id,
			$size,
			false,
			array(
				'class'    => $class,
				'alt'      => $name,
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	}

	return sprintf(
		'<img class="%1$s is-placeholder" src="%2$s" alt="%3$s" width="200" height="200" loading="lazy" decoding="async" />',
		esc_attr( $class ),
		esc_url( trendino_placeholder_image() ),
		esc_attr( $name )
	);
}

/**
 * Format a price number using WooCommerce formatting when available.
 *
 * @param float|string $amount Amount.
 * @return string
 */
function trendino_price( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return wc_price( $amount );
	}

	return esc_html( trendino_number( number_format_i18n( (float) $amount ) ) );
}
