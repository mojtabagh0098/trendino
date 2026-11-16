<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_TNO_Promo_Banner_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'tno_promo_banner'; }
	public function get_title() { return 'بنر پروموشن عریض'; }
	public function get_icon() { return 'eicon-banner'; }
	public function get_categories() { return [ 'trendino' ]; }

	protected function register_controls() {
		// ================= Content =================
		$this->start_controls_section('content_section', [
			'label' => 'محتوا',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('title', [
			'label' => 'عنوان اصلی',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'تجربه‌ای متفاوت با محصولات ما...',
		]);

		$this->add_control('description', [
			'label' => 'توضیحات',
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'default' => 'ترکیبی از طراحی زیبا، سرعت و عملکرد بی‌نظیر',
		]);

		$this->add_control('badge_text', [
			'label' => 'متن لیبل (مثل Snapp Pay)',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'Snapp Pay 💳',
		]);

		$this->add_control('banner_image', [
			'label' => 'تصویر محصول',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
			],
		]);

		$this->add_control('banner_link', [
			'label' => 'لینک بنر',
			'type' => \Elementor\Controls_Manager::URL,
		]);

		$this->end_controls_section();

		// ================= Style =================
		$this->start_controls_section('style_section', [
			'label' => 'رنگ‌بندی',
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('bg_gradient_start', [
			'label' => 'رنگ شروع گرادیانت',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#0a1440',
			'selectors' => [
				'{{WRAPPER}} .tno-promo-banner' => '--bg-start: {{VALUE}};',
			],
		]);

		$this->add_control('bg_gradient_end', [
			'label' => 'رنگ پایان گرادیانت',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#132878',
			'selectors' => [
				'{{WRAPPER}} .tno-promo-banner' => '--bg-end: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		if ( ! empty( $settings['banner_link']['url'] ) ) {
			$this->add_link_attributes( 'banner_wrapper', $settings['banner_link'] );
		}
		
		$wrapper_tag = ! empty( $settings['banner_link']['url'] ) ? 'a' : 'div';
		?>
		<<?php echo $wrapper_tag; ?> class="tno-promo-banner" <?php echo $this->get_render_attribute_string( 'banner_wrapper' ); ?>>
			
			<?php if ( ! empty( $settings['badge_text'] ) ) : ?>
				<div class="tno-promo-badge"><?php echo esc_html( $settings['badge_text'] ); ?></div>
			<?php endif; ?>

			<div class="tno-promo-content">
				<h3 class="tno-promo-title"><?php echo esc_html( $settings['title'] ); ?></h3>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="tno-promo-desc"><?php echo wp_kses_post( $settings['description'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['banner_image']['url'] ) ) : ?>
				<div class="tno-promo-image">
					<?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'banner_image' ); ?>
				</div>
			<?php endif; ?>

		</<?php echo $wrapper_tag; ?>>
		<?php
	}
}