<?php
defined('ABSPATH') || exit;

function tf_footer_default($key) {
    $defaults = array(
        'brand_name'        => 'ترندینو',
        'description_1'     => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
        'description_2'     => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
        'newsletter_title'  => 'آدرس ایمیل خود را وارد کنید',
        'newsletter_button' => 'عضویت',
        'phone_1'           => '091212345678',
        'phone_2'           => '326-1234567',
        'email'             => 'abcdefg@gmail.com',
        'address'           => 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، پلاک ۵۵، طبقه اول',
        'bg_color'          => '#06265f',
        'accent_color'      => '#ff4354',
    );

    return isset($defaults[$key]) ? $defaults[$key] : '';
}

function tf_footer_mod($key) {
    return get_theme_mod('tf_footer_' . $key, tf_footer_default($key));
}

add_action('after_setup_theme', function () {
    register_nav_menus(array(
        'tf_footer_quick'      => 'فوتر: دسترسی سریع',
        'tf_footer_products'   => 'فوتر: محصولات پرفروش',
        'tf_footer_categories' => 'فوتر: دسته‌بندی محصولات',
    ));
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'tf-custom-footer',
        get_theme_file_uri('/assets/css/custom-footer.css'),
        array(),
        '1.0.0'
    );
});

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section( 'footer_settings_section', array(
        'title'    => 'تنظیمات فوتر (پاورقی)',
        'priority' => 120,
    ) );

    // --- لوگو و توضیحات وسط ---
    $wp_customize->add_setting( 'footer_logo' );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_logo', array(
        'label'    => 'لوگوی فوتر',
        'section'  => 'footer_settings_section',
        'settings' => 'footer_logo',
    ) ) );

    $wp_customize->add_setting( 'footer_about_text', array(
        'default' => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.\nلورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است."
    ) );
    $wp_customize->add_control( 'footer_about_text', array(
        'label'   => 'متن توضیحات زیر لوگو',
        'section' => 'footer_settings_section',
        'type'    => 'textarea',
    ) );

    // --- شبکه های اجتماعی ---
    $socials = array(
        'whatsapp'  => 'لینک واتساپ',
        'instagram' => 'لینک اینستاگرام',
        'youtube'   => 'لینک یوتیوب',
        'telegram'  => 'لینک تلگرام',
    );
    foreach ( $socials as $key => $label ) {
        $wp_customize->add_setting( 'footer_social_' . $key, array( 'default' => '#' ) );
        $wp_customize->add_control( 'footer_social_' . $key, array(
            'label'   => $label,
            'section' => 'footer_settings_section',
            'type'    => 'url',
        ) );
    }

        // --- عنوان ستون‌ها + انتخاب فهرست زیر هر عنوان ---
    $footer_columns = array(
        1 => array( 'title' => 'دسته بندی محصولات', 'label' => 'ستون اول' ),
        2 => array( 'title' => 'محصولات پرفروش',    'label' => 'ستون دوم' ),
        3 => array( 'title' => 'دسترسی سریع',       'label' => 'ستون سوم' ),
    );

    foreach ( $footer_columns as $i => $col ) {

        // عنوان ستون
        $wp_customize->add_setting( "footer_col{$i}_title", array(
            'default'           => $col['title'],
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "footer_col{$i}_title", array(
            'label'   => 'عنوان ' . $col['label'],
            'section' => 'footer_settings_section',
            'type'    => 'text',
        ) );

        // انتخاب فهرست (درست زیر عنوان)
        $wp_customize->add_setting( "footer_col{$i}_menu", array(
            'default'           => '',
            'sanitize_callback' => 'absint',
        ) );
        $wp_customize->add_control( "footer_col{$i}_menu", array(
            'label'       => 'فهرست نمایشی ' . $col['label'],
            'description' => 'فهرست مورد نظر را از بخش نمایش > فهرست‌ها بسازید و اینجا انتخاب کنید.',
            'section'     => 'footer_settings_section',
            'type'        => 'select',
            'choices'     => mytheme_get_menus_choices(),
        ) );
    }

    // --- کدهای نماد اعتماد و اسنپ پی ---
    $wp_customize->add_setting( 'footer_badge_1', array( 'default' => '<img src="https://placehold.co/75x75/008cff/fff?text=Snapp!+Pay" alt="Snapp Pay">' ) );
    $wp_customize->add_control( 'footer_badge_1', array(
        'label'   => 'کد HTML نماد اول (مثلا اسنپ پی)',
        'section' => 'footer_settings_section',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'footer_badge_2', array( 'default' => '<img src="https://placehold.co/75x75/ffffff/002157?text=eNamad" alt="Enamad">' ) );
    $wp_customize->add_control( 'footer_badge_2', array(
        'label'   => 'کد HTML نماد دوم (اینماد)',
        'section' => 'footer_settings_section',
        'type'    => 'textarea',
    ) );

    // --- اطلاعات تماس پایین فوتر ---
    $wp_customize->add_setting( 'footer_phone_1', array( 'default' => '021-1234567' ) );
    $wp_customize->add_control( 'footer_phone_1', array( 'label' => 'شماره تماس ۱', 'section' => 'footer_settings_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'footer_phone_2', array( 'default' => '091212345678' ) );
    $wp_customize->add_control( 'footer_phone_2', array( 'label' => 'شماره تماس ۲', 'section' => 'footer_settings_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'footer_address', array( 'default' => 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، پلاک ۱۵۵، طبقه اول' ) );
    $wp_customize->add_control( 'footer_address', array( 'label' => 'آدرس مجموعه', 'section' => 'footer_settings_section', 'type' => 'text' ) );

    $wp_customize->add_setting( 'footer_email', array( 'default' => 'abcdefg@gmail.com' ) );
    $wp_customize->add_control( 'footer_email', array( 'label' => 'ایمیل مجموعه', 'section' => 'footer_settings_section', 'type' => 'email' ) );
});

/* لینک‌های نمونه تا قبل از ساخت فهرست‌های اختصاصی */
function tf_footer_menu($location) {
    $examples = array(
        'tf_footer_quick' => array(
            array('محصولات', '/shop/'),
            array('درباره ما', '/about-us/'),
            array('تماس با ما', '/contact-us/'),
            array('بلاگ', '/blog/'),
            array('شگفت‌انگیزها', '/shop/'),
        ),
        'tf_footer_products' => array(
            array('گوشی آیفون', '/shop/'),
            array('گوشی سامسونگ', '/shop/'),
            array('محافظ گوشی', '/shop/'),
            array('گوشی شیائومی', '/shop/'),
            array('کنسول بازی', '/shop/'),
        ),
        'tf_footer_categories' => array(
            array('لپتاپ', '/shop/'),
            array('گوشی', '/shop/'),
            array('ساعت هوشمند', '/shop/'),
            array('هدفون', '/shop/'),
            array('کنسول بازی', '/shop/'),
        ),
    );

    if (has_nav_menu($location)) {
        wp_nav_menu(array(
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => 'tf-footer__links',
            'depth'          => 1,
        ));
        return;
    }

    echo '<ul class="tf-footer__links">';
    foreach ($examples[$location] as $item) {
        printf(
            '<li><a href="%s">%s</a></li>',
            esc_url(home_url($item[1])),
            esc_html($item[0])
        );
    }
    echo '</ul>';
}

function tf_footer_newsletter_submit() {
    $status = 'error';

    if (
        isset($_POST['tf_footer_nonce']) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['tf_footer_nonce'])),
            'tf_footer_newsletter'
        ) &&
        empty($_POST['website'])
    ) {
        $email = isset($_POST['subscriber_email'])
            ? sanitize_email(wp_unslash($_POST['subscriber_email']))
            : '';

        if (is_email($email)) {
            $sent = wp_mail(
                get_option('admin_email'),
                'عضویت جدید در خبرنامه ' . get_bloginfo('name'),
                "ایمیل ثبت‌شده: " . $email
            );
            $status = $sent ? 'success' : 'error';
        }
    }

    $referer = wp_get_referer();
    $target  = $referer ? $referer : home_url('/');
    $target  = remove_query_arg('tf_footer_status', $target);
    $target  = add_query_arg('tf_footer_status', $status, $target);

    wp_safe_redirect($target . '#theme-footer');
    exit;
}

