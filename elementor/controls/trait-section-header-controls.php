<?php
/**
 * Reusable section header controls (title, subtitle, "see all" link) + styles.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Trait Section_Header_Controls
 */
trait Section_Header_Controls {

	/**
	 * Register the content controls for a section header.
	 *
	 * @param array $defaults title, subtitle, link_text.
	 * @return void
	 */
	protected function register_section_header_controls( array $defaults = array() ) {
		$defaults = wp_parse_args(
			$defaults,
			array(
				'title'     => esc_html__( 'Section title', 'trendino' ),
				'subtitle'  => '',
				'link_text' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'Show Section Header', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => $defaults['title'],
				'condition'   => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => $defaults['subtitle'],
				'condition'   => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label'     => esc_html__( 'Title Icon', 'trendino' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'     => esc_html__( 'Link Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $defaults['link_text'],
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'     => esc_html__( 'Link URL', 'trendino' ),
				'type'      => Controls_Manager::URL,
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'header_align',
			array(
				'label'     => esc_html__( 'Alignment', 'trendino' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'start'  => array(
						'title' => esc_html__( 'Start', 'trendino' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'trendino' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'    => array(
						'title' => esc_html__( 'End', 'trendino' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'start',
				'condition' => array( 'show_header' => 'yes' ),
			)
		);
	}

	/**
	 * Register style controls for the section header.
	 *
	 * @return void
	 */
	protected function register_section_header_style_controls() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label'     => esc_html__( 'Section Header', 'trendino' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-section-head__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-section-head__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Subtitle Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-section-head__subtitle',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-section-head__subtitle' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-section-head__link' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => esc_html__( 'Spacing Below', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-section-head' => 'margin-block-end:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the section header from settings.
	 *
	 * @param array $settings Widget settings.
	 * @param bool  $show_nav Whether to render carousel arrows inside the header.
	 * @return void
	 */
	protected function render_section_header( array $settings, $show_nav = false ) {
		if ( 'yes' !== ( $settings['show_header'] ?? 'yes' ) ) {
			return;
		}

		$icon_html = '';

		if ( ! empty( $settings['header_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) );
			$icon_html = ob_get_clean();
		}

		\Trendino\Elementor\Render_Helper::section_header(
			array(
				'title'     => $settings['title'] ?? '',
				'subtitle'  => $settings['subtitle'] ?? '',
				'icon_html' => $icon_html,
				'link'      => $settings['link'] ?? array(),
				'link_text' => $settings['link_text'] ?? '',
				'align'     => $settings['header_align'] ?? 'start',
				'show_nav'  => $show_nav,
			)
		);
	}
}
