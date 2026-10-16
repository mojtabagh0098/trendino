<?php
/**
 * Main navigation menu widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Menu
 */
class Header_Menu extends Base_Widget {

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
		return 'trendino-header-menu';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Menu', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-header', 'trendino-footer' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Menu', 'trendino' ) )
		);

		$menus = Query_Helper::menu_options();

		if ( empty( $menus ) ) {
			$this->add_control(
				'no_menu_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf(
						/* translators: %s: menus admin url */
						esc_html__( 'There are no menus yet. %s', 'trendino' ),
						'<a href="' . esc_url( admin_url( 'nav-menus.php?action=edit&menu=0' ) ) . '" target="_blank">' . esc_html__( 'Create one', 'trendino' ) . '</a>'
					),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		} else {
			$this->add_control(
				'menu',
				array(
					'label'       => esc_html__( 'Select Menu', 'trendino' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => $menus,
					'default'     => array_key_first( $menus ),
					'label_block' => true,
				)
			);
		}

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'trendino' ),
					'vertical'   => esc_html__( 'Vertical', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'submenu_indicator',
			array(
				'label'        => esc_html__( 'Submenu Indicator', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'mega_menu',
			array(
				'label'        => esc_html__( 'Full Width Submenu (mega)', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Renders 2nd level items as columns spanning the container width.', 'trendino' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'trendino' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'    => array(
						'title' => esc_html__( 'Start', 'trendino' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'        => array(
						'title' => esc_html__( 'Center', 'trendino' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'      => array(
						'title' => esc_html__( 'End', 'trendino' ),
						'icon'  => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => esc_html__( 'Space Between', 'trendino' ),
						'icon'  => 'eicon-justify-space-between-h',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu > ul' => 'justify-content:{{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Menu Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .trendino-menu > ul > li > a',
			)
		);

		$this->start_controls_tabs( 'menu_state_tabs' );

		$this->start_controls_tab( 'menu_normal', array( 'label' => esc_html__( 'Normal', 'trendino' ) ) );

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu > ul > li > a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'menu_hover', array( 'label' => esc_html__( 'Hover', 'trendino' ) ) );

		$this->add_control(
			'menu_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu > ul > li > a:hover' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_hover_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu > ul > li > a:hover' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'menu_active', array( 'label' => esc_html__( 'Active', 'trendino' ) ) );

		$this->add_control(
			'menu_active_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu > ul > li.current-menu-item > a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'Item Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-menu > ul' => 'gap:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-menu > ul > li > a' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submenu_style',
			array(
				'label' => esc_html__( 'Submenu', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'submenu_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu ul ul' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submenu_typography',
				'selector' => '{{WRAPPER}} .trendino-menu ul ul a',
			)
		);

		$this->add_control(
			'submenu_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu ul ul a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submenu_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-menu ul ul a:hover' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submenu_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-menu ul ul' => 'border-radius:{{SIZE}}{{UNIT}};',
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
		$menu     = $settings['menu'] ?? '';

		if ( ! $menu ) {
			$this->editor_notice( esc_html__( 'Select a menu for this widget.', 'trendino' ) );

			return;
		}

		$classes = array(
			'trendino-menu',
			'trendino-menu--' . sanitize_html_class( $settings['layout'] ),
		);

		if ( 'yes' === $settings['submenu_indicator'] ) {
			$classes[] = 'has-indicator';
		}

		if ( 'yes' === $settings['mega_menu'] ) {
			$classes[] = 'is-mega';
		}
		?>
		<nav class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-label="<?php esc_attr_e( 'Main navigation', 'trendino' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'menu'        => $menu,
					'container'   => false,
					'menu_class'  => 'trendino-menu__list',
					'depth'       => 3,
					'fallback_cb' => '__return_empty_string',
					'walker'      => new \Trendino_Menu_Walker(),
				)
			);
			?>
		</nav>
		<?php
	}
}
