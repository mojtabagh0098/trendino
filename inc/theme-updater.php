<?php
/** Manual updates of the parent theme from the public Trendino main branch. */
defined( 'ABSPATH' ) || exit;

/** Only full commit hashes received from GitHub may be used in archive URLs. */
function tno_update_valid_sha( $sha ) {
    return is_string( $sha ) && (bool) preg_match( '/^[a-f0-9]{40}$/', $sha );
}

function tno_update_page_url() {
    return add_query_arg( array( 'page' => 'tno-settings', 'tab' => 'update' ), admin_url( 'admin.php' ) );
}

add_action( 'admin_post_tno_check_theme_update', function () {
    if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'update_themes' ) ) {
        wp_die( esc_html__( 'شما اجازه بروزرسانی قالب را ندارید.', 'trendino' ), '', array( 'response' => 403 ) );
    }
    check_admin_referer( 'tno_check_theme_update' );

    $response = wp_safe_remote_get( 'https://api.github.com/repos/mojtabagh0098/trendino/commits/main', array(
        'timeout' => 15,
        'headers' => array( 'Accept' => 'application/vnd.github+json', 'User-Agent' => 'Trendino-Theme-Updater' ),
    ) );
    $data = ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response )
        ? json_decode( wp_remote_retrieve_body( $response ), true ) : null;
    if ( ! is_array( $data ) || ! tno_update_valid_sha( $data['sha'] ?? null ) ) {
        // Never keep an old result available for installation after a failed check.
        delete_option( 'tno_update_available_commit' );
        set_transient( 'tno_update_check_error_' . get_current_user_id(), 'دریافت اطلاعات از GitHub ناموفق بود. اتصال سرور و محدودیت درخواست GitHub را بررسی کنید.', 60 );
    } else {
        update_option( 'tno_update_available_commit', array( 'sha' => $data['sha'], 'checked' => time() ), false );
    }
    wp_safe_redirect( tno_update_page_url() );
    exit;
} );

function tno_render_update_settings() {
    $available = get_option( 'tno_update_available_commit', array() );
    $sha = is_array( $available ) ? ( $available['sha'] ?? '' ) : '';
    $installed = get_option( 'tno_update_installed_commit', '' );
    $error_key = 'tno_update_check_error_' . get_current_user_id();
    $error = get_transient( $error_key );
    delete_transient( $error_key );
    $can_update = current_user_can( 'update_themes' );
    ?>
    <?php if ( $error ) : ?><div class="tno-alert warning" role="alert"><?php echo esc_html( $error ); ?></div><?php endif; ?>
    <section class="tno-card">
      <div class="tno-card-head"><div><span class="tno-card-indicator"></span><h3>بروزرسانی قالب ترندینو</h3></div></div>
      <div class="tno-update-content">
        <p>منبع بروزرسانی: <a href="https://github.com/mojtabagh0098/trendino" target="_blank" rel="noopener noreferrer" dir="ltr">mojtabagh0098/trendino</a>، شاخه <code>main</code></p>
        <p>نسخه نصب‌شده قالب: <bdi><?php echo esc_html( wp_get_theme( get_template() )->get( 'Version' ) ); ?></bdi></p>
        <p>آخرین نسخه نصب‌شده از این بخش: <bdi><?php echo tno_update_valid_sha( $installed ) ? esc_html( substr( $installed, 0, 12 ) ) : 'نامشخص'; ?></bdi></p>
        <?php if ( tno_update_valid_sha( $sha ) ) : ?>
          <p>آخرین نسخه بررسی‌شده در مخزن: <bdi><?php echo esc_html( substr( $sha, 0, 12 ) ); ?></bdi>
          <?php if ( $sha === $installed ) : ?> — مطابق آخرین نصب از این بخش<?php elseif ( tno_update_valid_sha( $installed ) ) : ?> — نسخه جدید موجود است<?php else : ?> — وضعیت نسخه نصب‌شده نامشخص است<?php endif; ?></p>
          <?php if ( ! empty( $available['checked'] ) ) : ?><p>زمان بررسی: <?php echo esc_html( wp_date( 'Y/m/d H:i', (int) $available['checked'] ) ); ?></p><?php endif; ?>
        <?php else : ?><p>برای دریافت اطلاعات آخرین نسخه، ابتدا مخزن را بررسی کنید.</p><?php endif; ?>
        <div class="tno-alert warning"><span class="dashicons dashicons-info-outline"></span><div><strong>پیش از بروزرسانی نسخه پشتیبان تهیه کنید.</strong><p>فایل‌های قالب اصلی جایگزین می‌شوند و تغییرات مستقیم آن‌ها از بین می‌رود. تنظیمات و محتوای پایگاه داده تغییر نمی‌کنند؛ تغییرات سفارشی را در قالب فرزند نگه دارید.</p></div></div>
        <?php if ( $can_update ) : ?>
          <div class="tno-update-actions">
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="tno_check_theme_update"><?php wp_nonce_field( 'tno_check_theme_update' ); ?><button class="tno-save" type="submit">بررسی آخرین نسخه</button></form>
            <?php if ( tno_update_valid_sha( $sha ) ) :
                $url = wp_nonce_url( add_query_arg( array( 'action' => 'upgrade-theme', 'theme' => get_template() ), admin_url( 'update.php' ) ), 'upgrade-theme_' . get_template() );
                ?><a class="tno-save tno-update-link" href="<?php echo esc_url( $url ); ?>" onclick="return confirm('فایل‌های قالب اصلی جایگزین می‌شوند. آیا نسخه پشتیبان تهیه کرده‌اید؟');"><?php echo $sha === $installed ? 'نصب مجدد نسخه بررسی‌شده' : 'نصب نسخه بررسی‌شده'; ?></a><?php endif; ?>
          </div>
        <?php else : ?><p>برای بررسی و نصب بروزرسانی به مجوز بروزرسانی قالب نیاز دارید.</p><?php endif; ?>
      </div>
    </section>
    <?php
}

