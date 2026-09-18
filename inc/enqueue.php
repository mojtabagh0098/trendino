<?php
/**
 * Conditional asset loading.
 *
 * Every widget registers the handles it needs through trendino_enqueue_component(),
 * so a page only downloads the CSS/JS it actually uses.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Component asset map: handle => [css, js, deps].
 *
 * @return array<string,array{css?:string,js?:string,deps?:array}>
 */
function trendino_component_assets() {
	return apply_filters(
		'trendino/component_assets',
		array(
			'carousel'      => array(
				'css' => 'components/carousel.css',
				'js'  => 'components/carousel.js',
			),
			'product-card'  => array( 'css' => 'components/product-card.css' ),
			'header'        => array(
				'css' => 'components/header.css',
				'js'  => 'components/header.js',
			),
			'footer'        => array( 'css' => 'components/footer.css' ),
			'stories'       => array( 'css' => 'components/stories.css' ),
			'hero'          => array( 'css' => 'components/hero.css' ),
			'banner'        => array( 'css' => 'components/banner.css' ),
			'categories'    => array( 'css' => 'components/categories.css' ),
			'tabs'          => array(
				'css' => 'components/tabs.css',
				'js'  => 'components/tabs.js',
			),
			'brands'        => array( 'css' => 'components/brands.css' ),
			'blog'          => array( 'css' => 'components/blog.css' ),
			'newsletter'    => array(
				'css' => 'components/newsletter.css',
				'js'  => 'components/newsletter.js',
			),
			'countdown'     => array(
				'css' => 'components/countdown.css',
				'js'  => 'components/countdown.js',
			),
			'services'      => array( 'css' => 'components/services.css' ),
			'section-head'  => array( 'css' => 'components/section-head.css' ),
		)
	);
}

/**
 * Enqueue a registered component's assets on demand.
 *
 * @param string|array $components Component handle(s).
 * @return void
 */
function trendino_enqueue_component( $components ) {
	$map = trendino_component_assets();

	foreach ( (array) $components as $component ) {
		if ( ! isset( $map[ $component ] ) ) {
			continue;
		}

		$config = $map[ $component ];
		$handle = 'trendino-' . $component;

		if ( ! empty( $config['css'] ) && ! wp_style_is( $handle, 'enqueued' ) ) {
			wp_enqueue_style(
				$handle,
				TRENDINO_ASSETS . 'css/' . $config['css'],
				array( 'trendino-base' ),
				TRENDINO_VERSION
			);
		}

		if ( ! empty( $config['js'] ) && ! wp_script_is( $handle, 'enqueued' ) ) {
			wp_enqueue_script(
				$handle,
				TRENDINO_ASSETS . 'js/' . $config['js'],
				isset( $config['deps'] ) ? $config['deps'] : array( 'trendino-app' ),
				TRENDINO_VERSION,
				true
			);
		}
	}
}

/**
 * Register + enqueue global front-end assets.
 *
 * @return void
 */
function trendino_enqueue_assets() {
	// Fonts (self hosted, variable weight).
	wp_enqueue_style( 'trendino-fonts', TRENDINO_ASSETS . 'fonts/fonts.css', array(), TRENDINO_VERSION );

	/*
	 * Every stylesheet is written with CSS logical properties (inline-start/end,
	 * margin-inline…), so a single file serves both LTR and RTL. That is why no
	 * -rtl.css counterparts are registered here.
	 */
	wp_enqueue_style( 'trendino-base', TRENDINO_ASSETS . 'css/base.css', array( 'trendino-fonts' ), TRENDINO_VERSION );

	wp_add_inline_style( 'trendino-base', trendino_root_css_variables() );

	wp_enqueue_style( 'trendino-layout', TRENDINO_ASSETS . 'css/layout.css', array( 'trendino-base' ), TRENDINO_VERSION );

	wp_enqueue_script( 'trendino-app', TRENDINO_ASSETS . 'js/app.js', array(), TRENDINO_VERSION, true );

	wp_localize_script(
		'trendino-app',
		'trendinoConfig',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'restUrl'  => esc_url_raw( rest_url( 'trendino/v1/' ) ),
			'nonce'    => wp_create_nonce( 'trendino_nonce' ),
			'isRtl'    => is_rtl(),
			'hasWoo'   => trendino_is_woocommerce_active(),
			'i18n'     => array(
				'added'    => esc_html__( 'Added to cart', 'trendino' ),
				'error'    => esc_html__( 'Something went wrong. Please try again.', 'trendino' ),
				'loading'  => esc_html__( 'Loading…', 'trendino' ),
				'days'     => esc_html__( 'days', 'trendino' ),
				'hours'    => esc_html__( 'hours', 'trendino' ),
				'minutes'  => esc_html__( 'minutes', 'trendino' ),
				'seconds'  => esc_html__( 'seconds', 'trendino' ),
			),
		)
	);

	if ( trendino_is_woocommerce_active() ) {
		wp_enqueue_style( 'trendino-woocommerce', TRENDINO_ASSETS . 'css/woocommerce.css', array( 'trendino-base' ), TRENDINO_VERSION );
		trendino_enqueue_component( 'product-card' );
	}

	// Header / footer are always present.
	trendino_enqueue_component( array( 'header', 'footer' ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// In the Elementor editor we cannot know which widgets will be dropped in, so load everything.
	if ( trendino_is_elementor_preview() ) {
		trendino_enqueue_component( array_keys( trendino_component_assets() ) );
	}
}
add_action( 'wp_enqueue_scripts', 'trendino_enqueue_assets', 20 );

/**
 * Admin/editor styles.
 *
 * @return void
 */
function trendino_admin_assets() {
	wp_enqueue_style( 'trendino-admin', TRENDINO_ASSETS . 'css/admin.css', array(), TRENDINO_VERSION );
}
add_action( 'admin_enqueue_scripts', 'trendino_admin_assets' );

/**
 * Preload the primary font file for faster first paint.
 *
 * @return void
 */
function trendino_resource_hints() {
	if ( ! file_exists( TRENDINO_DIR . 'assets/fonts/Vazirmatn-Regular.woff2' ) ) {
		return;
	}

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( TRENDINO_ASSETS . 'fonts/Vazirmatn-Regular.woff2' )
	);
}
add_action( 'wp_head', 'trendino_resource_hints', 1 );
