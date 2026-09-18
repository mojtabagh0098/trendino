<?php
/**
 * Template tags: menu walker, cards, pagination, breadcrumbs.
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nav menu walker adding Trendino BEM classes, indicators and mega-menu wrappers.
 */
class Trendino_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Start level.
	 *
	 * @param string   $output Output.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$classes = array( 'trendino-menu__submenu', 'depth-' . ( $depth + 1 ) );

		$output .= "\n{$indent}<ul class=\"" . esc_attr( implode( ' ', $classes ) ) . "\">\n";
	}

	/**
	 * Start element.
	 *
	 * @param string   $output Output.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'trendino-menu__item';
		$classes[] = 'menu-item-' . $item->ID;

		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			$classes[] = 'has-children';
		}

		$class_names = implode( ' ', array_filter( apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) ) );
		$id_attr     = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		$output .= '<li id="' . esc_attr( $id_attr ) . '" class="' . esc_attr( $class_names ) . '">';

		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'class'  => 'trendino-menu__link',
		);

		$atts       = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
		$attributes = trendino_build_attributes( $atts );

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$indicator = in_array( 'has-children', $classes, true )
			? '<span class="trendino-menu__indicator">' . trendino_get_icon( 'chevron-down', array( 'size' => 14 ) ) . '</span>'
			: '';

		$output .= '<a ' . $attributes . '>';
		$output .= '<span class="trendino-menu__text">' . esc_html( $title ) . '</span>';

		if ( ! empty( $item->description ) ) {
			$output .= '<span class="trendino-menu__badge">' . esc_html( $item->description ) . '</span>';
		}

		$output .= $indicator;
		$output .= '</a>';
	}
}

/**
 * Wishlist page URL (supports YITH / TI wishlist, falls back to a theme page).
 *
 * @return string
 */
function trendino_wishlist_url() {
	if ( function_exists( 'YITH_WCWL' ) ) {
		return YITH_WCWL()->get_wishlist_url();
	}

	if ( defined( 'TINVWL_URL' ) && function_exists( 'tinv_url_wishlist_default' ) ) {
		return tinv_url_wishlist_default();
	}

	$page_id = (int) trendino_get_option( 'wishlist_page_id', 0 );

	return $page_id ? get_permalink( $page_id ) : home_url( '/wishlist/' );
}

/**
 * Current wishlist item count.
 *
 * @return int
 */
function trendino_wishlist_count() {
	if ( function_exists( 'YITH_WCWL' ) ) {
		return (int) YITH_WCWL()->count_products();
	}

	if ( function_exists( 'tinv_get_option' ) && class_exists( 'TInvWL_Product' ) ) {
		$wishlist = new TInvWL_Product();

		return (int) $wishlist->get_count();
	}

	$cookie = isset( $_COOKIE['trendino_wishlist'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['trendino_wishlist'] ) ) : '';

	if ( ! $cookie ) {
		return 0;
	}

	return count( array_filter( array_map( 'absint', explode( ',', $cookie ) ) ) );
}

/**
 * Compare page URL.
 *
 * @return string
 */
function trendino_compare_url() {
	$page_id = (int) trendino_get_option( 'compare_page_id', 0 );

	return $page_id ? get_permalink( $page_id ) : home_url( '/compare/' );
}

/**
 * Render a blog post card.
 *
 * @param int|WP_Post $post     Post.
 * @param array       $settings show_image, show_date, show_excerpt, excerpt_length,
 *                              show_category, show_author, show_comments, image_size.
 * @return void
 */
