<?php
/**
 * Trendino Elementor Archive (PLP) Widgets
 * Path: inc/elementor/widgets/tno-archive-plp.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ویجت ۱: نوار دسته‌بندی‌های بالای صفحه آرشیو
 */
class Elementor_TNO_PLP_Categories_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'tno_plp_categories'; }
    public function get_title() { return 'نوار دسته‌بندی‌های آرشیو (PLP)'; }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return [ 'trendino' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'content_sec', [
            'label' => 'تنظیمات دسته‌بندی‌ها',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'source', [
            'label'   => 'منبع نمایش',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => [
                'auto'   => 'دریافت خودکار دسته‌های ووکامرس',
                'manual' => 'انتخاب دستی آیتم‌ها',
            ],
        ] );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'title', [
            'label'   => 'عنوان دسته',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'لپتاپ',
        ] );
        $repeater->add_control( 'img', [
            'label'   => 'تصویر دسته',
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
        ] );
        $repeater->add_control( 'link', [
            'label' => 'لینک دسته',
            'type'  => \Elementor\Controls_Manager::URL,
        ] );
        $repeater->add_control( 'active', [
            'label'   => 'حالت فعال (هایلایت آبی)',
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'no',
        ] );

        $this->add_control( 'items', [
            'label'       => 'لیست دسته‌ها',
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'title_field' => '{{{ title }}}',
            'condition'   => [ 'source' => 'manual' ],
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style_sec', [
            'label' => 'استایل',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'active_bg', [
            'label'     => 'رنگ پس‌زمینه کارت فعال',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#DCE8FA',
            'selectors' => [ '{{WRAPPER}} .tno-plp-cat-item.is-active .tno-plp-cat-box' => 'background-color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $custom   = array();

        if ( 'manual' === $settings['source'] && ! empty( $settings['items'] ) ) {
            foreach ( $settings['items'] as $item ) {
                $custom[] = array(
                    'title'  => $item['title'],
                    'img'    => ! empty( $item['img']['url'] ) ? $item['img']['url'] : '',
                    'url'    => ! empty( $item['link']['url'] ) ? $item['link']['url'] : '#',
                    'active' => ( 'yes' === $item['active'] ),
                );
            }
        }

        if ( function_exists( 'tno_render_plp_top_categories' ) ) {
            tno_render_plp_top_categories( $custom );
        }
    }
}

/**
 * ویجت ۲: گرید محصولات + سایدبار فیلتر ایجکس + نوار مرتب‌سازی
 */
class Elementor_TNO_Archive_Products_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'tno_archive_products'; }
    public function get_title() { return 'گرید و فیلتر محصولات آرشیو (PLP)'; }
    public function get_icon() { return 'eicon-products'; }
    public function get_categories() { return [ 'trendino' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'layout_sec', [
            'label' => 'چیدمان و تنظیمات',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_breadcrumb', [
            'label'   => 'نمایش مسیر راهنما (Breadcrumb) در بالا',
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'no',
        ] );

        $this->add_control( 'show_sidebar', [
            'label'   => 'نمایش سایدبار فیلتر محصولات',
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );

        $this->add_control( 'columns', [
            'label'   => 'تعداد ستون محصولات',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'default' => '4',
            'options' => [
                '3' => '۳ ستون',
                '4' => '۴ ستون (مطابق فیگما)',
                '5' => '۵ ستون',
            ],
        ] );

        $this->add_control( 'per_page', [
            'label'   => 'تعداد محصول در هر صفحه',
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'default' => 24,
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'style_sec', [
            'label' => 'رنگ‌بندی',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'active_sort_bg', [
            'label'     => 'رنگ تب فعال مرتب‌سازی',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#012561',
            'selectors' => [ '{{WRAPPER}} .tno-sort-pill.is-active' => 'background-color: {{VALUE}};' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( 'yes' === $settings['show_breadcrumb'] && function_exists( 'tno_render_plp_breadcrumb' ) ) {
            tno_render_plp_breadcrumb();
        }

        if ( function_exists( 'tno_render_complete_plp_section' ) ) {
            tno_render_complete_plp_section( array(
                'columns'      => $settings['columns'],
                'per_page'     => $settings['per_page'],
                'show_sidebar' => ( 'yes' === $settings['show_sidebar'] ),
            ) );
        }
    }
}

/**
 * ویجت ۳: باکس توضیحات سئو دسته‌بندی (با دکمه مشاهده بیشتر)
 */
class Elementor_TNO_Category_SEO_Widget extends \Elementor\Widget_Base {

    public function get_name() { return 'tno_category_seo'; }
    public function get_title() { return 'باکس توضیحات سئو آرشیو (PLP)'; }
    public function get_icon() { return 'eicon-text-area'; }
    public function get_categories() { return [ 'trendino' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'content_sec', [
            'label' => 'محتوای باکس سئو',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'dynamic_cat', [
            'label'   => 'خواندن خودکار از دسته‌بندی فعلی',
            'type'    => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );

        $this->add_control( 'title', [
            'label'     => 'عنوان سئو',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'قیمت گوشی موبایل',
            'condition' => [ 'dynamic_cat!' => 'yes' ],
        ] );

        $this->add_control( 'content', [
            'label'     => 'متن کامل سئو',
            'type'      => \Elementor\Controls_Manager::WYSIWYG,
            'default'   => 'قیمت گوشی موبایل به عوامل مختلفی از جمله برند، مدل، حافظه داخلی و کیفیت دوربین بستگی دارد...',
            'condition' => [ 'dynamic_cat!' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if ( ! function_exists( 'tno_render_plp_seo_box' ) ) {
            return;
        }

        if ( 'yes' === $settings['dynamic_cat'] ) {
            tno_render_plp_seo_box();
        } else {
            tno_render_plp_seo_box( $settings['title'], $settings['content'] );
        }
    }
}