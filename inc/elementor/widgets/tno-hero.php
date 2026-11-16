<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم به فایل
}

class Elementor_TNO_Hero_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tno_hero';
	}

	public function get_title() {
		return 'هیرو ترندینو';
	}

	public function get_icon() {
		return 'eicon-t-letter';
	}

	public function get_categories() {
		return [ 'trendino' ];
	}

	public function get_style_depends() {
		return [ 'elementor-frontend' ]; 
	}

	public function get_script_depends() {
		return [ 'swiper', 'tno-widgets-script' ];
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
			'card_title',
			[
				'label' => 'عنوان',
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'نسل جدید گوشی های پرچمدار',
			]
		);

		$repeater->add_control(
			'title_tag',
			[
				'label' => 'تگ عنوان',
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$repeater->add_control(
			'content_alignment',
			[
				'label' => 'موقعیت محتوا',
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'top-left'      => 'بالا چپ',
					'top-center'    => 'بالا وسط',
					'top-right'     => 'بالا راست',
					'middle-left'   => 'وسط چپ',
					'middle-center' => 'وسط وسط',
					'middle-right'  => 'وسط راست',
					'bottom-left'   => 'پایین چپ',
					'bottom-center' => 'پایین وسط',
					'bottom-right'  => 'پایین راست',
				],
				'default' => 'middle-right',
			]
		);

		$repeater->add_control(
			'text_align',
			[
				'label' => 'تراز متن',
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'right' => [
						'title' => 'راست',
						'icon' => 'eicon-text-align-right',
					],
					'center' => [
						'title' => 'وسط',
						'icon' => 'eicon-text-align-center',
					],
					'left' => [
						'title' => 'چپ',
						'icon' => 'eicon-text-align-left',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-hero__title, {{WRAPPER}} {{CURRENT_ITEM}} .c-hero__subtitle' => 'text-align: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'inner_alignment',
			[
				'label' => 'تراز داخلی محتوا (align-items)',
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => 'شروع',
						'icon' => 'eicon-align-start-h',
					],
					'center' => [
						'title' => 'وسط',
						'icon' => 'eicon-align-center-h',
					],
					'flex-end' => [
						'title' => 'پایان',
						'icon' => 'eicon-align-end-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-hero__content' => 'display: flex; flex-direction: column; align-items: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'content_gap',
			[
				'label' => 'فاصله بین عناصر محتوا',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-hero__content' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'button_top_margin',
			[
				'label' => 'فاصله دکمه از بالا',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-hero__btn' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$repeater->add_control(
			'card_subtitle',
			[
				'label' => 'زیر عنوان (اختیاری)',
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'card_image',
			[
				'label' => 'تصویر شاخص اسلاید',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'description' => 'این تصویر در کنار محتوای متنی نمایش داده میشود.',
			]
		);

		$repeater->add_control(
			'show_button',
			[
				'label' => 'نمایش دکمه',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'بله',
				'label_off' => 'خیر',
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => 'متن دکمه',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'مشاهده',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'button_icon',
			[
				'label' => 'آیکون دکمه',
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-chevron-left',
					'library' => 'solid',
				],
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label' => 'لینک دکمه',
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'bg_type',
			[
				'label' => 'نوع پسزمینه',
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'color' => 'رنگ',
					'image' => 'تصویر',
					'gradient' => 'گرادیانت',
				],
				'default' => 'color',
			]
		);

		$repeater->add_control(
			'bg_color',
			[
				'label' => 'رنگ پسزمینه',
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#E81327',
				'condition' => [
					'bg_type' => 'color',
				],
			]
		);

		$repeater->add_control(
			'bg_image',
			[
				'label' => 'تصویر پسزمینه',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'bg_type' => 'image',
				],
			]
		);

		$repeater->add_control(
			'bg_gradient_color_a',
			[
				'label' => 'رنگ اول گرادیانت',
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#E81327',
				'condition' => [
					'bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'bg_gradient_color_b',
			[
				'label' => 'رنگ دوم گرادیانت',
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#0000FF',
				'condition' => [
					'bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'bg_gradient_angle',
			[
				'label' => 'زاویه گرادیانت',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'deg' ],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'deg',
					'size' => 135,
				],
				'condition' => [
					'bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'min_height',
			[
				'label' => 'حداقل ارتفاع اسلاید',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'enable_carousel',
			[
				'label' => 'فعالسازی کاروسل',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'بله',
				'label_off' => 'خیر',
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => 'حلقه بی پایان',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'بله',
				'label_off' => 'خیر',
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'enable_carousel' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'label' => 'نمایش دکمههای ناوبری',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'condition' => [ 'enable_carousel' => 'yes' ],
				'default' => 'yes',
			]
		);

		$this->add_control(
			'prev_arrow_icon',
			[
				'label' => 'آیکون قبلی',
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-chevron-left', 'library' => 'solid' ],
				'condition' => [ 'enable_carousel' => 'yes', 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'next_arrow_icon',
			[
				'label' => 'آیکون بعدی',
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-chevron-right', 'library' => 'solid' ],
				'condition' => [ 'enable_carousel' => 'yes', 'show_arrows' => 'yes' ],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => 'نمایش صفحهبندی (Dots)',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'condition' => [ 'enable_carousel' => 'yes' ],
				'default' => 'yes',
			]
		);

		$this->add_control(
			'slides',
			[
				'label' => 'اسلایدها',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'card_title' => 'نسل جدید گوشی های پرچمدار، جایی که نوآوری، قدرت و زیبایی بهم میرسند',
						'bg_color' => '#E81327',
					],
				],
				'title_field' => '{{{ card_title }}}',
			]
		);

		$this->end_controls_section();

		// --- بخش استایل دکمه ---
		$this->start_controls_section(
			'style_title_section',
			[
				'label' => 'استایل عنوان',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .c-hero__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => 'رنگ عنوان',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-hero__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_subtitle_section',
			[
				'label' => 'استایل زیرعنوان',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .c-hero__subtitle',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => 'رنگ زیرعنوان',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-hero__subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_button_section',
			[
				'label' => 'استایل دکمه',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .c-hero__btn, {{WRAPPER}} .c-hero__circ-btn',
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => 'رنگ متن دکمه',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-hero__btn, {{WRAPPER}} .c-hero__circ-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => 'رنگ پسزمینه دکمه',
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-hero__btn, {{WRAPPER}} .c-hero__circ-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => 'پدینگ دکمه',
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .c-hero__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => 'شعاع حاشیه دکمه',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .c-hero__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$is_carousel = ( 'yes' === $settings['enable_carousel'] );
		$loop = ( 'yes' === $settings['loop'] ) ? 'true' : 'false';
		?>
		<section class="c-hero <?php echo $is_carousel ? 'swiper tno-hero-swiper' : ''; ?>" data-loop="<?php echo esc_attr( $loop ); ?>">
			<div class="<?php echo $is_carousel ? 'swiper-wrapper' : ''; ?>">
				<?php foreach ( $settings['slides'] as $index => $item ) : 
					$card_class = 'c-hero__card elementor-repeater-item-' . esc_attr( $item['_id'] );
					
					// تنظیمات چیدمان محتوا
					$alignment_map = [
						'top-left'      => [ 'justify-content' => 'flex-start', 'align-items' => 'flex-start', 'text-align' => 'right' ],
						'top-center'    => [ 'justify-content' => 'flex-start', 'align-items' => 'center', 'text-align' => 'center' ],
						'top-right'     => [ 'justify-content' => 'flex-start', 'align-items' => 'flex-end', 'text-align' => 'left' ],
						'middle-left'   => [ 'justify-content' => 'center', 'align-items' => 'flex-start', 'text-align' => 'right' ],
						'middle-center' => [ 'justify-content' => 'center', 'align-items' => 'center', 'text-align' => 'center' ],
						'middle-right'  => [ 'justify-content' => 'center', 'align-items' => 'flex-end', 'text-align' => 'left' ],
						'bottom-left'   => [ 'justify-content' => 'flex-end', 'align-items' => 'flex-start', 'text-align' => 'right' ],
						'bottom-center' => [ 'justify-content' => 'flex-end', 'align-items' => 'center', 'text-align' => 'center' ],
						'bottom-right'  => [ 'justify-content' => 'flex-end', 'align-items' => 'flex-end', 'text-align' => 'left' ],
					];

					$align_config = isset( $alignment_map[ $item['content_alignment'] ] ) ? $alignment_map[ $item['content_alignment'] ] : $alignment_map['middle-right'];

					$card_style = '';
					$card_style .= 'justify-content: ' . $align_config['justify-content'] . ';';
					$card_style .= 'align-items: ' . $align_config['align-items'] . ';';
					
					// If custom text align is not set, use default from map
					if ( empty( $item['text_align'] ) ) {
						$card_style .= 'text-align: ' . $align_config['text-align'] . ';';
					}

					if ( 'color' === $item['bg_type'] && ! empty( $item['bg_color'] ) ) {
						$card_style .= 'background-color: ' . esc_attr( $item['bg_color'] ) . ';';
					} elseif ( 'image' === $item['bg_type'] && ! empty( $item['bg_image']['url'] ) ) {
						$card_style .= 'background-image: url(\'' . esc_url( $item['bg_image']['url'] ) . '\'); background-size: cover; background-position: center;';
					} elseif ( 'gradient' === $item['bg_type'] ) {
						$color_a = ! empty( $item['bg_gradient_color_a'] ) ? $item['bg_gradient_color_a'] : '#E81327';
						$color_b = ! empty( $item['bg_gradient_color_b'] ) ? $item['bg_gradient_color_b'] : '#0000FF';
						$angle = isset( $item['bg_gradient_angle']['size'] ) ? $item['bg_gradient_angle']['size'] : 135;
						$card_style .= 'background: linear-gradient(' . esc_attr( $angle ) . 'deg, ' . esc_attr( $color_a ) . ' 0%, ' . esc_attr( $color_b ) . ' 100%);';
					}

					$title_tag = ! empty( $item['title_tag'] ) ? $item['title_tag'] : 'h3';
					$has_image = ! empty( $item['card_image']['url'] );
					?>
					<div class="<?php echo esc_attr($card_class . ($is_carousel ? ' swiper-slide' : '') . ($has_image ? ' has-image' : '')); ?>" style="<?php echo $card_style; ?>">
						<div class="c-hero__inner">
							<?php 
							$content_style = '';
							if ( ! empty( $item['inner_alignment'] ) ) {
								$content_style .= ' align-items: ' . esc_attr( $item['inner_alignment'] ) . ';';
							}
							
							$gap = isset( $item['content_gap']['size'] ) ? $item['content_gap']['size'] : 15;
							$gap_unit = isset( $item['content_gap']['unit'] ) ? $item['content_gap']['unit'] : 'px';
							$content_style .= ' gap: ' . $gap . $gap_unit . ';';
							?>
							<div class="c-hero__content" style="<?php echo $content_style; ?>">
								<<?php echo esc_attr( $title_tag ); ?> class="c-hero__title"><?php echo esc_html( $item['card_title'] ); ?></<?php echo esc_attr( $title_tag ); ?>>
							
								<?php if ( ! empty( $item['card_subtitle'] ) ) : ?>
									<p class="c-hero__subtitle"><?php echo esc_html( $item['card_subtitle'] ); ?></p>
								<?php endif; ?>

								<?php if ( 'yes' === $item['show_button'] ) : ?>
									<?php 
									if ( ! empty( $item['button_link']['url'] ) ) {
										$this->add_link_attributes( 'button_link_' . $item['_id'], $item['button_link'] );
									}
									
									$btn_style = '';
									if ( isset( $item['button_top_margin']['size'] ) ) {
										$btn_style = 'style="margin-top: ' . esc_attr( $item['button_top_margin']['size'] . $item['button_top_margin']['unit'] ) . ';"';
									}
									?>
									<a class="c-hero__btn" <?php echo $this->get_render_attribute_string( 'button_link_' . $item['_id'] ); ?> <?php echo $btn_style; ?>>
										<?php echo esc_html( $item['button_text'] ); ?>
										<?php if ( ! empty( $item['button_icon']['value'] ) ) : ?>
											<?php \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
										<?php endif; ?>
									</a>
								<?php endif; ?>
							</div>

							<?php if ( $has_image ) : ?>
								<div class="c-hero__media">
									<?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, 'card_image', 'card_image' ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $is_carousel ) : ?>
				<?php if ( 'yes' === $settings['show_arrows'] ) : ?>
					<div class="swiper-navigation">
						<div class="swiper-button-prev">
							<?php \Elementor\Icons_Manager::render_icon( $settings['prev_arrow_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</div>
						<div class="swiper-button-next">
							<?php \Elementor\Icons_Manager::render_icon( $settings['next_arrow_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_pagination'] ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			<?php endif; ?>
		</section>
		<?php
	}
}