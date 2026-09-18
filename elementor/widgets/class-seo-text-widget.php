<?php
/**
 * Collapsible SEO text block (the long description at the bottom of shop homepages).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

/**
 * Class Seo_Text
 */
class Seo_Text extends Base_Widget {

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
		return 'trendino-seo-text';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino SEO Text', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-area';
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
				'default'     => esc_html__( 'Online shopping with Trendino', 'trendino' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'   => esc_html__( 'Text', 'trendino' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'collapsible',
			array(
				'label'        => esc_html__( 'Collapsible', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'collapsed_height',
			array(
				'label'      => esc_html__( 'Collapsed Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 120 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-seo-text__body' => '--trendino-collapsed:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'collapsible' => 'yes' ),
			)
		);

		$this->add_control(
			'more_text',
			array(
				'label'     => esc_html__( 'Expand Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read more', 'trendino' ),
				'condition' => array( 'collapsible' => 'yes' ),
			)
		);

		$this->add_control(
			'less_text',
			array(
				'label'     => esc_html__( 'Collapse Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Show less', 'trendino' ),
				'condition' => array( 'collapsible' => 'yes' ),
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
			'bg_color',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-seo-text' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-seo-text' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-seo-text' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-seo-text__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-seo-text__title' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Text Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-seo-text__body',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-seo-text__body' => 'color:{{VALUE}};',
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
		$settings    = $this->get_settings_for_display();
		$collapsible = 'yes' === $settings['collapsible'];
		?>
		<section class="trendino-seo-text<?php echo $collapsible ? ' is-collapsible is-collapsed' : ''; ?>" data-trendino-seo-text>
			<?php if ( $settings['title'] ) : ?>
				<h2 class="trendino-seo-text__title"><?php echo esc_html( $settings['title'] ); ?></h2>
			<?php endif; ?>

			<div class="trendino-seo-text__body">
				<?php echo wp_kses_post( $settings['content'] ); ?>
			</div>

			<?php if ( $collapsible ) : ?>
				<button type="button" class="trendino-seo-text__toggle"
					data-more="<?php echo esc_attr( $settings['more_text'] ); ?>"
					data-less="<?php echo esc_attr( $settings['less_text'] ); ?>">
					<span><?php echo esc_html( $settings['more_text'] ); ?></span>
					<?php trendino_icon( 'chevron-down', array( 'size' => 16 ) ); ?>
				</button>
			<?php endif; ?>
		</section>
		<?php
	}
}
