<?php
/**
 * Header account / cart / wishlist / compare icon cluster.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Actions
 */
class Header_Actions extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'header' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-header-actions';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Header Actions', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-cart-medium';
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
			'section_items',
			array( 'label' => esc_html__( 'Actions', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_type',
			array(
				'label'   => esc_html__( 'Type', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'account',
				'options' => array(
					'account'  => esc_html__( 'Account / Login', 'trendino' ),
					'cart'     => esc_html__( 'Cart', 'trendino' ),
					'wishlist' => esc_html__( 'Wishlist', 'trendino' ),
					'compare'  => esc_html__( 'Compare', 'trendino' ),
					'orders'   => esc_html__( 'Orders', 'trendino' ),
					'custom'   => esc_html__( 'Custom Link', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
			)
		);

		$repeater->add_control(
			'show_label',
			array(
				'label'        => esc_html__( 'Show Label', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label' => esc_html__( 'Custom Icon', 'trendino' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'custom_url',
			array(
				'label'     => esc_html__( 'URL', 'trendino' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'item_type' => 'custom' ),
			)
		);

		$repeater->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'Show Counter Bubble', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'item_type' => array( 'cart', 'wishlist', 'compare' ) ),
			)
		);

		$repeater->add_control(
			'style_variant',
			array(
				'label'   => esc_html__( 'Style', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'    => esc_html__( 'Icon', 'trendino' ),
					'outline' => esc_html__( 'Outlined Button', 'trendino' ),
					'filled'  => esc_html__( 'Filled Button', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ item_type }}}',
				'default'     => array(
					array(
						'item_type'     => 'account',
						'label'         => esc_html__( 'Login | Register', 'trendino' ),
						'show_label'    => 'yes',
						'style_variant' => 'outline',
					),
					array(
						'item_type'     => 'cart',
						'label'         => esc_html__( 'Cart', 'trendino' ),
						'show_count'    => 'yes',
						'style_variant' => 'icon',
					),
				),
			)
		);

		$this->add_control(
			'separator_between',
			array(
				'label'        => esc_html__( 'Divider Between Items', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'cart_dropdown',
			array(
				'label'        => esc_html__( 'Cart Mini Dropdown', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'items_gap',
			array(
				'label'      => esc_html__( 'Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-header-actions' => 'gap:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-header-action' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-header-action svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .trendino-header-action__label',
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => esc_html__( 'Counter Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-header-action__count' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => esc_html__( 'Counter Text', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-header-action__count' => 'color:{{VALUE}};',
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

		$classes = array( 'trendino-header-actions' );

		if ( 'yes' === $settings['separator_between'] ) {
			$classes[] = 'has-dividers';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php $this->render_item( $item, $settings ); ?>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render a single action.
	 *
	 * @param array $item     Repeater item.
	 * @param array $settings Widget settings.
	 * @return void
	 */
	protected function render_item( array $item, array $settings ) {
		$type  = $item['item_type'];
		$data  = $this->get_item_data( $type );
		$label = $item['label'] ? $item['label'] : $data['label'];

		$classes = array(
			'trendino-header-action',
			'trendino-header-action--' . sanitize_html_class( $type ),
			'is-' . sanitize_html_class( $item['style_variant'] ),
		);

		$is_cart_dropdown = 'cart' === $type && 'yes' === $settings['cart_dropdown'] && trendino_is_woocommerce_active();

		if ( $is_cart_dropdown ) {
			$classes[] = 'has-dropdown';
		}
		?>
		<div class="trendino-header-action-wrap">
			<a class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" href="<?php echo esc_url( $data['url'] ); ?>"
				<?php echo ! empty( $item['custom_url']['is_external'] ) ? ' target="_blank"' : ''; ?>
				aria-label="<?php echo esc_attr( $label ); ?>">
				<span class="trendino-header-action__icon">
					<?php
					if ( ! empty( $item['custom_icon']['value'] ) ) {
						\Elementor\Icons_Manager::render_icon( $item['custom_icon'], array( 'aria-hidden' => 'true' ) );
					} else {
						trendino_icon( $data['icon'], array( 'size' => 22 ) );
					}
					?>

					<?php if ( 'yes' === ( $item['show_count'] ?? '' ) && null !== $data['count'] ) : ?>
						<span class="trendino-header-action__count" data-trendino-count="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( trendino_number( $data['count'] ) ); ?></span>
					<?php endif; ?>
				</span>

				<?php if ( 'yes' === ( $item['show_label'] ?? '' ) && $label ) : ?>
					<span class="trendino-header-action__label"><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			</a>

			<?php if ( $is_cart_dropdown ) : ?>
				<div class="trendino-mini-cart" data-trendino-mini-cart>
					<div class="widget_shopping_cart_content">
						<?php woocommerce_mini_cart(); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Resolve url / icon / label / counter for an action type.
	 *
	 * @param string $type Action type.
	 * @return array{url:string,icon:string,label:string,count:?int}
	 */
	protected function get_item_data( $type ) {
		$defaults = array(
			'url'   => home_url( '/' ),
			'icon'  => 'user',
			'label' => '',
			'count' => null,
		);

		switch ( $type ) {
			case 'cart':
				$defaults['icon']  = 'cart';
				$defaults['label'] = esc_html__( 'Cart', 'trendino' );

				if ( trendino_is_woocommerce_active() ) {
					$defaults['url']   = wc_get_cart_url();
					$defaults['count'] = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
				}
				break;

			case 'wishlist':
				$defaults['icon']  = 'heart';
				$defaults['label'] = esc_html__( 'Wishlist', 'trendino' );
				$defaults['url']   = trendino_wishlist_url();
				$defaults['count'] = trendino_wishlist_count();
				break;

			case 'compare':
				$defaults['icon']  = 'compare';
				$defaults['label'] = esc_html__( 'Compare', 'trendino' );
				$defaults['url']   = trendino_compare_url();
				$defaults['count'] = 0;
				break;

			case 'orders':
				$defaults['icon']  = 'truck';
				$defaults['label'] = esc_html__( 'Order tracking', 'trendino' );
				$defaults['url']   = trendino_is_woocommerce_active() ? wc_get_account_endpoint_url( 'orders' ) : home_url( '/' );
				break;

			case 'custom':
				$defaults['icon'] = 'grid';
				break;

			case 'account':
			default:
				$defaults['icon']  = 'user';
				$defaults['label'] = is_user_logged_in() ? esc_html__( 'My account', 'trendino' ) : esc_html__( 'Login | Register', 'trendino' );
				$defaults['url']   = trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
				break;
		}

		return $defaults;
	}
}
