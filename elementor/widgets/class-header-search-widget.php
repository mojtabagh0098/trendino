<?php
/**
 * Header product search widget (with live AJAX suggestions).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Search
 */
class Header_Search extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'header' );

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-header-search';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Search', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-site-search';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-header' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Search', 'trendino' ) )
		);

		$this->add_control(
			'placeholder',
			array(
				'label'       => esc_html__( 'Placeholder', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Search products, brands and categories…', 'trendino' ),
			)
		);

		$this->add_control(
			'search_scope',
			array(
				'label'   => esc_html__( 'Search Scope', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => trendino_is_woocommerce_active() ? 'product' : 'all',
				'options' => array(
					'product' => esc_html__( 'Products Only', 'trendino' ),
					'all'     => esc_html__( 'All Content', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'show_category_filter',
			array(
				'label'        => esc_html__( 'Category Dropdown', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'live_search',
			array(
				'label'        => esc_html__( 'Live Suggestions (AJAX)', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label'   => esc_html__( 'Submit Button', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon' => esc_html__( 'Icon Only', 'trendino' ),
					'text' => esc_html__( 'Text', 'trendino' ),
					'both' => esc_html__( 'Icon + Text', 'trendino' ),
					'none' => esc_html__( 'Hidden', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'Button Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Search', 'trendino' ),
				'condition' => array( 'button_style' => array( 'text', 'both' ) ),
			)
		);

		$this->add_control(
			'trending_terms',
			array(
				'label'       => esc_html__( 'Trending Keywords', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Comma separated. Shown under the field on focus.', 'trendino' ),
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
			'field_height',
			array(
				'label'      => esc_html__( 'Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 72,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-search' => '--trendino-search-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-search__form' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'field_border',
				'selector' => '{{WRAPPER}} .trendino-search__form',
			)
		);

		$this->add_responsive_control(
			'field_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-search__form' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'selector' => '{{WRAPPER}} .trendino-search__input',
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => esc_html__( 'Placeholder Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-search__input::placeholder' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-search__submit' => 'color:{{VALUE}};',
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
		$scope    = 'product' === $settings['search_scope'] && trendino_is_woocommerce_active() ? 'product' : 'all';
		$trending = array_filter( array_map( 'trim', explode( ',', (string) $settings['trending_terms'] ) ) );
		?>
		<div class="trendino-search" data-live-search="<?php echo esc_attr( $settings['live_search'] ); ?>" data-scope="<?php echo esc_attr( $scope ); ?>">
			<form role="search" method="get" class="trendino-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( 'product' === $scope ) : ?>
					<input type="hidden" name="post_type" value="product" />
				<?php endif; ?>

				<span class="trendino-search__icon"><?php trendino_icon( 'search', array( 'size' => 20 ) ); ?></span>

				<?php if ( 'yes' === $settings['show_category_filter'] && trendino_is_woocommerce_active() ) : ?>
					<select class="trendino-search__category" name="product_cat" aria-label="<?php esc_attr_e( 'Search category', 'trendino' ); ?>">
						<option value=""><?php esc_html_e( 'All categories', 'trendino' ); ?></option>
						<?php foreach ( Query_Helper::product_categories( array( 'parent' => 0, 'number' => 30 ) ) as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>

				<input
					type="search"
					class="trendino-search__input"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
					autocomplete="off"
					aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>"
				/>

				<?php if ( 'none' !== $settings['button_style'] ) : ?>
					<button type="submit" class="trendino-search__submit is-<?php echo esc_attr( $settings['button_style'] ); ?>">
						<?php if ( in_array( $settings['button_style'], array( 'icon', 'both' ), true ) ) : ?>
							<?php trendino_icon( 'search', array( 'size' => 20 ) ); ?>
						<?php endif; ?>
						<?php if ( in_array( $settings['button_style'], array( 'text', 'both' ), true ) ) : ?>
							<span><?php echo esc_html( $settings['button_text'] ); ?></span>
						<?php endif; ?>
					</button>
				<?php endif; ?>
			</form>

			<div class="trendino-search__panel" hidden>
				<?php if ( $trending ) : ?>
					<div class="trendino-search__trending">
						<span class="trendino-search__trending-label"><?php esc_html_e( 'Trending searches', 'trendino' ); ?></span>
						<ul>
							<?php foreach ( $trending as $term ) : ?>
								<li>
									<a href="<?php echo esc_url( add_query_arg( array( 's' => rawurlencode( $term ), 'post_type' => 'product' ), home_url( '/' ) ) ); ?>">
										<?php trendino_icon( 'search', array( 'size' => 14 ) ); ?>
										<span><?php echo esc_html( $term ); ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<div class="trendino-search__results" aria-live="polite"></div>
			</div>
		</div>
		<?php
	}
}
