<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ۱. افزودن فیلدهای سفارشی در بخش نمایش > فهرست‌ها
 */
add_action( 'wp_nav_menu_item_custom_fields', 'tno_megamenu_custom_fields', 10, 4 );
function tno_megamenu_custom_fields( $item_id, $item, $depth, $args ) {
    $is_megamenu = get_post_meta( $item_id, '_tno_is_megamenu', true );
    $icon_id     = get_post_meta( $item_id, '_tno_menu_icon_id', true );
    $icon_url    = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
    ?>
    
    <!-- فیلد فعالسازی مگامنو (مخصوص سطح ۰ - آیتم‌های اصلی هدر) -->
    <p class="field-tno-megamenu description description-wide" style="margin: 10px 0;">
        <label for="edit-menu-item-megamenu-<?php echo esc_attr( $item_id ); ?>">
            <input type="checkbox" id="edit-menu-item-megamenu-<?php echo esc_attr( $item_id ); ?>" name="menu_item_tno_megamenu[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_megamenu, '1' ); ?> />
            <strong>فعالسازی مگامنو برای این آیتم (فقط سطح اول)</strong>
        </label>
    </p>

    <!-- فیلد انتخاب تصویر/آیکون (مخصوص تب‌های سطح ۱ مگامنو) -->
    <div class="field-tno-icon description description-wide" style="margin: 10px 0;">
        <span class="description">تصویر منو (نمایش در ستون راست مگامنو):</span>
        <div class="tno-menu-icon-wrapper" style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">
            <div class="tno-icon-preview" style="width: 44px; height: 44px; border: 1px solid #ddd; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f9f9f9;">
                <?php if ( $icon_url ) : ?>
                    <img src="<?php echo esc_url( $icon_url ); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="" />
                <?php endif; ?>
            </div>
            <input type="hidden" class="tno-menu-icon-id" name="menu_item_tno_icon_id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $icon_id ); ?>" />
            <button type="button" class="button tno-upload-icon-btn">انتخاب عکس</button>
            <button type="button" class="button tno-remove-icon-btn" style="<?php echo $icon_id ? '' : 'display:none;'; ?> color: #b32d2e;">حذف</button>
        </div>
    </div>
    <?php
}

/**
 * ۲. ذخیره متادیتای آیتم‌های منو
 */
add_action( 'wp_update_nav_menu_item', 'tno_save_megamenu_custom_fields', 10, 3 );
function tno_save_megamenu_custom_fields( $menu_id, $menu_item_db_id, $args ) {
    // ذخیره وضعیت مگامنو
    if ( isset( $_POST['menu_item_tno_megamenu'][ $menu_item_db_id ] ) ) {
        update_post_meta( $menu_item_db_id, '_tno_is_megamenu', '1' );
    } else {
        delete_post_meta( $menu_item_db_id, '_tno_is_megamenu' );
    }

    // ذخیره آیدی تصویر
    if ( isset( $_POST['menu_item_tno_icon_id'][ $menu_item_db_id ] ) ) {
        $icon_id = absint( $_POST['menu_item_tno_icon_id'][ $menu_item_db_id ] );
        if ( $icon_id > 0 ) {
            update_post_meta( $menu_item_db_id, '_tno_menu_icon_id', $icon_id );
        } else {
            delete_post_meta( $menu_item_db_id, '_tno_menu_icon_id' );
        }
    }
}

/**
 * ۳. لود کردن Media Uploader وردپرس در صفحه فهرست‌ها
 */
