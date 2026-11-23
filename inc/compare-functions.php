<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TNO_COMPARE_MAX_ITEMS = 4;
const TNO_COMPARE_COOKIE    = 'tno_compare_ids';

/**
 * Enqueue compare page assets only where needed.
 */
add_action( 'wp_enqueue_scripts', function (): void {
	global $post;

	$is_compare_page = is_page_template( 'page-compare.php' )
		|| ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'tno_compare' ) );

	if ( ! $is_compare_page ) {
		return;
	}

	$theme_uri  = get_template_directory_uri();
	$theme_path = get_template_directory();

	$css_ver = file_exists( $theme_path . '/assets/css/compare.css' )
		? (string) filemtime( $theme_path . '/assets/css/compare.css' )
		: '1.0.0';

	$js_ver = file_exists( $theme_path . '/assets/js/compare.js' )
		? (string) filemtime( $theme_path . '/assets/js/compare.js' )
		: '1.0.0';

	wp_enqueue_style( 'tno-compare', $theme_uri . '/assets/css/compare.css', [], $css_ver );
	wp_enqueue_script( 'tno-compare', $theme_uri . '/assets/js/compare.js', [], $js_ver, true );

	$initial_ids      = tno_compare_get_initial_ids();
	$initial_products = tno_compare_fetch_products_payload( $initial_ids );

	wp_localize_script( 'tno-compare', 'tnoCompareConfig', [
		'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		'nonce'           => wp_create_nonce( 'tno_compare_nonce' ),
		'maxItems'        => TNO_COMPARE_MAX_ITEMS,
		'cookieName'      => TNO_COMPARE_COOKIE,
		'initialProducts' => $initial_products,
		'i18n'            => [
			'limitExceeded'  => 'مجاز به افزودن کالای جدید نیستید.',
			'remainingTpl'   => '{n} کالا برای انتخاب باقی مانده',
			'totalTpl'       => '{n} کالا',
			'addProduct'     => 'افزودن کالا',
			'currency'       => 'تومان',
			'emptySearch'    => 'محصولی با این مشخصات یافت نشد.',
			'emptyCompare'   => 'هنوز کالایی برای مقایسه انتخاب نشده است.',
			'loading'        => 'در حال بارگذاری...',
			'defaultGroup'   => 'مشخصات کلی',
		],
	] );
} );

/**
 * Parse and sanitize initial product IDs from query string (?ids=1,2,3) or cookie.
 *
 * @return int[]
 */
function tno_compare_get_initial_ids(): array {
	$raw = '';

	if ( isset( $_GET['ids'] ) && is_string( $_GET['ids'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['ids'] ) );
	} elseif ( isset( $_COOKIE[ TNO_COMPARE_COOKIE ] ) && is_string( $_COOKIE[ TNO_COMPARE_COOKIE ] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_COOKIE[ TNO_COMPARE_COOKIE ] ) );
	}

	if ( $raw === '' ) {
		return [];
	}

	$parts = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	return array_slice( array_values( array_unique( $parts ) ), 0, TNO_COMPARE_MAX_ITEMS );
}

/**
 * Extract grouped product specifications.
 * Hook `tno_compare_product_spec_groups` allows plugging custom PDP meta groups directly.
 *
 * Expected return format:
 * [
 *   'مشخصات کلی' => [
 *     'وزن' => '۱۳۸',
 *     'توضیحات بدنه' => 'قاب جلو و پشت از جنس شیشه',
 *   ],
 * ]
 *
 * @return array<string, array<string, string>>
 */
