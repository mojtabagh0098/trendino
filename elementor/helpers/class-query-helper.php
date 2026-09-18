<?php
/**
 * Shared WooCommerce / WordPress query builder used by the widgets.
 *
 * @package Trendino
 */

namespace Trendino\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Query_Helper
 */
class Query_Helper {

	/**
	 * Build a WP_Query for products from widget settings.
	 *
	 * Accepted keys: source, categories, tags, products, limit, orderby, order, exclude,
	 * hide_out_of_stock, offset.
	 *
	 * @param array $args Widget settings subset.
	 * @return \WP_Query
	 */
	public static function products( array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'source'            => 'recent',
				'categories'        => array(),
				'tags'              => array(),
				'products'          => array(),
				'brands'            => array(),
				'limit'             => 10,
				'orderby'           => 'date',
				'order'             => 'DESC',
				'offset'            => 0,
				'exclude'           => array(),
				'hide_out_of_stock' => false,
			)
		);

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, (int) $args['limit'] ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'meta_query'          => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		);

		if ( (int) $args['offset'] > 0 ) {
			$query_args['offset']        = (int) $args['offset'];
			$query_args['no_found_rows'] = false;
		}

		if ( ! empty( $args['exclude'] ) ) {
			$query_args['post__not_in'] = array_map( 'absint', (array) $args['exclude'] );
		}

		// Visibility (exclude hidden products).
		$visibility_terms = array( 'exclude-from-catalog' );

		switch ( $args['source'] ) {
			case 'featured':
				$query_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				break;

			case 'sale':
				$product_ids_on_sale = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
				$query_args['post__in'] = ! empty( $product_ids_on_sale ) ? $product_ids_on_sale : array( 0 );
				break;

			case 'best_selling':
				$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;

			case 'top_rated':
				$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;

			case 'manual':
				$ids                    = array_filter( array_map( 'absint', (array) $args['products'] ) );
				$query_args['post__in'] = ! empty( $ids ) ? $ids : array( 0 );
				$query_args['orderby']  = 'post__in';
				break;

			case 'related':
				$ids                    = self::related_ids( (int) $args['limit'] );
				$query_args['post__in'] = ! empty( $ids ) ? $ids : array( 0 );
				$query_args['orderby']  = 'post__in';
				break;

			case 'recent':
			default:
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'DESC';
				break;
		}

		// Explicit ordering overrides the source preset (except manual/related).
		if ( ! in_array( $args['source'], array( 'manual', 'related' ), true ) && ! empty( $args['orderby'] ) ) {
			switch ( $args['orderby'] ) {
				case 'price':
					$query_args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'popularity':
					$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'rating':
					$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$query_args['orderby']  = 'meta_value_num';
					break;
				case 'rand':
					$query_args['orderby'] = 'rand';
					break;
				case 'title':
					$query_args['orderby'] = 'title';
					break;
				case 'menu_order':
					$query_args['orderby'] = 'menu_order';
					break;
				case 'date':
					$query_args['orderby'] = 'date';
					break;
			}

			$query_args['order'] = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		}

		if ( ! empty( $args['categories'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', (array) $args['categories'] ),
			);
		}

		if ( ! empty( $args['tags'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', (array) $args['tags'] ),
			);
		}

		if ( ! empty( $args['brands'] ) ) {
			$brand_taxonomy = self::brand_taxonomy();

			if ( $brand_taxonomy ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => $brand_taxonomy,
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', (array) $args['brands'] ),
				);
			}
		}

		if ( $args['hide_out_of_stock'] || 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$visibility_terms[] = 'outofstock';
		}

		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => $visibility_terms,
			'operator' => 'NOT IN',
		);

		if ( count( $query_args['tax_query'] ) > 1 ) {
			$query_args['tax_query']['relation'] = 'AND';
		}

		/**
		 * Filters the product query arguments built by widgets.
		 *
		 * @param array $query_args WP_Query args.
		 * @param array $args       Widget settings.
		 */
		$query_args = apply_filters( 'trendino/query/products', $query_args, $args );

		return new \WP_Query( $query_args );
	}

	/**
	 * Build a WP_Query for posts.
	 *
	 * @param array $args source, categories, posts, limit, orderby, order, offset.
	 * @return \WP_Query
	 */
	public static function posts( array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'post_type'  => 'post',
				'categories' => array(),
				'posts'      => array(),
				'limit'      => 4,
				'orderby'    => 'date',
				'order'      => 'DESC',
				'offset'     => 0,
			)
		);

		$query_args = array(
			'post_type'           => $args['post_type'],
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, (int) $args['limit'] ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => in_array( $args['orderby'], array( 'date', 'title', 'rand', 'comment_count', 'modified' ), true ) ? $args['orderby'] : 'date',
			'order'               => 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC',
		);

		if ( (int) $args['offset'] > 0 ) {
			$query_args['offset']        = (int) $args['offset'];
			$query_args['no_found_rows'] = false;
		}

		if ( ! empty( $args['posts'] ) ) {
			$query_args['post__in'] = array_map( 'absint', (array) $args['posts'] );
			$query_args['orderby']  = 'post__in';
		} elseif ( ! empty( $args['categories'] ) ) {
			$query_args['category__in'] = array_map( 'absint', (array) $args['categories'] );
		}

		/**
		 * Filters the post query arguments built by widgets.
		 *
		 * @param array $query_args WP_Query args.
		 * @param array $args       Widget settings.
		 */
		$query_args = apply_filters( 'trendino/query/posts', $query_args, $args );

		return new \WP_Query( $query_args );
	}

	/**
	 * Fetch product categories.
	 *
	 * @param array $args parent, include, number, orderby, order, hide_empty.
	 * @return \WP_Term[]
	 */
	public static function product_categories( array $args ) {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return array();
		}

		$args = wp_parse_args(
			$args,
			array(
				'parent'     => '',
				'include'    => array(),
				'number'     => 10,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
			)
		);

		$term_args = array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => (bool) $args['hide_empty'],
			'number'     => max( 1, (int) $args['number'] ),
			'orderby'    => $args['orderby'],
			'order'      => $args['order'],
		);

		if ( ! empty( $args['include'] ) ) {
			$term_args['include'] = array_map( 'absint', (array) $args['include'] );
			$term_args['orderby'] = 'include';
			unset( $term_args['number'] );
		} elseif ( '' !== $args['parent'] ) {
			$term_args['parent'] = absint( $args['parent'] );
		}

		$terms = get_terms( $term_args );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Detect the active brand taxonomy (WooCommerce core 9.4+, Perfect Brands, YITH…).
	 *
	 * @return string Empty string when none is available.
	 */
	public static function brand_taxonomy() {
		$candidates = array( 'product_brand', 'pwb-brand', 'yith_product_brand', 'berocket_brand', 'brand' );

		/**
		 * Filters the brand taxonomy candidates.
		 *
		 * @param array $candidates Taxonomy slugs.
		 */
		$candidates = apply_filters( 'trendino/brand_taxonomies', $candidates );

		foreach ( $candidates as $taxonomy ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				return $taxonomy;
			}
		}

		return '';
	}

	/**
	 * Get brand terms.
	 *
	 * @param array $args include, number, orderby, order, hide_empty.
	 * @return \WP_Term[]
	 */
	public static function brands( array $args = array() ) {
		$taxonomy = self::brand_taxonomy();

		if ( ! $taxonomy ) {
			return array();
		}

		$args = wp_parse_args(
			$args,
			array(
				'include'    => array(),
				'number'     => 12,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
			)
		);

		$term_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => (bool) $args['hide_empty'],
			'number'     => max( 1, (int) $args['number'] ),
			'orderby'    => $args['orderby'],
			'order'      => $args['order'],
		);

		if ( ! empty( $args['include'] ) ) {
			$term_args['include'] = array_map( 'absint', (array) $args['include'] );
			$term_args['orderby'] = 'include';
			unset( $term_args['number'] );
		}

		$terms = get_terms( $term_args );

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Related product IDs for the current product.
	 *
	 * @param int $limit Limit.
	 * @return int[]
	 */
	protected static function related_ids( $limit ) {
		if ( ! function_exists( 'wc_get_related_products' ) ) {
			return array();
		}

		$product_id = get_the_ID();

		if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
			return array();
		}

		return wc_get_related_products( $product_id, $limit );
	}

	/**
	 * Options array for a select2 control listing terms.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param int    $limit    Max terms.
	 * @return array<int,string>
	 */
	public static function term_options( $taxonomy, $limit = 200 ) {
		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => $limit,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$options = array();

		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}

		return $options;
	}

	/**
	 * Options array listing posts of a type.
	 *
	 * @param string $post_type Post type.
	 * @param int    $limit     Max posts.
	 * @return array<int,string>
	 */
	public static function post_options( $post_type = 'post', $limit = 100 ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			)
		);

		$options = array();

		foreach ( $posts as $post_id ) {
			$options[ $post_id ] = get_the_title( $post_id );
		}

		return $options;
	}

	/**
	 * Options array listing registered nav menus.
	 *
	 * @return array<string,string>
	 */
	public static function menu_options() {
		$menus   = wp_get_nav_menus();
		$options = array();

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}
}