add_action( 'admin_enqueue_scripts', 'tno_admin_nav_menus_scripts' );
function tno_admin_nav_menus_scripts( $hook ) {
    if ( 'nav-menus.php' !== $hook ) {
        return;
    }
    wp_enqueue_media();
    
    $inline_js = "
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('tno-upload-icon-btn')) {
            e.preventDefault();
            const btn = e.target;
            const wrapper = btn.closest('.tno-menu-icon-wrapper');
            const input = wrapper.querySelector('.tno-menu-icon-id');
            const preview = wrapper.querySelector('.tno-icon-preview');
            const removeBtn = wrapper.querySelector('.tno-remove-icon-btn');

            const frame = wp.media({
                title: 'انتخاب تصویر منو',
                button: { text: 'استفاده از این تصویر' },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                input.value = attachment.id;
                const imgUrl = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
                preview.innerHTML = '<img src=\"' + imgUrl + '\" style=\"max-width:100%;max-height:100%;object-fit:contain;\" />';
                removeBtn.style.display = 'inline-block';
            });

            frame.open();
        }

        if (e.target && e.target.classList.contains('tno-remove-icon-btn')) {
            e.preventDefault();
            const btn = e.target;
            const wrapper = btn.closest('.tno-menu-icon-wrapper');
            wrapper.querySelector('.tno-menu-icon-id').value = '';
            wrapper.querySelector('.tno-icon-preview').innerHTML = '';
            btn.style.display = 'none';
        }
    });
    ";
    wp_add_inline_script( 'media-editor', $inline_js );
}

/**
 * ۴. بازنویسی Walker منوی هدر برای پشتیبانی کامل از مگامنو و منوی عادی
 */
class Header_Menu_Walker extends Walker_Nav_Menu {

