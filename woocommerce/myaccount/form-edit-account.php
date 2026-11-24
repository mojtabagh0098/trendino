<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id       = get_current_user_id();
$user          = wp_get_current_user();
$first_name    = (string) $user->first_name;
$last_name     = (string) $user->last_name;
$email         = (string) $user->user_email;
$phone         = (string) get_user_meta( $user_id, 'billing_phone', true );
if ( $phone === '' ) {
	$phone = (string) get_user_meta( $user_id, 'digits_phone_no', true );
}
if ( $phone === '' && preg_match( '/^09\d{9}$/', (string) $user->user_login ) ) {
	$phone = (string) $user->user_login;
}
$national_code = (string) get_user_meta( $user_id, '_tno_national_code', true );
if ( $national_code === '' ) {
	$national_code = (string) get_user_meta( $user_id, 'billing_national_code', true );
}

$dashboard_url = wc_get_account_endpoint_url( 'dashboard' );
?>

<div class="tno-acc-head tno-acc-head--mobile-bar">
	<a href="<?php echo esc_url( $dashboard_url ); ?>" class="tno-acc-back-mobile" aria-label="بازگشت">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
	</a>
	<h1>اطلاعات حساب کاربری</h1>
</div>

<section class="tno-acc-card tno-edit-account-card">
	<form id="tno-edit-account-form" class="tno-edit-account-form" method="post" novalidate>
		<div class="tno-edit-account-grid">
			<!-- Row 1: Right -> First Name -->
			<div class="tno-acc-pill-field">
				<label for="tno-acc-first-name">نام</label>
				<input
					type="text"
					id="tno-acc-first-name"
					name="first_name"
					value="<?php echo esc_attr( $first_name ); ?>"
					placeholder="مثال : احسان"
					autocomplete="given-name"
				>
			</div>

			<!-- Row 1: Middle -> Last Name -->
			<div class="tno-acc-pill-field">
				<label for="tno-acc-last-name">نام خانوادگی</label>
				<input
					type="text"
					id="tno-acc-last-name"
					name="last_name"
					value="<?php echo esc_attr( $last_name ); ?>"
					placeholder="مثال : فرجامی"
					autocomplete="family-name"
				>
			</div>

			<!-- Row 1: Left -> Mobile Phone -->
			<div class="tno-acc-pill-field">
				<label for="tno-acc-phone">شماره همراه</label>
				<input
					type="tel"
					id="tno-acc-phone"
					name="phone"
					value="<?php echo esc_attr( $phone !== '' ? tno_fa_digits( $phone ) : '' ); ?>"
					placeholder="۰۹۱۲۳۴۵۶۷۸۹"
					autocomplete="tel"
					inputmode="numeric"
				>
			</div>

			<!-- Row 2: Right -> Email -->
			<div class="tno-acc-pill-field">
				<label for="tno-acc-email">ایمیل</label>
				<input
					type="email"
					id="tno-acc-email"
					name="email"
					value="<?php echo esc_attr( $email ); ?>"
					placeholder="مثال : sample1236@gmail.com"
					autocomplete="email"
				>
			</div>

			<!-- Row 2: Middle -> National Code -->
			<div class="tno-acc-pill-field">
				<label for="tno-acc-national-code">کد ملی</label>
				<input
					type="text"
					id="tno-acc-national-code"
					name="national_code"
					value="<?php echo esc_attr( $national_code !== '' ? tno_fa_digits( $national_code ) : '' ); ?>"
					placeholder="۰۰۲۴۵۴۵۶۷۸۸۲"
					inputmode="numeric"
					maxlength="10"
				>
			</div>
		</div>

		<div id="tno-edit-account-feedback" class="tno-edit-account-feedback" hidden role="status"></div>

		<div class="tno-edit-account-actions">
			<button type="submit" id="tno-edit-account-submit" class="tno-acc-save-pill-btn">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<polyline points="20 6 9 17 4 12"></polyline>
				</svg>
				<span>ذخیره اطلاعات</span>
			</button>
		</div>
	</form>
</section>
