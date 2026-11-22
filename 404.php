<?php
/** The not-found page uses the theme's existing header and footer. */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
get_header();
?>
<main class="tno-not-found" dir="rtl">
    <div class="tno-not-found__inner">
        <div class="tno-not-found__content">
            <h1>متاسفانه صفحه موردنظر یافت نشد</h1>
            <div class="tno-not-found__actions">
                <a class="tno-not-found__button tno-not-found__button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به صفحه اصلی</a>
                <a class="tno-not-found__button tno-not-found__button--countdown" href="<?php echo esc_url( home_url( '/' ) ); ?>" data-404-countdown>بازگشت به صفحه اصلی</a>
            </div>
        </div>
        <div class="tno-not-found__art" aria-hidden="true">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/404-plug.svg' ); ?>" alt="" width="360" height="510">
        </div>
    </div>
</main>
<?php get_footer(); ?>
