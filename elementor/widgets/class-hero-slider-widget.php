<?php
/**
 * Hero slider widget (main homepage banner with optional side banners).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Hero_Slider
 */
class Hero_Slider extends Base_Widget {

	use Carousel_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'hero', 'carousel' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-hero-slider';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Hero Slider', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'trendino', 'hero', 'slider', 'banner', 'carousel' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			array( 'label' => esc_html__( 'Slides', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Desktop Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'image_tablet',
			array(
				'label' => esc_html__( 'Tablet Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'image_mobile',
			array(
				'label' => esc_html__( 'Mobile Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'trendino' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label' => esc_html__( 'Button Text', 'trendino' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => esc_html__( 'Link', 'trendino' ),
				'type'    => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
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

		$repeater->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-hero__content' => 'color:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'Overlay', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-hero__overlay' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'Slides', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title || "Slide" }}}',
				'default'     => array(
					array( 'title' => esc_html__( 'Slide one', 'trendino' ) ),
					array( 'title' => esc_html__( 'Slide two', 'trendino' ) ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_side',
			array( 'label' => esc_html__( 'Side Banners', 'trendino' ) )
		);

		$this->add_control(
			'show_side',
			array(
				'label'        => esc_html__( 'Enable Side Column', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Adds a stacked banner column next to the slider (hidden on mobile).', 'trendino' ),
			)
		);

		$side = new Repeater();

		$side->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$side->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$side->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'trendino' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'side_banners',
			array(
				'label'       => esc_html__( 'Banners', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $side->get_controls(),
				'title_field' => '{{{ title || "Banner" }}}',
				'condition'   => array( 'show_side' => 'yes' ),
				'default'     => array( array(), array() ),
			)
		);

		$this->add_responsive_control(
			'side_width',
			array(
				'label'      => esc_html__( 'Side Column Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 15, 'max' => 50 ),
					'px' => array( 'min' => 160, 'max' => 520 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 26 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-hero-layout' => '--trendino-hero-side:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_side' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array( 'label' => esc_html__( 'Slider Settings', 'trendino' ) )
		);

		$this->register_carousel_behaviour_controls(
			array(
				'arrows'   => 'yes',
				'dots'     => 'yes',
				'autoplay' => 'yes',
				'loop'     => 'yes',
			)
		);

		$this->add_control(
			'transition',
			array(
				'label'   => esc_html__( 'Transition', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => esc_html__( 'Slide', 'trendino' ),
					'fade'  => esc_html__( 'Fade', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'transition_speed',
			array(
				'label'   => esc_html__( 'Transition Speed (ms)', 'trendino' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 500,
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
			'hero_height',
			array(
				'label'      => esc_html__( 'Slider Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 140, 'max' => 800 ),
					'vh' => array( 'min' => 20, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 380 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 300 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 200 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-hero' => '--trendino-hero-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-hero, {{WRAPPER}} .trendino-hero__slide, {{WRAPPER}} .trendino-hero-side__item' => 'border-radius:{{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .trendino-hero__img' => 'object-fit:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hero_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-hero__title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hero_subtitle_typography',
				'label'    => esc_html__( 'Subtitle Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-hero__subtitle',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Content Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-hero__content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-hero__button' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-hero__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .trendino-hero__button',
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => esc_html__( 'Dots Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-carousel__dot' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'dots' => 'yes' ),
			)
		);

		$this->add_control(
			'dots_active_color',
			array(
				'label'     => esc_html__( 'Active Dot Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-carousel__dot.is-active' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'dots' => 'yes' ),
			)
		);

		$this->register_arrow_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = $settings['slides'] ?? array();

		if ( empty( $slides ) ) {
			$this->editor_notice( esc_html__( 'Add at least one slide.', 'trendino' ) );

			return;
		}

		$config = array(
			'autoplay'      => 'yes' === $settings['autoplay'],
			'autoplayDelay' => (int) $settings['autoplay_delay'],
			'loop'          => 'yes' === $settings['loop'],
			'pauseOnHover'  => 'yes' === $settings['pause_on_hover'],
			'effect'        => $settings['transition'],
			'speed'         => (int) $settings['transition_speed'],
			'rtl'           => is_rtl(),
		);

		$show_side = 'yes' === $settings['show_side'] && ! empty( $settings['side_banners'] );
		?>
		<div class="trendino-hero-layout<?php echo $show_side ? ' has-side' : ''; ?>">
			<div class="trendino-hero trendino-carousel is-<?php echo esc_attr( $settings['transition'] ); ?>"
				data-trendino-slider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
				<div class="trendino-hero__viewport">
					<div class="trendino-hero__track">
						<?php foreach ( $slides as $index => $slide ) : ?>
							<?php $this->render_slide( $slide, $index ); ?>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( 'yes' === $settings['arrows'] && count( $slides ) > 1 ) : ?>
					<button type="button" class="trendino-carousel__arrow is-prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous slide', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 22 ) ); ?>
					</button>
					<button type="button" class="trendino-carousel__arrow is-next" data-carousel-next aria-label="<?php esc_attr_e( 'Next slide', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 22 ) ); ?>
					</button>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['dots'] && count( $slides ) > 1 ) : ?>
					<div class="trendino-carousel__dots" data-carousel-dots>
						<?php foreach ( $slides as $index => $slide ) : ?>
							<button type="button" class="trendino-carousel__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" data-carousel-dot="<?php echo esc_attr( $index ); ?>"
								aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Go to slide %d', 'trendino' ), $index + 1 ) ); ?>"></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $show_side ) : ?>
				<div class="trendino-hero-side">
					<?php foreach ( $settings['side_banners'] as $banner ) : ?>
						<?php $has_link = ! empty( $banner['link']['url'] ); ?>
						<<?php echo $has_link ? 'a' : 'div'; ?> class="trendino-hero-side__item elementor-repeater-item-<?php echo esc_attr( $banner['_id'] ); ?>"
							<?php if ( $has_link ) : ?>
								href="<?php echo esc_url( $banner['link']['url'] ); ?>"
								<?php echo ! empty( $banner['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
							<?php endif; ?>>
							<?php if ( ! empty( $banner['image']['id'] ) ) : ?>
								<?php echo wp_get_attachment_image( (int) $banner['image']['id'], 'trendino-banner-md', false, array( 'class' => 'trendino-hero-side__img', 'alt' => $banner['title'] ?? '', 'loading' => 'lazy' ) ); ?>
							<?php elseif ( ! empty( $banner['image']['url'] ) ) : ?>
								<img class="trendino-hero-side__img" src="<?php echo esc_url( $banner['image']['url'] ); ?>" alt="<?php echo esc_attr( $banner['title'] ?? '' ); ?>" loading="lazy" />
							<?php else : ?>
								<img class="trendino-hero-side__img is-placeholder" src="<?php echo esc_url( trendino_placeholder_image() ); ?>" alt="" loading="lazy" />
							<?php endif; ?>
						</<?php echo $has_link ? 'a' : 'div'; ?>>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render a single slide.
	 *
	 * @param array $slide Slide settings.
	 * @param int   $index Slide index.
	 * @return void
	 */
	protected function render_slide( array $slide, $index ) {
		$position  = $slide['content_position'] ?? 'center-start';
		$has_link  = ! empty( $slide['link']['url'] );
		$has_text  = ! empty( $slide['title'] ) || ! empty( $slide['subtitle'] ) || ! empty( $slide['button_text'] );
		$image_url = $slide['image']['url'] ?? '';
		?>
		<div class="trendino-hero__slide elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); ?><?php echo 0 === $index ? ' is-active' : ''; ?>"
			data-slide-index="<?php echo esc_attr( $index ); ?>"
			<?php echo 0 !== $index ? ' aria-hidden="true"' : ''; ?>>

			<?php if ( $has_link ) : ?>
				<a class="trendino-hero__link" href="<?php echo esc_url( $slide['link']['url'] ); ?>"
					<?php echo ! empty( $slide['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
					aria-label="<?php echo esc_attr( $slide['title'] ? $slide['title'] : __( 'Banner', 'trendino' ) ); ?>"></a>
			<?php endif; ?>

			<picture class="trendino-hero__picture">
				<?php if ( ! empty( $slide['image_mobile']['url'] ) ) : ?>
					<source media="(max-width: 767px)" srcset="<?php echo esc_url( $slide['image_mobile']['url'] ); ?>" />
				<?php endif; ?>
				<?php if ( ! empty( $slide['image_tablet']['url'] ) ) : ?>
					<source media="(max-width: 1024px)" srcset="<?php echo esc_url( $slide['image_tablet']['url'] ); ?>" />
				<?php endif; ?>
				<img class="trendino-hero__img"
					src="<?php echo esc_url( $image_url ? $image_url : trendino_placeholder_image() ); ?>"
					alt="<?php echo esc_attr( $slide['title'] ?? '' ); ?>"
					<?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
					decoding="async" />
			</picture>

			<span class="trendino-hero__overlay" aria-hidden="true"></span>

			<?php if ( $has_text ) : ?>
				<div class="trendino-hero__content is-<?php echo esc_attr( $position ); ?>">
					<?php if ( ! empty( $slide['title'] ) ) : ?>
						<h2 class="trendino-hero__title"><?php echo esc_html( $slide['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $slide['subtitle'] ) ) : ?>
						<p class="trendino-hero__subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $slide['button_text'] ) ) : ?>
						<span class="trendino-hero__button">
							<?php echo esc_html( $slide['button_text'] ); ?>
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 16 ) ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
