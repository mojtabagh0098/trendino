<?php
/**
 * Deal of the day / flash sale widget: countdown panel + product carousel.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Product_Card_Controls;
use Trendino\Elementor\Controls\Query_Controls;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Render_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Deal_Countdown
 */
class Deal_Countdown extends Base_Widget {

	use Query_Controls;
	use Carousel_Controls;
	use Product_Card_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'product-card', 'carousel', 'countdown' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-deal-countdown';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Deals & Countdown', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
			array( 'label' => esc_html__( 'Panel', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Amazing offers', 'trendino' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Special discounts, limited time', 'trendino' ),
			)
		);

		$this->add_control(
			'panel_image',
			array(
				'label' => esc_html__( 'Panel Image', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'show_countdown',
			array(
				'label'        => esc_html__( 'Show Countdown', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'countdown_type',
			array(
				'label'     => esc_html__( 'Countdown Type', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'daily',
				'options'   => array(
					'daily' => esc_html__( 'Resets Daily (midnight)', 'trendino' ),
					'fixed' => esc_html__( 'Fixed Date', 'trendino' ),
				),
				'condition' => array( 'show_countdown' => 'yes' ),
			)
		);

		$this->add_control(
			'countdown_date',
			array(
				'label'     => esc_html__( 'End Date', 'trendino' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => gmdate( 'Y-m-d H:i', strtotime( '+7 days' ) ),
				'condition' => array(
					'show_countdown' => 'yes',
					'countdown_type' => 'fixed',
				),
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => esc_html__( 'Show Unit Labels', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_countdown' => 'yes' ),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'   => esc_html__( 'Button Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'View all', 'trendino' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'Button Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array( 'label' => esc_html__( 'Query', 'trendino' ) )
		);

		$this->register_product_query_controls( array( 'default_limit' => 10 ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->register_columns_controls(
			array(
				'desktop' => 4,
				'tablet'  => 3,
				'mobile'  => 2.1,
				'max'     => 8,
				'selector'=> '{{WRAPPER}} .trendino-track',
			)
		);

		$this->register_gap_control( 12 );

		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => esc_html__( 'Panel Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 140, 'max' => 420 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 210 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-deals' => '--trendino-panel-width:{{SIZE}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .trendino-deals',
			)
		);

		$this->add_control(
			'panel_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-deals' => 'border-radius:{{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .trendino-deals' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'deal_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-deals__title',
			)
		);

		$this->add_control(
			'deal_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .trendino-deals__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'deal_subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-deals__subtitle' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'countdown_bg',
			array(
				'label'     => esc_html__( 'Countdown Box Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-countdown__unit' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'show_countdown' => 'yes' ),
			)
		);

		$this->add_control(
			'countdown_color',
			array(
				'label'     => esc_html__( 'Countdown Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-countdown' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_countdown' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'countdown_typography',
				'label'     => esc_html__( 'Countdown Typography', 'trendino' ),
				'selector'  => '{{WRAPPER}} .trendino-countdown__value',
				'condition' => array( 'show_countdown' => 'yes' ),
			)
		);

		$this->add_control(
			'deal_button_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-deals__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'deal_button_bg',
			array(
				'label'     => esc_html__( 'Button Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-deals__button' => 'background-color:{{VALUE}};',
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
		$args     = $this->get_product_query_args( $settings );

		// Deals default to on-sale products.
		if ( 'recent' === $args['source'] ) {
			$args['source'] = 'sale';
		}

		$query = Query_Helper::products( $args );

		if ( ! $query->have_posts() ) {
			Render_Helper::empty_state( esc_html__( 'No discounted products found.', 'trendino' ) );

			return;
		}

		$card_settings = $this->get_product_card_settings( $settings );
		$end_time      = 'fixed' === $settings['countdown_type'] && ! empty( $settings['countdown_date'] )
			? strtotime( $settings['countdown_date'] )
			: strtotime( 'tomorrow midnight', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		?>
		<div class="trendino-deals">
			<div class="trendino-deals__panel">
				<?php if ( $settings['title'] ) : ?>
					<h2 class="trendino-deals__title"><?php echo esc_html( $settings['title'] ); ?></h2>
				<?php endif; ?>

				<?php if ( $settings['subtitle'] ) : ?>
					<p class="trendino-deals__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_countdown'] ) : ?>
					<div class="trendino-countdown" data-trendino-countdown="<?php echo esc_attr( $end_time ); ?>"
						data-daily="<?php echo esc_attr( 'daily' === $settings['countdown_type'] ? '1' : '0' ); ?>">
						<?php
						$units = array(
							'hours'   => esc_html__( 'hours', 'trendino' ),
							'minutes' => esc_html__( 'minutes', 'trendino' ),
							'seconds' => esc_html__( 'seconds', 'trendino' ),
						);

						if ( 'fixed' === $settings['countdown_type'] ) {
							$units = array( 'days' => esc_html__( 'days', 'trendino' ) ) + $units;
						}

						foreach ( $units as $unit => $label ) :
							?>
							<span class="trendino-countdown__unit">
								<span class="trendino-countdown__value" data-unit="<?php echo esc_attr( $unit ); ?>">۰۰</span>
								<?php if ( 'yes' === $settings['show_labels'] ) : ?>
									<span class="trendino-countdown__label"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
							</span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['panel_image']['url'] ) ) : ?>
					<img class="trendino-deals__image" src="<?php echo esc_url( $settings['panel_image']['url'] ); ?>" alt="" loading="lazy" decoding="async" />
				<?php endif; ?>

				<?php if ( $settings['link_text'] && ! empty( $settings['link']['url'] ) ) : ?>
					<a class="trendino-deals__button" href="<?php echo esc_url( $settings['link']['url'] ); ?>">
						<span><?php echo esc_html( $settings['link_text'] ); ?></span>
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 16 ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="trendino-deals__products trendino-carousel" <?php echo Render_Helper::carousel_attributes( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="trendino-track" data-trendino-scroller>
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
				</div>

				<?php if ( 'yes' === $settings['arrows'] ) : ?>
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
