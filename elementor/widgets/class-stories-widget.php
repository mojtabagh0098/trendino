<?php
/**
 * Stories / circular categories widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Controls\Carousel_Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Stories
 */
class Stories extends Base_Widget {

	use Carousel_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'stories', 'carousel' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-stories';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Stories / Circles', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-circle';
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'trendino', 'stories', 'categories', 'circle', 'highlight' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Items', 'trendino' ) )
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'      => esc_html__( 'Manual Items', 'trendino' ),
					'product_cat' => esc_html__( 'WooCommerce Categories', 'trendino' ),
					'brands'      => esc_html__( 'Brands', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'categories',
			array(
				'label'       => esc_html__( 'Select Categories', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'product_cat' ),
				'description' => esc_html__( 'Leave empty to use top level categories.', 'trendino' ),
				'condition'   => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'terms_limit',
			array(
				'label'     => esc_html__( 'Items Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 30,
				'default'   => 10,
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'enabled',
			array(
				'label'        => esc_html__( 'Enabled', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Category', 'trendino' ),
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
			'ring_color',
			array(
				'label'     => esc_html__( 'Ring Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-story__ring' => 'border-color:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'badge',
			array(
				'label'       => esc_html__( 'Badge Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Story Items', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'source' => 'manual' ),
				'default'     => array_map(
					static function ( $index ) {
						return array(
							'title'   => sprintf( /* translators: %d: item number */ esc_html__( 'Item %d', 'trendino' ), $index ),
							'enabled' => 'yes',
						);
					},
					range( 1, 8 )
				),
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Titles', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
					'carousel' => esc_html__( 'Carousel / Scroll', 'trendino' ),
					'grid'     => esc_html__( 'Grid', 'trendino' ),
				),
			)
		);

		$this->register_columns_controls(
			array(
				'desktop' => 10,
				'tablet'  => 6,
				'mobile'  => 4.2,
				'max'     => 14,
				'selector'=> '{{WRAPPER}} .trendino-stories',
			)
		);

		$this->register_gap_control( 16, '{{WRAPPER}} .trendino-stories' );

		$this->add_control(
			'shape',
			array(
				'label'   => esc_html__( 'Shape', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle'  => esc_html__( 'Circle', 'trendino' ),
					'rounded' => esc_html__( 'Rounded Square', 'trendino' ),
					'square'  => esc_html__( 'Square', 'trendino' ),
				),
			)
		);

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

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'circle_size',
			array(
				'label'      => esc_html__( 'Circle Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 48, 'max' => 180 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 96 ),
				'tablet_default' => array( 'unit' => 'px', 'size' => 84 ),
				'mobile_default' => array( 'unit' => 'px', 'size' => 72 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-stories' => '--trendino-story-size:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ring_width',
			array(
				'label'      => esc_html__( 'Ring Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 8 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 2 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-story__ring' => 'border-width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ring_color_global',
			array(
				'label'     => esc_html__( 'Ring Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-story__ring' => 'border-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ring_gap',
			array(
				'label'      => esc_html__( 'Ring Inner Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 12 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 3 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-story__ring' => 'padding:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .trendino-story__media',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .trendino-story__title',
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-story__title' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'title_spacing',
			array(
				'label'      => esc_html__( 'Title Spacing', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-story__title' => 'margin-block-start:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_title' => 'yes' ),
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
					'zoom'  => esc_html__( 'Zoom Image', 'trendino' ),
					'ring'  => esc_html__( 'Highlight Ring', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'wrapper_bg',
			array(
				'label'     => esc_html__( 'Section Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-stories-wrap' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => esc_html__( 'Section Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-stories-wrap' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'wrapper_radius',
			array(
				'label'      => esc_html__( 'Section Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-stories-wrap' => 'border-radius:{{SIZE}}{{UNIT}};',
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
		$items    = $this->get_items( $settings );

		if ( empty( $items ) ) {
			$this->editor_notice( esc_html__( 'Add story items or choose a category source.', 'trendino' ) );

			return;
		}

		$classes = array(
			'trendino-stories',
			'is-' . sanitize_html_class( $settings['display_mode'] ),
			'shape-' . sanitize_html_class( $settings['shape'] ),
			'hover-' . sanitize_html_class( $settings['hover_effect'] ),
		);

		$is_carousel = 'carousel' === $settings['display_mode'];
		?>
		<div class="trendino-stories-wrap">
			<?php if ( $is_carousel && 'yes' === $settings['arrows'] ) : ?>
				<div class="trendino-carousel__nav is-floating" data-carousel-nav>
					<button type="button" class="trendino-carousel__arrow" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 18 ) ); ?>
					</button>
					<button type="button" class="trendino-carousel__arrow" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 18 ) ); ?>
					</button>
				</div>
			<?php endif; ?>

			<ul class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
				<?php if ( $is_carousel ) : ?>data-trendino-scroller<?php endif; ?>>
				<?php foreach ( $items as $index => $item ) : ?>
					<li class="trendino-story elementor-repeater-item-<?php echo esc_attr( $item['id'] ); ?>">
						<a class="trendino-story__link" href="<?php echo esc_url( $item['url'] ); ?>"
							<?php echo ! empty( $item['external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
							<span class="trendino-story__ring">
								<span class="trendino-story__media">
									<?php if ( $item['image_id'] ) : ?>
										<?php
										echo wp_get_attachment_image(
											(int) $item['image_id'],
											'trendino-story',
											false,
											array(
												'class'    => 'trendino-story__img',
												'alt'      => $item['title'],
												'loading'  => $index < 6 ? 'eager' : 'lazy',
												'decoding' => 'async',
											)
										);
										?>
									<?php elseif ( $item['image_url'] ) : ?>
										<img class="trendino-story__img" src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" width="200" height="200" loading="lazy" decoding="async" />
									<?php else : ?>
										<img class="trendino-story__img is-placeholder" src="<?php echo esc_url( trendino_placeholder_image() ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" width="200" height="200" loading="lazy" decoding="async" />
									<?php endif; ?>
								</span>
								<?php if ( ! empty( $item['badge'] ) ) : ?>
									<span class="trendino-story__badge"><?php echo esc_html( $item['badge'] ); ?></span>
								<?php endif; ?>
							</span>

							<?php if ( 'yes' === $settings['show_title'] && $item['title'] ) : ?>
								<span class="trendino-story__title"><?php echo esc_html( $item['title'] ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Normalise items from the selected source.
	 *
	 * @param array $settings Settings.
	 * @return array<int,array{id:string,title:string,url:string,image_id:int,image_url:string,badge:string,external:bool}>
	 */
	protected function get_items( array $settings ) {
		$items = array();

		if ( 'manual' === $settings['source'] ) {
			foreach ( (array) ( $settings['items'] ?? array() ) as $item ) {
				if ( 'yes' !== ( $item['enabled'] ?? 'yes' ) ) {
					continue;
				}

				$items[] = array(
					'id'        => $item['_id'] ?? '',
					'title'     => $item['title'] ?? '',
					'url'       => $item['link']['url'] ?? '#',
					'image_id'  => (int) ( $item['image']['id'] ?? 0 ),
					'image_url' => $item['image']['url'] ?? '',
					'badge'     => $item['badge'] ?? '',
					'external'  => ! empty( $item['link']['is_external'] ),
				);
			}

			return $items;
		}

		if ( 'brands' === $settings['source'] ) {
			$terms = Query_Helper::brands( array( 'number' => (int) $settings['terms_limit'] ) );
		} else {
			$terms = ! empty( $settings['categories'] )
				? Query_Helper::product_categories( array( 'include' => $settings['categories'] ) )
				: Query_Helper::product_categories(
					array(
						'parent' => 0,
						'number' => (int) $settings['terms_limit'],
					)
				);
		}

		foreach ( $terms as $term ) {
			$thumbnail_id = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );

			$items[] = array(
				'id'        => 'term-' . $term->term_id,
				'title'     => $term->name,
				'url'       => get_term_link( $term ),
				'image_id'  => $thumbnail_id,
				'image_url' => '',
				'badge'     => '',
				'external'  => false,
			);
		}

		return $items;
	}
}
