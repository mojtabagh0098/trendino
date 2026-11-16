<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم به فایل
}

class Elementor_TNO_Categories_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tno_categories';
	}

	public function get_title() {
		return 'دستهبندیهای ترندینو';
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'trendino' ];
	}

	protected function register_controls() {

		// --- بخش محتوا ---
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'محتوا',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'cat_title',
			[
				'label' => 'عنوان',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'عنوان دسته',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'cat_icon',
			[
				'label' => 'تصویر/آیکون',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'cat_link',
			[
				'label' => 'لینک',
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
			]
		);

		$this->add_control(
			'categories_list',
			[
				'label' => 'لیست دستهبندیها',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[ 'cat_title' => 'کنسول بازی' ],
					[ 'cat_title' => 'لپتاپ' ],
				],
				'title_field' => '{{{ cat_title }}}',
			]
		);

		$this->end_controls_section();

		// --- بخش استایل ---
		$this->start_controls_section(
			'style_section',
			[
				'label' => 'استایل',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => 'رنگ پسزمینه کارت',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-cat-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label' => 'شعاع لبه (Border Radius)',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors' => [
					'{{WRAPPER}} .c-cat-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => 'تایپوگرافی عنوان',
				'selector' => '{{WRAPPER}} .c-cat-card__title',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<section class="c-cat-grid">
			<?php foreach ( $settings['categories_list'] as $item ) : 
				$link_url = $item['cat_link']['url'];
				if ( ! empty( $link_url ) ) {
					$this->add_link_attributes( 'cat_link_' . $item['_id'], $item['cat_link'] );
				}
				?>
				<div class="c-cat-card">
					<?php if ( ! empty( $link_url ) ) : ?><a <?php echo $this->get_render_attribute_string( 'cat_link_' . $item['_id'] ); ?>><?php endif; ?>
					<div class="c-cat-card__icon">
						<img src="<?php echo esc_url( $item['cat_icon']['url'] ); ?>" alt="<?php echo esc_attr( $item['cat_title'] ); ?>" />
					</div>
					<span class="c-cat-card__title">
						<?php echo esc_html( $item['cat_title'] ); ?>
					</span>
					<?php if ( ! empty( $link_url ) ) : ?></a><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</section>
		<?php
	}
}