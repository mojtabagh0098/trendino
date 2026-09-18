<?php
/**
 * Registers the theme's Elementor widget panel categories.
 *
 * @package Trendino
 */

namespace Trendino\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Categories_Manager
 */
class Categories_Manager {

	/**
	 * Singleton instance.
	 *
	 * @var Categories_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton.
	 *
	 * @return Categories_Manager
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_categories' ) );
	}

	/**
	 * Category definitions.
	 *
	 * @return array<string,array{title:string,icon:string}>
	 */
	public static function get_categories() {
		return array(
			'trendino-header'   => array(
				'title' => esc_html__( 'Trendino Header', 'trendino' ),
				'icon'  => 'eicon-header',
			),
			'trendino-shop'     => array(
				'title' => esc_html__( 'Trendino Shop', 'trendino' ),
				'icon'  => 'eicon-woocommerce',
			),
			'trendino-content'  => array(
				'title' => esc_html__( 'Trendino Content', 'trendino' ),
				'icon'  => 'eicon-favorite',
			),
			'trendino-footer'   => array(
				'title' => esc_html__( 'Trendino Footer', 'trendino' ),
				'icon'  => 'eicon-footer',
			),
		);
	}

	/**
	 * Register the categories with Elementor.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_categories( $elements_manager ) {
		foreach ( self::get_categories() as $slug => $args ) {
			$elements_manager->add_category(
				$slug,
				array(
					'title' => $args['title'],
					'icon'  => $args['icon'],
				)
			);
		}
	}
}