function tno_compare_get_product_spec_groups( WC_Product $product ): array {
	$custom_groups = apply_filters( 'tno_compare_product_spec_groups', null, $product );
	if ( is_array( $custom_groups ) && ! empty( $custom_groups ) ) {
		return $custom_groups;
	}

	$groups  = [];
	$general = [];

	if ( $product->has_weight() ) {
		$general['وزن'] = wc_format_weight( (float) $product->get_weight() );
	}

	if ( $product->has_dimensions() ) {
		$general['ابعاد'] = wc_format_dimensions( $product->get_dimensions( false ) );
	}

	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute instanceof WC_Product_Attribute || ! $attribute->get_visible() ) {
			continue;
		}

		$label = wc_attribute_label( $attribute->get_name(), $product );
		if ( $attribute->is_taxonomy() ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'names' ] );
			$value = ! is_wp_error( $terms ) ? implode( '، ', $terms ) : '';
		} else {
			$value = implode( '، ', $attribute->get_options() );
		}

		$value = trim( wp_strip_all_tags( $value ) );
		if ( $label !== '' && $value !== '' ) {
			$general[ $label ] = $value;
		}
	}

	if ( ! empty( $general ) ) {
		$groups['مشخصات کلی'] = $general;
	}

	return $groups;
}

/**
 * Build badge payload for a product card.
 *
 * @return array{text: string, style: string}|null
 */
function tno_compare_get_product_badge( WC_Product $product ): ?array {
	$badge = apply_filters( 'tno_compare_product_badge', null, $product );
	if ( is_array( $badge ) && ! empty( $badge['text'] ) ) {
		return [
			'text'  => (string) $badge['text'],
			'style' => in_array( $badge['style'] ?? '', [ 'pink', 'blue' ], true ) ? $badge['style'] : 'blue',
		];
	}

	$meta_text  = (string) get_post_meta( $product->get_id(), '_tno_badge_text', true );
	$meta_style = (string) get_post_meta( $product->get_id(), '_tno_badge_style', true );

	if ( $meta_text !== '' ) {
		return [
			'text'  => $meta_text,
			'style' => $meta_style === 'pink' ? 'pink' : 'blue',
		];
	}

	if ( $product->is_featured() ) {
		return [
			'text'  => 'لیبل سفارشی',
			'style' => 'pink',
		];
	}

	return null;
}

/**
 * Serialize a single WC_Product for compare cards, modal, and specification matrix.
 *
 * @return array<string, mixed>
 */
function tno_compare_serialize_product( WC_Product $product, bool $include_specs = true ): array {
	$regular_price = (float) $product->get_regular_price();
	$sale_price    = (float) $product->get_sale_price();
	$active_price  = (float) $product->get_price();

	if ( $product->is_type( 'variable' ) ) {
		$regular_price = (float) $product->get_variation_regular_price( 'min', true );
		$sale_price    = (float) $product->get_variation_sale_price( 'min', true );
		$active_price  = (float) $product->get_variation_price( 'min', true );
	}

	$on_sale          = $product->is_on_sale() && $regular_price > 0 && $active_price < $regular_price;
	$discount_percent = $on_sale ? (int) round( ( ( $regular_price - $active_price ) / $regular_price ) * 100 ) : 0;

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );

	$data = [
		'id'              => $product->get_id(),
		'title'           => $product->get_name(),
		'url'             => $product->get_permalink(),
		'image'           => (string) $image_url,
		'price'           => $active_price,
		'regularPrice'    => $regular_price,
		'onSale'          => $on_sale,
		'discountPercent' => $discount_percent,
		'inStock'         => $product->is_in_stock(),
		'badge'           => tno_compare_get_product_badge( $product ),
	];

	if ( $include_specs ) {
		$data['specs'] = tno_compare_get_product_spec_groups( $product );
	}

	return $data;
}

/**
 * Fetch ordered serialized products by IDs.
 *
 * @param int[] $ids
 * @return array<int, array<string, mixed>>
 */
function tno_compare_fetch_products_payload( array $ids ): array {
	if ( empty( $ids ) || ! function_exists( 'wc_get_product' ) ) {
		return [];
	}

	$ids     = array_slice( array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) ), 0, TNO_COMPARE_MAX_ITEMS );
	$payload = [];

	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( $product instanceof WC_Product && $product->get_status() === 'publish' ) {
			$payload[] = tno_compare_serialize_product( $product, true );
		}
	}

	return $payload;
}

/**
 * AJAX: Fetch full product payloads (including specs) for selected IDs.
 */
