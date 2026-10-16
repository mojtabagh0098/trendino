<?php
/**
 * Promotional boxes widget — tinted panels with text on one side and a product
 * image on the other (the pink/blue trio and the wide dark banner in the design).
 *
 * Distinct from the Banner widget, which is an image-background with overlay text.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Promo_Boxes
 */
class Promo_Boxes extends Base_Widget {

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'banner' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-promo-boxes';
	}

	/**
	 * Panel title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Promotional Boxes', 'trendino' );
	}

	/**
	 * Panel icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-banner';
	}

	/**
	 * Panel categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-home' );
	}

	/**
	 * Keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'promo', 'box', 'banner', 'cta', 'offer', 'پروموشن' );
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_boxes',
			array( 'label' => esc_html__( 'Boxes', 'trendino' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'trendino' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Dive into sound', 'trendino' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => esc_html__( 'Description', 'trendino' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'dynamic'     => array( 'active' => true ),
				'default'     => esc_html__( 'Wireless headphones with active noise cancelling.', 'trendino' ),
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'View', 'trendino' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'trendino' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Product Image', 'trendino' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'background',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fdebee',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-promo__title' => 'color:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .trendino-promo__desc' => 'color:{{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'span',
			array(
				'label'       => esc_html__( 'Width (columns)', 'trendino' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 12,
				'default'     => 4,
				'description' => esc_html__( 'Out of the 12 column grid. Use 12 for a full width banner.', 'trendino' ),
			)
		);

		$repeater->add_control(
			'image_position',
			array(
				'label'   => esc_html__( 'Image Side', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'end',
				'options' => array(
					'end'   => esc_html__( 'End (after text)', 'trendino' ),
					'start' => esc_html__( 'Start (before text)', 'trendino' ),
				),
			)
		);

		$this->add_control(
			'boxes',
			array(
				'label'       => esc_html__( 'Boxes', 'trendino' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title'      => esc_html__( 'Dive into sound', 'trendino' ),
						'background' => '#fdebee',
					),
					array(
						'title'      => esc_html__( 'Every moment, smarter', 'trendino' ),
						'background' => '#eaf0fb',
					),
					array(
						'title'      => esc_html__( 'Never run out of charge', 'trendino' ),
						'background' => '#fdebee',
					),
				),
			)
		);

		$this->end_controls_section();

		// --- Layout ----------------------------------------------------.
		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-promo-boxes' => '--trendino-gap:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => esc_html__( 'Min Height', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 140 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-promo' => 'min-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_scroll',
			array(
				'label'        => esc_html__( 'Swipe On Mobile', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 32 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-promo' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .trendino-promo__title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'selector' => '{{WRAPPER}} .trendino-promo__desc',
			)
		);

		$this->add_control(
			'button_heading',
			array(
				'label'     => esc_html__( 'Button', 'trendino' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-promo__button' => 'background:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Text Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-promo__button' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'Image Width', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array( 'min' => 20, 'max' => 70 ),
					'px' => array( 'min' => 60, 'max' => 400 ),
				),
				'default'    => array( 'unit' => '%', 'size' => 40 ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-promo__media' => 'flex-basis:{{SIZE}}{{UNIT}};',
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
		$boxes    = $settings['boxes'] ?? array();

		if ( empty( $boxes ) ) {
			$this->editor_notice( esc_html__( 'Add at least one promotional box.', 'trendino' ) );

			return;
		}

		$wrap_classes = array( 'trendino-promo-boxes' );

		if ( 'yes' === $settings['mobile_scroll'] ) {
			$wrap_classes[] = 'is-mobile-scroll';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrap_classes ) ); ?>">
			<?php
			foreach ( $boxes as $box ) :
				$has_link = ! empty( $box['link']['url'] );
				$tag      = $has_link ? 'a' : 'div';

				$classes = array(
					'trendino-promo',
					'elementor-repeater-item-' . $box['_id'],
					'span-' . absint( $box['span'] ),
					'image-' . sanitize_html_class( $box['image_position'] ?? 'end' ),
				);

				$attributes = array( 'class' => implode( ' ', $classes ) );

				if ( $has_link ) {
					$attributes['href'] = $box['link']['url'];

					if ( ! empty( $box['link']['is_external'] ) ) {
						$attributes['target'] = '_blank';
						$attributes['rel']    = 'noopener';
					}

					if ( ! empty( $box['link']['nofollow'] ) ) {
						$attributes['rel'] = trim( ( $attributes['rel'] ?? '' ) . ' nofollow' );
					}
				}
				?>
				<<?php echo esc_html( $tag ); ?> <?php echo trendino_build_attributes( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="trendino-promo__text">
						<?php if ( ! empty( $box['title'] ) ) : ?>
							<h3 class="trendino-promo__title"><?php echo esc_html( $box['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $box['description'] ) ) : ?>
							<p class="trendino-promo__desc"><?php echo esc_html( $box['description'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $box['button_text'] ) ) : ?>
							<span class="trendino-promo__button">
								<?php echo esc_html( $box['button_text'] ); ?>
								<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ); ?>
							</span>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $box['image']['url'] ) ) : ?>
						<div class="trendino-promo__media">
							<?php
							if ( ! empty( $box['image']['id'] ) ) {
								echo wp_get_attachment_image(
									(int) $box['image']['id'],
									'trendino-banner-md',
									false,
									array(
										'class'    => 'trendino-promo__img',
										'alt'      => $box['title'] ?? '',
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
							} else {
								printf(
									'<img class="trendino-promo__img" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
									esc_url( $box['image']['url'] ),
									esc_attr( $box['title'] ?? '' )
								);
							}
							?>
						</div>
					<?php endif; ?>
				</<?php echo esc_html( $tag ); ?>>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
