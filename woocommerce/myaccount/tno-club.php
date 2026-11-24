<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
tno_club_sync_user_state( $user_id );

$opts    = tno_get_club_settings();
$balance = tno_club_get_user_points( $user_id );
$pending = tno_club_get_user_pending_points( $user_id );
$ledger  = tno_club_get_user_ledger( $user_id );
$coupons = tno_club_get_user_coupons( $user_id );

$tiers = array_values( array_filter(
	is_array( $opts['tiers'] ?? null ) ? $opts['tiers'] : [],
	fn( $t ): bool => is_array( $t ) && ! empty( $t['enabled'] )
) );

$rules_raw   = (string) ( $opts['rules_lines'] ?? '' );
$rules_lines = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $rules_raw ) ?: [] ) ) );

$dashboard_url = wc_get_account_endpoint_url( 'dashboard' );
$club_enabled  = ! empty( $opts['enabled'] );
?>

<div class="tno-acc-head tno-acc-head--mobile-bar">
	<a href="<?php echo esc_url( $dashboard_url ); ?>" class="tno-acc-back-mobile" aria-label="بازگشت">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1><?php echo esc_html( (string) $opts['page_title'] ); ?></h1>
</div>

<div class="tno-club-shell" id="tno-club-app">
	<!-- 1. Points Balance Banner -->
	<section class="tno-club-balance-banner" aria-label="موجودی امتیاز">
		<div class="tno-club-balance-copy">
			<h2 class="tno-club-balance-title"><?php echo esc_html( (string) $opts['balance_title'] ); ?></h2>
			<div class="tno-club-balance-meta">
				<strong class="tno-club-balance-value" id="tno-club-balance-value">
					<?php echo esc_html( tno_fa_digits( number_format( $balance ) ) . ' امتیاز' ); ?>
				</strong>
				<?php if ( $pending > 0 ) : ?>
					<span class="tno-club-pending-note">
						(<?php echo esc_html( tno_fa_digits( number_format( $pending ) ) ); ?> امتیاز در انتظار پایان مهلت مرجوعی)
					</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="tno-club-balance-icon" aria-hidden="true">
			<svg width="34" height="34" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="6" y="15" width="24" height="16" rx="2.5" stroke="#E63946" stroke-width="2.2"/>
				<rect x="4.5" y="10.5" width="27" height="5" rx="1.5" stroke="#E63946" stroke-width="2.2"/>
				<path d="M18 10.5V31" stroke="#E63946" stroke-width="2.2"/>
				<path d="M18 10.5C18 10.5 14.5 5 11.5 5.5C9.2 5.9 9 8.5 11 9.8C12.8 10.9 18 10.5 18 10.5Z" stroke="#E63946" stroke-width="2.1" stroke-linejoin="round"/>
				<path d="M18 10.5C18 10.5 21.5 5 24.5 5.5C26.8 5.9 27 8.5 25 9.8C23.2 10.9 18 10.5 18 10.5Z" stroke="#E63946" stroke-width="2.1" stroke-linejoin="round"/>
			</svg>
		</div>
	</section>

	<div id="tno-club-feedback" class="tno-club-feedback" hidden role="status"></div>

	<!-- 2. Discount Tiers Grid -->
	<section class="tno-club-card">
		<h2 class="tno-acc-card-title"><?php echo esc_html( (string) $opts['offers_title'] ); ?></h2>

		<?php if ( empty( $tiers ) ) : ?>
			<p class="tno-club-empty-text">در حال حاضر بسته تخفیفی فعالی تعریف نشده است.</p>
		<?php else : ?>
			<div class="tno-club-tiers-grid">
				<?php foreach ( $tiers as $tier ) :
					$tier_id      = sanitize_key( (string) ( $tier['id'] ?? '' ) );
					$cost         = max( 1, (int) ( $tier['points_cost'] ?? 0 ) );
					$can_claim    = $club_enabled && ( $balance >= $cost );
					$theme        = in_array( (string) ( $tier['theme'] ?? 'dark' ), [ 'dark', 'blue', 'pink' ], true )
						? (string) $tier['theme']
						: 'dark';
					$custom_image = trim( (string) ( $tier['custom_image'] ?? '' ) );
					$cost_label   = tno_fa_digits( number_format( $cost ) ) . ' امتیاز';
					?>
					<article class="tno-club-tier" data-tier-id="<?php echo esc_attr( $tier_id ); ?>" data-tier-cost="<?php echo esc_attr( (string) $cost ); ?>">
						<div class="tno-club-tier-banner tno-club-tier-banner--<?php echo esc_attr( $theme ); ?>">
							<?php if ( $custom_image !== '' ) : ?>
								<img src="<?php echo esc_url( $custom_image ); ?>" alt="<?php echo esc_attr( (string) $tier['title'] ); ?>" class="tno-club-tier-custom-img" loading="lazy">
							<?php else : ?>
								<span class="tno-club-tier-banner-pts"><?php echo esc_html( $cost_label ); ?></span>
								<div class="tno-club-tier-art" aria-hidden="true">
									<svg viewBox="0 0 132 104" fill="none" xmlns="http://www.w3.org/2000/svg">
										<defs>
											<linearGradient id="tnoTicketGrad-<?php echo esc_attr( $tier_id ); ?>" x1="0" y1="0" x2="1" y2="1">
												<stop offset="0%" stop-color="#FF3B4E"/>
												<stop offset="100%" stop-color="#B90F22"/>
											</linearGradient>
											<linearGradient id="tnoBoxGrad-<?php echo esc_attr( $tier_id ); ?>" x1="0" y1="0" x2="1" y2="1">
												<stop offset="0%" stop-color="#203764"/>
												<stop offset="100%" stop-color="#0D1933"/>
											</linearGradient>
										</defs>
										<!-- Ambient Glow -->
										<ellipse cx="66" cy="86" rx="46" ry="9" fill="rgba(0,0,0,0.22)"/>
										<!-- Coupon Ticket -->
										<g transform="translate(52 14) rotate(8)">
											<path d="M6 0H58C61.3 0 64 2.7 64 6V20C60.7 20 58 22.7 58 26C58 29.3 60.7 32 64 32V48C64 51.3 61.3 54 58 54H6C2.7 54 0 51.3 0 48V32C3.3 32 6 29.3 6 26C6 22.7 3.3 20 0 20V6C0 2.7 2.7 0 6 0Z" fill="url(#tnoTicketGrad-<?php echo esc_attr( $tier_id ); ?>)"/>
											<line x1="16" y1="6" x2="16" y2="48" stroke="rgba(255,255,255,0.35)" stroke-width="1.5" stroke-dasharray="3 3"/>
											<!-- % Symbol -->
											<circle cx="33" cy="20" r="4.5" stroke="#FFFFFF" stroke-width="2.6"/>
											<circle cx="47" cy="34" r="4.5" stroke="#FFFFFF" stroke-width="2.6"/>
											<line x1="48" y1="16" x2="32" y2="38" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round"/>
										</g>
										<!-- Gift Box -->
										<g transform="translate(14 40)">
											<rect x="4" y="13" width="44" height="33" rx="4" fill="url(#tnoBoxGrad-<?php echo esc_attr( $tier_id ); ?>)"/>
											<rect x="22" y="13" width="8" height="33" fill="#E63946"/>
											<rect x="1" y="7" width="50" height="9" rx="3" fill="#2A4478"/>
											<rect x="22" y="7" width="8" height="9" fill="#FF4757"/>
											<path d="M26 7C26 7 18 -1 13 2C10 3.8 12 7 17 7H26Z" fill="#FF4757"/>
											<path d="M26 7C26 7 34 -1 39 2C42 3.8 40 7 35 7H26Z" fill="#E63946"/>
										</g>
										<!-- Sparkle Stars -->
										<path d="M24 18L25.8 22.2L30 24L25.8 25.8L24 30L22.2 25.8L18 24L22.2 22.2L24 18Z" fill="#FFFFFF" fill-opacity="0.85"/>
										<path d="M114 70L115.3 73.2L118.5 74.5L115.3 75.8L114 79L112.7 75.8L109.5 74.5L112.7 73.2L114 70Z" fill="#FFFFFF" fill-opacity="0.8"/>
										<circle cx="46" cy="24" r="2" fill="#FFFFFF" fill-opacity="0.6"/>
									</svg>
								</div>
							<?php endif; ?>
						</div>

						<div class="tno-club-tier-body">
							<span class="tno-club-tier-sub"><?php echo esc_html( (string) $tier['title'] ); ?></span>
							<strong class="tno-club-tier-cost"><?php echo esc_html( $cost_label ); ?></strong>
						</div>

						<button
							type="button"
							class="tno-club-tier-btn <?php echo $can_claim ? 'is-available' : ''; ?>"
							<?php echo $can_claim ? '' : 'disabled'; ?>
							data-claim-tier="<?php echo esc_attr( $tier_id ); ?>"
						>
							<?php echo $can_claim ? 'دریافت کد تخفیف' : 'امتیاز کافی ندارید'; ?>
						</button>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<!-- 3. Rules Card -->
	<section class="tno-club-card">
		<h2 class="tno-acc-card-title"><?php echo esc_html( (string) $opts['rules_title'] ); ?></h2>
		<?php if ( ! empty( $rules_lines ) ) : ?>
			<ol class="tno-club-rules-list">
				<?php foreach ( $rules_lines as $idx => $line ) : ?>
					<li>
						<span class="tno-club-rule-num"><?php echo esc_html( tno_fa_digits( (string) ( $idx + 1 ) ) ); ?>.</span>
						<span><?php echo esc_html( $line ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</section>

	<!-- 4. Points Ledger Card -->
	<section class="tno-club-card">
		<h2 class="tno-acc-card-title"><?php echo esc_html( (string) $opts['ledger_title'] ); ?></h2>

		<div class="tno-club-ledger-list" id="tno-club-ledger-list">
			<?php if ( empty( $ledger ) ) : ?>
				<p class="tno-club-empty-text" id="tno-club-ledger-empty"><?php echo esc_html( (string) $opts['empty_ledger_text'] ); ?></p>
			<?php else : ?>
				<?php foreach ( $ledger as $idx => $entry ) :
					$pts    = (int) ( $entry['points'] ?? 0 );
					$status = (string) ( $entry['status'] ?? 'available' );
					$tone   = 'green';
					$suffix = '';

					if ( $status === 'cancelled' ) {
						$tone   = 'muted';
						$suffix = ' (لغو شده)';
					} elseif ( $status === 'pending' ) {
						$tone   = 'amber';
						$suffix = ' (در انتظار مهلت مرجوعی)';
					} elseif ( $pts < 0 ) {
						$tone = 'red';
					}

					$pts_label = ( $pts < 0 ? '-' : '' ) . tno_fa_digits( number_format( abs( $pts ) ) ) . ' امتیاز';
					?>
					<div class="tno-club-ledger-row">
						<span class="tno-club-ledger-desc">
							<span class="tno-club-ledger-num"><?php echo esc_html( tno_fa_digits( (string) ( $idx + 1 ) ) ); ?>.</span>
							<span><?php echo esc_html( (string) ( $entry['description'] ?? '' ) . $suffix ); ?></span>
						</span>
						<span class="tno-club-pts-pill tno-club-pts-pill--<?php echo esc_attr( $tone ); ?>">
							<?php echo esc_html( $pts_label ); ?>
						</span>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>

	<!-- 5. My Claimed Coupons Card -->
	<section class="tno-club-card">
		<h2 class="tno-acc-card-title"><?php echo esc_html( (string) $opts['coupons_title'] ); ?></h2>

		<div class="tno-club-coupons-list" id="tno-club-coupons-list">
			<?php if ( empty( $coupons ) ) : ?>
				<p class="tno-club-empty-text" id="tno-club-coupons-empty"><?php echo esc_html( (string) $opts['empty_coupons_text'] ); ?></p>
			<?php else : ?>
				<?php foreach ( $coupons as $c_item ) :
					$st_meta    = tno_club_get_coupon_status_meta( $c_item );
					$expires_at = (int) ( $c_item['expires_at'] ?? 0 );
					$exp_label  = $expires_at > 0 ? tno_club_format_jalali_date( $expires_at ) : '—';
					$code       = strtoupper( (string) ( $c_item['code'] ?? '' ) );
					?>
					<div class="tno-club-coupon-row">
						<div class="tno-club-coupon-info">
							<strong class="tno-club-coupon-title"><?php echo esc_html( (string) ( $c_item['title'] ?? '' ) ); ?></strong>
							<span class="tno-club-coupon-exp">اعتبار تا <?php echo esc_html( $exp_label ); ?></span>
						</div>
						<div class="tno-club-coupon-actions">
							<button type="button" class="tno-club-code-pill" data-copy-coupon="<?php echo esc_attr( $code ); ?>" title="کپی کد تخفیف">
								<span dir="ltr"><?php echo esc_html( $code ); ?></span>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
							</button>
							<span class="tno-club-pts-pill tno-club-pts-pill--<?php echo esc_attr( $st_meta['tone'] ); ?>">
								<?php echo esc_html( $st_meta['label'] ); ?>
							</span>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>
</div>
