<?php
$status = isset($_GET['tf_footer_status'])
    ? sanitize_key(wp_unslash($_GET['tf_footer_status']))
    : '';

$socials = array(
    'telegram'  => array(
        'تلگرام',
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_38_1352)">
                <path d="M13.7501 0.203328L0.750106 7.7002C-0.324894 8.32208 -0.221769 9.9127 0.928231 10.3846L4.50011 11.8627V14.5002C4.50011 15.9502 6.35011 16.5471 7.20636 15.394L8.57511 13.5471L12.072 14.9908C12.2564 15.0658 12.4501 15.1033 12.6439 15.1033C12.9001 15.1033 13.1532 15.0377 13.3814 14.9096C13.7814 14.6846 14.0564 14.2846 14.1282 13.8315L15.9845 1.73145C16.1751 0.478328 14.8314 -0.418547 13.7501 0.203328ZM6.00011 14.5002V12.4815L7.14386 12.9533L6.00011 14.5002ZM12.6439 13.6033L7.83761 11.619L12.2189 5.29708C12.5532 4.8127 11.922 4.2502 11.4782 4.63458L4.86886 10.394L1.50011 9.0002L14.5001 1.5002L12.6439 13.6033Z" fill="#012561"/>
            </g>
            <defs>
                <clipPath id="clip0_38_1352">
                    <rect width="16" height="16" fill="white"/>
                </clipPath>
            </defs>
        </svg>'
    ),
    'youtube'   => array(
        'یوتیوب',
        '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.75 15H5.25C3 15 1.5 13.5 1.5 11.25V6.75C1.5 4.5 3 3 5.25 3H12.75C15 3 16.5 4.5 16.5 6.75V11.25C16.5 13.5 15 15 12.75 15Z" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" stroke="#012561"/>
            <path d="M8.54995 7.12547L10.425 8.25047C11.1 8.70047 11.1 9.37547 10.425 9.82547L8.54995 10.9505C7.79995 11.4005 7.19995 11.0255 7.19995 10.2005V7.95047C7.19995 6.97547 7.79995 6.67547 8.54995 7.12547Z" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" stroke="#012561"/>
        </svg>'
    ),
    'instagram' => array(
        'اینستاگرام',
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8.00303 4.40586C6.01553 4.40586 4.4124 6.00898 4.4124 7.99648C4.4124 9.98398 6.01553 11.5871 8.00303 11.5871C9.99053 11.5871 11.5937 9.98398 11.5937 7.99648C11.5937 6.00898 9.99053 4.40586 8.00303 4.40586ZM8.00303 10.3309C6.71865 10.3309 5.66865 9.28398 5.66865 7.99648C5.66865 6.70898 6.71553 5.66211 8.00303 5.66211C9.29053 5.66211 10.3374 6.70898 10.3374 7.99648C10.3374 9.28398 9.2874 10.3309 8.00303 10.3309ZM12.578 4.25898C12.578 4.72461 12.203 5.09648 11.7405 5.09648C11.2749 5.09648 10.903 4.72148 10.903 4.25898C10.903 3.79648 11.278 3.42148 11.7405 3.42148C12.203 3.42148 12.578 3.79648 12.578 4.25898ZM14.9562 5.10898C14.903 3.98711 14.6468 2.99336 13.8249 2.17461C13.0062 1.35586 12.0124 1.09961 10.8905 1.04336C9.73428 0.977734 6.26865 0.977734 5.1124 1.04336C3.99365 1.09648 2.9999 1.35273 2.17803 2.17148C1.35615 2.99023 1.10303 3.98398 1.04678 5.10586C0.981152 6.26211 0.981152 9.72773 1.04678 10.884C1.0999 12.0059 1.35615 12.9996 2.17803 13.8184C2.9999 14.6371 3.99053 14.8934 5.1124 14.9496C6.26865 15.0152 9.73428 15.0152 10.8905 14.9496C12.0124 14.8965 13.0062 14.6402 13.8249 13.8184C14.6437 12.9996 14.8999 12.0059 14.9562 10.884C15.0218 9.72773 15.0218 6.26523 14.9562 5.10898ZM13.4624 12.1246C13.2187 12.7371 12.7468 13.209 12.1312 13.4559C11.2093 13.8215 9.02178 13.7371 8.00303 13.7371C6.98428 13.7371 4.79365 13.8184 3.8749 13.4559C3.2624 13.2121 2.79053 12.7402 2.54365 12.1246C2.17803 11.2027 2.2624 9.01523 2.2624 7.99648C2.2624 6.97773 2.18115 4.78711 2.54365 3.86836C2.7874 3.25586 3.25928 2.78398 3.8749 2.53711C4.79678 2.17148 6.98428 2.25586 8.00303 2.25586C9.02178 2.25586 11.2124 2.17461 12.1312 2.53711C12.7437 2.78086 13.2155 3.25273 13.4624 3.86836C13.828 4.79023 13.7437 6.97773 13.7437 7.99648C13.7437 9.01523 13.828 11.2059 13.4624 12.1246Z" fill="#012561"/>
        </svg>'
    ),
    'whatsapp'  => array(
        'واتساپ', 
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.9031 3.03437C11.5938 1.72187 9.85 1 7.99687 1C4.17187 1 1.05937 4.1125 1.05937 7.9375C1.05937 9.15938 1.37813 10.3531 1.98438 11.4062L1 15L4.67812 14.0344C5.69063 14.5875 6.83125 14.8781 7.99375 14.8781H7.99687C11.8188 14.8781 15 11.7656 15 7.94063C15 6.0875 14.2125 4.34687 12.9031 3.03437ZM7.99687 13.7094C6.95937 13.7094 5.94375 13.4312 5.05937 12.9062L4.85 12.7812L2.66875 13.3531L3.25 11.225L3.1125 11.0063C2.53437 10.0875 2.23125 9.02812 2.23125 7.9375C2.23125 4.75938 4.81875 2.17188 8 2.17188C9.54062 2.17188 10.9875 2.77187 12.075 3.8625C13.1625 4.95312 13.8313 6.4 13.8281 7.94063C13.8281 11.1219 11.175 13.7094 7.99687 13.7094ZM11.1594 9.39062C10.9875 9.30313 10.1344 8.88438 9.975 8.82812C9.81563 8.76875 9.7 8.74062 9.58438 8.91562C9.46875 9.09062 9.1375 9.47813 9.03438 9.59688C8.93438 9.7125 8.83125 9.72812 8.65938 9.64062C7.64062 9.13125 6.97188 8.73125 6.3 7.57812C6.12188 7.27187 6.47812 7.29375 6.80937 6.63125C6.86562 6.51562 6.8375 6.41563 6.79375 6.32812C6.75 6.24062 6.40313 5.3875 6.25938 5.04063C6.11875 4.70312 5.975 4.75 5.86875 4.74375C5.76875 4.7375 5.65312 4.7375 5.5375 4.7375C5.42188 4.7375 5.23438 4.78125 5.075 4.95312C4.91562 5.12813 4.46875 5.54688 4.46875 6.4C4.46875 7.25313 5.09063 8.07813 5.175 8.19375C5.2625 8.30938 6.39687 10.0594 8.1375 10.8125C9.2375 11.2875 9.66875 11.3281 10.2188 11.2469C10.5531 11.1969 11.2437 10.8281 11.3875 10.4219C11.5312 10.0156 11.5312 9.66875 11.4875 9.59688C11.4469 9.51875 11.3313 9.475 11.1594 9.39062Z" fill="#012561"/>
        </svg>'
    ),
);
?>

