<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

$post_id  = get_the_ID();
$comments = get_comments( array(
	'post_id' => $post_id,
	'status'  => 'approve',
	'order'   => 'DESC',
) );

$user          = wp_get_current_user();
$is_logged     = is_user_logged_in();
$default_name  = $is_logged ? trim( $user->first_name . ' ' . $user->last_name ) ?: $user->display_name : '';
$default_email = $is_logged ? $user->user_email : '';
?>

<section class="tno-comments-card" id="comments" dir="rtl">
	<div class="tno-blog-sec-head tno-comments-card__head">
		<span class="tno-blog-glow-dot tno-blog-glow-dot--blue" aria-hidden="true"></span>
		<h2 class="tno-blog-sec-title">نظرات کاربران</h2>
	</div>

	<div class="tno-comments-grid">
		<!-- Right: Comment Form -->
		<div class="tno-comments-form-col">
			<?php if ( comments_open( $post_id ) ) : ?>
				<form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" class="tno-comments-form" id="commentform">
					<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( (string) $post_id ); ?>" id="comment_post_ID">
					<input type="hidden" name="comment_parent" value="0" id="comment_parent">

					<div class="tno-comments-field">
						<label for="author">نام و نام خانوادگی <span class="tno-req">*</span></label>
						<input
							type="text"
							id="author"
							name="author"
							value="<?php echo esc_attr( $default_name ); ?>"
							placeholder="نام و نام خانوادگی خود را وارد نمایید"
							required
						>
					</div>

					<div class="tno-comments-field">
						<label for="email">ایمیل <span class="tno-req">*</span></label>
						<input
							type="email"
							id="email"
							name="email"
							value="<?php echo esc_attr( $default_email ); ?>"
							placeholder="ایمیل خود را وارد نمایید"
							required
						>
					</div>

					<div class="tno-comments-field">
						<label for="comment">دیدگاه <span class="tno-req">*</span></label>
						<textarea
							id="comment"
							name="comment"
							rows="4"
							placeholder="دیدگاه خود را بنویسید..."
							required
						></textarea>
					</div>

					<?php do_action( 'comment_form_after_fields' ); ?>

					<button type="submit" name="submit" id="submit" class="tno-comments-submit">ارسال نظر</button>
				</form>
			<?php else : ?>
				<p class="tno-comments-closed">ارسال دیدگاه برای این نوشته بسته شده است.</p>
			<?php endif; ?>
		</div>

		<!-- Left: Approved Comments List -->
		<div class="tno-comments-list-col">
			<?php if ( ! empty( $comments ) ) : ?>
				<div class="tno-comments-scroll">
					<?php foreach ( $comments as $c ) :
						$c_ts = strtotime( $c->comment_date );
						if ( $c_ts && function_exists( 'tno_gregorian_to_jalali' ) && function_exists( 'tno_fa_digits' ) ) {
							$j      = tno_gregorian_to_jalali( (int) $c_ts );
							$c_date = sprintf( '%s %s %s', tno_fa_digits( (string) $j['day'] ), $j['month_name'], tno_fa_digits( (string) $j['year'] ) );
						} else {
							$c_date = get_comment_date( '', $c );
						}
						?>
						<article class="tno-comment-item" id="comment-<?php echo esc_attr( (string) $c->comment_ID ); ?>">
							<header class="tno-comment-item__head">
								<span class="tno-comment-item__avatar" aria-hidden="true">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
								</span>
								<div class="tno-comment-item__meta">
									<strong class="tno-comment-item__author"><?php echo esc_html( $c->comment_author ); ?></strong>
									<span class="tno-comment-item__date"><?php echo esc_html( $c_date ); ?></span>
								</div>
							</header>
							<div class="tno-comment-item__body">
								<?php echo wpautop( esc_html( wp_strip_all_tags( (string) $c->comment_content ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="tno-comments-empty">
					<p>هنوز دیدگاهی برای این مقاله ثبت نشده است. اولین نفری باشید که نظر می‌دهد!</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
