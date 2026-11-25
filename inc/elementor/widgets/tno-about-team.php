<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;

/**
 * Trendino About Us: Team Members Grid Widget.
 */
class TNO_Widget_About_Team extends Widget_Base {

    public function get_name() {
        return 'tno_about_team';
    }

    public function get_title() {
        return 'اعضای تیم (درباره ما)';
    }

    public function get_icon() {
        return 'eicon-person';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'team', 'member', 'staff', 'about', 'تیم', 'اعضا', 'درباره ما', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'اعضای تیم',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'member_image',
            array(
                'label'   => 'تصویر عضو تیم',
                'type'    => Controls_Manager::MEDIA,
                'default' => array(
                    'url' => Utils::get_placeholder_image_src(),
                ),
            )
        );

        $repeater->add_control(
            'member_name',
            array(
                'label'       => 'نام و نام خانوادگی',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'علی محمودی',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'member_role',
            array(
                'label'       => 'سمت / نقش',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'مدیرعامل',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'team_members',
            array(
                'label'       => 'لیست اعضا',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ member_name }}} — {{{ member_role }}}',
                'default'     => array(
                    array(
                        'member_name' => 'علی محمودی',
                        'member_role' => 'مدیرعامل',
                    ),
                    array(
                        'member_name' => 'محمد رستگاری',
                        'member_role' => 'معاون',
                    ),
                    array(
                        'member_name' => 'حسام خدابنده لو',
                        'member_role' => 'مدیریت سایت',
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
                    '{{WRAPPER}} .tno-about-team__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
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
                    '{{WRAPPER}} .tno-about-team__grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE
         * ========================================================= */
        $this->start_controls_section(
            'section_style_team',
            array(
                'label' => 'استایل کارت و تصویر',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کارت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'card_border_color',
            array(
                'label'     => 'رنگ حاشیه کارت (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F2F4F7',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'card_hover_border_color',
            array(
                'label'     => 'رنگ حاشیه کارت (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#E7E7E7',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card:hover' => 'border-color: {{VALUE}};' ),
            )
        );

        $this->add_responsive_control(
            'card_radius',
            array(
                'label'     => 'گردی گوشه کارت',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'image_height',
            array(
                'label'     => 'ارتفاع تصویر',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 140, 'max' => 480 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card__media' => 'height: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_control(
            'image_bg',
            array(
                'label'     => 'رنگ پس‌زمینه قاب تصویر',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#D4E2FF',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card__media' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'hover_zoom',
            array(
                'label'     => 'بزرگنمایی تصویر در هاور',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 1, 'max' => 1.3, 'step' => 0.01 ) ),
                'default'   => array( 'size' => 1.07 ),
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card:hover .tno-about-team-card__img' => 'transform: scale({{SIZE}});' ),
            )
        );

        $this->add_control(
            'name_color',
            array(
                'label'     => 'رنگ نام',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card__name' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'name_typography',
                'label'    => 'تایپوگرافی نام',
                'selector' => '{{WRAPPER}} .tno-about-team-card__name',
            )
        );

        $this->add_control(
            'role_color',
            array(
                'label'     => 'رنگ سمت',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6B7280',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-team-card__role' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'role_typography',
                'label'    => 'تایپوگرافی سمت',
                'selector' => '{{WRAPPER}} .tno-about-team-card__role',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $members  = $settings['team_members'] ?? array();
        if ( empty( $members ) ) {
            return;
        }
        ?>
        <section class="tno-about-team" dir="rtl">
            <div class="tno-about-team__grid">
                <?php foreach ( $members as $member ) :
                    $img_url = ! empty( $member['member_image']['url'] ) ? $member['member_image']['url'] : Utils::get_placeholder_image_src();
                    $name    = $member['member_name'] ?? '';
                    $role    = $member['member_role'] ?? '';
                    ?>
                    <article class="tno-about-team-card">
                        <div class="tno-about-team-card__media">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="tno-about-team-card__img" loading="lazy">
                        </div>
                        <div class="tno-about-team-card__body">
                            <?php if ( $name !== '' ) : ?>
                                <h3 class="tno-about-team-card__name"><?php echo esc_html( $name ); ?></h3>
                            <?php endif; ?>
                            <?php if ( $role !== '' ) : ?>
                                <p class="tno-about-team-card__role"><?php echo esc_html( $role ); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
