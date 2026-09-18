<?php
/**
 * Top bar widget (announcement + quick links + contact).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Topbar
 */
class Header_Topbar extends Base_Widget {

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
		return 'trendino-header-topbar';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Top Bar', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-box';
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
			array( 'label' => esc_html__( 'Top Bar', 'trendino' ) )
		);

		$this->add_control(
			'message',
			array(
				'label'       => esc_html__( 'Announcement', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Free shipping on orders over 500,000 Toman', 'trendino' ),
			)
		);

		$this->add_control(
			'message_icon',
			array(
				'label'   => esc_html__( 'Announcement Icon', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'truck',
				'options' => array(
					'truck'   => esc_html__( 'Delivery', 'trendino' ),
					'percent' => esc_html__( 'Discount', 'trendino' ),
					'gift'    => esc_html__( 'Gift', 'trendino' ),
					'bell'    => esc_html__( 'Bell', 'trendino' ),
					'none'    => esc_html__( 'None', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'message_link',
			array(
				'label' => esc_html__( 'Announcement Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
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

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => esc_html__( 'None', 'trendino' ),
					'phone'    => esc_html__( 'Phone', 'trendino' ),
					'truck'    => esc_html__( 'Delivery', 'trendino' ),
					'headset'  => esc_html__( 'Support', 'trendino' ),
					'location' => esc_html__( 'Location', 'trendino' ),
					'refresh'  => esc_html__( 'Returns', 'trendino' ),
					'shield'   => esc_html__( 'Warranty', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => esc_html__( 'Quick Links', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array( 'text' => esc_html__( 'Order tracking', 'trendino' ), 'icon' => 'truck' ),
					array( 'text' => esc_html__( 'Contact us', 'trendino' ), 'icon' => 'headset' ),
					array( 'text' => esc_html__( 'Sell on Trendino', 'trendino' ), 'icon' => 'none' ),
				),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label'       => esc_html__( 'Support Phone', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
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
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-topbar' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-topbar' => 'color:{{VALUE}};',
					'{{WRAPPER}} .trendino-topbar a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bar_typography',
				'selector' => '{{WRAPPER}} .trendino-topbar',
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-topbar' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hide_on_mobile',
			array(
				'label'        => esc_html__( 'Hide Quick Links On Mobile', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
		$links    = $settings['links'] ?? array();
		?>
		<div class="trendino-topbar<?php echo 'yes' === $settings['hide_on_mobile'] ? ' hide-links-mobile' : ''; ?>">
			<?php if ( $links ) : ?>
				<ul class="trendino-topbar__links">
					<?php foreach ( $links as $link ) : ?>
						<li>
							<a href="<?php echo esc_url( $link['url']['url'] ?? '#' ); ?>"
								<?php echo ! empty( $link['url']['is_external'] ) ? ' target="_blank"' : ''; ?>>
								<?php if ( 'none' !== $link['icon'] ) : ?>
									<?php trendino_icon( $link['icon'], array( 'size' => 16 ) ); ?>
								<?php endif; ?>
								<span><?php echo esc_html( $link['text'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<div class="trendino-topbar__end">
				<?php if ( $settings['message'] ) : ?>
					<?php $has_link = ! empty( $settings['message_link']['url'] ); ?>
					<<?php echo $has_link ? 'a' : 'span'; ?> class="trendino-topbar__message"
						<?php if ( $has_link ) : ?>
							href="<?php echo esc_url( $settings['message_link']['url'] ); ?>"
							<?php echo ! empty( $settings['message_link']['is_external'] ) ? ' target="_blank"' : ''; ?>
						<?php endif; ?>>
						<?php if ( 'none' !== $settings['message_icon'] ) : ?>
							<?php trendino_icon( $settings['message_icon'], array( 'size' => 16 ) ); ?>
						<?php endif; ?>
						<span><?php echo esc_html( $settings['message'] ); ?></span>
					</<?php echo $has_link ? 'a' : 'span'; ?>>
				<?php endif; ?>

				<?php if ( $settings['phone'] ) : ?>
					<a class="trendino-topbar__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $settings['phone'] ) ); ?>">
						<?php trendino_icon( 'phone', array( 'size' => 16 ) ); ?>
						<span><?php echo esc_html( trendino_number( $settings['phone'] ) ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
