<?php
/**
 * PDP Modals Markup
 *
 * @package Trendino
 */

defined( 'ABSPATH' ) || exit;

$product = wc_get_product( get_queried_object_id() );
if ( ! $product ) {
	return;
}

$product_id    = $product->get_id();
$product_title = $product->get_name();
$product_thumb = $product->get_image( 'woocommerce_thumbnail', [ 'class' => 'tno-modal-product__img', 'loading' => 'lazy' ] );
$terms_url     = apply_filters( 'tno_terms_page_url', home_url( '/terms/' ) );
$auth_url      = apply_filters(
	'tno_pdp_auth_url',
	add_query_arg( 'redirect_to', rawurlencode( get_permalink( $product_id ) ), wc_get_page_permalink( 'myaccount' ) ),
	$product_id
);

$close_icon_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$star_icon_svg  = '<svg class="tno-star-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2.75L14.85 8.53L21.23 9.46L16.61 13.96L17.7 20.31L12 17.31L6.3 20.31L7.39 13.96L2.77 9.46L9.15 8.53L12 2.75Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>';
?>

<div class="tno-modal-root" id="tno-pdp-modal-root" dir="rtl" aria-hidden="true">

	<!-- 1. مودال ثبت دیدگاه -->
	<div class="tno-modal" id="tno-modal-review" role="dialog" aria-modal="true" aria-labelledby="tno-modal-review-title" hidden>
		<div class="tno-modal__backdrop" data-tno-modal-close></div>
		<div class="tno-modal__dialog">
			<div class="tno-modal__header">
				<h3 class="tno-modal__title" id="tno-modal-review-title">ثبت دیدگاه</h3>
				<button type="button" class="tno-modal__close" data-tno-modal-close aria-label="بستن">
					<?php echo $close_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>

			<form class="tno-modal__form" id="tno-form-review" novalidate>
				<div class="tno-modal-product">
					<div class="tno-modal-product__thumb">
						<?php echo wp_kses_post( $product_thumb ); ?>
					</div>
					<div class="tno-modal-product__info">
						<div class="tno-modal-product__title"><?php echo esc_html( $product_title ); ?></div>
						<div class="tno-modal-rating">
							<span class="tno-modal-rating__label">امتیاز دهید به این محصول</span>
							<div class="tno-stars" role="radiogroup" aria-label="امتیاز به محصول">
								<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
									<button
										type="button"
										class="tno-star-btn"
										role="radio"
										aria-checked="false"
										aria-label="<?php echo esc_attr( sprintf( '%d ستاره', $i ) ); ?>"
										data-star-value="<?php echo esc_attr( (string) $i ); ?>"
									>
										<?php echo $star_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</button>
								<?php endfor; ?>
							</div>
							<input type="hidden" name="rating" value="0" required>
						</div>
					</div>
				</div>

				<div class="tno-modal-field">
					<label class="tno-modal-field__label" for="tno-review-content">متن دیدگاه</label>
					<textarea
						id="tno-review-content"
						name="content"
						class="tno-modal-textarea"
						rows="5"
						maxlength="2000"
						placeholder="تجربه و نظر خود درباره این محصول را با دیگران به اشتراک بگذارید..."
						required
					></textarea>
				</div>

				<div class="tno-modal-alert" role="alert" aria-live="polite" hidden></div>

				<button type="submit" class="tno-modal-btn">
					<span class="tno-modal-btn__text">ثبت دیدگاه</span>
					<span class="tno-modal-btn__spinner" aria-hidden="true"></span>
				</button>

				<p class="tno-modal-terms">
					ثبت دیدگاه به معنای موافقت با
					<a href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener noreferrer">قوانین ترندینو</a>
					می باشد
				</p>
			</form>
		</div>
	</div>

	<!-- 2. مودال ثبت پرسش -->
	<div class="tno-modal" id="tno-modal-question" role="dialog" aria-modal="true" aria-labelledby="tno-modal-question-title" hidden>
		<div class="tno-modal__backdrop" data-tno-modal-close></div>
		<div class="tno-modal__dialog">
			<div class="tno-modal__header">
				<h3 class="tno-modal__title" id="tno-modal-question-title">ثبت پرسش</h3>
				<button type="button" class="tno-modal__close" data-tno-modal-close aria-label="بستن">
					<?php echo $close_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>

			<form class="tno-modal__form" id="tno-form-question" novalidate>
				<div class="tno-modal-field">
					<label class="tno-modal-field__label" for="tno-question-content">پرسش خود را درمورد محصول مطرح کنید</label>
					<textarea
						id="tno-question-content"
						name="content"
						class="tno-modal-textarea"
						rows="5"
						maxlength="2000"
						placeholder="پرسش خود را درباره این محصول بنویسید..."
						required
					></textarea>
				</div>

				<div class="tno-modal-alert" role="alert" aria-live="polite" hidden></div>

				<button type="submit" class="tno-modal-btn">
					<span class="tno-modal-btn__text">ثبت پرسش</span>
					<span class="tno-modal-btn__spinner" aria-hidden="true"></span>
				</button>

				<p class="tno-modal-terms">
					ثبت پرسش به معنای موافقت با
					<a href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener noreferrer">قوانین انتشار ترندینو</a>
					می باشد
				</p>
			</form>
		</div>
	</div>

	<!-- 3. مودال پاسخ به پرسش -->
	<div class="tno-modal" id="tno-modal-answer" role="dialog" aria-modal="true" aria-labelledby="tno-modal-answer-title" hidden>
		<div class="tno-modal__backdrop" data-tno-modal-close></div>
		<div class="tno-modal__dialog">
			<div class="tno-modal__header">
				<h3 class="tno-modal__title" id="tno-modal-answer-title">به این پرسش پاسخ دهید</h3>
				<button type="button" class="tno-modal__close" data-tno-modal-close aria-label="بستن">
					<?php echo $close_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>

			<form class="tno-modal__form" id="tno-form-answer" novalidate>
				<input type="hidden" name="question_id" id="tno-answer-question-id" value="0">

				<div class="tno-modal-question-preview" id="tno-answer-question-preview"></div>

				<div class="tno-modal-field">
					<label class="screen-reader-text" for="tno-answer-content">متن پاسخ</label>
					<textarea
						id="tno-answer-content"
						name="content"
						class="tno-modal-textarea"
						rows="5"
						maxlength="2000"
						placeholder="پاسخ خود را به این پرسش بنویسید..."
						required
					></textarea>
				</div>

				<div class="tno-modal-alert" role="alert" aria-live="polite" hidden></div>

				<button type="submit" class="tno-modal-btn">
					<span class="tno-modal-btn__text">ثبت پاسخ</span>
					<span class="tno-modal-btn__spinner" aria-hidden="true"></span>
				</button>

				<p class="tno-modal-terms">
					ثبت پاسخ به معنای موافقت با
					<a href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener noreferrer">قوانین انتشار ترندینو</a>
					می باشد
				</p>
			</form>
		</div>
	</div>

	<!-- 4. مودال الزام ورود به حساب کاربری (مشترک برای دیدگاه و پرسش/پاسخ) -->
	<div class="tno-modal tno-modal--compact" id="tno-modal-auth-gate" role="dialog" aria-modal="true" aria-labelledby="tno-modal-auth-title" hidden>
		<div class="tno-modal__backdrop" data-tno-modal-close></div>
		<div class="tno-modal__dialog">
			<div class="tno-modal__header tno-modal__header--auth">
				<h3 class="tno-modal__title" id="tno-modal-auth-title">برای ثبت پرسش و پاسخ باید وارد حساب کاربری خود شوید</h3>
				<button type="button" class="tno-modal__close" data-tno-modal-close aria-label="بستن">
					<?php echo $close_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>

			<div class="tno-modal__auth-body">
				<a href="<?php echo esc_url( $auth_url ); ?>" class="tno-modal-btn" id="tno-modal-auth-cta">
					<span class="tno-modal-btn__text">ورود به حساب کاربری</span>
				</a>
			</div>
		</div>
	</div>

</div>