function tno_ajax_compare_get_products(): void {
	check_ajax_referer( 'tno_compare_nonce', 'nonce' );

	$raw_ids = isset( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : [];
	if ( is_string( $raw_ids ) ) {
		$raw_ids = explode( ',', $raw_ids );
	}
	if ( ! is_array( $raw_ids ) ) {
		wp_send_json_error( [ 'message' => 'Invalid IDs.' ], 400 );
	}

	$ids      = array_slice( array_values( array_unique( array_filter( array_map( 'absint', $raw_ids ) ) ) ), 0, TNO_COMPARE_MAX_ITEMS );
	$products = tno_compare_fetch_products_payload( $ids );

	wp_send_json_success( [ 'products' => $products ] );
}
add_action( 'wp_ajax_tno_compare_get_products', 'tno_ajax_compare_get_products' );
add_action( 'wp_ajax_nopriv_tno_compare_get_products', 'tno_ajax_compare_get_products' );

/**
 * AJAX: Search/list products for the comparison selection modal.
 */
function tno_ajax_compare_search_products(): void {
	check_ajax_referer( 'tno_compare_nonce', 'nonce' );

	$search   = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
	$paged    = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$per_page = 12;

	$args = [
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $paged,
		's'              => $search,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	// Optional category lock via filter if needed per project policy.
	$args = apply_filters( 'tno_compare_modal_query_args', $args, $search );

	$query    = new WP_Query( $args );
	$products = [];

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post_obj ) {
			$product = wc_get_product( $post_obj->ID );
			if ( $product instanceof WC_Product ) {
				$products[] = tno_compare_serialize_product( $product, false );
			}
		}
	}

	wp_send_json_success( [
		'total'       => (int) $query->found_posts,
		'maxPages'    => (int) $query->max_num_pages,
		'currentPage' => $paged,
		'products'    => $products,
	] );
}
add_action( 'wp_ajax_tno_compare_search_products', 'tno_ajax_compare_search_products' );
add_action( 'wp_ajax_nopriv_tno_compare_search_products', 'tno_ajax_compare_search_products' );

/**
 * Render main comparison page markup (reusable by page template and shortcode).
 */
