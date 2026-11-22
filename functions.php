<?php
function add_theme_scripts() {
	wp_enqueue_style( 'style', get_stylesheet_uri() );

	// wp_enqueue_style( 'slider', get_template_directory_uri() . '/css/slider.css', array(), '1.1', 'all' );

    wp_register_script( 
        'tno-widgets-script', 
        get_template_directory_uri() . '/assets/js/tno-widgets.js', 
        [ 'jquery', 'elementor-frontend' ], // وابستگی‌ها
        '1.1', 
        true 
    );

	// wp_enqueue_script( 'script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), 1.1, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    if ( is_product() ) {
        wp_enqueue_style(
            'tno-pdp-style',
            get_template_directory_uri() . '/assets/css/pdp.css',
            array(),
            filemtime( get_template_directory() . '/assets/css/pdp.css' )
        );

        wp_enqueue_script(
            'tno-pdp-script',
            get_template_directory_uri() . '/assets/js/pdp.js',
            array(),
            filemtime( get_template_directory() . '/assets/js/pdp.js' ),
            true
        );

        wp_enqueue_script(
            'tno-pdp-gallery',
            get_template_directory_uri() . '/assets/js/pdp-gallery.js',
            array( 'tno-pdp-script' ),
            filemtime( get_template_directory() . '/assets/js/pdp-gallery.js' ),
            true
        );

        wp_localize_script( 'tno-pdp-script', 'tnoPdpData', array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'tno_pdp_nonce' ),
            'productId' => get_the_ID(),
            'isLogged'  => is_user_logged_in(),
            'cartUrl'   => WC_AJAX::get_endpoint( 'add_to_cart' ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );
function register_trendino_menu() {
  	register_nav_menu('header-menu',__( 'Header Menu' ));
}
add_action( 'init', 'register_trendino_menu' );

// ۱. این خط را جایگزین کلاس Header_Menu_Walker قبلی در functions.php کنید:
require_once get_template_directory() . '/inc/megamenu.php';
require_once get_template_directory() . '/inc/header-ajax.php';

// ۲. کد جاوااسکریپت تعویض تب‌ها با هاور (اضافه کردن به انتهای functions.php):
add_action( 'wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const megaMenus = document.querySelectorAll('.tno-megamenu-dropdown');
        
        megaMenus.forEach(function(menu) {
            const tabs = menu.querySelectorAll('.tno-mm-tab-item');
            const panels = menu.querySelectorAll('.tno-mm-panel');

            tabs.forEach(function(tab) {
                tab.addEventListener('mouseenter', function() {
                    const targetId = this.getAttribute('data-tab-target');
                    
                    tabs.forEach(function(t) { t.classList.remove('active'); });
                    panels.forEach(function(p) { p.classList.remove('active'); });

                    this.classList.add('active');
                    const targetPanel = menu.querySelector('#' + targetId);
                    if (targetPanel) {
                        targetPanel.classList.add('active');
                    }
                });
            });
        });
    });
    </script>
    <?php
}, 99 );

function tno_elementor_init() {
	// فراخوانی فایل تنظیمات المنتور
	require_once get_template_directory() . '/inc/elementor/elementor-init.php';
}
// استفاده از اکشن init برای اطمینان از لود شدن کلاسهای المنتور
add_action( 'init', 'tno_elementor_init' );

if (!function_exists("disable_emojis")) {
    function disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        }
    add_action( 'init', 'disable_emojis' );
}

include_once 'inc/theme-options.php';

function trendino_widgets_init() {
    // ثبت ۴ ستون برای فوتر
    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar( array(
            'name'          => esc_html__( 'ستون فوتر ', 'trendino' ) . $i,
            'id'            => 'footer-col-' . $i,
            'description'   => esc_html__( 'ابزارک‌های این بخش در ستون ' . $i . ' فوتر نمایش داده می‌شوند.', 'trendino' ),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ) );
    }
}
add_action( 'widgets_init', 'trendino_widgets_init' );

function trendino_register_footer_menu() {
    register_nav_menu( 'footer-menu', __( 'منوی ستون اول فوتر', 'trendino' ) );
}
add_action( 'init', 'trendino_register_footer_menu' );

