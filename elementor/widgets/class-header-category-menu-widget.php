<?php
/**
 * Category mega menu widget ("همه دسته‌بندی‌ها" trigger + flyout panel).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Category_Menu
 */
class Header_Category_Menu extends Base_Widget {

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
		return 'trendino-header-category-menu';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Category Menu', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-menu-bar';
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
	 * Requires WooCommerce when sourcing product categories.
	 *
	 * @return bool
	 */
	protected function requires_woocommerce() {
		return false;
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Category Menu', 'trendino' ) )
		);

		$this->add_control(
			'trigger_text',
			array(
				'label'   => esc_html__( 'Trigger Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'All categories', 'trendino' ),
			)
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label'   => esc_html__( 'Trigger Icon', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'Lines', 'trendino' ),
					'grid'     => esc_html__( 'Grid', 'trendino' ),
					'menu'     => esc_html__( 'Hamburger', 'trendino' ),
					'none'     => esc_html__( 'None', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'product_cat',
				'options' => array(
					'product_cat' => esc_html__( 'WooCommerce Categories', 'trendino' ),
					'menu'        => esc_html__( 'WordPress Menu', 'trendino' ),
					'manual'      => esc_html__( 'Selected Categories', 'trendino' ),
				),
			)
		);

		$menus = Query_Helper::menu_options();

		$this->add_control(
			'menu',
			array(
				'label'     => esc_html__( 'Select Menu', 'trendino' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $menus,
				'default'   => $menus ? array_key_first( $menus ) : '',
				'condition' => array( 'source' => 'menu' ),
			)
		);

		$this->add_control(
			'selected_categories',
			array(
				'label'       => esc_html__( 'Categories', 'trendino' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Query_Helper::term_options( 'product_cat' ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Parent Categories Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 30,
				'default'   => 12,
				'condition' => array( 'source' => 'product_cat' ),
			)
		);

		$this->add_control(
			'children_limit',
			array(
				'label'   => esc_html__( 'Sub Categories Per Column', 'trendino' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'default' => 8,
			)
		);

		$this->add_control(
			'show_thumbnails',
			array(
				'label'        => esc_html__( 'Show Category Icons', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'open_on',
			array(
				'label'   => esc_html__( 'Open On', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => esc_html__( 'Hover', 'trendino' ),
					'click' => esc_html__( 'Click', 'trendino' ),
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'trigger_typography',
				'selector' => '{{WRAPPER}} .trendino-catmenu__trigger',
			)
		);

		$this->add_control(
			'trigger_color',
			array(
				'label'     => esc_html__( 'Trigger Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-catmenu__trigger' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_bg',
			array(
				'label'     => esc_html__( 'Trigger Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-catmenu__trigger' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_bg',
			array(
				'label'     => esc_html__( 'Panel Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-catmenu__panel' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_width',
			array(
				'label'      => esc_html__( 'Panel Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array( 'px' => array( 'min' => 240, 'max' => 1200 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-catmenu__panel' => 'width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-catmenu a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-catmenu a:hover' => 'color:{{VALUE}};',
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
		?>
		<div class="trendino-catmenu" data-open-on="<?php echo esc_attr( $settings['open_on'] ); ?>">
			<button type="button" class="trendino-catmenu__trigger" aria-expanded="false" aria-haspopup="true">
				<?php if ( 'none' !== $settings['trigger_icon'] ) : ?>
					<?php trendino_icon( $settings['trigger_icon'], array( 'size' => 20 ) ); ?>
				<?php endif; ?>
				<span><?php echo esc_html( $settings['trigger_text'] ); ?></span>
				<?php trendino_icon( 'chevron-down', array( 'size' => 14, 'class' => 'trendino-catmenu__caret' ) ); ?>
			</button>

			<div class="trendino-catmenu__panel">
				<?php
				if ( 'menu' === $settings['source'] && ! empty( $settings['menu'] ) ) {
					$this->render_menu_source( $settings );
				} else {
					$this->render_term_source( $settings );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the panel from a WordPress menu.
	 *
	 * @param array $settings Settings.
	 * @return void
	 */
	protected function render_menu_source( array $settings ) {
		wp_nav_menu(
			array(
				'menu'        => $settings['menu'],
				'container'   => false,
				'menu_class'  => 'trendino-catmenu__list',
				'depth'       => 3,
				'fallback_cb' => '__return_empty_string',
				'walker'      => new \Trendino_Menu_Walker(),
			)
		);
	}

	/**
	 * Render the panel from product categories.
	 *
	 * @param array $settings Settings.
	 * @return void
	 */
	protected function render_term_source( array $settings ) {
		if ( ! trendino_is_woocommerce_active() ) {
			$this->editor_notice( esc_html__( 'WooCommerce is required to list product categories.', 'trendino' ) );

			return;
		}

		$parents = 'manual' === $settings['source']
			? Query_Helper::product_categories( array( 'include' => $settings['selected_categories'] ?? array() ) )
			: Query_Helper::product_categories(
				array(
					'parent' => 0,
					'number' => (int) $settings['limit'],
				)
			);

		if ( empty( $parents ) ) {
			$this->editor_notice( esc_html__( 'No product categories found.', 'trendino' ) );

			return;
		}
		?>
		<ul class="trendino-catmenu__list">
			<?php foreach ( $parents as $parent ) : ?>
				<?php
				$children = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $parent->term_id,
						'hide_empty' => false,
						'number'     => (int) $settings['children_limit'],
					)
				);
				$children = is_wp_error( $children ) ? array() : $children;
				?>
				<li class="trendino-catmenu__item<?php echo $children ? ' has-children' : ''; ?>">
					<a class="trendino-catmenu__link" href="<?php echo esc_url( get_term_link( $parent ) ); ?>">
						<?php if ( 'yes' === $settings['show_thumbnails'] ) : ?>
							<?php echo trendino_category_thumbnail_html( $parent, 'trendino-story', 'trendino-catmenu__thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
						<span><?php echo esc_html( $parent->name ); ?></span>
						<?php if ( $children ) : ?>
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14, 'class' => 'trendino-catmenu__arrow' ) ); ?>
						<?php endif; ?>
					</a>

					<?php if ( $children ) : ?>
						<div class="trendino-catmenu__flyout">
							<div class="trendino-catmenu__flyout-head">
								<a href="<?php echo esc_url( get_term_link( $parent ) ); ?>">
									<?php echo esc_html( $parent->name ); ?>
									<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ); ?>
								</a>
							</div>
							<ul class="trendino-catmenu__children">
								<?php foreach ( $children as $child ) : ?>
									<?php
									$grandchildren = get_terms(
										array(
											'taxonomy'   => 'product_cat',
											'parent'     => $child->term_id,
											'hide_empty' => false,
											'number'     => 6,
										)
									);
									$grandchildren = is_wp_error( $grandchildren ) ? array() : $grandchildren;
									?>
									<li class="trendino-catmenu__child">
										<a class="trendino-catmenu__child-link" href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo esc_html( $child->name ); ?></a>
										<?php if ( $grandchildren ) : ?>
											<ul class="trendino-catmenu__grandchildren">
												<?php foreach ( $grandchildren as $grandchild ) : ?>
													<li><a href="<?php echo esc_url( get_term_link( $grandchild ) ); ?>"><?php echo esc_html( $grandchild->name ); ?></a></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