add_action('admin_post_nopriv_tf_footer_newsletter', 'tf_footer_newsletter_submit');
add_action('admin_post_tf_footer_newsletter', 'tf_footer_newsletter_submit');

/**
 * تنظیمات سفارشی‌سازی: ۱. نوار اعلان بالای سایت  ۲. تایمر شگفت‌انگیز هدر
 */
add_action( 'customize_register', 'tno_header_customizer_settings' );
function tno_header_customizer_settings( $wp_customize ) {

    // =========================================================
    // بخش ۱: نوار اعلان بالای هدر (Announcement Bar)
    // =========================================================
    $wp_customize->add_section( 'tno_announcement_section', array(
        'title'    => 'تنظیمات نوار اعلان بالا (هدر)',
        'priority' => 25,
    ) );

    // فعال/غیرفعال کردن نوار اعلان
    $wp_customize->add_setting( 'tno_ann_enable', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'tno_ann_enable', array(
        'label'   => 'فعال‌سازی نوار اعلان بالا',
        'section' => 'tno_announcement_section',
        'type'    => 'checkbox',
    ) );

    // متن اعلان (با پشتیبانی از تگ b و strong)
    $wp_customize->add_setting( 'tno_ann_text', array(
        'default'           => 'شروع تخفیف تابستانه <b>ترندینو</b> از 28 تیرماه',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'tno_ann_text', array(
        'label'       => 'متن نوار اعلان',
        'description' => 'برای متمایز کردن کلمات از تگ &lt;b&gt;متن&lt;/b&gt; استفاده کنید.',
        'section'     => 'tno_announcement_section',
        'type'        => 'textarea',
    ) );

    // لینک نوار اعلان
    $wp_customize->add_setting( 'tno_ann_link', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'tno_ann_link', array(
        'label'   => 'لینک نوار اعلان (اختیاری)',
        'section' => 'tno_announcement_section',
        'type'    => 'url',
    ) );

    // نمایش دکمه بستن و آیکون‌های پس‌زمینه
    $wp_customize->add_setting( 'tno_ann_closable', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'tno_ann_closable', array(
        'label'   => 'نمایش دکمه بستن (×)',
        'section' => 'tno_announcement_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'tno_ann_show_bg_shape', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'tno_ann_show_bg_shape', array(
        'label'   => 'نمایش آیکون‌های تزئینی سبد خرید در سمت راست',
        'section' => 'tno_announcement_section',
        'type'    => 'checkbox',
    ) );

    // رنگ پس‌زمینه
    $wp_customize->add_setting( 'tno_ann_bg_color', array(
        'default'           => '#012561',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_ann_bg_color', array(
        'label'   => 'رنگ پس‌زمینه نوار',
        'section' => 'tno_announcement_section',
    ) ) );

    // رنگ متن عادی
    $wp_customize->add_setting( 'tno_ann_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_ann_text_color', array(
        'label'   => 'رنگ متن اصلی',
        'section' => 'tno_announcement_section',
    ) ) );

    // رنگ متن بولد (داخل تگ b)
    $wp_customize->add_setting( 'tno_ann_bold_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_ann_bold_color', array(
        'label'   => 'رنگ متن بولد (داخل تگ b)',
        'section' => 'tno_announcement_section',
    ) ) );

    // انتخاب فونت
    $wp_customize->add_setting( 'tno_ann_font_family', array(
        'default'           => 'rokh',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_ann_font_family', array(
        'label'   => 'فونت متن نوار اعلان',
        'section' => 'tno_announcement_section',
        'type'    => 'select',
        'choices' => array(
            'rokh'         => 'فونت رخ (Rokh)',
            "'Yekan Bakh'" => 'فونت یکان بخ (Yekan Bakh)',
            'inherit'      => 'فونت پیش‌فرض قالب',
        ),
    ) );

    // سایز فونت و وزن فونت
    $wp_customize->add_setting( 'tno_ann_font_size', array(
        'default'           => 16,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_ann_font_size', array(
        'label'       => 'اندازه فونت (px)',
        'section'     => 'tno_announcement_section',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 10, 'max' => 32, 'step' => 1 ),
    ) );

    $wp_customize->add_setting( 'tno_ann_font_weight', array(
        'default'           => '400',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_ann_font_weight', array(
        'label'   => 'وزن فونت متن اصلی',
        'section' => 'tno_announcement_section',
        'type'    => 'select',
        'choices' => array(
            '300' => 'سبک (300)',
            '400' => 'معمولی (400)',
            '500' => 'متوسط (500)',
            '700' => 'ضخیم - بولد (700)',
            '800' => 'فوق ضخیم (800)',
        ),
    ) );

    // ارتفاع (پدینگ عمودی)
    $wp_customize->add_setting( 'tno_ann_padding_y', array(
        'default'           => 10,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_ann_padding_y', array(
        'label'       => 'فاصله داخلی بالا و پایین (px)',
        'section'     => 'tno_announcement_section',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 4, 'max' => 40, 'step' => 1 ),
    ) );


    // =========================================================
    // بخش ۲: ویجت تایمر شگفت‌انگیز هدر (سمت چپ منو)
    // =========================================================
    $wp_customize->add_section( 'tno_header_deal_section', array(
        'title'    => 'تنظیمات تایمر شگفت‌انگیز هدر',
        'priority' => 26,
    ) );

    // فعال/غیرفعال
    $wp_customize->add_setting( 'tno_hdeal_enable', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'tno_hdeal_enable', array(
        'label'   => 'فعال‌سازی ویجت شگفت‌انگیز در سمت چپ منو',
        'section' => 'tno_header_deal_section',
        'type'    => 'checkbox',
    ) );

    // عنوان و لینک
    $wp_customize->add_setting( 'tno_hdeal_title', array(
        'default'           => 'شگفت انگیزات',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_hdeal_title', array(
        'label'   => 'عنوان لینک شگفت‌انگیز',
        'section' => 'tno_header_deal_section',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tno_hdeal_link', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'tno_hdeal_link', array(
        'label'   => 'لینک صفحه شگفت‌انگیزها',
        'section' => 'tno_header_deal_section',
        'type'    => 'url',
    ) );

    // فعال بودن تایمر و تاریخ پایان
    $wp_customize->add_setting( 'tno_hdeal_show_timer', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'tno_hdeal_show_timer', array(
        'label'   => 'نمایش شمارنده معکوس (تایمر)',
        'section' => 'tno_header_deal_section',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'tno_hdeal_end_date', array(
        'default'           => date( 'Y-m-d\TH:i', strtotime( '+18 days +3 hours +52 minutes' ) ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_hdeal_end_date', array(
        'label'       => 'تاریخ و ساعت پایان جشنواره',
        'description' => 'فرمت: YYYY-MM-DDTHH:MM',
        'section'     => 'tno_header_deal_section',
        'type'        => 'datetime-local',
    ) );

    // رنگ‌های تایمر شگفت‌انگیز
    $wp_customize->add_setting( 'tno_hdeal_primary_color', array(
        'default'           => '#E81327',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_hdeal_primary_color', array(
        'label'   => 'رنگ اصلی تایمر (باکس‌ها، برچسب‌ها و آیکون %)',
        'section' => 'tno_header_deal_section',
    ) ) );

    $wp_customize->add_setting( 'tno_hdeal_title_color', array(
        'default'           => '#212121',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_hdeal_title_color', array(
        'label'   => 'رنگ متن عنوان (شگفت انگیزات)',
        'section' => 'tno_header_deal_section',
    ) ) );

    $wp_customize->add_setting( 'tno_hdeal_num_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tno_hdeal_num_color', array(
        'label'   => 'رنگ اعداد داخل باکس تایمر',
        'section' => 'tno_header_deal_section',
    ) ) );

    // فونت و اندازه متن عنوان
    $wp_customize->add_setting( 'tno_hdeal_font_family', array(
        'default'           => "'Yekan Bakh'",
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tno_hdeal_font_family', array(
        'label'   => 'فونت ویجت شگفت‌انگیز',
        'section' => 'tno_header_deal_section',
        'type'    => 'select',
        'choices' => array(
            "'Yekan Bakh'" => 'فونت یکان بخ (Yekan Bakh)',
            'rokh'         => 'فونت رخ (Rokh)',
            'inherit'      => 'فونت پیش‌فرض قالب',
        ),
    ) );

    $wp_customize->add_setting( 'tno_hdeal_title_size', array(
        'default'           => 15,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tno_hdeal_title_size', array(
        'label'       => 'اندازه فونت عنوان (px)',
        'section'     => 'tno_header_deal_section',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 11, 'max' => 24, 'step' => 1 ),
    ) );
}

