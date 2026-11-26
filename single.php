<?php
declare(strict_types=1);

get_header();

while ( have_posts() ) :
	the_post();
	$post_id      = get_the_ID();
	$cat          = tno_blog_primary_category( $post_id );
	$date_label   = tno_blog_jalali_date( $post_id );
	$read_time    = tno_blog_reading_time( $post_id );
	$thumb_url    = tno_blog_thumb_url( $post_id, 'full' );
	$permalink    = get_permalink( $post_id );
	$title        = get_the_title( $post_id );

	$blog_page_id = (int) get_option( 'page_for_posts' );
	$blog_url     = $blog_page_id > 0 ? (string) get_permalink( $blog_page_id ) : ( get_post_type_archive_link( 'post' ) ?: home_url( '/' ) );

	// Related posts (same category, fallback latest)
	$cat_ids      = wp_get_post_categories( $post_id );
	$related_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'post__not_in'        => array( $post_id ),
		'ignore_sticky_posts' => 1,
	);
	if ( ! empty( $cat_ids ) ) {
		$related_args['category__in'] = $cat_ids;
	}
	$related_q = new WP_Query( $related_args );
	if ( ! $related_q->have_posts() && ! empty( $cat_ids ) ) {
		unset( $related_args['category__in'] );
		$related_q = new WP_Query( $related_args );
	}

	$tg_share_url = 'https://t.me/share/url?url=' . rawurlencode( (string) $permalink ) . '&text=' . rawurlencode( $title );
	$wa_share_url = 'https://wa.me/?text=' . rawurlencode( $title . "\n" . $permalink );
	?>

	<main class="tno-single-page" dir="rtl">
		<div class="tno-blog-container">

			<!-- Breadcrumb -->
			<nav class="tno-single-breadcrumb" aria-label="مسیر صفحه">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tno-single-bc-home">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
					<span>صفحه اصلی</span>
				</a>
				<span class="tno-single-bc-sep" aria-hidden="true">›</span>
				<a href="<?php echo esc_url( $blog_url ); ?>">بلاگ</a>
				<span class="tno-single-bc-sep" aria-hidden="true">›</span>
				<span class="tno-single-bc-current"><?php echo esc_html( $title ); ?></span>
			</nav>

			<!-- Hero Featured Banner -->
			<div class="tno-single-hero">
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="tno-single-hero__img">
			</div>

			<!-- Floating Overlapping Article Card -->
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'tno-single-card' ); ?>>
				<header class="tno-single-card__head">
					<div class="tno-single-meta-pills">
						<a href="<?php echo esc_url( $cat['url'] ); ?>" class="tno-single-pill tno-single-pill--cat">
							<span class="tno-single-pill__dot" aria-hidden="true"></span>
							<span><?php echo esc_html( $cat['name'] ); ?></span>
						</a>
						<span class="tno-single-pill"><?php echo esc_html( $date_label ); ?></span>
						<span class="tno-single-pill"><?php echo esc_html( $read_time ); ?></span>
					</div>

					<h1 class="tno-single-title"><?php echo esc_html( $title ); ?></h1>
				</header>

				<div class="tno-single-content">
					<?php the_content(); ?>
				</div>

				<!-- Blog Newsletter Strip -->
				<section class="tno-single-newsletter">
					<div class="tno-single-newsletter__info">
						<div class="tno-single-newsletter__title-row">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
							<strong>عضویت در خبرنامه بلاگ</strong>
						</div>
						<p>با عضویت در خبرنامه از جدیدترین مقالات باخبر شوید.</p>
					</div>

					<form class="tno-single-newsletter__form" id="tno-blog-newsletter-form" novalidate>
						<input
							type="email"
							name="email"
							id="tno-blog-newsletter-email"
							placeholder="ایمیل خود را وارد کنید"
							required
						>
						<button type="submit" id="tno-blog-newsletter-submit">عضویت در خبرنامه</button>
					</form>
				</section>
				<div class="tno-single-newsletter__feedback" id="tno-blog-newsletter-feedback" hidden aria-live="polite"></div>

				<!-- Share Row -->
				<footer class="tno-single-share">
					<span class="tno-single-share__label">به اشتراک بگذارید :</span>
					<div class="tno-single-share__actions">
						<a href="<?php echo esc_url( $tg_share_url ); ?>" target="_blank" rel="noopener noreferrer" class="tno-single-share-btn">
							<span class="tno-share-dot tno-share-dot--tg" aria-hidden="true">
								<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M9.9 15.2l-.4 5.6c.6 0 .8-.2 1.1-.5l2.7-2.6 5.6 4.1c1 .6 1.7.3 2-.9l3.6-16.8c.3-1.4-.5-2-1.5-1.6L2.3 10.6c-1.4.5-1.4 1.3-.2 1.7l5.4 1.7L20 6.1c.6-.4 1.1-.2.7.2"/></svg>
							</span>
							<span>تلگرام</span>
						</a>

						<a href="<?php echo esc_url( $wa_share_url ); ?>" target="_blank" rel="noopener noreferrer" class="tno-single-share-btn">
							<span class="tno-share-dot tno-share-dot--wa" aria-hidden="true">
								<svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.2c-.2.6-1.2 1.1-1.7 1.2-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.6-.6-2.7-1.2-4.5-3.9-4.6-4.1-.1-.2-1.1-1.5-1.1-2.8s.7-2 1-2.3c.2-.2.5-.3.7-.3h.5c.2 0 .4 0 .6.4.2.5.7 1.8.8 1.9.1.2.1.3 0 .5-.1.2-.2.3-.3.5-.2.2-.3.4-.5.5-.1.2-.3.3-.1.6.2.3.7 1.2 1.6 2 .9.9 1.9 1.2 2.2 1.4.3.1.5.1.7-.1.2-.2.8-.9 1-1.2.2-.3.4-.2.7-.1.3.1 1.7.8 2 .9.3.2.5.2.6.4.1.2.1.8-.1 1.4z"/></svg>
							</span>
							<span>واتساپ</span>
						</a>

						<button type="button" class="tno-single-share-btn" data-copy-url="<?php echo esc_attr( (string) $permalink ); ?>">
							<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
							<span data-copy-label="1">کپی لینک</span>
						</button>
					</div>
				</footer>
			</article>

			<!-- Related Posts -->
			<?php if ( $related_q->have_posts() ) : ?>
				<section class="tno-single-related">
					<div class="tno-blog-sec-head">
						<span class="tno-blog-glow-dot tno-blog-glow-dot--red" aria-hidden="true"></span>
						<h2 class="tno-blog-sec-title">مقالات پیشنهادی</h2>
					</div>

					<div class="tno-blog-grid-4">
						<?php while ( $related_q->have_posts() ) : $related_q->the_post();
							$rid = get_the_ID();
							?>
							<a href="<?php the_permalink(); ?>" class="tno-blog-card">
								<div class="tno-blog-card__thumb">
									<img src="<?php echo esc_url( tno_blog_thumb_url( $rid, 'medium_large' ) ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
								</div>
								<h3 class="tno-blog-card__title"><?php the_title(); ?></h3>
								<div class="tno-blog-card__foot">
									<span><?php echo esc_html( tno_blog_jalali_date( $rid ) ); ?></span>
									<span class="tno-blog-card__arrow" aria-hidden="true">
										<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
									</span>
								</div>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- Comments Section -->
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>

		</div>
	</main>

<?php endwhile;

get_footer();
