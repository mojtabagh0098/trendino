<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

/**
 * Trendino About Us: Testimonials Carousel Widget.
 */
class TNO_Widget_About_Testimonials extends Widget_Base {

    public function get_name() {
        return 'tno_about_testimonials';
    }

    public function get_title() {
        return 'نظرات مشتریان (درباره ما)';
    }

    public function get_icon() {
        return 'eicon-testimonial-carousel';
    }

    public function get_categories() {
        return array( 'trendino' );
    }

    public function get_keywords() {
        return array( 'testimonial', 'review', 'carousel', 'about', 'نظرات', 'مشتریان', 'درباره ما', 'ترندینو' );
    }

    public function get_script_depends() {
        return array( 'tno-widgets-script' );
    }

    protected function register_controls() {

        /* =========================================================
         * TAB: CONTENT
         * ========================================================= */
        $this->start_controls_section(
            'section_content',
            array(
                'label' => 'نظرات مشتریان',
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'author_name',
            array(
                'label'       => 'نام مشتری',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'امیرسام تیموری',
                'label_block' => true,
                'dynamic'     => array( 'active' => true ),
            )
        );

        $repeater->add_control(
            'author_avatar',
            array(
                'label'       => 'تصویر آواتار (اختیاری)',
                'type'        => Controls_Manager::MEDIA,
                'description' => 'در صورت خالی بودن، آیکون پیش‌فرض کاربر نمایش داده می‌شود.',
            )
        );

        $repeater->add_control(
            'quote_text',
            array(
                'label'   => 'متن نظر',
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 4,
                'default' => '«یکی از مهم‌ترین دغدغه‌های من هنگام خرید آنلاین، اصالت کالا بود. خوشبختانه محصولی که از ترندینو سفارش دادم کاملاً اورجینال بود و تمام جزئیات آن با نمونه اصلی مطابقت داشت. این سطح از اعتماد و صداقت واقعاً ارزشمند است.»',
                'dynamic' => array( 'active' => true ),
            )
        );

        $this->add_control(
            'testimonials',
            array(
                'label'       => 'لیست نظرات',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ author_name }}}',
                'default'     => array(
                    array(
                        'author_name' => 'الهام رشوندی',
                        'quote_text'  => '«بعد از تجربه خرید از ترندینو، خیالم بابت خرید کالاهای اورجینال راحت شد. محصول دقیقاً همان چیزی بود که انتظار داشتم و کیفیت ساخت، بسته‌بندی و جزئیات آن اصالت کالا را به‌خوبی نشان می‌داد. قطعاً این فروشگاه را به دیگران هم پیشنهاد می‌کنم.»',
                    ),
                    array(
                        'author_name' => 'امیرسام تیموری',
                        'quote_text'  => '«یکی از مهم‌ترین دغدغه‌های من هنگام خرید آنلاین، اصالت کالا بود. خوشبختانه کالایی که از ترندینو سفارش دادم کاملاً اورجینال بود و تمام جزئیات آن با نمونه اصلی مطابقت داشت. این سطح از اعتماد و صداقت واقعاً ارزشمند است.»',
                    ),
                    array(
                        'author_name' => 'محمد فرخی',
                        'quote_text'  => '«برخلاف بسیاری از فروشگاه‌ها، محصولی که از ترندینو دریافت کردم دقیقاً مطابق تصاویر و توضیحات سایت بود. اصالت کالا کاملاً مشخص بود و کیفیت آن فراتر از انتظارم بود. از خرید خودم کاملاً راضی هستم و بدون شک برای خریدهای بعدی هم ترندینو را انتخاب می‌کنم.»',
                    ),
                    array(
                        'author_name' => 'سارا کریمی',
                        'quote_text'  => '«ارسال سریع، پشتیبانی پاسخگو و بسته‌بندی بسیار مرتب باعث شد یکی از بهترین تجربه‌های خرید اینترنتی خودم را در ترندینو داشته باشم.»',
                    ),
                ),
            )
        );

        $this->add_control(
            'show_arrows',
            array(
                'label'        => 'نمایش دکمه‌های قبلی / بعدی',
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();

        /* =========================================================
         * TAB: STYLE
         * ========================================================= */
        $this->start_controls_section(
            'section_style_testi',
            array(
                'label' => 'استایل کارت‌های نظر',
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کارت (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F8F9FA',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'card_hover_bg',
            array(
                'label'     => 'رنگ پس‌زمینه کارت (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#222222',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card:hover' => 'background-color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'name_color',
            array(
                'label'     => 'رنگ نام (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1F2937',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card__name' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'name_hover_color',
            array(
                'label'     => 'رنگ نام (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card:hover .tno-about-testi-card__name' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'name_typography',
                'label'    => 'تایپوگرافی نام',
                'selector' => '{{WRAPPER}} .tno-about-testi-card__name',
            )
        );

        $this->add_control(
            'quote_color',
            array(
                'label'     => 'رنگ متن نظر (عادی)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6B7280',
                'separator' => 'before',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card__quote' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_control(
            'quote_hover_color',
            array(
                'label'     => 'رنگ متن نظر (هاور)',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#D1D5DB',
                'selectors' => array( '{{WRAPPER}} .tno-about-testi-card:hover .tno-about-testi-card__quote' => 'color: {{VALUE}};' ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'quote_typography',
                'label'    => 'تایپوگرافی متن نظر',
                'selector' => '{{WRAPPER}} .tno-about-testi-card__quote',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings    = $this->get_settings_for_display();
        $items       = $settings['testimonials'] ?? array();
        $show_arrows = ( 'yes' === ( $settings['show_arrows'] ?? 'yes' ) );

        if ( empty( $items ) ) {
            return;
        }
        ?>
        <section class="tno-about-testi" dir="rtl" data-tno-testi-carousel="1">
            <?php if ( $show_arrows && count( $items ) > 1 ) : ?>
                <button type="button" class="tno-about-testi__nav tno-about-testi__nav--prev" aria-label="قبلی" data-testi-prev="1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            <?php endif; ?>

            <div class="tno-about-testi__viewport">
                <div class="tno-about-testi__track" data-testi-track="1">
                    <?php foreach ( $items as $item ) :
                        $avatar_url = ! empty( $item['author_avatar']['url'] ) ? $item['author_avatar']['url'] : '';
                        $name       = $item['author_name'] ?? '';
                        $quote      = $item['quote_text'] ?? '';
                        ?>
                        <div class="tno-about-testi__slide">
                            <article class="tno-about-testi-card">
                                <div class="tno-about-testi-card__avatar" aria-hidden="true">
                                    <?php if ( $avatar_url !== '' ) : ?>
                                        <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                                    <?php endif; ?>
                                </div>
                                <h3 class="tno-about-testi-card__name"><?php echo esc_html( $name ); ?></h3>
                                <p class="tno-about-testi-card__quote"><?php echo nl2br( esc_html( $quote ) ); ?></p>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ( $show_arrows && count( $items ) > 1 ) : ?>
                <button type="button" class="tno-about-testi__nav tno-about-testi__nav--next" aria-label="بعدی" data-testi-next="1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            <?php endif; ?>
        </section>
        <?php
    }
}
