<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bundle        = tno_account_get_customer_orders_bundle( get_current_user_id() );
$orders        = $bundle['orders'];
$counts        = $bundle['counts'];
$active_filter = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'all';

if ( ! in_array( $active_filter, [ 'all', 'processing', 'shipped', 'completed' ], true ) ) {
	$active_filter = 'all';
}

$dashboard_url = wc_get_account_endpoint_url( 'dashboard' );
?>

<div class="tno-acc-head tno-acc-head--mobile-bar">
	<a href="<?php echo esc_url( $dashboard_url ); ?>" class="tno-acc-back-mobile" aria-label="بازگشت">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1>سفارش های من</h1>
	<span class="tno-acc-mobile-total"><?php echo esc_html( tno_fa_digits( (string) $counts['all'] ) ); ?> سفارش</span>
</div>

<div id="tno-orders-app" data-active-filter="<?php echo esc_attr( $active_filter ); ?>">
	<!-- Filter Pills Box -->
	<section class="tno-acc-card tno-orders-filter-card">
		<h2 class="tno-acc-card-title tno-desktop-only">سفارش های من</h2>

		<div class="tno-stat-grid tno-stat-grid--filter" role="tablist">
			<button type="button" class="tno-stat-pill <?php echo $active_filter === 'all' ? 'is-active' : ''; ?>" data-order-filter="all" role="tab" aria-selected="<?php echo $active_filter === 'all' ? 'true' : 'false'; ?>">
				<span class="tno-stat-right">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
					<span class="tno-stat-label">سفارشات</span>
				</span>
				<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['all'] ) ); ?> سفارش</strong>
			</button>

			<button type="button" class="tno-stat-pill <?php echo $active_filter === 'processing' ? 'is-active' : ''; ?>" data-order-filter="processing" role="tab" aria-selected="<?php echo $active_filter === 'processing' ? 'true' : 'false'; ?>">
				<span class="tno-stat-right">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>
					<span class="tno-stat-label">در حال پردازش</span>
				</span>
				<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['processing'] ) ); ?> سفارش</strong>
			</button>

			<button type="button" class="tno-stat-pill <?php echo $active_filter === 'shipped' ? 'is-active' : ''; ?>" data-order-filter="shipped" role="tab" aria-selected="<?php echo $active_filter === 'shipped' ? 'true' : 'false'; ?>">
				<span class="tno-stat-right">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
					<span class="tno-stat-label">ارسال شده</span>
				</span>
				<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['shipped'] ) ); ?> سفارش</strong>
			</button>

			<button type="button" class="tno-stat-pill <?php echo $active_filter === 'completed' ? 'is-active' : ''; ?>" data-order-filter="completed" role="tab" aria-selected="<?php echo $active_filter === 'completed' ? 'true' : 'false'; ?>">
				<span class="tno-stat-right">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>
					<span class="tno-stat-label">تحویل شده</span>
				</span>
				<strong class="tno-stat-count"><?php echo esc_html( tno_fa_digits( (string) $counts['completed'] ) ); ?> سفارش</strong>
			</button>
		</div>
	</section>

	<!-- Orders Accordion Stack -->
	<div class="tno-orders-list" id="tno-orders-list">
		<?php if ( ! empty( $orders ) ) : ?>
			<?php foreach ( $orders as $idx => $order ) :
				$status_meta = tno_account_get_status_meta( $order->get_status() );
				$is_visible  = ( $active_filter === 'all' || $status_meta['bucket'] === $active_filter );
				$is_open     = ( $idx < 2 );
				$order_num   = tno_fa_digits( (string) $order->get_order_number() );
				$date_label  = tno_account_get_order_date_label( $order );
				$view_url    = $order->get_view_order_url();
				?>
				<article
					class="tno-ord-card <?php echo $is_open ? 'is-open' : ''; ?>"
					data-order-bucket="<?php echo esc_attr( $status_meta['bucket'] ); ?>"
					<?php echo $is_visible ? '' : 'hidden'; ?>
				>
					<header class="tno-ord-card-head">
						<div class="tno-ord-head-right">
							<span class="tno-status-badge tno-status-badge--<?php echo esc_attr( $status_meta['tone'] ); ?>">
								<?php echo esc_html( $status_meta['label'] ); ?>
							</span>

							<div class="tno-ord-head-metas">
								<span class="tno-ord-head-meta">
									<span class="tno-muted-lbl">کد سفارش :</span>
									<span><?php echo esc_html( $order_num ); ?></span>
								</span>
								<span class="tno-ord-head-meta">
									<span class="tno-muted-lbl">تاریخ تحویل :</span>
									<span><?php echo esc_html( $date_label ); ?></span>
								</span>
							</div>
						</div>

						<div class="tno-ord-head-left">
							<a href="<?php echo esc_url( $view_url ); ?>" class="tno-ord-detail-btn tno-desktop-only">جزئیات سفارش</a>
							<button type="button" class="tno-ord-toggle-btn" data-toggle-order="1" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-label="باز و بسته کردن اقلام سفارش">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
							</button>
						</div>
					</header>

					<div class="tno-ord-card-body" <?php echo $is_open ? '' : 'hidden'; ?>>
						<?php
						foreach ( $order->get_items() as $item ) {
							if ( $item instanceof WC_Order_Item_Product ) {
								tno_account_render_order_item_row( $item, $order, false );
							}
						}
						?>
					</div>

					<!-- Mobile Meta & Full-Width Detail Button -->
					<div class="tno-ord-mobile-foot">
						<div class="tno-ord-mobile-metas">
							<span><span class="tno-muted-lbl">کد سفارش :</span> <?php echo esc_html( $order_num ); ?></span>
							<span><span class="tno-muted-lbl">تاریخ تحویل :</span> <?php echo esc_html( $date_label ); ?></span>
						</div>
						<a href="<?php echo esc_url( $view_url ); ?>" class="tno-ord-detail-btn tno-ord-detail-btn--full">جزئیات سفارش</a>
					</div>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="tno-acc-card tno-acc-empty-card" id="tno-orders-empty" <?php echo ! empty( $orders ) ? 'hidden' : ''; ?>>
			<p class="tno-acc-empty-title">سفارشی در این بخش یافت نشد</p>
			<p class="tno-acc-empty-sub">هنوز سفارشی با این وضعیت برای حساب کاربری شما ثبت نشده است.</p>
		</div>
	</div>
</div>