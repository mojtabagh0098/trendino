<?php
/**
 * Mobile header widget: hamburger drawer + logo + search + cart, and an optional
 * fixed bottom navigation bar for phones.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Mobile
 */
class Header_Mobile extends Base_Widget {

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
		return 'trendino-header-mobile';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Mobile Header', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-device-mobile';
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
			array( 'label' => esc_html__( 'Mobile Header', 'trendino' ) )
		);

		$menus = Query_Helper::menu_options();

		$this->add_control(
			'menu',
			array(
				'label'       => esc_html__( 'Drawer Menu', 'trendino' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $menus,
				'default'     => $menus ? array_key_first( $menus ) : '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_logo',
			array(
				'label'        => esc_html__( 'Show Logo', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => esc_html__( 'Show Search Row', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => esc_html__( 'Search Placeholder', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Search products…', 'trendino' ),
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'show_cart',
			array(
				'label'        => esc_html__( 'Show Cart', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_categories_in_drawer',
			array(
				'label'        => esc_html__( 'Show Product Categories In Drawer', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bottom_nav',
			array( 'label' => esc_html__( 'Bottom Navigation', 'trendino' ) )
		);

		$this->add_control(
			'bottom_nav',
			array(
				'label'        => esc_html__( 'Enable Bottom Bar', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Type', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'home',
				'options' => array(
					'home'       => esc_html__( 'Home', 'trendino' ),
					'categories' => esc_html__( 'Categories', 'trendino' ),
					'cart'       => esc_html__( 'Cart', 'trendino' ),
					'wishlist'   => esc_html__( 'Wishlist', 'trendino' ),
					'account'    => esc_html__( 'Account', 'trendino' ),
					'custom'     => esc_html__( 'Custom', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'     => esc_html__( 'URL', 'trendino' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'type' => 'custom' ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'Custom Icon', 'trendino' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'type' => 'custom' ),
			)
		);

		$this->add_control(
			'bottom_items',
			array(
				'label'       => esc_html__( 'Items', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ type }}}',
				'condition'   => array( 'bottom_nav' => 'yes' ),
				'default'     => array(
					array( 'type' => 'home', 'label' => esc_html__( 'Home', 'trendino' ) ),
					array( 'type' => 'categories', 'label' => esc_html__( 'Categories', 'trendino' ) ),
					array( 'type' => 'cart', 'label' => esc_html__( 'Cart', 'trendino' ) ),
					array( 'type' => 'wishlist', 'label' => esc_html__( 'Wishlist', 'trendino' ) ),
					array( 'type' => 'account', 'label' => esc_html__( 'Account', 'trendino' ) ),
				),
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
			'bar_bg',
			array(
				'label'     => esc_html__( 'Header Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-mobile-header' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-mobile-header' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bottom_active_color',
			array(
				'label'     => esc_html__( 'Bottom Bar Active Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-bottom-nav__item.is-active' => 'color:{{VALUE}};',
				),
				'condition' => array( 'bottom_nav' => 'yes' ),
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
		?>
		<div class="trendino-mobile-header">
			<div class="trendino-mobile-header__bar">
				<button type="button" class="trendino-mobile-header__toggle" data-trendino-drawer-open aria-label="<?php esc_attr_e( 'Open menu', 'trendino' ); ?>" aria-expanded="false">
					<?php trendino_icon( 'menu', array( 'size' => 24 ) ); ?>
				</button>

				<?php if ( 'yes' === $settings['show_logo'] ) : ?>
					<a class="trendino-mobile-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php if ( has_custom_logo() ) : ?>
							<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
						<?php else : ?>
							<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_cart'] && trendino_is_woocommerce_active() ) : ?>
					<a class="trendino-mobile-header__cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'trendino' ); ?>">
						<?php trendino_icon( 'cart', array( 'size' => 24 ) ); ?>
						<span class="trendino-header-action__count" data-trendino-count="cart"><?php echo esc_html( trendino_number( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?></span>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $settings['show_search'] ) : ?>
				<form role="search" method="get" class="trendino-mobile-header__search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( trendino_is_woocommerce_active() ) : ?>
						<input type="hidden" name="post_type" value="product" />
					<?php endif; ?>
					<?php trendino_icon( 'search', array( 'size' => 18 ) ); ?>
					<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>" aria-label="<?php esc_attr_e( 'Search', 'trendino' ); ?>" />
				</form>
			<?php endif; ?>

			<div class="trendino-drawer" data-trendino-drawer hidden>
				<div class="trendino-drawer__backdrop" data-trendino-drawer-close></div>
				<aside class="trendino-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile menu', 'trendino' ); ?>">
					<div class="trendino-drawer__head">
						<span class="trendino-drawer__title"><?php esc_html_e( 'Menu', 'trendino' ); ?></span>
						<button type="button" class="trendino-drawer__close" data-trendino-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'trendino' ); ?>">
							<?php trendino_icon( 'close', array( 'size' => 22 ) ); ?>
						</button>
					</div>

					<div class="trendino-drawer__account">
						<?php trendino_icon( 'user', array( 'size' => 22 ) ); ?>
						<?php if ( is_user_logged_in() ) : ?>
							<a href="<?php echo esc_url( trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : admin_url( 'profile.php' ) ); ?>">
								<?php echo esc_html( wp_get_current_user()->display_name ); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">
								<?php esc_html_e( 'Login | Register', 'trendino' ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $settings['menu'] ) ) : ?>
						<nav class="trendino-drawer__menu" aria-label="<?php esc_attr_e( 'Mobile navigation', 'trendino' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'menu'        => $settings['menu'],
									'container'   => false,
									'menu_class'  => 'trendino-drawer__list',
									'depth'       => 3,
									'fallback_cb' => '__return_empty_string',
									'walker'      => new \Trendino_Menu_Walker(),
								)
							);
							?>
						</nav>
					<?php endif; ?>

					<?php if ( 'yes' === $settings['show_categories_in_drawer'] && trendino_is_woocommerce_active() ) : ?>
						<div class="trendino-drawer__categories">
							<span class="trendino-drawer__section-title"><?php esc_html_e( 'Shop by category', 'trendino' ); ?></span>
							<ul>
								<?php foreach ( Query_Helper::product_categories( array( 'parent' => 0, 'number' => 12 ) ) as $term ) : ?>
									<li>
										<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
											<?php echo trendino_category_thumbnail_html( $term, 'trendino-story', 'trendino-drawer__thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<span><?php echo esc_html( $term->name ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</div>

		<?php if ( 'yes' === $settings['bottom_nav'] && ! empty( $settings['bottom_items'] ) ) : ?>
			<nav class="trendino-bottom-nav" aria-label="<?php esc_attr_e( 'Mobile quick navigation', 'trendino' ); ?>">
				<?php foreach ( $settings['bottom_items'] as $item ) : ?>
					<?php $data = $this->get_bottom_item_data( $item ); ?>
					<a class="trendino-bottom-nav__item<?php echo $data['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $data['url'] ); ?>">
						<span class="trendino-bottom-nav__icon">
							<?php
							if ( 'custom' === $item['type'] && ! empty( $item['icon']['value'] ) ) {
								\Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
							} else {
								trendino_icon( $data['icon'], array( 'size' => 22 ) );
							}
							?>
							<?php if ( null !== $data['count'] && $data['count'] > 0 ) : ?>
								<span class="trendino-bottom-nav__count"><?php echo esc_html( trendino_number( $data['count'] ) ); ?></span>
							<?php endif; ?>
						</span>
						<span class="trendino-bottom-nav__label"><?php echo esc_html( $item['label'] ? $item['label'] : $data['label'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<?php
	}

	/**
	 * Resolve bottom nav item data.
	 *
	 * @param array $item Repeater item.
	 * @return array{url:string,icon:string,label:string,count:?int,active:bool}
	 */
	protected function get_bottom_item_data( array $item ) {
		$data = array(
			'url'    => home_url( '/' ),
			'icon'   => 'grid',
			'label'  => '',
			'count'  => null,
			'active' => false,
		);

		switch ( $item['type'] ) {
			case 'home':
				$data['icon']   = 'grid';
				$data['label']  = esc_html__( 'Home', 'trendino' );
				$data['active'] = is_front_page();
				break;

			case 'categories':
				$data['icon']  = 'category';
				$data['label'] = esc_html__( 'Categories', 'trendino' );
				$data['url']   = trendino_is_woocommerce_active() ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
				break;

			case 'cart':
				$data['icon']  = 'cart';
				$data['label'] = esc_html__( 'Cart', 'trendino' );

				if ( trendino_is_woocommerce_active() ) {
					$data['url']    = wc_get_cart_url();
					$data['count']  = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
					$data['active'] = is_cart();
				}
				break;

			case 'wishlist':
				$data['icon']  = 'heart';
				$data['label'] = esc_html__( 'Wishlist', 'trendino' );
				$data['url']   = trendino_wishlist_url();
				$data['count'] = trendino_wishlist_count();
				break;

			case 'account':
				$data['icon']  = 'user';
				$data['label'] = esc_html__( 'Account', 'trendino' );
				$data['url']   = trendino_is_woocommerce_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();

				if ( trendino_is_woocommerce_active() ) {
					$data['active'] = is_account_page();
				}
				break;

			case 'custom':
			default:
				$data['url'] = $item['url']['url'] ?? home_url( '/' );
				break;
		}

		return $data;
	}
}
