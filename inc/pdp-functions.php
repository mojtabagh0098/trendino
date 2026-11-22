<?php
/**
 * Trendino Single Product Page (PDP) Backend Architecture
 * Handles Meta Boxes, Grouped Specs, Extended Reviews, Q&A, and AJAX Endpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TNO_PDP_Manager {

    public static function init(): void {
        // Product Meta Boxes
        add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_boxes' ) );
        add_action( 'save_post_product', array( __CLASS__, 'save_product_meta' ), 10, 2 );

        // Comment / Review Meta Box in Admin
        add_action( 'add_meta_boxes_comment', array( __CLASS__, 'register_comment_meta_box' ) );
        add_action( 'edit_comment', array( __CLASS__, 'save_admin_comment_meta' ) );

        // Clear rating cache on comment status change
        add_action( 'wp_set_comment_status', array( __CLASS__, 'flush_product_rating_cache' ) );
        add_action( 'comment_post', array( __CLASS__, 'flush_product_rating_cache' ) );

        add_filter( 'woocommerce_structured_data_breadcrumblist', '__return_empty_array' );

        // AJAX Endpoints: Reviews, Q&A, Votes, Sorting, Price Alert
        $ajax_actions = array(
            'tno_submit_review',
            'tno_submit_question',
            'tno_vote_comment',
            'tno_filter_comments',
            'tno_filter_questions',
            'tno_toggle_wishlist',
            'tno_price_report',
        );

        foreach ( $ajax_actions as$action ) {
            add_action( 'wp_ajax_' . $action, array( __CLASS__, 'ajax_' .$action ) );
            add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, 'ajax_' .$action ) );
        }
    }

    /**
     * 6 Review Rating Criteria Definitions
     */
    public static function get_review_criteria(): array {
        return array(
            'build'      => 'کیفیت ساخت',
            'value'      => 'ارزش خرید به نسبت قیمت',
            'innovation' => 'نوآوری',
            'features'   => 'امکانات و قابلیت‌ها',
            'ease'       => 'سهولت استفاده',
            'design'     => 'طراحی و ظاهر',
        );
    }

    /**
     * Register Product Meta Boxes
     */
    public static function register_meta_boxes(): void {
        add_meta_box(
            'tno_pdp_settings',
            'تنظیمات اختصاصی صفحه محصول (ترندینو)',
            array( __CLASS__, 'render_product_meta_box' ),
            'product',
            'normal',
            'high'
        );
    }

    /**
     * Render Product Meta Box HTML
     */
    public static function render_product_meta_box( \WP_Post $post ): void {
        wp_nonce_field( 'tno_save_pdp_meta', 'tno_pdp_meta_nonce' );

        $en_title        = get_post_meta( $post->ID, '_tno_en_title', true );
        $badge_text      = get_post_meta( $post->ID, '_tno_badge_text', true ) ?: 'تضمین اصالت و سلامت فیزیکی کالا';
        $seller_name     = get_post_meta( $post->ID, '_tno_seller_name', true ) ?: get_bloginfo( 'name' );
        $seller_perf     = get_post_meta( $post->ID, '_tno_seller_perf', true ) ?: 'عملکرد عالی';
        $warranty_text   = get_post_meta( $post->ID, '_tno_warranty_text', true ) ?: 'گارانتی ۱۸ ماهه شرکتی معتبر';
        $shipping_text   = get_post_meta( $post->ID, '_tno_shipping_text', true ) ?: 'موجود در انبار فروشنده (ارسال فوری)';
        $club_points     = get_post_meta( $post->ID, '_tno_club_points', true ) ?: '۱۵۰';
        $return_alert    = get_post_meta( $post->ID, '_tno_return_alert', true ) ?: 'درخواست مرجوع کردن کالا در گروه موبایل و کالای دیجیتال تنها با دلیل «انصراف از خرید» و در صورت باز نشدن پلمپ کالا امکان‌پذیر است.';
        
        // Banners
        $installment_title = get_post_meta( $post->ID, '_tno_installment_title', true ) ?: 'خرید اقساطی آسان و بدون ضامن';
        $installment_sub   = get_post_meta( $post->ID, '_tno_installment_sub', true ) ?: 'با اعتبار اسنپ‌پی یا ازکی‌وام در ۴ قسط بدون سود خرید کنید';
        $installment_link  = get_post_meta( $post->ID, '_tno_installment_link', true ) ?: '#';
        $free_ship_title   = get_post_meta( $post->ID, '_tno_free_ship_title', true ) ?: 'ارسال رایگان برای این محصول';

        // AI Review Summary
        $ai_summary = get_post_meta( $post->ID, '_tno_ai_summary', true );
        $ai_pros    = get_post_meta( $post->ID, '_tno_ai_pros', true );
        $ai_cons    = get_post_meta( $post->ID, '_tno_ai_cons', true );

        // Key Features & Grouped Specifications (JSON/Array)
        $key_features = get_post_meta($post->ID, '_tno_key_features', true );
        if ( ! is_array( $key_features ) ) {$key_features = array();
        }

        $spec_groups = get_post_meta($post->ID, '_tno_spec_groups', true );
        if ( ! is_array( $spec_groups ) ) {$spec_groups = array();
        }
        ?>
        <style>
            .tno-admin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; direction: rtl; }
            .tno-admin-field label { display: block; font-weight: 700; margin-bottom: 6px; }
            .tno-admin-field input[type="text"], .tno-admin-field textarea { width: 100%; }
            .tno-admin-section-title { background: #f0f0f1; padding: 10px 14px; font-weight: 700; border-right: 4px solid #2271b1; margin: 20px 0 12px; }
            .tno-repeater-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: center; }
            .tno-spec-group-box { border: 1px solid #ccd0d4; padding: 12px; margin-bottom: 14px; background: #fafafa; border-radius: 6px; }
        </style>

        <div class="tno-admin-section-title">اطلاعات عمومی و باکس خرید</div>
        <div class="tno-admin-grid">
            <div class="tno-admin-field">
                <label>عنوان انگلیسی محصول (Subtitle)</label>
                <input type="text" name="tno_en_title" value="<?php echo esc_attr( $en_title ); ?>" dir="ltr" placeholder="Xiaomi Redmi Note 14 Pro 5G Dual SIM 256GB And 8GB RAM Mobile Phone">
            </div>
            <div class="tno-admin-field">
                <label>متن بج بالای عنوان</label>
                <input type="text" name="tno_badge_text" value="<?php echo esc_attr( $badge_text ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>نام فروشنده</label>
                <input type="text" name="tno_seller_name" value="<?php echo esc_attr( $seller_name ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>عملکرد فروشنده</label>
                <input type="text" name="tno_seller_perf" value="<?php echo esc_attr( $seller_perf ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>متن گارانتی</label>
                <input type="text" name="tno_warranty_text" value="<?php echo esc_attr( $warranty_text ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>وضعیت انبار و ارسال</label>
                <input type="text" name="tno_shipping_text" value="<?php echo esc_attr( $shipping_text ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>امتیاز باشگاه مشتریان</label>
                <input type="text" name="tno_club_points" value="<?php echo esc_attr( $club_points ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>لینک راهنمای خرید اقساطی</label>
                <input type="text" name="tno_installment_link" value="<?php echo esc_attr( $installment_link ); ?>" dir="ltr">
            </div>
        </div>

        <div class="tno-admin-grid">
            <div class="tno-admin-field">
                <label>عنوان بنر خرید اقساطی</label>
                <input type="text" name="tno_installment_title" value="<?php echo esc_attr( $installment_title ); ?>">
                <label style="margin-top:8px;">زیرعنوان بنر اقساطی</label>
                <input type="text" name="tno_installment_sub" value="<?php echo esc_attr( $installment_sub ); ?>">
            </div>
            <div class="tno-admin-field">
                <label>عنوان بنر ارسال رایگان</label>
                <input type="text" name="tno_free_ship_title" value="<?php echo esc_attr( $free_ship_title ); ?>">
                <label style="margin-top:8px;">زیرعنوان بنر ارسال رایگان</label>
                <input type="text" name="tno_free_ship_sub" value="<?php echo esc_attr( $free_ship_sub ); ?>">
            </div>
        </div>

        <div class="tno-admin-field">
            <label>متن هشدار شرایط مرجوعی کالا</label>
            <textarea name="tno_return_alert" rows="2"><?php echo esc_textarea( $return_alert ); ?></textarea>
        </div>

        <div class="tno-admin-section-title">ویژگی‌های کلیدی بالای صفحه (حداکثر ۶ مورد برای گرید هدر)</div>
        <p class="description">در صورت خالی بودن، به صورت خودکار از ویژگی‌های استاندارد ووکامرس خوانده می‌شود.</p>
        <div id="tno-key-features-wrap">
            <?php
            if ( empty( $key_features ) ) {$key_features = array( array( 'label' => '', 'value' => '' ) );
            }
            foreach ( $key_features as $idx =>$feat ) :
                ?>
                <div class="tno-repeater-row">
                    <input type="text" name="tno_key_features[<?php echo (int) $idx; ?>][label]" value="<?php echo esc_attr( $feat['label'] ?? '' ); ?>" placeholder="عنوان ویژگی (مثال: فناوری صفحه‌نمایش)" style="width:40%;">
                    <input type="text" name="tno_key_features[<?php echo (int) $idx; ?>][value]" value="<?php echo esc_attr( $feat['value'] ?? '' ); ?>" placeholder="مقدار (مثال: AMOLED)" style="width:50%;">
                    <button type="button" class="button tno-remove-row">حذف</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button button-secondary" id="tno-add-key-feature">+ افزودن ویژگی کلیدی</button>

        <div class="tno-admin-section-title">جدول مشخصات فنی گروه‌بندی شده</div>
        <p class="description">هر گروه شامل یک عنوان (مثلاً: صفحه نمایش، تراشه، حافظه) و سطرهای «عنوان : مقدار» در هر خط است (فرمت هر خط: <code>عنوان | مقدار</code>).</p>
        <div id="tno-spec-groups-wrap">
            <?php
            if ( empty( $spec_groups ) ) {$spec_groups = array( array( 'group_title' => 'مشخصات کلی', 'items_raw' => '' ) );
            }
            foreach ( $spec_groups as $g_idx =>$group ) :
                ?>
                <div class="tno-spec-group-box">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <input type="text" name="tno_spec_groups[<?php echo (int) $g_idx; ?>][group_title]" value="<?php echo esc_attr( $group['group_title'] ?? '' ); ?>" placeholder="نام گروه (مثال: صفحه نمایش)" style="width:70%;font-weight:bold;">
                        <button type="button" class="button tno-remove-group">حذف گروه</button>
                    </div>
                    <textarea name="tno_spec_groups[<?php echo (int) $g_idx; ?>][items_raw]" rows="4" style="width:100%;" placeholder="ابعاد | 162.3x74.4x8.4 میلی‌متر&#10;وزن | 190 گرم&#10;توضیحات سیم‌کارت | سایز نانو (8.8 × 12.3 میلی‌متر)"><?php echo esc_textarea( $group['items_raw'] ?? '' ); ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button button-secondary" id="tno-add-spec-group">+ افزودن گروه مشخصات جدید</button>

        <div class="tno-admin-section-title">خلاصه دیدگاه‌های هوش مصنوعی (AI Review Summary)</div>
        <div class="tno-admin-field" style="margin-bottom:12px;">
            <label>متن خلاصه دیدگاه‌های خریداران (در صورت خالی بودن، خودکار از روی امتیازات تولید می‌شود)</label>
            <textarea name="tno_ai_summary" rows="3"><?php echo esc_textarea( $ai_summary ); ?></textarea>
        </div>
        <div class="tno-admin-grid">
            <div class="tno-admin-field">
                <label>نقاط قوت پرتکرار (با ویرگول جدا کنید)</label>
                <input type="text" name="tno_ai_pros" value="<?php echo esc_attr( $ai_pros ); ?>" placeholder="کیفیت صفحه نمایش بالا, شارژدهی عالی باتری, طراحی خوش‌دست">
            </div>
            <div class="tno-admin-field">
                <label>نقاط ضعف پرتکرار (با ویرگول جدا کنید)</label>
                <input type="text" name="tno_ai_cons" value="<?php echo esc_attr( $ai_cons ); ?>" placeholder="عدم وجود جک هدفون, داغ شدن در بازی سنگین">
            </div>
        </div>

        <script>
        (function(){
            const kfWrap = document.getElementById('tno-key-features-wrap');
            document.getElementById('tno-add-key-feature')?.addEventListener('click', function(){
                const idx = Date.now();
                const div = document.createElement('div');
                div.className = 'tno-repeater-row';
                div.innerHTML = `<input type="text" name="tno_key_features[${idx}][label]" placeholder="عنوان ویژگی" style="width:40%;">
                                 <input type="text" name="tno_key_features[${idx}][value]" placeholder="مقدار" style="width:50%;">
                                 <button type="button" class="button tno-remove-row">حذف</button>`;
                kfWrap.appendChild(div);
            });

            const sgWrap = document.getElementById('tno-spec-groups-wrap');
            document.getElementById('tno-add-spec-group')?.addEventListener('click', function(){
                const idx = Date.now();
                const div = document.createElement('div');
                div.className = 'tno-spec-group-box';
                div.innerHTML = `<div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                    <input type="text" name="tno_spec_groups[${idx}][group_title]" placeholder="نام گروه مشخصات" style="width:70%;font-weight:bold;">
                                    <button type="button" class="button tno-remove-group">حذف گروه</button>
                                 </div>
                                 <textarea name="tno_spec_groups[${idx}][items_raw]" rows="4" style="width:100%;" placeholder="عنوان ویژگی | مقدار ویژگی"></textarea>`;
                sgWrap.appendChild(div);
            });

            document.addEventListener('click', function(e){
                if(e.target.classList.contains('tno-remove-row')){
                    e.target.closest('.tno-repeater-row').remove();
                }
                if(e.target.classList.contains('tno-remove-group')){
                    e.target.closest('.tno-spec-group-box').remove();
                }
            });
        })();
        </script>
        <?php
    }

    /**
     * Save Product Meta Fields
     */
    public static function save_product_meta( int $post_id, \WP_Post $post ): void {
        if ( ! isset( $_POST['tno_pdp_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tno_pdp_meta_nonce'], 'tno_save_pdp_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $text_fields = array(
            'tno_en_title'          => '_tno_en_title',
            'tno_badge_text'        => '_tno_badge_text',
            'tno_seller_name'       => '_tno_seller_name',
            'tno_seller_perf'       => '_tno_seller_perf',
            'tno_warranty_text'     => '_tno_warranty_text',
            'tno_shipping_text'     => '_tno_shipping_text',
            'tno_club_points'       => '_tno_club_points',
            'tno_installment_title' => '_tno_installment_title',
            'tno_installment_sub'   => '_tno_installment_sub',
            'tno_installment_link'  => '_tno_installment_link',
            'tno_free_ship_title'   => '_tno_free_ship_title',
            'tno_ai_pros'           => '_tno_ai_pros',
            'tno_ai_cons'           => '_tno_ai_cons',
        );

        foreach ( $text_fields as $input => $meta_key ) {
            if ( isset( $_POST[ $input ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $input ] ) ) );
            }
        }

        if ( isset( $_POST['tno_return_alert'] ) ) {
            update_post_meta( $post_id, '_tno_return_alert', wp_kses( wp_unslash( $_POST['tno_return_alert'] ), array('strong' => [], 'b' => [],'span' => ['class' => true]) ) );
        }
        if ( isset( $_POST['tno_ai_summary'] ) ) {
            update_post_meta( $post_id, '_tno_ai_summary', sanitize_textarea_field( wp_unslash( $_POST['tno_ai_summary'] ) ) );
        }

        // Save Key Features
        $clean_features = array();
        if ( ! empty( $_POST['tno_key_features'] ) && is_array( $_POST['tno_key_features'] ) ) {
            foreach ( $_POST['tno_key_features'] as $row ) {
                $label = sanitize_text_field( wp_unslash( $row['label'] ?? '' ) );
                $value = sanitize_text_field( wp_unslash( $row['value'] ?? '' ) );
                if ( $label !== '' && $value !== '' ) {
                    $clean_features[] = array( 'label' => $label, 'value' => $value );
                }
            }
        }
        update_post_meta( $post_id, '_tno_key_features', $clean_features );

        // Save Spec Groups
        $clean_groups = array();
        if ( ! empty( $_POST['tno_spec_groups'] ) && is_array( $_POST['tno_spec_groups'] ) ) {
            foreach ( $_POST['tno_spec_groups'] as $group ) {
                $g_title   = sanitize_text_field( wp_unslash( $group['group_title'] ?? '' ) );
                $items_raw = sanitize_textarea_field( wp_unslash( $group['items_raw'] ?? '' ) );
                if ( $g_title !== '' && $items_raw !== '' ) {
                    $parsed_items = array();
                    $lines        = explode( "\n", $items_raw );
                    foreach ( $lines as $line ) {
                        $line = trim( $line );
                        if ( empty( $line ) ) {
                            continue;
                        }
                        $parts = explode( '|', $line, 2 );
                        $parsed_items[] = array(
                            'key' => trim( $parts[0] ?? '' ),
                            'val' => trim( $parts[1] ?? '' ),
                        );
                    }
                    $clean_groups[] = array(
                        'group_title' => $g_title,
                        'items_raw'   => $items_raw,
                        'items'       => $parsed_items,
                    );
                }
            }
        }
        update_post_meta( $post_id, '_tno_spec_groups', $clean_groups );
    }

    /**
     * Get Key Features with fallback to WooCommerce attributes
     */
    public static function get_product_key_features( \WC_Product $product ): array {
        $custom = get_post_meta( $product->get_id(), '_tno_key_features', true );
        if ( ! empty( $custom ) && is_array( $custom ) ) {
            return $custom;
        }

        $fallback = array();
        foreach ( $product->get_attributes() as $attribute ) {
            if ( $attribute->get_variation() ) {
                continue;
            }
            $label = wc_attribute_label( $attribute->get_name() );
            $value = $product->get_attribute( $attribute->get_name() );
            if ( $label && $value ) {
                $fallback[] = array( 'label' => $label, 'value' => $value );
            }
        }
        return $fallback;
    }

    /**
     * Get Grouped Specifications with fallback to WooCommerce attributes
     */
    public static function get_product_spec_groups( \WC_Product $product ): array {
        $groups = get_post_meta( $product->get_id(), '_tno_spec_groups', true );
        if ( ! empty( $groups ) && is_array( $groups ) ) {
            return $groups;
        }

        $items = array();
        if ( $product->has_weight() ) {
            $items[] = array( 'key' => 'وزن', 'val' => wc_format_weight( $product->get_weight() ) );
        }
        if ( $product->has_dimensions() ) {
            $items[] = array( 'key' => 'ابعاد', 'val' => wc_format_dimensions( $product->get_dimensions( false ) ) );
        }
        foreach ( $product->get_attributes() as $attribute ) {
            $items[] = array(
                'key' => wc_attribute_label( $attribute->get_name() ),
                'val' => $product->get_attribute( $attribute->get_name() ),
            );
        }

        if ( empty( $items ) ) {
            return array();
        }

        return array(
            array(
                'group_title' => 'مشخصات فنی',
                'items'       => $items,
            ),
        );
    }

    /**
     * Calculate Aggregated Criteria Ratings for a Product
     */
    public static function get_product_criteria_averages( int $product_id ): array {
        $cache_key = 'tno_criteria_avg_' . $product_id;
        $cached    = get_transient( $cache_key );
        if ( false !== $cached && is_array( $cached ) ) {
            return $cached;
        }

        $criteria_defs = self::get_review_criteria();
        $totals        = array_fill_keys( array_keys( $criteria_defs ), 0 );
        $counts        = array_fill_keys( array_keys( $criteria_defs ), 0 );

        $comments = get_comments( array(
            'post_id' => $product_id,
            'status'  => 'approve',
            'type'    => 'review',
            'number'  => 200,
        ) );

        foreach ( $comments as $c ) {
            $c_scores = get_comment_meta( $c->comment_ID, '_tno_criteria_scores', true );
            if ( is_array( $c_scores ) ) {
                foreach ( $criteria_defs as $k => $label ) {
                    if ( ! empty( $c_scores[ $k ] ) ) {
                        $totals[ $k ] += (float) $c_scores[ $k ];
                        $counts[ $k ]++;
                    }
                }
            }
        }

        $result = array();
        foreach ( $criteria_defs as $k => $label ) {
            $avg = $counts[ $k ] > 0 ? round( $totals[ $k ] / $counts[ $k ], 1 ) : 4.3;
            $result[ $k ] = array(
                'label'   => $label,
                'average' => $avg,
                'percent' => min( 100, round( ( $avg / 5 ) * 100 ) ),
            );
        }

        set_transient( $cache_key, $result, 6 * HOUR_IN_SECONDS );
        return $result;
    }

    public static function flush_product_rating_cache( $comment_id ): void {
        $comment = get_comment( $comment_id );
        if ( $comment && $comment->comment_post_ID ) {
            delete_transient( 'tno_criteria_avg_' . $comment->comment_post_ID );
        }
    }

    /**
     * AJAX: Submit Extended Product Review
     */
    public static function ajax_tno_submit_review(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $rating     = isset( $_POST['rating'] ) ? max( 1, min( 5, absint( $_POST['rating'] ) ) ) : 5;
        $content    = isset( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '';
        $recommend  = isset( $_POST['recommend'] ) ? sanitize_key( $_POST['recommend'] ) : 'yes';
        $is_anon    = ! empty( $_POST['is_anonymous'] );

        if ( ! $product_id || empty( $content ) ) {
            wp_send_json_error( array( 'message' => 'لطفاً متن دیدگاه خود را وارد کنید.' ) );
        }

        $user = wp_get_current_user();
        if ( $user->exists() ) {
            $author_name  = $is_anon ? 'کاربر ناشناس' : $user->display_name;
            $author_email = $user->user_email;
            $user_id      = $user->ID;
        } else {
            $author_name  = ! empty( $_POST['author_name'] ) ? sanitize_text_field( wp_unslash( $_POST['author_name'] ) ) : 'کاربر مهمان';
            $author_email = ! empty( $_POST['author_email'] ) ? sanitize_email( wp_unslash( $_POST['author_email'] ) ) : 'guest@' . wp_parse_url( home_url(), PHP_URL_HOST );
            $user_id      = 0;
        }

        $comment_data = array(
            'comment_post_ID'      => $product_id,
            'comment_author'       => $author_name,
            'comment_author_email' => $author_email,
            'comment_content'      => $content,
            'comment_type'         => 'review',
            'comment_parent'       => 0,
            'user_id'              => $user_id,
            'comment_approved'     => current_user_can( 'manage_options' ) ? 1 : 0,
        );

        $comment_id = wp_insert_comment( $comment_data );
        if ( ! $comment_id ) {
            wp_send_json_error( array( 'message' => 'خطا در ثبت دیدگاه. مجدداً تلاش کنید.' ) );
        }

        // Standard WooCommerce rating
        update_comment_meta( $comment_id, 'rating', $rating );
        update_comment_meta( $comment_id, '_tno_recommend', in_array( $recommend, array( 'yes', 'no', 'unsure' ), true ) ? $recommend : 'yes' );

        // Verified buyer check
        $verified = $user_id > 0 && wc_customer_bought_product( $author_email, $user_id, $product_id );
        update_comment_meta( $comment_id, 'verified', $verified ? 1 : 0 );

        // 6 Criteria Scores
        $criteria_scores = array();
        foreach ( array_keys( self::get_review_criteria() ) as $c_key ) {
            $val = isset( $_POST['criteria'][ $c_key ] ) ? max( 1, min( 5, absint( $_POST['criteria'][ $c_key ] ) ) ) : $rating;
            $criteria_scores[ $c_key ] = $val;
        }
        update_comment_meta( $comment_id, '_tno_criteria_scores', $criteria_scores );

        // Pros & Cons arrays
        $pros = ! empty( $_POST['pros'] ) && is_array( $_POST['pros'] )
            ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['pros'] ) ) )
            : array();
        $cons = ! empty( $_POST['cons'] ) && is_array( $_POST['cons'] )
            ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['cons'] ) ) )
            : array();

        update_comment_meta( $comment_id, '_tno_pros', array_values( $pros ) );
        update_comment_meta( $comment_id, '_tno_cons', array_values( $cons ) );

        // Optional Image Upload
        if ( ! empty( $_FILES['review_images']['name'][0] ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $uploaded_urls = array();
            $files         = $_FILES['review_images'];
            $count         = min( 4, count( $files['name'] ) );

            for ( $i = 0; $i < $count; $i++ ) {
                if ( $files['error'][ $i ] !== UPLOAD_ERR_OK || $files['size'][ $i ] > 3 * 1024 * 1024 ) {
                    continue;
                }
                $file_array = array(
                    'name'     => sanitize_file_name( $files['name'][ $i ] ),
                    'type'     => $files['type'][ $i ],
                    'tmp_name' => $files['tmp_name'][ $i ],
                    'error'    => $files['error'][ $i ],
                    'size'     => $files['size'][ $i ],
                );
                $movefile = wp_handle_upload( $file_array, array( 'test_form' => false, 'mimes' => array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp' ) ) );
                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    $uploaded_urls[] = esc_url_raw( $movefile['url'] );
                }
            }
            if ( ! empty( $uploaded_urls ) ) {
                update_comment_meta( $comment_id, '_tno_review_images', $uploaded_urls );
            }
        }

        self::flush_product_rating_cache( $comment_id );

        wp_send_json_success( array(
            'message' => current_user_can( 'manage_options' )
                ? 'دیدگاه شما با موفقیت ثبت و منتشر شد.'
                : 'دیدگاه شما ثبت شد و پس از تایید مدیریت نمایش داده می‌شود.',
        ) );
    }

    /**
     * AJAX: Submit Question or Answer
     */
    public static function ajax_tno_submit_question(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
        $parent_id  = isset( $_POST['parent_id'] ) ? absint( $_POST['parent_id'] ) : 0;
        $content    = isset( $_POST['question_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['question_text'] ) ) : '';

        if ( ! $product_id || empty( $content ) ) {
            wp_send_json_error( array( 'message' => 'متن پرسش یا پاسخ نمی‌تواند خالی باشد.' ) );
        }

        $user         = wp_get_current_user();
        $author_name  = $user->exists() ? $user->display_name : sanitize_text_field( wp_unslash( $_POST['author_name'] ?? 'کاربر مهمان' ) );
        $author_email = $user->exists() ? $user->user_email : 'qa@' . wp_parse_url( home_url(), PHP_URL_HOST );

        $comment_id = wp_insert_comment( array(
            'comment_post_ID'      => $product_id,
            'comment_author'       => $author_name,
            'comment_author_email' => $author_email,
            'comment_content'      => $content,
            'comment_type'         => 'tno_qa',
            'comment_parent'       => $parent_id,
            'user_id'              => $user->exists() ? $user->ID : 0,
            'comment_approved'     => current_user_can( 'manage_options' ) ? 1 : 0,
        ) );

        if ( ! $comment_id ) {
            wp_send_json_error( array( 'message' => 'خطا در ثبت اطلاعات.' ) );
        }

        wp_send_json_success( array(
            'message' => $parent_id > 0
                ? 'پاسخ شما ثبت شد و پس از بررسی منتشر خواهد شد.'
                : 'پرسش شما با موفقیت ثبت شد و پس از تایید نمایش داده می‌شود.',
        ) );
    }

    /**
     * AJAX: Helpful Like / Dislike Vote for Reviews & Q&A
     */
    public static function ajax_tno_vote_comment(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );

        $comment_id = isset( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
        $vote_type  = isset( $_POST['vote_type'] ) && $_POST['vote_type'] === 'dislike' ? 'dislike' : 'like';

        if ( ! $comment_id ) {
            wp_send_json_error();
        }

        $cookie_key = 'tno_voted_' . $comment_id;
        if ( isset( $_COOKIE[ $cookie_key ] ) ) {
            wp_send_json_error( array( 'message' => 'شما قبلاً به این مورد رای داده‌اید.' ) );
        }

        $meta_key = $vote_type === 'like' ? '_tno_likes' : '_tno_dislikes';
        $current  = (int) get_comment_meta( $comment_id, $meta_key, true );
        $updated  = $current + 1;
        update_comment_meta( $comment_id, $meta_key, $updated );

        setcookie( $cookie_key, '1', time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

        wp_send_json_success( array(
            'likes'    => (int) get_comment_meta( $comment_id, '_tno_likes', true ),
            'dislikes' => (int) get_comment_meta( $comment_id, '_tno_dislikes', true ),
        ) );
    }

    /**
     * Render Single Review Card HTML (used both initially and via AJAX sort)
     */
    public static function render_review_card( \WP_Comment $comment ): void {
        $rating     = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
        $verified   = (bool) get_comment_meta( $comment->comment_ID, 'verified', true );
        $recommend  = get_comment_meta( $comment->comment_ID, '_tno_recommend', true );
        $pros       = get_comment_meta( $comment->comment_ID, '_tno_pros', true );
        $cons       = get_comment_meta( $comment->comment_ID, '_tno_cons', true );
        $images     = get_comment_meta( $comment->comment_ID, '_tno_review_images', true );
        $likes      = (int) get_comment_meta( $comment->comment_ID, '_tno_likes', true );
        $dislikes   = (int) get_comment_meta( $comment->comment_ID, '_tno_dislikes', true );
        $seller     = get_post_meta( $comment->comment_post_ID, '_tno_seller_name', true ) ?: get_bloginfo( 'name' );
        ?>
        <article class="tno-review-card" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
            <header class="tno-review-header">
                <div class="tno-review-user-meta">
                    <span class="tno-user-avatar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <strong class="tno-review-author"><?php echo esc_html( $comment->comment_author ); ?></strong>
                    <?php if ( $verified ) : ?>
                        <span class="tno-buyer-badge">خریدار</span>
                    <?php endif; ?>
                    <span class="tno-review-date"><?php echo esc_html( get_comment_date( 'j F Y', $comment ) ); ?></span>
                </div>
                <?php if ( $rating >= 1 && $rating <= 5 ) : ?>
                <div class="tno-review-stars" aria-label="امتیاز <?php echo esc_attr( $rating ); ?> از 5">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <span class="tno-star <?php echo $i <= $rating ? 'is-filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </header>

            <?php if ( $recommend === 'yes' ) : ?>
                <div class="tno-recommend-tag is-yes">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    خرید این محصول را پیشنهاد می‌کنم
                </div>
            <?php elseif ( $recommend === 'no' ) : ?>
                <div class="tno-recommend-tag is-no">
                    خرید این محصول را پیشنهاد نمی‌کنم
                </div>
            <?php endif; ?>

            <div class="tno-review-body">
                <?php echo wpautop( esc_html( $comment->comment_content ) ); ?>
            </div>

            <?php if ( ( ! empty( $pros ) && is_array( $pros ) ) || ( ! empty( $cons ) && is_array( $cons ) ) ) : ?>
                <div class="tno-pros-cons-list">
                    <?php if ( ! empty( $pros ) && is_array( $pros ) ) : ?>
                        <?php foreach ( $pros as $pro ) : ?>
                            <div class="tno-pc-item is-pro"><span class="tno-pc-sign">+</span> <?php echo esc_html( $pro ); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ( ! empty( $cons ) && is_array( $cons ) ) : ?>
                        <?php foreach ( $cons as $con ) : ?>
                            <div class="tno-pc-item is-con"><span class="tno-pc-sign">−</span> <?php echo esc_html( $con ); ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $images ) && is_array( $images ) ) : ?>
                <div class="tno-review-Attachments">
                    <?php foreach ( $images as $img_url ) : ?>
                        <img src="<?php echo esc_url( $img_url ); ?>" alt="تصویر ارسالی کاربر" class="tno-review-thumb js-open-lightbox">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <footer class="tno-review-footer">
                <div class="tno-review-seller-tag">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    <span><?php echo esc_html( $seller ); ?></span>
                </div>
                <div class="tno-helpful-box">
                    <span class="tno-helpful-label">آیا این دیدگاه برایتان مفید بود؟</span>
                    <button type="button" class="tno-vote-btn js-vote-comment" data-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-type="like">
                        <span class="tno-vote-count"><?php echo esc_html( $likes ); ?></span> 👍
                    </button>
                    <button type="button" class="tno-vote-btn js-vote-comment" data-id="<?php echo esc_attr( $comment->comment_ID ); ?>" data-type="dislike">
                        <span class="tno-vote-count"><?php echo esc_html( $dislikes ); ?></span> 👎
                    </button>
                </div>
            </footer>
        </article>
        <?php
    }

    /**
     * Render Single Question Card + Replies
     */
    public static function render_question_card( \WP_Comment $question ): void {
        $replies = get_comments( array(
            'post_id'  => $question->comment_post_ID,
            'parent'   => $question->comment_ID,
            'status'   => 'approve',
            'type__in' => array( 'tno_answer', 'tno_qa' ),
            'orderby'  => 'comment_date_gmt',
            'order'    => 'ASC',
        ) );
        $extra_id = 'tno-qa-extra-' . $question->comment_ID;
        ?>
        <article class="tno-qa-card" data-question-id="<?php echo esc_attr( $question->comment_ID ); ?>">
            <h3 class="tno-question-text"><?php echo esc_html( $question->comment_content ); ?></h3>
            <?php if ( ! empty( $replies ) ) : ?>
                <div class="tno-answers-list">
                    <?php foreach ( $replies as $index => $reply ) :
                        $r_likes    = (int) get_comment_meta( $reply->comment_ID, '_tno_likes', true );
                        $r_dislikes = (int) get_comment_meta( $reply->comment_ID, '_tno_dislikes', true );
                        if ( 2 === $index ) : ?><div id="<?php echo esc_attr( $extra_id ); ?>" class="tno-extra-answers" hidden><?php endif; ?>
                        <div class="tno-answer-item">
                            <div class="tno-answer-meta">
                                <span><?php echo esc_html( $reply->comment_author ); ?></span>
                                <span class="tno-a-badge"><?php echo $reply->user_id ? 'کاربر ترندینو' : 'کاربر ناشناس'; ?></span>
                            </div>
                            <div class="tno-answer-body"><?php echo wpautop( esc_html( $reply->comment_content ) ); ?></div>
                            <div class="tno-answer-footer">
                                <time class="tno-answer-date"><?php echo esc_html( get_comment_date( 'Y/m/d', $reply ) ); ?></time>
                                <div class="tno-helpful-box">
                                    <button type="button" class="tno-vote-btn js-vote-comment" data-id="<?php echo esc_attr( $reply->comment_ID ); ?>" data-type="like" aria-label="پسندیدن پاسخ">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 10v11H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3Zm0 0 4-8a3 3 0 0 1 3 3v4h5.2a2 2 0 0 1 2 2.3l-1.3 8a2 2 0 0 1-2 1.7H7"/></svg><span class="tno-vote-count"><?php echo esc_html( number_format_i18n( $r_likes ) ); ?></span>
                                    </button>
                                    <button type="button" class="tno-vote-btn js-vote-comment" data-id="<?php echo esc_attr( $reply->comment_ID ); ?>" data-type="dislike" aria-label="نپسندیدن پاسخ">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 14V3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3Zm0 0-4 8a3 3 0 0 1-3-3v-4H4.8a2 2 0 0 1-2-2.3l1.3-8a2 2 0 0 1 2-1.7H17"/></svg><span class="tno-vote-count"><?php echo esc_html( number_format_i18n( $r_dislikes ) ); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ( count( $replies ) > 2 ) : ?></div><?php endif; ?>
                </div>
                <?php if ( count( $replies ) > 2 ) : ?>
                    <button type="button" class="tno-qa-more js-toggle-answers" aria-expanded="false" aria-controls="<?php echo esc_attr( $extra_id ); ?>"><span>مشاهده پاسخ‌های دیگر</span><span class="tno-qa-chevron" aria-hidden="true">⌄</span></button>
                <?php endif; ?>
            <?php endif; ?>
            <div class="tno-qa-actions">
                <button type="button" class="tno-reply-q-btn js-open-answer-modal" data-tno-modal="answer" data-question-id="<?php echo esc_attr( (string) $question->comment_ID ); ?>" data-question-text="<?php echo esc_attr( wp_strip_all_tags( $question->comment_content ) ); ?>" data-parent="<?php echo esc_attr( $question->comment_ID ); ?>">
                    <span aria-hidden="true">＋</span> ثبت پاسخ
                </button>
            </div>
        </article>
        <?php
    }

    /**
     * AJAX: Filter / Sort Reviews
     */
    public static function ajax_tno_filter_comments(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );

        $product_id = absint( $_POST['product_id'] ?? 0 );
        $sort       = sanitize_key( $_POST['sort'] ?? 'newest' );

        $args = array(
            'post_id' => $product_id,
            'status'  => 'approve',
            'type'    => 'review',
            'orderby' => 'comment_date_gmt',
            'order'   => 'DESC',
        );

        if ( $sort === 'buyers' ) {
            $args['meta_query'] = array(
                array(
                    'key'   => 'verified',
                    'value' => 1,
                ),
            );
        }

        $comments = get_comments( $args );
        if ( $sort === 'helpful' ) {
            usort( $comments, static function ( $a, $b ) {
                $difference = (int) get_comment_meta( $b->comment_ID, '_tno_likes', true ) <=> (int) get_comment_meta( $a->comment_ID, '_tno_likes', true );
                return $difference ?: strcmp( $b->comment_date_gmt, $a->comment_date_gmt );
            } );
        }
        ob_start();
        if ( ! empty( $comments ) ) {
            foreach ( $comments as $comment ) {
                self::render_review_card( $comment );
            }
        } else {
            echo '<p class="tno-empty-notice">هیچ دیدگاهی با این فیلتر یافت نشد.</p>';
        }
        wp_send_json_success( array( 'html' => ob_get_clean() ) );
    }

    /**
     * AJAX: Filter / Sort Questions
     */
    public static function ajax_tno_filter_questions(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );

        $product_id = absint( $_POST['product_id'] ?? 0 );
        $sort       = sanitize_key( $_POST['sort'] ?? 'newest' );

        $args = array(
            'post_id' => $product_id,
            'status'  => 'approve',
            'type__in' => array( 'tno_question', 'tno_qa' ),
            'parent'  => 0,
            'orderby' => 'comment_date_gmt',
            'order'   => 'DESC',
        );

        $questions = get_comments( $args );
        if ( $sort === 'buyers' ) {
            $questions = array_values( array_filter( $questions, static function ( $question ) use ( $product_id ) {
                return wc_customer_bought_product( $question->comment_author_email, $question->user_id, $product_id );
            } ) );
        } elseif ( $sort === 'answers' ) {
            $answer_counts = array();
            foreach ( $questions as $question ) {
                $answer_counts[ $question->comment_ID ] = (int) get_comments( array(
                    'post_id' => $product_id, 'parent' => $question->comment_ID,
                    'status' => 'approve', 'type__in' => array( 'tno_answer', 'tno_qa' ), 'count' => true,
                ) );
            }
            usort( $questions, static function ( $a, $b ) use ( $answer_counts ) {
                $difference = $answer_counts[ $b->comment_ID ] <=> $answer_counts[ $a->comment_ID ];
                return $difference ?: strcmp( $b->comment_date_gmt, $a->comment_date_gmt );
            } );
        }
        ob_start();
        if ( ! empty( $questions ) ) {
            foreach ( $questions as $q ) {
                self::render_question_card( $q );
            }
        } else {
            echo '<p class="tno-empty-notice">هنوز پرسشی ثبت نشده است.</p>';
        }
        wp_send_json_success( array( 'html' => ob_get_clean() ) );
    }

    /**
     * AJAX: Toggle Wishlist (User Meta or Cookie)
     */
    public static function ajax_tno_toggle_wishlist(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );
        $product_id = absint( $_POST['product_id'] ?? 0 );
        if ( ! $product_id ) {
            wp_send_json_error();
        }

        if ( is_user_logged_in() ) {
            $uid  = get_current_user_id();
            $list = get_user_meta( $uid, '_tno_wishlist', true );
            $list = is_array( $list ) ? $list : array();
            if ( in_array( $product_id, $list, true ) ) {
                $list   = array_diff( $list, array( $product_id ) );
                $active = false;
            } else {
                $list[] = $product_id;
                $active = true;
            }
            update_user_meta( $uid, '_tno_wishlist', array_values( $list ) );
        } else {
            $active = true;
        }

        wp_send_json_success( array( 'active' => $active ) );
    }

    /**
     * AJAX: Better Price Report Submission
     */
    public static function ajax_tno_price_report(): void {
        check_ajax_referer( 'tno_pdp_nonce', 'nonce' );
        $product_id = absint( $_POST['product_id'] ?? 0 );
        $price      = sanitize_text_field( wp_unslash( $_POST['found_price'] ?? '' ) );
        $store      = sanitize_text_field( wp_unslash( $_POST['store_name'] ?? '' ) );

        if ( ! $product_id || empty( $price ) ) {
            wp_send_json_error( array( 'message' => 'لطفاً قیمت مشاهده شده را وارد کنید.' ) );
        }

        $reports   = get_post_meta( $product_id, '_tno_price_reports', true );
        $reports   = is_array( $reports ) ? $reports : array();
        $reports[] = array(
            'price' => $price,
            'store' => $store,
            'date'  => current_time( 'mysql' ),
        );
        update_post_meta( $product_id, '_tno_price_reports', $reports );

        wp_send_json_success( array( 'message' => 'گزارش قیمت با موفقیت ثبت شد. سپاس از همراهی شما.' ) );
    }

    /**
     * Admin Comment Meta Box for Editing Pros/Cons & Scores
     */
    public static function register_comment_meta_box(): void {
        add_meta_box(
            'tno_comment_meta',
            'جزئیات دیدگاه محصول (ترندینو)',
            array( __CLASS__, 'render_comment_meta_box' ),
            'comment',
            'normal',
            'high'
        );
    }

    public static function render_comment_meta_box( \WP_Comment $comment ): void {
        wp_nonce_field( 'tno_save_comment_meta', 'tno_comment_meta_nonce' );
        $recommend = get_comment_meta( $comment->comment_ID, '_tno_recommend', true ) ?: 'yes';
        $pros      = get_comment_meta( $comment->comment_ID, '_tno_pros', true );
        $cons      = get_comment_meta( $comment->comment_ID, '_tno_cons', true );
        $pros_str  = is_array( $pros ) ? implode( "\n", $pros ) : '';
        $cons_str  = is_array( $cons ) ? implode( "\n", $cons ) : '';
        ?>
        <p>
            <label><strong>پیشنهاد خرید:</strong></label>
            <select name="tno_recommend">
                <option value="yes" <?php selected( $recommend, 'yes' ); ?>>پیشنهاد می‌کنم</option>
                <option value="no" <?php selected( $recommend, 'no' ); ?>>پیشنهاد نمی‌کنم</option>
                <option value="unsure" <?php selected( $recommend, 'unsure' ); ?>>مطمئن نیستم</option>
            </select>
        </p>
        <p>
            <label><strong>نقاط قوت (هر مورد در یک خط):</strong></label><br>
            <textarea name="tno_pros_raw" rows="3" style="width:100%;"><?php echo esc_textarea( $pros_str ); ?></textarea>
        </p>
        <p>
            <label><strong>نقاط ضعف (هر مورد در یک خط):</strong></label><br>
            <textarea name="tno_cons_raw" rows="3" style="width:100%;"><?php echo esc_textarea( $cons_str ); ?></textarea>
        </p>
        <?php
    }

    public static function save_admin_comment_meta( int $comment_id ): void {
        if ( ! isset( $_POST['tno_comment_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tno_comment_meta_nonce'], 'tno_save_comment_meta' ) ) {
            return;
        }
        if ( isset( $_POST['tno_recommend'] ) ) {
            update_comment_meta( $comment_id, '_tno_recommend', sanitize_key( $_POST['tno_recommend'] ) );
        }
        if ( isset( $_POST['tno_pros_raw'] ) ) {
            $lines = array_filter( array_map( 'sanitize_text_field', explode( "\n", wp_unslash( $_POST['tno_pros_raw'] ) ) ) );
            update_comment_meta( $comment_id, '_tno_pros', array_values( $lines ) );
        }
        if ( isset( $_POST['tno_cons_raw'] ) ) {
            $lines = array_filter( array_map( 'sanitize_text_field', explode( "\n", wp_unslash( $_POST['tno_cons_raw'] ) ) ) );
            update_comment_meta( $comment_id, '_tno_cons', array_values( $lines ) );
        }
    }

    /**
     * Render SEO-friendly Breadcrumb (Microdata + JSON-LD Schema.org BreadcrumbList)
     * Matches UI: Home Icon > Categories > Current Product Title
     */
    public static function render_breadcrumb( \WC_Product $product ): void {
        $items    = array();
        $position = 1;

        // 1. Home Item
        $items[] = array(
            'name'     => get_bloginfo( 'name' ) ?: 'خانه',
            'url'      => home_url( '/' ),
            'is_home'  => true,
            'is_last'  => false,
            'position' => $position++,
        );

        // 2. Product Category Hierarchy (Primary / Deepest Category + Ancestors)
        $terms = wc_get_product_terms(
            $product->get_id(),
            'product_cat',
            array(
                'orderby' => 'parent',
                'order'   => 'DESC',
            )
        );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $main_term = $terms[0];
            $ancestors = get_ancestors( $main_term->term_id, 'product_cat' );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor_id ) {
                $ancestor = get_term( $ancestor_id, 'product_cat' );
                if ( $ancestor && ! is_wp_error( $ancestor ) ) {
                    $term_link = get_term_link( $ancestor );
                    if ( ! is_wp_error( $term_link ) ) {
                        $items[] = array(
                            'name'     => $ancestor->name,
                            'url'      => $term_link,
                            'is_home'  => false,
                            'is_last'  => false,
                            'position' => $position++,
                        );
                    }
                }
            }

            $main_term_link = get_term_link( $main_term );
            if ( ! is_wp_error( $main_term_link ) ) {
                $items[] = array(
                    'name'     => $main_term->name,
                    'url'      => $main_term_link,
                    'is_home'  => false,
                    'is_last'  => false,
                    'position' => $position++,
                );
            }
        }

        // 3. Current Product Item
        $items[] = array(
            'name'     => $product->get_name(),
            'url'      => get_permalink( $product->get_id() ),
            'is_home'  => false,
            'is_last'  => true,
            'position' => $position,
        );

        // Build JSON-LD Schema Payload
        $json_ld = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array(),
        );

        foreach ( $items as $item ) {
            $json_ld['itemListElement'][] = array(
                '@type'    => 'ListItem',
                'position' => $item['position'],
                'name'     => $item['name'],
                'item'     => $item['url'],
            );
        }
        ?>
        <nav class="tno-pdp-breadcrumb container" aria-label="Breadcrumb">
            <ol class="tno-bc-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                <?php foreach ( $items as $item ) : ?>
                    <li class="tno-bc-item <?php echo $item['is_last'] ? 'is-current' : ''; ?>"
                        itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">

                        <?php if ( ! $item['is_last'] ) : ?>
                            <a href="<?php echo esc_url( $item['url'] ); ?>" class="tno-bc-link" itemprop="item" <?php echo $item['is_home'] ? 'aria-label="' . esc_attr( $item['name'] ) . '"' : ''; ?>>
                                <?php if ( $item['is_home'] ) : ?>
                                    <!-- Outline Home Icon matching screenshot -->
                                    <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.5961 6.2307L8.34609 0.333828C8.34304 0.331162 8.34017 0.328291 8.3375 0.325234C8.10739 0.115963 7.80752 0 7.49648 0C7.18545 0 6.88558 0.115963 6.65547 0.325234L6.64688 0.333828L0.403906 6.2307C0.276561 6.3478 0.174909 6.49006 0.105384 6.64848C0.0358588 6.80689 -2.62835e-05 6.97801 1.44436e-08 7.15102V14.3737C1.44436e-08 14.7052 0.131696 15.0231 0.366116 15.2576C0.600537 15.492 0.918479 15.6237 1.25 15.6237H5C5.33152 15.6237 5.64946 15.492 5.88388 15.2576C6.1183 15.0231 6.25 14.7052 6.25 14.3737V10.6237H8.75V14.3737C8.75 14.7052 8.8817 15.0231 9.11612 15.2576C9.35054 15.492 9.66848 15.6237 10 15.6237H13.75C14.0815 15.6237 14.3995 15.492 14.6339 15.2576C14.8683 15.0231 15 14.7052 15 14.3737V7.15102C15 6.97801 14.9641 6.80689 14.8946 6.64848C14.8251 6.49006 14.7234 6.3478 14.5961 6.2307ZM13.75 14.3737H10V10.6237C10 10.2922 9.8683 9.97421 9.63388 9.73979C9.39946 9.50537 9.08152 9.37367 8.75 9.37367H6.25C5.91848 9.37367 5.60054 9.50537 5.36612 9.73979C5.1317 9.97421 5 10.2922 5 10.6237V14.3737H1.25V7.15102L1.25859 7.1432L7.5 1.24867L13.7422 7.14164L13.7508 7.14945L13.75 14.3737Z" fill="#212121"/>
                                    </svg>
                                    <meta itemprop="name" content="<?php echo esc_attr( $item['name'] ); ?>" />
                                <?php else : ?>
                                    <span itemprop="name"><?php echo esc_html( $item['name'] ); ?></span>
                                <?php endif; ?>
                            </a>

                            <!-- Left Chevron Divider -->
                            <span class="tno-bc-sep" aria-hidden="true">
                                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="stroke0_260_521" fill="white">
                                        <path d="M6.4332 0.182465L0.183197 6.43246C0.125087 6.49051 0.0789871 6.55944 0.0475345 6.63531C0.0160818 6.71119 -0.000106335 6.79252 -0.000106335 6.87465C-0.000106335 6.95679 0.0160818 7.03812 0.0475345 7.11399C0.0789871 7.18986 0.125087 7.25879 0.183197 7.31684L6.4332 13.5668C6.55047 13.6841 6.70953 13.75 6.87538 13.75C7.04124 13.75 7.2003 13.6841 7.31757 13.5668C7.43485 13.4496 7.50073 13.2905 7.50073 13.1247C7.50073 12.9588 7.43485 12.7997 7.31757 12.6825L1.50898 6.87465L7.31757 1.06684C7.37564 1.00877 7.4217 0.939833 7.45313 0.863962C7.48456 0.788092 7.50073 0.706774 7.50073 0.624652C7.50073 0.54253 7.48456 0.461212 7.45313 0.385342C7.4217 0.309471 7.37564 0.240533 7.31757 0.182465C7.2595 0.124395 7.19057 0.0783329 7.1147 0.0469065C7.03882 0.01548 6.95751 -0.000696182 6.87538 -0.000696182C6.79326 -0.000696182 6.71195 0.01548 6.63607 0.0469065C6.5602 0.0783329 6.49127 0.124395 6.4332 0.182465Z"/>
                                    </mask>
                                    <path d="M6.4332 0.182464L0.183197 6.43246C0.125087 6.49051 0.0789873 6.55944 0.0475346 6.63531C0.0160819 6.71119 -0.000106215 6.79252 -0.000106215 6.87465C-0.000106215 6.95679 0.0160819 7.03812 0.0475346 7.11399C0.0789873 7.18986 0.125087 7.25879 0.183197 7.31684L6.4332 13.5668C6.55047 13.6841 6.70953 13.75 6.87538 13.75C7.04124 13.75 7.2003 13.6841 7.31757 13.5668C7.43485 13.4496 7.50073 13.2905 7.50073 13.1247C7.50073 12.9588 7.43485 12.7997 7.31757 12.6825L1.50898 6.87465L7.31757 1.06684C7.37564 1.00877 7.4217 0.939833 7.45313 0.863962C7.48456 0.788092 7.50073 0.706774 7.50073 0.624652C7.50073 0.54253 7.48456 0.461212 7.45313 0.385342C7.4217 0.309471 7.37564 0.240533 7.31757 0.182464C7.2595 0.124395 7.19057 0.0783331 7.1147 0.0469065C7.03882 0.0154798 6.95751 -0.000696182 6.87538 -0.000696182C6.79326 -0.000696182 6.71195 0.0154798 6.63607 0.0469065C6.5602 0.0783331 6.49127 0.124395 6.4332 0.182464Z" stroke-width="1.875" stroke="#707072" mask="url(#stroke0_260_521)"/>
                                </svg>
                            </span>
                        <?php else : ?>
                            <span class="tno-bc-current" itemprop="name" aria-current="page"><?php echo esc_html( $item['name'] ); ?></span>
                            <link itemprop="item" href="<?php echo esc_url( $item['url'] ); ?>" />
                        <?php endif; ?>

                        <meta itemprop="position" content="<?php echo esc_attr( $item['position'] ); ?>" />
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <script type="application/ld+json"><?php echo wp_json_encode( $json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
        <?php
    }
}

TNO_PDP_Manager::init();