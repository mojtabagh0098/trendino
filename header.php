<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>ترندینو | فروشگاه اینترنتی</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php wp_head();?>
</head>
<body>
    <?php get_template_part( 'template-parts/components/announcement' ); ?>
    <?php
$current_user  = wp_get_current_user();
$is_logged_in  = is_user_logged_in();
$display_name  = $is_logged_in ? $current_user->display_name : 'حساب کاربری';
$user_phone    = $is_logged_in ? get_user_meta( $current_user->ID, 'billing_phone', true ) : '';
if ( empty( $user_phone ) && $is_logged_in ) {
    $user_phone = $current_user->user_login;
}
$myaccount_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account' );
$orders_url    = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'orders' ) : $myaccount_url;
$edit_acc_url  = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-account' ) : $myaccount_url;
$popular_tags  = function_exists( 'tno_get_popular_search_terms' ) ? tno_get_popular_search_terms() : array();
?>
<header class="c-header">
    <div class="container c-header__container">
        
        <!-- لوگو و باکس جستجوی هوشمند -->
        <div class="c-header__logo-search">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="c-header__logo">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/logo-header.png" alt="<?php bloginfo( 'name' ); ?>" />
            </a>

            <div class="tno-search-wrap">
                <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="c-header__search tno-search-form" autocomplete="off">
                    <input type="hidden" name="post_type" value="product" />
                    
                    <!-- آیکون ذره‌بین سمت راست (در حالت فوکوس نمایش داده می‌شود) -->
                    <span class="tno-search-focus-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212121" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>

                    <input type="text" name="s" class="tno-search-input" value="<?php echo get_search_query(); ?>" placeholder="نام محصول ، دسته بندی" />
                    
                    <!-- دکمه کپسولی سمت چپ (در حالت پیش‌فرض نمایش داده می‌شود) -->
                    <button type="submit" class="c-header__search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 17 17">
                            <path d="M16.0845 15.2001L12.1727 11.2891C13.3065 9.92798 13.8719 8.1821 13.7512 6.41472C13.6305 4.64733 12.8331 2.9945 11.5249 1.80006C10.2166 0.605618 8.49824 -0.038471 6.72719 0.00177892C4.95615 0.0420288 3.2688 0.763519 2.01616 2.01616C0.763519 3.2688 0.0420288 4.95615 0.00177892 6.72719C-0.038471 8.49824 0.605618 10.2166 1.80006 11.5249C2.9945 12.8331 4.64733 13.6305 6.41472 13.7512C8.1821 13.8719 9.92798 13.3065 11.2891 12.1727L15.2001 16.0845C15.2581 16.1425 15.3271 16.1886 15.403 16.22C15.4788 16.2514 15.5601 16.2676 15.6423 16.2676C15.7244 16.2676 15.8057 16.2514 15.8816 16.22C15.9574 16.1886 16.0264 16.1425 16.0845 16.0845C16.1425 16.0264 16.1886 15.9574 16.22 15.8816C16.2514 15.8057 16.2676 15.7244 16.2676 15.6423C16.2676 15.5601 16.2514 15.4788 16.22 15.403C16.1886 15.3271 16.1425 15.2581 16.0845 15.2001ZM1.26727 6.89227C1.26727 5.77975 1.59717 4.69221 2.21525 3.76719C2.83333 2.84216 3.71184 2.12119 4.73967 1.69545C5.76751 1.2697 6.89851 1.15831 7.98965 1.37535C9.08079 1.59239 10.0831 2.12812 10.8697 2.91479C11.6564 3.70146 12.1921 4.70374 12.4092 5.79489C12.6262 6.88603 12.5148 8.01703 12.0891 9.04486C11.6633 10.0727 10.9424 10.9512 10.0174 11.5693C9.09232 12.1874 8.00479 12.5173 6.89227 12.5173C5.40093 12.5156 3.97115 11.9225 2.91662 10.8679C1.86209 9.81338 1.26892 8.3836 1.26727 6.89227Z"/>
                        </svg>
                        جستجو
                    </button>
                </form>

                <!-- دراپ‌داون جستجو -->
                <div class="tno-search-dropdown">
                    
                    <!-- حالت ۲ و ۳: جستجوهای اخیر و پرطرفدار -->
                    <div class="tno-search-view tno-search-view--default">
                        <div class="tno-search-sec tno-recent-searches-sec" style="display:none;">
                            <div class="tno-search-sec__head">
                                <span>جستجوهای اخیر</span>
                                <button type="button" class="tno-clear-recent-btn" title="پاک کردن تاریخچه">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="tno-search-chips tno-recent-tags"></div>
                        </div>

                        <div class="tno-search-sec">
                            <div class="tno-search-sec__head">
                                <span>جستجوهای پرطرفدار</span>
                            </div>
                            <div class="tno-search-chips">
                                <?php foreach ( $popular_tags as $tag_name ) : ?>
                                    <button type="button" class="tno-search-chip" data-term="<?php echo esc_attr( $tag_name ); ?>">
                                        <?php echo esc_html( $tag_name ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- حالت ۴: نمایش نتایج پیشنهادی و محصولات عکس‌دار -->
                    <div class="tno-search-view tno-search-view--results" style="display:none;">
                        <ul class="tno-search-suggestions"></ul>
                        <div class="tno-search-products"></div>
                    </div>

                    <!-- حالت ۵: نتیجه‌ای یافت نشد -->
                    <div class="tno-search-view tno-search-view--empty" style="display:none;">
                        <p class="tno-search-no-result">متاسفانه نتیجه ای یافت نشد</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- دکمه‌های حساب کاربری و سبد خرید با منوی هاور -->
        <div class="c-header__actions">
            
            <!-- ۱. حساب کاربری + دراپ‌داون -->
            <div class="tno-action-dropdown-wrap tno-profile-wrap">
                <a class="c-header__profile" href="<?php echo esc_url( $myaccount_url ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none">
                        <path d="M9.75977 0.5C11.1283 0.500078 12.4593 0.949795 13.5479 1.7793C14.6362 2.60878 15.4221 3.77244 15.7852 5.0918C16.1482 6.41145 16.0687 7.81429 15.5576 9.08398C15.0465 10.3536 14.1321 11.42 12.9561 12.1201L12.0107 12.6836L13.0566 13.0254C15.3523 13.7755 17.3444 15.3063 18.7109 17.4424L18.9756 17.8779L18.9814 17.8887C18.9989 17.9171 19.0106 17.9494 19.0156 17.9824C19.0206 18.0153 19.0189 18.0488 19.0107 18.0811C19.0025 18.1134 18.9879 18.1443 18.9678 18.1709C18.9477 18.1973 18.9223 18.2197 18.8936 18.2363C18.8647 18.2529 18.8328 18.2634 18.7998 18.2676C18.7668 18.2717 18.7332 18.2697 18.7012 18.2607C18.669 18.2517 18.6384 18.2357 18.6123 18.2148C18.5864 18.1941 18.5647 18.1679 18.5488 18.1387L18.543 18.127L18.3643 17.8311C16.483 14.806 13.2905 13.0031 9.75977 13.0029C6.11514 13.0029 2.83016 14.9237 0.976562 18.127L0.969727 18.1387C0.953835 18.1679 0.932247 18.194 0.90625 18.2148C0.880167 18.2357 0.849516 18.2517 0.817383 18.2607C0.785414 18.2697 0.751689 18.2717 0.71875 18.2676C0.685741 18.2634 0.653811 18.2529 0.625 18.2363C0.596206 18.2197 0.570881 18.1974 0.550781 18.1709C0.530639 18.1443 0.516021 18.1134 0.507812 18.0811C0.499637 18.0488 0.497973 18.0153 0.50293 17.9824C0.507947 17.9494 0.519656 17.9171 0.537109 17.8887L0.543945 17.8779C1.9112 15.5133 4.01339 13.8246 6.46191 13.0254L7.50879 12.6836L6.5625 12.1201C5.38654 11.4201 4.47301 10.3535 3.96191 9.08398C3.45084 7.81429 3.37033 6.41145 3.7334 5.0918C4.0965 3.77226 4.88313 2.60881 5.97168 1.7793C7.0603 0.949757 8.3911 0.5 9.75977 0.5Z" stroke="#131313"/>
                    </svg>
                    <span><?php echo esc_html( $display_name ); ?></span>
                </a>

                <!-- منوی هاور حساب کاربری -->
                <div class="tno-profile-dropdown">
                    <div class="tno-profile-drop__user">
                        <div class="tno-profile-drop__info">
                            <strong><?php echo esc_html( $is_logged_in ? $display_name : 'کاربر مهمان' ); ?></strong>
                            <span><?php echo esc_html( $is_logged_in ? tno_fa_num( $user_phone ) : 'برای مشاهده امکانات وارد شوید' ); ?></span>
                        </div>
                        <span class="tno-profile-drop__avatar-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#212121" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                    </div>

                    <ul class="tno-profile-drop__list">
                        <li>
                            <a href="<?php echo esc_url( $orders_url ); ?>">
                                <span class="tno-p-link-right">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 10a4 4 0 0 1-8 0"/>
                                    </svg>
                                    <span>سفارش های من</span>
                                </span>
                                <svg class="tno-p-arrow" width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>">
                                <span class="tno-p-link-right">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <span>علاقه مندی ها</span>
                                </span>
                                <svg class="tno-p-arrow" width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( $edit_acc_url ); ?>">
                                <span class="tno-p-link-right">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span>اطلاعات حساب کاربری</span>
                                </span>
                                <svg class="tno-p-arrow" width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( $is_logged_in ? wp_logout_url( home_url() ) : $myaccount_url ); ?>">
                                <span class="tno-p-link-right">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span><?php echo $is_logged_in ? 'خروج از حساب کاربری' : 'ورود / ثبت نام'; ?></span>
                                </span>
                                <svg class="tno-p-arrow" width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M6 11L1 6L6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- ۲. سبد خرید + دراپ‌داون -->
            <div class="tno-action-dropdown-wrap tno-cart-wrap">
                <a class="c-header__cart" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '/cart' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20.25 3.75H3.75C3.35218 3.75 2.97064 3.90804 2.68934 4.18934C2.40804 4.47064 2.25 4.85218 2.25 5.25V18.75C2.25 19.1478 2.40804 19.5294 2.68934 19.8107C2.97064 20.092 3.35218 20.25 3.75 20.25H20.25C20.6478 20.25 21.0294 20.092 21.3107 19.8107C21.592 19.5294 21.75 19.1478 21.75 18.75V5.25C21.75 4.85218 21.592 4.47064 21.3107 4.18934C21.0294 3.90804 20.6478 3.75 20.25 3.75ZM20.25 18.75H3.75V5.25H20.25V18.75ZM16.5 8.25C16.5 9.44347 16.0259 10.5881 15.182 11.432C14.3381 12.2759 13.1935 12.75 12 12.75C10.8065 12.75 9.66193 12.2759 8.81802 11.432C7.97411 10.5881 7.5 9.44347 7.5 8.25C7.5 8.05109 7.57902 7.86032 7.71967 7.71967C7.86032 7.57902 8.05109 7.5 8.25 7.5C8.44891 7.5 8.63968 7.57902 8.78033 7.71967C8.92098 7.86032 9 8.05109 9 8.25C9 9.04565 9.31607 9.80871 9.87868 10.3713C10.4413 10.9339 11.2044 11.25 12 11.25C12.7956 11.25 13.5587 10.9339 14.1213 10.3713C14.6839 9.80871 15 9.04565 15 8.25C15 8.05109 15.079 7.86032 15.2197 7.71967C15.3603 7.57902 15.5511 7.5 15.75 7.5C15.9489 7.5 16.1397 7.57902 16.2803 7.71967C16.421 7.86032 16.5 8.05109 16.5 8.25Z" fill="white"/>
                    </svg>
                    سبد خرید
                    <span class="c-header__cart-qty">
                        <?php echo class_exists( 'WooCommerce' ) && WC()->cart ? esc_html( tno_fa_num( WC()->cart->get_cart_contents_count() ) ) : '۰'; ?>
                    </span>
                </a>

                <!-- منوی هاور سبد خرید -->
                <div class="tno-cart-dropdown">
                    <?php echo function_exists( 'tno_render_mini_cart_inner' ) ? tno_render_mini_cart_inner() : ''; ?>
                </div>
            </div>

        </div>
    </div>
</header>
    <div class="container tno-nav-bar">
        <?php
        wp_nav_menu( array(
            'theme_location'  => 'header-menu',
            'container'       => 'nav',
            'container_class' => 'main-header-menu',
            'walker'          => new Header_Menu_Walker(),
        ) );
        ?>

        <?php if ( get_theme_mod( 'tno_hdeal_enable', true ) ) :
            $hd_title      = get_theme_mod( 'tno_hdeal_title', 'شگفت انگیزات' );
            $hd_link       = get_theme_mod( 'tno_hdeal_link', '#' );
            $hd_show_timer = get_theme_mod( 'tno_hdeal_show_timer', true );
            $hd_end_date   = get_theme_mod( 'tno_hdeal_end_date', date( 'Y-m-d\TH:i', strtotime( '+18 days +3 hours +52 minutes' ) ) );
        ?>
            <div class="tno-header-deal">
                <?php if ( $hd_show_timer ) : ?>
                    <div class="tno-header-deal__timer" data-end="<?php echo esc_attr( $hd_end_date ); ?>">
                        <div class="tno-hd-unit">
                            <span class="tno-hd-box" data-unit="days">۱۸</span>
                            <span class="tno-hd-label">روز</span>
                        </div>
                        <span class="tno-hd-sep">:</span>
                        <div class="tno-hd-unit">
                            <span class="tno-hd-box" data-unit="hours">۳</span>
                            <span class="tno-hd-label">ساعت</span>
                        </div>
                        <span class="tno-hd-sep">:</span>
                        <div class="tno-hd-unit">
                            <span class="tno-hd-box" data-unit="minutes">۵۲</span>
                            <span class="tno-hd-label">دقیقه</span>
                        </div>
                        <span class="tno-hd-sep">:</span>
                        <div class="tno-hd-unit">
                            <span class="tno-hd-box" data-unit="seconds">۴۵</span>
                            <span class="tno-hd-label">ثانیه</span>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="<?php echo esc_url( $hd_link ); ?>" class="tno-header-deal__link">
                    <span class="tno-header-deal__title"><?php echo esc_html( $hd_title ); ?></span>
                    <svg class="tno-header-deal__chevron" width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 11L1 6L6 1" stroke="#012561" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="tno-header-deal__badge" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M34.5844 20.006L36.4244 16.826C36.6456 16.4434 36.7057 15.9886 36.5916 15.5617C36.4775 15.1348 36.1986 14.7706 35.8161 14.5493L32.6327 12.7093V9.04266C32.6327 8.60064 32.4571 8.17671 32.1446 7.86415C31.832 7.55159 31.4081 7.376 30.9661 7.376H27.3011L25.4627 4.19433C25.2407 3.81252 24.8773 3.5335 24.4511 3.41766C24.2397 3.36036 24.019 3.34558 23.8019 3.37419C23.5847 3.4028 23.3754 3.47423 23.1861 3.58433L20.0027 5.42433L16.8194 3.58266C16.4366 3.36166 15.9817 3.30177 15.5547 3.41616C15.1278 3.53056 14.7638 3.80987 14.5427 4.19266L12.7027 7.376H9.03774C8.59572 7.376 8.17179 7.55159 7.85923 7.86415C7.54667 8.17671 7.37108 8.60064 7.37108 9.04266V12.7077L4.18774 14.5477C3.9978 14.6569 3.83131 14.8026 3.69785 14.9764C3.56439 15.1502 3.46659 15.3487 3.41006 15.5604C3.35353 15.7721 3.33939 15.9929 3.36845 16.2101C3.39752 16.4273 3.46921 16.6366 3.57941 16.826L5.41941 20.006L3.57941 23.186C3.35939 23.569 3.2997 24.0234 3.41334 24.4502C3.52699 24.8769 3.80475 25.2415 4.18608 25.4643L7.36941 27.3043V30.9693C7.36941 31.4114 7.545 31.8353 7.85757 32.1478C8.17013 32.4604 8.59405 32.636 9.03608 32.636H12.7027L14.5427 35.8193C14.6903 36.0716 14.901 36.281 15.154 36.4272C15.4071 36.5733 15.6939 36.651 15.9861 36.6527C16.2761 36.6527 16.5644 36.576 16.8211 36.4277L20.0011 34.5877L23.1844 36.4277C23.5671 36.6484 24.0217 36.7083 24.4485 36.5942C24.8753 36.4802 25.2395 36.2015 25.4611 35.8193L27.2994 32.636H30.9644C31.4064 32.636 31.8304 32.4604 32.1429 32.1478C32.4555 31.8353 32.6311 31.4114 32.6311 30.9693V27.3043L35.8144 25.4643C36.004 25.3548 36.1702 25.2089 36.3034 25.035C36.4366 24.8612 36.5343 24.6628 36.5908 24.4512C36.6473 24.2396 36.6615 24.019 36.6327 23.8019C36.6038 23.5848 36.5325 23.3755 36.4227 23.186L34.5844 20.006ZM15.8344 11.656C16.4977 11.6562 17.1337 11.9199 17.6025 12.3891C18.0714 12.8582 18.3346 13.4944 18.3344 14.1577C18.3342 14.8209 18.0705 15.4569 17.6013 15.9258C17.1322 16.3946 16.496 16.6579 15.8327 16.6577C15.1695 16.6574 14.5335 16.3938 14.0646 15.9246C13.5958 15.4554 13.3325 14.8193 13.3327 14.156C13.333 13.4927 13.5967 12.8567 14.0658 12.3879C14.535 11.919 15.1711 11.6558 15.8344 11.656ZM16.3344 27.656L13.6677 25.6577L23.6677 12.3243L26.3344 14.3227L16.3344 27.656ZM24.1677 28.3227C23.8393 28.3226 23.5142 28.2578 23.2108 28.132C22.9074 28.0062 22.6318 27.8219 22.3996 27.5896C22.1675 27.3573 21.9834 27.0815 21.8578 26.7781C21.7322 26.4746 21.6676 26.1494 21.6677 25.821C21.6679 25.4926 21.7326 25.1674 21.8584 24.864C21.9842 24.5607 22.1685 24.285 22.4008 24.0529C22.6331 23.8207 22.9089 23.6366 23.2123 23.511C23.5158 23.3855 23.841 23.3209 24.1694 23.321C24.8327 23.3212 25.4687 23.5849 25.9375 24.0541C26.4064 24.5232 26.6696 25.1594 26.6694 25.8227C26.6692 26.4859 26.4055 27.1219 25.9363 27.5908C25.4672 28.0596 24.831 28.3229 24.1677 28.3227Z" fill="currentColor"/>
                        </svg>
                    </span>
                </a>
            </div>
        <?php endif; ?>
    </div>