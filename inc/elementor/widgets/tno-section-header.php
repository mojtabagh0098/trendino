<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_TNO_Section_Header_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'tno_section_header'; }
	public function get_title() { return 'هدر بخش (ترندینو)'; }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return [ 'trendino' ]; }

	protected function register_controls() {
		// ================= Content =================
		$this->start_controls_section('content_section', [
			'label' => 'محتوا',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('title', [
			'label' => 'عنوان',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'مقالات آموزشی',
		]);

		$this->add_control('title_tag', [
			'label' => 'تگ عنوان',
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'div' => 'div',
			],
			'default' => 'h2',
		]);

		$this->add_control('show_view_all', [
			'label' => 'نمایش دکمه مشاهده همه',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => 'بله',
			'label_off' => 'خیر',
			'return_value' => 'yes',
			'default' => 'yes',
			'separator' => 'before',
		]);

		$this->add_control('view_all_text', [
			'label' => 'متن دکمه',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'مشاهده همه',
			'condition' => [ 'show_view_all' => 'yes' ],
		]);

		$this->add_control('view_all_link', [
			'label' => 'لینک دکمه',
			'type' => \Elementor\Controls_Manager::URL,
			'condition' => [ 'show_view_all' => 'yes' ],
		]);

		$this->end_controls_section();

		// ================= Style =================
		$this->start_controls_section('style_section', [
			'label' => 'رنگ‌بندی دات (نقطه)',
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('dot_inner_color', [
			'label' => 'رنگ مرکزی دات',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#0a1440',
			'selectors' => [
				'{{WRAPPER}} .tno-sh-dot' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('dot_outer_color', [
			'label' => 'رنگ هاله دات',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#93c5fd', // Light blue default
			'selectors' => [
				'{{WRAPPER}} .tno-sh-dot' => 'box-shadow: 0 0 0 5px {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$title_tag = \Elementor\Utils::validate_html_tag( $settings['title_tag'] );
		
		if ( 'yes' === $settings['show_view_all'] && ! empty( $settings['view_all_link']['url'] ) ) {
			$this->add_link_attributes( 'view_all_wrapper', $settings['view_all_link'] );
		}
		?>
		<div class="tno-section-header">
			<div class="tno-sh-right">
				<span class="tno-sh-dot"></span>
				<<?php echo $title_tag; ?> class="tno-sh-title">
					<?php echo esc_html( $settings['title'] ); ?>
				</<?php echo $title_tag; ?>>
			</div>
			
			<div class="tno-sh-divider"></div>
			
			<?php if ( 'yes' === $settings['show_view_all'] ) : ?>
				<?php $wrapper_tag = ! empty( $settings['view_all_link']['url'] ) ? 'a' : 'div'; ?>
				<<?php echo $wrapper_tag; ?> class="tno-sh-viewall" <?php echo $this->get_render_attribute_string( 'view_all_wrapper' ); ?>>
					<span class="tno-sh-text"><?php echo esc_html( $settings['view_all_text'] ); ?></span>
					<span class="tno-sh-icon">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
					</span>
				</<?php echo $wrapper_tag; ?>>
			<?php endif; ?>
		</div>
		<?php
	}
}