<?php
/**
 * Loads and registers every Trendino Elementor widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets_Manager
 */
class Widgets_Manager {

	/**
	 * Singleton instance.
	 *
	 * @var Widgets_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton.
	 *
	 * @return Widgets_Manager
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
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_styles' ) );
	}

	/**
	 * Map of widget file slug => class name (relative to Trendino\Elementor\Widgets).
	 *
	 * @return array<string,string>
	 */
	public function get_widget_map() {
		$widgets = array(
			// Header.
			'announcement-bar'     => 'Announcement_Bar',
			'header-logo'          => 'Header_Logo',
			'header-search'        => 'Header_Search',
			'header-actions'       => 'Header_Actions',
			'header-menu'          => 'Header_Menu',
			'header-category-menu' => 'Header_Category_Menu',
			'header-topbar'        => 'Header_Topbar',
			'header-mobile'        => 'Header_Mobile',

			// Homepage / content.
			'stories'              => 'Stories',
			'hero-slider'          => 'Hero_Slider',
			'banner'               => 'Banner',
			'banner-grid'          => 'Banner_Grid',
			'promo-boxes'          => 'Promo_Boxes',
			'category-cards'       => 'Category_Cards',
			'product-carousel'     => 'Product_Carousel',
			'product-grid'         => 'Product_Grid',
			'product-tabs'         => 'Product_Tabs',
			'category-showcase'    => 'Category_Showcase',
			'quad-boxes'           => 'Quad_Boxes',
			'deal-countdown'       => 'Deal_Countdown',
			'brands'               => 'Brands',
			'service-features'     => 'Service_Features',
			'blog-posts'           => 'Blog_Posts',
			'newsletter'           => 'Newsletter',
			'section-heading'      => 'Section_Heading',
			'seo-text'             => 'Seo_Text',

			// Footer.
			'footer-about'         => 'Footer_About',
			'footer-menu'          => 'Footer_Menu',
			'footer-badges'        => 'Footer_Badges',
			'footer-social'        => 'Footer_Social',
			'footer-bottom'        => 'Footer_Bottom',
		);

		/**
		 * Filters the registered widget map.
		 *
		 * @param array $widgets slug => class.
		 */
		return apply_filters( 'trendino/elementor/widgets', $widgets );
	}

	/**
	 * Register all widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widget manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		foreach ( $this->get_widget_map() as $slug => $class_name ) {
			$file = TRENDINO_DIR . 'elementor/widgets/class-' . $slug . '-widget.php';

			if ( ! is_readable( $file ) ) {
				continue;
			}

			require_once $file;

			$class = __NAMESPACE__ . '\\Widgets\\' . $class_name;

			if ( ! class_exists( $class ) ) {
				continue;
			}

			$widgets_manager->register( new $class() );
		}
	}

	/**
	 * Small editor tweaks so the custom category icons look right.
	 *
	 * @return void
	 */
	public function editor_styles() {
		wp_enqueue_style( 'trendino-elementor-editor', TRENDINO_ASSETS . 'css/elementor-editor.css', array(), TRENDINO_VERSION );
	}
}
