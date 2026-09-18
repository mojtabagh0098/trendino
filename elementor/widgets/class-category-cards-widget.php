<?php
/**
 * Category cards widget (image + title grid / carousel of product categories).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Section_Header_Controls;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category_Cards
 */
class Category_Cards extends Base_Widget {

	use Carousel_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'categories', 'carousel', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-category-cards';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Category Cards', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-categories';
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
				'title'     => esc_html__( 'Shop by category', 'trendino' ),
				'link_text' => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Categories', 'trendino' ) )
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Category Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'product_cat',
				'options' => array(
					'product_cat' => esc_html__( 'WooCommerce Categories', 'trendino' ),
					'manual'      => esc_html__( 'Manual Items', 'trendino' ),
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
				'description' => esc_html__( 'Leave empty to show top level categories.', 'trendino' ),
				'condition'   => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Categories Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 30,
				'default'   => 8,
				'condition' => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'Order By', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'name',
				'options'   => array(
					'name'  => esc_html__( 'Name', 'trendino' ),
					'count' => esc_html__( 'Product Count', 'trendino' ),
					'term_order' => esc_html__( 'Custom Order', 'trendino' ),
					'slug'  => esc_html__( 'Slug', 'trendino' ),
				),
				'condition' => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label'        => esc_html__( 'Hide Empty Categories', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'source' => 'product_cat' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Category', 'trendino' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'trendino' ),
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
			'bg_color',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'condition'   => array( 'source' => 'manual' ),
				'default'     => array( array(), array(), array(), array() ),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'Show Product Count', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'show_children',
			array(
				'label'        => esc_html__( 'Show Sub Category Links', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'children_limit',
			array(
				'label'     => esc_html__( 'Sub Categories Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'default'   => 4,
				'condition' => array( 'show_children' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_control(
			'card_style',
			array(
				'label'   => esc_html__( 'Card Style', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'boxed',
				'options' => array(
					'boxed'   => esc_html__( 'Boxed (image + title)', 'trendino' ),
					'overlay' => esc_html__( 'Overlay Title', 'trendino' ),
					'clean'   => esc_html__( 'Clean (no frame)', 'trendino' ),
					'tile'    => esc_html__( 'Tile With Sub Links', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => esc_html__( 'Display Mode', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'     => esc_html__( 'Grid', 'trendino' ),
					'carousel' => esc_html__( 'Carousel', 'trendino' ),
				),
			)
		);

		$this->register_columns_controls(
			array(
				'desktop' => 6,
				'tablet'  => 4,
				'mobile'  => 3,
				'max'     => 10,
				'selector'=> '{{WRAPPER}} .trendino-category-cards',
			)
		);

		$this->register_gap_control( 16, '{{WRAPPER}} .trendino-category-cards' );

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
				'label' => esc_html__( 'Card Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => esc_html__( 'Card Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-category-card' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .trendino-category-card',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-category-card' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_radius',
			array(
				'label'      => esc_html__( 'Image Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-category-card__media' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .trendino-category-card',
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-category-card' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-category-card__title',
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-category-card__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_title_hover_color',
			array(
				'label'     => esc_html__( 'Title Hover Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-category-card:hover .trendino-category-card__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => esc_html__( 'Hover Effect', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lift',
				'options' => array(
					'none' => esc_html__( 'None', 'trendino' ),
					'lift' => esc_html__( 'Lift', 'trendino' ),
					'zoom' => esc_html__( 'Zoom Image', 'trendino' ),
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
			$this->editor_notice( esc_html__( 'No categories found for this widget.', 'trendino' ) );

			return;
		}

		$is_carousel = 'carousel' === $settings['display_mode'];

		$classes = array(
			'trendino-category-cards',
			'style-' . sanitize_html_class( $settings['card_style'] ),
			'hover-' . sanitize_html_class( $settings['hover_effect'] ),
			'trendino-ratio-' . sanitize_html_class( $settings['image_ratio'] ),
			$is_carousel ? 'is-carousel' : 'is-grid',
		);
		?>
		<div class="trendino-category-cards-wrap trendino-section-box">
			<?php $this->render_section_header( $settings, $is_carousel && 'yes' === $settings['arrows'] ); ?>

			<div class="trendino-carousel">
				<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php echo $is_carousel ? 'data-trendino-scroller' : ''; ?>>
					<?php foreach ( $items as $item ) : ?>
						<a class="trendino-category-card <?php echo esc_attr( $item['class'] ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
							<span class="trendino-category-card__media">
								<?php if ( $item['image_html'] ) : ?>
									<?php echo $item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php else : ?>
									<img class="trendino-category-card__img is-placeholder" src="<?php echo esc_url( trendino_placeholder_image() ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" width="200" height="200" loading="lazy" decoding="async" />
								<?php endif; ?>
							</span>

							<span class="trendino-category-card__body">
								<span class="trendino-category-card__title"><?php echo esc_html( $item['title'] ); ?></span>

								<?php if ( $item['subtitle'] ) : ?>
									<span class="trendino-category-card__subtitle"><?php echo esc_html( $item['subtitle'] ); ?></span>
								<?php endif; ?>

								<?php if ( ! empty( $item['children'] ) ) : ?>
									<span class="trendino-category-card__children">
										<?php foreach ( $item['children'] as $child ) : ?>
											<span class="trendino-category-card__child"><?php echo esc_html( $child ); ?></span>
										<?php endforeach; ?>
									</span>
								<?php endif; ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Build the normalised item list.
	 *
	 * @param array $settings Settings.
	 * @return array<int,array{title:string,subtitle:string,url:string,image_html:string,children:array,class:string}>
	 */
	protected function get_items( array $settings ) {
		$items = array();

		if ( 'manual' === $settings['source'] ) {
			foreach ( (array) ( $settings['items'] ?? array() ) as $item ) {
				$image_html = '';

				if ( ! empty( $item['image']['id'] ) ) {
					$image_html = wp_get_attachment_image(
						(int) $item['image']['id'],
						'trendino-product-card',
						false,
						array( 'class' => 'trendino-category-card__img', 'alt' => $item['title'], 'loading' => 'lazy' )
					);
				} elseif ( ! empty( $item['image']['url'] ) ) {
					$image_html = sprintf(
						'<img class="trendino-category-card__img" src="%s" alt="%s" loading="lazy" decoding="async" />',
						esc_url( $item['image']['url'] ),
						esc_attr( $item['title'] )
					);
				}

				$items[] = array(
					'title'      => $item['title'],
					'subtitle'   => $item['subtitle'],
					'url'        => $item['link']['url'] ?? '#',
					'image_html' => $image_html,
					'children'   => array(),
					'class'      => 'elementor-repeater-item-' . $item['_id'],
				);
			}

			return $items;
		}

		if ( ! trendino_is_woocommerce_active() ) {
			return array();
		}

		$terms = ! empty( $settings['categories'] )
			? Query_Helper::product_categories( array( 'include' => $settings['categories'] ) )
			: Query_Helper::product_categories(
				array(
					'parent'     => 0,
					'number'     => (int) $settings['limit'],
					'orderby'    => $settings['orderby'],
					'hide_empty' => 'yes' === $settings['hide_empty'],
				)
			);

		foreach ( $terms as $term ) {
			$children = array();

			if ( 'yes' === $settings['show_children'] ) {
				$child_terms = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $term->term_id,
						'hide_empty' => false,
						'number'     => (int) $settings['children_limit'],
					)
				);

				if ( ! is_wp_error( $child_terms ) ) {
					$children = wp_list_pluck( $child_terms, 'name' );
				}
			}

			$items[] = array(
				'title'      => $term->name,
				'subtitle'   => 'yes' === $settings['show_count']
					? sprintf( /* translators: %s: product count */ esc_html__( '%s products', 'trendino' ), trendino_number( $term->count ) )
					: '',
				'url'        => get_term_link( $term ),
				'image_html' => trendino_category_thumbnail_html( $term, 'trendino-product-card', 'trendino-category-card__img' ),
				'children'   => $children,
				'class'      => 'term-' . $term->term_id,
			);
		}

		return $items;
	}
}
