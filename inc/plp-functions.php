<?php
/**
 * Trendino WooCommerce Archive (PLP) Core Functions, Meta Boxes, Customizer & AJAX Filter
 * Path: inc/plp-functions.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ۱. فعال‌سازی پشتیبانی رسمی ووکامرس در قالب
 */
add_action( 'after_setup_theme', 'tno_woocommerce_setup' );
function tno_woocommerce_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

// غیرفعال کردن استایل‌های پیش‌فرض ووکامرس برای جلوگیری از تداخل با طراحی فیگما
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * ۲. ثبت فایل‌های CSS و JS اختصاصی صفحه آرشیو
 */
add_action( 'wp_enqueue_scripts', 'tno_plp_enqueue_assets', 20 );
function tno_plp_enqueue_assets() {
    wp_enqueue_style(
        'tno-plp-style',
        get_template_directory_uri() . '/assets/css/plp.css',
        array( 'style' ),
        '1.0.1'
    );

    wp_enqueue_script(
        'tno-plp-script',
        get_template_directory_uri() . '/assets/js/plp.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script( 'tno-plp-script', 'tnoPlpData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tno_plp_nonce' ),
    ) );
}

/**
 * ۳. متاباکس تنظیمات کارت محصول (لیبل سفارشی، رنگ لیبل، ارسال امروز)
 */
add_action( 'add_meta_boxes', 'tno_register_product_plp_metabox' );
function tno_register_product_plp_metabox() {
    add_meta_box(
        'tno_product_plp_meta',
        'تنظیمات کارت محصول (ترندینو)',
        'tno_render_product_plp_metabox',
        'product',
        'side',
        'high'
    );
}

function tno_render_product_plp_metabox( $post ) {
    wp_nonce_field( 'tno_save_product_meta', 'tno_product_meta_nonce' );

    $badge_text  = get_post_meta($post->ID, 'label', true );
    $badge_style = get_post_meta($post->ID, '_tno_badge_style', true ) ?: 'pink';
    $fast_send   = get_post_meta($post->ID, '_tno_fast_send', true );
    ?>
    <p>
        <label for="tno_badge_text"><strong>متن لیبل بالای کارت:</strong></label><br>
        <input type="text" id="tno_badge_text" name="tno_badge_text" value="<?php echo esc_attr( $badge_text ); ?>" placeholder="مثال: لیبل سفارشی" style="width:100%;margin-top:4px;">
    </p>
    <p>
        <label for="tno_badge_style"><strong>استایل لیبل:</strong></label><br>
        <select id="tno_badge_style" name="tno_badge_style" style="width:100%;margin-top:4px;">
            <option value="pink" <?php selected( $badge_style, 'pink' ); ?>>صورتی ستاره‌دار (لیبل سفارشی)</option>
            <option value="blue" <?php selected( $badge_style, 'blue' ); ?>>آبی نقطه‌دار (لیبل عادی)</option>
        </select>
    </p>
    <p>
        <label>
            <input type="checkbox" name="tno_fast_send" value="yes" <?php checked( $fast_send, 'yes' ); ?>>
            <strong>دارای ارسال امروز (فیلتر ارسال سریع)</strong>
        </label>
    </p>
    <?php
}

