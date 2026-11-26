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
 * Trendino Social Media Cards Widget (شبکه‌های اجتماعی).
 */
class TNO_Widget_Social_Cards extends Widget_Base {

    public function get_name() {
        return 'tno_social_cards';
    }

    public function get_title() {
        return 'کارت‌های شبکه‌های اجتماعی ترندینو';
    }

    public function get_icon() {
        return 'eicon-social-icons';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'social', 'instagram', 'telegram', 'whatsapp', 'cards', 'شبکه اجتماعی', 'اینستاگرام', 'تلگرام', 'واتساپ', 'ترندینو' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'کارت‌های شبکه‌های اجتماعی',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'card_image',
            array(
                'label'   => 'تصویر داخل کارت',
                'type'    => Controls_Manager::MEDIA,
                'default' => array(
                    'url' => Utils::get_placeholder_image_src(),
                ),
            )
        );

        $repeater->add_control(
            'card_title',
            array(
                'label'       => 'عنوان',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'واتس اپ',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'dot_color',
            array(
                'label'   => 'رنگ نقطه کنار عنوان',
                'type'    => Controls_Manager::COLOR,
                'default' => '#10B981',
            )
        );

        $repeater->add_control(
            'card_desc',
            array(
                'label'       => 'توضیح کوتاه',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'در واتس اپ با پشتیبانی در ارتباط باشید.',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'card_link',
            array(
                'label'       => 'لینک کارت',
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://...',
                'default'     => array( 'url' => '#' ),
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'cards_list',
            array(
                'label'       => 'لیست کارت‌ها (تعداد داینامیک)',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ card_title }}}',
                'default'     => array(
                    array(
                        'card_title' => 'واتس اپ',
                        'dot_color'  => '#10B981',
                        'card_desc'  => 'در واتس اپ با پشتیبانی در ارتباط باشید.',
                    ),
                    array(
                        'card_title' => 'اینستاگرام',
                        'dot_color'  => '#EC4899',
                        'card_desc'  => 'در اینستاگرام جدیدترین محصولات را ببینید.',
                    ),
                    array(
                        'card_title' => 'تلگرام',
                        'dot_color'  => '#38BDF8',
                        'card_desc'  => 'در تلگرام از جدیدترین تخفیف‌ها باخبر شوید.',
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
                    '{{WRAPPER}} .tno-social-cards__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
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
                    '{{WRAPPER}} .tno-social-cards__grid' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE
         * ========================================================= */
        $this->start_controls_section(
            'section_style_cards',
            array(
                'label' => 'استایل باکس تصویر و متن‌ها',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'media_bg',
            array(
                'label'     => 'رنگ پس‌زمینه باکس تصویر',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F8F9FA',
                'selectors' => array( '{{WRAPPER}} .tno-social-card__media' => 'background-color: {{VALUE}};' ),
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
            'media_height',
            array(
                'label'     => 'ارتفاع باکس تصویر',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 140, 'max' => 420 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-social-card__media' => 'height: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_responsive_control(
            'media_radius',
            array(
                'label'     => 'گردی گوشه باکس تصویر',
                'type'      => Controls_Manager::SLIDER,
                'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
                'selectors' => array( '{{WRAPPER}} .tno-social-card__media' => 'border-radius: {{SIZE}}{{UNIT}};' ),
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => 'رنگ عنوان',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-social-card__title' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => 'تایپوگرافی عنوان',
                'selector' => '{{WRAPPER}} .tno-social-card__title',
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => 'رنگ توضیح',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6B7280',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-social-card__desc' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'desc_typography',
                'label'    => 'تایپوگرافی توضیح',
                'selector' => '{{WRAPPER}} .tno-social-card__desc',
            )
        );

        $this->add_control(
            'arrow_color',
            array(
                'label'     => 'رنگ فلش',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#374151',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-social-card__arrow' => 'color: {{VALUE}};' ),
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
        $has_dots = ( 'yes' === ( $settings['show_dot_pattern'] ?? 'yes' ) );
        ?>
        <section class="tno-social-cards" dir="rtl">
            <div class="tno-social-cards__grid">
                <?php foreach ( $cards as $index => $card ) :
                    $img_url   = ! empty( $card['card_image']['url'] ) ? $card['card_image']['url'] : Utils::get_placeholder_image_src();
                    $title     = $card['card_title'] ?? '';
                    $desc      = $card['card_desc'] ?? '';
                    $dot_color = $card['dot_color'] ?? '#10B981';
                    $link_url  = $card['card_link']['url'] ?? '';
                    $tag       = $link_url !== '' ? 'a' : 'div';

                    if ( $link_url !== '' ) {
                        $this->add_link_attributes( 'social_card_' . $index, $card['card_link'] );
                    }
                    ?>
                    <<?php echo esc_attr( $tag ); ?> <?php echo $link_url !== '' ? $this->get_render_attribute_string( 'social_card_' . $index ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="tno-social-card">
                        <div class="tno-social-card__media <?php echo $has_dots ? 'has-dots' : ''; ?>">
                            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="tno-social-card__img" loading="lazy">
                        </div>

                        <div class="tno-social-card__body">
                            <div class="tno-social-card__info">
                                <?php if ( $title !== '' ) : ?>
                                    <div class="tno-social-card__title-row">
                                        <span class="tno-social-card__dot" style="background-color: <?php echo esc_attr( $dot_color ); ?>;" aria-hidden="true"></span>
                                        <h3 class="tno-social-card__title"><?php echo esc_html( $title ); ?></h3>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $desc !== '' ) : ?>
                                    <p class="tno-social-card__desc"><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>
                            </div>

                            <span class="tno-social-card__arrow" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            </span>
                        </div>
                    </<?php echo esc_attr( $tag ); ?>>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
