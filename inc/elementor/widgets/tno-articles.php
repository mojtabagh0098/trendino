<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Elementor_TNO_Articles_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'tno_articles'; }
	public function get_title() { return 'مقالات (ترندینو)'; }
	public function get_icon() { return 'eicon-post-list'; }
	public function get_categories() { return [ 'trendino' ]; }
	
	public function get_script_depends() { return [ 'swiper', 'tno-widgets-script' ]; }

	protected function register_controls() {
		// ================= Content: Source =================
		$this->start_controls_section('content_section', [
			'label' => 'منبع مقالات',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('source', [
			'label' => 'منبع داده‌ها',
			'type' => \Elementor\Controls_Manager::SELECT,
			'options' => [
				'query' => 'نوشته‌های سایت (داینامیک)',
				'manual' => 'ورود دستی',
			],
			'default' => 'query',
		]);

		// Query Controls
		$this->add_control('category', [
			'label' => 'دسته‌بندی (خالی برای همه)',
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'options' => array_column( get_terms( [ 'taxonomy' => 'category', 'hide_empty' => false ] ), 'name', 'term_id' ),
			'condition' => [ 'source' => 'query' ],
		]);

		$this->add_control('posts_per_page', [
			'label' => 'تعداد نمایش',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => 4,
			'condition' => [ 'source' => 'query' ],
		]);

		// Manual Controls
		$repeater = new \Elementor\Repeater();
		$repeater->add_control('title', [
			'label' => 'عنوان/توضیح کوتاه',
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'default' => 'استفاده از حجم صدای مناسب و استراحت دادن به گوش‌ها...',
		]);
		$repeater->add_control('image', [
			'label' => 'تصویر شاخص',
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
		]);
		$repeater->add_control('date', [
			'label' => 'تاریخ',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '۱۲ اسفند ۱۴۰۴',
		]);
		$repeater->add_control('link', [
			'label' => 'لینک',
			'type' => \Elementor\Controls_Manager::URL,
		]);

		$this->add_control('manual_items', [
			'label' => 'لیست مقالات دستی',
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => [
				[ 'title' => 'مقاله تستی اول' ],
				[ 'title' => 'مقاله تستی دوم' ],
				[ 'title' => 'مقاله تستی سوم' ],
				[ 'title' => 'مقاله تستی چهارم' ],
			],
			'title_field' => '{{{ title }}}',
			'condition' => [ 'source' => 'manual' ],
		]);

		// Display Settings
		$this->add_control('show_image', [
			'label' => 'نمایش تصویر شاخص',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'separator' => 'before',
		]);
        
		$this->add_control('show_date', [
			'label' => 'نمایش تاریخ',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
		]);

		$this->end_controls_section();

		// ================= Slider Settings =================
		$this->start_controls_section('slider_section', [
			'label' => 'تنظیمات اسلایدر',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('enable_carousel', [
			'label' => 'نمایش بصورت اسلایدر',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'no',
		]);

		$this->add_responsive_control('slides_to_show', [
			'label' => 'تعداد نمایش اسلاید',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => 4,
			'tablet_default' => 2,
			'mobile_default' => 1,
			'condition' => [ 'enable_carousel' => 'yes' ],
		]);

		$this->add_control('autoplay', [
			'label' => 'پخش خودکار',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'no',
			'condition' => [ 'enable_carousel' => 'yes' ],
		]);

		$this->add_control('loop', [
			'label' => 'حلقه بی‌نهایت',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => [ 'enable_carousel' => 'yes' ],
		]);

		$this->add_control('show_arrows', [
			'label' => 'نمایش فلش‌ها',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'no',
			'condition' => [ 'enable_carousel' => 'yes' ],
		]);

		$this->add_control('show_dots', [
			'label' => 'نمایش نقطه‌ها (Pagination)',
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => [ 'enable_carousel' => 'yes' ],
		]);

		$this->end_controls_section();

		// ================= Style =================
		$this->start_controls_section('style_section', [
			'label' => 'رنگ‌بندی و تایپوگرافی',
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('card_bg', [
			'label' => 'پس‌زمینه کارت',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .tno-article-card' => 'background-color: {{VALUE}};' ],
		]);

		$this->add_control('title_color', [
			'label' => 'رنگ عنوان',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#131313',
			'selectors' => [ '{{WRAPPER}} .tno-article-title' => 'color: {{VALUE}};' ],
		]);

		$this->add_control('meta_color', [
			'label' => 'رنگ تاریخ و آیکون',
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '#8A8FA3',
			'selectors' => [ '{{WRAPPER}} .tno-article-date, {{WRAPPER}} .tno-article-icon' => 'color: {{VALUE}};' ],
		]);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => 'تایپوگرافی عنوان',
				'selector' => '{{WRAPPER}} .tno-article-title',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$is_carousel = 'yes' === $settings['enable_carousel'];
		$wrapper_id = 'tno-art-' . $this->get_id();
		$dir = is_rtl() ? 'rtl' : 'ltr';

		$slider_config = [];
		if ($is_carousel) {
			$slider_config = [
				'slidesPerView' => $settings['slides_to_show_mobile'] ?? 1,
				'spaceBetween' => 15,
				'loop' => 'yes' === $settings['loop'],
				'breakpoints' => [
					768 => [ 'slidesPerView' => $settings['slides_to_show_tablet'] ?? 2 ],
					1024 => [ 'slidesPerView' => $settings['slides_to_show'] ?? 4 ],
				]
			];
			if ( 'yes' === $settings['autoplay'] ) {
				$slider_config['autoplay'] = [ 'delay' => 3000, 'disableOnInteraction' => false ];
			}
		}

		// Prepare Items Array
		$items = [];
		if ( 'query' === $settings['source'] ) {
			$args = [
				'post_type' => 'post',
				'posts_per_page' => $settings['posts_per_page'] ?: 4,
			];
			if ( ! empty( $settings['category'] ) ) {
				$args['category__in'] = $settings['category'];
			}
			$query = new \WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$items[] = [
						'title' => get_the_title(),
						'link' => [ 'url' => get_permalink(), 'is_external' => false, 'nofollow' => false ],
						'date' => get_the_date(),
						'image_html' => get_the_post_thumbnail( get_the_ID(), 'medium' ),
					];
				}
				wp_reset_postdata();
			}
		} else {
			foreach ( $settings['manual_items'] as $item ) {
				$img_html = '';
				if ( ! empty( $item['image']['url'] ) ) {
					$img_html = '<img src="' . esc_url( $item['image']['url'] ) . '" alt="' . esc_attr( $item['title'] ) . '">';
				}
				$items[] = [
					'title' => $item['title'],
					'link' => $item['link'],
					'date' => $item['date'],
					'image_html' => $img_html,
					'_id' => $item['_id'],
				];
			}
		}

		if ( empty( $items ) ) return;
		?>
		
		<div class="tno-articles-wrapper" id="<?php echo esc_attr($wrapper_id); ?>">
			<div class="<?php echo $is_carousel ? 'swiper tno-articles-swiper' : 'tno-articles-grid'; ?>" 
				dir="<?php echo esc_attr($dir); ?>"
				<?php echo $is_carousel ? "data-settings='" . wp_json_encode($slider_config) . "'" : ''; ?>>
				
				<div class="<?php echo $is_carousel ? 'swiper-wrapper' : 'tno-articles-grid-inner'; ?>">
					
					<?php foreach ( $items as $index => $item ) : 
						$wrapper_tag = ! empty( $item['link']['url'] ) ? 'a' : 'div';
						$link_attr = '';
						if ( ! empty( $item['link']['url'] ) ) {
							$link_key = 'link_' . ( isset($item['_id']) ? $item['_id'] : $index );
							$this->add_link_attributes( $link_key, $item['link'] );
							$link_attr = $this->get_render_attribute_string( $link_key );
						}
					?>
						<div class="<?php echo $is_carousel ? 'swiper-slide' : 'tno-article-item'; ?>">
							<<?php echo $wrapper_tag; ?> class="tno-article-card" <?php echo $link_attr; ?>>
								
								<?php if ( 'yes' === $settings['show_image'] && ! empty( $item['image_html'] ) ) : ?>
									<div class="tno-article-img">
										<?php echo $item['image_html']; ?>
									</div>
								<?php endif; ?>

								<div class="tno-article-content">
									<h3 class="tno-article-title"><?php echo wp_kses_post( $item['title'] ); ?></h3>
									
									<div class="tno-article-meta">
										<?php if ( 'yes' === $settings['show_date'] && ! empty( $item['date'] ) ) : ?>
											<span class="tno-article-date"><?php echo esc_html( $item['date'] ); ?></span>
										<?php endif; ?>
                                        <span class="tno-article-icon">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21.0006 12.0004C21.0006 12.1993 20.9216 12.3901 20.7809 12.5307C20.6403 12.6714 20.4495 12.7504 20.2506 12.7504L5.5609 12.7504L11.0312 18.2198C11.1009 18.2895 11.1562 18.3722 11.1939 18.4632C11.2316 18.5543 11.251 18.6519 11.251 18.7504C11.251 18.849 11.2316 18.9465 11.1939 19.0376C11.1562 19.1286 11.1009 19.2114 11.0312 19.281C10.9615 19.3507 10.8788 19.406 10.7878 19.4437C10.6967 19.4814 10.5991 19.5008 10.5006 19.5008C10.402 19.5008 10.3045 19.4814 10.2134 19.4437C10.1224 19.406 10.0396 19.3507 9.96996 19.281L3.21996 12.531C3.15023 12.4614 3.09491 12.3787 3.05717 12.2876C3.01943 12.1966 3 12.099 3 12.0004C3 11.9019 3.01943 11.8043 3.05717 11.7132C3.09491 11.6222 3.15023 11.5394 3.21996 11.4698L9.96996 4.71979C10.1107 4.57906 10.3016 4.5 10.5006 4.5C10.6996 4.5 10.8905 4.57906 11.0312 4.71979C11.1719 4.86052 11.251 5.05139 11.251 5.25042C11.251 5.44944 11.1719 5.64031 11.0312 5.78104L5.5609 11.2504H20.2506C20.4495 11.2504 20.6403 11.3294 20.7809 11.4701C20.9216 11.6107 21.0006 11.8015 21.0006 12.0004Z" fill="#212121"/>
                                            </svg>
										</span>
									</div>
								</div>

							</<?php echo $wrapper_tag; ?>>
						</div>
					<?php endforeach; ?>
					
				</div>
				
				<?php if ( $is_carousel && 'yes' === $settings['show_arrows'] ) : ?>
					<div class="tno-swiper-btn tno-swiper-prev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg></div>
					<div class="tno-swiper-btn tno-swiper-next"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></div>
				<?php endif; ?>
				
				<?php if ( $is_carousel && 'yes' === $settings['show_dots'] ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}