/**
 * چاپ متغیرهای CSS سفارشی‌سازی در تگ <head>
 */
add_action( 'wp_head', 'tno_header_customizer_dynamic_css', 99 );
function tno_header_customizer_dynamic_css() {
    // نوار اعلان
    $ann_bg      = get_theme_mod( 'tno_ann_bg_color', '#012561' );
    $ann_text    = get_theme_mod( 'tno_ann_text_color', '#ffffff' );
    $ann_bold    = get_theme_mod( 'tno_ann_bold_color', '#ffffff' );
    $ann_font    = get_theme_mod( 'tno_ann_font_family', 'rokh' );
    $ann_size    = absint( get_theme_mod( 'tno_ann_font_size', 16 ) );
    $ann_weight  = get_theme_mod( 'tno_ann_font_weight', '400' );
    $ann_pad_y   = absint( get_theme_mod( 'tno_ann_padding_y', 10 ) );

    // تایمر شگفت‌انگیز هدر
    $hd_primary  = get_theme_mod( 'tno_hdeal_primary_color', '#E81327' );
    $hd_title    = get_theme_mod( 'tno_hdeal_title_color', '#212121' );
    $hd_num      = get_theme_mod( 'tno_hdeal_num_color', '#ffffff' );
    $hd_font     = get_theme_mod( 'tno_hdeal_font_family', "'Yekan Bakh'" );
    $hd_size     = absint( get_theme_mod( 'tno_hdeal_title_size', 15 ) );
    ?>
    <style id="tno-header-customizer-css">
        :root {
            --tno-ann-bg: <?php echo esc_attr( $ann_bg ); ?>;
            --tno-ann-text: <?php echo esc_attr( $ann_text ); ?>;
            --tno-ann-bold: <?php echo esc_attr( $ann_bold ); ?>;
            --tno-ann-font: <?php echo $ann_font; ?>;
            --tno-ann-size: <?php echo $ann_size; ?>px;
            --tno-ann-weight: <?php echo esc_attr( $ann_weight ); ?>;
            --tno-ann-pad-y: <?php echo $ann_pad_y; ?>px;

            --tno-hd-primary: <?php echo esc_attr( $hd_primary ); ?>;
            --tno-hd-title: <?php echo esc_attr( $hd_title ); ?>;
            --tno-hd-num: <?php echo esc_attr( $hd_num ); ?>;
            --tno-hd-font: <?php echo $hd_font; ?>;
            --tno-hd-size: <?php echo $hd_size; ?>px;
        }
    </style>
    <?php
}

