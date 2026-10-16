<?php
/**
 * Banner grid widget: 2..6 promotional boxes in a responsive grid.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Section_Header_Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Banner_Grid
 */
class Banner_Grid extends Base_Widget {

	use Carousel_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'banner', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-banner-grid';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Banner Grid', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_header',
			array( 'label' => esc_html__( 'Section Header', 'trendino' ) )
		);

		$this->register_section_header_controls(
			array(
				'title'     => '',
				'link_text' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Banners', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() ),
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
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => esc_html__( 'Description', 'trendino' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 2,
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
				'label' => esc_html__( 'Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'span',
			array(
				'label'   => esc_html__( 'Column Span', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => esc_html__( '1 column', 'trendino' ),
					'2' => esc_html__( '2 columns', 'trendino' ),
					'3' => esc_html__( '3 columns', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'content_position',
			array(
				'label'   => esc_html__( 'Content Position', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-start',
				'options' => array(
					'top-start'     => esc_html__( 'Top Start', 'trendino' ),
					'center-start'  => esc_html__( 'Middle Start', 'trendino' ),
					'center-center' => esc_html__( 'Middle Center', 'trendino' ),
					'bottom-start'  => esc_html__( 'Bottom Start', 'trendino' ),
					'bottom-center' => esc_html__( 'Bottom Center', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-banner__content' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'banners',
			array(
				'label'       => esc_html__( 'Banners', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title || "Banner" }}}',
				'default'     => array( array(), array(), array(), array() ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->register_columns_controls(
			array(
				'desktop' => 4,
				'tablet'  => 2,
				'mobile'  => 1,
				'max'     => 6,
				'selector'=> '{{WRAPPER}} .trendino-banner-grid',
			)
		);

		$this->register_gap_control( 16, '{{WRAPPER}} .trendino-banner-grid' );

		$this->add_responsive_control(
			'item_height',
			array(
				'label'      => esc_html__( 'Banner Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 600 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 200 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 150 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner' => '--trendino-banner-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_scroll',
			array(
				'label'        => esc_html__( 'Horizontal Scroll On Mobile', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->register_section_header_style_controls();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Banner Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner' => 'border-radius:{{SIZE}}{{UNIT}};',
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
				),
				'selectors' => array(
					'{{WRAPPER}} .trendino-banner__img' => 'object-fit:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-banner__title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_desc_typography',
				'label'    => esc_html__( 'Description Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-banner__desc',
			)
		);

		$this->add_responsive_control(
			'item_content_padding',
			array(
				'label'      => esc_html__( 'Content Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-banner__content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'none' => esc_html__( 'None', 'trendino' ),
					'zoom' => esc_html__( 'Zoom', 'trendino' ),
					'lift' => esc_html__( 'Lift', 'trendino' ),
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
		$banners  = $settings['banners'] ?? array();

		if ( empty( $banners ) ) {
			$this->editor_notice( esc_html__( 'Add at least one banner.', 'trendino' ) );

			return;
		}

		$classes = array( 'trendino-banner-grid' );

		if ( 'yes' === $settings['mobile_scroll'] ) {
			$classes[] = 'is-mobile-scroll';
		}
		?>
		<div class="trendino-banner-grid-wrap">
			<?php $this->render_section_header( $settings ); ?>

			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<?php foreach ( $banners as $banner ) : ?>
					<?php
					$has_link = ! empty( $banner['link']['url'] );
					$has_text = $banner['title'] || $banner['description'] || $banner['button_text'];
					$classes  = array(
						'trendino-banner',
						'elementor-repeater-item-' . $banner['_id'],
						'hover-' . sanitize_html_class( $settings['hover_effect'] ),
						'span-' . absint( $banner['span'] ),
					);
					?>
					<<?php echo $has_link ? 'a' : 'div'; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
						<?php if ( $has_link ) : ?>
							href="<?php echo esc_url( $banner['link']['url'] ); ?>"
							<?php echo ! empty( $banner['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
						<?php endif; ?>>

						<?php if ( ! empty( $banner['image']['url'] ) ) : ?>
							<picture class="trendino-banner__picture">
								<?php if ( ! empty( $banner['image_mobile']['url'] ) ) : ?>
									<source media="(max-width: 767px)" srcset="<?php echo esc_url( $banner['image_mobile']['url'] ); ?>" />
								<?php endif; ?>
								<img class="trendino-banner__img" src="<?php echo esc_url( $banner['image']['url'] ); ?>" alt="<?php echo esc_attr( $banner['title'] ); ?>" loading="lazy" decoding="async" />
							</picture>
						<?php endif; ?>

						<?php if ( $has_text ) : ?>
							<div class="trendino-banner__content is-<?php echo esc_attr( $banner['content_position'] ); ?>">
								<?php if ( $banner['title'] ) : ?>
									<h3 class="trendino-banner__title"><?php echo esc_html( $banner['title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( $banner['description'] ) : ?>
									<p class="trendino-banner__desc"><?php echo esc_html( $banner['description'] ); ?></p>
								<?php endif; ?>
								<?php if ( $banner['button_text'] ) : ?>
									<span class="trendino-banner__button">
										<?php echo esc_html( $banner['button_text'] ); ?>
										<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ); ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</<?php echo $has_link ? 'a' : 'div'; ?>>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
