<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Elementor_TNO_Box_Title_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'tno_box_title';
    }

    public function get_title() {
        return 'هدر پیشنهاد شگفت‌انگیز';
    }

    public function get_icon() {
        return 'eicon-heading';
    }

    public function get_categories() {
        return [ 'trendino' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'محتوا',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => 'متن عنوان',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'پیشنهاد شگفت انگیز',
            ]
        );

        $this->add_control(
            'icon_type',
            [
                'label' => 'نوع نماد کنار عنوان',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'icon' => 'آیکون',
                    'dot'  => 'نقطه رنگی (هاله‌دار)',
                ],
                'default' => 'icon',
            ]
        );

        $this->add_control(
            'title_icon',
            [
                'label' => 'آیکون عنوان',
                'type' => \Elementor\Controls_Manager::ICONS,
                'condition' => [
                    'icon_type' => 'icon',
                ],
            ]
        );

        $this->add_control(
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
                    'span' => 'span',
                ],
                'default' => 'h2',
            ]
        );

        $this->add_control(
            'show_divider',
            [
                'label' => 'نمایش جداکننده (Divider)',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_offer_time',
            [
                'label' => 'نمایش زمان پیشنهاد',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'offer_end_time',
            [
                'label' => 'زمان پایان',
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'condition' => [
                    'show_offer_time' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'offer_label',
            [
                'label' => 'متن فرصت باقی مانده',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'فرصت باقی مانده',
                'condition' => [
                    'show_offer_time' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_see_all',
            [
                'label' => 'نمایش دکمه مشاهده همه',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'see_all_text',
            [
                'label' => 'متن مشاهده همه',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'مشاهده همه',
                'condition' => [
                    'show_see_all' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'see_all_link',
            [
                'label' => 'لینک مشاهده همه',
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => 'https://your-link.com',
                'condition' => [
                    'show_see_all' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => 'استایل',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => 'رنگ آیکون',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-title-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition' => [
                    'icon_type' => 'icon',
                ],
            ]
        );

        $this->add_control(
            'dot_inner_color',
            [
                'label' => 'رنگ مرکزی نقطه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0a1440',
                'selectors' => [
                    '{{WRAPPER}} .tno-title-dot' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'icon_type' => 'dot',
                ],
            ]
        );

        $this->add_control(
            'dot_outer_color',
            [
                'label' => 'رنگ هاله نقطه',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#93c5fd',
                'selectors' => [
                    '{{WRAPPER}} .tno-title-dot' => 'box-shadow: 0 0 0 5px {{VALUE}};',
                ],
                'condition' => [
                    'icon_type' => 'dot',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'see_more_typography',
                'label' => 'تایپوگرافی دکمه مشاهده بیشتر',
                'selector' => '{{WRAPPER}} .see-all',
                'condition' => [
                    'show_see_all' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'labels_typography',
                'label' => 'تایپوگرافی برچسب‌ها',
                'selector' => '{{WRAPPER}} .offer-label, {{WRAPPER}} .timer-box .label',
                'condition' => [
                    'show_offer_time' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'counter_bg_color',
            [
                'label' => 'رنگ پس‌زمینه شمارنده',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .timer-box' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_offer_time' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $title_tag = \Elementor\Utils::validate_html_tag( $settings['title_tag'] );
        
        if ( 'yes' === $settings['show_see_all'] && ! empty( $settings['see_all_link']['url'] ) ) {
            $this->add_link_attributes( 'see_all_button', $settings['see_all_link'] );
        }
        ?>
        <div class="products-carousel-widget">
            
            <<?php echo $title_tag; ?> class="widget-title amazing-offer">
                <?php if ( 'icon' === $settings['icon_type'] && ! empty( $settings['title_icon']['value'] ) ) : ?>
                    <span class="widget-title-icon">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['title_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php elseif ( 'dot' === $settings['icon_type'] ) : ?>
                    <span class="tno-title-dot" style="display: block; width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0;"></span>
                <?php endif; ?>
                
                <?php echo esc_html( $settings['title_text'] ); ?>
            </<?php echo $title_tag; ?>>
            
            <?php if ( 'yes' === $settings['show_divider'] ) : ?>
                <div class="divider"></div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_offer_time'] ) : ?>
                <div class="offer-time-widget">
                    <span class="offer-label">
                        <?php echo esc_html( $settings['offer_label'] ); ?>
                    </span>
                    
                    <div class="off-timer tno-countdown" data-end-time="<?php echo esc_attr( $settings['offer_end_time'] ); ?>">
                        <span class="timer-box">
                            <span class="value days">00</span>
                            <span class="label">روز</span>
                        </span>
                        :
                        <span class="timer-box">
                            <span class="value hours">00</span>
                            <span class="label">ساعت</span>
                        </span>
                        :
                        <span class="timer-box">
                            <span class="value minutes">00</span>
                            <span class="label">دقیقه</span>
                        </span>
                        :
                        <span class="timer-box">
                            <span class="value seconds">00</span>
                            <span class="label">ثانیه</span>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_see_all'] && ! empty( $settings['see_all_text'] ) ) : ?>
                <?php 
                $link_tag = ! empty( $settings['see_all_link']['url'] ) ? 'a' : 'span';
                ?>
                <<?php echo $link_tag; ?> class="see-all" <?php echo $this->get_render_attribute_string( 'see_all_button' ); ?>>
                    <?php echo esc_html( $settings['see_all_text'] ); ?>
                    <span class="see-all-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14" fill="none">
                            <path d="M7.31768 12.6832C7.37575 12.7412 7.42181 12.8102 7.45324 12.886C7.48466 12.9619 7.50084 13.0432 7.50084 13.1253C7.50084 13.2075 7.48466 13.2888 7.45324 13.3647C7.42181 13.4405 7.37575 13.5095 7.31768 13.5675C7.25961 13.6256 7.19067 13.6717 7.1148 13.7031C7.03893 13.7345 6.95761 13.7507 6.87549 13.7507C6.79337 13.7507 6.71205 13.7345 6.63618 13.7031C6.56031 13.6717 6.49137 13.6256 6.4333 13.5675L0.183304 7.31754C0.125194 7.25949 0.0790947 7.19056 0.047642 7.11469C0.0161893 7.03881 0 6.95748 0 6.87535C0 6.79321 0.0161893 6.71188 0.047642 6.63601C0.0790947 6.56014 0.125194 6.49121 0.183304 6.43316L6.4333 0.18316C6.55058 0.0658846 6.70964 -3.26935e-09 6.87549 0C7.04134 3.26935e-09 7.2004 0.0658846 7.31768 0.18316C7.43496 0.300435 7.50084 0.459495 7.50084 0.625347C7.50084 0.7912 7.43496 0.95026 7.31768 1.06753L1.50909 6.87535L7.31768 12.6832Z" fill="#212121"/>
                        </svg>
                    </span>
                </<?php echo $link_tag; ?>>
            <?php endif; ?>

        </div>
        <?php
    }
}