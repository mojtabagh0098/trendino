<?php
/**
 * Footer bottom bar widget: copyright, secondary links, payment icons.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Footer_Bottom
 */
class Footer_Bottom extends Base_Widget {

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
		return 'trendino-footer-bottom';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Footer Bottom', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-footer';
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
			array( 'label' => esc_html__( 'Bottom Bar', 'trendino' ) )
		);

		$this->add_control(
			'copyright',
			array(
				'label'       => esc_html__( 'Copyright Text', 'trendino' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => sprintf(
					/* translators: %s: site name */
					esc_html__( 'All rights reserved for %s.', 'trendino' ),
					get_bloginfo( 'name' )
				),
				'description' => esc_html__( 'Use {year} to print the current year.', 'trendino' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
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
				'label'       => esc_html__( 'Bottom Links', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(),
			)
		);

		$payments = new Repeater();

		$payments->add_control(
			'image',
			array(
				'label' => esc_html__( 'Payment Icon', 'trendino' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$payments->add_control(
			'alt',
			array(
				'label' => esc_html__( 'Alt Text', 'trendino' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'payments',
			array(
				'label'       => esc_html__( 'Payment Icons', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $payments->get_controls(),
				'title_field' => '{{{ alt || "Payment" }}}',
				'default'     => array(),
			)
		);

		$this->add_control(
			'show_scroll_top',
			array(
				'label'        => esc_html__( 'Scroll To Top Button', 'trendino' ),
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

		$this->add_control(
			'bar_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-bottom' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .trendino-footer-bottom',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-bottom' => 'color:{{VALUE}};',
					'{{WRAPPER}} .trendino-footer-bottom a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-footer-bottom' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'payment_height',
			array(
				'label'      => esc_html__( 'Payment Icon Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 64 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-footer-bottom__payments img' => 'height:{{SIZE}}{{UNIT}};width:auto;',
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
		$settings  = $this->get_settings_for_display();
		$copyright = str_replace( '{year}', trendino_number( gmdate( 'Y' ) ), (string) $settings['copyright'] );
		?>
		<div class="trendino-footer-bottom">
			<div class="trendino-footer-bottom__start">
				<?php if ( $copyright ) : ?>
					<span class="trendino-footer-bottom__copyright"><?php echo esc_html( $copyright ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $settings['links'] ) ) : ?>
					<ul class="trendino-footer-bottom__links">
						<?php foreach ( $settings['links'] as $link ) : ?>
							<?php if ( empty( $link['text'] ) ) { continue; } ?>
							<li><a href="<?php echo esc_url( $link['url']['url'] ?? '#' ); ?>"><?php echo esc_html( $link['text'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="trendino-footer-bottom__end">
				<?php if ( ! empty( $settings['payments'] ) ) : ?>
					<div class="trendino-footer-bottom__payments">
						<?php foreach ( $settings['payments'] as $payment ) : ?>
							<?php if ( empty( $payment['image']['url'] ) ) { continue; } ?>
							<img src="<?php echo esc_url( $payment['image']['url'] ); ?>" alt="<?php echo esc_attr( $payment['alt'] ); ?>" loading="lazy" decoding="async" />
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_scroll_top'] ) : ?>
					<button type="button" class="trendino-scroll-top" data-trendino-scroll-top aria-label="<?php esc_attr_e( 'Scroll to top', 'trendino' ); ?>">
						<?php trendino_icon( 'chevron-down', array( 'size' => 18 ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
