<?php
/**
 * Quad boxes widget ("باکس سلیقه") — a row of panels, each showing a category
 * heading plus a 2×2 grid of its products.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Section_Header_Controls;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Quad_Boxes
 */
class Quad_Boxes extends Base_Widget {

	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'categories', 'section-head', 'carousel' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-quad-boxes';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Quad Boxes', 'trendino' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Panel categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-home' );
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'quad', 'box', 'category', 'products', 'grid', 'باکس' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_header',
			array( 'label' => esc_html__( 'Header', 'trendino' ) )
		);

		$this->register_section_header_controls(
			array(
				'title'     => esc_html__( 'Pick your taste', 'trendino' ),
				'link_text' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->end_controls_section();

		// --- Boxes -----------------------------------------------------.
		$this->start_controls_section(
			'section_boxes',
			array( 'label' => esc_html__( 'Boxes', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Smart watch', 'trendino' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Products From', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'A product category', 'trendino' ),
					'manual'   => esc_html__( 'Selected products', 'trendino' ),
					'sale'     => esc_html__( 'On sale', 'trendino' ),
					'featured' => esc_html__( 'Featured', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'category',
			array(
				'label'     => esc_html__( 'Category', 'trendino' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => Query_Helper::term_options( 'product_cat' ),
				'condition' => array( 'source' => 'category' ),
			)
		);

		$repeater->add_control(
			'products',
			array(
				'label'       => esc_html__( 'Products', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => Query_Helper::post_options( 'product' ),
				'label_block' => true,
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Box Link', 'trendino' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'description' => esc_html__( 'Leave empty to link to the selected category.', 'trendino' ),
			)
		);

		$this->add_control(
			'boxes',
			array(
				'label'       => esc_html__( 'Boxes', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => esc_html__( 'Smart watch', 'trendino' ) ),
					array( 'title' => esc_html__( 'Headphones', 'trendino' ) ),
					array( 'title' => esc_html__( 'Mobile', 'trendino' ) ),
					array( 'title' => esc_html__( 'Laptop', 'trendino' ) ),
				),
			)
		);

		$this->end_controls_section();

		// --- Layout ----------------------------------------------------.
		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Boxes Per Row', 'trendino' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => array(
					'{{WRAPPER}} .trendino-quad-boxes' => '--trendino-columns:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'items_per_box',
			array(
				'label'   => esc_html__( 'Products Per Box', 'trendino' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 2,
				'max'     => 6,
				'default' => 4,
			)
		);

		$this->add_control(
			'box_columns',
			array(
				'label'   => esc_html__( 'Product Grid Columns', 'trendino' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 3,
				'default' => 2,
				'selectors' => array(
					'{{WRAPPER}} .trendino-quad-box__grid' => 'grid-template-columns:repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-quad-boxes' => '--trendino-gap:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Box Title', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_arrow',
			array(
				'label'        => esc_html__( 'Show Arrow', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'        => esc_html__( 'Show Product Price', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// --- Style -----------------------------------------------------.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Box Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_background',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-quad-box' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-quad-box' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'box_title_typography',
				'selector' => '{{WRAPPER}} .trendino-quad-box__title',
			)
		);

		$this->add_control(
			'box_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-quad-box__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumb_background',
			array(
				'label'     => esc_html__( 'Thumbnail Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-quad-box__item' => 'background:{{VALUE}};',
				),
			)
		);

		$this->register_section_header_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Resolve the products for one box.
	 *
	 * @param array $box      Box settings.
	 * @param int   $quantity How many products.
	 * @return \WC_Product[]
	 */
	protected function get_box_products( array $box, $quantity ) {
		$args = array( 'limit' => $quantity );

		switch ( $box['source'] ?? 'category' ) {
			case 'manual':
				$args['include'] = array_map( 'absint', (array) ( $box['products'] ?? array() ) );
				$args['orderby'] = 'post__in';
				break;

			case 'sale':
				$args['on_sale'] = true;
				break;

			case 'featured':
				$args['featured'] = true;
				break;

			case 'category':
			default:
				if ( ! empty( $box['category'] ) ) {
					$term = get_term( absint( $box['category'] ), 'product_cat' );

					if ( $term && ! is_wp_error( $term ) ) {
						$args['category'] = array( $term->slug );
					}
				}
				break;
		}

		return Query_Helper::products( $args );
	}

	/**
	 * Resolve the box link (explicit URL, else the category archive).
	 *
	 * @param array $box Box settings.
	 * @return string
	 */
	protected function get_box_url( array $box ) {
		if ( ! empty( $box['link']['url'] ) ) {
			return $box['link']['url'];
		}

		if ( ! empty( $box['category'] ) ) {
			$link = get_term_link( absint( $box['category'] ), 'product_cat' );

			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}

		return '';
	}

	/**
	 * Render.
	 *
	 * @return void
	 */
	protected function render() {
		if ( ! $this->requires_woocommerce() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$boxes    = $settings['boxes'] ?? array();

		if ( empty( $boxes ) ) {
			$this->editor_notice( esc_html__( 'Add at least one box.', 'trendino' ) );

			return;
		}

		$quantity = max( 1, (int) $settings['items_per_box'] );
		?>
		<div class="trendino-quad-boxes-wrap">
			<?php $this->render_section_header( $settings ); ?>

			<div class="trendino-quad-boxes">
				<?php
				foreach ( $boxes as $box ) :
					$products = $this->get_box_products( $box, $quantity );
					$url      = $this->get_box_url( $box );
					?>
					<div class="trendino-quad-box elementor-repeater-item-<?php echo esc_attr( $box['_id'] ); ?>">
						<?php if ( 'yes' === $settings['show_title'] && ! empty( $box['title'] ) ) : ?>
							<div class="trendino-quad-box__head">
								<span class="trendino-quad-box__text">
									<h3 class="trendino-quad-box__title"><?php echo esc_html( $box['title'] ); ?></h3>
									<?php if ( ! empty( $box['subtitle'] ) ) : ?>
										<span class="trendino-quad-box__subtitle"><?php echo esc_html( $box['subtitle'] ); ?></span>
									<?php endif; ?>
								</span>

								<?php if ( 'yes' === $settings['show_arrow'] && $url ) : ?>
									<a class="trendino-quad-box__arrow" href="<?php echo esc_url( $url ); ?>"
										aria-label="<?php echo esc_attr( sprintf( /* translators: %s: box title */ __( 'View all %s', 'trendino' ), $box['title'] ) ); ?>">
										<?php trendino_icon( is_rtl() ? 'arrow-left' : 'arrow-right', array( 'size' => 16 ) ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( empty( $products ) ) : ?>
							<?php \Trendino\Elementor\Render_Helper::empty_state( esc_html__( 'No products in this box yet.', 'trendino' ) ); ?>
						<?php else : ?>
							<div class="trendino-quad-box__grid">
								<?php foreach ( $products as $product ) : ?>
									<a class="trendino-quad-box__item" href="<?php echo esc_url( $product->get_permalink() ); ?>"
										title="<?php echo esc_attr( $product->get_name() ); ?>">
										<?php
										$image_id = $product->get_image_id();

										if ( $image_id ) {
											echo wp_get_attachment_image(
												$image_id,
												'woocommerce_thumbnail',
												false,
												array(
													'class'    => 'trendino-quad-box__img',
													'alt'      => $product->get_name(),
													'loading'  => 'lazy',
													'decoding' => 'async',
												)
											);
										} else {
											printf(
												'<img class="trendino-quad-box__img is-placeholder" src="%1$s" alt="%2$s" width="160" height="160" loading="lazy" decoding="async" />',
												esc_url( trendino_placeholder_image() ),
												esc_attr( $product->get_name() )
											);
										}
										?>

										<?php if ( 'yes' === $settings['show_price'] ) : ?>
											<span class="trendino-quad-box__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
										<?php endif; ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