add_action( 'save_post_product', 'tno_save_product_plp_metabox' );
function tno_save_product_plp_metabox( $post_id ) {
    if ( ! isset( $_POST['tno_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tno_product_meta_nonce'] ) ), 'tno_save_product_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['tno_badge_text'] ) ) {
        update_post_meta( $post_id, 'label', sanitize_text_field( wp_unslash($_POST['tno_badge_text'] ) ) );
    }
    if ( isset( $_POST['tno_badge_style'] ) ) {
        update_post_meta( $post_id, '_tno_badge_style', sanitize_key( wp_unslash($_POST['tno_badge_style'] ) ) );
    }
    $fast_send = isset($_POST['tno_fast_send'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_tno_fast_send',$fast_send );
}

/**
 * ۴. فیلدهای سفارشی دسته‌بندی محصولات (product_cat) برای باکس سئوی پایین صفحه
 * + فیلد کد رنگ برای ویژگی رنگ (pa_color)
 */
add_action( 'product_cat_add_form_fields', 'tno_add_category_seo_fields' );
function tno_add_category_seo_fields() {
    ?>
    <div class="form-field">
        <label for="tno_cat_seo_title">عنوان باکس سئو (پایین صفحه دسته‌بندی)</label>
        <input type="text" name="tno_cat_seo_title" id="tno_cat_seo_title" placeholder="مثال: قیمت گوشی موبایل">
    </div>
    <div class="form-field">
        <label for="tno_cat_seo_content">متن کامل سئو (نمایش با دکمه مشاهده بیشتر)</label>
        <textarea name="tno_cat_seo_content" id="tno_cat_seo_content" rows="5"></textarea>
    </div>
    <?php
}

add_action( 'product_cat_edit_form_fields', 'tno_edit_category_seo_fields', 10, 1 );
function tno_edit_category_seo_fields( $term ) {
    $seo_title   = get_term_meta($term->term_id, '_tno_cat_seo_title', true );
    $seo_content = get_term_meta($term->term_id, '_tno_cat_seo_content', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="tno_cat_seo_title">عنوان باکس سئو (پایین صفحه)</label></th>
        <td>
            <input type="text" name="tno_cat_seo_title" id="tno_cat_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" placeholder="مثال: قیمت گوشی موبایل">
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row"><label for="tno_cat_seo_content">متن کامل باکس سئو</label></th>
        <td>
            <?php
            wp_editor(
                $seo_content,
                'tno_cat_seo_content',
                array(
                    'textarea_name' => 'tno_cat_seo_content',
                    'textarea_rows' => 8,
                    'media_buttons' => false,
                )
            );
            ?>
            <p class="description">اگر خالی باشد، از «توضیحات» پیش‌فرض دسته‌بندی استفاده می‌شود.</p>
        </td>
    </tr>
    <?php
}

add_action( 'created_product_cat', 'tno_save_category_seo_fields' );
add_action( 'edited_product_cat', 'tno_save_category_seo_fields' );
function tno_save_category_seo_fields( $term_id ) {
    if ( isset( $_POST['tno_cat_seo_title'] ) ) {
        update_term_meta( $term_id, '_tno_cat_seo_title', sanitize_text_field( wp_unslash($_POST['tno_cat_seo_title'] ) ) );
    }
    if ( isset( $_POST['tno_cat_seo_content'] ) ) {
        update_term_meta( $term_id, '_tno_cat_seo_content', wp_kses_post( wp_unslash($_POST['tno_cat_seo_content'] ) ) );
    }
}

// افزودن فیلد انتخاب کد رنگ به ویژگی رنگ (در صورت وجود ویژگی رنگ)
add_action( 'admin_init', 'tno_register_color_attribute_field' );
function tno_register_color_attribute_field() {
    $color_tax = get_theme_mod( 'tno_plp_color_tax', 'pa_color' );
    if ( taxonomy_exists( $color_tax ) ) {
        add_action( "{$color_tax}_add_form_fields", 'tno_add_color_hex_field' );
        add_action( "{$color_tax}_edit_form_fields", 'tno_edit_color_hex_field', 10, 1 );
        add_action( "created_{$color_tax}", 'tno_save_color_hex_field' );
        add_action( "edited_{$color_tax}", 'tno_save_color_hex_field' );
    }
}

function tno_add_color_hex_field() {
    ?>
    <div class="form-field">
        <label for="tno_color_hex">کد رنگ (Hex)</label>
        <input type="color" name="tno_color_hex" id="tno_color_hex" value="#012561">
    </div>
    <?php
}

function tno_edit_color_hex_field( $term ) {
    $hex = get_term_meta($term->term_id, '_tno_color_hex', true ) ?: '#012561';
    ?>
    <tr class="form-field">
        <th scope="row"><label for="tno_color_hex">کد رنگ (Hex)</label></th>
        <td><input type="color" name="tno_color_hex" id="tno_color_hex" value="<?php echo esc_attr( $hex ); ?>"></td>
    </tr>
    <?php
}

function tno_save_color_hex_field( $term_id ) {
    if ( isset( $_POST['tno_color_hex'] ) ) {
        update_term_meta( $term_id, '_tno_color_hex', sanitize_hex_color( wp_unslash($_POST['tno_color_hex'] ) ) );
    }
}

/**
 * ۵. تنظیمات پیشخوان وردپرس (نمایش > سفارشی‌سازی > تنظیمات صفحه آرشیو فروشگاه)
 */
add_action( 'customize_register', 'tno_plp_customizer_register' );
function tno_plp_customizer_register( $wp_customize ) {

    $wp_customize->add_section( 'tno_plp_section', array(
        'title'    => 'تنظیمات صفحه آرشیو فروشگاه (PLP)',
        'priority' => 35,
    ) );

    // انتخاب قالب المنتور جایگزین (در صورت تمایل به طراحی کل صفحه با المنتور)
    $elementor_templates = array( 0 => '— استفاده از چیدمان استاندارد فیگما —' );
    $templates = get_posts( array(
        'post_type'      => 'elementor_library',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ) );
    foreach ( $templates as $tpl ) {
        $elementor_templates[ $tpl->ID ] = $tpl->post_title;
    }

    $wp_customize->add_setting( 'tno_plp_elementor_template_id', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_plp_elementor_template_id', array(
        'label'       => 'قالب سفارشی المنتور برای آرشیو',
        'description' => 'اگر می‌خواهید کل صفحه آرشیو را با المنتور بچینید، یک قالب در المنتور بسازید و اینجا انتخاب کنید.',
        'section'     => 'tno_plp_section',
        'type'        => 'select',
        'choices'     => $elementor_templates,
    ) );

    // کنترل‌های نمایش اجزای صفحه
    $toggles = array(
        'tno_plp_show_breadcrumb'  => array( 'نمایش مسیر راهنما (Breadcrumb)', true ),
        'tno_plp_show_top_cats'    => array( 'نمایش نوار دسته‌بندی‌های بالای صفحه', true ),
        'tno_plp_show_sidebar'     => array( 'نمایش سایدبار فیلتر محصولات', true ),
        'tno_plp_show_cat_filter'  => array( 'نمایش فیلتر دسته‌بندی‌ها در سایدبار', true ),
        'tno_plp_show_brand_filter'=> array( 'نمایش فیلتر برند در سایدبار', true ),
        'tno_plp_show_color_filter'=> array( 'نمایش فیلتر رنگ در سایدبار', true ),
        'tno_plp_show_price_filter'=> array( 'نمایش فیلتر محدوده قیمت در سایدبار', true ),
        'tno_plp_show_toggles'     => array( 'نمایش سوئیچ‌های موجود / حراجی / ارسال امروز', true ),
        'tno_plp_show_seo_box'     => array( 'نمایش باکس توضیحات سئو در پایین صفحه', true ),
    );

    foreach ( $toggles as $id => $data ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $data[1],
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        $wp_customize->add_control( $id, array(
            'label'   => $data[0],
            'section' => 'tno_plp_section',
            'type'    => 'checkbox',
        ) );
    }

    // تعداد ستون گرید و تعداد محصول در هر صفحه
    $wp_customize->add_setting( 'tno_plp_columns', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_plp_columns', array(
        'label'   => 'تعداد ستون محصولات در دسکتاپ',
        'section' => 'tno_plp_section',
        'type'    => 'select',
        'choices' => array(
            3 => '۳ ستون',
            4 => '۴ ستون (مطابق فیگما)',
            5 => '۵ ستون',
        ),
    ) );

    $wp_customize->add_setting( 'tno_plp_per_page', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_plp_per_page', array(
        'label'   => 'تعداد محصول در هر صفحه',
        'section' => 'tno_plp_section',
        'type'    => 'number',
    ) );

    // نامک ویژگی برند و رنگ در ووکامرس
    $wp_customize->add_setting( 'tno_plp_brand_tax', array(
        'default'           => 'pa_brand',
        'sanitize_callback' => 'sanitize_key',
    ) );
    $wp_customize->add_control( 'tno_plp_brand_tax', array(
        'label'       => 'نامک تاکسونومی برند (Taxonomy)',
        'description' => 'پیش‌فرض ووکامرس: pa_brand یا product_brand',
        'section'     => 'tno_plp_section',
        'type'        => 'text',
    ) );

    $wp_customize->add_setting( 'tno_plp_color_tax', array(
        'default'           => 'pa_color',
        'sanitize_callback' => 'sanitize_key',
    ) );
    $wp_customize->add_control( 'tno_plp_color_tax', array(
        'label'       => 'نامک تاکسونومی رنگ (Taxonomy)',
        'description' => 'پیش‌فرض ووکامرس: pa_color',
        'section'     => 'tno_plp_section',
        'type'        => 'text',
    ) );

    // حداکثر قیمت اسلایدر
    $wp_customize->add_setting( 'tno_plp_max_price_limit', array(
        'default'           => 100000000,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_plp_max_price_limit', array(
        'label'   => 'سقف پیش‌فرض اسلایدر قیمت (تومان)',
        'section' => 'tno_plp_section',
        'type'    => 'number',
    ) );

    // عنوان و متن پیش‌فرض سئو (برای صفحه کلی فروشگاه)
    $wp_customize->add_setting( 'tno_plp_default_seo_title', array(
        'default'           => 'قیمت گوشی موبایل',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_plp_default_seo_title', array(
        'label'   => 'عنوان پیش‌فرض باکس سئو',
        'section' => 'tno_plp_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tno_plp_default_seo_text', array(
        'default'           => '<p>قیمت گوشی موبایل به عوامل مختلفی از جمله برند، مدل، حافظه داخلی، رم، کیفیت دوربین و ظرفیت باتری بستگی دارد. در فروشگاه اینترنتی ترندینو می‌توانید جدیدترین مدل‌های گوشی موبایل را با گارانتی معتبر و بهترین قیمت بازار مقایسه و خریداری کنید.</p><h4>بهترین گوشی موبایل را چگونه انتخاب کنیم؟</h4><p>برای انتخاب بهترین گوشی هوشمند ابتدا باید بودجه و نیاز اصلی خود را مشخص کنید. اگر به عکاسی و فیلم‌برداری علاقه‌مند هستید، کیفیت سنسور دوربین و پردازنده تصویر اولویت بالایی دارد؛ اما برای کاربری گیمینگ، پردازنده قدرتمند، نرخ نوسازی تصویر ۱۲۰ هرتز و سیستم خنک‌کننده اهمیت بیشتری پیدا می‌کند.</p>',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'tno_plp_default_seo_text', array(
        'label'   => 'متن پیش‌فرض باکس سئو',
        'section' => 'tno_plp_section',
        'type'    => 'textarea',
    ) );
}

/**
 * ۶. دریافت پارامترهای فیلتر از URL یا درخواست AJAX
 */
function tno_get_active_plp_filters( $source = null ) {
    if ( null === $source ) {
        $source = $_GET;
    }

    $cats   = array();
    if ( ! empty( $source['tno_cats'] ) ) {
        $cats = is_array( $source['tno_cats'] )
            ? array_map( 'sanitize_text_field', wp_unslash( $source['tno_cats'] ) )
            : array_filter( array_map( 'sanitize_text_field', explode( ',', wp_unslash( $source['tno_cats'] ) ) ) );
    } elseif ( is_product_category() && empty( $source['is_ajax'] ) ) {
        $queried = get_queried_object();
        if ( $queried && isset( $queried->slug ) ) {
            $cats[] = $queried->slug;
        }
    }

    $brands = array();
    if ( ! empty( $source['tno_brands'] ) ) {
        $brands = is_array( $source['tno_brands'] )
            ? array_map( 'sanitize_text_field', wp_unslash( $source['tno_brands'] ) )
            : array_filter( array_map( 'sanitize_text_field', explode( ',', wp_unslash( $source['tno_brands'] ) ) ) );
    }

    $colors = array();
    if ( ! empty( $source['tno_colors'] ) ) {
        $colors = is_array( $source['tno_colors'] )
            ? array_map( 'sanitize_text_field', wp_unslash( $source['tno_colors'] ) )
            : array_filter( array_map( 'sanitize_text_field', explode( ',', wp_unslash( $source['tno_colors'] ) ) ) );
    }

    $max_limit = absint( get_theme_mod( 'tno_plp_max_price_limit', 100000000 ) );
    $min_price = isset( $source['min_price'] ) && '' !== $source['min_price'] ? absint( $source['min_price'] ) : 0;
    $max_price = isset( $source['max_price'] ) && '' !== $source['max_price'] ? absint( $source['max_price'] ) : $max_limit;

    return array(
        'cats'      => $cats,
        'brands'    => $brands,
        'colors'    => $colors,
        'min_price' => $min_price,
        'max_price' => $max_price,
        'max_limit' => $max_limit,
        'in_stock'  => ! empty( $source['in_stock'] ) && '1' === (string) $source['in_stock'],
        'on_sale'   => ! empty( $source['on_sale'] ) && '1' === (string) $source['on_sale'],
        'fast_send' => ! empty( $source['fast_send'] ) && '1' === (string) $source['fast_send'],
        'orderby'   => isset( $source['orderby'] ) ? sanitize_key( $source['orderby'] ) : 'date',
        'paged'     => isset( $source['paged'] ) ? max( 1, absint( $source['paged'] ) ) : max( 1, get_query_var( 'paged', 1 ) ),
    );
}

/**
 * ۷. ساخت آرایه آرگومان‌های WP_Query بر اساس فیلترهای فعال
 */
function tno_build_plp_query_args( $filters, $per_page = 0 ) {
    if ( ! $per_page ) {
        $per_page = absint( get_theme_mod( 'tno_plp_per_page', 24 ) );
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $filters['paged'],
    );

    $tax_query  = array( 'relation' => 'AND' );
    $meta_query = array( 'relation' => 'AND' );

    // فیلتر دسته‌بندی
    if ( ! empty( $filters['cats'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $filters['cats'],
            'operator' => 'IN',
        );
    }

    // فیلتر برند
    $brand_tax = get_theme_mod( 'tno_plp_brand_tax', 'pa_brand' );
    if ( ! empty( $filters['brands'] ) && taxonomy_exists( $brand_tax ) ) {
        $tax_query[] = array(
            'taxonomy' => $brand_tax,
            'field'    => 'slug',
            'terms'    => $filters['brands'],
            'operator' => 'IN',
        );
    }

    // فیلتر رنگ
    $color_tax = get_theme_mod( 'tno_plp_color_tax', 'pa_color' );
    if ( ! empty( $filters['colors'] ) && taxonomy_exists( $color_tax ) ) {
        $tax_query[] = array(
            'taxonomy' => $color_tax,
            'field'    => 'slug',
            'terms'    => $filters['colors'],
            'operator' => 'IN',
        );
    }

    // فیلتر قیمت
    if ( $filters['min_price'] > 0 || $filters['max_price'] < $filters['max_limit'] ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => array( $filters['min_price'], $filters['max_price'] ),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        );
    }

    // فقط محصولات موجود
    if ( $filters['in_stock'] ) {
        $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        );
    }

    // ارسال امروز
    if ( $filters['fast_send'] ) {
        $meta_query[] = array(
            'key'     => '_tno_fast_send',
            'value'   => 'yes',
            'compare' => '=',
        );
    }

    // فقط محصولات حراجی
    if ( $filters['on_sale'] && function_exists( 'wc_get_product_ids_on_sale' ) ) {
        $sale_ids         = wc_get_product_ids_on_sale();
        $args['post__in'] = ! empty( $sale_ids ) ? $sale_ids : array( 0 );
    }

    // مرتب‌سازی
    switch ( $filters['orderby'] ) {
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'ASC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            break;
        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    if ( count( $tax_query ) > 1 ) {
        $args['tax_query'] = $tax_query;
    }
    if ( count( $meta_query ) > 1 ) {
        $args['meta_query'] = $meta_query;
    }

    return $args;
}

/**
 * ۸. کامپوننت ۱: رندر مسیر راهنما (Breadcrumb) مطابق فیگما
 */
function tno_render_plp_breadcrumb() {
    ?>
    <nav class="tno-plp-breadcrumb" aria-label="مسیر راهنما">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tno-bc-home" aria-label="صفحه اصلی">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>
        <span class="tno-bc-sep">
            <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M5 9L1 5L5 1" stroke="#8A8FA3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop' ) ); ?>">محصولات پرفروش</a>

        <?php if ( is_product_category() ) :
            $current_term = get_queried_object();
            $ancestors    = get_ancestors( $current_term->term_id, 'product_cat' );
            if ( ! empty( $ancestors ) ) : ?>
                <span class="tno-bc-sep">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M5 9L1 5L5 1" stroke="#8A8FA3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="tno-bc-dots">...</span>
            <?php endif; ?>
            <span class="tno-bc-sep">
                <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M5 9L1 5L5 1" stroke="#8A8FA3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <strong class="tno-bc-current"><?php echo esc_html( $current_term->name ); ?></strong>
        <?php else : ?>
            <span class="tno-bc-sep">
                <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M5 9L1 5L5 1" stroke="#8A8FA3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <strong class="tno-bc-current"><?php echo esc_html( is_search() ? 'نتایج جستجو: ' . get_search_query() : 'گوشی موبایل' ); ?></strong>
        <?php endif; ?>
    </nav>
    <?php
}

/**
 * ۹. کامپوننت ۲: نوار دسته‌بندی‌های بالای صفحه (با هایلایت آبی دسته فعال)
 */
function tno_render_plp_top_categories( $custom_items = array() ) {
    $current_cat_id = is_product_category() ? get_queried_object_id() : 0;

    if ( empty( $custom_items ) ) {
        $terms = get_terms( array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'parent'     => 0,
            'number'     => 8,
        ) );

        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
            foreach ( $terms as $index => $term ) {
                $thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                $img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : get_template_directory_uri() . '/assets/img/mobile.png';
                $custom_items[] = array(
                    'id'     => $term->term_id,
                    'slug'   => $term->slug,
                    'title'  => $term->name,
                    'url'    => get_term_link( $term ),
                    'img'    => $img_url,
                    'active' => ( $current_cat_id === $term->term_id ) || ( 0 === $current_cat_id && 0 === $index ),
                );
            }
        }
    }

    // فال‌بک نمونه مطابق عکس فیگما در صورت خالی بودن دسته‌ها
    if ( empty( $custom_items ) ) {
        $fallback_names = array(
            array( 'لپتاپ', 'laptop.png', true ),
            array( 'موبایل', 'mobile.png', false ),
            array( 'اسپیکر', 'headphone.png', false ),
            array( 'پاوربانک', 'mobile.png', false ),
            array( 'تبلت', 'tablet.png', false ),
            array( 'ساعت هوشمند', 'watch.png', false ),
            array( 'کامپیوتر', 'laptop.png', false ),
            array( 'کنسول بازی', 'ps5.png', false ),
            array( 'محافظ گوشی', 'mobile.png', false ),
        );
        foreach ( $fallback_names as $f ) {
            $custom_items[] = array(
                'title'  => $f[0],
                'url'    => '#',
                'img'    => get_template_directory_uri() . '/assets/img/' . $f[1],
                'active' => $f[2],
            );
        }
    }
    ?>
    <section class="tno-plp-cats-strip">
        <?php foreach ( $custom_items as $item ) :
            $active_cls = ! empty( $item['active'] ) ? ' is-active' : '';
        ?>
            <a href="<?php echo esc_url( $item['url'] ); ?>" class="tno-plp-cat-item<?php echo esc_attr( $active_cls ); ?>">
                <div class="tno-plp-cat-box">
                    <img src="<?php echo esc_url( $item['img'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy">
                </div>
                <span class="tno-plp-cat-title"><?php echo esc_html( $item['title'] ); ?></span>
            </a>
        <?php endforeach; ?>
    </section>
    <?php
}

