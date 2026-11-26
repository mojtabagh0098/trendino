<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$paged          = max( 1, (int) ( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1 ) );
$search_query   = get_search_query();
$is_filtered    = is_category() || is_tag() || $search_query !== '';
$blog_page_id   = (int) get_option( 'page_for_posts' );
$blog_base_url  = $blog_page_id > 0 ? (string) get_permalink( $blog_page_id ) : ( is_page_template( 'page-blog.php' ) ? (string) get_permalink() : home_url( '/' ) );

// Section 1: Featured Post (sticky or latest post)
$sticky_ids     = get_option( 'sticky_posts' );
$featured_args  = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 1,
	'ignore_sticky_posts' => 0,
);
if ( is_array( $sticky_ids ) && ! empty( $sticky_ids ) ) {
	$featured_args['post__in'] = $sticky_ids;
}
$featured_q   = new WP_Query( $featured_args );
$featured_id  = $featured_q->have_posts() ? (int) $featured_q->posts[0]->ID : 0;

// Hero side 3 posts (excluding featured post)
$hero_side_q = new WP_Query( array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 3,
	'post__not_in'        => $featured_id > 0 ? array( $featured_id ) : array(),
	'ignore_sticky_posts' => 1,
) );

// Section 2: Latest 8 posts
$latest_q = new WP_Query( array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 8,
	'ignore_sticky_posts' => 1,
) );

// Section 3: Educational / Guides (6 posts — prefers 'guide' / 'educational' / 'آموزشی' category if it exists, otherwise latest 6)
$guide_term = get_term_by( 'slug', 'educational', 'category' )
	?: get_term_by( 'slug', 'guide', 'category' )
	?: get_term_by( 'name', 'آموزشی', 'category' );

$guides_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => 1,
);
if ( $guide_term instanceof WP_Term ) {
	$guides_args['cat'] = $guide_term->term_id;
}
$guides_q = new WP_Query( $guides_args );

// Section 4: Paginated Main Feed (5 per page)
$feed_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 5,
	'paged'               => $paged,
	'ignore_sticky_posts' => 1,
);
if ( is_category() ) {
	$feed_args['cat'] = get_queried_object_id();
} elseif ( is_tag() ) {
	$feed_args['tag_id'] = get_queried_object_id();
}
if ( $search_query !== '' ) {
	$feed_args['s'] = $search_query;
}
$feed_q = new WP_Query( $feed_args );

// Sidebar: 4 suggested / popular posts (by comment count, fallback latest)
$sidebar_q = new WP_Query( array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 4,
	'orderby'             => 'comment_count date',
	'order'               => 'DESC',
	'ignore_sticky_posts' => 1,
) );
?>

