<?php
/**
 * Standalone section heading widget (title + link + optional divider).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Trendino\Elementor\Controls\Section_Header_Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Class Section_Heading
 */
class Section_Heading extends Base_Widget {

	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-section-heading';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Section Heading', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-heading';
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Heading', 'trendino' ) )
		);

		$this->register_section_header_controls(
			array(
				'title'     => esc_html__( 'Section title', 'trendino' ),
				'link_text' => '',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'div'  => 'div',
					'span' => 'span',
				),
			)
		);

		$this->add_control(
			'show_divider',
			array(
				'label'        => esc_html__( 'Show Divider Line', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->register_section_header_style_controls();

		$this->start_controls_section(
			'section_divider_style',
			array(
				'label'     => esc_html__( 'Divider', 'trendino' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_divider' => 'yes' ),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-section-head__divider' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_height',
			array(
				'label'      => esc_html__( 'Thickness', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 1, 'max' => 8 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-section-head__divider' => 'height:{{SIZE}}{{UNIT}};',
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

		if ( 'yes' !== $settings['show_header'] ) {
			return;
		}

		$tag       = in_array( $settings['title_tag'], array( 'h1', 'h2', 'h3', 'h4', 'div', 'span' ), true ) ? $settings['title_tag'] : 'h2';
		$icon_html = '';

		if ( ! empty( $settings['header_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) );
			$icon_html = ob_get_clean();
		}
		?>
		<div class="trendino-section-head is-align-<?php echo esc_attr( $settings['header_align'] ); ?>">
			<div class="trendino-section-head__text">
				<?php if ( $icon_html ) : ?>
					<span class="trendino-section-head__icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<?php if ( $settings['title'] ) : ?>
					<<?php echo esc_attr( $tag ); ?> class="trendino-section-head__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_attr( $tag ); ?>>
				<?php endif; ?>
				<?php if ( $settings['subtitle'] ) : ?>
					<span class="trendino-section-head__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $settings['show_divider'] ) : ?>
				<span class="trendino-section-head__divider" aria-hidden="true"></span>
			<?php endif; ?>

			<?php if ( $settings['link_text'] && ! empty( $settings['link']['url'] ) ) : ?>
				<div class="trendino-section-head__actions">
					<a class="trendino-section-head__link" href="<?php echo esc_url( $settings['link']['url'] ); ?>"
						<?php echo ! empty( $settings['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
						<span><?php echo esc_html( $settings['link_text'] ); ?></span>
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 16 ) ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
