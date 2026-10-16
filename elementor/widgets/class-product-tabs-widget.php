<?php
/**
 * Product tabs widget: each tab has its own independent WooCommerce query.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Product_Card_Controls;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Render_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Tabs
 */
class Product_Tabs extends Base_Widget {

	use Carousel_Controls;
	use Product_Card_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'product-card', 'tabs', 'carousel', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-product-tabs';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Product Tabs', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-tabs';
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
			'section_content',
			array( 'label' => esc_html__( 'Tabs', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Section Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Trendino picks', 'trendino' ),
			)
		);

		$this->add_control(
			'title_icon',
			array(
				'label' => esc_html__( 'Section Icon', 'trendino' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			array(
				'label'       => esc_html__( 'Tab Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Newest', 'trendino' ),
			)
		);

		$repeater->add_control(
			'tab_icon',
			array(
				'label' => esc_html__( 'Tab Icon', 'trendino' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Product Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => array(
					'recent'       => esc_html__( 'Recent Products', 'trendino' ),
					'featured'     => esc_html__( 'Featured Products', 'trendino' ),
					'sale'         => esc_html__( 'On Sale Products', 'trendino' ),
					'best_selling' => esc_html__( 'Best Selling Products', 'trendino' ),
					'top_rated'    => esc_html__( 'Top Rated Products', 'trendino' ),
					'manual'       => esc_html__( 'Specific Products', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'products',
			array(
				'label'       => esc_html__( 'Select Products', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::post_options( 'product', 150 ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$repeater->add_control(
			'categories',
			array(
				'label'       => esc_html__( 'Categories', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'product_cat' ),
				'condition'   => array( 'source!' => 'manual' ),
			)
		);

		$repeater->add_control(
			'limit',
			array(
				'label'   => esc_html__( 'Products Count', 'trendino' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 36,
				'default' => 10,
			)
		);

		$repeater->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Date', 'trendino' ),
					'title'      => esc_html__( 'Title', 'trendino' ),
					'price'      => esc_html__( 'Price', 'trendino' ),
					'popularity' => esc_html__( 'Popularity', 'trendino' ),
					'rating'     => esc_html__( 'Rating', 'trendino' ),
					'rand'       => esc_html__( 'Random', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'Descending', 'trendino' ),
					'ASC'  => esc_html__( 'Ascending', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'tab_link',
			array(
				'label' => esc_html__( 'Tab “View all” Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'tabs',
			array(
				'label'       => esc_html__( 'Tabs', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tab_title }}}',
				'default'     => array(
					array( 'tab_title' => esc_html__( 'Newest', 'trendino' ), 'source' => 'recent' ),
					array( 'tab_title' => esc_html__( 'Best selling', 'trendino' ), 'source' => 'best_selling' ),
					array( 'tab_title' => esc_html__( 'Most popular', 'trendino' ), 'source' => 'top_rated' ),
					array( 'tab_title' => esc_html__( 'On sale', 'trendino' ), 'source' => 'sale' ),
				),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'   => esc_html__( '“View all” Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'View all', 'trendino' ),
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
				'label'   => esc_html__( 'Products Display', 'trendino' ),
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
				'desktop' => 5,
				'tablet'  => 3,
				'mobile'  => 2.2,
				'max'     => 8,
				'selector'=> '{{WRAPPER}} .trendino-track, {{WRAPPER}} .trendino-grid',
			)
		);

		$this->register_gap_control( 12, '{{WRAPPER}} .trendino-track, {{WRAPPER}} .trendino-grid' );

		$this->add_control(
			'tabs_align',
			array(
				'label'     => esc_html__( 'Tabs Alignment', 'trendino' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'trendino' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'trendino' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'trendino' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__nav' => 'justify-content:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Carousel Arrows', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_mode' => 'carousel' ),
			)
		);

		$this->end_controls_section();

		$this->register_product_card_controls();

		$this->start_controls_section(
			'section_tabs_style',
			array(
				'label' => esc_html__( 'Tabs Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .trendino-tabs__button',
			)
		);

		$this->start_controls_tabs( 'tab_state' );

		$this->start_controls_tab( 'tab_state_normal', array( 'label' => esc_html__( 'Normal', 'trendino' ) ) );

		$this->add_control(
			'tab_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__button' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_state_active', array( 'label' => esc_html__( 'Active', 'trendino' ) ) );

		$this->add_control(
			'tab_active_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__button.is-active' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__button.is-active' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_indicator_color',
			array(
				'label'     => esc_html__( 'Indicator Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__button.is-active::after' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'tab_padding',
			array(
				'label'      => esc_html__( 'Tab Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-tabs__button' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tab_gap',
			array(
				'label'      => esc_html__( 'Tabs Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-tabs__nav' => 'gap:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'section_title_typography',
				'label'    => esc_html__( 'Section Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-tabs__title',
			)
		);

		$this->add_control(
			'section_title_color',
			array(
				'label'     => esc_html__( 'Section Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_product_card_style_controls();

		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => esc_html__( 'Section Box', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-tabs' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-tabs' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-tabs' => 'border-radius:{{SIZE}}{{UNIT}};',
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
		$tabs     = $settings['tabs'] ?? array();

		if ( empty( $tabs ) ) {
			$this->editor_notice( esc_html__( 'Add at least one tab.', 'trendino' ) );

			return;
		}

		$card_settings = $this->get_product_card_settings( $settings );
		$widget_id     = $this->get_id();
		$is_carousel   = 'carousel' === $settings['display_mode'];
		?>
		<div class="trendino-tabs trendino-section-box" data-trendino-tabs>
			<div class="trendino-tabs__head">
				<div class="trendino-tabs__title-wrap">
					<?php if ( ! empty( $settings['title_icon']['value'] ) ) : ?>
						<span class="trendino-tabs__icon"><?php \Elementor\Icons_Manager::render_icon( $settings['title_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
					<?php if ( $settings['title'] ) : ?>
						<h2 class="trendino-tabs__title"><?php echo esc_html( $settings['title'] ); ?></h2>
					<?php endif; ?>
				</div>

				<div class="trendino-tabs__nav" role="tablist">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<button type="button"
							class="trendino-tabs__button<?php echo 0 === $index ? ' is-active' : ''; ?>"
							role="tab"
							id="tab-<?php echo esc_attr( $widget_id . '-' . $index ); ?>"
							aria-controls="panel-<?php echo esc_attr( $widget_id . '-' . $index ); ?>"
							aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							data-tab-index="<?php echo esc_attr( $index ); ?>">
							<?php if ( ! empty( $tab['tab_icon']['value'] ) ) : ?>
								<?php \Elementor\Icons_Manager::render_icon( $tab['tab_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<?php endif; ?>
							<span><?php echo esc_html( $tab['tab_title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<?php if ( $is_carousel && 'yes' === $settings['arrows'] ) : ?>
					<div class="trendino-carousel__nav" data-carousel-nav>
						<button type="button" class="trendino-carousel__arrow" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'trendino' ); ?>">
							<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 18 ) ); ?>
						</button>
						<button type="button" class="trendino-carousel__arrow" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'trendino' ); ?>">
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 18 ) ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>

			<div class="trendino-tabs__panels">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<div class="trendino-tabs__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
						role="tabpanel"
						id="panel-<?php echo esc_attr( $widget_id . '-' . $index ); ?>"
						aria-labelledby="tab-<?php echo esc_attr( $widget_id . '-' . $index ); ?>"
						<?php echo 0 !== $index ? ' hidden' : ''; ?>>
						<?php $this->render_tab_products( $tab, $settings, $card_settings, $is_carousel ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the products of one tab.
	 *
	 * @param array $tab           Tab settings.
	 * @param array $settings      Widget settings.
	 * @param array $card_settings Card settings.
	 * @param bool  $is_carousel   Carousel or grid.
	 * @return void
	 */
	protected function render_tab_products( array $tab, array $settings, array $card_settings, $is_carousel ) {
		$query = Query_Helper::products(
			array(
				'source'     => $tab['source'] ?? 'recent',
				'categories' => $tab['categories'] ?? array(),
				'products'   => $tab['products'] ?? array(),
				'limit'      => $tab['limit'] ?? 10,
				'orderby'    => $tab['orderby'] ?? 'date',
				'order'      => $tab['order'] ?? 'DESC',
			)
		);

		if ( ! $query->have_posts() ) {
			Render_Helper::empty_state();

			return;
		}

		$container_class = $is_carousel ? 'trendino-track' : 'trendino-grid';
		$item_class      = $is_carousel ? 'trendino-track__item' : 'trendino-grid__item';
		?>
		<div class="trendino-carousel">
			<div class="<?php echo esc_attr( $container_class ); ?>" <?php echo $is_carousel ? 'data-trendino-scroller' : ''; ?>>
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$product = wc_get_product( get_the_ID() );

					if ( ! $product ) {
						continue;
					}
					?>
					<div class="<?php echo esc_attr( $item_class ); ?>">
						<?php Render_Helper::product_card( $product, $card_settings ); ?>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>

				<?php if ( ! empty( $tab['tab_link']['url'] ) ) : ?>
					<div class="<?php echo esc_attr( $item_class ); ?> is-cta">
						<a class="trendino-cta-card" href="<?php echo esc_url( $tab['tab_link']['url'] ); ?>">
							<span class="trendino-cta-card__icon"><?php trendino_icon( is_rtl() ? 'arrow-left' : 'arrow-right', array( 'size' => 26 ) ); ?></span>
							<span class="trendino-cta-card__text"><?php echo esc_html( $settings['link_text'] ); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
