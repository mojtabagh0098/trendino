<?php
/** Single product gallery and action dialogs. Variables come from single-product.php. */
defined( 'ABSPATH' ) || exit;
$gallery = array();
foreach ( $all_images as $image_id ) {
    $gallery[] = array( 'full' => wp_get_attachment_image_url( $image_id, 'full' ), 'thumb' => wp_get_attachment_image_url( $image_id, 'thumbnail' ), 'alt' => get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ?: get_the_title() );
}
if ( ! $gallery ) $gallery[] = array( 'full' => wc_placeholder_img_src( 'large' ), 'thumb' => wc_placeholder_img_src( 'thumbnail' ), 'alt' => get_the_title() );
$compare_data = array( 'id' => $product_id, 'title' => get_the_title(), 'url' => get_permalink(), 'image' => $main_img_url, 'price' => wp_strip_all_tags( $product->get_price_html() ), 'features' => array_slice( $key_features, 0, 6 ) );
?>
<div id="tno-gallery-data" hidden data-images="<?php echo esc_attr( wp_json_encode( $gallery ) ); ?>" data-history="<?php echo esc_attr( wp_json_encode( $history ) ); ?>" data-currency="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>" data-compare="<?php echo esc_attr( wp_json_encode( $compare_data ) ); ?>" data-cart-added="<?php echo (int) ( ( $GLOBALS['tno_cart_added_product'] ?? 0 ) === $product_id ); ?>" data-video="<?php echo esc_url( $video_url ); ?>" data-catalog="<?php echo esc_url( $catalog_url ); ?>"></div>

<div class="tno-modal-backdrop tno-full-gallery" id="tno-lightbox-modal" role="dialog" aria-modal="true" aria-label="گالری تصاویر محصول" hidden>
    <div class="tno-gallery-top"><div class="tno-gallery-counter"><button type="button" class="tno-gallery-fullscreen" aria-label="نمایش تمام صفحه">⛶</button><span id="tno-gallery-count" dir="ltr"></span></div><button type="button" class="tno-overlay-close js-close-modal" aria-label="بستن">×</button></div>
    <div class="tno-gallery-view"><button type="button" class="tno-gallery-arrow js-gallery-prev" aria-label="تصویر قبلی">‹</button><div class="tno-gallery-image-wrap"><img id="tno-lightbox-img" alt=""></div><button type="button" class="tno-gallery-arrow js-gallery-next" aria-label="تصویر بعدی">›</button></div>
    <div class="tno-gallery-zoom"><button type="button" class="js-zoom-out" aria-label="کوچک‌نمایی">⊖</button><button type="button" class="js-zoom-in" aria-label="بزرگ‌نمایی">⊕</button></div>
    <div class="tno-gallery-filmstrip" aria-label="تصاویر محصول"></div>
</div>

<div class="tno-modal-backdrop tno-action-backdrop" id="tno-chart-modal" role="dialog" aria-modal="true" aria-labelledby="tno-chart-title" hidden>
  <div class="tno-action-dialog tno-chart-dialog" dir="rtl">
    <button type="button" class="tno-dialog-close js-close-modal" aria-label="بستن">×</button>
    <div class="tno-chart-header"><img src="<?php echo esc_url( $main_img_url ); ?>" alt=""><div><h3 id="tno-chart-title">نمودار قیمت</h3><p><?php echo esc_html( get_the_title() ); ?></p></div></div>
    <?php if ( $product->is_type( 'variable' ) ) : ?><p class="tno-chart-variation-note">نمودار حداقل قیمت متغیرهای محصول را نشان می‌دهد.</p><?php endif; ?>
    <div id="tno-price-chart" class="tno-price-chart" aria-live="polite"></div>
    <div class="tno-chart-legend"><span><i class="is-sale"></i> قیمت تخفیف خورده</span><span><i class="is-regular"></i> قیمت بدون تخفیف</span></div>
  </div>
</div>

<div class="tno-modal-backdrop tno-action-backdrop" id="tno-share-modal" role="dialog" aria-modal="true" aria-labelledby="tno-share-title" hidden>
  <div class="tno-action-dialog tno-share-dialog" dir="rtl">
    <button type="button" class="tno-dialog-close js-close-modal" aria-label="بستن">×</button><h3 id="tno-share-title">اشتراک گذاری</h3><p>این محصول را با دوستان خود به اشتراک بگذارید.</p>
    <div class="tno-share-bottom"><span>اشتراک گذاری با شبکه های اجتماعی:</span><div class="tno-share-icons" dir="ltr">
      <button type="button" class="js-copy-link" aria-label="کپی لینک برای اینستاگرام" title="کپی لینک برای اینستاگرام"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></button>
      <a class="js-share-link" data-network="telegram" target="_blank" rel="noopener noreferrer" aria-label="تلگرام">✈</a>
      <a class="js-share-link" data-network="facebook" target="_blank" rel="noopener noreferrer" aria-label="فیسبوک">f</a>
      <a class="js-share-link" data-network="x" target="_blank" rel="noopener noreferrer" aria-label="ایکس">𝕏</a>
    </div></div><span class="tno-share-status" role="status"></span>
  </div>
</div>

<div class="tno-modal-backdrop tno-action-backdrop" id="tno-cart-success-modal" role="dialog" aria-modal="true" aria-labelledby="tno-cart-success-title" hidden>
  <div class="tno-action-dialog tno-cart-success" dir="rtl"><button type="button" class="tno-dialog-close js-close-modal" aria-label="بستن">×</button>
    <div class="tno-success-icon" aria-hidden="true"><svg viewBox="0 0 56 56" width="56" height="56"><path fill="#20bc8a" d="M28 1l6 4 7 1 2 7 6 4-1 7 3 6-4 6-1 7-7 2-4 6-7-1-6 3-6-4-7-1-2-7-6-4 1-7-3-6 4-6 1-7 7-2 4-6 7 1z"/><path d="M18 28l7 7 14-15" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    <h3 id="tno-cart-success-title">کالا به سبد خرید <span>اضافه</span> شد</h3><p><?php echo esc_html( get_the_title() ); ?></p><a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="tno-cart-link">برو به سبد خرید</a>
  </div>
</div>
<div class="tno-modal-backdrop tno-action-backdrop" id="tno-compare-modal" role="dialog" aria-modal="true" aria-labelledby="tno-compare-title" hidden><div class="tno-action-dialog tno-compare-dialog" dir="rtl"><button type="button" class="tno-dialog-close js-close-modal" aria-label="بستن">×</button><h3 id="tno-compare-title">مقایسه محصولات</h3><p>برای مقایسه، محصولات دیگر را نیز از صفحه همان محصول به این فهرست اضافه کنید.</p><div class="tno-compare-list"></div><button type="button" class="tno-compare-clear">پاک کردن فهرست</button></div></div>
<?php if ( $video_url ) : ?><div class="tno-modal-backdrop tno-action-backdrop" id="tno-video-modal" role="dialog" aria-modal="true" aria-label="ویدیو محصول" hidden><div class="tno-action-dialog tno-video-dialog"><button type="button" class="tno-dialog-close js-close-modal" aria-label="بستن">×</button><video controls preload="none" src="<?php echo esc_url( $video_url ); ?>"></video></div></div><?php endif; ?>
