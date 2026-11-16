<?php
/** Trendino phone authentication. SMS delivery must be configured separately. */
if ( ! defined( 'ABSPATH' ) ) exit;

function tno_auth_url( $redirect = '' ) {
    $url = add_query_arg( 'tno_auth', '1', home_url( '/' ) );
    return $redirect ? add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url ) : $url;
}
add_filter( 'login_url', function( $url, $redirect, $force_reauth ) { return tno_auth_url( $redirect ); }, 10, 3 );
add_filter( 'register_url', function() { return tno_auth_url(); } );
add_filter( 'lostpassword_url', function( $url, $redirect ) { return add_query_arg( 'forgot', '1', tno_auth_url( $redirect ) ); }, 10, 2 );
add_action( 'login_init', function() {
    $action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login';
    // Logout and password-protected post handling rely on WordPress's own nonce checks.
    if ( in_array( $action, array( 'logout', 'postpass', 'confirmaction' ), true ) ) return;
    $redirect = isset( $_REQUEST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_REQUEST['redirect_to'] ), '' ) : '';
    $url = tno_auth_url( $redirect );
    if ( in_array( $action, array( 'lostpassword', 'retrievepassword', 'resetpass', 'rp' ), true ) ) $url = add_query_arg( 'forgot', '1', $url );
    wp_safe_redirect( $url ); exit;
} );
add_action( 'template_redirect', function() {
    if ( isset( $_GET['tno_auth'] ) && '1' === $_GET['tno_auth'] ) {
        if ( is_user_logged_in() ) { wp_safe_redirect( tno_destination() ); exit; }
        nocache_headers();
        include get_template_directory() . '/template-parts/auth-page.php'; exit;
    }
    if ( ! is_user_logged_in() && function_exists( 'is_account_page' ) && is_account_page() ) {
        wp_safe_redirect( tno_auth_url( home_url( add_query_arg( array(), $GLOBALS['wp']->request ) ) ) ); exit;
    }
    // Preserve guest checkout when WooCommerce allows it.
    if ( ! is_user_logged_in() && function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && 'no' === get_option( 'woocommerce_enable_guest_checkout' ) ) {
        wp_safe_redirect( tno_auth_url( wc_get_checkout_url() ) ); exit;
    }
} );
function tno_destination() {
    $fallback = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
    if ( ! $fallback ) $fallback = home_url( '/' );
    $redirect = isset( $_REQUEST['redirect_to'] ) ? wp_unslash( $_REQUEST['redirect_to'] ) : '';
    return wp_validate_redirect( $redirect, $fallback );
}
function tno_phone( $value ) {
    $value = strtr( $value, array_combine( preg_split( '//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY ), str_split( '01234567890123456789' ) ) );
    $value = preg_replace( '/[\s\-()]/u', '', $value );
    if ( 0 === strpos( $value, '+98' ) ) $value = '0' . substr( $value, 3 );
    if ( 0 === strpos( $value, '0098' ) ) $value = '0' . substr( $value, 4 );
    return preg_match( '/^09[0-9]{9}$/', $value ) ? $value : '';
}
function tno_find_user( $phone ) {
    $users = get_users( array( 'meta_key' => 'billing_phone', 'meta_value' => $phone, 'number' => 2, 'fields' => 'all' ) );
    $login = get_user_by( 'login', $phone );
    if ( $login && ! in_array( $login->ID, wp_list_pluck( $users, 'ID' ), true ) ) $users[] = $login;
    if ( count( $users ) > 1 ) return new WP_Error( 'ambiguous', 'این شماره به چند حساب متصل است. با پشتیبانی تماس بگیرید.' );
    return $users ? $users[0] : false;
}
function tno_session_key() {
    $token = isset( $_COOKIE['tno_auth_session'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tno_auth_session'] ) ) : '';
    return preg_match( '/^[a-f0-9]{64}$/', $token ) ? 'tno_session_' . hash( 'sha256', $token ) : '';
}
function tno_new_session( $phone, $purpose ) {
    $old = tno_session_key(); if ( $old ) delete_transient( $old );
    $token = bin2hex( random_bytes( 32 ) );
    setcookie( 'tno_auth_session', $token, array( 'expires' => time() + 900, 'path' => COOKIEPATH ?: '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax' ) );
    $key = 'tno_session_' . hash( 'sha256', $token );
    set_transient( $key, array( 'phone' => $phone, 'purpose' => $purpose ), 900 );
    return $key;
}
function tno_limit( $type, $identity, $max, $ttl ) {
    $key = 'tno_limit_' . hash( 'sha256', $type . '|' . $identity );
    $count = (int) get_transient( $key );
    if ( $count >= $max ) wp_send_json_error( array( 'message' => 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد تلاش کنید.' ), 429 );
    set_transient( $key, $count + 1, $ttl );
}
function tno_send_code( $phone, $code ) {
    // Override via tno_auth_send_sms filter, returning true or WP_Error.
    $result = apply_filters( 'tno_auth_send_sms', null, $phone, $code );
    if ( null !== $result ) return $result;
    $settings = tno_auth_settings();
    $api_key = defined( 'TNO_KAVENEGAR_API_KEY' ) ? TNO_KAVENEGAR_API_KEY : get_option( 'tno_sms_api_key', '' );
    $template = defined( 'TNO_KAVENEGAR_TEMPLATE' ) ? TNO_KAVENEGAR_TEMPLATE : $settings['sms_template'];
    if ( ! $api_key || ! $template ) return new WP_Error( 'sms_config', 'سامانه پیامک تنظیم نشده است.' );
    $url = 'https://api.kavenegar.com/v1/' . rawurlencode( $api_key ) . '/verify/lookup.json';
    $response = wp_remote_post( $url, array( 'timeout' => 15, 'body' => array( 'receptor' => $phone, 'token' => $code, 'template' => $template ) ) );
    if ( is_wp_error( $response ) ) return $response;
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    return 200 === wp_remote_retrieve_response_code( $response ) && isset( $body['return']['status'] ) && 200 === (int) $body['return']['status'] ? true : new WP_Error( 'sms_failed', 'ارسال پیامک ناموفق بود. لطفاً دوباره تلاش کنید.' );
}
function tno_issue_code( $key, $session ) {
    $phone = $session['phone'];
    $settings = tno_auth_settings();
    $cooldown = max( 30, min( 600, (int) $settings['otp_cooldown'] ) );
    $expiry = max( 60, min( 900, (int) $settings['otp_expiry'] ) );
    $last = (int) get_transient( 'tno_sms_last_' . hash( 'sha256', $phone ) );
    if ( $last && time() - $last < $cooldown ) wp_send_json_error( array( 'message' => 'برای دریافت مجدد کد کمی صبر کنید.' ), 429 );
    tno_limit( 'sms-phone', $phone, 5, HOUR_IN_SECONDS );
    tno_limit( 'sms-ip', $_SERVER['REMOTE_ADDR'] ?? '', 20, HOUR_IN_SECONDS );
    $code = (string) random_int( 10000, 99999 );
    $sent = tno_send_code( $phone, $code );
    if ( true !== $sent ) wp_send_json_error( array( 'message' => 'ارسال پیامک ناموفق بود یا سامانه پیامک تنظیم نشده است. لطفاً با پشتیبانی تماس بگیرید.' ), 503 );
    $session['code_hash'] = wp_hash_password( $code ); $session['expires'] = time() + $expiry; $session['attempts'] = 0; $session['verified'] = false;
    set_transient( $key, $session, 900 );
    set_transient( 'tno_sms_last_' . hash( 'sha256', $phone ), time(), $cooldown );
}
function tno_sign_in( $user ) {
    wp_set_current_user( $user->ID ); wp_set_auth_cookie( $user->ID, true );
    do_action( 'wp_login', $user->user_login, $user );
    $key = tno_session_key(); if ( $key ) delete_transient( $key );
    wp_send_json_success( array( 'redirect' => tno_destination() ) );
}
add_action( 'wp_ajax_nopriv_tno_auth', 'tno_auth_ajax' );
add_action( 'wp_ajax_tno_auth', 'tno_auth_ajax' );
function tno_auth_ajax() {
    check_ajax_referer( 'tno_auth', 'nonce' );
    if ( is_user_logged_in() ) wp_send_json_error( array( 'message' => 'شما وارد حساب خود شده‌اید.' ), 403 );
    $step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
    tno_limit( 'requests-ip', $_SERVER['REMOTE_ADDR'] ?? '', 150, HOUR_IN_SECONDS );
    if ( 'start' === $step ) {
        $phone = tno_phone( isset( $_POST['phone'] ) ? wp_unslash( $_POST['phone'] ) : '' );
        if ( ! $phone ) wp_send_json_error( array( 'message' => 'شماره وارد شده صحیح نمی‌باشد لطفاً مجدداً امتحان کنید.' ) );
        $user = tno_find_user( $phone );
        if ( is_wp_error( $user ) ) wp_send_json_error( array( 'message' => $user->get_error_message() ) );
        $forgot = ! empty( $_POST['forgot'] );
        if ( $forgot && ! $user ) wp_send_json_error( array( 'message' => 'حسابی با این شماره پیدا نشد.' ) );
        if ( ! $user && ! $forgot && empty( tno_auth_settings()['registration'] ) ) wp_send_json_error( array( 'message' => 'ثبت‌نام کاربران جدید در حال حاضر غیرفعال است.' ) );
        $purpose = $forgot ? 'reset' : ( $user ? 'login' : 'register' );
        $key = tno_new_session( $phone, $purpose );
        if ( 'login' !== $purpose ) tno_issue_code( $key, array( 'phone' => $phone, 'purpose' => $purpose ) );
        wp_send_json_success( array( 'step' => 'login' === $purpose ? 'password' : 'otp', 'phone' => $phone ) );
    }
    $key = tno_session_key(); $session = $key ? get_transient( $key ) : false;
    if ( ! $session ) wp_send_json_error( array( 'message' => 'نشست منقضی شده است. دوباره شروع کنید.' ), 403 );
    $phone = $session['phone']; $user = tno_find_user( $phone );
    if ( is_wp_error( $user ) ) wp_send_json_error( array( 'message' => $user->get_error_message() ) );
    if ( 'password' === $step && 'login' === $session['purpose'] && $user ) {
        tno_limit( 'password-phone', $phone, 10, 15 * MINUTE_IN_SECONDS );
        tno_limit( 'password-ip', $_SERVER['REMOTE_ADDR'] ?? '', 30, 15 * MINUTE_IN_SECONDS );
        $password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
        $authenticated = wp_authenticate( $user->user_login, $password );
        if ( is_wp_error( $authenticated ) ) wp_send_json_error( array( 'message' => 'رمز خود را درست وارد کنید.' ) );
        tno_sign_in( $authenticated );
    }
    if ( 'send' === $step && in_array( $session['purpose'], array( 'login', 'register', 'reset' ), true ) ) {
        tno_issue_code( $key, $session ); wp_send_json_success();
    }
    if ( 'verify' === $step ) {
        tno_limit( 'otp-phone', $phone, 15, 15 * MINUTE_IN_SECONDS );
        $code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
        if ( empty( $session['code_hash'] ) || empty( $session['expires'] ) || time() > $session['expires'] || ( $session['attempts'] ?? 0 ) >= max( 1, min( 10, (int) tno_auth_settings()['otp_attempts'] ) ) ) wp_send_json_error( array( 'message' => 'کد منقضی شده است. کد جدید دریافت کنید.' ) );
        $session['attempts']++; set_transient( $key, $session, 900 );
        if ( ! preg_match( '/^[0-9]{5}$/', $code ) || ! wp_check_password( $code, $session['code_hash'] ) ) wp_send_json_error( array( 'message' => 'کد تایید وارد شده اشتباه می‌باشد.' ) );
        unset( $session['code_hash'] ); $session['verified'] = true; $session['verified_until'] = time() + 300; set_transient( $key, $session, 900 );
        if ( 'login' === $session['purpose'] && $user ) tno_sign_in( $user );
        wp_send_json_success( array( 'step' => 'newPassword' ) );
    }
    if ( 'set_password' === $step && ! empty( $session['verified'] ) && time() <= ( $session['verified_until'] ?? 0 ) && in_array( $session['purpose'], array( 'register', 'reset' ), true ) ) {
        $password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';
        $confirm = isset( $_POST['confirm'] ) ? wp_unslash( $_POST['confirm'] ) : '';
        if ( strlen( $password ) < 8 || ! preg_match( '/[0-9]/', $password ) || ! preg_match( '/[a-z]/', $password ) || ! preg_match( '/[A-Z]/', $password ) || ! preg_match( '/[@#$%^&*!?]/', $password ) || $password !== $confirm ) wp_send_json_error( array( 'message' => 'رمز عبور شرایط لازم را ندارد یا تکرار آن یکسان نیست.' ) );
        if ( 'register' === $session['purpose'] ) {
            if ( empty( tno_auth_settings()['registration'] ) ) wp_send_json_error( array( 'message' => 'ثبت‌نام کاربران جدید در حال حاضر غیرفعال است.' ) );
            if ( $user ) wp_send_json_error( array( 'message' => 'این شماره قبلاً ثبت شده است. دوباره وارد شوید.' ) );
            $id = wp_insert_user( array( 'user_login' => $phone, 'user_pass' => $password, 'role' => get_role( 'customer' ) ? 'customer' : 'subscriber' ) );
            if ( is_wp_error( $id ) ) wp_send_json_error( array( 'message' => 'ساخت حساب ناموفق بود. دوباره تلاش کنید.' ) );
            update_user_meta( $id, 'billing_phone', $phone );
            $user = get_user_by( 'id', $id );
            if ( function_exists( 'WC' ) ) do_action( 'woocommerce_created_customer', $id, array( 'user_login' => $phone ), false );
        } else {
            if ( ! $user ) wp_send_json_error( array( 'message' => 'حساب کاربری پیدا نشد.' ) );
            wp_set_password( $password, $user->ID ); $user = get_user_by( 'id', $user->ID );
        }
        tno_sign_in( $user );
    }
    wp_send_json_error( array( 'message' => 'درخواست نامعتبر است.' ), 400 );
}
