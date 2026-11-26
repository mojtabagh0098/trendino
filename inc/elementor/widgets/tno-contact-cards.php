<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

/**
 * Trendino Contact / Info 3-Cards Grid Widget.
 */
class TNO_Widget_Contact_Cards extends Widget_Base {

    public function get_name() {
        return 'tno_contact_cards';
    }

    public function get_title() {
        return 'کارت‌های اطلاعات تماس ترندینو';
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'contact', 'info', 'cards', 'address', 'phone', 'hours', 'تماس', 'آدرس', 'ساعت کاری', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'کارت‌های اطلاعات',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'card_icon',
            array(
                'label'   => 'آیکون کارت',
                'type'    => Controls_Manager::ICONS,
                'default' => array(
                    'value'   => 'far fa-clock',
                    'library' => 'fa-regular',
                ),
            )
        );

        $repeater->add_control(
            'card_title',
            array(
                'label'       => 'عنوان کارت',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'ساعت کاری',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_prefix',
            array(
                'label'       => 'متن فرعی / پیش‌وند مقدار (اختیاری)',
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => 'مثلاً: شنبه تا پنجشنبه :',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_value',
            array(
                'label'       => 'متن اصلی (پررنگ)',
                'type'        => Controls_Manager::TEXT,
                'default'     => '۹ الی ۱۷:۳۰',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_value_accent',
            array(
                'label'       => 'بخش رنگی انتهای مقدار (اختیاری)',
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => 'مثلاً: – ۰۹۱۲',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_value_link',
            array(
                'label'       => 'لینک متن اصلی (اختیاری)',
                'type'        => Controls_Manager::URL,
                'placeholder' => 'tel:09121234567',
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'show_action_badge',
            array(
                'label'        => 'نمایش دکمه/برچسب گوشه چپ',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $repeater->add_control(
            'action_badge_text',
            array(
                'label'     => 'متن دکمه گوشه چپ',
                'type'      => Controls_Manager::TEXT,
                'default'   => 'مسیریابی',
                'condition' => array( 'show_action_badge' => 'yes' ),
                'dynamic'   => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'action_badge_icon',
            array(
                'label'     => 'آیکون دکمه گوشه چپ',
                'type'      => Controls_Manager::ICONS,
                'default'   => array(
                    'value'   => 'fas fa-map-marker-alt',
                    'library' => 'fa-solid',
                ),
                'condition' => array( 'show_action_badge' => 'yes' ),
            )
        );

        $repeater->add_control(
            'action_badge_link',
            array(
                'label'       => 'لینک دکمه گوشه چپ',
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://maps.google.com/...',
                'condition'   => array( 'show_action_badge' => 'yes' ),
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_footer_text',
            array(
                'label'       => 'متن پایین کارت',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'ساعت کاری مجموعه در طول هفته',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'show_footer_dot',
            array(
                'label'        => 'نمایش نقطه رنگی کنار متن پایین',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $repeater->add_control(
            'footer_dot_color',
            array(
                'label'     => 'رنگ نقطه پایین',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#3B82F6',
                'condition' => array( 'show_footer_dot' => 'yes' ),
            )
        );

        $this->add_control(
            'cards_list',
            array(
                'label'       => 'لیست کارت‌ها',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ card_title }}}',
                'default'     => array(
                    array(
                        'card_icon'        => array( 'value' => 'far fa-clock', 'library' => 'fa-regular' ),
                        'card_title'       => 'ساعت کاری',
                        'card_prefix'      => 'شنبه تا پنجشنبه :',
                        'card_value'       => '۹ الی ۱۷:۳۰',
                        'card_footer_text' => 'ساعت کاری مجموعه در طول هفته',
                    ),
                    array(
                        'card_icon'         => array( 'value' => 'fas fa-phone-alt', 'library' => 'fa-solid' ),
                        'card_title'        => 'راه های ارتباطی',
                        'card_value'        => '۱۲۳۴۵۶۷',
                        'card_value_accent' => '– ۰۹۱۲',
                        'card_footer_text'  => 'در ساعات کاری پاسخگوی شما هستیم',
                    ),
                    array(
                        'card_icon'         => array( 'value' => 'far fa-map', 'library' => 'fa-regular' ),
                        'card_title'        => 'نشانی فروشگاه',
                        'card_value'        => 'تهران ، میدان فردوسی خیابان ایرانشهر',
                        'show_action_badge' => 'yes',
                        'action_badge_text' => 'مسیریابی',
                        'card_footer_text'  => 'با آغوش باز پذیرای شما هستیم',
                        'show_footer_dot'   => 'yes',
                        'footer_dot_color'  => '#3B82F6',
                    ),
                ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'          => 'تعداد ستون',
                'type'           => Controls_Manager::SELECT,
                'default'        => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '۱ ستون',
                    '2' => '۲ ستون',
                    '3' => '۳ ستون',
                    '4' => '۴ ستون',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .tno-contact-cards__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ),
            )
        );

        $this->add_responsive_control(
            'grid_gap',
            array(
                'label'     => 'فاصله بین کارت‌ها',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-contact-cards__grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - CARD
         * ========================================================= */
        $this->start_controls_section(
            'section_style_card',
            array(
                'label' => 'استایل کارت',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کارت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => 'فاصله داخلی (Padding)',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-contact-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_radius',
            array(
                'label'     => 'گردی گوشه کارت',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-contact-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_shadow',
                'selector' => '{{WRAPPER}} .tno-contact-card',
            )
        );

        $this->add_control(
            'divider_color',
            array(
                'label'     => 'رنگ خط چین جداکننده',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#E5E7EB',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__divider' => 'border-top-color: {{VALUE}};' ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - ICON
         * ========================================================= */
        $this->start_controls_section(
            'section_style_icon',
            array(
                'label' => 'استایل آیکون کارت',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'tabs_icon_style' );

        $this->start_controls_tab(
            'tab_icon_normal',
            array( 'label' => 'عادی' )
        );

        $this->add_control(
            'icon_bg',
            array(
                'label'     => 'رنگ پس‌زمینه آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F5F6F8',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__icon' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'icon_color',
            array(
                'label'     => 'رنگ آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'selectors' => array(
                    '{{WRAPPER}} .tno-contact-card__icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-contact-card__icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            array( 'label' => 'هاور کارت' )
        );

        $this->add_control(
            'icon_hover_bg',
            array(
                'label'     => 'رنگ پس‌زمینه آیکون (هاور کارت)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#03255C',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card:hover .tno-contact-card__icon' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'icon_hover_color',
            array(
                'label'     => 'رنگ آیکون (هاور کارت)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array(
                    '{{WRAPPER}} .tno-contact-card:hover .tno-contact-card__icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-contact-card:hover .tno-contact-card__icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'icon_box_size',
            array(
                'label'     => 'اندازه دایره آیکون',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 32, 'max' => 90 ) ),
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} .tno-contact-card__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'icon_size',
            array(
                'label'     => 'سایز آیکون',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 12, 'max' => 44 ) ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-contact-card__icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tno-contact-card__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - TYPOGRAPHY & COLORS
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
                'default'   => '#4B5563',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .tno-contact-card__title',
            )
        );

        $this->add_control(
            'value_color',
            array(
                'label'     => 'رنگ متن اصلی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__value' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'value_accent_color',
            array(
                'label'     => 'رنگ بخش تاکیدی (مثل پیش‌شماره)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#10B981',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__value-accent' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'value_typography',
                'label'    => 'تایپوگرافی متن اصلی',
                'selector' => '{{WRAPPER}} .tno-contact-card__value-wrap',
            )
        );

        $this->add_control(
            'footer_color',
            array(
                'label'     => 'رنگ متن پایین کارت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#9CA3AF',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__footer-text' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'action_bg',
            array(
                'label'     => 'رنگ پس‌زمینه دکمه گوشه چپ',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#EFF6FF',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__action' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'action_color',
            array(
                'label'     => 'رنگ متن دکمه گوشه چپ',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'selectors' => array( '{{WRAPPER}} .tno-contact-card__action' => 'color: {{VALUE}};' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $cards    = $settings['cards_list'] ?? array();

        if ( empty( $cards ) ) {
            return;
        }
        ?>
        <section class="tno-contact-cards" dir="rtl">
            <div class="tno-contact-cards__grid">
                <?php foreach ( $cards as $index => $card ) :
                    $has_action  = ( 'yes' === ( $card['show_action_badge'] ?? 'no' ) );
                    $action_text = $card['action_badge_text'] ?? '';
                    $action_url  = $card['action_badge_link']['url'] ?? '';
                    $val_url     = $card['card_value_link']['url'] ?? '';
                    $has_dot     = ( 'yes' === ( $card['show_footer_dot'] ?? 'no' ) );
                    $dot_color   = $card['footer_dot_color'] ?? '#3B82F6';
                    ?>
                    <article class="tno-contact-card">
                        <div class="tno-contact-card__top">
                            <span class="tno-contact-card__icon" aria-hidden="true">
                                <?php
                                if ( ! empty( $card['card_icon']['value'] ) ) {
                                    Icons_Manager::render_icon( $card['card_icon'], array( 'aria-hidden' => 'true' ) );
                                }
                                ?>
                            </span>

                            <?php if ( $has_action && $action_text !== '' ) : ?>
                                <?php if ( $action_url !== '' ) :
                                    $this->add_link_attributes( 'action_link_' . $index, $card['action_badge_link'] );
                                    ?>
                                    <a <?php $this->print_render_attribute_string( 'action_link_' . $index ); ?> class="tno-contact-card__action">
                                        <span><?php echo esc_html( $action_text ); ?></span>
                                        <?php if ( ! empty( $card['action_badge_icon']['value'] ) ) : ?>
                                            <span class="tno-contact-card__action-icon" aria-hidden="true">
                                                <?php Icons_Manager::render_icon( $card['action_badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                <?php else : ?>
                                    <span class="tno-contact-card__action">
                                        <span><?php echo esc_html( $action_text ); ?></span>
                                        <?php if ( ! empty( $card['action_badge_icon']['value'] ) ) : ?>
                                            <span class="tno-contact-card__action-icon" aria-hidden="true">
                                                <?php Icons_Manager::render_icon( $card['action_badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <?php if ( ! empty( $card['card_title'] ) ) : ?>
                            <h3 class="tno-contact-card__title"><?php echo esc_html( $card['card_title'] ); ?></h3>
                        <?php endif; ?>

                        <div class="tno-contact-card__value-wrap">
                            <?php if ( ! empty( $card['card_prefix'] ) ) : ?>
                                <span class="tno-contact-card__prefix"><?php echo esc_html( $card['card_prefix'] ); ?></span>
                            <?php endif; ?>

                            <?php if ( $val_url !== '' ) :
                                $this->add_link_attributes( 'val_link_' . $index, $card['card_value_link'] );
                                ?>
                                <a <?php $this->print_render_attribute_string( 'val_link_' . $index ); ?> class="tno-contact-card__value">
                                    <?php echo esc_html( $card['card_value'] ?? '' ); ?>
                                </a>
                            <?php else : ?>
                                <span class="tno-contact-card__value"><?php echo esc_html( $card['card_value'] ?? '' ); ?></span>
                            <?php endif; ?>

                            <?php if ( ! empty( $card['card_value_accent'] ) ) : ?>
                                <span class="tno-contact-card__value-accent"><?php echo esc_html( $card['card_value_accent'] ); ?></span>
                            <?php endif; ?>
                        </div>

                        <hr class="tno-contact-card__divider" aria-hidden="true">

                        <?php if ( ! empty( $card['card_footer_text'] ) ) : ?>
                            <div class="tno-contact-card__footer">
                                <?php if ( $has_dot ) : ?>
                                    <span class="tno-contact-card__dot" style="background-color: <?php echo esc_attr( $dot_color ); ?>;" aria-hidden="true"></span>
                                <?php endif; ?>
                                <span class="tno-contact-card__footer-text"><?php echo esc_html( $card['card_footer_text'] ); ?></span>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
