<?php
/**
 * Blog / article cards widget.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Trendino\Elementor\Controls\Carousel_Controls;
use Trendino\Elementor\Controls\Query_Controls;
use Trendino\Elementor\Controls\Section_Header_Controls;
use Trendino\Elementor\Query_Helper;
use Trendino\Elementor\Render_Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Class Blog_Posts
 */
class Blog_Posts extends Base_Widget {

	use Query_Controls;
	use Carousel_Controls;
	use Section_Header_Controls;

	/**
	 * Component assets.
	 *
	 * @var string[]
	 */
	protected $components = array( 'blog', 'carousel', 'section-head' );

	/**
	 * Slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'trendino-blog-posts';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Trendino Blog Posts', 'trendino' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_header',
			array( 'label' => esc_html__( 'Section Header', 'trendino' ) )
		);

		$this->register_section_header_controls(
			array(
				'title'     => esc_html__( 'Trendino magazine', 'trendino' ),
				'link_text' => esc_html__( 'All articles', 'trendino' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array( 'label' => esc_html__( 'Query', 'trendino' ) )
		);

		$this->register_post_query_controls( 4 );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array( 'label' => esc_html__( 'Layout', 'trendino' ) )
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => esc_html__( 'Display Mode', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'     => esc_html__( 'Grid', 'trendino' ),
					'carousel' => esc_html__( 'Carousel', 'trendino' ),
				),
			)
		);

		$this->register_columns_controls(
			array(
				'desktop' => 4,
				'tablet'  => 2,
				'mobile'  => 1.2,
				'max'     => 6,
				'selector'=> '{{WRAPPER}} .trendino-posts',
			)
		);

		$this->register_gap_control( 16, '{{WRAPPER}} .trendino-posts' );

		$this->add_control(
			'image_ratio',
			array(
				'label'   => esc_html__( 'Image Ratio', 'trendino' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => array(
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'1-1'  => '1:1',
					'3-2'  => '3:2',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Arrows', 'trendino' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_mode' => 'carousel' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_elements',
			array( 'label' => esc_html__( 'Card Elements', 'trendino' ) )
		);

		$toggles = array(
			'show_image'    => array( esc_html__( 'Show Image', 'trendino' ), 'yes' ),
			'show_category' => array( esc_html__( 'Show Category', 'trendino' ), 'yes' ),
			'show_excerpt'  => array( esc_html__( 'Show Excerpt', 'trendino' ), 'yes' ),
			'show_date'     => array( esc_html__( 'Show Date', 'trendino' ), 'yes' ),
			'show_author'   => array( esc_html__( 'Show Author', 'trendino' ), '' ),
			'show_comments' => array( esc_html__( 'Show Comment Count', 'trendino' ), '' ),
		);

		foreach ( $toggles as $key => $config ) {
			$this->add_control(
				$key,
				array(
					'label'        => $config[0],
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $config[1],
				)
			);
		}

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => esc_html__( 'Excerpt Length (characters)', 'trendino' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 20,
				'max'       => 300,
				'default'   => 90,
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'   => esc_html__( 'Read More Text', 'trendino' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->end_controls_section();

		$this->register_section_header_style_controls();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Card Style', 'trendino' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => esc_html__( 'Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-post-card' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .trendino-post-card',
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Radius', 'trendino' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-post-card' => 'border-radius:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .trendino-post-card',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'post_title_typography',
				'label'    => esc_html__( 'Title Typography', 'trendino' ),
				'selector' => '{{WRAPPER}} .trendino-post-card__title',
			)
		);

		$this->add_control(
			'post_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-post-card__title a' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'excerpt_typography',
				'label'     => esc_html__( 'Excerpt Typography', 'trendino' ),
				'selector'  => '{{WRAPPER}} .trendino-post-card__excerpt',
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => esc_html__( 'Excerpt Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-post-card__excerpt' => 'color:{{VALUE}};',
				),
				'condition' => array( 'show_excerpt' => 'yes' ),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'Meta Color', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-post-card__meta' => 'color:{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'category_bg',
			array(
				'label'     => esc_html__( 'Category Badge Background', 'trendino' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .trendino-post-card__category' => 'background-color:{{VALUE}};',
				),
				'condition' => array( 'show_category' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Body Padding', 'trendino' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .trendino-post-card__body' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$query    = Query_Helper::posts( $this->get_post_query_args( $settings ) );

		if ( ! $query->have_posts() ) {
			Render_Helper::empty_state( esc_html__( 'No posts found.', 'trendino' ) );

			return;
		}

		$is_carousel = 'carousel' === $settings['display_mode'];

		$classes = array(
			'trendino-posts',
			'trendino-ratio-' . sanitize_html_class( $settings['image_ratio'] ),
			$is_carousel ? 'is-carousel' : 'is-grid',
		);

		$card_settings = array(
			'show_image'     => $settings['show_image'],
			'show_date'      => $settings['show_date'],
			'show_excerpt'   => $settings['show_excerpt'],
			'excerpt_length' => $settings['excerpt_length'],
			'show_category'  => $settings['show_category'],
			'show_author'    => $settings['show_author'],
			'show_comments'  => $settings['show_comments'],
			'link_text'      => $settings['read_more_text'],
		);
		?>
		<div class="trendino-posts-wrap trendino-section-box">
			<?php $this->render_section_header( $settings, $is_carousel && 'yes' === $settings['arrows'] ); ?>

			<div class="trendino-carousel">
				<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php echo $is_carousel ? 'data-trendino-scroller' : ''; ?>>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						trendino_post_card( get_post(), $card_settings );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
		<?php
	}
}
