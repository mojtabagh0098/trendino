<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Elementor_TNO_Flash_Deal_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tno_flash_deal';
    }

    public function get_title() {
        return 'پیشنهاد شگفت‌انگیز (محصولات)';
    }

    public function get_icon() {
        return 'eicon-price-list';
    }

    public function get_categories() {
        return [ 'trendino' ];
    }
    
    // فراخوانی اسکریپت‌های مورد نیاز برای اسلایدر
    public function get_script_depends() {
        return [ 'swiper', 'tno-widgets-script' ];
    }

    protected function register_controls() {

        // --- محتوا ---
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'تنظیمات محصولات',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'product_cat',
            [
                'label' => 'دسته‌بندی محصولات',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => array_column( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] ), 'name', 'term_id' ),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => 'تعداد کل محصولات (کویری)',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 8,
            ]
        );

        $this->add_control(
            'show_label',
            [
                'label' => 'نمایش لیبل',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_discount',
            [
                'label' => 'نمایش درصد تخفیف',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // --- تنظیمات اسلایدر ---
        $this->start_controls_section(
            'slider_section',
            [
                'label' => 'تنظیمات اسلایدر',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => 'نمایش بصورت اسلایدر',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => 'تعداد نمایش در هر اسلاید',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5,
                'tablet_default' => 3,
                'mobile_default' => 2,
                'condition' => [ 'enable_carousel' => 'yes' ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => 'پخش خودکار',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [ 'enable_carousel' => 'yes' ],
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => 'حلقه بی‌نهایت',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [ 'enable_carousel' => 'yes' ],
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => 'نمایش فلش‌ها (Navigation)',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [ 'enable_carousel' => 'yes' ],
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => 'نمایش نقطه‌ها (Pagination)',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'condition' => [ 'enable_carousel' => 'yes' ],
            ]
        );

        $this->end_controls_section();

        // --- استایل ---
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'استایل کارت محصول',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg_color',
            [
                'label' => 'رنگ پس‌زمینه کارت',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .prod-card' => 'background-color: {{VALUE}};' ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .prod-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'شعاع لبه (Border Radius)',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [ '{{WRAPPER}} .prod-card' => 'border-radius: {{SIZE}}px;' ],
            ]
        );

        $this->add_control(
            'discount_style_heading',
            [ 'label' => 'استایل تخفیف', 'type' => \Elementor\Controls_Manager::HEADING ]
        );

        $this->add_control(
            'discount_bg_color',
            [
                'label' => 'رنگ پس‌زمینه تخفیف',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .prod-discount' => 'background-color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'discount_text_color',
            [
                'label' => 'رنگ متن تخفیف',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .prod-discount' => 'color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'label_style_heading',
            [ 'label' => 'استایل لیبل (نشان)', 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before' ]
        );

        $this->add_control(
            'label_bg_color',
            [
                'label' => 'رنگ پس‌زمینه لیبل',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .prod-badge' => 'background-color: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'label_text_color',
            [
                'label' => 'رنگ متن لیبل',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .prod-badge' => 'color: {{VALUE}};' ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => 'تایپوگرافی لیبل',
                'selector' => '{{WRAPPER}} .prod-badge',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'title_typography', 'label' => 'تایپوگرافی عنوان', 'selector' => '{{WRAPPER}} .prod-title' ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'price_typography', 'label' => 'تایپوگرافی قیمت', 'selector' => '{{WRAPPER}} .prod-price' ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type'      => 'product',
            'posts_per_page' => $settings['posts_per_page'] ?: 5,
        ];

        if ( ! empty( $settings['product_cat'] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $settings['product_cat'],
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) return;

        $is_carousel = 'yes' === $settings['enable_carousel'];
        $wrapper_id = 'tno-fd-' . $this->get_id();
        
        // تشخیص داینامیک جهت سایت (راست‌چین یا چپ‌چین)
        $dir = is_rtl() ? 'rtl' : 'ltr';
        
        // تولید تنظیمات جاوااسکریپت برای Swiper
        $slider_config = [];
        if ($is_carousel) {
            $slider_config = [
                'slidesPerView' => $settings['slides_to_show_mobile'] ?? 2,
                'spaceBetween' => 12,
                'loop' => 'yes' === $settings['loop'],
                'breakpoints' => [
                    768 => [ 'slidesPerView' => $settings['slides_to_show_tablet'] ?? 3 ],
                    1024 => [ 'slidesPerView' => $settings['slides_to_show'] ?? 5 ],
                ]
            ];
            if ( 'yes' === $settings['autoplay'] ) {
                $slider_config['autoplay'] = [ 'delay' => 3000, 'disableOnInteraction' => false ];
            }
        }
        ?>
        <div class="tno-flash-deal-wrapper" id="<?php echo esc_attr($wrapper_id); ?>">
            <section class="<?php echo $is_carousel ? 'prod-scroll swiper tno-flash-swiper' : 'prod-scroll'; ?>" 
                dir="<?php echo esc_attr($dir); ?>"
                <?php echo $is_carousel ? "data-settings='" . wp_json_encode($slider_config) . "'" : ''; ?>>
                
                <!-- کلاس swiper-wrapper برای اسلایدر -->
                <div class="<?php echo $is_carousel ? 'swiper-wrapper' : 'prod-grid-inner'; ?>">
                    
                    <?php while ( $query->have_posts() ) : $query->the_post(); 
                        global $product;
                        if ( ! is_a( $product, 'WC_Product' ) ) {
                            $product = wc_get_product( get_the_ID() );
                        }
                        
                        $is_on_sale = $product->is_on_sale();
                        $custom_label = get_post_meta( get_the_ID(), 'label', true );
                    ?>
                        <!-- پوشش هر اسلاید -->
                        <div class="<?php echo $is_carousel ? 'swiper-slide' : 'prod-grid-item'; ?>">
                            <div class="prod-card">
                                <?php if ( 'yes' === $settings['show_label'] && ! empty( $custom_label ) ) : ?>
                                    <span class="prod-badge"><?php echo esc_html( $custom_label ); ?></span>
                                <?php endif; ?>

                                <div class="prod-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?>
                                    </a>
                                </div>
                                <div class="prod-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </div>

                                <?php if ( $is_on_sale ) : ?>
                                    <div class="prod-price-row">
                                        <?php if ( 'yes' === $settings['show_discount'] ) : 
                                            $regular_price = $product->get_regular_price();
                                            $sale_price = $product->get_sale_price();
                                            if ( $regular_price > 0 ) {
                                                $percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
                                            } else {
                                                $percentage = 0;
                                            }
                                        ?>
                                            <span class="prod-discount"><?php echo $percentage; ?>%</span>
                                        <?php endif; ?>
                                        <span class="prod-price"><?php echo $product->get_price_html(); ?></span>
                                    </div>
                                <?php else : ?>
                                    <div class="prod-price-row-simple">
                                        <span class="prod-price"><?php echo $product->get_price_html(); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                
                <?php if ( $is_carousel && 'yes' === $settings['show_arrows'] ) : ?>
                    <div class="tno-swiper-btn tno-swiper-prev">
						<svg width="9" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M0.218784 15.2198C0.149102 15.2895 0.0938272 15.3722 0.0561152 15.4632C0.0184031 15.5543 -0.00100803 15.6519 -0.00100803 15.7504C-0.00100803 15.849 0.0184031 15.9465 0.0561152 16.0376C0.0938272 16.1286 0.149102 16.2114 0.218784 16.281C0.288467 16.3507 0.371193 16.406 0.462237 16.4437C0.553283 16.4814 0.650864 16.5008 0.74941 16.5008C0.847956 16.5008 0.945538 16.4814 1.03658 16.4437C1.12763 16.406 1.21035 16.3507 1.28004 16.281L8.78004 8.78104C8.84977 8.71139 8.90509 8.62867 8.94283 8.53762C8.98057 8.44657 9 8.34898 9 8.25042C9 8.15186 8.98057 8.05426 8.94283 7.96321C8.90509 7.87216 8.84977 7.78945 8.78004 7.71979L1.28004 0.219792C1.1393 0.0790615 0.948433 -3.92322e-09 0.74941 0C0.550387 3.92322e-09 0.359515 0.0790615 0.218784 0.219792C0.0780544 0.360523 -0.00100803 0.551394 -0.00100803 0.750417C-0.00100803 0.94944 0.0780544 1.14031 0.218784 1.28104L7.1891 8.25042L0.218784 15.2198Z" fill="#212121"/>
						</svg>
					</div>
                    <div class="tno-swiper-btn tno-swiper-next">
						<svg width="9" height="17" viewBox="0 0 9 17" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M8.78122 15.2198C8.8509 15.2895 8.90617 15.3722 8.94388 15.4632C8.9816 15.5543 9.00101 15.6519 9.00101 15.7504C9.00101 15.849 8.9816 15.9465 8.94388 16.0376C8.90617 16.1286 8.8509 16.2114 8.78122 16.281C8.71153 16.3507 8.62881 16.406 8.53776 16.4437C8.44672 16.4814 8.34914 16.5008 8.25059 16.5008C8.15204 16.5008 8.05446 16.4814 7.96342 16.4437C7.87237 16.406 7.78965 16.3507 7.71996 16.281L0.219965 8.78104C0.150232 8.71139 0.0949136 8.62867 0.0571704 8.53762C0.0194272 8.44657 0 8.34898 0 8.25042C0 8.15186 0.0194272 8.05426 0.0571704 7.96321C0.0949136 7.87216 0.150232 7.78945 0.219965 7.71979L7.71996 0.219792C7.8607 0.0790615 8.05157 -3.92322e-09 8.25059 0C8.44961 3.92322e-09 8.64048 0.0790615 8.78122 0.219792C8.92195 0.360523 9.00101 0.551394 9.00101 0.750417C9.00101 0.94944 8.92195 1.14031 8.78122 1.28104L1.8109 8.25042L8.78122 15.2198Z" fill="#212121"/>
						</svg>
					</div>
                <?php endif; ?>
                
                <?php if ( $is_carousel && 'yes' === $settings['show_dots'] ) : ?>
                    <div class="swiper-pagination"></div>
                <?php endif; ?>

            </section>
        </div>
        <?php
    }
}