/**
 * ۱۰. رندر تگ‌های «فیلترهای اعمال شده» در بالای سایدبار
 */
function tno_render_applied_filter_chips( $filters ) {
    $chips = array();

    // دسته‌بندی‌ها
    if ( ! empty( $filters['cats'] ) ) {
        foreach ( $filters['cats'] as $slug ) {
            $term = get_term_by( 'slug', $slug, 'product_cat' );
            if ( $term ) {
                $chips[] = array(
                    'type'  => 'cat',
                    'val'   => $slug,
                    'label' => 'دسته بندی ها : ' . $term->name,
                );
            }
        }
    }

    // برندها
    $brand_tax = get_theme_mod( 'tno_plp_brand_tax', 'pa_brand' );
    if ( ! empty( $filters['brands'] ) && taxonomy_exists( $brand_tax ) ) {
        foreach ( $filters['brands'] as $slug ) {
            $term = get_term_by( 'slug', $slug, $brand_tax );
            if ( $term ) {
                $chips[] = array(
                    'type'  => 'brand',
                    'val'   => $slug,
                    'label' => 'برند : ' . $term->name,
                );
            }
        }
    }

    // رنگ‌ها
    $color_tax = get_theme_mod( 'tno_plp_color_tax', 'pa_color' );
    if ( ! empty( $filters['colors'] ) && taxonomy_exists( $color_tax ) ) {
        foreach ( $filters['colors'] as $slug ) {
            $term = get_term_by( 'slug', $slug, $color_tax );
            if ( $term ) {
                $chips[] = array(
                    'type'  => 'color',
                    'val'   => $slug,
                    'label' => 'رنگ : ' . $term->name,
                );
            }
        }
    }

    // محدوده قیمت
    if ( $filters['min_price'] > 0 || $filters['max_price'] < $filters['max_limit'] ) {
        $chips[] = array(
            'type'  => 'price',
            'val'   => 'reset',
            'label' => 'قیمت : از ' . tno_fa_num( number_format( $filters['min_price'] ) ) . ' تا ' . tno_fa_num( number_format( $filters['max_price'] ) ),
        );
    }

    if ( $filters['in_stock'] ) {
        $chips[] = array( 'type' => 'toggle', 'val' => 'in_stock', 'label' => 'فقط کالاهای موجود' );
    }
    if ( $filters['on_sale'] ) {
        $chips[] = array( 'type' => 'toggle', 'val' => 'on_sale', 'label' => 'فقط کالاهای حراجی' );
    }
    if ( $filters['fast_send'] ) {
        $chips[] = array( 'type' => 'toggle', 'val' => 'fast_send', 'label' => 'ارسال امروز' );
    }

    if ( empty( $chips ) ) {
        return '';
    }

    ob_start();
    ?>
    <div class="tno-filter-block tno-applied-filters-block">
        <div class="tno-applied-head">
            <span>فیلترهای اعمال شده</span>
            <button type="button" class="tno-clear-all-filters" title="حذف همه فیلترها">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#E81327" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        </div>
        <div class="tno-applied-chips">
            <?php foreach ( $chips as $chip ) : ?>
                <button type="button" class="tno-applied-chip" data-filter-type="<?php echo esc_attr( $chip['type'] ); ?>" data-filter-val="<?php echo esc_attr( $chip['val'] ); ?>">
                    <span><?php echo esc_html( $chip['label'] ); ?></span>
                    <i class="tno-chip-remove">&times;</i>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * ۱۱. رندر کارت‌های محصول در گرید ۴ ستونه مطابق فیگما
 */
function tno_render_plp_products_loop( $query ) {
    ob_start();
    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            global $product;
            if ( ! is_a( $product, 'WC_Product' ) ) {
                $product = wc_get_product( get_the_ID() );
            }
            if ( ! $product ) {
                continue;
            }

            $custom_label  = get_post_meta( get_the_ID(), 'label', true );
            $badge_style   = get_post_meta( get_the_ID(), '_tno_badge_style', true ) ?: 'pink';
            $regular_price = (float) $product->get_regular_price();
            $active_price  = (float) $product->get_price();
            $is_on_sale    = $product->is_on_sale() && $regular_price > $active_price && $regular_price > 0;
            $discount_pct  = $is_on_sale ? round( ( ( $regular_price - $active_price ) / $regular_price ) * 100 ) : 0;
            ?>
            <article class="prod-card tno-plp-card">
                <?php if ( ! empty( $custom_label ) ) : ?>
                    <span class="prod-badge <?php echo 'blue' === $badge_style ? 'tno-badge--blue' : 'tno-badge--pink'; ?>">
                        <?php if ( 'pink' === $badge_style ) : ?>
                            <svg class="tno-badge-star" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7.2-6.3-4.6-6.3 4.6 2.3-7.2-6-4.6h7.6z"/></svg>
                        <?php else : ?>
                            <span class="tno-badge-dot"></span>
                        <?php endif; ?>
                        <?php echo esc_html( $custom_label ); ?>
                    </span>
                <?php endif; ?>

                <a href="<?php the_permalink(); ?>" class="prod-thumb">
                    <?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?>
                </a>

                <h3 class="prod-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>

                <div class="tno-plp-card__footer">
                    <?php if ( $is_on_sale ) : ?>
                        <div class="tno-plp-price-wrap">
                            <del class="tno-plp-old-price"><?php echo esc_html( tno_fa_num( number_format( $regular_price ) ) ); ?></del>
                            <div class="tno-plp-current-price">
                                <strong><?php echo esc_html( tno_fa_num( number_format( $active_price ) ) ); ?></strong>
                                <small>تومان</small>
                            </div>
                        </div>
                        <span class="prod-discount"><?php echo esc_html( tno_fa_num( $discount_pct ) ); ?>٪</span>
                    <?php else : ?>
                        <div class="tno-plp-price-wrap">
                            <div class="tno-plp-current-price">
                                <strong><?php echo esc_html( tno_fa_num( number_format( $active_price ) ) ); ?></strong>
                                <small>تومان</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        ?>
        <div class="tno-plp-empty-state">
            <p>محصولی با فیلترهای انتخابی شما یافت نشد.</p>
            <button type="button" class="tno-clear-all-filters tno-reset-btn">حذف همه فیلترها</button>
        </div>
        <?php
    endif;
    return ob_get_clean();
}

/**
 * ۱۲. رندر صفحه‌بندی (Pagination) دایره‌ای مطابق فیگما
 */
function tno_render_plp_pagination( $query, $current_page = 1 ) {
    $total_pages = (int) $query->max_num_pages;
    if ( $total_pages <= 1 ) {
        return '';
    }

    $links = paginate_links( array(
        'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
        'format'    => '',
        'current'   => max( 1, $current_page ),
        'total'     => $total_pages,
        'prev_text' => '<svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'next_text' => '<svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'type'      => 'array',
        'end_size'  => 1,
        'mid_size'  => 2,
    ) );

    if ( empty( $links ) ) {
        return '';
    }

    ob_start();
    ?>
    <nav class="tno-plp-pagination" aria-label="صفحه‌بندی محصولات">
        <ul>
            <?php foreach ( $links as $link ) : ?>
                <li><?php echo tno_fa_num( $link ); ?></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}

/**
 * ۱۳. کامپوننت اصلی: رندر کل بخش آرشیو (تولبار مرتب‌سازی + سایدبار فیلترها + گرید محصولات)
 */
function tno_render_complete_plp_section( $override_settings = array() ) {
    $filters    = tno_get_active_plp_filters();
    $columns    = isset( $override_settings['columns'] ) ? absint( $override_settings['columns'] ) : absint( get_theme_mod( 'tno_plp_columns', 4 ) );
    $per_page   = isset( $override_settings['per_page'] ) ? absint( $override_settings['per_page'] ) : absint( get_theme_mod( 'tno_plp_per_page', 24 ) );
    $show_side  = isset( $override_settings['show_sidebar'] ) ? (bool) $override_settings['show_sidebar'] : get_theme_mod( 'tno_plp_show_sidebar', true );

    $query_args = tno_build_plp_query_args( $filters, $per_page );
    $query      = new WP_Query( $query_args );
    $found      = (int) $query->found_posts;

    // دریافت لیست دسته‌بندی‌ها برای سایدبار
    $all_cats = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ) );

    // دریافت لیست برندها
    $brand_tax  = get_theme_mod( 'tno_plp_brand_tax', 'pa_brand' );
    $all_brands = taxonomy_exists( $brand_tax ) ? get_terms( array( 'taxonomy' => $brand_tax, 'hide_empty' => false ) ) : array();

    // دریافت لیست رنگ‌ها
    $color_tax  = get_theme_mod( 'tno_plp_color_tax', 'pa_color' );
    $all_colors = taxonomy_exists( $color_tax ) ? get_terms( array( 'taxonomy' => $color_tax, 'hide_empty' => false ) ) : array();

    // رنگ‌های پیش‌فرض مطابق فیگما (اگر هنوز ویژگی رنگ در ووکامرس تعریف نشده باشد)
    $default_colors = array(
        array( 'slug' => 'blue',   'name' => 'آبی',     'hex' => '#1D5BD6' ),
        array( 'slug' => 'green',  'name' => 'سبز',     'hex' => '#26B94E' ),
        array( 'slug' => 'orange', 'name' => 'نارنجی',  'hex' => '#F4931E' ),
        array( 'slug' => 'red',    'name' => 'قرمز',    'hex' => '#E5252A' ),
        array( 'slug' => 'black',  'name' => 'مشکی',    'hex' => '#1D1D1D' ),
        array( 'slug' => 'brown',  'name' => 'قهوه ای', 'hex' => '#84593C' ),
        array( 'slug' => 'purple', 'name' => 'بنفش',    'hex' => '#8A307F' ),
    );

    $sort_options = array(
        'popularity' => 'پرفروش',
        'date'       => 'جدیدترین ها',
        'price'      => 'ارزانترین ها',
        'rating'     => 'بالاترین امتیاز',
    );
    ?>
    <div class="tno-plp-wrapper" data-max-limit="<?php echo esc_attr( $filters['max_limit'] ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>">

        <!-- ردیف هدر فیلتر و نوار مرتب‌سازی -->
        <div class="tno-plp-top-bar">
            <?php if ( $show_side ) : ?>
                <div class="tno-plp-filter-heading">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#131313" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>فیلتر محصولات</span>
                    <button type="button" class="tno-mobile-filter-trigger">مشاهده فیلترها</button>
                </div>
            <?php endif; ?>

            <div class="tno-plp-toolbar">
                <div class="tno-plp-sort-group">
                    <span class="tno-sort-label">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#131313" stroke-width="1.8" stroke-linecap="round">
                            <line x1="4" y1="6" x2="20" y2="6"></line>
                            <line x1="4" y1="12" x2="14" y2="12"></line>
                            <line x1="4" y1="18" x2="9" y2="18"></line>
                        </svg>
                        ترتیب:
                    </span>
                    <div class="tno-sort-pills">
                        <?php foreach ( $sort_options as $key => $label ) :
                            $active = ( $filters['orderby'] === $key ) ? ' is-active' : '';
                        ?>
                            <button type="button" class="tno-sort-pill<?php echo esc_attr( $active ); ?>" data-orderby="<?php echo esc_attr( $key ); ?>">
                                <?php echo esc_html( $label ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="tno-plp-result-count">
                    <span id="tno-plp-count-num"><?php echo esc_html( tno_fa_num( $found ) ); ?></span> نتیجه
                </div>
            </div>
        </div>

        <!-- چیدمان دو ستونه: سایدبار راست + گرید محصولات چپ -->
        <div class="tno-plp-body <?php echo ! $show_side ? 'is-fullwidth' : ''; ?>">

            <?php if ( $show_side ) : ?>
                <aside class="tno-plp-sidebar" id="tno-plp-sidebar">
                    <div class="tno-sidebar-mobile-head">
                        <strong>فیلتر محصولات</strong>
                        <button type="button" class="tno-close-mobile-sidebar">&times;</button>
                    </div>

                    <!-- ۱. باکس فیلترهای اعمال شده -->
                    <div id="tno-applied-filters-container">
                        <?php echo tno_render_applied_filter_chips( $filters ); ?>
                    </div>

                    <!-- ۲. آکاردئون دسته‌بندی‌ها -->
                    <?php if ( get_theme_mod( 'tno_plp_show_cat_filter', true ) ) : ?>
                        <div class="tno-filter-block is-open">
                            <button type="button" class="tno-filter-block__head">
                                <span>دسته بندی ها</span>
                                <svg class="tno-acc-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 6L6 1L11 6" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tno-filter-block__body">
                                <ul class="tno-checkbox-list">
                                    <?php if ( ! is_wp_error( $all_cats ) && ! empty( $all_cats ) ) :
                                        foreach ( $all_cats as $cat ) :
                                            $checked = in_array( $cat->slug, $filters['cats'], true );
                                        ?>
                                            <li>
                                                <label class="tno-check-label">
                                                    <input type="checkbox" name="tno_cats[]" value="<?php echo esc_attr( $cat->slug ); ?>" <?php checked( $checked ); ?>>
                                                    <span class="tno-custom-check"></span>
                                                    <span class="tno-check-text"><?php echo esc_html( $cat->name ); ?></span>
                                                </label>
                                            </li>
                                        <?php endforeach;
                                    endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ۳. آکاردئون برند -->
                    <?php if ( get_theme_mod( 'tno_plp_show_brand_filter', true ) ) : ?>
                        <div class="tno-filter-block is-open">
                            <button type="button" class="tno-filter-block__head">
                                <span>برند</span>
                                <svg class="tno-acc-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 6L6 1L11 6" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tno-filter-block__body">
                                <ul class="tno-checkbox-list">
                                    <?php
                                    if ( ! is_wp_error( $all_brands ) && ! empty( $all_brands ) ) :
                                        foreach ( $all_brands as $brand ) :
                                            $checked = in_array( $brand->slug, $filters['brands'], true );
                                        ?>
                                            <li>
                                                <label class="tno-check-label">
                                                    <input type="checkbox" name="tno_brands[]" value="<?php echo esc_attr( $brand->slug ); ?>" <?php checked( $checked ); ?>>
                                                    <span class="tno-custom-check"></span>
                                                    <span class="tno-check-text"><?php echo esc_html( $brand->name ); ?></span>
                                                </label>
                                            </li>
                                        <?php endforeach;
                                    else :
                                        // لیست پیش‌فرض مطابق فیگما تا پیش از ثبت ویژگی pa_brand
                                        $sample_brands = array( 'apple' => 'اپل', 'xiaomi' => 'شیائومی', 'honor' => 'هانر', 'pixel' => 'پیکسل', 'nubia' => 'نوبیا', 'gplus' => 'جی پلاس', 'motorola' => 'موتورولا' );
                                        foreach ( $sample_brands as $slug => $b_name ) :
                                            $checked = in_array( $slug, $filters['brands'], true );
                                        ?>
                                            <li>
                                                <label class="tno-check-label">
                                                    <input type="checkbox" name="tno_brands[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $checked ); ?>>
                                                    <span class="tno-custom-check"></span>
                                                    <span class="tno-check-text"><?php echo esc_html( $b_name ); ?></span>
                                                </label>
                                            </li>
                                        <?php endforeach;
                                    endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ۴. آکاردئون رنگ -->
                    <?php if ( get_theme_mod( 'tno_plp_show_color_filter', true ) ) : ?>
                        <div class="tno-filter-block is-open">
                            <button type="button" class="tno-filter-block__head">
                                <span>رنگ</span>
                                <svg class="tno-acc-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 6L6 1L11 6" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tno-filter-block__body">
                                <div class="tno-color-swatches">
                                    <?php
                                    $colors_to_render = array();
                                    if ( ! is_wp_error( $all_colors ) && ! empty( $all_colors ) ) {
                                        foreach ( $all_colors as $c_term ) {
                                            $colors_to_render[] = array(
                                                'slug' => $c_term->slug,
                                                'name' => $c_term->name,
                                                'hex'  => get_term_meta( $c_term->term_id, '_tno_color_hex', true ) ?: '#012561',
                                            );
                                        }
                                    } else {
                                        $colors_to_render = $default_colors;
                                    }

                                    foreach ( $colors_to_render as $clr ) :
                                        $checked = in_array( $clr['slug'], $filters['colors'], true );
                                    ?>
                                        <label class="tno-color-item <?php echo $checked ? 'is-selected' : ''; ?>">
                                            <input type="checkbox" name="tno_colors[]" value="<?php echo esc_attr( $clr['slug'] ); ?>" <?php checked( $checked ); ?>>
                                            <span class="tno-color-circle" style="background-color: <?php echo esc_attr( $clr['hex'] ); ?>;">
                                                <svg class="tno-color-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </span>
                                            <span class="tno-color-name"><?php echo esc_html( $clr['name'] ); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ۵. آکاردئون محدوده قیمت -->
                    <?php if ( get_theme_mod( 'tno_plp_show_price_filter', true ) ) : ?>
                        <div class="tno-filter-block is-open">
                            <button type="button" class="tno-filter-block__head">
                                <span>محدوده قیمت</span>
                                <svg class="tno-acc-chevron" width="12" height="7" viewBox="0 0 12 7" fill="none"><path d="M1 6L6 1L11 6" stroke="#131313" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="tno-filter-block__body">
                                <div class="tno-price-range-slider">
                                    <div class="tno-slider-track-wrap">
                                        <div class="tno-slider-track"></div>
                                        <div class="tno-slider-fill" id="tno-slider-fill"></div>
                                        <input type="range" id="tno-range-min" min="0" max="<?php echo esc_attr( $filters['max_limit'] ); ?>" step="50000" value="<?php echo esc_attr( $filters['min_price'] ); ?>">
                                        <input type="range" id="tno-range-max" min="0" max="<?php echo esc_attr( $filters['max_limit'] ); ?>" step="50000" value="<?php echo esc_attr( $filters['max_price'] ); ?>">
                                    </div>
                                    <div class="tno-slider-Hints">
                                        <span>ارزانترین</span>
                                        <span>گرانترین</span>
                                    </div>
                                </div>

                                <div class="tno-price-inputs">
                                    <div class="tno-price-field">
                                        <label>محدوده قیمت از</label>
                                        <div class="tno-price-input-box">
                                            <input type="text" id="tno-price-min-display" value="<?php echo esc_attr( tno_fa_num( number_format( $filters['min_price'] ) ) ); ?>" readonly>
                                            <small>تومان</small>
                                        </div>
                                    </div>
                                    <div class="tno-price-field">
                                        <label>محدوده قیمت تا</label>
                                        <div class="tno-price-input-box">
                                            <input type="text" id="tno-price-max-display" value="<?php echo esc_attr( tno_fa_num( number_format( $filters['max_price'] ) ) ); ?>" readonly>
                                            <small>تومان</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ۶. سوئیچ‌های وضعیت (موجود، حراجی، ارسال امروز) -->
                    <?php if ( get_theme_mod( 'tno_plp_show_toggles', true ) ) : ?>
                        <div class="tno-filter-block tno-switch-row">
                            <span>فقط محصولات موجود</span>
                            <label class="tno-switch">
                                <input type="checkbox" name="in_stock" value="1" <?php checked( $filters['in_stock'] ); ?>>
                                <span class="tno-switch-slider"></span>
                            </label>
                        </div>

                        <div class="tno-filter-block tno-switch-row">
                            <span>فقط محصولات حراجی</span>
                            <label class="tno-switch">
                                <input type="checkbox" name="on_sale" value="1" <?php checked( $filters['on_sale'] ); ?>>
                                <span class="tno-switch-slider"></span>
                            </label>
                        </div>

                        <div class="tno-filter-block tno-switch-row">
                            <span>ارسال امروز</span>
                            <label class="tno-switch">
                                <input type="checkbox" name="fast_send" value="1" <?php checked( $filters['fast_send'] ); ?>>
                                <span class="tno-switch-slider"></span>
                            </label>
                        </div>
                    <?php endif; ?>

                </aside>
            <?php endif; ?>

            <!-- ستون چپ: گرید محصولات + صفحه‌بندی -->
            <div class="tno-plp-main-col">
                <div id="tno-plp-products-grid" class="tno-plp-grid tno-cols-<?php echo esc_attr( $columns ); ?>">
                    <?php echo tno_render_plp_products_loop( $query ); ?>
                </div>

                <div id="tno-plp-pagination-wrap">
                    <?php echo tno_render_plp_pagination( $query, $filters['paged'] ); ?>
                </div>
            </div>

        </div>
    </div>
    <?php
}

