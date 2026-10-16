<?php
/**
 * Product grid widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Product_Card_Controls;
use Trendino\Elementor\Controls\Query_Controls;
use Trendino\Elementor\Controls\Section_Header_Controls;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Render_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Grid
 */
class Product_Grid extends Base_Widget {

	use Query_Controls;
	use Carousel_Controls;
	use Product_Card_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'product-card', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-product-grid';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Product Grid', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
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
	 * WooCommerce required.
	 *
	 * @return bool
	 */
	protected function requires_woocommerce() {
		return true;
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
				'title'     => esc_html__( 'Latest products', 'trendino' ),
				'link_text' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->register_header_countdown_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array( 'label' => esc_html__( 'Query', 'trendino' ) )
		);

		$this->register_product_query_controls( array( 'default_limit' => 8 ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->register_columns_controls(
			array(
				'desktop' => 4,
				'tablet'  => 3,
				'mobile'  => 2,
				'max'     => 6,
				'selector'=> '{{WRAPPER}} .trendino-grid',
			)
		);

		$this->register_gap_control( 12, '{{WRAPPER}} .trendino-grid' );

		$this->add_control(
			'equal_height',
			array(
				'label'        => esc_html__( 'Equal Card Height', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->register_product_card_controls();
		$this->register_section_header_style_controls();
		$this->register_product_card_style_controls();

		$this->start_controls_section(
			'section_wrapper_style',
			array(
				'label' => esc_html__( 'Section Box', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wrapper_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-product-grid' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-product-grid' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'wrapper_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-product-grid' => 'border-radius:{{SIZE}}{{UNIT}};',
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
		if ( ! $this->can_render() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$query    = Query_Helper::products( $this->get_product_query_args( $settings ) );

		if ( ! $query->have_posts() ) {
			Render_Helper::empty_state();

			return;
		}

		$card_settings = $this->get_product_card_settings( $settings );
		?>
		<div class="trendino-product-grid trendino-section-box">
			<?php $this->render_section_header( $settings ); ?>

			<div class="trendino-grid<?php echo 'yes' === $settings['equal_height'] ? ' is-equal' : ''; ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$product = wc_get_product( get_the_ID() );

					if ( ! $product ) {
						continue;
					}
					?>
					<div class="trendino-grid__item">
						<?php Render_Helper::product_card( $product, $card_settings ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	}
}
