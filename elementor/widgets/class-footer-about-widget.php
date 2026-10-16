<?php
/**
 * Footer about widget: logo, description, contact rows.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Footer_About
 */
class Footer_About extends Base_Widget {

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
		return 'trendino-footer-about';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Footer About', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-circle-o';
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
			array( 'label' => esc_html__( 'About', 'trendino' ) )
		);

		$this->add_control(
			'logo_source',
			array(
				'label'   => esc_html__( 'Logo', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => array(
					'site'   => esc_html__( 'Site Logo', 'trendino' ),
					'custom' => esc_html__( 'Custom Image', 'trendino' ),
					'none'   => esc_html__( 'None', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => esc_html__( 'Logo Image', 'trendino' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'logo_source' => 'custom' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'Description', 'trendino' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'phone',
				'options' => array(
					'phone'    => esc_html__( 'Phone', 'trendino' ),
					'mail'     => esc_html__( 'Email', 'trendino' ),
					'location' => esc_html__( 'Address', 'trendino' ),
					'clock'    => esc_html__( 'Working Hours', 'trendino' ),
					'headset'  => esc_html__( 'Support', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'Label', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'value',
			array(
				'label'       => esc_html__( 'Value', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label' => esc_html__( 'Link', 'trendino' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'contacts',
			array(
				'label'       => esc_html__( 'Contact Rows', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label || value }}}',
				'default'     => array(
					array(
						'icon'  => 'phone',
						'label' => esc_html__( 'Support', 'trendino' ),
						'value' => '021-12345678',
					),
					array(
						'icon'  => 'mail',
						'label' => esc_html__( 'Email', 'trendino' ),
						'value' => 'info@example.com',
					),
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

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Logo Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 320 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-footer-about__logo img' => 'width:{{SIZE}}{{UNIT}};height:auto;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Description Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-footer-about__desc',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Description Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-about__desc' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'contact_color',
			array(
				'label'     => esc_html__( 'Contact Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-contact' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'contact_icon_color',
			array(
				'label'     => esc_html__( 'Contact Icon Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-footer-contact__icon' => 'color:{{VALUE}};',
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
		<div class="trendino-footer-about">
			<?php if ( 'none' !== $settings['logo_source'] ) : ?>
				<div class="trendino-footer-about__logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php if ( 'custom' === $settings['logo_source'] && ! empty( $settings['logo_image']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $settings['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy" />
						<?php elseif ( has_custom_logo() ) : ?>
							<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?>
						<?php else : ?>
							<span class="trendino-footer-about__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $settings['description'] ) : ?>
				<p class="trendino-footer-about__desc"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $settings['contacts'] ) ) : ?>
				<ul class="trendino-footer-contacts">
					<?php foreach ( $settings['contacts'] as $contact ) : ?>
						<?php if ( empty( $contact['value'] ) && empty( $contact['label'] ) ) { continue; } ?>
						<li class="trendino-footer-contact">
							<span class="trendino-footer-contact__icon"><?php trendino_icon( $contact['icon'], array( 'size' => 20 ) ); ?></span>
							<span class="trendino-footer-contact__text">
								<?php if ( $contact['label'] ) : ?>
									<span class="trendino-footer-contact__label"><?php echo esc_html( $contact['label'] ); ?></span>
								<?php endif; ?>
								<?php if ( $contact['value'] ) : ?>
									<?php if ( ! empty( $contact['url']['url'] ) ) : ?>
										<a class="trendino-footer-contact__value" href="<?php echo esc_url( $contact['url']['url'] ); ?>"><?php echo esc_html( trendino_number( $contact['value'] ) ); ?></a>
									<?php else : ?>
										<span class="trendino-footer-contact__value"><?php echo esc_html( trendino_number( $contact['value'] ) ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
}
