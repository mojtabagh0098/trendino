<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id     = get_current_user_id();
$addresses   = tno_account_get_addresses( $user_id );
$iran_states = WC()->countries->get_states( 'IR' ) ?: [];
?>

<?php if ( empty( $addresses ) ) : ?>
	<div class="tno-acc-head">
		<h1>آدرس ها</h1>
	</div>

	<!-- Empty State -->
	<section class="tno-acc-card tno-acc-empty-card tno-addr-empty-card">
		<div class="tno-addr-empty-icon" aria-hidden="true">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
		</div>
		<h2 class="tno-acc-empty-title">تا به حال آدرسی ثبت نشده است !</h2>
		<p class="tno-acc-empty-sub">نخستین گام برای ثبت سفارش، وارد کردن آدرس تحویل مورد نظر شماست</p>
		<button type="button" class="tno-acc-dark-btn" data-open-address-modal="1">ثبت آدرس</button>
	</section>
<?php else : ?>
	<!-- Populated Addresses Header -->
	<div class="tno-acc-head tno-acc-head--split">
		<h1>لیست آدرس ها</h1>
		<button type="button" class="tno-addr-add-btn" data-open-address-modal="1">
			<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
			<span>افزودن آدرس جدید</span>
		</button>
	</div>

	<!-- Populated Addresses Single Card List -->
	<section class="tno-acc-card tno-addr-list-card">
		<?php foreach ( $addresses as $addr ) : ?>
			<?php
			$is_default   = ! empty( $addr['is_default'] );
			$state_name   = (string) ( $addr['state_name'] ?? ( $iran_states[ $addr['state'] ?? '' ] ?? ( $addr['state'] ?? '' ) ) );
			$addr_display = (string) ( $addr['display'] ?? '' );
			?>
			<article class="tno-addr-item<?php echo $is_default ? ' is-default' : ''; ?>">
				<!-- Row 1: Pin + Address Text | Default Toggle Pill -->
				<div class="tno-addr-item-top">
					<div class="tno-addr-item-location">
						<span class="tno-addr-pin-icon<?php echo $is_default ? ' is-default' : ''; ?>" aria-hidden="true">
							<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
						</span>
						<p class="tno-addr-text"><?php echo esc_html( tno_fa_digits( $addr_display ) ); ?></p>
					</div>

					<button
						type="button"
						class="tno-addr-default-btn<?php echo $is_default ? ' is-active' : ''; ?>"
						data-set-default-address="<?php echo esc_attr( (string) ( $addr['id'] ?? '' ) ); ?>"
						<?php echo $is_default ? 'aria-pressed="true"' : 'aria-pressed="false"'; ?>
					>
						<span class="tno-addr-default-circle" aria-hidden="true">
							<?php if ( $is_default ) : ?>
								<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
							<?php endif; ?>
						</span>
						<span>آدرس پیش فرض</span>
					</button>
				</div>

				<!-- Row 2: Metadata Chips | Edit Button -->
				<div class="tno-addr-item-bottom">
					<div class="tno-addr-meta-chips">
						<?php if ( ! empty( $addr['receiver'] ) ) : ?>
							<span class="tno-addr-chip">
								<span class="tno-addr-chip-lbl">گیرنده :</span>
								<span><?php echo esc_html( (string) $addr['receiver'] ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( $state_name !== '' ) : ?>
							<span class="tno-addr-chip">
								<span class="tno-addr-chip-lbl">استان :</span>
								<span><?php echo esc_html( $state_name ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( ! empty( $addr['city'] ) ) : ?>
							<span class="tno-addr-chip">
								<span class="tno-addr-chip-lbl">شهر :</span>
								<span><?php echo esc_html( (string) $addr['city'] ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( ! empty( $addr['phone'] ) ) : ?>
							<span class="tno-addr-chip">
								<span class="tno-addr-chip-lbl">شماره تماس :</span>
								<span dir="ltr"><?php echo esc_html( tno_fa_digits( (string) $addr['phone'] ) ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( ! empty( $addr['postcode'] ) ) : ?>
							<span class="tno-addr-chip">
								<span class="tno-addr-chip-lbl">کد پستی :</span>
								<span dir="ltr"><?php echo esc_html( tno_fa_digits( (string) $addr['postcode'] ) ); ?></span>
							</span>
						<?php endif; ?>
					</div>

					<button
						type="button"
						class="tno-addr-edit-btn"
						data-edit-address="<?php echo esc_attr( wp_json_encode( $addr ) ); ?>"
					>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
						<span>ویرایش</span>
					</button>
				</div>
			</article>
		<?php endforeach; ?>
	</section>
<?php endif; ?>

<!-- Add / Edit Address Modal -->
<div class="tno-acc-modal-backdrop" id="tno-address-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-addr-modal-title">
	<div class="tno-acc-modal tno-acc-modal--wide">
		<header class="tno-acc-modal-head">
			<h2 id="tno-addr-modal-title">افزودن آدرس جدید</h2>
			<button type="button" class="tno-acc-modal-close" data-close-modal="tno-address-modal" aria-label="بستن">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
			</button>
		</header>

		<form id="tno-address-form" class="tno-addr-form" novalidate>
			<input type="hidden" name="address_id" id="tno-addr-id" value="">

			<!-- Row 1: Province & Cascading City -->
			<div class="tno-addr-form-grid">
				<div class="tno-acc-field tno-addr-field">
					<div class="tno-addr-label-row">
						<label for="tno-addr-state">استان</label>
						<span class="tno-req-tag">* الزامی</span>
					</div>
					<select id="tno-addr-state" name="state" required>
						<option value="">انتخاب کنید</option>
						<?php foreach ( $iran_states as $code => $name ) : ?>
							<option value="<?php echo esc_attr( (string) $code ); ?>"><?php echo esc_html( (string) $name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="tno-acc-field tno-addr-field">
					<div class="tno-addr-label-row">
						<label for="tno-addr-city">شهر</label>
						<span class="tno-req-tag">* الزامی</span>
					</div>
					<select id="tno-addr-city" name="city" required disabled>
						<option value="">انتخاب کنید</option>
					</select>
				</div>
			</div>

			<!-- Row 2: Full Postal Address -->
			<div class="tno-acc-field tno-addr-field">
				<div class="tno-addr-label-row">
					<label for="tno-addr-line">نشانی پستی</label>
					<span class="tno-req-tag">* الزامی</span>
				</div>
				<textarea id="tno-addr-line" name="address_1" rows="4" placeholder="خیابان ، کوچه ، جزئیات آدرس" required></textarea>
			</div>

			<!-- Row 3: Plaque & Unit -->
			<div class="tno-addr-form-grid">
				<div class="tno-acc-field tno-addr-field">
					<div class="tno-addr-label-row">
						<label for="tno-addr-plaque">پلاک</label>
					</div>
					<input type="text" id="tno-addr-plaque" name="plaque" placeholder="وارد نمایید...">
				</div>

				<div class="tno-acc-field tno-addr-field">
					<div class="tno-addr-label-row">
						<label for="tno-addr-unit">واحد</label>
					</div>
					<input type="text" id="tno-addr-unit" name="unit" placeholder="وارد نمایید...">
				</div>
			</div>

			<!-- Row 4: Postal Code -->
			<div class="tno-acc-field tno-addr-field">
				<div class="tno-addr-label-row">
					<label for="tno-addr-postcode">کدپستی</label>
					<span class="tno-req-tag">* الزامی</span>
				</div>
				<input type="text" id="tno-addr-postcode" name="postcode" inputmode="numeric" maxlength="10" placeholder="۱۰ رقمی باشد..." required>
				<span class="tno-addr-postcode-error" id="tno-addr-postcode-error" hidden>کدپستی وارد شده صحیح نمی باشد .</span>
			</div>

			<div class="tno-modal-feedback" id="tno-addr-feedback" aria-live="polite"></div>

			<div class="tno-addr-modal-foot">
				<button type="submit" class="tno-addr-submit-btn" id="tno-addr-submit" disabled>
					<span>تایید و ادامه</span>
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
				</button>

				<button type="button" class="tno-addr-modal-delete" id="tno-addr-modal-delete" hidden>حذف آدرس</button>
			</div>
		</form>
	</div>
</div>
