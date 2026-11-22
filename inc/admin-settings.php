<?php
/** Extensible theme settings. Existing visual settings continue to use theme_mods. */
if ( ! defined( 'ABSPATH' ) ) exit;
function tno_auth_settings() {
    return wp_parse_args( get_option( 'tno_auth_settings', array() ), array(
        'registration' => 1, 'otp_expiry' => 300, 'otp_cooldown' => 94, 'otp_attempts' => 5,
        'sms_template' => '', 'auth_logo' => '', 'auth_art' => '',
    ) );
}
function tno_settings_schema() {
    $font = array( 'rokh' => 'رخ', "'Yekan Bakh'" => 'یکان بخ', 'inherit' => 'پیش‌فرض قالب' );
    $menus = function_exists( 'mytheme_get_menus_choices' ) ? mytheme_get_menus_choices() : array( '' => 'پیش‌فرض' );
    $templates = array( 0 => 'چیدمان پیش‌فرض' );
    foreach ( get_posts( array( 'post_type' => 'elementor_library', 'posts_per_page' => -1, 'post_status' => 'publish' ) ) as $post ) $templates[ $post->ID ] = $post->post_title;
    $sections = array(
        'update' => array( 'title' => 'بروزرسانی', 'icon' => 'dashicons-update', 'description' => 'بررسی و نصب آخرین نسخه قالب از مخزن رسمی ترندینو.', 'groups' => array() ),
        'auth' => array( 'title' => 'ورود و ثبت‌نام', 'icon' => 'dashicons-shield-alt', 'description' => 'تنظیم تجربه ورود، ثبت‌نام و امنیت کد یکبارمصرف.', 'groups' => array(
            'رفتار و امنیت' => array(
                'registration' => array( 'label' => 'ثبت‌نام کاربران جدید', 'type' => 'checkbox', 'default' => 1, 'description' => 'کاربران جدید پس از تایید شماره و تعیین رمز، حساب دریافت می‌کنند.' ),
                'otp_expiry' => array( 'label' => 'اعتبار کد یکبارمصرف', 'type' => 'number', 'default' => 300, 'min' => 60, 'max' => 900, 'description' => 'زمان اعتبار کد به ثانیه (۶۰ تا ۹۰۰).' ),
                'otp_cooldown' => array( 'label' => 'فاصله ارسال مجدد', 'type' => 'number', 'default' => 94, 'min' => 30, 'max' => 600, 'description' => 'حداقل فاصله بین پیامک‌ها به ثانیه.' ),
                'otp_attempts' => array( 'label' => 'حداکثر تلاش برای هر کد', 'type' => 'number', 'default' => 5, 'min' => 1, 'max' => 10, 'description' => 'پس از این تعداد، دریافت کد جدید ضروری است.' ),
            ),
            'ظاهر صفحه ورود' => array(
                'auth_logo' => array( 'label' => 'لوگوی صفحه ورود', 'type' => 'image', 'default' => '', 'description' => 'در صورت خالی بودن، لوگوی پیش‌فرض قالب نمایش داده می‌شود.' ),
                'auth_art' => array( 'label' => 'تصویر کنار فرم', 'type' => 'image', 'default' => '', 'description' => 'تصویر عمودی با نسبت تقریبی ۴۳۲ به ۷۳۲ پیشنهاد می‌شود.' ),
            ),
        ) ),
        'sms' => array( 'title' => 'سامانه پیامکی', 'icon' => 'dashicons-email-alt', 'description' => 'اتصال امن به سرویس پیامک برای ارسال کد تأیید.', 'groups' => array(
            'اتصال کاوه‌نگار' => array(
                'sms_key' => array( 'label' => 'کلید API کاوه‌نگار', 'type' => 'secret', 'description' => 'برای حفظ کلید فعلی این فیلد را خالی بگذارید. کلید در دیتابیس وردپرس ذخیره می‌شود.' ),
                'sms_template' => array( 'label' => 'نام الگوی Verify Lookup', 'type' => 'text', 'default' => '', 'description' => 'نام الگوی تأییدشده در کاوه‌نگار؛ الگو باید شامل token باشد.' ),
            ),
        ) ),
        'header' => array( 'title' => 'هدر و اعلان', 'icon' => 'dashicons-welcome-view-site', 'description' => 'نوار اطلاع‌رسانی و شمارنده شگفت‌انگیز هدر.', 'groups' => array(
            'نوار اعلان' => array(
                'tno_ann_enable' => array( 'label' => 'نمایش نوار اعلان', 'type' => 'checkbox', 'default' => true ),
                'tno_ann_text' => array( 'label' => 'متن اعلان', 'type' => 'html', 'default' => 'شروع تخفیف تابستانه <b>ترندینو</b> از 28 تیرماه' ),
                'tno_ann_link' => array( 'label' => 'لینک اعلان', 'type' => 'url', 'default' => '' ),
                'tno_ann_closable' => array( 'label' => 'امکان بستن اعلان', 'type' => 'checkbox', 'default' => true ),
                'tno_ann_show_bg_shape' => array( 'label' => 'نمایش شکل پس‌زمینه', 'type' => 'checkbox', 'default' => true ),
                'tno_ann_bg_color' => array( 'label' => 'رنگ پس‌زمینه', 'type' => 'color', 'default' => '#012561' ),
                'tno_ann_text_color' => array( 'label' => 'رنگ متن', 'type' => 'color', 'default' => '#ffffff' ),
                'tno_ann_bold_color' => array( 'label' => 'رنگ متن بولد', 'type' => 'color', 'default' => '#ffffff' ),
                'tno_ann_font_family' => array( 'label' => 'فونت اعلان', 'type' => 'select', 'choices' => $font, 'default' => 'rokh' ),
                'tno_ann_font_size' => array( 'label' => 'اندازه فونت (پیکسل)', 'type' => 'number', 'default' => 16, 'min' => 10, 'max' => 32 ),
                'tno_ann_font_weight' => array( 'label' => 'وزن فونت', 'type' => 'select', 'default' => '400', 'choices' => array( '300' => 'سبک', '400' => 'معمولی', '500' => 'متوسط', '700' => 'بولد', '800' => 'فوق ضخیم' ) ),
                'tno_ann_padding_y' => array( 'label' => 'فاصله عمودی (پیکسل)', 'type' => 'number', 'default' => 10, 'min' => 4, 'max' => 40 ),
            ),
            'شگفت‌انگیز هدر' => array(
                'tno_hdeal_enable' => array( 'label' => 'نمایش ویجت شگفت‌انگیز', 'type' => 'checkbox', 'default' => true ),
                'tno_hdeal_title' => array( 'label' => 'عنوان', 'type' => 'text', 'default' => 'شگفت انگیزات' ),
                'tno_hdeal_link' => array( 'label' => 'لینک', 'type' => 'url', 'default' => '#' ),
                'tno_hdeal_show_timer' => array( 'label' => 'نمایش شمارنده', 'type' => 'checkbox', 'default' => true ),
                'tno_hdeal_end_date' => array( 'label' => 'تاریخ پایان', 'type' => 'datetime-local', 'default' => date( 'Y-m-d\TH:i', strtotime( '+18 days +3 hours +52 minutes' ) ) ),
                'tno_hdeal_primary_color' => array( 'label' => 'رنگ اصلی', 'type' => 'color', 'default' => '#E81327' ),
                'tno_hdeal_title_color' => array( 'label' => 'رنگ عنوان', 'type' => 'color', 'default' => '#212121' ),
                'tno_hdeal_num_color' => array( 'label' => 'رنگ اعداد', 'type' => 'color', 'default' => '#ffffff' ),
                'tno_hdeal_font_family' => array( 'label' => 'فونت', 'type' => 'select', 'choices' => $font, 'default' => "'Yekan Bakh'" ),
                'tno_hdeal_title_size' => array( 'label' => 'اندازه عنوان (پیکسل)', 'type' => 'number', 'default' => 15, 'min' => 11, 'max' => 24 ),
            ),
        ) ),
        'footer' => array( 'title' => 'فوتر و اطلاعات تماس', 'icon' => 'dashicons-layout', 'description' => 'محتوا، شبکه‌های اجتماعی و ستون‌های فوتر.', 'groups' => array(
            'معرفی و ارتباط' => array(
                'footer_logo' => array( 'label' => 'لوگوی فوتر', 'type' => 'image', 'default' => '' ),
                'footer_about_text' => array( 'label' => 'متن معرفی', 'type' => 'textarea', 'default' => "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.\nلورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است." ),
                'footer_phone_1' => array( 'label' => 'شماره تماس اول', 'type' => 'text', 'default' => '021-1234567' ),
                'footer_phone_2' => array( 'label' => 'شماره تماس دوم', 'type' => 'text', 'default' => '091212345678' ),
                'footer_email' => array( 'label' => 'ایمیل', 'type' => 'email', 'default' => 'abcdefg@gmail.com' ),
                'footer_address' => array( 'label' => 'آدرس', 'type' => 'text', 'default' => 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، پلاک ۱۵۵، طبقه اول' ),
            ),
            'شبکه‌های اجتماعی' => array(), 'ستون‌ها و نمادها' => array(),
        ) ),
        'shop' => array( 'title' => 'آرشیو فروشگاه', 'icon' => 'dashicons-store', 'description' => 'چیدمان محصولات، فیلترها و محتوای سئو.', 'groups' => array(
            'چیدمان و محتوا' => array(
                'tno_plp_elementor_template_id' => array( 'label' => 'قالب المنتور آرشیو', 'type' => 'select', 'default' => 0, 'choices' => $templates ),
                'tno_plp_columns' => array( 'label' => 'تعداد ستون محصولات', 'type' => 'select', 'default' => 4, 'choices' => array( 3 => '۳ ستون', 4 => '۴ ستون', 5 => '۵ ستون' ) ),
                'tno_plp_per_page' => array( 'label' => 'تعداد محصول در هر صفحه', 'type' => 'number', 'default' => 24, 'min' => 1, 'max' => 200 ),
                'tno_plp_brand_tax' => array( 'label' => 'نامک تاکسونومی برند', 'type' => 'key', 'default' => 'pa_brand' ),
                'tno_plp_color_tax' => array( 'label' => 'نامک تاکسونومی رنگ', 'type' => 'key', 'default' => 'pa_color' ),
                'tno_plp_max_price_limit' => array( 'label' => 'سقف اسلایدر قیمت', 'type' => 'number', 'default' => 100000000, 'min' => 1, 'max' => 999999999999 ),
                'tno_plp_default_seo_title' => array( 'label' => 'عنوان پیش‌فرض سئو', 'type' => 'text', 'default' => 'قیمت گوشی موبایل' ),
                'tno_plp_default_seo_text' => array( 'label' => 'متن پیش‌فرض سئو', 'type' => 'html', 'default' => '', 'description' => 'HTML مجاز وردپرس پذیرفته می‌شود.' ),
            ), 'اجزای صفحه' => array(),
        ) ),
    );
    foreach ( array( 'whatsapp' => 'واتساپ', 'instagram' => 'اینستاگرام', 'youtube' => 'یوتیوب', 'telegram' => 'تلگرام' ) as $key => $label ) $sections['footer']['groups']['شبکه‌های اجتماعی']['footer_social_' . $key] = array( 'label' => $label, 'type' => 'url', 'default' => '#' );
    foreach ( array( 1 => 'دسته بندی محصولات', 2 => 'محصولات پرفروش', 3 => 'دسترسی سریع', 4 => 'نمادهای اعتبار' ) as $i => $title ) {
        $sections['footer']['groups']['ستون‌ها و نمادها'][ 'footer_col' . $i . '_title' ] = array( 'label' => 'عنوان ستون ' . $i, 'type' => 'text', 'default' => $title );
        if ( $i <= 3 ) $sections['footer']['groups']['ستون‌ها و نمادها'][ 'footer_col' . $i . '_menu' ] = array( 'label' => 'فهرست ستون ' . $i, 'type' => 'select', 'default' => '', 'choices' => $menus );
    }
    foreach ( array( 1, 2 ) as $i ) $sections['footer']['groups']['ستون‌ها و نمادها'][ 'footer_badge_' . $i ] = array( 'label' => 'کد نماد ' . $i, 'type' => 'html', 'default' => '' );
    foreach ( array( 'breadcrumb' => 'مسیر راهنما', 'top_cats' => 'دسته‌بندی‌های بالا', 'sidebar' => 'سایدبار فیلترها', 'cat_filter' => 'فیلتر دسته‌بندی', 'brand_filter' => 'فیلتر برند', 'color_filter' => 'فیلتر رنگ', 'price_filter' => 'فیلتر قیمت', 'toggles' => 'سوئیچ‌های موجودی و حراج', 'seo_box' => 'باکس سئو' ) as $key => $label ) $sections['shop']['groups']['اجزای صفحه']['tno_plp_show_' . $key] = array( 'label' => 'نمایش ' . $label, 'type' => 'checkbox', 'default' => true );
    // Third-party additions: sections contain title, description, icon and groups of field definitions.
    return apply_filters( 'tno_settings_schema', $sections );
}
add_action( 'admin_menu', function() { add_menu_page( 'تنظیمات قالب ترندینو', 'تنظیمات قالب', 'manage_options', 'tno-settings', 'tno_settings_page', 'dashicons-admin-generic', 61 ); } );
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( 'toplevel_page_tno-settings' !== $hook ) return;
    wp_enqueue_media(); wp_enqueue_style( 'tno-settings', get_template_directory_uri() . '/assets/css/admin-settings.css', array(), '1.0' );
    wp_enqueue_script( 'tno-settings', get_template_directory_uri() . '/assets/js/admin-settings.js', array(), '1.0', true );
} );
function tno_settings_tab( $schema ) { $tab = isset( $_REQUEST['tab'] ) ? sanitize_key( wp_unslash( $_REQUEST['tab'] ) ) : 'auth'; return isset( $schema[ $tab ] ) ? $tab : 'auth'; }
function tno_settings_value( $tab, $key, $field ) {
    if ( 'secret' === $field['type'] ) return '';
    $default = $field['default'] ?? '';
    if ( in_array( $tab, array( 'auth', 'sms' ), true ) ) { $opts = tno_auth_settings(); return $opts[ $key ] ?? $default; }
    return get_theme_mod( $key, $default );
}
function tno_settings_clean( $value, $field ) {
    $type = $field['type'];
    if ( 'checkbox' === $type ) return ! empty( $value ) ? 1 : 0;
    if ( 'number' === $type ) return max( $field['min'] ?? 0, min( $field['max'] ?? PHP_INT_MAX, (int) $value ) );
    if ( 'select' === $type ) return array_key_exists( (string) $value, $field['choices'] ) ? (string) $value : (string) ( $field['default'] ?? '' );
    if ( 'html' === $type ) return wp_kses_post( $value );
    if ( 'textarea' === $type ) return sanitize_textarea_field( $value );
    if ( 'image' === $type || 'url' === $type ) return '#' === $value ? '#' : esc_url_raw( $value );
    if ( 'email' === $type ) return sanitize_email( $value );
    if ( 'color' === $type ) return sanitize_hex_color( $value ) ?: $field['default'];
    if ( 'key' === $type ) return sanitize_key( $value );
    return sanitize_text_field( $value );
}
add_action( 'admin_post_tno_save_settings', function() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'دسترسی مجاز نیست.' );
    check_admin_referer( 'tno_save_settings' );
    $schema = tno_settings_schema(); $tab = tno_settings_tab( $schema );
    $values = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array();
    $options = tno_auth_settings();
    foreach ( $schema[ $tab ]['groups'] as $fields ) foreach ( $fields as $key => $field ) {
        if ( 'secret' === $field['type'] ) {
            if ( ! empty( $_POST['clear_sms_key'] ) ) delete_option( 'tno_sms_api_key' );
            elseif ( isset( $values[ $key ] ) && is_string( $values[ $key ] ) && '' !== trim( $values[ $key ] ) ) update_option( 'tno_sms_api_key', sanitize_text_field( $values[ $key ] ), false );
            continue;
        }
        // Only the current section is submitted; do not change settings from other sections.
        $value = isset( $values[ $key ] ) && is_scalar( $values[ $key ] ) ? $values[ $key ] : '';
        $value = tno_settings_clean( $value, $field );
        if ( in_array( $tab, array( 'auth', 'sms' ), true ) ) $options[ $key ] = $value; else set_theme_mod( $key, $value );
    }
    if ( in_array( $tab, array( 'auth', 'sms' ), true ) ) update_option( 'tno_auth_settings', $options, false );
    do_action( 'tno_settings_saved', $tab, $values );
    wp_safe_redirect( add_query_arg( array( 'page' => 'tno-settings', 'tab' => $tab, 'saved' => '1' ), admin_url( 'admin.php' ) ) ); exit;
} );
function tno_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $schema = tno_settings_schema(); $tab = tno_settings_tab( $schema ); $section = $schema[ $tab ];
    $configured = ( defined( 'TNO_KAVENEGAR_API_KEY' ) && TNO_KAVENEGAR_API_KEY ) || get_option( 'tno_sms_api_key' );
    $template = defined( 'TNO_KAVENEGAR_TEMPLATE' ) ? TNO_KAVENEGAR_TEMPLATE : ( tno_auth_settings()['sms_template'] ?? '' );
    ?>
    <div class="tno-admin" dir="rtl">
      <header class="tno-admin-hero"><div><div class="tno-eyebrow"><span class="tno-dot"></span> میز مدیریت ترندینو</div><h1>تنظیمات قالب</h1><p>همه چیز برای ساخت تجربه‌ای یکپارچه، در یک جای مشخص.</p></div><a class="tno-site-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">مشاهده سایت <span aria-hidden="true">↗</span></a></header>
      <div class="tno-workspace"><aside class="tno-sidebar"><div class="tno-sidebar-label">بخش‌های تنظیمات</div><nav aria-label="بخش‌های تنظیمات قالب"><?php foreach ( $schema as $id => $item ) : ?><a class="tno-nav-item <?php echo $id === $tab ? 'is-active' : ''; ?>" <?php echo $id === $tab ? 'aria-current="page"' : ''; ?> href="<?php echo esc_url( add_query_arg( array( 'page' => 'tno-settings', 'tab' => $id ), admin_url( 'admin.php' ) ) ); ?>"><span class="dashicons <?php echo esc_attr( $item['icon'] ?? 'dashicons-admin-generic' ); ?>"></span><span><?php echo esc_html( $item['title'] ); ?></span><span class="tno-arrow">‹</span></a><?php endforeach; ?></nav><div class="tno-sidebar-help"><span class="dashicons dashicons-lightbulb"></span><strong>مرکز کنترل قالب</strong><p>تنظیمات هر بخش را مستقل ذخیره کنید. بخش‌های جدید نیز در همین صفحه اضافه می‌شوند.</p></div></aside>
      <main class="tno-main"><div class="tno-heading"><div><span class="tno-breadcrumb">تنظیمات قالب / <?php echo esc_html( $section['title'] ); ?></span><h2><?php echo esc_html( $section['title'] ); ?></h2><p><?php echo esc_html( $section['description'] ?? '' ); ?></p></div><span class="tno-heading-icon dashicons <?php echo esc_attr( $section['icon'] ?? 'dashicons-admin-generic' ); ?>"></span></div>
      <?php if ( isset( $_GET['saved'] ) ) : ?><div class="tno-alert success" role="status"><span class="dashicons dashicons-yes-alt"></span> تنظیمات این بخش با موفقیت ذخیره شد.</div><?php endif; ?>
      <?php if ( in_array( $tab, array( 'auth', 'sms' ), true ) ) : ?><div class="tno-alert <?php echo $configured && $template ? 'ready' : 'warning'; ?>"><span class="dashicons <?php echo $configured && $template ? 'dashicons-yes-alt' : 'dashicons-info-outline'; ?>"></span><div><strong><?php echo $configured && $template ? 'تنظیمات اتصال پیامک وارد شده است' : 'اتصال پیامک نیاز به تنظیم دارد'; ?></strong><p><?php echo $configured && $template ? 'برای بررسی عملکرد، ارسال پیامک واقعی را در محیط آزمایشی امتحان کنید.' : 'برای ثبت‌نام و بازیابی رمز، کلید API و الگوی تأییدشده کاوه‌نگار را در بخش سامانه پیامکی وارد کنید.'; ?></p></div><?php if ( 'auth' === $tab ) : ?><a href="<?php echo esc_url( admin_url( 'admin.php?page=tno-settings&tab=sms' ) ); ?>">تنظیم اتصال ←</a><?php endif; ?></div><?php endif; ?>
      <?php if ( 'update' === $tab ) : ?>
      <?php tno_render_update_settings(); ?>
      <?php else : ?>
      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="tno_save_settings"><input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"><?php wp_nonce_field( 'tno_save_settings' ); ?>
      <?php foreach ( $section['groups'] as $group => $fields ) : ?><section class="tno-card"><div class="tno-card-head"><div><span class="tno-card-indicator"></span><h3><?php echo esc_html( $group ); ?></h3></div><span class="tno-card-caption">تنظیمات بخش</span></div><div class="tno-fields"><?php foreach ( $fields as $key => $field ) : tno_settings_field( $tab, $key, $field ); endforeach; ?></div></section><?php endforeach; ?>
      <div class="tno-savebar"><div><strong>تغییرات این بخش</strong><span>پس از ویرایش، تنظیمات را ذخیره کنید.</span></div><button class="tno-save" type="submit"><span class="dashicons dashicons-saved"></span> ذخیره تغییرات</button></div></form>
      <?php endif; ?>
      </main></div><footer class="tno-admin-footer">ترندینو <span>•</span> تنظیمات قالب</footer></div>
    <?php
}
function tno_settings_field( $tab, $key, $field ) {
    $type = $field['type']; $value = tno_settings_value( $tab, $key, $field ); $id = 'tno-field-' . $key;
    ?>
    <div class="tno-field <?php echo 'checkbox' === $type ? 'tno-field-toggle' : ''; ?> <?php echo in_array( $type, array( 'textarea', 'html', 'image', 'secret' ), true ) ? 'tno-field-wide' : ''; ?>">
      <div class="tno-field-copy"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label><?php if ( ! empty( $field['description'] ) ) : ?><p><?php echo esc_html( $field['description'] ); ?></p><?php endif; ?></div>
      <?php if ( 'checkbox' === $type ) : ?><label class="tno-switch"><input id="<?php echo esc_attr( $id ); ?>" type="checkbox" name="fields[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $value ); ?>><span></span><span class="screen-reader-text"><?php echo esc_html( $field['label'] ); ?></span></label>
      <?php elseif ( 'select' === $type ) : ?><select id="<?php echo esc_attr( $id ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]"><?php foreach ( $field['choices'] as $choice => $label ) : ?><option value="<?php echo esc_attr( $choice ); ?>" <?php selected( (string) $value, (string) $choice ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select>
      <?php elseif ( in_array( $type, array( 'textarea', 'html' ), true ) ) : ?><textarea id="<?php echo esc_attr( $id ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]" rows="4"><?php echo esc_textarea( $value ); ?></textarea>
      <?php elseif ( 'image' === $type ) : ?><div class="tno-media"><input id="<?php echo esc_attr( $id ); ?>" type="url" name="fields[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" placeholder="https://"><button class="tno-media-select" type="button">انتخاب تصویر</button><button class="tno-media-clear" type="button">حذف</button><?php if ( $value ) : ?><img src="<?php echo esc_url( $value ); ?>" alt="پیش‌نمایش تصویر"><?php else : ?><img alt="پیش‌نمایش تصویر" hidden><?php endif; ?></div>
      <?php elseif ( 'secret' === $type ) : ?><input id="<?php echo esc_attr( $id ); ?>" type="password" name="fields[<?php echo esc_attr( $key ); ?>]" value="" autocomplete="new-password" placeholder="<?php echo get_option( 'tno_sms_api_key' ) ? 'کلید ذخیره شده است؛ برای تغییر، کلید جدید وارد کنید' : 'کلید API را وارد کنید'; ?>" dir="ltr"><div class="tno-secret-meta"><label><input type="checkbox" name="clear_sms_key" value="1"> حذف کلید ذخیره‌شده</label><?php if ( defined( 'TNO_KAVENEGAR_API_KEY' ) ) : ?><span>کلید تعریف‌شده در wp-config.php اولویت دارد.</span><?php endif; ?></div>
      <?php else : ?><input id="<?php echo esc_attr( $id ); ?>" type="<?php echo esc_attr( in_array( $type, array( 'number', 'email', 'color', 'url', 'datetime-local' ), true ) ? $type : 'text' ); ?>" name="fields[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php if ( isset( $field['min'] ) ) echo 'min="' . esc_attr( $field['min'] ) . '"'; ?> <?php if ( isset( $field['max'] ) ) echo 'max="' . esc_attr( $field['max'] ) . '"'; ?>><?php if ( 'sms_template' === $key && defined( 'TNO_KAVENEGAR_TEMPLATE' ) ) : ?><p class="tno-override">الگوی تعریف‌شده در wp-config.php اولویت دارد.</p><?php endif; ?>
      <?php endif; ?>
    </div><?php
}
