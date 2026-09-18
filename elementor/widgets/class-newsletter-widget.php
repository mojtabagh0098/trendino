<?php
/**
 * Newsletter subscription widget (AJAX, stores subscribers + supports shortcode form).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Newsletter
 */
class Newsletter extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'newsletter' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-newsletter';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Newsletter', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-email-field';
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
			array( 'label' => esc_html__( 'Content', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Subscribe to the newsletter', 'trendino' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'trendino' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Be the first to know about new arrivals and exclusive offers.', 'trendino' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mail',
				'options' => array(
					'mail'  => esc_html__( 'Mail', 'trendino' ),
					'bell'  => esc_html__( 'Bell', 'trendino' ),
					'gift'  => esc_html__( 'Gift', 'trendino' ),
					'none'  => esc_html__( 'None', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'form_type',
			array(
				'label'   => esc_html__( 'Form Type', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'builtin',
				'options' => array(
					'builtin'   => esc_html__( 'Built-in (AJAX)', 'trendino' ),
					'shortcode' => esc_html__( 'Shortcode (Mailchimp, CF7…)', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'shortcode',
			array(
				'label'     => esc_html__( 'Shortcode', 'trendino' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'condition' => array( 'form_type' => 'shortcode' ),
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label'     => esc_html__( 'Input Placeholder', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Your email address', 'trendino' ),
				'condition' => array( 'form_type' => 'builtin' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'Button Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Subscribe', 'trendino' ),
				'condition' => array( 'form_type' => 'builtin' ),
			)
		);

		$this->add_control(
			'consent_text',
			array(
				'label'     => esc_html__( 'Consent Note', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array( 'form_type' => 'builtin' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'  => esc_html__( 'Text Beside Form', 'trendino' ),
					'stacked' => esc_html__( 'Text Above Form', 'trendino' ),
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
			Group_Control_Background::get_type(),
			array(
				'name'     => 'wrapper_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .trendino-newsletter',
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-newsletter' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
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
					'{{WRAPPER}} .trendino-newsletter' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-newsletter__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-newsletter__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Description Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-newsletter__desc' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_bg',
			array(
				'label'     => esc_html__( 'Input Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-newsletter__form' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_radius',
			array(
				'label'      => esc_html__( 'Input Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-newsletter__form' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'Button Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-newsletter__submit' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-newsletter__submit' => 'color:{{VALUE}};',
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
		<div class="trendino-newsletter layout-<?php echo esc_attr( $settings['layout'] ); ?>">
			<div class="trendino-newsletter__text">
				<?php if ( 'none' !== $settings['icon'] ) : ?>
					<span class="trendino-newsletter__icon"><?php trendino_icon( $settings['icon'], array( 'size' => 32 ) ); ?></span>
				<?php endif; ?>

				<div>
					<?php if ( $settings['title'] ) : ?>
						<h3 class="trendino-newsletter__title"><?php echo esc_html( $settings['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( $settings['description'] ) : ?>
						<p class="trendino-newsletter__desc"><?php echo esc_html( $settings['description'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="trendino-newsletter__form-wrap">
				<?php if ( 'shortcode' === $settings['form_type'] ) : ?>
					<?php echo do_shortcode( wp_kses_post( $settings['shortcode'] ) ); ?>
				<?php else : ?>
					<form class="trendino-newsletter__form" data-trendino-newsletter method="post">
						<?php wp_nonce_field( 'trendino_newsletter', 'trendino_newsletter_nonce' ); ?>
						<label class="screen-reader-text" for="trendino-newsletter-<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'Email address', 'trendino' ); ?></label>
						<input
							id="trendino-newsletter-<?php echo esc_attr( $this->get_id() ); ?>"
							class="trendino-newsletter__input"
							type="email"
							name="email"
							required
							placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
						/>
						<button type="submit" class="trendino-newsletter__submit">
							<span><?php echo esc_html( $settings['button_text'] ); ?></span>
						</button>
					</form>

					<p class="trendino-newsletter__message" role="status" aria-live="polite"></p>

					<?php if ( $settings['consent_text'] ) : ?>
						<p class="trendino-newsletter__consent"><?php echo esc_html( $settings['consent_text'] ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
