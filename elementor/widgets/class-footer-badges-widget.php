<?php
/**
 * Footer trust badges / payment icons widget (enamad, samandehi, gateways…).
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Footer_Badges
 */
class Footer_Badges extends Base_Widget {

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
		return 'trendino-footer-badges';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Trust Badges', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image-box';
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
			array( 'label' => esc_html__( 'Badges', 'trendino' ) )
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'label'   => esc_html__( 'Type', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => esc_html__( 'Image', 'trendino' ),
					'html'  => esc_html__( 'HTML / Script Badge', 'trendino' ),
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'     => esc_html__( 'Image', 'trendino' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'type' => 'image' ),
			)
		);

		$repeater->add_control(
			'html',
			array(
				'label'       => esc_html__( 'HTML Code', 'trendino' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'description' => esc_html__( 'Paste the badge code provided by the certificate issuer.', 'trendino' ),
				'condition'   => array( 'type' => 'html' ),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'     => esc_html__( 'Link', 'trendino' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'type' => 'image' ),
			)
		);

		$repeater->add_control(
			'alt',
			array(
				'label'     => esc_html__( 'Alt Text', 'trendino' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array( 'type' => 'image' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Badges', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ alt || type }}}',
				'default'     => array( array(), array() ),
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
			'badge_size',
			array(
				'label'      => esc_html__( 'Badge Size', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 180 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 86 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-badge-item' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'badge_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-badge-item' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'badge_border',
				'selector' => '{{WRAPPER}} .trendino-badge-item',
			)
		);

		$this->add_control(
			'badge_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-badge-item' => 'border-radius:{{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .trendino-badges' => 'gap:{{SIZE}}{{UNIT}};',
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
		<div class="trendino-badges-wrap">
			<?php if ( $settings['title'] ) : ?>
				<span class="trendino-badges__title"><?php echo esc_html( $settings['title'] ); ?></span>
			<?php endif; ?>

			<div class="trendino-badges">
				<?php foreach ( $items as $item ) : ?>
					<?php if ( 'html' === $item['type'] ) : ?>
						<div class="trendino-badge-item">
							<?php echo wp_kses( $item['html'], trendino_badge_allowed_html() ); ?>
						</div>
					<?php elseif ( ! empty( $item['image']['url'] ) ) : ?>
						<?php $has_link = ! empty( $item['url']['url'] ); ?>
						<<?php echo $has_link ? 'a' : 'div'; ?> class="trendino-badge-item"
							<?php if ( $has_link ) : ?>
								href="<?php echo esc_url( $item['url']['url'] ); ?>"
								<?php echo ! empty( $item['url']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
							<?php endif; ?>>
							<img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>" loading="lazy" decoding="async" />
						</<?php echo $has_link ? 'a' : 'div'; ?>>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
