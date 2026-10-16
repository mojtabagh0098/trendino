<?php
/**
 * Reusable WooCommerce / post query controls.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Controls;

use Elementor\Controls_Manager;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Query_Controls
 */
trait Query_Controls {

	/**
	 * Register the product query controls inside the currently open section.
	 *
	 * @param array $args default_limit (int), show_offset (bool).
	 * @return void
	 */
	protected function register_product_query_controls( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'default_limit' => 10,
				'show_offset'   => true,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Product Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => array(
					'recent'       => esc_html__( 'Recent Products', 'trendino' ),
					'featured'     => esc_html__( 'Featured Products', 'trendino' ),
					'sale'         => esc_html__( 'On Sale Products', 'trendino' ),
					'best_selling' => esc_html__( 'Best Selling Products', 'trendino' ),
					'top_rated'    => esc_html__( 'Top Rated Products', 'trendino' ),
					'related'      => esc_html__( 'Related Products', 'trendino' ),
					'manual'       => esc_html__( 'Specific Products', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'products',
			array(
				'label'       => esc_html__( 'Select Products', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::post_options( 'product', 150 ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'       => esc_html__( 'Product Categories', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'product_cat' ),
				'condition'   => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'       => esc_html__( 'Product Tags', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'product_tag' ),
				'condition'   => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		$brand_taxonomy = Query_Helper::brand_taxonomy();

		if ( $brand_taxonomy ) {
			$this->add_control(
				'brands',
				array(
					'label'       => esc_html__( 'Brands', 'trendino' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => Query_Helper::term_options( $brand_taxonomy ),
					'condition'   => array( 'source!' => array( 'manual', 'related' ) ),
				)
			);
		}

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Products Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 48,
				'step'      => 1,
				'default'   => (int) $args['default_limit'],
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'Order By', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'Date', 'trendino' ),
					'title'      => esc_html__( 'Title', 'trendino' ),
					'price'      => esc_html__( 'Price', 'trendino' ),
					'popularity' => esc_html__( 'Popularity (sales)', 'trendino' ),
					'rating'     => esc_html__( 'Rating', 'trendino' ),
					'menu_order' => esc_html__( 'Menu Order', 'trendino' ),
					'rand'       => esc_html__( 'Random', 'trendino' ),
				),
				'condition' => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => esc_html__( 'Order', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => esc_html__( 'Descending', 'trendino' ),
					'ASC'  => esc_html__( 'Ascending', 'trendino' ),
				),
				'condition' => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		if ( $args['show_offset'] ) {
			$this->add_control(
				'offset',
				array(
					'label'     => esc_html__( 'Offset', 'trendino' ),
					'type'      => Controls_Manager::NUMBER,
					'min'       => 0,
					'default'   => 0,
					'condition' => array( 'source!' => 'manual' ),
				)
			);
		}

		$this->add_control(
			'hide_out_of_stock',
			array(
				'label'        => esc_html__( 'Hide Out Of Stock', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
	}

	/**
	 * Extract the query arguments from the widget settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function get_product_query_args( array $settings ) {
		return array(
			'source'            => $settings['source'] ?? 'recent',
			'categories'        => $settings['categories'] ?? array(),
			'tags'              => $settings['tags'] ?? array(),
			'brands'            => $settings['brands'] ?? array(),
			'products'          => $settings['products'] ?? array(),
			'limit'             => $settings['limit'] ?? 10,
			'orderby'           => $settings['orderby'] ?? 'date',
			'order'             => $settings['order'] ?? 'DESC',
			'offset'            => $settings['offset'] ?? 0,
			'hide_out_of_stock' => 'yes' === ( $settings['hide_out_of_stock'] ?? '' ),
		);
	}

	/**
	 * Register post query controls.
	 *
	 * @param int $default_limit Default post count.
	 * @return void
	 */
	protected function register_post_query_controls( $default_limit = 4 ) {
		$this->add_control(
			'post_source',
			array(
				'label'   => esc_html__( 'Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'latest',
				'options' => array(
					'latest' => esc_html__( 'Latest Posts', 'trendino' ),
					'manual' => esc_html__( 'Specific Posts', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'posts',
			array(
				'label'       => esc_html__( 'Select Posts', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::post_options( 'post', 100 ),
				'condition'   => array( 'post_source' => 'manual' ),
			)
		);

		$this->add_control(
			'post_categories',
			array(
				'label'       => esc_html__( 'Categories', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'category' ),
				'condition'   => array( 'post_source' => 'latest' ),
			)
		);

		$this->add_control(
			'post_limit',
			array(
				'label'     => esc_html__( 'Posts Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 24,
				'default'   => (int) $default_limit,
				'condition' => array( 'post_source' => 'latest' ),
			)
		);

		$this->add_control(
			'post_orderby',
			array(
				'label'     => esc_html__( 'Order By', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'          => esc_html__( 'Date', 'trendino' ),
					'title'         => esc_html__( 'Title', 'trendino' ),
					'comment_count' => esc_html__( 'Comment Count', 'trendino' ),
					'modified'      => esc_html__( 'Last Modified', 'trendino' ),
					'rand'          => esc_html__( 'Random', 'trendino' ),
				),
				'condition' => array( 'post_source' => 'latest' ),
			)
		);

		$this->add_control(
			'post_order',
			array(
				'label'     => esc_html__( 'Order', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => esc_html__( 'Descending', 'trendino' ),
					'ASC'  => esc_html__( 'Ascending', 'trendino' ),
				),
				'condition' => array( 'post_source' => 'latest' ),
			)
		);
	}

	/**
	 * Build post query args from settings.
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	protected function get_post_query_args( array $settings ) {
		return array(
			'posts'      => 'manual' === ( $settings['post_source'] ?? 'latest' ) ? ( $settings['posts'] ?? array() ) : array(),
			'categories' => $settings['post_categories'] ?? array(),
			'limit'      => $settings['post_limit'] ?? 4,
			'orderby'    => $settings['post_orderby'] ?? 'date',
			'order'      => $settings['post_order'] ?? 'DESC',
		);
	}
}