function trendino_post_card( $post, array $settings = array() ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

	$settings = wp_parse_args(
		$settings,
		array(
			'show_image'     => 'yes',
			'show_date'      => 'yes',
			'show_excerpt'   => 'yes',
			'excerpt_length' => 90,
			'show_category'  => 'yes',
			'show_author'    => '',
			'show_comments'  => '',
			'image_size'     => 'trendino-post-card',
			'link_text'      => '',
		)
	);

	$permalink  = get_permalink( $post );
	$categories = get_the_category( $post->ID );
	?>
	<article <?php post_class( 'trendino-post-card', $post->ID ); ?>>
		<?php if ( 'yes' === $settings['show_image'] ) : ?>
			<a class="trendino-post-card__media" href="<?php echo esc_url( $permalink ); ?>" aria-hidden="true" tabindex="-1">
				<?php if ( has_post_thumbnail( $post ) ) : ?>
					<?php echo get_the_post_thumbnail( $post, $settings['image_size'], array( 'loading' => 'lazy', 'class' => 'trendino-post-card__img' ) ); ?>
				<?php else : ?>
					<img class="trendino-post-card__img is-placeholder" src="<?php echo esc_url( trendino_placeholder_image() ); ?>" alt="" width="600" height="360" loading="lazy" />
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_category'] && ! empty( $categories ) ) : ?>
					<span class="trendino-post-card__category"><?php echo esc_html( $categories[0]->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>

		<div class="trendino-post-card__body">
			<h3 class="trendino-post-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
			</h3>

			<?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
				<p class="trendino-post-card__excerpt">
					<?php echo esc_html( wp_html_excerpt( wp_strip_all_tags( get_the_excerpt( $post ) ), (int) $settings['excerpt_length'], '…' ) ); ?>
				</p>
			<?php endif; ?>

			<div class="trendino-post-card__meta">
				<?php if ( 'yes' === $settings['show_date'] ) : ?>
					<span class="trendino-post-card__date">
						<?php trendino_icon( 'clock', array( 'size' => 15 ) ); ?>
						<?php echo esc_html( trendino_number( get_the_date( '', $post ) ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_author'] ) : ?>
					<span class="trendino-post-card__author">
						<?php trendino_icon( 'user', array( 'size' => 15 ) ); ?>
						<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_comments'] ) : ?>
					<span class="trendino-post-card__comments">
						<?php echo esc_html( trendino_number( get_comments_number( $post ) ) ); ?>
						<?php esc_html_e( 'comments', 'trendino' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( ! empty( $settings['link_text'] ) ) : ?>
					<a class="trendino-post-card__more" href="<?php echo esc_url( $permalink ); ?>">
						<span><?php echo esc_html( $settings['link_text'] ); ?></span>
						<?php trendino_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Theme pagination.
 *
 * @param WP_Query|null $query Query. Defaults to the main query.
 * @return void
 */
function trendino_pagination( $query = null ) {
	global $wp_query;

	$query = $query ? $query : $wp_query;

	if ( $query->max_num_pages < 2 ) {
		return;
	}

	$links = paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'type'      => 'array',
			'prev_text' => trendino_get_icon( is_rtl() ? 'chevron-right' : 'chevron-left', array( 'size' => 18 ) ),
			'next_text' => trendino_get_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 18 ) ),
			'mid_size'  => 1,
		)
	);

	if ( empty( $links ) ) {
		return;
	}
	?>
	<nav class="trendino-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'trendino' ); ?>">
		<?php foreach ( $links as $link ) : ?>
			<?php echo wp_kses_post( $link ); ?>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Breadcrumbs (uses WooCommerce breadcrumb when available for shop pages).
 *
 * @return void
 */
function trendino_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="trendino-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'trendino' ) . '"><div class="trendino-container">';

	if ( trendino_is_woocommerce_active() && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		woocommerce_breadcrumb(
			array(
				'delimiter'   => '<span class="trendino-breadcrumbs__sep">' . trendino_get_icon( is_rtl() ? 'chevron-left' : 'chevron-right', array( 'size' => 14 ) ) . '</span>',
				'wrap_before' => '<ol class="trendino-breadcrumbs__list">',
				'wrap_after'  => '</ol>',
				'before'      => '<li>',
				'after'       => '</li>',
				'home'        => esc_html__( 'Home', 'trendino' ),
			)
		);
	} else {
		echo '<ol class="trendino-breadcrumbs__list">';
		printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/' ) ), esc_html__( 'Home', 'trendino' ) );

		if ( is_singular( 'post' ) ) {
			$categories = get_the_category();

			if ( ! empty( $categories ) ) {
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_category_link( $categories[0]->term_id ) ),
					esc_html( $categories[0]->name )
				);
			}

			printf( '<li aria-current="page">%s</li>', esc_html( get_the_title() ) );
		} elseif ( is_singular() ) {
			printf( '<li aria-current="page">%s</li>', esc_html( get_the_title() ) );
		} elseif ( is_archive() ) {
			printf( '<li aria-current="page">%s</li>', esc_html( wp_strip_all_tags( get_the_archive_title() ) ) );
		} elseif ( is_search() ) {
			printf(
				'<li aria-current="page">%s</li>',
				esc_html( sprintf( /* translators: %s: search term */ __( 'Search results for “%s”', 'trendino' ), get_search_query() ) )
			);
		} elseif ( is_404() ) {
			printf( '<li aria-current="page">%s</li>', esc_html__( 'Page not found', 'trendino' ) );
		}

		echo '</ol>';
	}

	echo '</div></nav>';
}

/**
 * Print the page title header used on archives / blog pages.
 *
 * @param string $title    Title.
 * @param string $subtitle Optional subtitle.
 * @return void
 */
function trendino_page_header( $title = '', $subtitle = '' ) {
	if ( '' === $title ) {
		if ( is_home() ) {
			$title = single_post_title( '', false ) ? single_post_title( '', false ) : esc_html__( 'Blog', 'trendino' );
		} elseif ( is_search() ) {
			/* translators: %s: search query */
			$title = sprintf( esc_html__( 'Search results for: %s', 'trendino' ), get_search_query() );
		} else {
			$title = get_the_archive_title();
		}
	}

	if ( '' === $subtitle && is_archive() ) {
		$subtitle = wp_strip_all_tags( get_the_archive_description() );
	}
	?>
	<header class="trendino-page-header">
		<h1 class="trendino-page-header__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $subtitle ) : ?>
			<p class="trendino-page-header__subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Whether a sidebar should be displayed for the current view.
 *
 * @return bool
 */
function trendino_has_sidebar() {
	if ( is_page() || is_front_page() ) {
		return false;
	}

	if ( trendino_is_woocommerce_active() && ( is_shop() || is_product_taxonomy() ) ) {
		return is_active_sidebar( 'sidebar-shop' ) && 'full-width' !== trendino_get_option( 'shop_layout' );
	}

	if ( trendino_is_woocommerce_active() && is_product() ) {
		return false;
	}

	return is_active_sidebar( 'sidebar-blog' ) && 'full-width' !== trendino_get_option( 'blog_layout' );
}
