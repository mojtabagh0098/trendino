<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

/**
 * Trendino About Us: Hanging Stats Cards Widget.
 */
class TNO_Widget_About_Stats extends Widget_Base {

    public function get_name() {
        return 'tno_about_stats';
    }

    public function get_title() {
        return 'آمار و دستاوردها (درباره ما)';
    }

    public function get_icon() {
        return 'eicon-counter';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'stats', 'counter', 'about', 'آمار', 'دستاورد', 'درباره ما', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'کارت‌های آمار',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'stat_label',
            array(
                'label'       => 'عنوان برچسب',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'سال سابقه',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'stat_number',
            array(
                'label'   => 'عدد / مقدار',
                'type'    => Controls_Manager::TEXT,
                'default' => '۱۹',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'stats_list',
            array(
                'label'       => 'لیست آمار',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ stat_label }}} : {{{ stat_number }}}',
                'default'     => array(
                    array(
                        'stat_label'  => 'سال سابقه',
                        'stat_number' => '۱۹',
                    ),
                    array(
                        'stat_label'  => 'سفارش موفق در سراسر ایران',
                        'stat_number' => '۳,۰۰۰',
                    ),
                    array(
                        'stat_label'  => 'ارسال روزانه',
                        'stat_number' => '۵,۰۰۰',
                    ),
                    array(
                        'stat_label'  => 'کاربر فعال در سایت',
                        'stat_number' => '۴۵,۲۳۰',
                    ),
                ),
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label'           => 'تعداد ستون',
                'type'            => Controls_Manager::SELECT,
                'default'         => '4',
                'tablet_default'  => '2',
                'mobile_default'  => '1',
                'options'         => array(
                    '1' => '۱ ستون',
                    '2' => '۲ ستون',
                    '3' => '۳ ستون',
                    '4' => '۴ ستون',
                    '5' => '۵ ستون',
                ),
                'selectors'       => array(
                    '{{WRAPPER}} .tno-about-stats__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
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
                    '{{WRAPPER}} .tno-about-stats__grid' => 'gap: {{SIZE}}{{UNIT}};',
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
                'default'   => '#F8F9FA',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-card' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'show_dot_pattern',
            array(
                'label'        => 'نمایش پترن نقطه‌ای پس‌زمینه',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_responsive_control(
            'card_radius',
            array(
                'label'      => 'گردی گوشه کارت',
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors'  => array( '{{WRAPPER}} .tno-about-stat-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE - HANGING TAG & NUMBER
         * ========================================================= */
        $this->start_controls_section(
            'section_style_tag',
            array(
                'label' => 'استایل تابلو و عدد',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'tag_bg',
            array(
                'label'     => 'رنگ پس‌زمینه تابلو (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-tag' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'tag_color',
            array(
                'label'     => 'رنگ متن تابلو (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#4B5563',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-tag' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'tag_hover_bg',
            array(
                'label'     => 'رنگ پس‌زمینه تابلو (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#03255C',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-card:hover .tno-about-stat-tag' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'tag_hover_color',
            array(
                'label'     => 'رنگ متن تابلو (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-card:hover .tno-about-stat-tag' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'tag_typography',
                'label'    => 'تایپوگرافی برچسب',
                'selector' => '{{WRAPPER}} .tno-about-stat-tag__text',
            )
        );

        $this->add_control(
            'number_color',
            array(
                'label'     => 'رنگ عدد',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-stat-card__num' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'number_typography',
                'label'    => 'تایپوگرافی عدد',
                'selector' => '{{WRAPPER}} .tno-about-stat-card__num',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items    = $settings['stats_list'] ?? array();
        if ( empty( $items ) ) {
            return;
        }
        $has_dots = ( 'yes' === ( $settings['show_dot_pattern'] ?? 'yes' ) );
        ?>
        <section class="tno-about-stats" dir="rtl">
            <div class="tno-about-stats__grid">
                <?php foreach ( $items as $item ) : ?>
                    <article class="tno-about-stat-card <?php echo $has_dots ? 'has-dots' : ''; ?>">
                        <div class="tno-about-stat-tag-wrap">
                            <span class="tno-about-stat-wire tno-about-stat-wire--right" aria-hidden="true"></span>
                            <span class="tno-about-stat-wire tno-about-stat-wire--left" aria-hidden="true"></span>
                            <div class="tno-about-stat-tag">
                                <span class="tno-about-stat-pin tno-about-stat-pin--right" aria-hidden="true"></span>
                                <span class="tno-about-stat-tag__text"><?php echo esc_html( $item['stat_label'] ?? '' ); ?></span>
                                <span class="tno-about-stat-pin tno-about-stat-pin--left" aria-hidden="true"></span>
                            </div>
                        </div>
                        <div class="tno-about-stat-card__num">
                            <?php echo esc_html( $item['stat_number'] ?? '' ); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
