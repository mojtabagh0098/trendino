<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * تبدیل اعداد انگلیسی به فارسی
 */
function tno_fa_num( $str ) {
    $en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
    $fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
    return str_replace( $en, $fa, (string) $str );
}

/**
 * دریافت تگ‌های جستجوی پرطرفدار (از دسته‌ها یا برچسب‌های پرتکرار محصولات)
 */
function tno_get_popular_search_terms() {
    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'orderby'    => 'count',
        'order'      => 'DESC',
        'hide_empty' => true,
        'number'     => 7,
    ) );

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        return wp_list_pluck( $terms, 'name' );
    }

    return array( 'لپتاپ گیمینگ', 'گوشی سامسونگ', 'آیفون ۱۶', 'ساعت هوشمند', 'ایرپاد', 'پلی استیشن ۵' );
}

/**
 * رندر محتوای داخلی دراپ‌داون سبد خرید (مینی‌کارت)
 */
function tno_render_mini_cart_inner() {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return '';
    }

    $cart       = WC()->cart;
    $items      = $cart->get_cart();
    $item_count = $cart->get_cart_contents_count();

    ob_start();
    ?>
    <div class="tno-cart-drop__header">
        <span class="tno-cart-drop__title">سبد خرید</span>
        <span class="tno-cart-drop__badge"><?php echo esc_html( tno_fa_num( $item_count ) ); ?> محصول</span>
    </div>

    <?php if ( empty( $items ) ) : ?>
        <div class="tno-cart-drop__empty">سبد خرید شما خالی است.</div>
    <?php else : ?>
        <div class="tno-cart-drop__items">
            <?php foreach ( $items as $cart_item_key => $cart_item ) :
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
                    continue;
                }

                $product_name      = $_product->get_name();
                $product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
                $thumbnail         = $_product->get_image( 'woocommerce_thumbnail' );
                $qty               = (int) $cart_item['quantity'];

                $regular_price = (float) $_product->get_regular_price();
                $active_price  = (float) $_product->get_price();
                $discount_pct  = 0;

                if ( $_product->is_on_sale() && $regular_price > $active_price && $regular_price > 0 ) {
                    $discount_pct = round( ( ( $regular_price - $active_price ) / $regular_price ) * 100 );
                }
            ?>
                <div class="tno-cart-item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
                    <div class="tno-cart-item__top">
                        <a href="<?php echo esc_url( $product_permalink ); ?>" class="tno-cart-item__thumb">
                            <?php echo $thumbnail; ?>
                        </a>
                        <h4 class="tno-cart-item__title">
                            <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $product_name ); ?></a>
                        </h4>
                    </div>

                    <div class="tno-cart-item__bottom">
                        <!-- کنترل تعداد (+ / عدد / سطل زباله) -->
                        <div class="tno-cart-item__qty-ctrl">
                            <button type="button" class="tno-qty-btn tno-qty-plus" data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( $qty + 1 ); ?>" aria-label="افزایش">+</button>
                            <span class="tno-qty-val"><?php echo esc_html( tno_fa_num( $qty ) ); ?></span>
                            <button type="button" class="tno-qty-btn tno-qty-trash" data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( $qty - 1 ); ?>" aria-label="حذف یا کاهش">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </div>

                        <!-- قیمت و درصد تخفیف -->
                        <div class="tno-cart-item__pricing">
                            <?php if ( $discount_pct > 0 ) : ?>
                                <div class="tno-cart-item__old-row">
                                    <span class="tno-cart-item__disc"><?php echo esc_html( tno_fa_num( $discount_pct ) ); ?>٪</span>
                                    <del class="tno-cart-item__old-price"><?php echo esc_html( tno_fa_num( number_format( $regular_price ) ) ); ?></del>
                                </div>
                            <?php endif; ?>
                            <div class="tno-cart-item__new-price">
                                <strong><?php echo esc_html( tno_fa_num( number_format( $active_price ) ) ); ?></strong>
                                <small>تومان</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="tno-cart-drop__footer">
            <div class="tno-cart-drop__payable">
                <span class="tno-payable-label">مبلغ قابل پرداخت:</span>
                <div class="tno-payable-amount">
                    <strong><?php echo esc_html( tno_fa_num( number_format( (float) $cart->get_cart_contents_total() ) ) ); ?></strong>
                    <small>تومان</small>
                </div>
            </div>
            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="tno-cart-drop__submit">ثبت سفارش</a>
        </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

/**
 * ایجکس ۱: بروزرسانی تعداد یا حذف آیتم از سبد خرید هدر
 */