/**
 * اسکریپت فرانت‌اند برای شمارش معکوس تایمر هدر (اعداد فارسی) و دکمه بستن نوار اعلان
 */
add_action( 'wp_footer', 'tno_header_deal_and_ann_scripts', 98 );
function tno_header_deal_and_ann_scripts() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ۱. بستن نوار اعلان بالا
        const closeBtn = document.querySelector('.c-announcement__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const bar = this.closest('.c-announcement');
                if (bar) {
                    bar.style.display = 'none';
                }
            });
        }

        // ۲. تایمر شگفت‌انگیز هدر با تبدیل به اعداد فارسی
        const timerEl = document.querySelector('.tno-header-deal__timer');
        if (!timerEl) return;

        const endTimeStr = timerEl.getAttribute('data-end');
        if (!endTimeStr) return;

        const targetDate = new Date(endTimeStr).getTime();
        if (isNaN(targetDate)) return;

        const toPersianDigits = function(num) {
            const persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            return String(num).replace(/\d/g, function(d) { return persian[d]; });
        };

        const elDays = timerEl.querySelector('[data-unit="days"]');
        const elHours = timerEl.querySelector('[data-unit="hours"]');
        const elMinutes = timerEl.querySelector('[data-unit="minutes"]');
        const elSeconds = timerEl.querySelector('[data-unit="seconds"]');

        const updateHeaderTimer = function() {
            const now = new Date().getTime();
            const diff = targetDate - now;

            if (diff <= 0) {
                if (elDays) elDays.textContent = '۰';
                if (elHours) elHours.textContent = '۰';
                if (elMinutes) elMinutes.textContent = '۰';
                if (elSeconds) elSeconds.textContent = '۰';
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            if (elDays) elDays.textContent = toPersianDigits(d);
            if (elHours) elHours.textContent = toPersianDigits(h);
            if (elMinutes) elMinutes.textContent = toPersianDigits(m < 10 ? '0' + m : m);
            if (elSeconds) elSeconds.textContent = toPersianDigits(s < 10 ? '0' + s : s);
        };

        updateHeaderTimer();
        setInterval(updateHeaderTimer, 1000);
    });
    </script>
    <?php
}