<main class="tno-blog-page" dir="rtl">
	<div class="tno-blog-container">

		<!-- ===== Intro Header ===== -->
		<header class="tno-blog-intro">
			<div class="tno-blog-intro__header">
				<h1 class="tno-blog-intro__title">
					<?php
					if ( is_category() ) {
						echo esc_html( single_cat_title( 'دسته بندی: ', false ) );
					} elseif ( $search_query !== '' ) {
						echo esc_html( 'نتایج جستجو برای: ' . $search_query );
					} else {
						echo 'به بلاگ ترندینو خوش آمدید!';
					}
					?>
				</h1>
				<p class="tno-blog-intro__desc">
					<?php
					if ( is_category() && category_description() ) {
						echo wp_kses_post( wp_strip_all_tags( category_description() ) );
					} else {
						echo 'جدیدترین اخبار دنیای فناوری، بررسی تخصصی محصولات دیجیتال و راهنمای خرید هوشمندانه در بلاگ ترندینو منتشر می‌شود تا همیشه یک قدم جلوتر باشید.';
					}
					?>
				</p>
			</div>
			<div class="tno-blog-intro__search">
				<form class="tno-blog-search" role="search" method="get" action="<?php echo esc_url( $blog_base_url ); ?>">
					<input type="hidden" name="post_type" value="post">
					<input
						type="search"
						name="s"
						class="tno-blog-search__input"
						value="<?php echo esc_attr( $search_query ); ?>"
						placeholder="جستجو بین مقالات ..."
					>
					<button type="submit" class="tno-blog-search__btn" aria-label="جستجو">
						<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
					</button>
				</form>
			</div>
		</header>

		<?php if ( ! $is_filtered && $paged === 1 ) : ?>

			<!-- ===== Section 1: Search + 3 Mini Cards | Large Featured Card ===== -->
			<section class="tno-blog-hero">
				<?php if ( $featured_q->have_posts() ) : $featured_q->the_post();
					$fid  = get_the_ID();
					$fcat = tno_blog_primary_category( $fid );
					?>
					<a href="<?php the_permalink(); ?>" class="tno-blog-featured">
						<img src="<?php echo esc_url( tno_blog_thumb_url( $fid, 'large' ) ); ?>" alt="<?php the_title_attribute(); ?>" class="tno-blog-featured__img">
						<div class="tno-blog-featured__top">
							<span class="tno-blog-cat-pill tno-blog-cat-pill--white"><?php echo esc_html( $fcat['name'] ); ?></span>
						</div>
						<div class="tno-blog-featured__bottom">
							<div class="tno-blog-featured__body">
								<h2 class="tno-blog-featured__title"><?php the_title(); ?></h2>
								<p class="tno-blog-featured__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							</div>
							<div class="tno-blog-featured__foot">
								<span class="tno-blog-featured__btn">
									<span>مشاهده مقاله</span>
									<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
								</span>
							</div>
						</div>
					</a>
				<?php wp_reset_postdata(); endif; ?>
				<div class="tno-blog-left">
					<?php if ( $hero_side_q->have_posts() ) : ?>
						<div class="tno-blog-hero__list">
							<?php while ( $hero_side_q->have_posts() ) : $hero_side_q->the_post();
								$pid = get_the_ID();
								$cat = tno_blog_primary_category( $pid );
								?>
								<a href="<?php the_permalink(); ?>" class="tno-blog-mini-card">
									<div class="tno-blog-mini-card__thumb">
										<img src="<?php echo esc_url( tno_blog_thumb_url( $pid, 'medium' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
									</div>
									<div class="tno-blog-mini-card__body">
										<h2 class="tno-blog-mini-card__title"><?php the_title(); ?></h2>
										<div class="tno-blog-mini-card__meta">
											<span class="tno-blog-cat-pill"><?php echo esc_html( $cat['name'] ); ?></span>
											<span><?php echo esc_html( tno_blog_jalali_date( $pid ) ); ?></span>
										</div>
									</div>
									<span class="tno-blog-mini-card__arrow" aria-hidden="true">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
									</span>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					<?php endif; ?>
				</div>

			</section>

			<!-- ===== Section 2: Latest Articles (4-Column Grid) ===== -->
			<?php if ( $latest_q->have_posts() ) : ?>
				<section class="tno-blog-latest">
					<div class="tno-blog-sec-head">
						<span class="tno-blog-glow-dot tno-blog-glow-dot--red" aria-hidden="true"></span>
						<h2 class="tno-blog-sec-title">جدیدترین مقالات</h2>
					</div>

					<div class="tno-blog-grid-4">
						<?php while ( $latest_q->have_posts() ) : $latest_q->the_post();
							$pid = get_the_ID();
							?>
							<a href="<?php the_permalink(); ?>" class="tno-blog-card">
								<div class="tno-blog-card__thumb">
									<img src="<?php echo esc_url( tno_blog_thumb_url( $pid, 'medium_large' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
								</div>
								<h3 class="tno-blog-card__title"><?php the_title(); ?></h3>
								<div class="tno-blog-card__foot">
									<span><?php echo esc_html( tno_blog_jalali_date( $pid ) ); ?></span>
									<span class="tno-blog-card__arrow" aria-hidden="true">
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
									</span>
								</div>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- ===== Section 3: Educational & Guides Gray Box (3-Column Grid) ===== -->
			<?php if ( $guides_q->have_posts() ) : ?>
				<section class="tno-blog-guides">
					<div class="tno-blog-guides__head">
						<div>
							<span class="tno-blog-guides__badge">آموزشی</span>
							<h2 class="tno-blog-guides__title">مقالات آموزشی و راهنمای خرید</h2>
						</div>
						<p class="tno-blog-guides__sub">
							جدیدترین مقالات تخصصی، نکات کاربردی و بررسی‌های عمیق، مقالات آموزشی و راهنمای خرید در دنیای تکنولوژی
						</p>
					</div>

					<div class="tno-blog-grid-3">
						<?php while ( $guides_q->have_posts() ) : $guides_q->the_post();
							$pid = get_the_ID();
							$cat = tno_blog_primary_category( $pid );
							?>
							<a href="<?php the_permalink(); ?>" class="tno-blog-guide-card">
								<div class="tno-blog-guide-card__thumb">
									<img src="<?php echo esc_url( tno_blog_thumb_url( $pid, 'medium_large' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
								</div>
								<div class="tno-blog-guide-card__meta">
									<span class="tno-blog-cat-pill"><?php echo esc_html( $cat['name'] ); ?></span>
									<span><?php echo esc_html( tno_blog_jalali_date( $pid ) ); ?></span>
								</div>
								<h3 class="tno-blog-guide-card__title"><?php the_title(); ?></h3>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

		<?php endif; ?>

		<!-- ===== Section 4: Paginated Main Feed + Sidebar ===== -->
		<section class="tno-blog-main-wrap">
			<div class="tno-blog-main">
				<div class="tno-blog-sec-head">
					<span class="tno-blog-glow-dot tno-blog-glow-dot--blue" aria-hidden="true"></span>
					<h2 class="tno-blog-sec-title">آخرین مطالب</h2>
				</div>

				<?php if ( $feed_q->have_posts() ) : ?>
					<div class="tno-blog-feed-list">
						<?php while ( $feed_q->have_posts() ) : $feed_q->the_post();
							$pid = get_the_ID();
							$cat = tno_blog_primary_category( $pid );
							?>
							<article class="tno-blog-feed-card">
								<a href="<?php the_permalink(); ?>" class="tno-blog-feed-card__thumb">
									<img src="<?php echo esc_url( tno_blog_thumb_url( $pid, 'medium_large' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
								</a>
								<div class="tno-blog-feed-card__body">
									<div class="tno-blog-feed-card__meta">
										<a href="<?php echo esc_url( $cat['url'] ); ?>" class="tno-blog-cat-pill"><?php echo esc_html( $cat['name'] ); ?></a>
										<span><?php echo esc_html( tno_blog_jalali_date( $pid ) ); ?></span>
									</div>
									<h3 class="tno-blog-feed-card__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
									<p class="tno-blog-feed-card__excerpt">
										<?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?>
									</p>
									<a href="<?php the_permalink(); ?>" class="tno-blog-feed-card__btn">
										<span>مطالعه</span>
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
									</a>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>

					<?php
					$total_pages = (int) $feed_q->max_num_pages;
					if ( $total_pages > 1 ) :
						$links = paginate_links( array(
							'base'      => str_replace( '999999999', '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
							'format'    => '?paged=%#%',
							'current'   => $paged,
							'total'     => $total_pages,
							'prev_text' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>',
							'next_text' => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>',
						) );
						if ( $links ) :
							?>
							<nav class="tno-blog-pagination" aria-label="صفحه‌بندی مقالات">
								<?php echo function_exists( 'tno_fa_digits' ) ? tno_fa_digits( $links ) : $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</nav>
						<?php endif; ?>
					<?php endif; ?>
				<?php else : ?>
					<div class="tno-blog-empty">
						<p>مقاله‌ای با این مشخصات یافت نشد.</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Sidebar: Suggested Articles -->
			<?php if ( $sidebar_q->have_posts() ) : ?>
				<aside class="tno-blog-sidebar">
					<div class="tno-blog-sidebar__head">
						<span class="tno-blog-sidebar__dot" aria-hidden="true"></span>
						<h3 class="tno-blog-sidebar__title">شاید خوشتان بیاید</h3>
					</div>

					<div class="tno-blog-sidebar__list">
						<?php while ( $sidebar_q->have_posts() ) : $sidebar_q->the_post();
							$pid = get_the_ID();
							$cat = tno_blog_primary_category( $pid );
							?>
							<a href="<?php the_permalink(); ?>" class="tno-blog-side-card">
								<div class="tno-blog-side-card__thumb">
									<img src="<?php echo esc_url( tno_blog_thumb_url( $pid, 'thumbnail' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
								</div>
								<div class="tno-blog-side-card__body">
									<h4 class="tno-blog-side-card__title"><?php the_title(); ?></h4>
									<div class="tno-blog-side-card__meta">
										<span class="tno-blog-cat-pill"><?php echo esc_html( $cat['name'] ); ?></span>
										<span><?php echo esc_html( tno_blog_jalali_date( $pid ) ); ?></span>
									</div>
								</div>
								<span class="tno-blog-mini-card__arrow" aria-hidden="true">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
								</span>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</aside>
			<?php endif; ?>
		</section>

	</div>
</main>
