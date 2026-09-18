<?php
/**
 * Brand carousel / grid widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Section_Header_Controls;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Brands
 */
class Brands extends Base_Widget {

	use Carousel_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'brands', 'carousel', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-brands';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Brands', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-logo';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-shop' );
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
				'title'     => esc_html__( 'Popular brands', 'trendino' ),
				'link_text' => esc_html__( 'All brands', 'trendino' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Brands', 'trendino' ) )
		);

		$brand_taxonomy = Query_Helper::brand_taxonomy();

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $brand_taxonomy ? 'taxonomy' : 'manual',
				'options' => array(
					'taxonomy' => esc_html__( 'Brand Taxonomy', 'trendino' ),
					'manual'   => esc_html__( 'Manual Logos', 'trendino' ),
				),
			)
		);

		if ( ! $brand_taxonomy ) {
			$this->add_control(
				'no_taxonomy_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'No brand taxonomy detected. Install a brands plugin or use manual logos.', 'trendino' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array( 'source' => 'taxonomy' ),
				)
			);
		}

		$this->add_control(
			'brands',
			array(
				'label'       => esc_html__( 'Select Brands', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $brand_taxonomy ? Query_Helper::term_options( $brand_taxonomy ) : array(),
				'condition'   => array( 'source' => 'taxonomy' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Brands Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 40,
				'default'   => 12,
				'condition' => array( 'source' => 'taxonomy' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'logo',
			array(
				'label'   => esc_html__( 'Logo', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Brand Name', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Brand', 'trendino' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => esc_html__( 'URL', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'active',
			array(
				'label'        => esc_html__( 'Highlighted', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Logos', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'condition'   => array( 'source' => 'manual' ),
				'default'     => array_fill( 0, 6, array() ),
			)
		);

		$this->add_control(
			'show_name',
			array(
				'label'        => esc_html__( 'Show Brand Name', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => esc_html__( 'Display Mode', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => array(
					'carousel' => esc_html__( 'Carousel', 'trendino' ),
					'grid'     => esc_html__( 'Grid', 'trendino' ),
				),
			)
		);

		$this->register_columns_controls(
			array(
				'desktop' => 7,
				'tablet'  => 4,
				'mobile'  => 3,
				'max'     => 12,
				'selector'=> '{{WRAPPER}} .trendino-brands',
			)
		);

		$this->register_gap_control( 12, '{{WRAPPER}} .trendino-brands' );

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Arrows', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_mode' => 'carousel' ),
			)
		);

		$this->end_controls_section();

		$this->register_section_header_style_controls();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Logo Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_height',
			array(
				'label'      => esc_html__( 'Logo Box Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 88 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-brand' => 'min-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'logo_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-brand' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'selector' => '{{WRAPPER}} .trendino-brand',
			)
		);

		$this->add_control(
			'logo_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-brand' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grayscale',
			array(
				'label'        => esc_html__( 'Grayscale Logos', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'Hover Effect', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lift',
				'options' => array(
					'none'  => esc_html__( 'None', 'trendino' ),
					'lift'  => esc_html__( 'Lift', 'trendino' ),
					'color' => esc_html__( 'Colorize', 'trendino' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'name_typography',
				'selector'  => '{{WRAPPER}} .trendino-brand__name',
				'condition' => array( 'show_name' => 'yes' ),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'Name Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-brand__name' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_name' => 'yes' ),
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
		$items    = $this->get_items( $settings );

		if ( empty( $items ) ) {
			$this->editor_notice( esc_html__( 'No brands to display.', 'trendino' ) );

			return;
		}

		$is_carousel = 'carousel' === $settings['display_mode'];

		$classes = array(
			'trendino-brands',
			'hover-' . sanitize_html_class( $settings['hover_effect'] ),
			$is_carousel ? 'is-carousel' : 'is-grid',
		);

		if ( 'yes' === $settings['grayscale'] ) {
			$classes[] = 'is-grayscale';
		}
		?>
		<div class="trendino-brands-wrap trendino-section-box">
			<?php $this->render_section_header( $settings, $is_carousel && 'yes' === $settings['arrows'] ); ?>

			<div class="trendino-carousel">
				<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php echo $is_carousel ? 'data-trendino-scroller' : ''; ?>>
					<?php foreach ( $items as $item ) : ?>
						<a class="trendino-brand<?php echo $item['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $item['url'] ); ?>" title="<?php echo esc_attr( $item['name'] ); ?>">
							<?php if ( $item['logo_html'] ) : ?>
								<?php echo $item['logo_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="trendino-brand__fallback"><?php echo esc_html( $item['name'] ); ?></span>
							<?php endif; ?>

							<?php if ( 'yes' === $settings['show_name'] && $item['logo_html'] ) : ?>
								<span class="trendino-brand__name"><?php echo esc_html( $item['name'] ); ?></span>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Build brand items.
	 *
	 * @param array $settings Settings.
	 * @return array<int,array{name:string,url:string,logo_html:string,active:bool}>
	 */
	protected function get_items( array $settings ) {
		$items = array();

		if ( 'manual' === $settings['source'] ) {
			foreach ( (array) ( $settings['items'] ?? array() ) as $item ) {
				$logo_html = '';

				if ( ! empty( $item['logo']['id'] ) ) {
					$logo_html = wp_get_attachment_image(
						(int) $item['logo']['id'],
						'trendino-brand',
						false,
						array( 'class' => 'trendino-brand__logo', 'alt' => $item['name'] ?? '', 'loading' => 'lazy' )
					);
				} elseif ( ! empty( $item['logo']['url'] ) ) {
					$logo_html = sprintf(
						'<img class="trendino-brand__logo" src="%s" alt="%s" loading="lazy" decoding="async" />',
						esc_url( $item['logo']['url'] ),
						esc_attr( $item['name'] ?? '' )
					);
				}

				$items[] = array(
					'name'      => $item['name'] ?? '',
					'url'       => $item['link']['url'] ?? '#',
					'logo_html' => $logo_html,
					'active'    => 'yes' === ( $item['active'] ?? '' ),
				);
			}

			return $items;
		}

		$terms = Query_Helper::brands(
			array(
				'include' => $settings['brands'] ?? array(),
				'number'  => (int) $settings['limit'],
			)
		);

		foreach ( $terms as $term ) {
			$thumbnail_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

			if ( ! $thumbnail_id ) {
				$thumbnail_id = (int) get_term_meta( $term->term_id, 'pwb_brand_image', true );
			}

			$logo_html = $thumbnail_id
				? wp_get_attachment_image( $thumbnail_id, 'trendino-brand', false, array( 'class' => 'trendino-brand__logo', 'alt' => $term->name, 'loading' => 'lazy' ) )
				: '';

			$items[] = array(
				'name'      => $term->name,
				'url'       => get_term_link( $term ),
				'logo_html' => $logo_html,
				'active'    => false,
			);
		}

		return $items;
	}
}
