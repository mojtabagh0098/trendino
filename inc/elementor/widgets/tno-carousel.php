<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_TNO_Carousel_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tno_carousel';
	}

	public function get_title() {
		return 'کاروسل ترندینو';
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'trendino' ];
	}

    public function get_script_depends() {
		return [ 'swiper' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => 'محتوا و تنظیمات',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
			'slide_bg',
			[
				'label' => 'پسزمینه اسلاید',
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);
        $repeater->add_control(
			'slide_bg_color',
			[
				'label' => 'رنگ پسزمینه اسلاید',
				'type' => \Elementor\Controls_Manager::COLOR,
			]
		);
        $repeater->add_control(
            'slide_content',
            [
                'label' => 'محتوا',
                'type' => \Elementor\Controls_Manager::WYSIWYG,
            ]
        );
        $this->add_control(
            'slides',
            [
                'label' => 'اسلایدها',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_responsive_control(
			'slides_to_show',
			[
				'label' => 'تعداد نمایش اسلاید',
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
			]
		);

        $this->add_control(
			'loop',
			[
				'label' => 'حلقه بی نهایت',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
			'slide_spacing',
			[
				'label' => 'فاصله اسلایدها (px)',
				'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => ['size' => 20],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100]
                ]
			]
		);

        $this->add_control(
			'show_arrows',
			[
				'label' => 'نمایش دکمههای ناوبری',
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
			'prev_arrow_icon',
			[
				'label' => 'آیکون قبلی',
				'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => ['show_arrows' => 'yes'],
			]
		);

        $this->add_control(
			'next_arrow_icon',
			[
				'label' => 'آیکون بعدی',
				'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => ['show_arrows' => 'yes'],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'style_arrows',
			[
				'label' => 'استایل دکمهها',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['show_arrows' => 'yes'],
			]
		);

        $this->add_control(
			'arrow_color',
			[
				'label' => 'رنگ آیکون',
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'color: {{VALUE}};']
			]
		);

        $this->add_control(
			'arrow_bg_color',
			[
				'label' => 'رنگ پسزمینه',
				'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'background-color: {{VALUE}};']
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'arrow_border',
                'label' => 'حاشیه (Border)',
                'selector' => '{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next',
            ]
        );

        $this->add_control(
			'arrow_border_radius',
			[
				'label' => 'شعاع حاشیه',
				'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => ['{{WRAPPER}} .swiper-button-prev, {{WRAPPER}} .swiper-button-next' => 'border-radius: {{SIZE}}{{UNIT}};']
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
			'style_slides',
			[
				'label' => 'استایل اسلایدها',
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'slide_border_radius',
			[
				'label' => 'شعاع حاشیه اسلاید',
				'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => ['{{WRAPPER}} .tno-carousel-slide' => 'border-radius: {{SIZE}}{{UNIT}};']
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        $slides = $settings['slides'];
        $slides_to_show = $settings['slides_to_show'];
        $loop = 'yes' === $settings['loop'] ? 'true' : 'false';
        $spacing = $settings['slide_spacing']['size'];

		$id = 'tno-carousel-' . $this->get_id();

        $swiper_settings = [
            'slidesPerView' => $slides_to_show,
            'spaceBetween' => $spacing,
            'loop' => 'yes' === $settings['loop'],
            'autoHeight' => true,
            'navigation' => [
                'nextEl' => '#' . $id . ' .swiper-button-next',
                'prevEl' => '#' . $id . ' .swiper-button-prev',
            ],
        ];

		?>
		<div id="<?php echo esc_attr($id); ?>" class="tno-carousel swiper" data-settings='<?php echo json_encode($swiper_settings); ?>'>
            <div class="swiper-wrapper">
                <?php foreach($slides as $slide): 
                    $has_bg = !empty($slide['slide_bg']['url']);
                    $style = '';
                    if($has_bg) $style .= 'background-image: url('.esc_url($slide['slide_bg']['url']).'); background-size: cover; background-position: center;';
                    if(!empty($slide['slide_bg_color'])) $style .= 'background-color: '.esc_attr($slide['slide_bg_color']).';';
                ?>
                    <div class="swiper-slide tno-carousel-slide" style="<?php echo $style; ?>">
                        <?php if($has_bg): ?>
                            <img src="<?php echo esc_url($slide['slide_bg']['url']); ?>" style="visibility: hidden; width: 100%; display: block;" alt="">
                        <?php endif; ?>
                        <div class="tno-carousel-slide-content" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            <?php echo $slide['slide_content']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if('yes' === $settings['show_arrows']): ?>
                <div class="swiper-button-prev">
                    <?php \Elementor\Icons_Manager::render_icon($settings['prev_arrow_icon']); ?>
                </div>
                <div class="swiper-button-next">
                    <?php \Elementor\Icons_Manager::render_icon($settings['next_arrow_icon']); ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            jQuery(window).on('elementor/frontend/init', function() {
                elementorFrontend.hooks.addAction('frontend/element_ready/tno_carousel.default', function($scope) {
                    var $swiperContainer = $scope.find('.tno-carousel');
                    if ($swiperContainer.length) {
                        var settings = $swiperContainer.data('settings');
                        new Swiper($swiperContainer[0], settings);
                    }
                });
            });
        </script>
		<?php
	}
}