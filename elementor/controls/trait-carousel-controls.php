<?php
/**
 * Reusable carousel / grid layout controls.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Controls;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Carousel_Controls
 */
trait Carousel_Controls {

	/**
	 * Responsive column controls.
	 *
	 * @param array $defaults desktop, tablet, mobile, max.
	 * @return void
	 */
	protected function register_columns_controls( array $defaults = array() ) {
		$defaults = wp_parse_args(
			$defaults,
			array(
				'desktop' => 5,
				'tablet'  => 3,
				'mobile'  => 2,
				'max'     => 8,
				'selector'=> '{{WRAPPER}} .trendino-track',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'           => esc_html__( 'Columns', 'trendino' ),
				'type'            => Controls_Manager::NUMBER,
				'min'             => 1,
				'max'             => (int) $defaults['max'],
				'step'            => 1,
				'default'         => (int) $defaults['desktop'],
				'tablet_default'  => (int) $defaults['tablet'],
				'mobile_default'  => (float) $defaults['mobile'],
				'selectors'       => array(
					$defaults['selector'] => '--trendino-columns:{{VALUE}};',
				),
				'render_type'     => 'template',
			)
		);
	}

	/**
	 * Gap control (responsive).
	 *
	 * @param int    $default  Default gap in px.
	 * @param string $selector CSS selector receiving the --trendino-gap variable.
	 * @return void
	 */
	protected function register_gap_control( $default = 12, $selector = '{{WRAPPER}} .trendino-track' ) {
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => (int) $default,
				),
				'selectors'  => array(
					$selector => '--trendino-gap:{{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	/**
	 * Slider behaviour controls (autoplay, loop, arrows, dots, speed).
	 *
	 * @param array $defaults Optional defaults.
	 * @return void
	 */
	protected function register_carousel_behaviour_controls( array $defaults = array() ) {
		$defaults = wp_parse_args(
			$defaults,
			array(
				'arrows'   => 'yes',
				'dots'     => '',
				'autoplay' => '',
				'loop'     => '',
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Arrows', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $defaults['arrows'],
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => esc_html__( 'Pagination Dots', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $defaults['dots'],
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $defaults['autoplay'],
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'Autoplay Delay (ms)', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'default'   => 5000,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'Infinite Loop', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => $defaults['loop'],
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause On Hover', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);
	}

	/**
	 * Arrow style controls.
	 *
	 * @return void
	 */
	protected function register_arrow_style_controls() {
		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-carousel__arrow' => 'color:{{VALUE}};',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => esc_html__( 'Arrow Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-carousel__arrow' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_size',
			array(
				'label'      => esc_html__( 'Arrow Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-carousel__arrow' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_radius',
			array(
				'label'      => esc_html__( 'Arrow Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-carousel__arrow' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'arrows' => 'yes' ),
			)
		);
	}
}