// لیست فهرست‌های ساخته‌شده در وردپرس برای فیلد انتخاب
function mytheme_get_menus_choices() {
    $choices = array( '' => '— پیش‌فرض (جایگاه منو / لینک‌های نمونه) —' );
    $menus   = wp_get_nav_menus();
    if ( ! empty( $menus ) ) {
        foreach ( $menus as $menu ) {
            $choices[ $menu->term_id ] = $menu->name;
        }
    }
    return $choices;
}

// نمایش منوی ستون فوتر
// اولویت: ۱) فهرست انتخاب‌شده در سفارشی‌سازی  ۲) جایگاه منو  ۳) لینک‌های نمونه
function mytheme_footer_menu( $col, $location, $fallback_html ) {
    $menu_id = absint( get_theme_mod( 'footer_col' . $col . '_menu', 0 ) );

    if ( $menu_id && wp_get_nav_menu_object( $menu_id ) ) {
        wp_nav_menu( array(
            'menu'        => $menu_id,
            'container'   => false,
            'menu_class'  => 'footer-links',
            'depth'       => 1,
            'fallback_cb' => false,
        ) );
    } elseif ( has_nav_menu( $location ) ) {
        wp_nav_menu( array(
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => 'footer-links',
            'depth'          => 1,
        ) );
    } else {
        echo $fallback_html;
    }
}
if ( ! function_exists( 'mytheme_style_footer_title' ) ) {
    /**
     * Fallback for missing footer title styling function.
     * Sanitizes output to prevent XSS vulnerabilities.
     */
    function mytheme_style_footer_title( $title ) {
        return esc_html( $title );
    }
}
// Resolve conflicts between WordPress output buffering and server zlib compression
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
    while ( @ob_end_flush() );
} );

require_once get_template_directory() . '/inc/plp-functions.php';
require_once get_template_directory() . '/inc/login.php';
require_once get_template_directory() . '/inc/admin-settings.php';
require_once get_template_directory() . '/inc/theme-updater.php';
require_once get_template_directory() . '/inc/pdp-functions.php';
require_once get_template_directory() . '/inc/pdp-modals.php';

/**
 * 1. Declare WooCommerce Support (Prevents fallback to page.php / default WC wrapper)
 */
add_action( 'after_setup_theme', function() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}, 10 );

/**
 * 2. Load PDP Backend Module
 */
$tno_pdp_inc = get_stylesheet_directory() . '/inc/pdp-functions.php';
if ( file_exists( $tno_pdp_inc ) ) {
    require_once $tno_pdp_inc;
}

/**
 * 3. Force Load Custom woocommerce/single-product.php (Bypasses WC_Template_Loader & Elementor)
 */
add_filter( 'template_include', function( $template ) {
    if ( is_singular( 'product' ) ) {
        $custom_pdp = get_stylesheet_directory() . '/woocommerce/single-product.php';
        if ( file_exists( $custom_pdp ) ) {
            return $custom_pdp;
        }
    }
    return $template;
}, 99999 );

/**
 * 4. Intercept wc_get_template_part if called directly
 */
add_filter( 'wc_get_template_part', function( $template, $slug, $name ) {
    if ( $slug === 'content' && $name === 'single-product' ) {
        $custom_pdp = get_stylesheet_directory() . '/woocommerce/single-product.php';
        if ( file_exists( $custom_pdp ) ) {
            return $custom_pdp;
        }
    }
    return $template;
}, 99999, 3 );

/**
 * 5. Enqueue PDP Assets & Dequeue Conflicting Default WC Styles on Product Page
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( is_singular( 'product' ) ) {
        $css_path = get_stylesheet_directory() . '/assets/css/pdp.css';
        $js_path  = get_stylesheet_directory() . '/assets/js/pdp.js';

        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'tno-pdp-style',
                get_stylesheet_directory_uri() . '/assets/css/pdp.css',
                array(),
                filemtime( $css_path )
            );
        }

        if ( file_exists( $js_path ) ) {
            wp_enqueue_script(
                'tno-pdp-script',
                get_stylesheet_directory_uri() . '/assets/js/pdp.js',
                array(),
                filemtime( $js_path ),
                true
            );

            wp_localize_script( 'tno-pdp-script', 'tnoPdpData', array(
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'tno_pdp_nonce' ),
                'productId' => get_queried_object_id(),
                'isLogged'  => is_user_logged_in(),
            'cartUrl'   => WC_AJAX::get_endpoint( 'add_to_cart' ),
            ) );
        }
    }
}, 99 );

require_once get_template_directory() . '/inc/pdp-gallery.php';
