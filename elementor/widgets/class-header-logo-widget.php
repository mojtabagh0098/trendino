<?php
/**
 * Header logo widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Header_Logo
 */
class Header_Logo extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'header' );

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-header-logo';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Logo', 'trendino' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-site-logo';
	}

	/**
	 * Panel categories.
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
			array( 'label' => esc_html__( 'Logo', 'trendino' ) )
		);

		$this->add_control(
			'logo_source',
			array(
				'label'   => esc_html__( 'Logo Source', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => array(
					'site'   => esc_html__( 'Site Logo (Customizer)', 'trendino' ),
					'custom' => esc_html__( 'Custom Image', 'trendino' ),
					'text'   => esc_html__( 'Text Logo', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'     => esc_html__( 'Image', 'trendino' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'logo_source' => 'custom' ),
			)
		);

		$this->add_control(
			'logo_text',
			array(
				'label'       => esc_html__( 'Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => get_bloginfo( 'name' ),
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
				'condition'   => array( 'logo_source' => 'text' ),
			)
		);

		$this->add_control(
			'tagline',
			array(
				'label'       => esc_html__( 'Tagline', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'custom_link',
			array(
				'label'       => esc_html__( 'Custom Link', 'trendino' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => home_url( '/' ),
				'dynamic'     => array( 'active' => true ),
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
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-logo__image img' => 'width:{{SIZE}}{{UNIT}};height:auto;',
				),
			)
		);

		$this->add_responsive_control(
			'logo_align',
			array(
				'label'     => esc_html__( 'Alignment', 'trendino' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'trendino' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'trendino' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'trendino' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .trendino-logo' => 'justify-content:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'logo_text_typography',
				'selector'  => '{{WRAPPER}} .trendino-logo__text',
				'condition' => array( 'logo_source' => 'text' ),
			)
		);

		$this->add_control(
			'logo_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-logo__text' => 'color:{{VALUE}};',
				),
				'condition' => array( 'logo_source' => 'text' ),
			)
		);

		$this->add_control(
			'tagline_color',
			array(
				'label'     => esc_html__( 'Tagline Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-logo__tagline' => 'color:{{VALUE}};',
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

		$url    = ! empty( $settings['custom_link']['url'] ) ? $settings['custom_link']['url'] : home_url( '/' );
		$target = ! empty( $settings['custom_link']['is_external'] ) ? ' target="_blank"' : '';
		?>
		<div class="trendino-logo">
			<a class="trendino-logo__link" href="<?php echo esc_url( $url ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( 'text' === $settings['logo_source'] ) : ?>
					<span class="trendino-logo__text"><?php echo esc_html( $settings['logo_text'] ); ?></span>
				<?php elseif ( 'custom' === $settings['logo_source'] && ! empty( $settings['logo_image']['url'] ) ) : ?>
					<span class="trendino-logo__image">
						<img src="<?php echo esc_url( $settings['logo_image']['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="160" height="48" />
					</span>
				<?php elseif ( has_custom_logo() ) : ?>
					<span class="trendino-logo__image"><?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?></span>
				<?php else : ?>
					<span class="trendino-logo__text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $settings['tagline'] ) ) : ?>
					<span class="trendino-logo__tagline"><?php echo esc_html( $settings['tagline'] ); ?></span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
}
