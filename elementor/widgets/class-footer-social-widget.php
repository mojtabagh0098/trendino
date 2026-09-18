<?php
/**
 * Footer social icons widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Footer_Social
 */
class Footer_Social extends Base_Widget {

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
		return 'trendino-footer-social';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Social Icons', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-social-icons';
	}

	/**
	 * Categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-footer', 'trendino-header' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Social', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Follow us', 'trendino' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'network',
			array(
				'label'   => esc_html__( 'Network', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'instagram',
				'options' => array(
					'instagram' => 'Instagram',
					'telegram'  => 'Telegram',
					'whatsapp'  => 'WhatsApp',
					'twitter'   => 'X / Twitter',
					'youtube'   => 'YouTube',
					'linkedin'  => 'LinkedIn',
					'aparat'    => 'Aparat',
					'custom'    => esc_html__( 'Custom', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'custom_icon',
			array(
				'label'     => esc_html__( 'Custom Icon', 'trendino' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'network' => 'custom' ),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'   => esc_html__( 'URL', 'trendino' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'is_external' => true ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Networks', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ network }}}',
				'default'     => array(
					array( 'network' => 'instagram' ),
					array( 'network' => 'telegram' ),
					array( 'network' => 'twitter' ),
					array( 'network' => 'youtube' ),
				),
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'   => esc_html__( 'Shape', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle'  => esc_html__( 'Circle', 'trendino' ),
					'rounded' => esc_html__( 'Rounded', 'trendino' ),
					'square'  => esc_html__( 'Square', 'trendino' ),
					'bare'    => esc_html__( 'No Background', 'trendino' ),
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
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .trendino-social__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-social__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-social__link svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_size',
			array(
				'label'      => esc_html__( 'Box Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 72 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-social__link' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-social__link' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-social__link' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'Hover Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-social__link:hover' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_bg',
			array(
				'label'     => esc_html__( 'Hover Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-social__link:hover' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-social__list' => 'gap:{{SIZE}}{{UNIT}};',
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
		?>
		<div class="trendino-social shape-<?php echo esc_attr( $settings['shape'] ); ?>">
			<?php if ( $settings['title'] ) : ?>
				<span class="trendino-social__title"><?php echo esc_html( $settings['title'] ); ?></span>
			<?php endif; ?>

			<ul class="trendino-social__list">
				<?php foreach ( $items as $item ) : ?>
					<li>
						<a class="trendino-social__link is-<?php echo esc_attr( $item['network'] ); ?>"
							href="<?php echo esc_url( $item['url']['url'] ?? '#' ); ?>"
							<?php echo ! empty( $item['url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
							aria-label="<?php echo esc_attr( $item['network'] ); ?>">
							<?php
							if ( 'custom' === $item['network'] && ! empty( $item['custom_icon']['value'] ) ) {
								\Elementor\Icons_Manager::render_icon( $item['custom_icon'], array( 'aria-hidden' => 'true' ) );
							} else {
								trendino_icon( $item['network'], array( 'size' => 20 ) );
							}
							?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
