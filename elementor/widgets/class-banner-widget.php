<?php
/**
 * Single promotional banner widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Banner
 */
class Banner extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'banner' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-banner';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Banner', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image-rollover';
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Banner', 'trendino' ) )
		);

		$this->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Desktop Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'image_tablet',
			array(
				'label' => esc_html__( 'Tablet Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'image_mobile',
			array(
				'label' => esc_html__( 'Mobile Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'trendino' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => esc_html__( 'Button Text', 'trendino' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'trendino' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'content_position',
			array(
				'label'   => esc_html__( 'Content Position', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center-start',
				'options' => array(
					'top-start'     => esc_html__( 'Top Start', 'trendino' ),
					'top-center'    => esc_html__( 'Top Center', 'trendino' ),
					'center-start'  => esc_html__( 'Middle Start', 'trendino' ),
					'center-center' => esc_html__( 'Middle Center', 'trendino' ),
					'center-end'    => esc_html__( 'Middle End', 'trendino' ),
					'bottom-start'  => esc_html__( 'Bottom Start', 'trendino' ),
					'bottom-center' => esc_html__( 'Bottom Center', 'trendino' ),
				),
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'trendino' ),
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
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__content' => 'text-align:{{VALUE}};align-items:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'Hover Effect', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => array(
					'none'   => esc_html__( 'None', 'trendino' ),
					'zoom'   => esc_html__( 'Zoom', 'trendino' ),
					'lift'   => esc_html__( 'Lift', 'trendino' ),
					'shine'  => esc_html__( 'Shine', 'trendino' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'banner_height',
			array(
				'label'      => esc_html__( 'Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 700 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner' => '--trendino-banner-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'     => esc_html__( 'Aspect Ratio', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''      => esc_html__( 'Auto / Height', 'trendino' ),
					'2/1'   => '2:1',
					'3/1'   => '3:1',
					'4/3'   => '4:3',
					'16/9'  => '16:9',
					'1/1'   => '1:1',
					'21/9'  => '21:9',
				),
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner' => 'aspect-ratio:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label'     => esc_html__( 'Image Fit', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'cover'   => esc_html__( 'Cover', 'trendino' ),
					'contain' => esc_html__( 'Contain', 'trendino' ),
					'fill'    => esc_html__( 'Fill', 'trendino' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__img' => 'object-fit:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'banner_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trendino-banner',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'Overlay', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__overlay' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'banner_border',
				'selector' => '{{WRAPPER}} .trendino-banner',
			)
		);

		$this->add_responsive_control(
			'banner_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'banner_shadow',
				'selector' => '{{WRAPPER}} .trendino-banner',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Content Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner__content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-banner__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Description Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-banner__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Description Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__desc' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_heading',
			array(
				'label'     => esc_html__( 'Button', 'trendino' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .trendino-banner__button',
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'Button Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__button' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'Button Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner__button' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$has_link = ! empty( $settings['link']['url'] );
		$has_text = $settings['title'] || $settings['description'] || $settings['button_text'];

		$classes = array(
			'trendino-banner',
			'hover-' . sanitize_html_class( $settings['hover_effect'] ),
		);
		?>
		<<?php echo $has_link ? 'a' : 'div'; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			<?php if ( $has_link ) : ?>
				href="<?php echo esc_url( $settings['link']['url'] ); ?>"
				<?php echo ! empty( $settings['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
				<?php echo ! empty( $settings['link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>
			<?php endif; ?>>

			<?php if ( ! empty( $settings['image']['url'] ) ) : ?>
				<picture class="trendino-banner__picture">
					<?php if ( ! empty( $settings['image_mobile']['url'] ) ) : ?>
						<source media="(max-width: 767px)" srcset="<?php echo esc_url( $settings['image_mobile']['url'] ); ?>" />
					<?php endif; ?>
					<?php if ( ! empty( $settings['image_tablet']['url'] ) ) : ?>
						<source media="(max-width: 1024px)" srcset="<?php echo esc_url( $settings['image_tablet']['url'] ); ?>" />
					<?php endif; ?>
					<img class="trendino-banner__img" src="<?php echo esc_url( $settings['image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['title'] ); ?>" loading="lazy" decoding="async" />
				</picture>
			<?php endif; ?>

			<span class="trendino-banner__overlay" aria-hidden="true"></span>

			<?php if ( $has_text ) : ?>
				<div class="trendino-banner__content is-<?php echo esc_attr( $settings['content_position'] ); ?>">
					<?php if ( $settings['title'] ) : ?>
						<h3 class="trendino-banner__title"><?php echo esc_html( $settings['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( $settings['description'] ) : ?>
						<p class="trendino-banner__desc"><?php echo esc_html( $settings['description'] ); ?></p>
					<?php endif; ?>
					<?php if ( $settings['button_text'] ) : ?>
						<span class="trendino-banner__button">
							<?php echo esc_html( $settings['button_text'] ); ?>
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 15 ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</<?php echo $has_link ? 'a' : 'div'; ?>>
		<?php
	}
}
