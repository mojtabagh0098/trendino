<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_TNO_Taste_Box_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'tno_taste_box'; }
	public function get_title() { return 'باکس سلیقه (ترندینو)'; }
	public function get_icon() { return 'eicon-gallery-grid'; }
	public function get_categories() { return [ 'trendino' ]; }

	protected function register_controls() {
		// ================= Content =================
		$this->start_controls_section('content_section', [
			'label' => 'محتوای متنی و لینک',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('highlight_text', [
			'label' => 'متن رنگی (مثال: گوشی موبایل)',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'گوشی موبایل',
		]);

		$this->add_control('normal_text_1', [
			'label' => 'متن معمولی ۱ (مثال: براساس)',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'براساس',
		]);

		$this->add_control('normal_text_2', [
			'label' => 'متن معمولی ۲ (خط دوم)',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'سلیقه شما',
		]);

		$this->add_control('box_link', [
			'label' => 'لینک کل باکس',
			'type' => \Elementor\Controls_Manager::URL,
			'placeholder' => 'https://...',
		]);

		$this->end_controls_section();

		// ================= Images =================
		$this->start_controls_section('images_section', [
			'label' => 'تصاویر',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('main_image', [
			'label' => 'تصویر اصلی (داخل باکس متنی)',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		]);

		$this->add_control('grid_image_1', [
			'label' => 'تصویر گرید ۱ (بالا چپ)',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		]);

		$this->add_control('grid_image_2', [
			'label' => 'تصویر گرید ۲ (پایین راست)',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		]);

		$this->add_control('grid_image_3', [
			'label' => 'تصویر گرید ۳ (پایین چپ)',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		]);

		$this->end_controls_section();

		// ================= Style =================
		$this->start_controls_section('style_section', [
			'label' => 'رنگ‌بندی',
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('highlight_color', [
			'label' => 'رنگ متن متمایز',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#ef394e',
			'selectors' => [
				'{{WRAPPER}} .tno-tb-highlight' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('text_color', [
			'label' => 'رنگ متن معمولی',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#333333',
			'selectors' => [
				'{{WRAPPER}} .tno-tb-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('text_box_bg', [
			'label' => 'پس‌زمینه باکس متنی',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#f5f5f5',
			'selectors' => [
				'{{WRAPPER}} .tno-tb-text-cell' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => 'تایپوگرافی',
				'selector' => '{{WRAPPER}} .tno-tb-title',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		if ( ! empty( $settings['box_link']['url'] ) ) {
			$this->add_link_attributes( 'wrapper_link', $settings['box_link'] );
		}
		$wrapper_tag = ! empty( $settings['box_link']['url'] ) ? 'a' : 'div';
		?>
		<<?php echo $wrapper_tag; ?> class="tno-taste-box-wrapper" <?php echo $this->get_render_attribute_string( 'wrapper_link' ); ?>>
			<div class="tno-tb-grid">
				
				<!-- Cell 1: Text Box (Top Right in RTL) -->
				<div class="tno-tb-cell tno-tb-text-cell">
					<div class="tno-tb-title">
						<span class="tno-tb-highlight"><?php echo esc_html( $settings['highlight_text'] ); ?></span> 
						<?php echo esc_html( $settings['normal_text_1'] ); ?>
						<?php echo esc_html( $settings['normal_text_2'] ); ?>
					</div>
					
					<div class="tno-tb-bottom-row">
						<span class="tno-tb-arrow">
							<svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M18.0006 7.50042C18.0006 7.69933 17.9216 7.8901 17.7809 8.03075C17.6403 8.1714 17.4495 8.25042 17.2506 8.25042L2.5609 8.25042L8.03122 13.7198C8.1009 13.7895 8.15617 13.8722 8.19389 13.9632C8.2316 14.0543 8.25101 14.1519 8.25101 14.2504C8.25101 14.349 8.2316 14.4465 8.19389 14.5376C8.15617 14.6286 8.1009 14.7114 8.03122 14.781C7.96153 14.8507 7.87881 14.906 7.78776 14.9437C7.69672 14.9814 7.59914 15.0008 7.50059 15.0008C7.40204 15.0008 7.30446 14.9814 7.21342 14.9437C7.12237 14.906 7.03965 14.8507 6.96996 14.781L0.219965 8.03104C0.150233 7.96139 0.0949134 7.87867 0.0571702 7.78762C0.019427 7.69657 0 7.59898 0 7.50042C0 7.40186 0.019427 7.30426 0.0571702 7.21321C0.0949134 7.12216 0.150233 7.03945 0.219965 6.96979L6.96996 0.219792C7.1107 0.0790615 7.30157 -1.48284e-09 7.50059 0C7.69961 1.48284e-09 7.89048 0.0790615 8.03122 0.219792C8.17195 0.360523 8.25101 0.551394 8.25101 0.750417C8.25101 0.94944 8.17195 1.14031 8.03122 1.28104L2.5609 6.75042L17.2506 6.75042C17.4495 6.75042 17.6403 6.82943 17.7809 6.97009C17.9216 7.11074 18.0006 7.3015 18.0006 7.50042Z" fill="black"/>
							</svg>
						</span>
						<?php if ( ! empty( $settings['main_image']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $settings['main_image']['url'] ); ?>" class="tno-tb-main-img" alt="Product">
						<?php endif; ?>
					</div>
				</div>

				<!-- Cell 2: Top Left -->
				<div class="tno-tb-cell tno-tb-img-cell">
					<?php if ( ! empty( $settings['grid_image_1']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $settings['grid_image_1']['url'] ); ?>" alt="Grid Image 1">
					<?php endif; ?>
				</div>

				<!-- Cell 3: Bottom Right -->
				<div class="tno-tb-cell tno-tb-img-cell">
					<?php if ( ! empty( $settings['grid_image_2']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $settings['grid_image_2']['url'] ); ?>" alt="Grid Image 2">
					<?php endif; ?>
				</div>

				<!-- Cell 4: Bottom Left -->
				<div class="tno-tb-cell tno-tb-img-cell">
					<?php if ( ! empty( $settings['grid_image_3']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $settings['grid_image_3']['url'] ); ?>" alt="Grid Image 3">
					<?php endif; ?>
				</div>

			</div>
		</<?php echo $wrapper_tag; ?>>
		<?php
	}
}