add_action( 'wp_ajax_tno_update_mini_cart', 'tno_ajax_update_mini_cart' );
add_action( 'wp_ajax_nopriv_tno_update_mini_cart', 'tno_ajax_update_mini_cart' );
function tno_ajax_update_mini_cart() {
    check_ajax_referer( 'tno_header_nonce', 'nonce' );

    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        wp_send_json_error();
    }

    $key = isset( $_POST['cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_key'] ) ) : '';
    $qty = isset( $_POST['qty'] ) ? max( 0, intval( $_POST['qty'] ) ) : 0;

    if ( $key ) {
        if ( 0 === $qty ) {
            WC()->cart->remove_cart_item( $key );
        } else {
            WC()->cart->set_quantity( $key, $qty, true );
        }
        WC()->cart->calculate_totals();
    }

    wp_send_json_success( array(
        'html'  => tno_render_mini_cart_inner(),
        'count' => tno_fa_num( WC()->cart->get_cart_contents_count() ),
    ) );
}

/**
 * ایجکس ۲: جستجوی زنده (پیشنهادات + محصولات عکس‌دار)
 */
add_action( 'wp_ajax_tno_live_search', 'tno_ajax_live_search' );
add_action( 'wp_ajax_nopriv_tno_live_search', 'tno_ajax_live_search' );
function tno_ajax_live_search() {
    check_ajax_referer( 'tno_header_nonce', 'nonce' );

    $q = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
    if ( mb_strlen( $q ) < 2 ) {
        wp_send_json_error();
    }

    $suggestions = array();

    // ۱. جستجوی دسته‌بندی‌های مرتبط
    $cats = get_terms( array(
        'taxonomy'   => 'product_cat',
        'name__like' => $q,
        'hide_empty' => false,
        'number'     => 3,
    ) );
    if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
        foreach ( $cats as $cat ) {
            $suggestions[] = array(
                'title' => $cat->name,
                'url'   => get_term_link( $cat ),
            );
        }
    }

    // ۲. جستجوی محصولات
    $query = new WP_Query( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $q,
        'posts_per_page' => 5,
    ) );

    $products = array();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $title = get_the_title();
            $url   = get_permalink();

            if ( count( $suggestions ) < 5 ) {
                $suggestions[] = array(
                    'title' => $title,
                    'url'   => $url,
                );
            }

            if ( count( $products ) < 3 ) {
                $products[] = array(
                    'title' => $title,
                    'url'   => $url,
                    'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ?: wc_placeholder_img_src( 'thumbnail' ),
                );
            }
        }
        wp_reset_postdata();
    }

    wp_send_json_success( array(
        'empty'       => ( empty( $suggestions ) && empty( $products ) ),
        'suggestions' => array_slice( $suggestions, 0, 5 ),
        'products'    => $products,
    ) );
}

/**
 * اسکریپت Vanilla JS برای مدیریت باکس سرچ (۵ حالت) و ایجکس سبد خرید
 */
