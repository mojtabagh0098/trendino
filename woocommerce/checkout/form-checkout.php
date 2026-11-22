<?php
/**
 * Checkout Form
 * Path: woocommerce/checkout/form-checkout.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// بررسی اینکه آیا سبد خرید خالی است یا خیر
if ( WC()->cart->is_empty() ) {
    echo '<main class="container tno-checkout-page-container" dir="rtl"><div class="tno-cart-empty-box"><p>سبد خرید شما خالی است.</p><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="tno-return-shop-btn">بازگشت به فروشگاه</a></div></main>';
    get_footer();
    return;
}

// انجام عملیات پرداخت یا ثبت سفارش در صورت ارسال فرم
do_action( 'woocommerce_before_checkout_form', $checkout );
?>

<main class="container tno-checkout-page-container" dir="rtl">

    <!-- استپ‌بار مراحل خرید (سبد خرید -> آدرس و زمان ارسال -> پرداخت) -->
    <div class="tno-checkout-steps">
        <div class="tno-step-item is-done">
            <span class="tno-step-dot"></span>
            <span>سبد خرید</span>
        </div>
        <div class="tno-step-line is-active"></div>
        <div class="tno-step-item is-active">
            <span class="tno-step-dot"></span>
            <span>آدرس و زمان ارسال</span>
        </div>
        <div class="tno-step-line"></div>
        <div class="tno-step-item">
            <span>پرداخت</span>
        </div>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <div class="tno-checkout-layout">
            
            <!-- ستون راست: اطلاعات گیرنده، آدرس‌ها، روش ارسال، زمان ارسال و توضیحات -->
            <div class="tno-checkout-main-col">
                
                <?php if ( $checkout->get_checkout_fields() ) : ?>

                    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                    <!-- ۱. اطلاعات گیرنده -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">اطلاعات گیرنده</h3>
                        </div>
                        
                        <div class="tno-form-grid-2">
                            <div class="tno-field-wrap">
                                <label>نام</label>
                                <input type="text" name="billing_first_name" class="tno-input" value="<?php echo esc_attr( WC()->customer->get_billing_first_name() ); ?>" placeholder="مثال: رضا">
                            </div>
                            <div class="tno-field-wrap">
                                <label>نام خانوادگی</label>
                                <input type="text" name="billing_last_name" class="tno-input" value="<?php echo esc_attr( WC()->customer->get_billing_last_name() ); ?>" placeholder="مثال: جنیدی">
                            </div>
                            <div class="tno-field-wrap tno-field-full">
                                <label>شماره تماس گیرنده</label>
                                <input type="tel" name="billing_phone" class="tno-input" value="<?php echo esc_attr( WC()->customer->get_billing_phone() ); ?>" placeholder="09128874627" dir="ltr">
                            </div>
                        </div>
                    </div>

                    <!-- ۲. ارسال به آدرس انتخاب شده -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">ارسال به آدرس انتخاب شده</h3>
                        </div>

                        <div class="tno-address-options-list">
                            <label class="tno-radio-card is-selected">
                                <input type="radio" name="tno_selected_address" value="default" checked>
                                <span class="tno-radio-custom"></span>
                                <span class="tno-address-text">تهران، خیابان ولیعصر، منطقه ۱۲، بلوار کاوه، کوچه ابوذر، پلاک ۱۵</span>
                            </label>
                        </div>
                    </div>

                    <!-- ۳. افزودن آدرس جدید -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">افزودن آدرس جدید</h3>
                        </div>

                        <div class="tno-form-grid-2">
                            <div class="tno-field-wrap">
                                <label>استان</label>
                                <select name="billing_state" class="tno-select">
                                    <option value="">انتخاب کنید</option>
                                    <option value="THR">تهران</option>
                                </select>
                            </div>
                            <div class="tno-field-wrap">
                                <label>شهر</label>
                                <select name="billing_city" class="tno-select">
                                    <option value="">انتخاب کنید</option>
                                    <option value="TEH">تهران</option>
                                </select>
                            </div>
                            <div class="tno-field-wrap">
                                <label>کدپستی</label>
                                <input type="text" name="billing_postcode" class="tno-input" placeholder="۳۳۱۲۵۶۳۷۴۹۰" dir="ltr">
                            </div>
                            <div class="tno-field-wrap tno-field-full">
                                <label>نشانی پستی</label>
                                <input type="text" name="billing_address_1" class="tno-input" placeholder="مثال: تهران، خیابان ولیعصر، منطقه ۱۲، بلوار کاوه، کوچه ابوذر، پلاک ۱۵">
                            </div>
                        </div>
                    </div>

                    <!-- ۴. انتخاب روش ارسال -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">انتخاب روش ارسال</h3>
                        </div>

                        <div class="tno-shipping-methods-row">
                            <label class="tno-shipping-card">
                                <input type="radio" name="shipping_method[0]" value="flat_rate">
                                <span class="tno-shipping-text">ارسال نرمال</span>
                            </label>
                            <label class="tno-shipping-card is-selected">
                                <input type="radio" name="shipping_method[0]" value="express" checked>
                                <span class="tno-shipping-text">ارسال اکسپرس</span>
                            </label>
                            <label class="tno-shipping-card">
                                <input type="radio" name="shipping_method[0]" value="tipax">
                                <span class="tno-shipping-text">تیپاکس</span>
                            </label>
                        </div>
                    </div>

                    <!-- ۵. زمان ارسال -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">زمان ارسال</h3>
                        </div>

                        <div class="tno-date-chips-scroll">
                            <?php 
                            $dates = ['پنجشنبه ۲۲ مهر', 'جمعه ۲۳ مهر', 'شنبه ۲۴ مهر', 'یکشنبه ۲۵ مهر', 'دوشنبه ۲۶ مهر', 'سه شنبه ۲۷ مهر'];
                            foreach($dates as $i => $d): 
                            ?>
                                <label class="tno-chip-date <?php echo $i === 1 ? 'is-selected' : ''; ?>">
                                    <input type="radio" name="tno_delivery_date" value="<?php echo esc_attr($d); ?>" <?php echo $i === 1 ? 'checked' : ''; ?>>
                                    <span><?php echo esc_html($d); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="tno-time-slots-list">
                            <?php 
                            $times = ['ساعت ۹ تا ۱۱', 'ساعت ۱۱ تا ۱۳', 'ساعت ۱۳ تا ۱۵'];
                            foreach($times as $j => $t): 
                            ?>
                                <label class="tno-radio-row <?php echo $j === 0 ? 'is-selected' : ''; ?>">
                                    <input type="radio" name="tno_delivery_time" value="<?php echo esc_attr($t); ?>" <?php echo $j === 0 ? 'checked' : ''; ?>>
                                    <span class="tno-radio-custom"></span>
                                    <span><?php echo esc_html($t); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ۶. توضیحات تکمیلی -->
                    <div class="tno-checkout-section-box">
                        <div class="tno-section-box-header">
                            <span class="tno-red-dot"></span>
                            <h3 class="tno-section-title">توضیحات تکمیلی</h3>
                        </div>
                        <textarea name="order_comments" class="tno-textarea" placeholder="در صورت تمایل، توضیحات یا درخواست‌های ویژه مربوط به سفارش خود را وارد کنید (اختیاری)"></textarea>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                <?php endif; ?>
            </div>

            <!-- ستون چپ: باکس مجموع قابل پرداخت و محصولات سبد خرید -->
            <div class="tno-checkout-summary-col">
                <div class="tno-summary-box">
                    <div class="tno-summary-title">مجموع قابل پرداخت</div>

                    <!-- لیست مینی اقلام در خلاصه سفارش -->
                    <div class="tno-summary-mini-items">
                        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            if ( $_product && $_product->exists() ) :
                        ?>
                            <div class="tno-summary-item-row">
                                <span class="tno-sum-item-name"><?php echo esc_html( $_product->get_name() ); ?></span>
                                <span class="tno-sum-item-price"><strong><?php echo esc_html( tno_fa_num( number_format( floatval( $_product->get_price() * $cart_item['quantity'] ) ) ) ); ?></strong> <small>تومان</small></span>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>

                    <div class="tno-summary-divider"></div>

                    <div class="tno-summary-row">
                        <span>قیمت کالاها (<?php echo esc_html( tno_fa_num( WC()->cart->get_cart_contents_count() ) ); ?> کالا)</span>
                        <span><strong><?php echo esc_html( tno_fa_num( number_format( floatval( WC()->cart->get_subtotal() ) ) ) ); ?></strong> <small>تومان</small></span>
                    </div>

                    <div class="tno-summary-row">
                        <span>نحوه ارسال</span>
                        <span class="tno-express-tag">ارسال اکسپرس</span>
                    </div>

                    <?php if ( WC()->cart->get_discount_total() > 0 ) : ?>
                        <div class="tno-summary-row">
                            <span>مجموع تخفیف کالاها (۵۰٪)</span>
                            <span style="color: #E81327;"><strong><?php echo esc_html( tno_fa_num( number_format( floatval( WC()->cart->get_discount_total() ) ) ) ); ?></strong> <small>تومان</small></span>
                        </div>
                    <?php endif; ?>

                    <div class="tno-summary-divider"></div>

                    <div class="tno-summary-row tno-payable-row">
                        <span>مبلغ قابل پرداخت</span>
                        <div class="tno-payable-final-val">
                            <strong><?php echo esc_html( tno_fa_num( number_format( floatval( WC()->cart->get_total( 'raw' ) ) ) ) ); ?></strong>
                            <small>تومان</small>
                        </div>
                    </div>

                    <!-- دکمه ثبت سفارش ووکامرس -->
                    <button type="submit" class="tno-proceed-checkout-btn" name="woocommerce_checkout_place_order" id="place_order" value="پرداخت و خرید محصول">
                        پرداخت و خرید محصول
                    </button>
                </div>
            </div>

        </div>

    </form>

    <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</main>