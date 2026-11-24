<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id     = get_current_user_id();
$addresses   = tno_account_get_addresses( $user_id );
$iran_states = WC()->countries->get_states( 'IR' ) ?: [];
$user        = wp_get_current_user();
$def_name    = trim( $user->first_name . ' ' . $user->last_name ) ?: $user->display_name;
$def_phone   = (string) get_user_meta( $user_id, 'billing_phone', true );
?>

<div class="tno-acc-head tno-acc-head--split">
	<h1>آدرس ها</h1>
	<?php if ( ! empty( $addresses ) ) : ?>
		<button type="button" class="tno-acc-dark-btn tno-acc-dark-btn--sm" data-open-address-modal="1">ثبت آدرس جدید</button>
	<?php endif; ?>
</div>

<?php if ( empty( $addresses ) ) : ?>
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
	<!-- Populated Addresses List -->
	<div class="tno-addr-list">
		<?php foreach ( $addresses as $addr ) : ?>
			<article class="tno-acc-card tno-addr-card">
				<div class="tno-addr-card-main">
					<p class="tno-addr-text"><?php echo esc_html( $addr['display'] ); ?></p>
					<div class="tno-addr-metas">
						<?php if ( ! empty( $addr['postcode'] ) ) : ?>
							<span><span class="tno-muted-lbl">کدپستی :</span> <?php echo esc_html( tno_fa_digits( $addr['postcode'] ) ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $addr['receiver'] ) ) : ?>
							<span><span class="tno-muted-lbl">تحویل گیرنده :</span> <?php echo esc_html( $addr['receiver'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $addr['phone'] ) ) : ?>
							<span><span class="tno-muted-lbl">شماره تماس :</span> <span dir="ltr"><?php echo esc_html( tno_fa_digits( $addr['phone'] ) ); ?></span></span>
						<?php endif; ?>
					</div>
				</div>
				<div class="tno-addr-card-actions">
					<button
						type="button"
						class="tno-ord-detail-btn"
						data-edit-address="<?php echo esc_attr( wp_json_encode( $addr ) ); ?>"
					>
						ویرایش
					</button>
					<button
						type="button"
						class="tno-addr-delete-btn"
						data-delete-address="<?php echo esc_attr( $addr['id'] ); ?>"
					>
						حذف
					</button>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<!-- Add / Edit Address Modal -->
<div class="tno-acc-modal-backdrop" id="tno-address-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-addr-modal-title">
	<div class="tno-acc-modal">
		<header class="tno-acc-modal-head">
			<h2 id="tno-addr-modal-title">ثبت آدرس جدید</h2>
			<button type="button" class="tno-acc-modal-close" data-close-modal="tno-address-modal" aria-label="بستن">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
			</button>
		</header>

		<form id="tno-address-form" class="tno-addr-form">
			<input type="hidden" name="address_id" id="tno-addr-id" value="">

			<div class="tno-addr-form-grid">
				<div class="tno-acc-field">
					<label for="tno-addr-state">استان</label>
					<select id="tno-addr-state" name="state" required>
						<option value="">انتخاب کنید</option>
						<?php foreach ( $iran_states as $code => $name ) : ?>
							<option value="<?php echo esc_attr( (string) $code ); ?>"><?php echo esc_html( (string) $name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="tno-acc-field">
					<label for="tno-addr-city">شهر</label>
					<input type="text" id="tno-addr-city" name="city" placeholder="مثال : تهران" required>
				</div>
				<div class="tno-acc-field">
					<label for="tno-addr-postcode">کدپستی</label>
					<input type="text" id="tno-addr-postcode" name="postcode" placeholder="مثال : ۳۲۱۲۵۶۳۷۴۹۰" inputmode="numeric">
				</div>
				<div class="tno-acc-field">
					<label for="tno-addr-phone">شماره تماس تحویل گیرنده</label>
					<input type="tel" id="tno-addr-phone" name="phone" value="<?php echo esc_attr( $def_phone ); ?>" placeholder="۰۹۱۲۳۴۵۶۷۸۹">
				</div>
			</div>

			<div class="tno-acc-field">
				<label for="tno-addr-receiver">نام تحویل گیرنده</label>
				<input type="text" id="tno-addr-receiver" name="receiver" value="<?php echo esc_attr( $def_name ); ?>" placeholder="نام و نام خانوادگی">
			</div>

			<div class="tno-acc-field">
				<label for="tno-addr-line">نشانی پستی کامل</label>
				<textarea id="tno-addr-line" name="address_1" rows="3" placeholder="مثال : خیابان ولیعصر، منطقه ۱۲، بلوار کاوه، کوچه ابوذر، پلاک ۱۵" required></textarea>
			</div>

			<div class="tno-modal-feedback" id="tno-addr-feedback" aria-live="polite"></div>

			<button type="submit" class="tno-acc-dark-btn tno-acc-dark-btn--full" id="tno-addr-submit">ذخیره آدرس</button>
		</form>
	</div>
</div>