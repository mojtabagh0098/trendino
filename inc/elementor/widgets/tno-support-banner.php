<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

/**
 * Trendino Support CTA Banner Widget (دریافت پشتیبانی بدون معطلی).
 */
class TNO_Widget_Support_Banner extends Widget_Base {

    public function get_name() {
        return 'tno_support_banner';
    }

    public function get_title() {
        return 'بنر پشتیبانی بدون معطلی ترندینو';
    }

    public function get_icon() {
        return 'eicon-call-to-action';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'support', 'cta', 'banner', 'contact', 'پشتیبانی', 'بنر', 'ارتباط', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'محتوای بنر پشتیبانی',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'use_custom_icon',
            array(
                'label'        => 'انتخاب آیکون دلخواه',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'no',
                'description'  => 'در صورت غیرفعال بودن، آیکون پیش‌فرض گفتگوی پشتیبانی نمایش داده می‌شود.',
            )
        );

        $this->add_control(
            'banner_icon',
            array(
                'label'     => 'آیکون',
                'type'      => Controls_Manager::ICONS,
                'default'   => array(
                    'value'   => 'far fa-comments',
                    'library' => 'fa-regular',
                ),
                'condition' => array( 'use_custom_icon' => 'yes' ),
            )
        );

        $this->add_control(
            'show_rings',
            array(
                'label'        => 'نمایش حلقه‌های اطراف آیکون',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'title_text',
            array(
                'label'       => 'عنوان بنر',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'دریافت پشتیبانی بدون معطلی !',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'description_text',
            array(
                'label'   => 'توضیحات',
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 3,
                'default' => 'برای دریافت راهنمایی، رفع مشکلات یا پاسخ به پرسش‌های خود، می‌توانید از طریق این بخش به‌صورت مستقیم و بدون معطلی با تیم پشتیبانی در ارتباط باشید.',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label'   => 'متن دکمه',
                'type'    => Controls_Manager::TEXT,
                'default' => 'ارتباط با پشتیبانی',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'button_link',
            array(
                'label'       => 'لینک دکمه',
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://trendino.ir/contact',
                'default'     => array( 'url' => '#' ),
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'show_button_arrow',
            array(
                'label'        => 'نمایش فلش داخل دکمه',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - BOX & ICON
         * ========================================================= */
        $this->start_controls_section(
            'section_style_box',
            array(
                'label' => 'استایل باکس و آیکون',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'box_bg',
            array(
                'label'     => 'رنگ پس‌زمینه بنر',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#002460',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_responsive_control(
            'box_padding',
            array(
                'label'      => 'فاصله داخلی (Padding)',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-support-banner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'box_radius',
            array(
                'label'     => 'گردی گوشه بنر',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-support-banner' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .tno-support-banner',
            )
        );

        $this->add_control(
            'icon_circle_bg',
            array(
                'label'     => 'رنگ پس‌زمینه دایره آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__icon-circle' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'icon_color',
            array(
                'label'     => 'رنگ آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#002460',
                'selectors' => array(
                    '{{WRAPPER}} .tno-support-banner__icon-circle'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-support-banner__icon-circle svg' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - TEXTS & BUTTON
         * ========================================================= */
        $this->start_controls_section(
            'section_style_texts',
            array(
                'label' => 'استایل متن‌ها و دکمه',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => 'رنگ عنوان',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .tno-support-banner__title',
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => 'رنگ توضیحات',
                'type'      => Controls_Manager::COLOR,
                'default'   => 'rgba(255, 255, 255, 0.82)',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__desc' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'label'    => 'تایپوگرافی توضیحات',
                'selector' => '{{WRAPPER}} .tno-support-banner__desc',
            )
        );

        $this->start_controls_tabs(
            'tabs_button_style',
            array( 'separator' => 'before' )
        );

        $this->start_controls_tab(
            'tab_btn_normal',
            array( 'label' => 'دکمه (عادی)' )
        );

        $this->add_control(
            'btn_bg',
            array(
                'label'     => 'رنگ پس‌زمینه دکمه',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__btn' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'btn_color',
            array(
                'label'     => 'رنگ متن دکمه',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__btn' => 'color: {{VALUE}};' ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_btn_hover',
            array( 'label' => 'دکمه (هاور)' )
        );

        $this->add_control(
            'btn_hover_bg',
            array(
                'label'     => 'رنگ پس‌زمینه دکمه (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F3F4F6',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__btn:hover' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'btn_hover_color',
            array(
                'label'     => 'رنگ متن دکمه (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => array( '{{WRAPPER}} .tno-support-banner__btn:hover' => 'color: {{VALUE}};' ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'btn_typography',
                'label'    => 'تایپوگرافی دکمه',
                'selector' => '{{WRAPPER}} .tno-support-banner__btn',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $use_custom = ( 'yes' === ( $settings['use_custom_icon'] ?? 'no' ) );
        $show_rings = ( 'yes' === ( $settings['show_rings'] ?? 'yes' ) );
        $show_arrow = ( 'yes' === ( $settings['show_button_arrow'] ?? 'yes' ) );
        $btn_text   = $settings['button_text'] ?? '';

        $this->add_link_attributes( 'support_btn', $settings['button_link'] ?? array( 'url' => '#' ) );
        ?>
        <section class="tno-support-banner" dir="rtl">
            <div class="tno-support-banner__content">
                <div class="tno-support-banner__icon-wrap <?php echo $show_rings ? 'has-rings' : ''; ?>" aria-hidden="true">
                    <?php if ( $show_rings ) : ?>
                        <span class="tno-support-banner__ring tno-support-banner__ring--1"></span>
                        <span class="tno-support-banner__ring tno-support-banner__ring--2"></span>
                        <span class="tno-support-banner__ring tno-support-banner__ring--3"></span>
                    <?php endif; ?>
                    <span class="tno-support-banner__icon-circle">
                        <?php if ( $use_custom && ! empty( $settings['banner_icon']['value'] ) ) : ?>
                            <?php Icons_Manager::render_icon( $settings['banner_icon'], array( 'aria-hidden' => 'true' ) ); ?>
                        <?php else : ?>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                <circle cx="9.5" cy="11.5" r="0.8" fill="currentColor" stroke="none"/>
                                <circle cx="12.5" cy="11.5" r="0.8" fill="currentColor" stroke="none"/>
                                <circle cx="15.5" cy="11.5" r="0.8" fill="currentColor" stroke="none"/>
                            </svg>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="tno-support-banner__texts">
                    <?php if ( ! empty( $settings['title_text'] ) ) : ?>
                        <h3 class="tno-support-banner__title"><?php echo esc_html( $settings['title_text'] ); ?></h3>
                    <?php endif; ?>

                    <?php if ( ! empty( $settings['description_text'] ) ) : ?>
                        <p class="tno-support-banner__desc"><?php echo nl2br( esc_html( $settings['description_text'] ) ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( $btn_text !== '' ) : ?>
                <a <?php $this->print_render_attribute_string( 'support_btn' ); ?> class="tno-support-banner__btn">
                    <span><?php echo esc_html( $btn_text ); ?></span>
                    <?php if ( $show_arrow ) : ?>
                        <svg class="tno-support-banner__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </section>
        <?php
    }
}
