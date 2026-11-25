<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

/**
 * Trendino About Us: Horizontal History Timeline Widget.
 */
class TNO_Widget_About_Timeline extends Widget_Base {

    public function get_name() {
        return 'tno_about_timeline';
    }

    public function get_title() {
        return 'خط زمان تاریخچه (درباره ما)';
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'timeline', 'history', 'about', 'تاریخچه', 'خط زمان', 'درباره ما', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'رویدادهای خط زمان',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'item_date',
            array(
                'label'       => 'تاریخ',
                'type'        => Controls_Manager::TEXT,
                'default'     => '۲۷ اسفند ماه ۱۴۰۲',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'item_title',
            array(
                'label'       => 'عنوان رویداد',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'شروع فعالیت ترندینو',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'item_desc',
            array(
                'label'   => 'توضیحات کوتاه',
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 3,
                'default' => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'milestones',
            array(
                'label'       => 'لیست رویدادها',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ item_date }}} — {{{ item_title }}}',
                'default'     => array(
                    array(
                        'item_date'  => '۲۷ اسفند ماه ۱۴۰۲',
                        'item_title' => 'تاسیس فروشگاه',
                        'item_desc'  => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
                    ),
                    array(
                        'item_date'  => '۲۷ اسفند ماه ۱۴۰۲',
                        'item_title' => 'گسترش سبد محصولات',
                        'item_desc'  => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
                    ),
                    array(
                        'item_date'  => '۲۷ اسفند ماه ۱۴۰۲',
                        'item_title' => 'ارسال به سراسر کشور',
                        'item_desc'  => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
                    ),
                    array(
                        'item_date'  => '۲۷ اسفند ماه ۱۴۰۲',
                        'item_title' => 'باشگاه مشتریان ترندینو',
                        'item_desc'  => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
                    ),
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE
         * ========================================================= */
        $this->start_controls_section(
            'section_style_timeline',
            array(
                'label' => 'استایل خط زمان و نقاط',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'line_color',
            array(
                'label'     => 'رنگ خط افقی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#D1D5DB',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__track::before' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'dot_color',
            array(
                'label'     => 'رنگ نقطه مرکزی',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#E81327',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__dot' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'node_border_color',
            array(
                'label'     => 'رنگ حاشیه دایره (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#D1D5DB',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__node' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'node_hover_bg',
            array(
                'label'     => 'رنگ پس‌زمینه دایره (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FEE2E2',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__item:hover .tno-about-timeline__node' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'node_hover_border',
            array(
                'label'     => 'رنگ حاشیه دایره (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F87171',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__item:hover .tno-about-timeline__node' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'date_color',
            array(
                'label'     => 'رنگ تاریخ (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#9CA3AF',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__date' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'date_hover_color',
            array(
                'label'     => 'رنگ تاریخ (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__item:hover .tno-about-timeline__date' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'date_typography',
                'label'    => 'تایپوگرافی تاریخ',
                'selector' => '{{WRAPPER}} .tno-about-timeline__date',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => 'رنگ عنوان (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#4B5563',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label'     => 'رنگ عنوان (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__item:hover .tno-about-timeline__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .tno-about-timeline__title',
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => 'رنگ توضیحات',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6B7280',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-timeline__desc' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'label'    => 'تایپوگرافی توضیحات',
                'selector' => '{{WRAPPER}} .tno-about-timeline__desc',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $milestones = $settings['milestones'] ?? array();
        if ( empty( $milestones ) ) {
            return;
        }
        $count = count( $milestones );
        ?>
        <section class="tno-about-timeline" dir="rtl">
            <div class="tno-about-timeline__track" style="--tno-tl-cols: <?php echo esc_attr( (string) max( 1, $count ) ); ?>;">
                <?php foreach ( $milestones as $item ) : ?>
                    <article class="tno-about-timeline__item">
                        <span class="tno-about-timeline__date">
                            <?php echo esc_html( $item['item_date'] ?? '' ); ?>
                        </span>

                        <div class="tno-about-timeline__node-wrap">
                            <span class="tno-about-timeline__node" aria-hidden="true">
                                <span class="tno-about-timeline__dot"></span>
                            </span>
                        </div>

                        <h3 class="tno-about-timeline__title">
                            <?php echo esc_html( $item['item_title'] ?? '' ); ?>
                        </h3>

                        <?php if ( ! empty( $item['item_desc'] ) ) : ?>
                            <p class="tno-about-timeline__desc">
                                <?php echo nl2br( esc_html( $item['item_desc'] ) ); ?>
                            </p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
