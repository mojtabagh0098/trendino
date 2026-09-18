<?php
/**
 * Product carousel widget.
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
 * Class Product_Carousel
 */
class Product_Carousel extends Base_Widget {

	use Query_Controls;
	use Carousel_Controls;
	use Product_Card_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'product-card', 'carousel', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-product-carousel';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Product Carousel', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products-carousel';
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
				'title'     => esc_html__( 'Best sellers', 'trendino' ),
				'link_text' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array( 'label' => esc_html__( 'Query', 'trendino' ) )
		);

		$this->register_product_query_controls( array( 'default_limit' => 12 ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->register_columns_controls(
			array(
				'desktop' => 5,
				'tablet'  => 3,
				'mobile'  => 2.2,
				'max'     => 8,
				'selector'=> '{{WRAPPER}} .trendino-track',
			)
		);

		$this->register_gap_control( 12 );

		$this->add_control(
			'first_slide_promo',
			array(
				'label'        => esc_html__( 'Promo Slide At Start', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Shows a banner card as the first item of the carousel.', 'trendino' ),
			)
		);

		$this->add_control(
			'promo_image',
			array(
				'label'     => esc_html__( 'Promo Image', 'trendino' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'first_slide_promo' => 'yes' ),
			)
		);

		$this->add_control(
			'promo_link',
			array(
				'label'     => esc_html__( 'Promo Link', 'trendino' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'first_slide_promo' => 'yes' ),
			)
		);

		$this->add_control(
			'last_slide_cta',
			array(
				'label'        => esc_html__( '“View all” Card At End', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel',
			array( 'label' => esc_html__( 'Carousel', 'trendino' ) )
		);

		$this->register_carousel_behaviour_controls();

		$this->add_control(
			'arrows_position',
			array(
				'label'     => esc_html__( 'Arrows Position', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'header',
				'options'   => array(
					'header'  => esc_html__( 'In Section Header', 'trendino' ),
					'sides'   => esc_html__( 'Floating On Sides', 'trendino' ),
				),
				'condition' => array( 'arrows' => 'yes' ),
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
					'{{WRAPPER}} .trendino-product-carousel' => 'background-color:{{VALUE}};',
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
					'{{WRAPPER}} .trendino-product-carousel' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .trendino-product-carousel' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
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
		if ( ! $this->can_render() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$query    = Query_Helper::products( $this->get_product_query_args( $settings ) );

		if ( ! $query->have_posts() ) {
			Render_Helper::empty_state();

			return;
		}

		$card_settings   = $this->get_product_card_settings( $settings );
		$arrows_in_head  = 'yes' === $settings['arrows'] && 'header' === $settings['arrows_position'];
		$arrows_on_sides = 'yes' === $settings['arrows'] && 'sides' === $settings['arrows_position'];
		?>
		<div class="trendino-product-carousel trendino-section-box">
			<?php $this->render_section_header( $settings, $arrows_in_head ); ?>

			<div class="trendino-carousel" <?php echo Render_Helper::carousel_attributes( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="trendino-track" data-trendino-scroller>
					<?php if ( 'yes' === $settings['first_slide_promo'] && ! empty( $settings['promo_image']['url'] ) ) : ?>
						<div class="trendino-track__item is-promo">
							<a class="trendino-promo-card" href="<?php echo esc_url( $settings['promo_link']['url'] ?? '#' ); ?>">
								<img src="<?php echo esc_url( $settings['promo_image']['url'] ); ?>" alt="" loading="lazy" />
							</a>
						</div>
					<?php endif; ?>

					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$product = wc_get_product( get_the_ID() );

						if ( ! $product ) {
							continue;
						}
						?>
						<div class="trendino-track__item">
							<?php Render_Helper::product_card( $product, $card_settings ); ?>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>

					<?php if ( 'yes' === $settings['last_slide_cta'] && ! empty( $settings['link']['url'] ) ) : ?>
						<div class="trendino-track__item is-cta">
							<a class="trendino-cta-card" href="<?php echo esc_url( $settings['link']['url'] ); ?>">
								<span class="trendino-cta-card__icon"><?php trendino_icon( is_rtl() ? 'arrow-left' : 'arrow-right', array( 'size' => 26 ) ); ?></span>
								<span class="trendino-cta-card__text"><?php echo esc_html( $settings['link_text'] ? $settings['link_text'] : __( 'View all', 'trendino' ) ); ?></span>
							</a>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $arrows_on_sides ) : ?>
					<button type="button" class="trendino-carousel__arrow is-prev is-floating" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 20 ) ); ?>
					</button>
					<button type="button" class="trendino-carousel__arrow is-next is-floating" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 20 ) ); ?>
					</button>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['dots'] ) : ?>
					<div class="trendino-carousel__dots" data-carousel-dots></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
