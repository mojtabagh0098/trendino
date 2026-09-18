<?php
/**
 * Service features / trust badges widget (delivery, warranty, returns, support…).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Controls\Carousel_Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Service_Features
 */
class Service_Features extends Base_Widget {

	use Carousel_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'services' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-service-features';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Service Features', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-content', 'trendino-footer' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Features', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Icon Type', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'preset',
				'options' => array(
					'preset' => esc_html__( 'Theme Icon', 'trendino' ),
					'custom' => esc_html__( 'Custom Icon', 'trendino' ),
					'image'  => esc_html__( 'Image', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'preset_icon',
			array(
				'label'     => esc_html__( 'Icon', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'truck',
				'options'   => array(
					'truck'    => esc_html__( 'Delivery', 'trendino' ),
					'shield'   => esc_html__( 'Warranty', 'trendino' ),
					'refresh'  => esc_html__( 'Returns', 'trendino' ),
					'headset'  => esc_html__( 'Support', 'trendino' ),
					'wallet'   => esc_html__( 'Payment', 'trendino' ),
					'gift'     => esc_html__( 'Gift', 'trendino' ),
					'percent'  => esc_html__( 'Discount', 'trendino' ),
					'check'    => esc_html__( 'Original', 'trendino' ),
				),
				'condition' => array( 'icon_type' => 'preset' ),
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label'     => esc_html__( 'Custom Icon', 'trendino' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'icon_type' => 'custom' ),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Image', 'trendino' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'icon_type' => 'image' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Express delivery', 'trendino' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => esc_html__( 'Description', 'trendino' ),
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

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'preset_icon' => 'truck', 'title' => esc_html__( 'Express delivery', 'trendino' ) ),
					array( 'preset_icon' => 'wallet', 'title' => esc_html__( 'Cash on delivery', 'trendino' ) ),
					array( 'preset_icon' => 'refresh', 'title' => esc_html__( '7-day return', 'trendino' ) ),
					array( 'preset_icon' => 'check', 'title' => esc_html__( 'Original products', 'trendino' ) ),
					array( 'preset_icon' => 'headset', 'title' => esc_html__( '24/7 support', 'trendino' ) ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_control(
			'item_layout',
			array(
				'label'   => esc_html__( 'Item Layout', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stacked',
				'options' => array(
					'stacked'    => esc_html__( 'Icon Above Text', 'trendino' ),
					'inline'     => esc_html__( 'Icon Beside Text', 'trendino' ),
				),
			)
		);

		$this->register_columns_controls(
			array(
				'desktop' => 5,
				'tablet'  => 3,
				'mobile'  => 2,
				'max'     => 8,
				'selector'=> '{{WRAPPER}} .trendino-services',
			)
		);

		$this->register_gap_control( 12, '{{WRAPPER}} .trendino-services' );

		$this->add_control(
			'show_dividers',
			array(
				'label'        => esc_html__( 'Dividers Between Items', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
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

		$this->add_control(
			'wrapper_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-services' => 'background-color:{{VALUE}};',
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
					'{{WRAPPER}} .trendino-services' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'wrapper_border',
				'selector' => '{{WRAPPER}} .trendino-services',
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
					'{{WRAPPER}} .trendino-services' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 90 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 42 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-service__icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .trendino-service__icon img' => 'width:{{SIZE}}{{UNIT}};height:auto;',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-service__icon' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-service__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-service__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Description Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-service__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Description Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-service__desc' => 'color:{{VALUE}};',
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
		$items    = $settings['items'] ?? array();

		if ( empty( $items ) ) {
			return;
		}

		$classes = array(
			'trendino-services',
			'layout-' . sanitize_html_class( $settings['item_layout'] ),
		);

		if ( 'yes' === $settings['show_dividers'] ) {
			$classes[] = 'has-dividers';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php $has_link = ! empty( $item['link']['url'] ); ?>
				<<?php echo $has_link ? 'a' : 'div'; ?> class="trendino-service"
					<?php if ( $has_link ) : ?>
						href="<?php echo esc_url( $item['link']['url'] ); ?>"
						<?php echo ! empty( $item['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
					<?php endif; ?>>
					<span class="trendino-service__icon">
						<?php
						if ( 'custom' === $item['icon_type'] && ! empty( $item['custom_icon']['value'] ) ) {
							\Elementor\Icons_Manager::render_icon( $item['custom_icon'], array( 'aria-hidden' => 'true' ) );
						} elseif ( 'image' === $item['icon_type'] && ! empty( $item['image']['url'] ) ) {
							printf(
								'<img src="%s" alt="%s" loading="lazy" decoding="async" />',
								esc_url( $item['image']['url'] ),
								esc_attr( $item['title'] )
							);
						} else {
							trendino_icon( $item['preset_icon'], array( 'size' => 42 ) );
						}
						?>
					</span>

					<span class="trendino-service__text">
						<?php if ( $item['title'] ) : ?>
							<span class="trendino-service__title"><?php echo esc_html( $item['title'] ); ?></span>
						<?php endif; ?>
						<?php if ( $item['description'] ) : ?>
							<span class="trendino-service__desc"><?php echo esc_html( $item['description'] ); ?></span>
						<?php endif; ?>
					</span>
				</<?php echo $has_link ? 'a' : 'div'; ?>>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