function tno_render_compare_page(): string {
	ob_start();
	?>
	<main class="tno-compare-page" dir="rtl" id="tno-compare-app">
		<!-- Sticky Compact Header (visible on scroll) -->
		<div class="tno-cmp-sticky" id="tno-cmp-sticky" aria-hidden="true">
			<div class="tno-cmp-container">
				<div class="tno-cmp-sticky-grid" id="tno-cmp-sticky-grid"></div>
			</div>
		</div>

		<div class="tno-cmp-container">
			<!-- Top Cards Box -->
			<section class="tno-cmp-cards-shell" id="tno-cmp-cards-shell" aria-label="محصولات در حال مقایسه">
				<div class="tno-cmp-cards-grid" id="tno-cmp-cards-grid"></div>
			</section>

			<!-- Specifications Comparison Matrix -->
			<section class="tno-cmp-specs" id="tno-cmp-specs" aria-label="جدول مشخصات فنی"></section>
		</div>

		<!-- Product Selection Modal -->
		<div class="tno-cmp-modal-backdrop" id="tno-cmp-modal" hidden role="dialog" aria-modal="true" aria-labelledby="tno-cmp-modal-title">
			<div class="tno-cmp-modal">
				<header class="tno-cmp-modal-header">
					<h2 class="tno-cmp-modal-title" id="tno-cmp-modal-title">انتخاب کالا برای مقایسه</h2>
					<button type="button" class="tno-cmp-icon-btn" id="tno-cmp-modal-close" aria-label="بستن">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
					</button>
				</header>

				<div class="tno-cmp-modal-toolbar">
					<div class="tno-cmp-search-box">
						<input type="search" id="tno-cmp-search-input" class="tno-cmp-search-input" placeholder="نام محصول" autocomplete="off">
						<span class="tno-cmp-search-icon" aria-hidden="true">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.5">
                                    <path d="M17.9438 17.058L14.0321 13.1471C15.1659 11.7859 15.7312 10.04 15.6106 8.27263C15.4899 6.50524 14.6925 4.85241 13.3843 3.65797C12.076 2.46353 10.3576 1.81944 8.58657 1.85969C6.81553 1.89994 5.12818 2.62143 3.87554 3.87407C2.62289 5.12671 1.9014 6.81406 1.86115 8.5851C1.8209 10.3561 2.46499 12.0745 3.65943 13.3828C4.85387 14.691 6.5067 15.4884 8.27409 15.6091C10.0415 15.7298 11.7874 15.1644 13.1485 14.0306L17.0595 17.9424C17.1175 18.0004 17.1865 18.0465 17.2623 18.0779C17.3382 18.1094 17.4195 18.1255 17.5016 18.1255C17.5838 18.1255 17.6651 18.1094 17.741 18.0779C17.8168 18.0465 17.8858 18.0004 17.9438 17.9424C18.0019 17.8843 18.048 17.8154 18.0794 17.7395C18.1108 17.6636 18.127 17.5823 18.127 17.5002C18.127 17.4181 18.1108 17.3367 18.0794 17.2609C18.048 17.185 18.0019 17.1161 17.9438 17.058ZM3.12664 8.75018C3.12664 7.63766 3.45654 6.55012 4.07463 5.6251C4.69271 4.70007 5.57121 3.9791 6.59905 3.55336C7.62688 3.12761 8.75788 3.01622 9.84903 3.23326C10.9402 3.4503 11.9424 3.98603 12.7291 4.7727C13.5158 5.55937 14.0515 6.56165 14.2686 7.6528C14.4856 8.74394 14.3742 9.87494 13.9485 10.9028C13.5227 11.9306 12.8018 12.8091 11.8767 13.4272C10.9517 14.0453 9.86416 14.3752 8.75164 14.3752C7.26031 14.3735 5.83053 13.7804 4.77599 12.7258C3.72146 11.6713 3.1283 10.2415 3.12664 8.75018Z" fill="#131313"/>
                                </g>
                            </svg>
						</span>
					</div>

					<div class="tno-cmp-toolbar-meta">
						<div class="tno-cmp-selected-strip" id="tno-cmp-selected-strip"></div>

						<div class="tno-cmp-remaining">
							<span class="tno-cmp-remaining-icon" aria-hidden="true">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="M12 8v8M8 12h8"/></svg>
							</span>
							<span id="tno-cmp-remaining-text"></span>
						</div>

						<span class="tno-cmp-total-count" id="tno-cmp-total-count"></span>
					</div>
				</div>

				<div class="tno-cmp-modal-body" id="tno-cmp-modal-body">
					<div class="tno-cmp-modal-grid" id="tno-cmp-modal-grid"></div>
					<div class="tno-cmp-modal-more-wrap" id="tno-cmp-modal-more-wrap" hidden>
						<button type="button" class="tno-cmp-load-more-btn" id="tno-cmp-load-more">مشاهده کالاهای بیشتر</button>
					</div>
				</div>

				<footer class="tno-cmp-modal-footer">
					<button type="button" class="tno-cmp-submit-btn" id="tno-cmp-modal-submit">
						<span>افزودن به لیست مقایسه</span>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
					</button>
				</footer>
			</div>
		</div>

		<!-- Floating Error Toast -->
		<div class="tno-cmp-toast" id="tno-cmp-toast" role="alert" aria-live="assertive" hidden>
			<div class="tno-cmp-toast-content">
				<svg class="tno-cmp-toast-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
				<span id="tno-cmp-toast-msg">مجاز به افزودن کالای جدید نیستید.</span>
			</div>
			<button type="button" class="tno-cmp-toast-close" id="tno-cmp-toast-close" aria-label="بستن اعلان">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
			</button>
		</div>
	</main>
	<?php
	return (string) ob_get_clean();
}

add_shortcode( 'tno_compare', 'tno_render_compare_page' );