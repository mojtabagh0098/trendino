<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * رندر شورت‌کد یا قالب صفحه لاگین و ثبت‌نام ترندینو
 */
add_shortcode( 'tno_auth_box', 'tno_render_auth_container' );
function tno_render_auth_container() {
    if ( is_user_logged_in() ) {
        return '<p class="tno-logged-in-msg">شما در حال حاضر وارد حساب کاربری خود شده‌اید.</p>';
    }

    ob_start();
    ?>
    <div class="browser-frame tno-auth-container">
        <div class="browser-topbar">
            <div class="traffic">
                <span class="r"></span>
                <span class="y"></span>
                <span class="g"></span>
            </div>
            <div class="url-bar">trendino.ir/auth</div>
        </div>

        <div class="auth-box-inner" style="padding: 40px; background: #fff; text-align: right;" dir="rtl">
            <div class="auth-logo-wrap" style="text-align: center; margin-bottom: 25px;">
                <div class="tf-footer__text-logo default-logo" aria-label="ترندینو">
                    <span class="logo-icon"><i>T</i><i>D</i></span>
                    <strong class="logo-text" style="color: #012561;">ترندینو</strong>
                </div>
                <h3 style="margin-top: 15px; font-size: 18px; font-weight: 800;">ورود / ثبت نام</h3>
            </div>

            <!-- مرحله ۱: ورود شماره تلفن همراه (تصویر ۱) -->
            <div class="auth-step auth-step-phone">
                <p style="color: #717171; font-size: 14px; margin-bottom: 20px;">لطفا شماره موبایل خود را وارد کنید</p>
                <form id="tno-phone-form">
                    <div style="position: relative; margin-bottom: 15px;">
                        <input type="text" name="phone" placeholder="09121234567" maxlength="11" style="width: 100%; height: 48px; border: 1px solid #ECECEC; border-radius: 8px; padding: 0 15px; font-family: inherit; direction: ltr; text-align: right;" required>
                    </div>
                    <div class="auth-error-msg" style="color: #E54B4B; font-size: 12px; margin-bottom: 15px; display: none;">
                        ⚠️ شماره وارد شده صحیح نمی باشد لطفا مجددا امتحان کنید[cite: 1]
                    </div>
                    <button type="submit" style="width: 100%; height: 48px; background: #131313; color: #fff; border-radius: 8px; font-weight: 700; cursor: pointer; border: none;">تایید و ادامه[cite: 1]</button>
                    <p style="font-size: 11px; color: #8A8FA3; text-align: center; margin-top: 15px;">ورود شما به معنی پذیرش قوانین و مقررات ترندینو است[cite: 1]</p>
                </form>
            </div>

            <!-- مرحله ۲: دریافت کد OTP (تصویر ۲ و ۳) -->
            <div class="auth-step auth-step-otp" style="display: none;">
                <p style="color: #717171; font-size: 14px; margin-bottom: 20px;">کد تایید برای شماره <span class="otp-target-phone" style="direction: ltr; display: inline-block;"></span> پیامک شد[cite: 2, 3]</p>
                <div class="otp-inputs-row" style="display: flex; gap: 10px; justify-content: center; direction: ltr; margin-bottom: 20px;">
                    <input type="text" maxlength="1" class="otp-digit-input" style="width: 45px; height: 45px; text-align: center; font-size: 18px; border: 1px solid #ECECEC; border-radius: 8px;">
                    <input type="text" maxlength="1" class="otp-digit-input" style="width: 45px; height: 45px; text-align: center; font-size: 18px; border: 1px solid #ECECEC; border-radius: 8px;">
                    <input type="text" maxlength="1" class="otp-digit-input" style="width: 45px; height: 45px; text-align: center; font-size: 18px; border: 1px solid #ECECEC; border-radius: 8px;">
                    <input type="text" maxlength="1" class="otp-digit-input" style="width: 45px; height: 45px; text-align: center; font-size: 18px; border: 1px solid #ECECEC; border-radius: 8px;">
                    <input type="text" maxlength="1" class="otp-digit-input" style="width: 45px; height: 45px; text-align: center; font-size: 18px; border: 1px solid #ECECEC; border-radius: 8px;">
                </div>
                <div class="otp-timer-display" style="text-align: center; font-size: 12px; color: #8A8FA3; margin-bottom: 20px;">01:34 دقیقه مانده تا دریافت مجدد کد[cite: 2]</div>
                <a href="#" class="switch-to-pass-btn" style="display: block; text-align: center; font-size: 13px; color: #012561; text-decoration: none; margin-bottom: 15px;">ورود با رمز عبور <</a>[cite: 2]
            </div>

            <!-- مرحله ۳: ورود با رمزعبور (کاربر ثبت‌نام شده - تصویر ۴) -->
            <div class="auth-step auth-step-password" style="display: none;">
                <p style="color: #717171; font-size: 14px; margin-bottom: 20px;">رمز عبور خود را وارد کنید (<span class="user-display-phone" style="direction: ltr; display: inline-block;"></span>)</p>
                <form id="tno-pass-login-form">
                    <input type="password" name="password" placeholder="رمز عبور" style="width: 100%; height: 48px; border: 1px solid #ECECEC; border-radius: 8px; padding: 0 15px; margin-bottom: 15px;" required>
                    <button type="submit" style="width: 100%; height: 48px; background: #131313; color: #fff; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">تایید</button>
                </form>
                <div style="display: flex; justify-content: space-between; margin-top: 15px; font-size: 13px;">
                    <a href="#" class="switch-to-otp-btn" style="color: #012561;">ورود با رمز یکبار مصرف <</a>[cite: 4]
                    <a href="#" class="forgot-pass-link" style="color: #E54B4B;">فراموشی رمز عبور <</a>[cite: 4]
                </div>
            </div>

            <!-- مرحله ۴: تعیین رمزعبور جدید / ثبت‌نام نهایی (تصویر ۵، ۶، ۷ و ۸) -->
            <div class="auth-step auth-step-new-password" style="display: none;">
                <h4 style="font-size: 16px; font-weight: 800; margin-bottom: 5px;">تغییر رمز عبور</h4>
                <p style="color: #717171; font-size: 13px; margin-bottom: 20px;">رمز عبور جدید خود را وارد کنید[cite: 5]</p>
                <form id="tno-set-password-form">
                    <div style="position: relative; margin-bottom: 10px;">
                        <input type="password" id="tno-new-password" name="new_password" placeholder="رمز عبور جدید" style="width: 100%; height: 48px; border: 1px solid #ECECEC; border-radius: 8px; padding: 0 15px;" required>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div style="display: flex; gap: 5px; flex: 1; margin-left: 15px;">
                            <span class="strength-bar" style="height: 4px; flex: 1; background: #ECECEC; border-radius: 2px;"></span>
                            <span class="strength-bar" style="height: 4px; flex: 1; background: #ECECEC; border-radius: 2px;"></span>
                            <span class="strength-bar" style="height: 4px; flex: 1; background: #ECECEC; border-radius: 2px;"></span>
                        </div>
                        <span class="password-strength-text" style="font-size: 12px; font-weight: 700; color: #E54B4B;">ضعیف[cite: 5]</span>
                    </div>
                    <ul style="font-size: 11px; color: #8A8FA3; margin-bottom: 20px; list-style: disc; padding-right: 15px; line-height: 1.8;">
                        <li>شامل عدد[cite: 5]</li>
                        <li>حداقل ۸ حرف[cite: 5]</li>
                        <li>شامل یک حرف بزرگ و یک حرف کوچک[cite: 5]</li>
                        <li>شامل علامت (@#$%^&*)[cite: 5]</li>
                    </ul>
                    <input type="password" name="confirm_password" placeholder="تکرار رمز عبور جدید" style="width: 100%; height: 48px; border: 1px solid #ECECEC; border-radius: 8px; padding: 0 15px; margin-bottom: 20px;" required>
                    <button type="submit" style="width: 100%; height: 48px; background: #131313; color: #fff; border-radius: 8px; font-weight: 700; border: none; cursor: pointer;">تغییر رمز</button>
                </form>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * پردازش ایجکس بررسی وضعیت کاربر (ثبت‌نام شده یا جدید)
 */
add_action( 'wp_ajax_nopriv_tno_check_user_status', 'tno_ajax_check_user_status' );
add_action( 'wp_ajax_tno_check_user_status', 'tno_ajax_check_user_status' );
function tno_ajax_check_user_status() {
    check_ajax_referer( 'tno_auth_nonce', 'nonce' );

    $phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    if ( empty( $phone ) ) {
        wp_send_json_error();
    }

    $user = get_user_by( 'login', $phone );
    if ( ! $user ) {
        $user = get_users( array(
            'meta_key'   => 'billing_phone',
            'meta_value' => $phone,
            'number'     => 1,
        ) );
        $exists = ! empty( $user );
    } else {
        $exists = true;
    }

    wp_send_json_success( array( 'exists' => $exists ) );
}