    public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element ) {
            return;
        }

        $id_field = $this->db_fields['id'];
        $id       = $element->$id_field;

        // بررسی اینکه آیا آیتم سطح اول، مگامنو است و زیرمنو دارد یا خیر
        if ( 0 === $depth && get_post_meta( $id, '_tno_is_megamenu', true ) === '1' && ! empty( $children_elements[ $id ] ) ) {
            $element->classes[] = 'has-tno-megamenu';
            $element->classes[] = 'menu-item-has-children';

            $cb_args = array_merge( array( &$output, $element, $depth ), $args );
            call_user_func_array( array( $this, 'start_el' ), $cb_args );

            // رندر اختصاصی ساختار مگامنو
            $output .= $this->render_megamenu_box( $element, $children_elements );

            // پاک کردن فرزندان برای جلوگیری از رندر تکراری توسط وردپرس
            unset( $children_elements[ $id ] );

            $cb_args = array_merge( array( &$output, $element, $depth ), $args );
            call_user_func_array( array( $this, 'end_el' ), $cb_args );
            return;
        }

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    /**
     * تولید HTML مگامنو (ستون تب‌ها در راست + محتوای دسته‌ها در چپ)
     */
    private function render_megamenu_box( $parent_item, &$children_elements ) {
        $parent_id = $parent_item->ID;
        $tabs      = isset( $children_elements[ $parent_id ] ) ? $children_elements[ $parent_id ] : array();

        if ( empty( $tabs ) ) {
            return '';
        }

        ob_start();
        ?>
        <div class="tno-megamenu-dropdown">
            <div class="tno-megamenu-inner">
                
                <!-- سایدبار سمت راست: دسته‌های اصلی (سطح ۱) به همراه عکس -->
                <ul class="tno-mm-tabs">
                    <?php foreach ( $tabs as $index => $tab ) :
                        $active_class = ( 0 === $index ) ? ' active' : '';
                        $icon_id      = get_post_meta( $tab->ID, '_tno_menu_icon_id', true );
                        $icon_url     = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
                    ?>
                        <li class="tno-mm-tab-item<?php echo esc_attr( $active_class ); ?>" data-tab-target="tno-mm-panel-<?php echo esc_attr( $tab->ID ); ?>">
                            <a href="<?php echo esc_url( $tab->url ); ?>">
                                <?php if ( $icon_url ) : ?>
                                    <span class="tno-mm-tab-icon">
                                        <img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $tab->title ); ?>" loading="lazy" />
                                    </span>
                                <?php endif; ?>
                                <span class="tno-mm-tab-title"><?php echo esc_html( $tab->title ); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- پنل سمت چپ: زیرمنوها (سطح ۲ و ۳) -->
                <div class="tno-mm-panels-wrapper">
                    <?php foreach ( $tabs as $index => $tab ) :
                        $active_class = ( 0 === $index ) ? ' active' : '';
                        $headings     = isset( $children_elements[ $tab->ID ] ) ? $children_elements[ $tab->ID ] : array();
                    ?>
                        <div class="tno-mm-panel<?php echo esc_attr( $active_class ); ?>" id="tno-mm-panel-<?php echo esc_attr( $tab->ID ); ?>">
                            
                            <!-- لینک بالای پنل: همه محصولات دسته -->
                            <a href="<?php echo esc_url( $tab->url ); ?>" class="tno-mm-all-link">
                                <span class="tno-mm-dot"></span>
                                <span>همه ی محصولات <?php echo esc_html( $tab->title ); ?></span>
                                <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>

                            <!-- ستون‌های دسته‌بندی (عناوین سطح ۲ و لینک‌های سطح ۳) -->
                            <?php if ( ! empty( $headings ) ) : ?>
                                <div class="tno-mm-columns">
                                    <?php foreach ( $headings as $heading ) :
                                        $sub_links = isset( $children_elements[ $heading->ID ] ) ? $children_elements[ $heading->ID ] : array();
                                    ?>
                                        <div class="tno-mm-group">
                                            <a href="<?php echo esc_url( $heading->url ); ?>" class="tno-mm-heading">
                                                <span class="tno-mm-dot"></span>
                                                <span class="tno-mm-heading-text"><?php echo esc_html( $heading->title ); ?></span>
                                                <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </a>

                                            <?php if ( ! empty( $sub_links ) ) : ?>
                                                <ul class="tno-mm-sublist">
                                                    <?php foreach ( $sub_links as $sub ) : ?>
                                                        <li>
                                                            <a href="<?php echo esc_url( $sub->url ); ?>"><?php echo esc_html( $sub->title ); ?></a>
                                                        </li>
                                                        <?php unset( $children_elements[ $sub->ID ] ); ?>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                        <?php unset( $children_elements[ $heading->ID ] ); ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>
                        <?php unset( $children_elements[ $tab->ID ] ); ?>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes      = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        $output .= '<li class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';

        $attributes = ! empty( $item->url ) ? ' href="' . esc_url( $item->url ) . '"' : '';

        $output .= '<a' . $attributes . '>';
        $output .= esc_html( $item->title );

        if ( $has_children && 0 === $depth ) {
            $output .= '<svg class="tno-menu-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none"><path d="M13.125 0.46875C13.1456 0.46875 13.1665 0.4726 13.1855 0.480469C13.2045 0.488337 13.2218 0.50015 13.2363 0.514648C13.2508 0.529159 13.2627 0.546491 13.2705 0.56543C13.2783 0.584275 13.2822 0.604622 13.2822 0.625C13.2822 0.64556 13.2784 0.666543 13.2705 0.685547C13.2626 0.704523 13.2509 0.721804 13.2363 0.736328L6.98633 6.98633C6.97187 7.00081 6.95444 7.01265 6.93555 7.02051C6.91658 7.02837 6.89554 7.03223 6.875 7.03223C6.8546 7.03218 6.83427 7.02831 6.81543 7.02051L6.76465 6.98633L0.514648 0.736328C0.485281 0.706961 0.46875 0.666533 0.46875 0.625C0.468842 0.583593 0.485363 0.543934 0.514648 0.514648C0.543934 0.485363 0.583592 0.468842 0.625 0.46875C0.666533 0.46875 0.706961 0.485281 0.736328 0.514648L6.54395 6.32324L6.875 6.6543L7.20703 6.32324L13.0146 0.514648C13.0292 0.500109 13.0464 0.488339 13.0654 0.480469C13.0843 0.472665 13.1046 0.468795 13.125 0.46875Z" fill="currentColor" stroke="currentColor" stroke-width="0.9375"/></svg>';
        }

        $output .= '</a>';
    }

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="sub-menu">';
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}