/** Supply the pinned archive only to the explicit native WordPress upgrade request. */
function tno_is_manual_update_request() {
    return is_admin() && current_user_can( 'update_themes' )
        && isset( $_GET['action'], $_GET['theme'] )
        && 'upgrade-theme' === sanitize_key( wp_unslash( $_GET['action'] ) )
        && get_template() === sanitize_text_field( wp_unslash( $_GET['theme'] ) );
}
add_filter( 'site_transient_update_themes', function ( $updates ) {
    if ( ! tno_is_manual_update_request() ) return $updates;
    $available = get_option( 'tno_update_available_commit', array() );
    $sha = is_array( $available ) ? ( $available['sha'] ?? '' ) : '';
    if ( ! tno_update_valid_sha( $sha ) ) return $updates;
    if ( ! is_object( $updates ) ) $updates = new stdClass();
    if ( ! isset( $updates->response ) || ! is_array( $updates->response ) ) $updates->response = array();
    $updates->response[ get_template() ] = array(
        'theme' => get_template(),
        'new_version' => $sha,
        'url' => 'https://github.com/mojtabagh0098/trendino',
        'package' => 'https://codeload.github.com/mojtabagh0098/trendino/zip/' . $sha,
    );
    return $updates;
} );

/** GitHub names archive directories after the commit; preserve the installed theme slug. */
add_filter( 'upgrader_source_selection', function ( $source, $remote_source, $upgrader, $hook_extra ) {
    if ( ! tno_is_manual_update_request() || ( $hook_extra['theme'] ?? '' ) !== get_template() ) return $source;
    $available = get_option( 'tno_update_available_commit', array() );
    $sha = is_array( $available ) ? ( $available['sha'] ?? '' ) : '';
    if ( ! tno_update_valid_sha( $sha ) || basename( untrailingslashit( $source ) ) !== 'trendino-' . $sha ) {
        return new WP_Error( 'tno_invalid_update_source', 'ساختار بسته بروزرسانی معتبر نیست.' );
    }
    global $wp_filesystem;
    if ( ! $wp_filesystem->exists( trailingslashit( $source ) . 'style.css' ) || ! $wp_filesystem->exists( trailingslashit( $source ) . 'functions.php' ) ) {
        return new WP_Error( 'tno_invalid_update_theme', 'فایل‌های اصلی قالب در بسته یافت نشد.' );
    }
    $destination = trailingslashit( $remote_source ) . get_template();
    if ( untrailingslashit( $source ) !== untrailingslashit( $destination ) && ! $wp_filesystem->move( $source, $destination ) ) {
        return new WP_Error( 'tno_update_rename_failed', 'آماده‌سازی پوشه قالب ناموفق بود.' );
    }
    $GLOBALS['tno_update_source_verified'] = $sha;
    return trailingslashit( $destination );
}, 10, 4 );

add_action( 'upgrader_process_complete', function ( $upgrader, $hook_extra ) {
    $sha = $GLOBALS['tno_update_source_verified'] ?? '';
    if ( tno_is_manual_update_request() && ( $hook_extra['theme'] ?? '' ) === get_template()
        && tno_update_valid_sha( $sha ) && is_array( $upgrader->result ) && is_array( $upgrader->skin->result ) ) {
        update_option( 'tno_update_installed_commit', $sha, false );
    }
}, 10, 2 );
