<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

class TNO_Widget_FAQ extends Widget_Base {

    public function get_name() {
        return 'tno_faq';
    }

    public function get_title() {
        return 'سوالات متداول دسته‌بندی‌شده ترندینو';
    }

    public function get_icon() {
        return 'eicon-accordion';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'faq', 'accordion', 'schema', 'سوالات متداول', 'پرسش', 'اسکیما' );
    }

    public function get_script_depends() {
            return array( 'tno-widgets-script' ); // هندل دقیق فایل tno-widgets.js در قالب
        }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT - CATEGORIES
         * ========================================================= */
        $this->start_controls_section(
            'section_categories',
            array(
                'label' => 'دسته‌بندی‌ها (تب‌ها)',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_categories',
            array(
                'label'        => 'نمایش نوار دسته‌بندی‌ها',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_all_tab',
            array(
                'label'        => 'افزودن تب «همه موارد»',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => '',
                'condition'    => array( 'show_categories' => 'yes' ),
            )
        );

        $this->add_control(
            'all_tab_label',
            array(
                'label'     => 'عنوان تب همه',
                'type'      => Controls_Manager::TEXT,
                'default'   => 'همه سوالات',
                'condition' => array(
                    'show_categories' => 'yes',
                    'show_all_tab'    => 'yes',
                ),
            )
        );

        $cat_repeater = new Repeater();

        $cat_repeater->add_control(
            'cat_slug',
            array(
                'label'       => 'شناسه یکتای دسته (لاتین)',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'shipping',
                'description' => 'این شناسه را در بخش «سوالات» برای اتصال هر سوال به این دسته وارد کنید.',
            )
        );

        $cat_repeater->add_control(
            'cat_title',
            array(
                'label'   => 'عنوان دسته‌بندی',
                'type'    => Controls_Manager::TEXT,
                'default' => 'پیگیری ارسال سفارش',
            )
        );

        $cat_repeater->add_control(
            'cat_icon',
            array(
                'label'   => 'آیکون',
                'type'    => Controls_Manager::ICONS,
                'default' => array(
                    'value'   => 'fas fa-truck',
                    'library' => 'fa-solid',
                ),
            )
        );

        $this->add_control(
            'categories',
            array(
                'label'       => 'لیست دسته‌بندی‌ها',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $cat_repeater->get_controls(),
                'title_field' => '{{{ cat_title }}} ({{{ cat_slug }}})',
                'condition'   => array( 'show_categories' => 'yes' ),
                'default'     => array(
                    array(
                        'cat_slug'  => 'auth',
                        'cat_title' => 'ورود و ثبت نام',
                        'cat_icon'  => array( 'value' => 'far fa-user', 'library' => 'fa-regular' ),
                    ),
                    array(
                        'cat_slug'  => 'order',
                        'cat_title' => 'روند ثبت سفارش',
                        'cat_icon'  => array( 'value' => 'far fa-envelope', 'library' => 'fa-regular' ),
                    ),
                    array(
                        'cat_slug'  => 'shipping',
                        'cat_title' => 'پیگیری ارسال سفارش',
                        'cat_icon'  => array( 'value' => 'fas fa-truck', 'library' => 'fa-solid' ),
                    ),
                    array(
                        'cat_slug'  => 'returns',
                        'cat_title' => 'برگرداندن کالا',
                        'cat_icon'  => array( 'value' => 'fas fa-rotate', 'library' => 'fa-solid' ),
                    ),
                    array(
                        'cat_slug'  => 'discount',
                        'cat_title' => 'کد تخفیف',
                        'cat_icon'  => array( 'value' => 'fas fa-gift', 'library' => 'fa-solid' ),
                    ),
                    array(
                        'cat_slug'  => 'other',
                        'cat_title' => 'سایر موارد',
                        'cat_icon'  => array( 'value' => 'fas fa-ellipsis', 'library' => 'fa-solid' ),
                    ),
                ),
            )
        );

        $this->add_control(
            'default_active_cat',
            array(
                'label'       => 'شناسه دسته فعال پیش‌فرض',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'shipping',
                'description' => 'در صورت خالی بودن، اولین دسته به‌صورت خودکار فعال می‌شود.',
                'condition'   => array( 'show_categories' => 'yes' ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: CONTENT - FAQ ITEMS
         * ========================================================= */
        $this->start_controls_section(
            'section_items',
            array(
                'label' => 'پرسش و پاسخ‌ها',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $faq_repeater = new Repeater();

        $faq_repeater->add_control(
            'item_cat_slug',
            array(
                'label'       => 'شناسه دسته‌بندی',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'shipping',
                'description' => 'شناسه دسته (یا چندین شناسه جداشده با کاما مثلاً: order,shipping)',
            )
        );

        $faq_repeater->add_control(
            'question',
            array(
                'label'       => 'متن پرسش',
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
                'default'     => 'چگونه می‌توانم سفارشم را ثبت کنم؟',
                'dynamic'     => array( 'active' => true ),
            )
        );

        $faq_repeater->add_control(
            'answer',
            array(
                'label'   => 'متن پاسخ',
                'type'    => Controls_Manager::WYSIWYG,
                'default' => 'برای ثبت سفارش کافی‌ست وارد صفحه محصول شوید و دکمه «افزودن به سبد خرید» را بزنید. سپس با ورود یا ثبت‌نام در سایت، مراحل پرداخت را تکمیل کنید.',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'faq_items',
            array(
                'label'       => 'لیست سوالات',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $faq_repeater->get_controls(),
                'title_field' => '{{{ question }}} [{{{ item_cat_slug }}}]',
                'default'     => array(
                    array(
                        'item_cat_slug' => 'shipping',
                        'question'      => 'چگونه می‌توانم سفارشم را ثبت کنم؟',
                        'answer'        => 'برای ثبت سفارش کافی‌ست وارد صفحه محصول شوید و دکمه «افزودن به سبد خرید» را بزنید. سپس با ورود یا ثبت‌نام در سایت، مراحل پرداخت را تکمیل کنید.',
                    ),
                    array(
                        'item_cat_slug' => 'shipping',
                        'question'      => 'چه روش‌هایی برای پرداخت وجود دارد؟',
                        'answer'        => 'امکان پرداخت اینترنتی با کلیه کارت‌های عضو شتاب و همچنین خرید اقساطی از طریق درگاه‌های اعتباری فعال در صفحه تسویه حساب فراهم است.',
                    ),
                    array(
                        'item_cat_slug' => 'shipping',
                        'question'      => 'حداقل زمان ارسال چند روز است؟',
                        'answer'        => 'سفارش‌های شهر تهران ظرف ۲۴ ساعت کاری و سفارش‌های سایر شهرها بین ۲ تا ۴ روز کاری از طریق پست پیشتاز یا تیپاکس تحویل داده می‌شوند.',
                    ),
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: CONTENT - BEHAVIOR & SEO SCHEMA
         * ========================================================= */
        $this->start_controls_section(
            'section_settings',
            array(
                'label' => 'تنظیمات رفتار و اسکیما (SEO)',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'enable_schema',
            array(
                'label'        => 'تولید اسکیمای FAQPage (JSON-LD)',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'فعال',
                'label_off'    => 'غیرفعال',
                'return_value' => 'yes',
                'default'      => 'yes',
                'description'  => 'خروجی استاندارد Schema.org/FAQPage برای تمام سوالات این ویجت ایجاد می‌کند.',
            )
        );

        $this->add_control(
            'question_tag',
            array(
                'label'   => 'تگ HTML عنوان هر سوال',
                'type'    => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => array(
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'div'  => 'div',
                    'span' => 'span',
                ),
            )
        );

        $this->add_control(
            'numbering_style',
            array(
                'label'   => 'شماره‌گذاری خودکار سوالات',
                'type'    => Controls_Manager::SELECT,
                'default' => 'persian',
                'options' => array(
                    'persian' => 'اعداد فارسی (۱. ۲. ۳.)',
                    'decimal' => 'اعداد لاتین (1. 2. 3.)',
                    'none'    => 'بدون شماره',
                ),
            )
        );

        $this->add_control(
            'open_first',
            array(
                'label'        => 'باز بودن اولین سوال هر دسته',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'single_open',
            array(
                'label'        => 'حالت آکاردئون (بسته شدن خودکار سایر سوالات)',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_responsive_control(
            'list_max_width',
            array(
                'label'      => 'حداکثر عرض باکس سوالات',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array( 'min' => 400, 'max' => 1400 ),
                    '%'  => array( 'min' => 50, 'max' => 100 ),
                ),
                'selectors'  => array( '{{WRAPPER}} .tno-faq__list' => 'max-width: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - CATEGORY TABS
         * ========================================================= */
        $this->start_controls_section(
            'section_style_tabs',
            array(
                'label'     => 'استایل تب‌های دسته‌بندی',
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array( 'show_categories' => 'yes' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'tab_typography',
                'selector' => '{{WRAPPER}} .tno-faq__tab',
            )
        );

        $this->add_responsive_control(
            'tabs_gap',
            array(
                'label'     => 'فاصله بین تب‌ها',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 4, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-faq__tabs' => 'gap: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'tabs_margin_bottom',
            array(
                'label'     => 'فاصله نوار تب‌ها تا سوالات',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 10, 'max' => 80 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-faq__tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'tab_padding',
            array(
                'label'      => 'فاصله داخلی تب',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px' ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-faq__tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_style_states' );

        $this->start_controls_tab( 'tab_state_normal', array( 'label' => 'عادی' ) );
        $this->add_control(
            'tab_text_color',
            array(
                'label'     => 'رنگ متن و آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#262626',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab' => 'color: {{VALUE}};' ),
            )
        );
        $this->add_control(
            'tab_bg_color',
            array(
                'label'     => 'رنگ پس‌زمینه',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab' => 'background-color: {{VALUE}};' ),
            )
        );
        $this->add_control(
            'tab_border_color',
            array(
                'label'     => 'رنگ حاشیه',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#EAEAEA',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab' => 'border-color: {{VALUE}};' ),
            )
        );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_state_active', array( 'label' => 'فعال' ) );
        $this->add_control(
            'tab_active_text_color',
            array(
                'label'     => 'رنگ متن و آیکون فعال',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab.is-active' => 'color: {{VALUE}};' ),
            )
        );
        $this->add_control(
            'tab_active_bg_color',
            array(
                'label'     => 'رنگ پس‌زمینه فعال',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1C1C1C',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab.is-active' => 'background-color: {{VALUE}};' ),
            )
        );
        $this->add_control(
            'tab_active_border_color',
            array(
                'label'     => 'رنگ حاشیه فعال',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1C1C1C',
                'selectors' => array( '{{WRAPPER}} .tno-faq__tab.is-active' => 'border-color: {{VALUE}};' ),
            )
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - ACCORDION ITEMS
         * ========================================================= */
        $this->start_controls_section(
            'section_style_items',
            array(
                'label' => 'استایل باکس سوالات',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'items_gap',
            array(
                'label'     => 'فاصله بین سوالات',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 4, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-faq__list' => 'gap: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'item_radius',
            array(
                'label'     => 'گردی گوشه‌ها',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-faq__item' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_control(
            'item_closed_bg',
            array(
                'label'     => 'پس‌زمینه حالت بسته',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F8F8F8',
                'selectors' => array( '{{WRAPPER}} .tno-faq__item' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'item_open_bg',
            array(
                'label'     => 'پس‌زمینه حالت باز',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-faq__item.is-open' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'item_open_border',
            array(
                'label'     => 'رنگ حاشیه حالت باز',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1C1C1C',
                'selectors' => array( '{{WRAPPER}} .tno-faq__item.is-open' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'question_color',
            array(
                'label'     => 'رنگ متن سوال',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1A1A1A',
                'selectors' => array( '{{WRAPPER}} .tno-faq__question' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'question_typography',
                'label'    => 'تایپوگرافی سوال',
                'selector' => '{{WRAPPER}} .tno-faq__question',
            )
        );

        $this->add_control(
            'answer_color',
            array(
                'label'     => 'رنگ متن پاسخ',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6B7280',
                'selectors' => array( '{{WRAPPER}} .tno-faq__answer-inner' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'answer_typography',
                'label'    => 'تایپوگرافی پاسخ',
                'selector' => '{{WRAPPER}} .tno-faq__answer-inner',
            )
        );

        $this->add_control(
            'toggle_icon_color',
            array(
                'label'     => 'رنگ آیکون +/-',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1A1A1A',
                'selectors' => array( '{{WRAPPER}} .tno-faq__toggle-icon' => 'color: {{VALUE}};' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings        = $this->get_settings_for_display();
        $widget_id       = $this->get_id();
        $show_categories = ( 'yes' === $settings['show_categories'] && ! empty( $settings['categories'] ) );
        $show_all_tab    = ( 'yes' === $settings['show_all_tab'] );
        $categories      = $show_categories ? $settings['categories'] : array();
        $faq_items       = ! empty( $settings['faq_items'] ) ? $settings['faq_items'] : array();
        $open_first      = ( 'yes' === $settings['open_first'] );
        $single_open     = ( 'yes' === $settings['single_open'] );
        $numbering       = in_array( $settings['numbering_style'], array( 'persian', 'decimal', 'none' ), true )
            ? $settings['numbering_style']
            : 'persian';

        $allowed_q_tags = array( 'h2', 'h3', 'h4', 'h5', 'div', 'span' );
        $q_tag          = in_array( $settings['question_tag'], $allowed_q_tags, true ) ? $settings['question_tag'] : 'h3';

        // تعیین دسته‌بندی فعال اولیه
        $active_cat = 'all';
        if ( $show_categories ) {
            $valid_slugs = array_map(
                static function ( $c ) {
                    return sanitize_title( $c['cat_slug'] ?? '' );
                },
                $categories
            );
            $requested_default = sanitize_title( $settings['default_active_cat'] ?? '' );

            if ( $requested_default && in_array( $requested_default, $valid_slugs, true ) ) {
                $active_cat = $requested_default;
            } elseif ( $show_all_tab ) {
                $active_cat = 'all';
            } elseif ( ! empty( $valid_slugs[0] ) ) {
                $active_cat = $valid_slugs[0];
            }
        }
        ?>
        <div
            class="tno-faq tno-faq--num-<?php echo esc_attr( $numbering ); ?>"
            dir="rtl"
            data-single-open="<?php echo $single_open ? 'true' : 'false'; ?>"
            data-open-first="<?php echo $open_first ? 'true' : 'false'; ?>"
        >
            <?php if ( $show_categories ) : ?>
                <div class="tno-faq__tabs" role="tablist" aria-label="دسته‌بندی سوالات متداول">
                    <?php if ( $show_all_tab ) : ?>
                        <button
                            type="button"
                            class="tno-faq__tab <?php echo 'all' === $active_cat ? 'is-active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo 'all' === $active_cat ? 'true' : 'false'; ?>"
                            data-cat="all"
                        >
                            <span class="tno-faq__tab-label"><?php echo esc_html( $settings['all_tab_label'] ?: 'همه سوالات' ); ?></span>
                        </button>
                    <?php endif; ?>

                    <?php foreach ( $categories as $cat ) :
                        $slug      = sanitize_title( $cat['cat_slug'] ?? '' );
                        $is_active = ( $slug === $active_cat );
                        ?>
                        <button
                            type="button"
                            class="tno-faq__tab <?php echo $is_active ? 'is-active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                            data-cat="<?php echo esc_attr( $slug ); ?>"
                        >
                            <?php if ( ! empty( $cat['cat_icon']['value'] ) ) : ?>
                                <span class="tno-faq__tab-icon" aria-hidden="true">
                                    <?php Icons_Manager::render_icon( $cat['cat_icon'], array( 'aria-hidden' => 'true' ) ); ?>
                                </span>
                            <?php endif; ?>
                            <span class="tno-faq__tab-label"><?php echo esc_html( $cat['cat_title'] ?? '' ); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $faq_items ) ) : ?>
                <div class="tno-faq__list">
                    <?php
                    $first_visible_opened = false;
                    $schema_entities      = array();

                    foreach ( $faq_items as $index => $item ) :
                        $question = trim( (string) ( $item['question'] ?? '' ) );
                        $answer   = trim( (string) ( $item['answer'] ?? '' ) );
                        if ( '' === $question ) {
                            continue;
                        }

                        // پشتیبانی از اتصال یک سوال به یک یا چند دسته (جداشده با کاما)
                        $raw_cats   = explode( ',', (string) ( $item['item_cat_slug'] ?? '' ) );
                        $item_cats  = array_values( array_filter( array_map( 'sanitize_title', $raw_cats ) ) );
                        $is_visible = ( ! $show_categories || 'all' === $active_cat || in_array( $active_cat, $item_cats, true ) );

                        $is_open = false;
                        if ( $is_visible && $open_first && ! $first_visible_opened ) {
                            $is_open              = true;
                            $first_visible_opened = true;
                        }

                        $panel_id = 'tno-faq-panel-' . $widget_id . '-' . $index;

                        // جمع‌آوری داده برای اسکیمای FAQPage (تمام سوالات ویجت)
                        if ( '' !== $answer ) {
                            $schema_entities[] = array(
                                '@type'          => 'Question',
                                'name'           => wp_strip_all_tags( $question ),
                                'acceptedAnswer' => array(
                                    '@type' => 'Answer',
                                    'text'  => trim( wp_strip_all_tags( $answer ) ),
                                ),
                            );
                        }
                        ?>
                        <article
                            class="tno-faq__item <?php echo $is_open ? 'is-open' : ''; ?>"
                            data-cats="<?php echo esc_attr( implode( ',', $item_cats ) ); ?>"
                            <?php echo $is_visible ? '' : 'hidden'; ?>
                        >
                            <button
                                type="button"
                                class="tno-faq__trigger"
                                aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr( $panel_id ); ?>"
                            >
                                <<?php echo esc_attr( $q_tag ); ?> class="tno-faq__question">
                                    <?php echo esc_html( $question ); ?>
                                </<?php echo esc_attr( $q_tag ); ?>>
                                <span class="tno-faq__toggle-icon" aria-hidden="true"></span>
                            </button>

                            <div
                                id="<?php echo esc_attr( $panel_id ); ?>"
                                class="tno-faq__panel"
                                role="region"
                            >
                                <div class="tno-faq__answer-inner">
                                    <?php echo wp_kses_post( $answer ); ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php
        // خروجی اسکیمای JSON-LD فقط در حالت فرانت‌اند (نه داخل ویرایشگر المنتور) و در صورت فعال بودن
        if (
            'yes' === $settings['enable_schema']
            && ! empty( $schema_entities )
            && ! \Elementor\Plugin::$instance->editor->is_edit_mode()
        ) :
            $schema = array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $schema_entities,
            );
            ?>
            <script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ); ?></script>
            <?php
        endif;
    }
}