/**
 * ۱۴. کامپوننت ۴: باکس توضیحات سئو دسته‌بندی با دکمه «مشاهده بیشتر»
 */
function tno_render_plp_seo_box( $custom_title = '', $custom_content = '' ) {
    if ( '' === $custom_title && is_product_category() ) {
        $term         = get_queried_object();
        $custom_title = get_term_meta( $term->term_id, '_tno_cat_seo_title', true ) ?: 'قیمت ' . $term->name;
    }
    if ( '' === $custom_title ) {
        $custom_title = get_theme_mod( 'tno_plp_default_seo_title', 'قیمت گوشی موبایل' );
    }

    if ( '' === $custom_content && is_product_category() ) {
        $term           = get_queried_object();
        $custom_content = get_term_meta( $term->term_id, '_tno_cat_seo_content', true ) ?: term_description( $term->term_id, 'product_cat' );
    }
    if ( '' === $custom_content ) {
        $custom_content = get_theme_mod( 'tno_plp_default_seo_text', '' );
    }

    if ( empty( $custom_content ) ) {
        return;
    }
    ?>
    <section class="tno-plp-seo-box">
        <div class="tno-seo-box__header">
            <span class="tno-seo-dot"></span>
            <h2 class="tno-seo-title"><?php echo esc_html( $custom_title ); ?></h2>
        </div>

        <div class="tno-seo-box__content is-collapsed" id="tno-seo-content">
            <div class="tno-seo-box__inner">
                <?php echo wp_kses_post( wpautop( $custom_content ) ); ?>
            </div>
            <div class="tno-seo-fade-overlay"></div>
        </div>

        <div class="tno-seo-box__footer">
            <button type="button" class="tno-seo-toggle-btn" id="tno-seo-toggle-btn">
                <span class="tno-seo-btn-text">مشاهده بیشتر</span>
                <svg width="16" height="14" viewBox="0 0 18 15" fill="none">
                    <path d="M18.0006 7.50042C18.0006 7.69933 17.9216 7.8901 17.7809 8.03075C17.6403 8.1714 17.4495 8.25042 17.2506 8.25042H2.5609L8.03122 13.7198C8.17195 13.8605 8.25101 14.0514 8.25101 14.2504C8.25101 14.4494 8.17195 14.6403 8.03122 14.781C7.89048 14.9218 7.69961 15.0008 7.50059 15.0008C7.30157 15.0008 7.1107 14.9218 6.96996 14.781L0.219965 8.03104C0.079233 7.89031 0 7.69944 0 7.50042C0 7.3014 0.079233 7.11053 0.219965 6.96979L6.96996 0.219792C7.1107 0.0790615 7.30157 0 7.50059 0C7.69961 0 7.89048 0.0790615 8.03122 0.219792C8.17195 0.360523 8.25101 0.551394 8.25101 0.750417C8.25101 0.94944 8.17195 1.14031 8.03122 1.28104L2.5609 6.75042H17.2506C17.6648 6.75042 18.0006 7.08621 18.0006 7.50042Z" fill="white"/>
                </svg>
            </button>
        </div>
    </section>
    <?php
}

/**
 * ۱۵. هندلر ایجکس فیلتر محصولات و مرتب‌سازی
 */
add_action( 'wp_ajax_tno_filter_plp', 'tno_ajax_filter_plp_handler' );
add_action( 'wp_ajax_nopriv_tno_filter_plp', 'tno_ajax_filter_plp_handler' );
function tno_ajax_filter_plp_handler() {
    check_ajax_referer( 'tno_plp_nonce', 'nonce' );

    $_POST['is_ajax'] = 1;
    $filters          = tno_get_active_plp_filters( $_POST );
    $per_page         = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : absint( get_theme_mod( 'tno_plp_per_page', 24 ) );

    $query_args = tno_build_plp_query_args( $filters, $per_page );
    $query      = new WP_Query( $query_args );

    wp_send_json_success( array(
        'products_html'   => tno_render_plp_products_loop( $query ),
        'pagination_html' => tno_render_plp_pagination( $query, $filters['paged'] ),
        'chips_html'      => tno_render_applied_filter_chips( $filters ),
        'count'           => tno_fa_num( (int) $query->found_posts ),
    ) );
}