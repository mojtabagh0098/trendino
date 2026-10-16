<?php
/**
 * Footer link column widget (WP menu or manual links).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Trendino\Elementor\Query_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Footer_Menu
 */
class Footer_Menu extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'footer' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-footer-menu';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Footer Links', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-footer' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Links', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Column Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Customer service', 'trendino' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'      => esc_html__( 'Manual Links', 'trendino' ),
					'menu'        => esc_html__( 'WordPress Menu', 'trendino' ),
					'product_cat' => esc_html__( 'Product Categories', 'trendino' ),
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
			'categories_limit',
			array(
				'label'     => esc_html__( 'Categories Count', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 20,
				'default'   => 6,
				'condition' => array( 'source' => 'product_cat' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Link', 'trendino' ),
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
				'label'       => esc_html__( 'Links', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'source' => 'manual' ),
				'default'     => array(
					array( 'text' => esc_html__( 'Frequently asked questions', 'trendino' ) ),
					array( 'text' => esc_html__( 'Order tracking', 'trendino' ) ),
					array( 'text' => esc_html__( 'Returns policy', 'trendino' ) ),
					array( 'text' => esc_html__( 'Privacy policy', 'trendino' ) ),
					array( 'text' => esc_html__( 'Terms and conditions', 'trendino' ) ),
				),
			)
		);

		$this->add_control(
			'collapsible_mobile',
			array(
				'label'        => esc_html__( 'Accordion On Mobile', 'trendino' ),
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-footer-links__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-links__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'Link Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-footer-links a',
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-links a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-links a:hover' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_spacing',
			array(
				'label'      => esc_html__( 'Row Spacing', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-footer-links__list' => 'gap:{{SIZE}}{{UNIT}};',
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
		$classes  = array( 'trendino-footer-links' );

		if ( 'yes' === $settings['collapsible_mobile'] ) {
			$classes[] = 'is-collapsible';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php if ( $settings['title'] ) : ?>
				<button type="button" class="trendino-footer-links__title" data-trendino-footer-toggle aria-expanded="false">
					<span><?php echo esc_html( $settings['title'] ); ?></span>
					<?php trendino_icon( 'chevron-down', array( 'size' => 16, 'class' => 'trendino-footer-links__caret' ) ); ?>
				</button>
			<?php endif; ?>

			<div class="trendino-footer-links__body">
				<?php if ( 'menu' === $settings['source'] && ! empty( $settings['menu'] ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'menu'        => $settings['menu'],
							'container'   => false,
							'menu_class'  => 'trendino-footer-links__list',
							'depth'       => 1,
							'fallback_cb' => '__return_empty_string',
						)
					);
					?>
				<?php elseif ( 'product_cat' === $settings['source'] && trendino_is_woocommerce_active() ) : ?>
					<ul class="trendino-footer-links__list">
						<?php foreach ( Query_Helper::product_categories( array( 'parent' => 0, 'number' => (int) $settings['categories_limit'] ) ) as $term ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<ul class="trendino-footer-links__list">
						<?php foreach ( (array) ( $settings['links'] ?? array() ) as $link ) : ?>
							<?php if ( empty( $link['text'] ) ) { continue; } ?>
							<li>
								<a href="<?php echo esc_url( $link['url']['url'] ?? '#' ); ?>"
									<?php echo ! empty( $link['url']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
									<?php echo esc_html( $link['text'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
