<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $order instanceof WC_Order ) {
	return;
}

$orders_url    = wc_get_account_endpoint_url( 'orders' );
$receiver_name = trim( $order->get_formatted_shipping_full_name() ?: $order->get_formatted_billing_full_name() );
$phone         = $order->get_billing_phone();

$states        = WC()->countries->get_states( 'IR' ) ?: [];
$state_code    = $order->get_shipping_state() ?: $order->get_billing_state();
$state_name    = $states[ $state_code ] ?? $state_code;
$city          = $order->get_shipping_city() ?: $order->get_billing_city();
$addr1         = $order->get_shipping_address_1() ?: $order->get_billing_address_1();
$full_address  = implode( '، ', array_filter( [ $state_name, $city, $addr1 ] ) );

$order_total   = tno_format_toman( (float) $order->get_total() );
$is_paid       = $order->is_paid() || in_array( $order->get_status(), [ 'processing', 'shipped', 'completed' ], true );
$pay_label     = $is_paid ? 'موفق' : ( $order->has_status( 'failed' ) ? 'ناموفق' : 'در انتظار پرداخت' );
$pay_tone      = $is_paid ? 'green' : 'red';

$shipping_name = $order->get_shipping_method() ?: 'ارسال عادی';
$shipping_cost = (float) $order->get_shipping_total() + (float) $order->get_shipping_tax();
$date_label    = tno_account_get_order_date_label( $order );
$status_meta   = tno_account_get_status_meta( $order->get_status() );
$order_num     = tno_fa_digits( (string) $order->get_order_number() );

$invoice_url_tpl = function_exists( 'tno_get_checkout_option' )
	? trim( (string) tno_get_checkout_option( 'invoice_btn_url', '' ) )
	: '';
$invoice_url     = $invoice_url_tpl !== ''
	? str_replace( '{order_id}', (string) $order->get_id(), $invoice_url_tpl )
	: '#print-invoice';

$terms_url  = function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'terms' ) > 0
	? get_permalink( wc_get_page_id( 'terms' ) )
	: '#';
$brand_name = get_bloginfo( 'name' ) ?: 'ترندینو';
?>

<div class="tno-acc-head tno-acc-head--back">
	<a href="<?php echo esc_url( $orders_url ); ?>" class="tno-acc-back-link" aria-label="بازگشت به سفارش‌ها">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1>جزئیات سفارش</h1>
</div>

<section class="tno-acc-card tno-order-detail-card">
	<!-- Section 1: Receiver & Address -->
	<div class="tno-detail-section">
		<div class="tno-detail-row">
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">تحویل گیرنده :</span>
				<strong><?php echo esc_html( $receiver_name ?: '—' ); ?></strong>
			</span>
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">شماره تماس :</span>
				<strong dir="ltr"><?php echo esc_html( tno_fa_digits( $phone ?: '—' ) ); ?></strong>
			</span>
		</div>
		<div class="tno-detail-row">
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">آدرس ارسال مرسوله :</span>
				<strong><?php echo esc_html( $full_address ?: '—' ); ?></strong>
			</span>
		</div>
	</div>

	<!-- Section 2: Payment & Shipping Meta -->
	<div class="tno-detail-section">
		<div class="tno-detail-row">
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">مبلغ کل :</span>
				<strong><?php echo esc_html( $order_total ); ?> تومان</strong>
			</span>
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">وضعیت پرداخت :</span>
				<strong class="tno-pay-status tno-pay-status--<?php echo esc_attr( $pay_tone ); ?>"><?php echo esc_html( $pay_label ); ?></strong>
			</span>
		</div>

		<div class="tno-detail-row">
			<span class="tno-detail-pair tno-detail-ship-method">
				<svg class="tno-ship-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
				<strong><?php echo esc_html( $shipping_name ); ?></strong>
			</span>
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">هزینه بسته بندی و ارسال :</span>
				<strong><?php echo $shipping_cost > 0 ? esc_html( tno_format_toman( $shipping_cost ) . ' تومان' ) : 'رایگان'; ?></strong>
			</span>
			<span class="tno-detail-pair">
				<span class="tno-muted-lbl">تاریخ تحویل سفارش :</span>
				<strong><?php echo esc_html( $date_label ); ?></strong>
			</span>
		</div>
	</div>

	<!-- Section 3: Status, Invoice & Line Items -->
	<div class="tno-detail-section tno-detail-section--items">
		<div class="tno-detail-items-head">
			<div class="tno-ord-head-right">
				<span class="tno-status-badge tno-status-badge--<?php echo esc_attr( $status_meta['tone'] ); ?>">
					<?php echo esc_html( $status_meta['label'] ); ?>
				</span>
				<span class="tno-ord-head-meta">
					<span class="tno-muted-lbl">کد سفارش :</span>
					<span dir="ltr"><?php echo esc_html( $order_num ); ?></span>
				</span>
				<span class="tno-dot-sep" aria-hidden="true">•</span>
				<span class="tno-ord-head-meta">
					<span class="tno-muted-lbl">تاریخ تحویل سفارش :</span>
					<strong><?php echo esc_html( $date_label ); ?></strong>
				</span>
			</div>

			<a
				href="<?php echo esc_url( $invoice_url ); ?>"
				class="tno-ord-detail-btn"
				<?php echo $invoice_url === '#print-invoice' ? 'data-action="print-invoice"' : ''; ?>
			>
				دریافت فاکتور سفارش
			</a>
		</div>

		<div class="tno-detail-items-list">
			<?php
			foreach ( $order->get_items() as $item ) {
				if ( $item instanceof WC_Order_Item_Product ) {
					tno_account_render_order_item_row( $item, $order, true );
				}
			}
			?>
		</div>
	</div>
</section>