<?php
/**
 * Shared render routines (product card, section header, carousel shell…).
 *
 * @package Trendino
 */

namespace Trendino\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Render_Helper
 */
class Render_Helper {

	/**
	 * Render a section header (title + optional subtitle + "see all" link + carousel nav slot).
	 *
	 * @param array $args title, subtitle, icon, link (url/is_external/nofollow), link_text,
	 *                    align, show_nav, id.
	 * @return void
	 */
	public static function section_header( array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'     => '',
				'subtitle'  => '',
				'icon_html' => '',
				'link'      => array(),
				'link_text' => '',
				'align'     => 'start',
				'show_nav'  => false,
				'class'     => '',
				'accent'    => '',
			)
		);

		if ( ! $args['title'] && ! $args['subtitle'] && ! $args['link_text'] && ! $args['show_nav'] ) {
			return;
		}

		trendino_enqueue_component( 'section-head' );

		$classes = array( 'trendino-section-head', 'is-align-' . sanitize_html_class( $args['align'] ) );

		if ( $args['class'] ) {
			$classes[] = sanitize_html_class( $args['class'] );
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="trendino-section-head__text">
				<?php if ( $args['icon_html'] ) : ?>
					<span class="trendino-section-head__icon"><?php echo $args['icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<?php if ( $args['title'] ) : ?>
					<h2 class="trendino-section-head__title"><?php echo esc_html( $args['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( $args['subtitle'] ) : ?>
					<span class="trendino-section-head__subtitle"><?php echo esc_html( $args['subtitle'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="trendino-section-head__actions">
				<?php if ( $args['link_text'] && ! empty( $args['link']['url'] ) ) : ?>
					<a class="trendino-section-head__link" href="<?php echo esc_url( $args['link']['url'] ); ?>"
						<?php echo ! empty( $args['link']['is_external'] ) ? ' target="_blank"' : ''; ?>
						<?php echo ! empty( $args['link']['nofollow'] ) ? ' rel="nofollow"' : ''; ?>>
						<span><?php echo esc_html( $args['link_text'] ); ?></span>
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 16 ) ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $args['show_nav'] ) : ?>
					<div class="trendino-carousel__nav" data-carousel-nav>
						<button type="button" class="trendino-carousel__arrow" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'trendino' ); ?>">
							<?php trendino_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 18 ) ); ?>
						</button>
						<button type="button" class="trendino-carousel__arrow" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'trendino' ); ?>">
							<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 18 ) ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render one WooCommerce product card.
	 *
	 * @param \WC_Product $product  Product object.
	 * @param array       $settings Card display settings.
	 * @return void
	 */
	public static function product_card( $product, array $settings = array() ) {
		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		trendino_enqueue_component( 'product-card' );

		$settings = wp_parse_args(
			$settings,
			array(
				'show_image'     => 'yes',
				'show_badge'     => 'yes',
				'show_discount'  => 'yes',
				'show_title'     => 'yes',
				'show_rating'    => 'yes',
				'show_brand'     => '',
				'show_price'     => 'yes',
				'show_old_price' => 'yes',
				'show_stock'     => '',
				'show_cart'      => 'yes',
				'show_wishlist'  => 'yes',
				'show_compare'   => '',
				'show_quickview' => '',
				'title_length'   => 60,
				'card_layout'    => 'vertical',
				'image_ratio'    => '1-1',
				'colors_swatch'  => 'yes',
			)
		);

		$post_id     = $product->get_id();
		$permalink   = $product->get_permalink();
		$title       = $product->get_name();
		$is_on_sale  = $product->is_on_sale();
		$in_stock    = $product->is_in_stock();
		$rating      = (float) $product->get_average_rating();
		$rating_cnt  = (int) $product->get_rating_count();
		$percentage  = self::discount_percentage( $product );

		$classes = array(
			'trendino-product-card',
			'trendino-product-card--' . sanitize_html_class( $settings['card_layout'] ),
			'trendino-ratio-' . sanitize_html_class( $settings['image_ratio'] ),
		);

		if ( ! $in_stock ) {
			$classes[] = 'is-out-of-stock';
		}
		?>
		<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-product-id="<?php echo esc_attr( $post_id ); ?>">
			<?php if ( 'yes' === $settings['show_image'] ) : ?>
				<div class="trendino-product-card__media">
					<a class="trendino-product-card__image" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
						<?php echo self::product_thumbnail( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>

					<?php if ( 'yes' === $settings['show_badge'] ) : ?>
						<?php self::product_badges( $product, $settings ); ?>
					<?php endif; ?>

					<div class="trendino-product-card__tools">
						<?php if ( 'yes' === $settings['show_wishlist'] ) : ?>
							<button type="button" class="trendino-product-card__tool" data-trendino-wishlist="<?php echo esc_attr( $post_id ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'trendino' ); ?>">
								<?php trendino_icon( 'heart', array( 'size' => 18 ) ); ?>
							</button>
						<?php endif; ?>
						<?php if ( 'yes' === $settings['show_compare'] ) : ?>
							<button type="button" class="trendino-product-card__tool" data-trendino-compare="<?php echo esc_attr( $post_id ); ?>" aria-label="<?php esc_attr_e( 'Compare', 'trendino' ); ?>">
								<?php trendino_icon( 'compare', array( 'size' => 18 ) ); ?>
							</button>
						<?php endif; ?>
						<?php if ( 'yes' === $settings['show_quickview'] ) : ?>
							<button type="button" class="trendino-product-card__tool" data-trendino-quickview="<?php echo esc_attr( $post_id ); ?>" aria-label="<?php esc_attr_e( 'Quick view', 'trendino' ); ?>">
								<?php trendino_icon( 'eye', array( 'size' => 18 ) ); ?>
							</button>
						<?php endif; ?>
					</div>

					<?php if ( 'yes' === $settings['colors_swatch'] ) : ?>
						<?php self::product_color_swatches( $product ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="trendino-product-card__body">
				<?php if ( 'yes' === $settings['show_brand'] ) : ?>
					<?php self::product_brand( $product ); ?>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_title'] ) : ?>
					<h3 class="trendino-product-card__title">
						<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( self::trim_title( $title, (int) $settings['title_length'] ) ); ?></a>
					</h3>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_rating'] && $rating > 0 ) : ?>
					<div class="trendino-product-card__rating" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rating */ __( 'Rated %s out of 5', 'trendino' ), $rating ) ); ?>">
						<?php trendino_icon( 'star', array( 'size' => 14, 'class' => 'is-filled' ) ); ?>
						<span class="trendino-product-card__rating-value"><?php echo esc_html( trendino_number( number_format_i18n( $rating, 1 ) ) ); ?></span>
						<?php if ( $rating_cnt ) : ?>
							<span class="trendino-product-card__rating-count">(<?php echo esc_html( trendino_number( $rating_cnt ) ); ?>)</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_stock'] && ! $in_stock ) : ?>
					<span class="trendino-product-card__stock is-out"><?php esc_html_e( 'Out of stock', 'trendino' ); ?></span>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_price'] ) : ?>
					<div class="trendino-product-card__price-row">
						<div class="trendino-product-card__prices">
							<?php if ( 'yes' === $settings['show_old_price'] && $is_on_sale && $product->get_regular_price() ) : ?>
								<del class="trendino-product-card__old-price"><?php echo wp_kses_post( wc_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) ) ); ?></del>
							<?php endif; ?>
							<span class="trendino-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
						</div>

						<?php if ( 'yes' === $settings['show_discount'] && $percentage ) : ?>
							<span class="trendino-product-card__discount"><?php echo esc_html( trendino_number( $percentage ) ); ?>٪</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_cart'] ) : ?>
					<div class="trendino-product-card__cart">
						<?php echo self::add_to_cart_button( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}

	/**
	 * Product thumbnail markup with lazy loading + placeholder fallback.
	 *
	 * @param \WC_Product $product Product.
	 * @param string      $size    Image size.
	 * @return string
	 */
	public static function product_thumbnail( $product, $size = 'trendino-product-card' ) {
		$image_id = $product->get_image_id();

		if ( $image_id ) {
			return wp_get_attachment_image(
				$image_id,
				$size,
				false,
				array(
					'class'    => 'trendino-product-card__img',
					'loading'  => 'lazy',
					'decoding' => 'async',
					'alt'      => $product->get_name(),
				)
			);
		}

		return sprintf(
			'<img class="trendino-product-card__img is-placeholder" src="%1$s" alt="%2$s" loading="lazy" decoding="async" width="400" height="400" />',
			esc_url( trendino_placeholder_image() ),
			esc_attr( $product->get_name() )
		);
	}

	/**
	 * Discount percentage for a product (works for simple + variable).
	 *
	 * @param \WC_Product $product Product.
	 * @return int 0 when not on sale.
	 */
	public static function discount_percentage( $product ) {
		if ( ! $product->is_on_sale() ) {
			return 0;
		}

		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();

		if ( $product->is_type( 'variable' ) ) {
			$regular = (float) $product->get_variation_regular_price( 'min' );
			$sale    = (float) $product->get_variation_sale_price( 'min' );
		}

		if ( $regular <= 0 || $sale <= 0 || $sale >= $regular ) {
			return 0;
		}

		return (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
	}

	/**
	 * Badges: sale percentage, featured, new, out of stock.
	 *
	 * @param \WC_Product $product  Product.
	 * @param array       $settings Settings.
	 * @return void
	 */
	public static function product_badges( $product, array $settings = array() ) {
		$badges = array();

		$percentage = self::discount_percentage( $product );

		if ( $percentage ) {
			$badges[] = array(
				'class' => 'is-sale',
				'label' => sprintf( '%s٪ %s', trendino_number( $percentage ), esc_html__( 'off', 'trendino' ) ),
			);
		}

		if ( $product->is_featured() ) {
			$badges[] = array(
				'class' => 'is-featured',
				'label' => esc_html__( 'Featured', 'trendino' ),
			);
		}

		$created = $product->get_date_created();

		if ( $created && ( time() - $created->getTimestamp() ) < ( 14 * DAY_IN_SECONDS ) ) {
			$badges[] = array(
				'class' => 'is-new',
				'label' => esc_html__( 'New', 'trendino' ),
			);
		}

		/**
		 * Filters the badges shown on a product card.
		 *
		 * @param array       $badges  Badges.
		 * @param \WC_Product $product Product.
		 */
		$badges = apply_filters( 'trendino/product_card/badges', $badges, $product );

		if ( empty( $badges ) ) {
			return;
		}
		?>
		<div class="trendino-product-card__badges">
			<?php foreach ( $badges as $badge ) : ?>
				<span class="trendino-badge <?php echo esc_attr( $badge['class'] ); ?>"><?php echo esc_html( $badge['label'] ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render colour swatches from the product's colour attribute.
	 *
	 * @param \WC_Product $product Product.
	 * @return void
	 */
	public static function product_color_swatches( $product ) {
		$attribute_keys = apply_filters( 'trendino/color_attributes', array( 'pa_color', 'pa_رنگ', 'color', 'رنگ' ) );
		$terms          = array();

		foreach ( $attribute_keys as $key ) {
			$values = $product->get_attribute( $key );

			if ( $values ) {
				$terms = array_map( 'trim', explode( ',', $values ) );
				break;
			}
		}

		if ( empty( $terms ) ) {
			return;
		}

		$map = apply_filters(
			'trendino/color_map',
			array(
				'مشکی'    => '#111111',
				'سفید'    => '#ffffff',
				'قرمز'    => '#e53935',
				'آبی'     => '#1e88e5',
				'سبز'     => '#43a047',
				'زرد'     => '#fdd835',
				'نارنجی'  => '#fb8c00',
				'طوسی'    => '#9e9e9e',
				'خاکستری' => '#757575',
				'صورتی'   => '#ec407a',
				'بنفش'    => '#8e24aa',
				'قهوه‌ای' => '#6d4c41',
				'طلایی'   => '#d4af37',
				'نقره‌ای' => '#c0c0c0',
			)
		);
		?>
		<div class="trendino-product-card__swatches">
			<?php
			$count = 0;
			foreach ( $terms as $term ) :
				if ( $count >= 4 ) {
					break;
				}
				$color = isset( $map[ $term ] ) ? $map[ $term ] : '#d9d9de';
				$count++;
				?>
				<span class="trendino-swatch" style="--swatch:<?php echo esc_attr( $color ); ?>" title="<?php echo esc_attr( $term ); ?>"></span>
			<?php endforeach; ?>
			<?php if ( count( $terms ) > 4 ) : ?>
				<span class="trendino-swatch is-more">+<?php echo esc_html( trendino_number( count( $terms ) - 4 ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Output the product brand name (linked) when a brand taxonomy exists.
	 *
	 * @param \WC_Product $product Product.
	 * @return void
	 */
	public static function product_brand( $product ) {
		$taxonomy = Query_Helper::brand_taxonomy();

		if ( ! $taxonomy ) {
			return;
		}

		$terms = get_the_terms( $product->get_id(), $taxonomy );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$term = reset( $terms );
		?>
		<a class="trendino-product-card__brand" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
		<?php
	}

	/**
	 * Add to cart button markup (uses WooCommerce core classes so AJAX add-to-cart works).
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	public static function add_to_cart_button( $product ) {
		if ( ! $product->is_purchasable() && ! $product->is_type( 'external' ) ) {
			return sprintf(
				'<a href="%1$s" class="trendino-btn trendino-btn--ghost">%2$s</a>',
				esc_url( $product->get_permalink() ),
				esc_html__( 'View product', 'trendino' )
			);
		}

		$classes = array(
			'trendino-btn',
			'trendino-btn--cart',
			'product_type_' . $product->get_type(),
		);

		if ( $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			$classes[] = 'ajax_add_to_cart';
			$classes[] = 'add_to_cart_button';
		}

		return sprintf(
			'<a href="%1$s" data-quantity="1" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" rel="nofollow" aria-label="%5$s">%6$s<span>%7$s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( implode( ' ', $classes ) ),
			(int) $product->get_id(),
			esc_attr( $product->get_sku() ),
			esc_attr( $product->add_to_cart_description() ),
			trendino_get_icon( 'basket', array( 'size' => 18 ) ),
			esc_html( $product->add_to_cart_text() )
		);
	}

	/**
	 * Trim a title to a character length without breaking words.
	 *
	 * @param string $title  Title.
	 * @param int    $length Max characters. 0 = no trim.
	 * @return string
	 */
	public static function trim_title( $title, $length ) {
		if ( $length <= 0 ) {
			return $title;
		}

		if ( function_exists( 'mb_strlen' ) && mb_strlen( $title ) <= $length ) {
			return $title;
		}

		return wp_html_excerpt( $title, $length, '…' );
	}

	/**
	 * Print an "empty state" notice used when a query returns nothing.
	 *
	 * @param string $message Message.
	 * @return void
	 */
	public static function empty_state( $message = '' ) {
		$message = $message ? $message : esc_html__( 'No items found. Adjust the widget query settings.', 'trendino' );
		?>
		<div class="trendino-empty-state">
			<?php trendino_icon( 'basket', array( 'size' => 32 ) ); ?>
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}

	/**
	 * Build carousel data attributes from widget settings.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	public static function carousel_attributes( array $settings ) {
		$data = array(
			'slidesDesktop' => (int) ( $settings['columns'] ?? 5 ),
			'slidesTablet'  => (int) ( $settings['columns_tablet'] ?? 3 ),
			'slidesMobile'  => (float) ( $settings['columns_mobile'] ?? 2 ),
			'gap'           => (int) ( $settings['gap']['size'] ?? 12 ),
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay' => (int) ( $settings['autoplay_delay'] ?? 5000 ),
			'loop'          => 'yes' === ( $settings['loop'] ?? '' ),
			'arrows'        => 'yes' === ( $settings['arrows'] ?? 'yes' ),
			'dots'          => 'yes' === ( $settings['dots'] ?? '' ),
			'rtl'           => is_rtl(),
		);

		return "data-trendino-carousel='" . esc_attr( wp_json_encode( $data ) ) . "'";
	}
}
