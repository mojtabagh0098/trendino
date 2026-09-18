<?php
/**
 * Announcement bar widget — the dismissible promo strip above the header.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Announcement_Bar
 */
class Announcement_Bar extends Base_Widget {

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
		return 'trendino-announcement-bar';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Announcement Bar', 'trendino' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-header';
	}

	/**
	 * Panel categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-header' );
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'announcement', 'promo', 'notice', 'bar', 'اطلاعیه' );
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
			'text',
			array(
				'label'       => esc_html__( 'Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Free shipping on orders over 500,000 Toman', 'trendino' ),
			)
		);

		$this->add_control(
			'highlight',
			array(
				'label'       => esc_html__( 'Highlighted Text', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Shown in the accent colour before the main text.', 'trendino' ),
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'   => esc_html__( 'Link Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'trendino' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'condition'   => array( 'link_text!' => '' ),
			)
		);

		$this->add_control(
			'dismissible',
			array(
				'label'        => esc_html__( 'Dismissible', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Remembers the choice for 7 days in the visitor browser.', 'trendino' ),
			)
		);

		$this->add_control(
			'storage_key',
			array(
				'label'       => esc_html__( 'Dismiss Key', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'default',
				'description' => esc_html__( 'Change this to show the bar again after editing the message.', 'trendino' ),
				'condition'   => array( 'dismissible' => 'yes' ),
			)
		);

		$this->end_controls_section();

		// --- Style -----------------------------------------------------.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'background',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-announce' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-announce' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_color',
			array(
				'label'     => esc_html__( 'Highlight Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-announce__highlight' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .trendino-announce',
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => esc_html__( 'Min Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-announce__inner' => 'min-height:{{SIZE}}{{UNIT}};',
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

		if ( ! $settings['text'] && ! $settings['highlight'] ) {
			$this->editor_notice( esc_html__( 'Add the announcement text.', 'trendino' ) );

			return;
		}

		$dismissible = 'yes' === $settings['dismissible'];
		$key         = $settings['storage_key'] ? sanitize_key( $settings['storage_key'] ) : 'default';
		?>
		<div class="trendino-announce"
			<?php if ( $dismissible ) : ?>
				data-trendino-announce="<?php echo esc_attr( $key ); ?>" hidden
			<?php endif; ?>>
			<div class="trendino-container trendino-announce__inner">
				<?php if ( $settings['highlight'] ) : ?>
					<span class="trendino-announce__highlight"><?php echo esc_html( $settings['highlight'] ); ?></span>
				<?php endif; ?>

				<?php if ( $settings['text'] ) : ?>
					<span class="trendino-announce__text"><?php echo esc_html( $settings['text'] ); ?></span>
				<?php endif; ?>

				<?php if ( $settings['link_text'] && ! empty( $settings['link']['url'] ) ) : ?>
					<a class="trendino-announce__link" href="<?php echo esc_url( $settings['link']['url'] ); ?>"
						<?php echo ! empty( $settings['link']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
						<?php echo esc_html( $settings['link_text'] ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $dismissible ) : ?>
					<button type="button" class="trendino-announce__close" data-trendino-announce-close
						aria-label="<?php esc_attr_e( 'Dismiss announcement', 'trendino' ); ?>">
						<?php trendino_icon( 'close', array( 'size' => 16 ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
