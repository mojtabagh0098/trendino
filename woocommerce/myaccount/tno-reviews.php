<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id    = get_current_user_id();
$bundle     = tno_account_get_reviews_bundle( $user_id );
$pending    = $bundle['pending'];
$reviews    = $bundle['reviews'];
$questions  = $bundle['questions'];
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'pending';

if ( ! in_array( $active_tab, [ 'pending', 'reviews', 'questions' ], true ) ) {
	$active_tab = 'pending';
}

$dashboard_url = wc_get_account_endpoint_url( 'dashboard' );
?>

<div class="tno-acc-head tno-acc-head--mobile-bar">
	<a href="<?php echo esc_url( $dashboard_url ); ?>" class="tno-acc-back-mobile" aria-label="بازگشت">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1>دیدگاه ها و پرسش ها</h1>
</div>

<section class="tno-acc-card tno-rev-shell" id="tno-reviews-app" data-active-tab="<?php echo esc_attr( $active_tab ); ?>">
	<!-- Filter Pill Tabs -->
	<div class="tno-rev-tabs" role="tablist" aria-label="بخش‌های دیدگاه و پرسش">
		<button
			type="button"
			class="tno-rev-tab <?php echo $active_tab === 'pending' ? 'is-active' : ''; ?>"
			data-rev-tab="pending"
			role="tab"
			aria-selected="<?php echo $active_tab === 'pending' ? 'true' : 'false'; ?>"
		>
			در انتظار دیدگاه
		</button>
		<button
			type="button"
			class="tno-rev-tab <?php echo $active_tab === 'reviews' ? 'is-active' : ''; ?>"
			data-rev-tab="reviews"
			role="tab"
			aria-selected="<?php echo $active_tab === 'reviews' ? 'true' : 'false'; ?>"
		>
			دیدگاه‌های من
		</button>
		<button
			type="button"
			class="tno-rev-tab <?php echo $active_tab === 'questions' ? 'is-active' : ''; ?>"
			data-rev-tab="questions"
			role="tab"
			aria-selected="<?php echo $active_tab === 'questions' ? 'true' : 'false'; ?>"
		>
			پرسش‌های من
		</button>
	</div>

	<!-- Panel 1: در انتظار دیدگاه -->
	<div class="tno-rev-panel" data-rev-panel="pending" <?php echo $active_tab === 'pending' ? '' : 'hidden'; ?>>
		<?php if ( empty( $pending ) ) : ?>
			<div class="tno-rev-empty" id="tno-rev-pending-empty">
				<p>محصولی در انتظار ثبت دیدگاه وجود ندارد.</p>
			</div>
		<?php else : ?>
			<div class="tno-rev-pending-grid" id="tno-rev-pending-grid">
				<?php foreach ( $pending as $item ) : ?>
					<article class="tno-rev-pending-card" data-pending-product-id="<?php echo esc_attr( (string) $item['product_id'] ); ?>">
						<div class="tno-rev-card-prod">
							<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="tno-rev-card-thumb">
								<img src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy">
							</a>
							<h3 class="tno-rev-card-title">
								<a href="<?php echo esc_url( $item['permalink'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
							</h3>
						</div>

						<div class="tno-rev-card-rate-row">
							<span class="tno-rev-card-rate-lbl">امتیاز دهید به این محصول</span>
							<div class="tno-rev-card-stars" data-card-stars="1" role="group" aria-label="امتیاز">
								<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
									<button
										type="button"
										class="tno-rev-card-star"
										data-star="<?php echo esc_attr( (string) $s ); ?>"
										data-open-review="1"
										data-product-id="<?php echo esc_attr( (string) $item['product_id'] ); ?>"
										data-order-id="<?php echo esc_attr( (string) $item['order_id'] ); ?>"
										data-product-title="<?php echo esc_attr( $item['title'] ); ?>"
										data-product-image="<?php echo esc_url( $item['image_url'] ); ?>"
										data-initial-rating="<?php echo esc_attr( (string) $s ); ?>"
										aria-label="<?php echo esc_attr( (string) $s ); ?> ستاره"
									>
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
									</button>
								<?php endfor; ?>
							</div>
						</div>

						<button
							type="button"
							class="tno-acc-dark-btn tno-rev-card-btn"
							data-open-review="1"
							data-product-id="<?php echo esc_attr( (string) $item['product_id'] ); ?>"
							data-order-id="<?php echo esc_attr( (string) $item['order_id'] ); ?>"
							data-product-title="<?php echo esc_attr( $item['title'] ); ?>"
							data-product-image="<?php echo esc_url( $item['image_url'] ); ?>"
						>
							ثبت دیدگاه
						</button>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="tno-rev-empty" id="tno-rev-pending-empty" hidden>
				<p>محصولی در انتظار ثبت دیدگاه وجود ندارد.</p>
			</div>
		<?php endif; ?>
	</div>

	<!-- Panel 2: دیدگاه‌های من -->
	<div class="tno-rev-panel" data-rev-panel="reviews" <?php echo $active_tab === 'reviews' ? '' : 'hidden'; ?>>
		<?php if ( empty( $reviews ) ) : ?>
			<div class="tno-rev-empty" id="tno-rev-my-reviews-empty">
				<p>هنوز دیدگاهی ثبت نکرده‌اید.</p>
			</div>
			<div class="tno-rev-history-grid" id="tno-rev-my-reviews-grid" hidden></div>
		<?php else : ?>
			<div class="tno-rev-history-grid" id="tno-rev-my-reviews-grid">
				<?php foreach ( $reviews as $rev ) : ?>
					<article class="tno-rev-history-card">
						<div class="tno-rev-card-prod">
							<a href="<?php echo esc_url( $rev['product_url'] ); ?>" class="tno-rev-card-thumb">
								<img src="<?php echo esc_url( $rev['product_image'] ); ?>" alt="<?php echo esc_attr( $rev['product_title'] ); ?>" loading="lazy">
							</a>
							<div class="tno-rev-card-head-info">
								<h3 class="tno-rev-card-title">
									<a href="<?php echo esc_url( $rev['product_url'] ); ?>"><?php echo esc_html( $rev['product_title'] ); ?></a>
								</h3>
								<?php if ( $rev['rating'] > 0 ) : ?>
									<div class="tno-rev-readonly-stars" aria-label="<?php echo esc_attr( tno_fa_digits( (string) $rev['rating'] ) ); ?> از ۵ ستاره">
										<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
											<svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $s <= $rev['rating'] ? '#f5b942' : 'none'; ?>" stroke="#f5b942" stroke-width="1.8" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
										<?php endfor; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<p class="tno-rev-history-text"><?php echo esc_html( $rev['content'] ); ?></p>

						<footer class="tno-rev-history-foot">
							<span class="tno-rev-history-date"><?php echo esc_html( $rev['date_label'] ); ?></span>
							<span class="tno-status-badge tno-status-badge--<?php echo esc_attr( $rev['status_tone'] ); ?>">
								<?php echo esc_html( $rev['status_label'] ); ?>
							</span>
						</footer>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<!-- Panel 3: پرسش‌های من -->
	<div class="tno-rev-panel" data-rev-panel="questions" <?php echo $active_tab === 'questions' ? '' : 'hidden'; ?>>
		<?php if ( empty( $questions ) ) : ?>
			<div class="tno-rev-empty">
				<p>هنوز پرسشی ثبت نکرده‌اید.</p>
			</div>
		<?php else : ?>
			<div class="tno-rev-history-grid">
				<?php foreach ( $questions as $q ) : ?>
					<article class="tno-rev-history-card">
						<div class="tno-rev-card-prod">
							<a href="<?php echo esc_url( $q['product_url'] ); ?>" class="tno-rev-card-thumb">
								<img src="<?php echo esc_url( $q['product_image'] ); ?>" alt="<?php echo esc_attr( $q['product_title'] ); ?>" loading="lazy">
							</a>
							<h3 class="tno-rev-card-title">
								<a href="<?php echo esc_url( $q['product_url'] ); ?>"><?php echo esc_html( $q['product_title'] ); ?></a>
							</h3>
						</div>

						<p class="tno-rev-history-text"><?php echo esc_html( $q['content'] ); ?></p>

						<?php if ( ! empty( $q['answer'] ) ) : ?>
							<div class="tno-rev-answer-box">
								<strong>پاسخ فروشگاه:</strong>
								<span><?php echo esc_html( $q['answer'] ); ?></span>
							</div>
						<?php endif; ?>

						<footer class="tno-rev-history-foot">
							<span class="tno-rev-history-date"><?php echo esc_html( $q['date_label'] ); ?></span>
							<span class="tno-status-badge tno-status-badge--<?php echo esc_attr( $q['status_tone'] ); ?>">
								<?php echo esc_html( $q['status_label'] ); ?>
							</span>
						</footer>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
