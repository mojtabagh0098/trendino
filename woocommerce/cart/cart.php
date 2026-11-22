<?php
/**
 * The Template for displaying cart page
 * Path: woocommerce/cart/cart.php
 */

// به‌روزرسانی سبد خرید در صورت ارسال فرم
if ( isset( $_POST['update_cart'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'woocommerce-cart' ) ) {
    // handled via WC
}
?>

<main class="container tno-cart-page-container" dir="rtl">

    <!-- بخش بالایی: استپ‌بار مراحل خرید (سبد خرید -> آدرس و زمان ارسال -> پرداخت) -->
    <div class="tno-checkout-steps">
        <div class="tno-step-item is-active">
            <span class="tno-step-dot"></span>
            <span>سبد خرید</span>
        </div>
        <div class="tno-step-line"></div>
        <div class="tno-step-item">
            <span>آدرس و زمان ارسال</span>
        </div>
        <div class="tno-step-line"></div>
        <div class="tno-step-item">
            <span>پرداخت</span>
        </div>
    </div>

    <?php do_action( 'woocommerce_before_cart' ); ?>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div class="tno-cart-empty-box">
            <p>سبد خرید شما در حال حاضر خالی است.</p>
            <a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="tno-return-shop-btn">
                بازگشت به فروشگاه
            </a>
        </div>
    <?php else : ?>

        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <?php do_action( 'woocommerce_before_cart_table' ); ?>

            <div class="tno-cart-layout">
                
                <!-- ستون راست: لیست اقلام سبد خرید -->
                <div class="tno-cart-items-col">
                    <div class="tno-cart-box-header">
                        <span class="tno-red-dot"></span>
                        <h2 class="tno-cart-box-title">کالاهای سبد خرید</h2>
                        <span class="tno-cart-count-badge"><?php echo esc_html( tno_fa_num( WC()->cart->get_cart_contents_count() ) ); ?></span>
                    </div>

                    <div class="tno-cart-items-list">
                        <?php
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                
                                // قیمت‌ها و تخفیف
                                $regular_price = (float) $_product->get_regular_price();
                                $active_price  = (float) $_product->get_price();
                                $discount_pct  = 0;
                                if ( $_product->is_on_sale() && $regular_price > $active_price && $regular_price > 0 ) {
                                    $discount_pct = round( ( ( $regular_price - $active_price ) / $regular_price ) * 100 );
                                }

                                // ویژگی‌های رنگ یا حافظه (در صورت وجود در سبد یا محصول)
                                $item_data = wc_get_formatted_cart_item_data( $cart_item, true );
                                ?>
                                <div class="tno-cart-item-card" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
                                    <div class="tno-cart-item-thumb">
                                        <?php
                                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
                                        if ( ! $product_permalink ) {
                                            echo $thumbnail;
                                        } else {
                                            printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                        }
                                        ?>
                                    </div>

                                    <div class="tno-cart-item-info">
                                        <h3 class="tno-cart-item-title">
                                            <?php
                                            if ( ! $product_permalink ) {
                                                echo wp_kses_post( $_product->get_name() );
                                            } else {
                                                printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $_product->get_name() ) );
                                            }
                                            ?>
                                        </h3>

                                        <?php if ( ! empty( $item_data ) ) : ?>
                                            <div class="tno-cart-item-attributes">
                                                <?php echo $item_data; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="tno-cart-item-qty-row">
                                            <span>تعداد : </span>
                                            <div class="tno-qty-box-inline">
                                                <?php
                                                if ( $_product->is_sold_individually() ) {
                                                    $min_quantity = 1;
                                                    $max_quantity = 1;
                                                } else {
                                                    $min_quantity = 0;
                                                    $max_quantity = $_product->get_max_purchase_quantity();
                                                }

                                                $product_quantity = woocommerce_quantity_input( array(
                                                    'input_name'   => "cart[{$cart_item_key}]si[qty]",
                                                    'input_value'  => $cart_item['quantity'],
                                                    'max_value'    => $max_quantity,
                                                    'min_value'    => $min_quantity,
                                                    'product_name' => $_product->get_name(),
                                                ), $_product, false );

                                                echo $product_quantity;
                                                ?>
                                            </div>

                                            <!-- دکمه حذف سطل زباله -->
                                            <?php
                                            echo apply_filters(
                                                'woocommerce_cart_item_remove_link',
                                                sprintf(
                                                    '<a href="%s" class="tno-remove-item-btn" aria-label="%s" data-product_id="%s" data-cart_item_key="%s"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>',
                                                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                    esc_html__( 'حذف این آیتم', 'woocommerce' ),
                                                    esc_attr( $product_id ),
                                                    esc_attr( $cart_item_key )
                                                ),
                                                $cart_item_key
                                            );
                                            ?>
                                        </div>
                                    </div>

                                    <div class="tno-cart-item-pricing-col">
                                        <?php if ( $discount_pct > 0 ) : ?>
                                            <div class="tno-cart-badge-row">
                                                <span class="tno-item-disc-badge"><?php echo esc_html( tno_fa_num( $discount_pct ) ); ?>٪</span>
                                                <del class="tno-item-old-price"><?php echo esc_html( tno_fa_num( number_format( $regular_price * $cart_item['quantity'] ) ) ); ?></del>
                                            </div>
                                        <?php endif; ?>
                                        <div class="tno-item-final-price">
                                            <strong><?php echo esc_html( tno_fa_num( number_format( floatval( $regular_price * $cart_item['quantity'] ) ) ) ); ?></strong>
                                            <small>تومان</small>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- ستون چپ: باکس مجموع قابل پرداخت -->
                <div class="tno-cart-summary-col">
                    <div class="tno-summary-box">
                        <div class="tno-summary-title">مجموع قابل پرداخت</div>
                        
                        <div class="tno-summary-row">
                            <span>قیمت کالاها (<?php echo esc_html( tno_fa_num( WC()->cart->get_cart_contents_count() ) ); ?> کالا)</span>
                            <span><strong><?php echo esc_html( tno_fa_num( number_format( WC()->cart->get_subtotal() ) ) ); ?></strong> <small>تومان</small></span>
                        </div>

                        <?php if ( WC()->cart->get_discount_total() > 0 ) : ?>
                            <div class="tno-summary-row tno-discount-row">
                               <span>مجموع تخفیف کالاها</span>
                               <span><strong><?php echo esc_html( tno_fa_num( number_format( WC()->cart->get_discount_total() ) ) ); ?></strong> <small>تومان</small></span>
                            </div>
                        <?php endif; ?>

                        <div class="tno-summary-divider"></div>

                        <div class="tno-summary-row tno-payable-row">
                            <span>قابل پرداخت</span>
                            <div class="tno-payable-final-val">
                                <strong><?php echo esc_html( tno_fa_num( number_format( WC()->cart->get_total( 'raw' ) ) ) ); ?></strong>
                                <small>تومان</small>
                            </div>
                        </div>

                        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="tno-proceed-checkout-btn">
                            ادامه فرایند خرید
                        </a>
                    </div>
                </div>

            </div>

            <?php do_action( 'woocommerce_cart_actions' ); ?>
            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
            
            <button type="submit" class="button" name="update_cart" value="به‌روزرسانی سبد خرید" style="display:none;"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

            <?php do_action( 'woocommerce_after_cart_table' ); ?>
        </form>

    <?php endif; ?>

    <?php do_action( 'woocommerce_after_cart' ); ?>
</main>