add_action( 'wp_footer', 'tno_header_interactive_js', 99 );
function tno_header_interactive_js() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce    = wp_create_nonce( 'tno_header_nonce' );
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const AJAX_URL = <?php echo wp_json_encode( $ajax_url ); ?>;
        const NONCE    = <?php echo wp_json_encode( $nonce ); ?>;
        const STORAGE_KEY = 'tno_recent_searches';

        // =========================================
        // ۱. مدیریت باکس جستجوی هوشمند (۵ حالت)
        // =========================================
        const searchWrap      = document.querySelector('.tno-search-wrap');
        const searchForm      = document.querySelector('.tno-search-form');
        const searchInput     = document.querySelector('.tno-search-input');
        const searchDropdown  = document.querySelector('.tno-search-dropdown');
        const defaultView     = document.querySelector('.tno-search-view--default');
        const resultsView     = document.querySelector('.tno-search-view--results');
        const emptyView       = document.querySelector('.tno-search-view--empty');
        const recentSection   = document.querySelector('.tno-recent-searches-sec');
        const recentTagsBox   = document.querySelector('.tno-recent-tags');
        const clearRecentBtn  = document.querySelector('.tno-clear-recent-btn');
        const suggestList     = document.querySelector('.tno-search-suggestions');
        const productsList    = document.querySelector('.tno-search-products');

        let searchTimeout = null;

        const getRecentSearches = function() {
            try {
                return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
            } catch (e) {
                return [];
            }
        };

        const saveRecentSearch = function(term) {
            term = term.trim();
            if (!term) return;
            let list = getRecentSearches().filter(item => item !== term);
            list.unshift(term);
            list = list.slice(0, 6);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
        };

        const renderRecentSearches = function() {
            if (!recentSection || !recentTagsBox) return;
            const list = getRecentSearches();
            if (!list.length) {
                recentSection.style.display = 'none';
                return;
            }
            recentSection.style.display = 'block';
            recentTagsBox.innerHTML = list.map(item => 
                `<button type="button" class="tno-search-chip" data-term="${item.replace(/"/g, '&quot;')}">${item}</button>`
            ).join('');
        };

        const showSearchState = function(state) {
            if (!searchDropdown) return;
            defaultView.style.display = (state === 'default') ? 'block' : 'none';
            resultsView.style.display = (state === 'results') ? 'block' : 'none';
            emptyView.style.display   = (state === 'empty')   ? 'block' : 'none';
        };

        const triggerLiveSearch = function(query) {
            const formData = new FormData();
            formData.append('action', 'tno_live_search');
            formData.append('nonce', NONCE);
            formData.append('q', query);

            fetch(AJAX_URL, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (!res.success || res.data.empty) {
                        showSearchState('empty');
                        return;
                    }

                    // رندر لیست پیشنهادات بالا
                    suggestList.innerHTML = res.data.suggestions.map(item => `
                        <li>
                            <a href="${item.url}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <span>${item.title}</span>
                            </a>
                        </li>
                    `).join('');

                    // رندر لیست محصولات عکس‌دار پایین
                    if (res.data.products && res.data.products.length) {
                        productsList.style.display = 'flex';
                        productsList.innerHTML = res.data.products.map(prod => `
                            <a href="${prod.url}" class="tno-search-prod-item">
                                <img src="${prod.thumb}" alt="${prod.title.replace(/"/g, '&quot;')}" />
                                <span>${prod.title}</span>
                            </a>
                        `).join('');
                    } else {
                        productsList.style.display = 'none';
                        productsList.innerHTML = '';
                    }

                    showSearchState('results');
                })
                .catch(() => {});
        };

        if (searchInput && searchWrap) {
            searchInput.addEventListener('focus', function() {
                searchWrap.classList.add('is-active');
                renderRecentSearches();
                if (this.value.trim().length < 2) {
                    showSearchState('default');
                }
            });

            searchInput.addEventListener('input', function() {
                const val = this.value.trim();
                clearTimeout(searchTimeout);

                if (val.length < 2) {
                    renderRecentSearches();
                    showSearchState('default');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    triggerLiveSearch(val);
                }, 300);
            });

            searchForm.addEventListener('submit', function() {
                if (searchInput.value.trim()) {
                    saveRecentSearch(searchInput.value.trim());
                }
            });

            if (clearRecentBtn) {
                clearRecentBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    localStorage.removeItem(STORAGE_KEY);
                    renderRecentSearches();
                });
            }

            searchDropdown.addEventListener('click', function(e) {
                const chip = e.target.closest('.tno-search-chip');
                if (chip) {
                    const term = chip.getAttribute('data-term');
                    searchInput.value = term;
                    searchInput.focus();
                    saveRecentSearch(term);
                    triggerLiveSearch(term);
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchWrap.contains(e.target)) {
                    searchWrap.classList.remove('is-active');
                }
            });
        }

        // =========================================
        // ۲. مدیریت ایجکس سبد خرید هدر (+ و حذف)
        // =========================================
        document.addEventListener('click', function(e) {
            const qtyBtn = e.target.closest('.tno-qty-btn');
            if (!qtyBtn) return;

            e.preventDefault();
            const key = qtyBtn.getAttribute('data-key');
            const qty = qtyBtn.getAttribute('data-qty');
            const cartDrop = qtyBtn.closest('.tno-cart-dropdown');

            if (cartDrop) cartDrop.classList.add('is-loading');

            const formData = new FormData();
            formData.append('action', 'tno_update_mini_cart');
            formData.append('nonce', NONCE);
            formData.append('cart_key', key);
            formData.append('qty', qty);

            fetch(AJAX_URL, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (cartDrop) cartDrop.innerHTML = res.data.html;
                        const badge = document.querySelector('.c-header__cart-qty');
                        if (badge) badge.textContent = res.data.count;
                    }
                })
                .finally(() => {
                    if (cartDrop) cartDrop.classList.remove('is-loading');
                });
        });
    });
    </script>
    <?php
}