<footer id="theme-footer" class="tf-footer" dir="rtl">

    <div class="tf-footer__box">

    <div class="tf-footer__notch" aria-hidden="true">
        <svg class="tf-footer__notch-outer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 537 61" preserveAspectRatio="none" fill="none">
            <path d="M 0 0 Q 8 0, 12 6.78 L 40 54.22 Q 44 61, 52 61 L 485 61 Q 493 61, 497 54.22 L 525 6.78 Q 529 0, 537 0 Z" fill="#FFFFFF"/>
        </svg>
        <svg class="tf-footer__notch-inner" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 114" preserveAspectRatio="none" fill="none">
            <path d="M 12 0 L 988 0 A 12 12 0 0 1 998.39 18 L 947.58 106 Q 942.96 114, 933.72 114 L 66.28 114 Q 57.04 114, 52.42 106 L 1.61 18 A 12 12 0 0 1 12 0 Z" fill="#F2F2F2"/>
        </svg>
    </div>

        <!-- خبرنامه -->
        <div class="tf-footer__newsletter">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                  method="post"
                  class="tf-footer__newsletter-form">
                <input type="hidden" name="action" value="tf_footer_newsletter">
                <?php wp_nonce_field('tf_footer_newsletter', 'tf_footer_nonce'); ?>

                <label class="screen-reader-text" for="tf-footer-email">
                    ایمیل برای عضویت در خبرنامه
                </label>
                <input id="tf-footer-email"
                       type="email"
                       name="subscriber_email"
                       placeholder="آدرس ایمیل خود را وارد کنید"
                       autocomplete="email"
                       required>

                <button type="submit">عضویت</button>

                <input type="text"
                       name="website"
                       class="tf-footer__honeypot"
                       tabindex="-1"
                       autocomplete="off"
                       aria-hidden="true">
            </form>

            <?php if (in_array($status, array('success', 'error'), true)) : ?>
                <span class="tf-footer__message" role="status">
                    <?php echo $status === 'success'
                        ? 'ایمیل شما ثبت شد.'
                        : 'ارسال انجام نشد؛ دوباره تلاش کنید.'; ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- ستون‌های اصلی -->
        <div class="tf-footer__main">

            <!-- ستون ۱: دسته‌بندی محصولات -->
            <section class="tf-footer__column">
                <h4 class="footer-title">
                    <?php echo mytheme_style_footer_title( get_theme_mod( 'footer_col1_title', 'دسته بندی محصولات' ) ); ?>
                </h4>
                <?php
                mytheme_footer_menu( 1, 'footer_cats',
                    '<ul class="footer-links"><li><a href="#">لپتاپ</a></li><li><a href="#">گوشی</a></li><li><a href="#">ساعت هوشمند</a></li><li><a href="#">هدفون</a></li><li><a href="#">کنسول بازی</a></li></ul>'
                );
                ?>
            </section>

            <!-- ستون ۲: محصولات پرفروش -->
            <section class="tf-footer__column">
                <h4 class="footer-title">
                    <?php echo mytheme_style_footer_title( get_theme_mod( 'footer_col2_title', 'محصولات پرفروش' ) ); ?>
                </h4>
                
            </section>

            <!-- ستون مرکزی: لوگو، توضیحات، شبکه‌های اجتماعی -->
            <div class="tf-footer__brand">
                <?php if ( get_theme_mod( 'footer_logo' ) ) : ?>
                    <img class="tf-footer__logo"
                         src="<?php echo esc_url( get_theme_mod( 'footer_logo' ) ); ?>"
                         alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                <?php else : ?>
                    <div class="tf-footer__text-logo default-logo" aria-label="ترندینو">
                        <span class="logo-icon"><i>T</i><i>D</i></span>
                        <strong class="logo-text">ترندینو</strong>
                    </div>
                <?php endif; ?>

                <p class="footer-bio">
                    <?php echo nl2br( esc_html( get_theme_mod( 'footer_about_text', "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.\nلورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است." ) ) ); ?>
                </p>

                <div class="tf-footer__socials footer-socials" aria-label="شبکه‌های اجتماعی">
                    <?php foreach ( $socials as $key => $social ) :
                        $url = get_theme_mod( 'footer_social_' . $key, '#' );
                        if ( $url && $url !== '#' ) : ?>
                            <a href="<?php echo esc_url( $url ); ?>"
                               aria-label="<?php echo esc_attr( $social[0] ); ?>"
                               target="_blank"
                               rel="noopener noreferrer"><?=$social[1]; ?></a>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>

            <!-- ستون ۳: دسترسی سریع -->
            <section class="tf-footer__column">
                <h4 class="footer-title">
                    <?php echo mytheme_style_footer_title( get_theme_mod( 'footer_col3_title', 'دسترسی سریع' ) ); ?>
                </h4>
                <?php
                mytheme_footer_menu( 3, 'footer_quickaccess',
                    '<ul class="footer-links"><li><a href="#">محصولات</a></li><li><a href="#">درباره ما</a></li><li><a href="#">تماس با ما</a></li><li><a href="#">بلاگ</a></li><li><a href="#">شگفت انگیزها</a></li></ul>'
                );
                ?>
            </section>

            <!-- ستون ۴: نمادهای اعتبار -->
            <section class="tf-footer__column tf-footer__trust">
                <h4 class="footer-title">
                    <?php echo mytheme_style_footer_title( get_theme_mod( 'footer_col4_title', 'نمادهای اعتبار' ) ); ?>
                </h4>
                <div class="tf-footer__badges trust-badges">
                    <?php foreach ( array( 'footer_badge_1', 'footer_badge_2' ) as $badge ) : ?>
                        <div class="badge-box">
                            <?php
                            $badge_html = get_theme_mod( $badge );
                            if ( $badge_html ) {
                                echo wp_kses_post( $badge_html );
                            } else {
                                echo '<span class="tf-footer__badge-placeholder">نشان<br>اعتماد</span>';
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>

        <!-- بخش پایین -->
        <div class="tf-footer__bottom footer-bottom-bar">

            <div class="tf-footer__contact contact-item">
                <div class="contact-label">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.20768 12.4587L7.66602 14.0003C7.34102 14.3253 6.82435 14.3253 6.49102 14.0087C6.39935 13.917 6.30768 13.8337 6.21602 13.742C5.35768 12.8753 4.58268 11.967 3.89102 11.017C3.20768 10.067 2.65768 9.11699 2.25768 8.17533C1.86602 7.22533 1.66602 6.31699 1.66602 5.45033C1.66602 4.88366 1.76602 4.34199 1.96602 3.84199C2.16602 3.33366 2.48268 2.86699 2.92435 2.45033C3.45768 1.92533 4.04102 1.66699 4.65768 1.66699C4.89102 1.66699 5.12435 1.71699 5.33268 1.81699C5.54935 1.91699 5.74102 2.06699 5.89102 2.28366L7.82435 5.00866C7.97435 5.21699 8.08268 5.40866 8.15768 5.59199C8.23268 5.76699 8.27435 5.94199 8.27435 6.10033C8.27435 6.30033 8.21602 6.50033 8.09935 6.69199C7.99102 6.88366 7.83268 7.08366 7.63268 7.28366L6.99935 7.94199C6.90768 8.03366 6.86602 8.14199 6.86602 8.27533C6.86602 8.34199 6.87435 8.40033 6.89102 8.46699C6.91602 8.53366 6.94102 8.58366 6.95768 8.63366C7.10768 8.90866 7.36602 9.26699 7.73268 9.70033C8.10768 10.1337 8.50768 10.5753 8.94102 11.017C9.02435 11.1003 9.11602 11.1837 9.19935 11.267C9.53268 11.592 9.54102 12.1253 9.20768 12.4587Z" fill="white"/>
                        <path d="M18.3083 15.2752C18.3083 15.5085 18.2667 15.7502 18.1833 15.9835C18.1583 16.0502 18.1333 16.1169 18.1 16.1835C17.9583 16.4835 17.775 16.7669 17.5333 17.0335C17.125 17.4835 16.675 17.8085 16.1667 18.0169C16.1583 18.0169 16.15 18.0252 16.1417 18.0252C15.65 18.2252 15.1167 18.3335 14.5417 18.3335C13.6917 18.3335 12.7833 18.1335 11.825 17.7252C10.8667 17.3169 9.90833 16.7669 8.95833 16.0752C8.63333 15.8335 8.30833 15.5919 8 15.3335L10.725 12.6085C10.9583 12.7835 11.1667 12.9169 11.3417 13.0085C11.3833 13.0252 11.4333 13.0502 11.4917 13.0752C11.5583 13.1002 11.625 13.1085 11.7 13.1085C11.8417 13.1085 11.95 13.0585 12.0417 12.9669L12.675 12.3419C12.8833 12.1335 13.0833 11.9752 13.275 11.8752C13.4667 11.7585 13.6583 11.7002 13.8667 11.7002C14.025 11.7002 14.1917 11.7335 14.375 11.8085C14.5583 11.8835 14.75 11.9919 14.9583 12.1335L17.7167 14.0919C17.9333 14.2419 18.0833 14.4169 18.175 14.6252C18.2583 14.8335 18.3083 15.0419 18.3083 15.2752Z" fill="white"/>
                    </svg>
                    <span>تلفن تماس</span>
                </div>
                <div class="tf-footer__phones phones-wrapper contact-value" dir="ltr">
                    <?php foreach ( array( 'footer_phone_1', 'footer_phone_2' ) as $key ) :
                        $phone = get_theme_mod( $key );
                        if ( $phone ) : ?>
                            <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
                                <?php echo esc_html( $phone ); ?>
                            </a>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>

            <div class="tf-footer__contact contact-item">
                <div class="contact-label">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.1829 7.04134C16.3079 3.19134 12.9496 1.45801 9.99959 1.45801C9.99959 1.45801 9.99959 1.45801 9.99126 1.45801C7.04959 1.45801 3.68292 3.18301 2.80792 7.03301C1.83292 11.333 4.46626 14.9747 6.84959 17.2663C7.73292 18.1163 8.86626 18.5413 9.99959 18.5413C11.1329 18.5413 12.2663 18.1163 13.1413 17.2663C15.5246 14.9747 18.1579 11.3413 17.1829 7.04134ZM9.99959 11.2163C8.54959 11.2163 7.37459 10.0413 7.37459 8.59134C7.37459 7.14134 8.54959 5.96634 9.99959 5.96634C11.4496 5.96634 12.6246 7.14134 12.6246 8.59134C12.6246 10.0413 11.4496 11.2163 9.99959 11.2163Z" fill="white"/>
                    </svg>
                    <span>نشانی</span>
                </div>
                <div class="contact-value">
                    <?php echo esc_html( get_theme_mod( 'footer_address', 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، پلاک ۱۵۵، طبقه اول' ) ); ?>
                </div>
            </div>

            <div class="tf-footer__contact contact-item">
                <div class="contact-label">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.166 2.91699H5.83268C3.33268 2.91699 1.66602 4.16699 1.66602 7.08366V12.917C1.66602 15.8337 3.33268 17.0837 5.83268 17.0837H14.166C16.666 17.0837 18.3327 15.8337 18.3327 12.917V7.08366C18.3327 4.16699 16.666 2.91699 14.166 2.91699ZM14.5577 7.99199L11.9493 10.0753C11.3993 10.517 10.6993 10.7337 9.99935 10.7337C9.29935 10.7337 8.59102 10.517 8.04935 10.0753L5.44102 7.99199C5.17435 7.77533 5.13268 7.37533 5.34102 7.10866C5.55768 6.84199 5.94935 6.79199 6.21602 7.00866L8.82435 9.09199C9.45768 9.60033 10.5327 9.60033 11.166 9.09199L13.7743 7.00866C14.041 6.79199 14.441 6.83366 14.6493 7.10866C14.866 7.37533 14.8243 7.77533 14.5577 7.99199Z" fill="white"/>
                    </svg>
                    <span>ایمیل مجموعه</span>
                </div>
                <div class="contact-value" dir="ltr">
                    <a href="mailto:<?php echo esc_attr( get_theme_mod( 'footer_email', 'abcdefg@gmail.com' ) ); ?>">
                        <?php echo esc_html( get_theme_mod( 'footer_email', 'abcdefg@gmail.com' ) ); ?>
                    </a>
                </div>
            </div>

            <div class="back-to-top-wrapper">
                <a class="tf-footer__back-to-top back-to-top-btn" href="#top">
                    <i class="fa-solid fa-arrow-up"></i>
                    <span>بازگشت به بالا</span>
                </a>
            </div>

        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>