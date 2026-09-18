<?php
/**
 * Category showcase widget: a banner/links panel plus that category's products.
 * Mirrors the "دسته‌بندی + محصولات" blocks of the reference homepage.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Product_Card_Controls;
use Trendino\Elementor\Controls\Query_Controls;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Render_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category_Showcase
 */
class Category_Showcase extends Base_Widget {

	use Query_Controls;
	use Carousel_Controls;
	use Product_Card_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'product-card', 'carousel', 'categories', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-category-showcase';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Category Showcase', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-banner';
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
			'section_panel',
			array( 'label' => esc_html__( 'Panel', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Digital goods', 'trendino' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->add_control(
			'panel_image',
			array(
				'label'   => esc_html__( 'Panel Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'panel_position',
			array(
				'label'   => esc_html__( 'Panel Position', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'start',
				'options' => array(
					'start' => esc_html__( 'Start Side', 'trendino' ),
					'end'   => esc_html__( 'End Side', 'trendino' ),
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Link Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Sub category', 'trendino' ),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label' => esc_html__( 'URL', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => esc_html__( 'Quick Links', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array( array(), array(), array() ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label' => esc_html__( 'Button Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array( 'label' => esc_html__( 'Products', 'trendino' ) )
		);

		$this->register_product_query_controls( array( 'default_limit' => 8 ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_control(
			'products_display',
			array(
				'label'   => esc_html__( 'Products Display', 'trendino' ),
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
				'desktop' => 4,
				'tablet'  => 3,
				'mobile'  => 2,
				'max'     => 6,
				'selector'=> '{{WRAPPER}} .trendino-track, {{WRAPPER}} .trendino-grid',
			)
		);

		$this->register_gap_control( 12, '{{WRAPPER}} .trendino-track, {{WRAPPER}} .trendino-grid' );

		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => esc_html__( 'Panel Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 480 ),
					'%'  => array( 'min' => 15, 'max' => 45 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 240 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-showcase' => '--trendino-panel-width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_product_card_controls();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Panel Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'panel_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trendino-showcase__panel',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'panel_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-showcase__title',
			)
		);

		$this->add_control(
			'panel_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-showcase__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-showcase__link' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-showcase__panel' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-showcase__panel' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'panel_button_bg',
			array(
				'label'     => esc_html__( 'Button Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-showcase__button' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_button_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-showcase__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->register_product_card_style_controls();
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
		$is_carousel   = 'carousel' === $settings['products_display'];
		$container     = $is_carousel ? 'trendino-track' : 'trendino-grid';
		$item_class    = $is_carousel ? 'trendino-track__item' : 'trendino-grid__item';
		?>
		<div class="trendino-showcase panel-<?php echo esc_attr( $settings['panel_position'] ); ?>">
			<aside class="trendino-showcase__panel">
				<?php if ( $settings['title'] ) : ?>
					<h2 class="trendino-showcase__title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>

				<?php if ( $settings['subtitle'] ) : ?>
					<p class="trendino-showcase__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['panel_image']['url'] ) ) : ?>
					<img class="trendino-showcase__image" src="<?php echo esc_url( $settings['panel_image']['url'] ); ?>" alt="<?php echo esc_attr( $settings['title'] ); ?>" loading="lazy" decoding="async" />
				<?php endif; ?>

				<?php if ( ! empty( $settings['links'] ) ) : ?>
					<ul class="trendino-showcase__links">
						<?php foreach ( $settings['links'] as $link ) : ?>
							<?php if ( empty( $link['text'] ) ) { continue; } ?>
							<li>
								<a class="trendino-showcase__link" href="<?php echo esc_url( $link['url']['url'] ?? '#' ); ?>">
									<span><?php echo esc_html( $link['text'] ); ?></span>
									<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $settings['button_text'] && ! empty( $settings['button_link']['url'] ) ) : ?>
					<a class="trendino-showcase__button" href="<?php echo esc_url( $settings['button_link']['url'] ); ?>">
						<span><?php echo esc_html( $settings['button_text'] ); ?></span>
						<?php trendino_icon( is_rtl() ? 'arrow-left' : 'arrow-right', array( 'size' => 16 ) ); ?>
					</a>
				<?php endif; ?>
			</aside>

			<div class="trendino-showcase__products trendino-carousel">
				<div class="<?php echo esc_attr( $container ); ?>" <?php echo $is_carousel ? 'data-trendino-scroller' : ''; ?>>
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
				</div>

				<?php if ( $is_carousel ) : ?>
					<button type="button" class="trendino-carousel__arrow is-prev is-floating" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 20 ) ); ?>
					</button>
					<button type="button" class="trendino-carousel__arrow is-next is-floating" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'trendino' ); ?>">
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 20 ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
