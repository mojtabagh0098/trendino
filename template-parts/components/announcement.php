<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! get_theme_mod( 'tno_ann_enable', true ) ) {
    return;
}

$ann_text     = get_theme_mod( 'tno_ann_text', 'شروع تخفیف تابستانه <b>ترندینو</b> از 28 تیرماه' );
$ann_link     = get_theme_mod( 'tno_ann_link', '' );
$ann_closable = get_theme_mod( 'tno_ann_closable', true );
$ann_bg_shape = get_theme_mod( 'tno_ann_show_bg_shape', true );
?>
<div class="c-announcement">
    <div class="container c-announcement__container">
        
        <!-- آیکون‌های تزئینی سمت راست نوار اعلان -->
        <div class="c-announcement__side c-announcement__side--right">
            <?php if ( $ann_bg_shape ) : ?>
                <svg class="c-announcement__bags" width="66" height="42" viewBox="0 0 66 42" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="28" y="4" width="34" height="38" rx="6" fill="white" fill-opacity="0.1"/>
                    <path d="M39 14C39 17.3137 41.6863 20 45 20C48.3137 20 51 17.3137 51 14" stroke="#012561" stroke-width="2.5" stroke-linecap="round"/>
                    <rect x="4" y="18" width="22" height="24" rx="4" fill="white" fill-opacity="0.15"/>
                    <path d="M11 25C11 27.2091 12.7909 29 15 29C17.2091 29 19 27.2091 19 25" stroke="#012561" stroke-width="2" stroke-linecap="round"/>
                </svg>
            <?php endif; ?>
        </div>

        <!-- متن مرکزی نوار اعلان -->
        <div class="c-announcement__content">
            <?php if ( ! empty( $ann_link ) ) : ?>
                <a href="<?php echo esc_url( $ann_link ); ?>" class="c-announcement__link">
                    <?php echo wp_kses_post( $ann_text ); ?>
                </a>
            <?php else : ?>
                <?php echo wp_kses_post( $ann_text ); ?>
            <?php endif; ?>
        </div>

        <!-- دکمه بستن در سمت چپ -->
        <div class="c-announcement__side c-announcement__side--left">
            <?php if ( $ann_closable ) : ?>
                <button type="button" class="c-announcement__close" aria-label="بستن اعلان">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 15 15" fill="none">
                        <path d="M14.781 13.7198C14.8507 13.7895 14.906 13.8722 14.9437 13.9632C14.9814 14.0543 15.0008 14.1519 15.0008 14.2504C15.0008 14.349 14.9814 14.4465 14.9437 14.5376C14.906 14.6286 14.8507 14.7114 14.781 14.781C14.7114 14.8507 14.6286 14.906 14.5376 14.9437C14.4465 14.9814 14.349 15.0008 14.2504 15.0008C14.1519 15.0008 14.0543 14.9814 13.9632 14.9437C13.8722 14.906 13.7198 14.781L7.50042 8.56073L1.28104 14.781C1.14031 14.9218 0.94944 15.0008 0.750417 15.0008C0.551394 15.0008 0.360523 14.9218 0.219792 14.781C0.0790615 14.6403 3.92322e-09 14.4494 0 14.2504C-3.92322e-09 14.0514 0.0790615 13.8605 0.219792 13.7198L6.4401 7.50042L0.219792 1.28104C0.0790615 1.14031 0 0.94944 0 0.750417C0 0.551394 0.0790615 0.360523 0.219792 0.219792C0.360523 0.0790615 0.551394 0 0.750417 0C0.94944 0 1.14031 0.0790615 1.28104 0.219792L7.50042 6.4401L13.7198 0.219792C13.8605 0.0790615 14.0514 -3.92322e-09 14.2504 0C14.4494 3.92322e-09 14.6403 0.0790615 14.781 0.219792C14.9218 0.360523 15.0008 0.551394 15.0008 0.750417C15.0008 0.94944 14.9218 1.14031 14.781 1.28104L8.56073 7.50042L14.781 13.7198Z" fill="currentColor"/>
                    </svg>
                </button>
            <?php endif; ?>
        </div>

    </div>
</div>