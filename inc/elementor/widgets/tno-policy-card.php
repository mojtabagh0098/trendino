<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

/**
 * Trendino Policy / Terms & Conditions Card Widget (شرایط و قوانین).
 */
class TNO_Widget_Policy_Card extends Widget_Base {

    public function get_name() {
        return 'tno_policy_card';
    }

    public function get_title() {
        return 'کارت قوانین و شرایط ترندینو';
    }

    public function get_icon() {
        return 'eicon-document-file';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'policy', 'terms', 'rules', 'returns', 'شرایط', 'قوانین', 'مرجوعی', 'تعویض', 'ترندینو' );
    }

    /**
     * Convert integer index to Persian digits.
     */
    private function to_persian_num( int $num ): string {
        return strtr( (string) $num, array(
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ) );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT - HEADER & INTRO
         * ========================================================= */
        $this->start_controls_section(
            'section_header',
            array(
                'label' => 'سربرگ و متن مقدمه',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'card_title',
            array(
                'label'       => 'عنوان باکس',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'شرایط تعویض و مرجوعی',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'title_tag',
            array(
                'label'   => 'تگ HTML عنوان',
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => array(
                    'h1'  => 'H1',
                    'h2'  => 'H2',
                    'h3'  => 'H3',
                    'h4'  => 'H4',
                    'div' => 'div',
                ),
            )
        );

        $this->add_control(
            'show_intro',
            array(
                'label'        => 'نمایش پاراگراف مقدمه',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'intro_text',
            array(
                'label'     => 'متن مقدمه',
                'type'      => Controls_Manager::TEXTAREA,
                'rows'      => 4,
                'default'   => 'برای استفاده از خدمات تعویض یا مرجوعی، کالا باید در وضعیت اولیه، بدون استفاده و همراه با جعبه و تمامی متعلقات آن بازگردانده شود. پس از بررسی و تأیید کارشناسان، فرآیند تعویض کالا یا بازگشت وجه در کوتاه‌ترین زمان ممکن انجام خواهد شد. هدف ما فراهم کردن تجربه‌ای مطمئن، شفاف و رضایت‌بخش برای تمامی مشتریان ترندینو است.',
                'condition' => array( 'show_intro' => 'yes' ),
                'dynamic'   => array( 'active' => true ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: CONTENT - NUMBERED RULES
         * ========================================================= */
        $this->start_controls_section(
            'section_rules',
            array(
                'label' => 'لیست موارد شماره‌گذاری‌شده',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_rules',
            array(
                'label'        => 'نمایش لیست موارد',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'numbering_style',
            array(
                'label'     => 'نوع شماره‌گذاری',
                'type'      => Controls_Manager::SELECT,
                'default'   => 'persian',
                'options'   => array(
                    'persian' => 'اعداد فارسی (۱. ۲. ۳.)',
                    'decimal' => 'اعداد لاتین (1. 2. 3.)',
                    'none'    => 'بدون شماره',
                ),
                'condition' => array( 'show_rules' => 'yes' ),
            )
        );

        $rules_repeater = new Repeater();

        $rules_repeater->add_control(
            'rule_text',
            array(
                'label'       => 'متن بند',
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 2,
                'default'     => 'کالا باید کاملاً نو، استفاده‌نشده و در شرایط اولیه خود باشد.',
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'rules_list',
            array(
                'label'       => 'لیست بندها',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $rules_repeater->get_controls(),
                'title_field' => '{{{ rule_text }}}',
                'condition'   => array( 'show_rules' => 'yes' ),
                'default'     => array(
                    array( 'rule_text' => 'کالا باید کاملاً نو، استفاده‌نشده و در شرایط اولیه خود باشد.' ),
                    array( 'rule_text' => 'جعبه اصلی و تمامی متعلقات محصول باید سالم و کامل باشند.' ),
                    array( 'rule_text' => 'هرگونه آسیب به جعبه یا استفاده از محصول ممکن است امکان تعویض یا مرجوعی را از بین ببرد.' ),
                    array( 'rule_text' => 'درخواست تعویض یا مرجوعی باید در بازه زمانی تعیین‌شده پس از دریافت سفارش به پشتیبانی فروشگاه اعلام شود.' ),
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: CONTENT - NESTED CALLOUT BOXES
         * ========================================================= */
        $this->start_controls_section(
            'section_boxes',
            array(
                'label' => 'باکس‌های توضیحات تکمیلی',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_boxes',
            array(
                'label'        => 'نمایش باکس‌های تکمیلی',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $box_repeater = new Repeater();

        $box_repeater->add_control(
            'box_title',
            array(
                'label'       => 'عنوان باکس',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'مغایرت یا ایراد کالا',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $box_repeater->add_control(
            'box_text',
            array(
                'label'   => 'متن توضیحات',
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 3,
                'default' => 'در صورتی که کالای دریافتی دارای ایراد، آسیب‌دیدگی یا مغایرت با سفارش ثبت‌شده باشد، لطفاً در اولین فرصت موضوع را به واحد پشتیبانی اطلاع دهید تا پس از بررسی، اقدامات لازم برای تعویض یا بازگشت کالا انجام شود.',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'boxes_list',
            array(
                'label'       => 'لیست باکس‌ها',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $box_repeater->get_controls(),
                'title_field' => '{{{ box_title }}}',
                'condition'   => array( 'show_boxes' => 'yes' ),
                'default'     => array(
                    array(
                        'box_title' => 'مغایرت یا ایراد کالا',
                        'box_text'  => 'در صورتی که کالای دریافتی دارای ایراد، آسیب‌دیدگی یا مغایرت با سفارش ثبت‌شده باشد، لطفاً در اولین فرصت موضوع را به واحد پشتیبانی اطلاع دهید تا پس از بررسی، اقدامات لازم برای تعویض یا بازگشت کالا انجام شود.',
                    ),
                    array(
                        'box_title' => 'بازگشت وجه',
                        'box_text'  => 'پس از دریافت و بررسی کالای مرجوعی توسط کارشناسان ترندینو، مبلغ سفارش از طریق همان روش پرداخت یا طبق هماهنگی انجام‌شده به مشتری بازگردانده خواهد شد.',
                    ),
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - OUTER CARD
         * ========================================================= */
        $this->start_controls_section(
            'section_style_card',
            array(
                'label' => 'استایل کارت اصلی',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کارت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-policy-card' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'card_border_color',
            array(
                'label'     => 'رنگ حاشیه کارت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F1F3F5',
                'selectors' => array( '{{WRAPPER}} .tno-policy-card' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_responsive_control(
            'card_radius',
            array(
                'label'     => 'گردی گوشه کارت',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-policy-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_shadow',
                'selector' => '{{WRAPPER}} .tno-policy-card',
            )
        );

        $this->add_control(
            'header_title_color',
            array(
                'label'     => 'رنگ عنوان اصلی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-policy-card__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'header_title_typo',
                'label'    => 'تایپوگرافی عنوان اصلی',
                'selector' => '{{WRAPPER}} .tno-policy-card__title',
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - BODY & NESTED BOXES
         * ========================================================= */
        $this->start_controls_section(
            'section_style_body',
            array(
                'label' => 'استایل متن‌ها و باکس‌های داخلی',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'body_text_color',
            array(
                'label'     => 'رنگ متن مقدمه و بندها',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#4B5563',
                'selectors' => array(
                    '{{WRAPPER}} .tno-policy-card__intro' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-policy-card__rule-text' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'body_text_typo',
                'label'    => 'تایپوگرافی متن مقدمه و بندها',
                'selector' => '{{WRAPPER}} .tno-policy-card__intro, {{WRAPPER}} .tno-policy-card__rule',
            )
        );

        $this->add_control(
            'nested_outer_bg',
            array(
                'label'     => 'رنگ پس‌زمینه باکس بیرونی (خاکستری)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F7F7F8',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-policy-box' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'nested_inner_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کادر سفید داخلی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-policy-box__inner' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'nested_title_color',
            array(
                'label'     => 'رنگ عنوان باکس داخلی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'selectors' => array( '{{WRAPPER}} .tno-policy-box__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'nested_text_color',
            array(
                'label'     => 'رنگ متن باکس داخلی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#4B5563',
                'selectors' => array( '{{WRAPPER}} .tno-policy-box__text' => 'color: {{VALUE}};' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings     = $this->get_settings_for_display();
        $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'div' );
        $title_tag    = in_array( $settings['title_tag'] ?? 'h2', $allowed_tags, true ) ? $settings['title_tag'] : 'h2';

        $show_intro   = ( 'yes' === ( $settings['show_intro'] ?? 'yes' ) ) && ! empty( $settings['intro_text'] );
        $show_rules   = ( 'yes' === ( $settings['show_rules'] ?? 'yes' ) ) && ! empty( $settings['rules_list'] );
        $show_boxes   = ( 'yes' === ( $settings['show_boxes'] ?? 'yes' ) ) && ! empty( $settings['boxes_list'] );
        $num_style    = $settings['numbering_style'] ?? 'persian';
        ?>
        <section class="tno-policy-card" dir="rtl">
            <?php if ( ! empty( $settings['card_title'] ) ) : ?>
                <header class="tno-policy-card__head">
                    <<?php echo esc_attr( $title_tag ); ?> class="tno-policy-card__title">
                        <?php echo esc_html( $settings['card_title'] ); ?>
                    </<?php echo esc_attr( $title_tag ); ?>>
                </header>
            <?php endif; ?>

            <div class="tno-policy-card__body">
                <?php if ( $show_intro ) : ?>
                    <p class="tno-policy-card__intro">
                        <?php echo nl2br( esc_html( $settings['intro_text'] ) ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $show_rules ) : ?>
                    <ol class="tno-policy-card__rules">
                        <?php foreach ( $settings['rules_list'] as $idx => $rule ) :
                            $text = $rule['rule_text'] ?? '';
                            if ( $text === '' ) {
                                continue;
                            }
                            $num = $idx + 1;
                            $num_label = $num_style === 'persian'
                                ? $this->to_persian_num( $num ) . '.'
                                : ( $num_style === 'decimal' ? $num . '.' : '' );
                            ?>
                            <li class="tno-policy-card__rule">
                                <?php if ( $num_label !== '' ) : ?>
                                    <span class="tno-policy-card__rule-num"><?php echo esc_html( $num_label ); ?></span>
                                <?php endif; ?>
                                <span class="tno-policy-card__rule-text"><?php echo esc_html( $text ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

                <?php if ( $show_boxes ) : ?>
                    <div class="tno-policy-card__boxes">
                        <?php foreach ( $settings['boxes_list'] as $box ) :
                            $b_title = $box['box_title'] ?? '';
                            $b_text  = $box['box_text'] ?? '';
                            if ( $b_title === '' && $b_text === '' ) {
                                continue;
                            }
                            ?>
                            <div class="tno-policy-box">
                                <?php if ( $b_title !== '' ) : ?>
                                    <h3 class="tno-policy-box__title"><?php echo esc_html( $b_title ); ?></h3>
                                <?php endif; ?>

                                <?php if ( $b_text !== '' ) : ?>
                                    <div class="tno-policy-box__inner">
                                        <p class="tno-policy-box__text"><?php echo nl2br( esc_html( $b_text ) ); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
