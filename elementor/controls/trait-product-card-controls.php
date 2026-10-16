<?php
/**
 * Reusable product card display + style controls.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Product_Card_Controls
 */
trait Product_Card_Controls {

	/**
	 * Product element toggles (own section).
	 *
	 * @return void
	 */
	protected function register_product_card_controls() {
		$this->start_controls_section(
			'section_product',
			array(
				'label' => esc_html__( 'Product Card', 'trendino' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'card_layout',
			array(
				'label'   => esc_html__( 'Card Layout', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => array(
					'vertical'   => esc_html__( 'Vertical', 'trendino' ),
					'horizontal' => esc_html__( 'Horizontal', 'trendino' ),
					'minimal'    => esc_html__( 'Minimal (no frame)', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'image_ratio',
			array(
				'label'   => esc_html__( 'Image Ratio', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1-1',
				'options' => array(
					'1-1'  => '1:1',
					'4-3'  => '4:3',
					'3-4'  => '3:4',
					'16-9' => '16:9',
				),
			)
		);

		$toggles = array(
			'show_image'     => array( esc_html__( 'Show Image', 'trendino' ), 'yes' ),
			'show_badge'     => array( esc_html__( 'Show Badges', 'trendino' ), 'yes' ),
			'show_discount'  => array( esc_html__( 'Show Discount %', 'trendino' ), 'yes' ),
			'show_brand'     => array( esc_html__( 'Show Brand', 'trendino' ), '' ),
			'show_title'     => array( esc_html__( 'Show Title', 'trendino' ), 'yes' ),
			'show_rating'    => array( esc_html__( 'Show Rating', 'trendino' ), 'yes' ),
			'show_price'     => array( esc_html__( 'Show Price', 'trendino' ), 'yes' ),
			'show_old_price' => array( esc_html__( 'Show Old Price', 'trendino' ), 'yes' ),
			'show_stock'     => array( esc_html__( 'Show Stock Status', 'trendino' ), '' ),
			'colors_swatch'  => array( esc_html__( 'Show Color Swatches', 'trendino' ), 'yes' ),
			'show_cart'      => array( esc_html__( 'Show Add To Cart', 'trendino' ), '' ),
			'show_wishlist'  => array( esc_html__( 'Show Wishlist', 'trendino' ), 'yes' ),
			'show_compare'   => array( esc_html__( 'Show Compare', 'trendino' ), '' ),
			'show_quickview' => array( esc_html__( 'Show Quick View', 'trendino' ), '' ),
		);

		foreach ( $toggles as $key => $config ) {
			$this->add_control(
				$key,
				array(
					'label'        => $config[0],
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $config[1],
				)
			);
		}

		$this->add_control(
			'title_length',
			array(
				'label'     => esc_html__( 'Title Max Characters', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 200,
				'default'   => 60,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Product card style controls (own section).
	 *
	 * @return void
	 */
	protected function register_product_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Product Card Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => esc_html__( 'Card Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .trendino-product-card',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-product-card' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .trendino-product-card',
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-product-card' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => esc_html__( 'Title', 'trendino' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_title_typography',
				'selector'  => '{{WRAPPER}} .trendino-product-card__title',
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card__title a' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'heading_price_style',
			array(
				'label'     => esc_html__( 'Price', 'trendino' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_price' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_price_typography',
				'selector'  => '{{WRAPPER}} .trendino-product-card__price',
				'condition' => array( 'show_price' => 'yes' ),
			)
		);

		$this->add_control(
			'card_price_color',
			array(
				'label'     => esc_html__( 'Price Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card__price' => 'color:{{VALUE}};',
					'{{WRAPPER}} .trendino-product-card__price .woocommerce-Price-amount' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_price' => 'yes' ),
			)
		);

		$this->add_control(
			'card_old_price_color',
			array(
				'label'     => esc_html__( 'Old Price Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card__old-price' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_old_price' => 'yes' ),
			)
		);

		$this->add_control(
			'card_discount_bg',
			array(
				'label'     => esc_html__( 'Discount Badge Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card__discount' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'show_discount' => 'yes' ),
			)
		);

		$this->add_control(
			'card_discount_color',
			array(
				'label'     => esc_html__( 'Discount Badge Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-card__discount' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_discount' => 'yes' ),
			)
		);

		$this->add_control(
			'card_badge_bg',
			array(
				'label'     => esc_html__( 'Sale Badge Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-badge.is-sale' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Collect the card settings subset passed to Render_Helper::product_card().
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function get_product_card_settings( array $settings ) {
		$keys = array(
			'show_image',
			'show_badge',
			'show_discount',
			'show_brand',
			'show_title',
			'show_rating',
			'show_price',
			'show_old_price',
			'show_stock',
			'show_cart',
			'show_wishlist',
			'show_compare',
			'show_quickview',
			'colors_swatch',
			'title_length',
			'card_layout',
			'image_ratio',
		);

		$card = array();

		foreach ( $keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$card[ $key ] = $settings[ $key ];
			}
		}

		return $card;
	}
}
