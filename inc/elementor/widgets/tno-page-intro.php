<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class TNO_Widget_Page_Intro extends Widget_Base {

    public function get_name() {
        return 'tno_page_intro';
    }

    public function get_title() {
        return 'سرصفحه و عنوان صفحه ترندینو';
    }

    public function get_icon() {
        return 'eicon-heading';
    }

    public function get_categories() {
        // اسلاگ دسته‌بندی ویجت‌های قالب را در inc/elementor/elementor-init.php تطبیق دهید
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'title', 'header', 'intro', 'heading', 'badge', 'icon', 'عنوان', 'سرصفحه', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'محتوای سرصفحه',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_badge',
            array(
                'label'        => 'نمایش برچسب بالایی',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'badge_type',
            array(
                'label'     => 'نوع برچسب بالایی',
                'type'      => Controls_Manager::SELECT,
                'default'   => 'text',
                'options'   => array(
                    'text' => 'فقط متن',
                    'icon' => 'فقط آیکون',
                    'both' => 'متن و آیکون',
                ),
                'condition' => array( 'show_badge' => 'yes' ),
            )
        );

        $this->add_control(
            'badge_icon',
            array(
                'label'     => 'آیکون برچسب',
                'type'      => Controls_Manager::ICONS,
                'default'   => array(
                    'value'   => 'fas fa-star',
                    'library' => 'fa-solid',
                ),
                'condition' => array(
                    'show_badge' => 'yes',
                    'badge_type' => array( 'icon', 'both' ),
                ),
            )
        );

        $this->add_control(
            'badge_text',
            array(
                'label'       => 'متن برچسب',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'معرفی بخش',
                'placeholder' => 'مثلاً: درباره ترندینو',
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_badge' => 'yes',
                    'badge_type' => array( 'text', 'both' ),
                ),
            )
        );

        $this->add_control(
            'title_text',
            array(
                'label'       => 'عنوان اصلی',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'تجربه خریدی مطمئن و هوشمند با ترندینو',
                'placeholder' => 'عنوان صفحه را وارد کنید',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'title_tag',
            array(
                'label'   => 'تگ HTML عنوان (SEO)',
                'type'    => Controls_Manager::SELECT,
                'default' => 'h1',
                'options' => array(
                    'h1'   => 'H1',
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'h6'   => 'H6',
                    'div'  => 'div',
                    'span' => 'span',
                    'p'    => 'p',
                ),
            )
        );

        $this->add_control(
            'show_dividers',
            array(
                'label'        => 'نمایش خطوط محوشونده طرفین عنوان',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_description',
            array(
                'label'        => 'نمایش توضیحات',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'description_text',
            array(
                'label'     => 'متن توضیحات',
                'type'      => Controls_Manager::TEXTAREA,
                'rows'      => 3,
                'default'   => 'در این بخش می‌توانید اطلاعات کامل، ویژگی‌ها و جزئیات مرتبط با این صفحه را مطالعه کنید تا با آگاهی و اطمینان بیشتری انتخاب خود را انجام دهید.',
                'dynamic'   => array( 'active' => true ),
                'condition' => array( 'show_description' => 'yes' ),
            )
        );

        $this->add_responsive_control(
            'alignment',
            array(
                'label'        => 'ترازبندی کلی',
                'type'         => Controls_Manager::CHOOSE,
                'default'      => 'center',
                'options'      => array(
                    'right'  => array( 'title' => 'راست', 'icon' => 'eicon-text-align-right' ),
                    'center' => array( 'title' => 'وسط', 'icon' => 'eicon-text-align-center' ),
                    'left'   => array( 'title' => 'چپ', 'icon' => 'eicon-text-align-left' ),
                ),
                'prefix_class' => 'tno-intro-align-',
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - BADGE & ICON
         * ========================================================= */
        $this->start_controls_section(
            'section_style_badge',
            array(
                'label'     => 'استایل برچسب بالایی',
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array( 'show_badge' => 'yes' ),
            )
        );

        $this->add_control(
            'badge_color',
            array(
                'label'     => 'رنگ متن',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#2BB673',
                'condition' => array( 'badge_type!' => 'icon' ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-page-intro__badge'      => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-page-intro__badge-text' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'badge_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کادر برچسب',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#EBF9F3',
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__badge' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'badge_typography',
                'condition' => array( 'badge_type!' => 'icon' ),
                'selector'  => '{{WRAPPER}} .tno-page-intro__badge, {{WRAPPER}} .tno-page-intro__badge-text',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'badge_border',
                'selector' => '{{WRAPPER}} .tno-page-intro__badge',
            )
        );

        $this->add_responsive_control(
            'badge_padding',
            array(
                'label'      => 'فاصله داخلی (Padding)',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_radius',
            array(
                'label'      => 'گردی گوشه‌ها',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
                'selectors'  => array( '{{WRAPPER}} .tno-page-intro__badge' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'badge_gap',
            array(
                'label'     => 'فاصله تا عنوان',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__badge' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
            )
        );

        /* ---- تنظیمات آیکون و چینش ---- */
        $this->add_control(
            'badge_icon_heading',
            array(
                'label'     => 'تنظیمات آیکون و نحوه چینش',
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array( 'badge_type' => array( 'icon', 'both' ) ),
            )
        );

        $this->add_responsive_control(
            'badge_direction',
            array(
                'label'     => 'نحوه چینش آیکون و متن',
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'row',
                'toggle'    => false,
                'options'   => array(
                    'row'            => array(
                        'title' => 'افقی (آیکون قبل از متن)',
                        'icon'  => 'eicon-h-align-right',
                    ),
                    'row-reverse'    => array(
                        'title' => 'افقی (آیکون بعد از متن)',
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'column'         => array(
                        'title' => 'عمودی (آیکون بالای متن)',
                        'icon'  => 'eicon-v-align-top',
                    ),
                    'column-reverse' => array(
                        'title' => 'عمودی (آیکون پایین متن)',
                        'icon'  => 'eicon-v-align-bottom',
                    ),
                ),
                'condition' => array( 'badge_type' => 'both' ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-page-intro__badge' => 'flex-direction: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_icon_gap',
            array(
                'label'      => 'فاصله بین آیکون و متن',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array( 'min' => 0, 'max' => 40 ),
                    'em' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
                ),
                'condition'  => array( 'badge_type' => 'both' ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'badge_icon_color',
            array(
                'label'     => 'رنگ آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#2BB673',
                'condition' => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tno-page-intro__badge-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'badge_icon_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کادر آیکون',
                'type'      => Controls_Manager::COLOR,
                'condition' => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors' => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_icon_size',
            array(
                'label'      => 'سایز آیکون',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em', 'rem' ),
                'range'      => array(
                    'px'  => array( 'min' => 8, 'max' => 96 ),
                    'em'  => array( 'min' => 0.5, 'max' => 6, 'step' => 0.1 ),
                    'rem' => array( 'min' => 0.5, 'max' => 6, 'step' => 0.1 ),
                ),
                'condition'  => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tno-page-intro__badge-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_icon_box_size',
            array(
                'label'      => 'اندازه کادر آیکون',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 16, 'max' => 120 ),
                ),
                'condition'  => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_icon_radius',
            array(
                'label'      => 'گردی کادر آیکون',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array( 'min' => 0, 'max' => 100 ),
                    '%'  => array( 'min' => 0, 'max' => 50 ),
                ),
                'condition'  => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_icon_rotate',
            array(
                'label'      => 'چرخش آیکون (درجه)',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'deg' ),
                'range'      => array(
                    'deg' => array( 'min' => -180, 'max' => 180 ),
                ),
                'condition'  => array( 'badge_type' => array( 'icon', 'both' ) ),
                'selectors'  => array(
                    '{{WRAPPER}} .tno-page-intro__badge-icon' => 'transform: rotate({{SIZE}}deg);',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - TITLE & DIVIDERS
         * ========================================================= */
        $this->start_controls_section(
            'section_style_title',
            array(
                'label' => 'استایل عنوان و خطوط طرفین',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => 'رنگ عنوان',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1A1A1A',
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .tno-page-intro__title',
            )
        );

        $this->add_responsive_control(
            'title_gap',
            array(
                'label'     => 'فاصله عنوان تا توضیحات',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__heading-row' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_control(
            'divider_color',
            array(
                'label'     => 'رنگ خطوط طرفین',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#E5E7EB',
                'condition' => array( 'show_dividers' => 'yes' ),
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__heading-row' => '--tno-divider-color: {{VALUE}};' ),
            )
        );

        $this->add_responsive_control(
            'divider_max_width',
            array(
                'label'      => 'حداکثر عرض هر خط',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array( 'min' => 20, 'max' => 400 ),
                    '%'  => array( 'min' => 5, 'max' => 45 ),
                ),
                'condition'  => array( 'show_dividers' => 'yes' ),
                'selectors'  => array( '{{WRAPPER}} .tno-page-intro__line' => 'max-width: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - DESCRIPTION
         * ========================================================= */
        $this->start_controls_section(
            'section_style_desc',
            array(
                'label'     => 'استایل توضیحات',
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array( 'show_description' => 'yes' ),
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => 'رنگ متن',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#737373',
                'selectors' => array( '{{WRAPPER}} .tno-page-intro__desc' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'selector' => '{{WRAPPER}} .tno-page-intro__desc',
            )
        );

        $this->add_responsive_control(
            'desc_max_width',
            array(
                'label'      => 'حداکثر عرض پاراگراف',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array( 'min' => 240, 'max' => 1200 ),
                    '%'  => array( 'min' => 30, 'max' => 100 ),
                ),
                'selectors'  => array( '{{WRAPPER}} .tno-page-intro__desc' => 'max-width: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
        $title_tag    = in_array( $settings['title_tag'], $allowed_tags, true ) ? $settings['title_tag'] : 'h1';
        $show_lines   = ( 'yes' === ( $settings['show_dividers'] ?? 'yes' ) );

        $show_badge = ( 'yes' === ( $settings['show_badge'] ?? 'no' ) );
        $badge_type = $settings['badge_type'] ?? 'text';
        if ( ! in_array( $badge_type, array( 'text', 'icon', 'both' ), true ) ) {
            $badge_type = 'text';
        }

        $has_icon = $show_badge && in_array( $badge_type, array( 'icon', 'both' ), true ) && ! empty( $settings['badge_icon']['value'] );
        $has_text = $show_badge && in_array( $badge_type, array( 'text', 'both' ), true ) && ! empty( $settings['badge_text'] );
        ?>
        <header class="tno-page-intro" dir="rtl">
            <?php if ( $has_icon || $has_text ) : ?>
                <div class="tno-page-intro__badge-wrap">
                    <span class="tno-page-intro__badge <?php echo esc_attr( 'tno-page-intro__badge--' . $badge_type ); ?>">
                        <?php if ( $has_icon ) : ?>
                            <span class="tno-page-intro__badge-icon" aria-hidden="true">
                                <?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( $has_text ) : ?>
                            <span class="tno-page-intro__badge-text"><?php echo esc_html( $settings['badge_text'] ); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $settings['title_text'] ) ) : ?>
                <div class="tno-page-intro__heading-row <?php echo $show_lines ? 'has-lines' : ''; ?>">
                    <?php if ( $show_lines ) : ?>
                        <span class="tno-page-intro__line tno-page-intro__line--start" aria-hidden="true"></span>
                    <?php endif; ?>

                    <<?php echo esc_attr( $title_tag ); ?> class="tno-page-intro__title">
                        <?php echo esc_html( $settings['title_text'] ); ?>
                    </<?php echo esc_attr( $title_tag ); ?>>

                    <?php if ( $show_lines ) : ?>
                        <span class="tno-page-intro__line tno-page-intro__line--end" aria-hidden="true"></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === ( $settings['show_description'] ?? 'no' ) && ! empty( $settings['description_text'] ) ) : ?>
                <p class="tno-page-intro__desc">
                    <?php echo nl2br( esc_html( $settings['description_text'] ) ); ?>
                </p>
            <?php endif; ?>
        </header>
        <?php
    }
}
