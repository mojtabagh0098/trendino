<?php
/**
 * Trendino — Demo Content Importer
 *
 * یک بار اجرا کنید تا متن، دسته‌بندی، محصولات، پست‌ها، برندها، منوها و تنظیمات
 * پشتیبانی لازم برای نشان دادن قالب به‌صورت معتبر ایجاد شود.
 *
 * نحوه استفاده:
 *   1. همکاری `trendino` رو نصب/فعال کنید.
 *   2. به مسیر `admin.php?page=trendino-demo-content` بروید و دکمه «محتوا را ایمپورت کن» را بزنید
 *      (یا مستقیم با lingering ?trendino_demo=1 و nonce فراخوانی کنید).
 *   3. در صورت نیاز به تنظیمات خودرسانه‌ای، از WP-CLI استفاده کنید:
 *      wp option update trendino_options_container_width 1240
 *
 * NOTE: این ایمپورتر idempotent است: دفعات زیاد تکرار نمی‌کند.
 * NOTE: محصولات ترجه WooCommerce؛ فعال بودن WooCommerce หมาเทศ.
 *
 * @package Trendino
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//----------------------------------------------------------------------------------
// ثبت منوی ابزار
//----------------------------------------------------------------------------------

add_action( 'admin_menu', 'trendino_demo_content_menu' );
function trendino_demo_content_menu() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	add_management_page(
		'Trendino Demo Content',
		'Trendino Demo',
		'manage_options',
		'trendino-demo-content',
		'trendino_demo_content_page'
	);
}

add_action( 'admin_init', 'trendino_demo_content_load' );
function trendino_demo_content_load() {
	if ( isset( $_GET['trendino_demo'] ) && check_admin_referer( 'trendino_nonce' ) ) {
		trendino_import_demo();
		wp_redirect( add_query_arg( 'trendino_demo_done', '1', admin_url( 'tools.php' ) ) );
		exit;
	}
}

//----------------------------------------------------------------------------------
// صفحه ابزار
//----------------------------------------------------------------------------------

function trendino_demo_content_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$done = isset( $_GET['trendino_demo_done'] );
	?>
	<div class="wrap">
		<h1>Trendino — Demo Content</h1>
		<p class="description">
			برای نمایش دقیق قالب، یکبار این ایمپورتر را اجرا کنید.
			دسته‌بندی‌ها، محصولات، برندها، پست‌ها و منوها هر بار تکراری نیستند.
		</p>
		<?php if ( $done ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>دمو محتوا با موفقیت ایمپورت شد.</p>
			</div>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'trendino_nonce' ); ?>
			<p class="submit">
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'trendino_demo', '1', admin_url() ), 'trendino_nonce' ) ); ?>"
				   class="button button-primary">
					محتوا را ایمپورت کن
				</a>
			</p>
		</form>
	</div>
	<?php
}

//----------------------------------------------------------------------------------
// اجرای اصلی
//----------------------------------------------------------------------------------

function trendino_import_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	trendino_import_theme_options();
	trendino_import_categories();
	trendino_import_brands();

	if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_create_product_attribute' ) ) {
		trendino_import_product_attributes();
		trendino_import_products();
	}

	trendino_import_posts();
	trendino_import_pages();
	trendino_import_menus();
	trendino_set_front_page();
}

//----------------------------------------------------------------------------------
// تنظیمات قالب
//----------------------------------------------------------------------------------

function trendino_import_theme_options() {
	$defaults = array(
		'container_width'          => 1240,
		'base_font_size'           => 15,
		'base_radius'              => 12,
		'card_radius'              => 12,
		'shop_columns'             => 4,
		'shop_products_count'      => 20,
		'persian_digits'           => true,
		'shop_layout'              => 'grid',
		'blog_layout'              => 'grid',
		'product_card_style'       => 'vertical',
		'product_compare'          => true,
		'wishlist_page_id'         => 0,
		'compare_page_id'          => 0,
	);

	foreach ( $defaults as $key => $value ) {
		$option_key = 'trendino_options_' . str_replace( '-', '_', $key );
		if ( false === get_option( $option_key ) ) {
			add_option( $option_key, $value );
		}
	}

	$color_map = array(
		'color_primary'         => '#e5152d',
		'color_primary_dark'    => '#c11026',
		'color_primary_soft'    => '#fdebee',
		'color_secondary'       => '#0d1b3e',
		'color_secondary_dark'  => '#091430',
		'color_secondary_soft'  => '#eaf0fb',
		'color_accent'          => '#1f5fd0',
		'color_success'         => '#01a049',
		'color_warning'         => '#f5a623',
		'color_text'            => '#15192b',
		'color_muted'           => '#8a8fa3',
		'color_border'          => '#ebecf2',
		'color_background'      => '#f4f5f9',
		'color_surface'         => '#ffffff',
		'color_footer_bg'       => '#0d1b3e',
		'color_footer_text'     => '#c6cbe0',
	);

	foreach ( $color_map as $key => $value ) {
		if ( false === get_option( $key ) ) {
			add_option( $key, $value );
		}
	}
}

//----------------------------------------------------------------------------------
// دسته‌بندی‌های محصول
//----------------------------------------------------------------------------------

function trendino_import_categories() {
	$main_categories = array(
		array( 'name' => 'موبایل و تبلت',      'slug' => 'mobile-tablet',       'description' => 'گوشی موبایل، تبلت و دستیارهای هوشمند' ),
		array( 'name' => 'لپ‌تاپ',             'slug' => 'laptop',              'description' => 'لپ‌تاپهای برای کار، بازی و تحصیل' ),
		array( 'name' => 'ساعت هوشمند',         'slug' => 'smartwatch',          'description' => 'ساعت‌های هوشمند و پیک‌آگاهی' ),
		array( 'name' => 'هدفون',              'slug' => 'headphones',          'description' => 'هدفون‌های کلاسیک، بی‌سیم و گیمینگ' ),
		array( 'name' => 'لوازم خانگی',         'slug' => 'home-appliances',     'description' => 'لوازم خانگی و دستگاه‌های اتاق' ),
		array( 'name' => 'آرایشی و سلامت',      'slug' => 'beauty-health',       'description' => 'مرسات آرایشی، لوازم بهداشتی و سلامت' ),
		array( 'name' => 'ورزش و سفر',         'slug' => 'sports-travel',       'description' => 'کالای ورزشی و سفر' ),
		array( 'name' => 'کتاب و مجله',         'slug' => 'books',               'description' => 'کتاب، مجله و رسانه دیجیتال' ),
		array( 'name' => 'اسباب‌بازی',          'slug' => 'toys',                'description' => 'اسباب‌بازی برای کودکان و بزرگسالان' ),
		array( 'name' => 'خودرو و اکسترنال',   'slug' => 'auto-accessories',    'description' => 'اتاق و اکسترنال خودرو' ),
		array( 'name' => 'گیمینگ',             'slug' => 'gaming',              'description' => 'کنسول بازی، کیبورد، موش و دسته‌بندی‌های مرتبط' ),
		array( 'name' => 'دوربین و فیلم',        'slug' => 'camera-video',        'description' => 'دوربین عکاسی، فیلم و دستگاه‌های تصویربرداری' ),
	);

	foreach ( $main_categories as $cat ) {
		$existing = get_term_by( 'slug', $cat['slug'], 'product_cat' );
		if ( ! $existing ) {
			wp_insert_term( $cat['name'], 'product_cat', array( 'slug' => $cat['slug'], 'description' => $cat['description'] ) );
		} else {
			wp_update_term( $existing->term_id, 'product_cat', array( 'name' => $cat['name'], 'description' => $cat['description'] ) );
		}
	}

	$sub_map = array(
		'mobile-tablet' => array( 'گوشی موبایل', 'تبلت', 'قاب و کاور', 'پاوربانک', 'شارژر و کابل', 'هندزفری' ),
		'laptop'        => array( 'لپ‌تاپ ۱۳ اینچی', 'لپ‌تاپ ۱۵ اینچی', 'لپ‌تاپ گیمینگ', 'تبلت' ),
		'headphones'    => array( 'هدفون بی‌سیم', 'هدفون کلاسیک', 'هدفون گیمینگ', 'هدفون 중랑جی' ),
		'smartwatch'    => array( 'ساعت هوشمند iOS', 'ساعت هوشمند اندروید', 'ساعت ورزشی', 'ساعت قیمت‌بالا' ),
	);

	foreach ( $sub_map as $parent_slug => $subs ) {
		$parent = get_term_by( 'slug', $parent_slug, 'product_cat' );
		if ( $parent ) {
			foreach ( $subs as $sub_name ) {
				$sub_slug = sanitize_title( $sub_name );
				$existing_sub = get_term_by( 'slug', $sub_slug, 'product_cat' );
				if ( ! $existing_sub ) {
					wp_insert_term( $sub_name, 'product_cat', array( 'slug' => $sub_slug, 'parent' => $parent->term_id ) );
				}
			}
		}
	}
}

//----------------------------------------------------------------------------------
// برندها (در صورت active بودن taxonomy برند)
//----------------------------------------------------------------------------------

function trendino_import_brands() {
	$brand_tax = trendino_get_brand_taxonomy();
	if ( ! $brand_tax ) {
		return;
	}

	$brands = array(
		'Samsung',
		'Apple',
		'Xiaomi',
		'Sony',
		'LG',
		'Asus',
		'Huawei',
		'Lenovo',
	);

	foreach ( $brands as $brand_name ) {
		$slug = sanitize_title( $brand_name );
		$existing = get_term_by( 'slug', $slug, $brand_tax );
		if ( ! $existing ) {
			wp_insert_term( $brand_name, $brand_tax, array( 'slug' => $slug ) );
		}
	}
}

function trendino_get_brand_taxonomy() {
	$candidates = array( 'product_brand', 'pwb-brand', 'yith_product_brand', 'berocket_brand', 'brand' );
	foreach ( $candidates as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			return $tax;
		}
	}
	return null;
}

//----------------------------------------------------------------------------------
// ویژگی‌های محصول (رنگ)
//----------------------------------------------------------------------------------

function trendino_import_product_attributes() {
	if ( ! function_exists( 'wc_create_product_attribute' ) ) {
		return;
	}
	$attribute_name = 'رنگ';
	if ( ! taxonomy_exists( 'pa_' . $attribute_name ) ) {
		wc_create_product_attribute( array(
			'name'      => $attribute_name,
			'slug'      => 'pa_' . sanitize_title( $attribute_name ),
			'visible'   => true,
			'variation' => false,
			'options'   => array( 'جنوبی', 'آبی', 'قرمز', 'سفید' ),
		) );
	}
}

//----------------------------------------------------------------------------------
// محصولات
//----------------------------------------------------------------------------------

function trendino_import_products() {
	$products = array(
		// ---- Stories widget ──────────────────────────────
		array(
			'name'       => 'گوشی موبایل سامسونگ Galaxy S24 Ultra',
			'base_price' => 42900000,
			'sale_price' => 34500000,
			'discount'   => 19,
			'category'   => 'mobile-tablet',
			'brand'      => 'Samsung',
			'new'        => true,
		),
		array(
			'name'       => 'هدفون بی‌سیم اپل AirPods Pro نسل دوم',
			'base_price' => 9500000,
			'sale_price' => 6700000,
			'discount'   => 29,
			'category'   => 'headphones',
			'brand'      => 'Apple',
			'new'        => true,
		),
		array(
			'name'       => 'لپ‌تاپ ۱۵ اینچی ایسوس مدل VivoBook',
			'base_price' => 28400000,
			'sale_price' => 22900000,
			'discount'   => 19,
			'category'   => 'laptop',
			'brand'      => 'Asus',
			'new'        => false,
		),
		array(
			'name'       => 'ساعت هوشمند شیائومی Watch S3 نسخه گلوبال',
			'base_price' => 8900000,
			'sale_price' => 5900000,
			'discount'   => 33,
			'category'   => 'smartwatch',
			'brand'      => 'Xiaomi',
			'new'        => false,
		),
		array(
			'name'       => 'کنسول بازی سونی PlayStation 5 Slim',
			'base_price' => 22000000,
			'sale_price' => 17900000,
			'discount'   => 18,
			'category'   => 'gaming',
			'brand'      => 'Sony',
			'new'        => true,
		),
		array(
			'name'       => 'دوربین عکاسی کانن EOS R50 کیت',
			'base_price' => 25800000,
			'sale_price' => 18500000,
			'discount'   => 28,
			'category'   => 'camera-video',
			'brand'      => 'Sony',
			'new'        => false,
		),
		array(
			'name'       => 'تبلت سامسونگ Galaxy Tab S9 FE',
			'base_price' => 13500000,
			'sale_price' => 9800000,
			'discount'   => 27,
			'category'   => 'mobile-tablet',
			'brand'      => 'Samsung',
			'new'        => true,
		),
		array(
			'name'       => 'اسپیکر بلوتوثی JBL Charge 5',
			'base_price' => 5200000,
			'sale_price' => 3800000,
			'discount'   => 26,
			'category'   => 'home-appliances',
			'brand'      => 'Sony',
			'new'        => false,
		),

		// ---- پیشنهاد شگفت‌انگیز ──────────────────────────
		array(
			'name'       => 'گوشی موبایل آیفون 15 پرو مکس ۲۵۶ GB',
			'base_price' => 52800000,
			'sale_price' => 40100000,
			'discount'   => 24,
			'category'   => 'mobile-tablet',
			'brand'      => 'Apple',
			'deal'       => true,
		),
		array(
			'name'       => 'هدفون بی‌سیم سامسونگ Galaxy Buds3 Pro',
			'base_price' => 12600000,
			'sale_price' => 8400000,
			'discount'   => 33,
			'category'   => 'headphones',
			'brand'      => 'Samsung',
			'deal'       => true,
		),
		array(
			'name'       => 'لپ‌تاپ مک‌بوک ایır M2 ۱۳ اینچی',
			'base_price' => 48500000,
			'sale_price' => 38700000,
			'discount'   => 20,
			'category'   => 'laptop',
			'brand'      => 'Apple',
			'deal'       => true,
		),
		array(
			'name'       => 'ساعت هوشمند اپل Watch Ultra',
			'base_price' => 28900000,
			'sale_price' => 21400000,
			'discount'   => 26,
			'category'   => 'smartwatch',
			'brand'      => 'Apple',
			'deal'       => true,
		),
		array(
			'name'       => 'تبلت آیفون آی‌پد Pro ۱۲.۹ نسل پنجم',
			'base_price' => 55200000,
			'sale_price' => 42600000,
			'discount'   => 22,
			'category'   => 'mobile-tablet',
			'brand'      => 'Apple',
			'deal'       => true,
		),

		// ---- جدیدترین محصولات ───────────────────────────
		array(
			'name'       => 'گوشی موبایل شیائومی 14 Pro ۵G',
			'base_price' => 18500000,
			'sale_price' => 14900000,
			'discount'   => 19,
			'category'   => 'mobile-tablet',
			'brand'      => 'Xiaomi',
			'new'        => true,
		),
		array(
			'name'       => 'لپ‌تاپ ایچ‌پی Pavilion ۱۶ برای گیمینگ',
			'base_price' => 32000000,
			'sale_price' => 25600000,
			'discount'   => 20,
			'category'   => 'laptop',
			'brand'      => 'Lenovo',
			'new'        => true,
		),
		array(
			'name'       => 'هدفون گیمینگ Razer Barracuda Pro',
			'base_price' => 11200000,
			'sale_price' => 7900000,
			'discount'   => 29,
			'category'   => 'headphones',
			'brand'      => 'Sony',
			'new'        => true,
		),
		array(
			'name'       => 'ساعت هوشمند گوگل Pixel Watch ۲',
			'base_price' => 15800000,
			'sale_price' => 11700000,
			'discount'   => 26,
			'category'   => 'smartwatch',
			'brand'      => 'Apple',
			'new'        => true,
		),
	);

	foreach ( $products as $i => $prod ) {
		$name = $prod['name'];

		$existing = wc_get_product_id_by_name( $name );
		if ( $existing ) {
			continue;
		}

		$sku = 'trendino-' . ( $i + 1 ) . '-' . sanitize_title( $name );

		$category_obj = get_term_by( 'slug', $prod['category'], 'product_cat' );
		$category_ids = $category_obj ? array( $category_obj->term_id ) : array();

		$brand_tax = trendino_get_brand_taxonomy();
		$brand_id  = 0;
		if ( $brand_tax && ! empty( $prod['brand'] ) ) {
			$brand_term = get_term_by( 'slug', sanitize_title( $prod['brand'] ), $brand_tax );
			$brand_id   = $brand_term ? $brand_term->term_id : 0;
		}

		$product = wc_create_product( array(
			'name'                 => $name,
			'sku'                  => $sku,
			'status'               => 'publish',
			'catalog_visibility'   => 'visible',
			'price'                => $prod['base_price'],
			'sale_price'           => isset( $prod['sale_price'] ) ? $prod['sale_price'] : '',
			'regular_price'        => $prod['base_price'],
			'manage_stock'         => false,
			'virtual'              => false,
			'downloadable'         => false,
			'short_description'    => isset( $prod['short_desc'] ) ? $prod['short_desc'] : '',
			'description'          => sprintf(
				/* translators: product name */
				__( 'نسخه کامل و باکیفیت %s را در فروشگاه ترندینو با گارانتی اصالت و ارسال سریع به سراسر کشور خرید کنید.', 'trendino' ),
				$name
			),
		) );

		if ( is_wp_error( $product ) ) {
			continue;
		}

		if ( ! empty( $category_ids ) ) {
			$product->set_category_ids( $category_ids );
		}
		if ( $brand_id ) {
			$product->set_brand_id( $brand_id );
		}
		if ( ! empty( $prod['new'] ) ) {
			$tag = get_term_by( 'slug', 'new', 'product_tag' );
			if ( $tag ) {
				$product->set_tag_ids( array( $tag->term_id ) );
			}
		}
		if ( ! empty( $prod['deal'] ) ) {
			$tag = get_term_by( 'slug', 'deal', 'product_tag' );
			if ( $tag ) {
				$product->set_tag_ids( array( $tag->term_id ) );
			}
		}

		$attachment_id = trendino_demo_create_placeholder_attachment( $name );
		if ( $attachment_id ) {
			$product->set_image_id( $attachment_id );
		}

		$product->save();

		if ( ! empty( $prod['sale_price'] ) ) {
			$product->set_regular_price( $prod['base_price'] );
			$product->set_sale_price( $prod['sale_price'] );
			$product->save();
		}
	}
}

//----------------------------------------------------------------------------------
// پست‌های وبلاگ
//----------------------------------------------------------------------------------

function trendino_import_posts() {
	$posts = array(
		array(
			'title'   => 'راهنمای خرید گوشی موبایل در سال ۱۴۰۵',
			'content' => '
				<p>در این راهنمای کامل به بررسی معیارهای کلیدی برای خرید گوشی موبایل در سال ۱۴۰۵ می‌پردازیم.
				از چیپست تا دوربین، از باتری تا نرم‌افزار — همه چیز را در کنار هم مرور می‌کنیم.</p>
				<h3>چیپست مهم‌ترین فاکتور است</h3>
				<p>پردازنده گوشی، مرکزی است که همه عملیات را مدیریت می‌کند. پردازنده‌های جدیدتر معمولاً مصرف انرژی کمتری دارند
				و عملکرد بهتری در بازی‌ها و اپلیکیشن‌های سنگین ارائه می‌دهند.</p>
				<h3>دوربین و عکس‌برداری</h3>
				<p>عدد مگاپیکسل تنها معیار نیست. سایز سنسور، نسبت اطار و پردازش نرم‌افزاری دوربین سه عامل کلیدی‌تری هستند.</p>
			',
			'category' => 'فناوری',
			'excerpt'  => 'در این مقاله به بررسی نکات کلیدی و مهمی می‌پردازیم که پیش از خرید گوشی موبایل باید بدانید.',
		),
		array(
			'title'   => '۱۰ نکته برای افزایش عمر باتری لپ‌تاپ',
			'content' => '
				<p>از تنظیمات ساده سیستم‌عامل تا انتخاب شارژر مناسب — این نکات به شما کمک می‌کند باتری لپ‌تاپ
				در طول زمان بهتر باقی بماند.</p>
				<ol>
					<li>روشنایی دسکتاپ را کاهش دهید</li>
					<li>کارهای پس‌زمینه را ببندید</li>
					<li>اندازه نمایشگر را تنظیم کنید</li>
					<li>از حالت انرژی‌سAVING استفاده کنید</li>
					<li>در صورت امکان از برق پایدار استفاده کنید</li>
					<li>دما را در حد مجاز نگه دارید</li>
					<li>بازآرایی باتری هر چند ماه یکبار</li>
					<li>تاریخ نسخه سیستم‌عامل را کنترل کنید</li>
					<li>از شارژر استاندارد استفاده کنید</li>
					<li>باتری را هرگز به ۰ درصد نمی‌گذارید</li>
				</ol>
			',
			'category' => 'فناوری',
		),
		array(
			'title'   => 'بهترین هدفون‌های زیر ۵ میلیون تومان',
			'content' => '
				<p>بودجه شما ۵ میلیون تومان است؟ این لیست بهترین هدفون‌های موجود در بازار ایران در این بودجه را معرفی می‌کند.</p>
				<p>هدفون‌های زیر ۵ میلیون تومان امروزه کیفیت صوتی شگفت‌انگیز ارائه می‌دهند —
				در برخی موارد حتی از مدل‌های پریمیوم‌تر بهتر هستند.</p>
			',
			'category' => 'فناوری',
		),
		array(
			'title'   => 'مقایسه کامل PS5 و Xbox Series X',
			'content' => '
				<p>دو کنسول نسل پنجم در معرض مقایسه: پردازنده، گرافیک، کیفیت بازی‌ها، قیمت و اکوسیستم.
				این مقاله هر دو را کنار هم قرار می‌دهد تا تصمیم نهایی را به شما بگذارد.</p>
				<h3>پرفورمنس</h3>
				<p>هر دو کنسول در خروجی ۴K و ۶۰ فریم بر ثانیه عملکرد منسجم می‌دهند،
				اما در آزمون‌های خاص تفاوت‌های ظریفی وجود دارد.</p>
			',
			'category' => 'فناوری',
		),
	);

	foreach ( $posts as $post_data ) {
		$slug = sanitize_title( $post_data['title'] );
		if ( get_page_by_path( $slug, OBJECT, 'post' ) ) {
			continue;
		}

		$cat_obj = get_term_by( 'name', $post_data['category'], 'category' );
		if ( ! $cat_obj ) {
			$term = wp_insert_term( $post_data['category'], 'category' );
			if ( ! is_wp_error( $term ) ) {
				$cat_obj = get_term_by( 'slug', $term['slug'], 'category' );
			}
		}

		$attachment_id = trendino_demo_create_placeholder_attachment( $post_data['title'] );

		$post_id = wp_insert_post( array(
			'post_title'   => $post_data['title'],
			'post_content' => $post_data['content'],
			'post_excerpt' => $post_data['excerpt'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_name'    => $slug,
		), true );

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		if ( $cat_obj ) {
			wp_set_post_categories( $post_id, array( $cat_obj->term_id ) );
		}
		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}
}

//----------------------------------------------------------------------------------
// صفحات
//----------------------------------------------------------------------------------

function trendino_import_pages() {
	$pages = array(
		array( 'title' => 'تماس با ما',     'slug' => 'contact',       'content' => '<p>با تیم پشتیبانی ترندینو در تماس باشید.</p><p><strong>تلفن پشتیبانی:</strong> ۰۲۱-۹۱۰۰۲۰۳۰</p><p><strong>ایمیل:</strong> info@trendino.ir</p><p>ساعت کاری: ۸ صبح تا ۱۰ شب، دوشنبه تا جمعه.</p>' ),
		array( 'title' => 'وبلاگ',           'slug' => 'blog',           'content' => '<p>مقالات و راهنماهای مرتبط با تکنولوژی و خرید هوشمند.</p>' ),
		array( 'title' => 'درباره ما',       'slug' => 'about',          'content' => '<p>ترندینو بزرگ‌ترین فروشگاه اینترنتی کشور است که با هدف ارائه کالای اصل و باکیفیت فعالیت می‌کند.</p>' ),
		array( 'title' => 'شیوه بازگشت کالا','slug' => 'return-policy',  'content' => '<p>شرایط و روش‌های بازگرداندن کالا را در این صفحه مطالعه کنید.</p>' ),
		array( 'title' => 'حریم خصوصی',     'slug' => 'privacy',        'content' => '<p>سیاست خصوصی ترندینو.</p>' ),
		array( 'title' => 'پرسش‌های متداول', 'slug' => 'faq',            'content' => '<p>پاسخ به پرسش‌های پرتکرار خریداران.</p>' ),
		array( 'title' => 'نحوه ثبت سفارش',  'slug' => 'how-to-order',   'content' => '<p>مراحل ثبت سفارش در فروشگاه ترندینو را در اینجا پیدا کنید.</p>' ),
		array( 'title' => 'شیوه‌های پرداخت',  'slug' => 'payment-methods','content' => '<p>روش‌های پرداخت موجود در ترندینو.</p>' ),
		array( 'title' => 'پیگیری سفارش',    'slug' => 'order-tracking', 'content' => '<p>از طریق این صفحه می‌توانید سفارش خود را پیگیری کنید.</p>' ),
		array( 'title' => 'شرایط استفاده',   'slug' => 'terms',          'content' => '<p>شرایط و قوانین استفاده از سایت ترندینو.</p>' ),
		array( 'title' => 'فرصت‌های شغلی',   'slug' => 'jobs',           'content' => '<p>پوزیشن‌های شغلی در ترندینو.</p>' ),
		array( 'title' => 'همکاری در فروش',  'slug' => 'affiliate',      'content' => '<p>برنامه همکاری در فروش ترندینو.</p>' ),
		array( 'title' => 'صفحه اصلی',       'slug' => 'home',           'content' => '' ),
	);

	foreach ( $pages as $page_data ) {
		if ( get_page_by_path( $page_data['slug'], OBJECT, 'page' ) ) {
			continue;
		}

		wp_insert_post( array(
			'post_title'   => $page_data['title'],
			'post_content' => $page_data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => $page_data['slug'],
		) );
	}
}

//----------------------------------------------------------------------------------
// منوها
//----------------------------------------------------------------------------------

function trendino_import_menus() {
	trendino_sync_menu( 'primary', array(
		array( 'label' => 'صفحه اصلی',  'url' => home_url( '/' ),                         'active' => true ),
		array( 'label' => 'شگفت‌انگیزها','url' => wc_get_page_permalink( 'shop' ) ?: home_url( '/shop' ), 'highlight' => true ),
		array(
			'label' => 'فروشگاه',
			'url'   => wc_get_page_permalink( 'shop' ) ?: home_url( '/shop' ),
			'sub'   => array(
				'پرفروش‌ترین‌ها' => home_url( '/shop/?orderby=best_selling' ),
				'جدیدترین‌ها'     => home_url( '/shop/?orderby=date' ),
				'تخفیف‌دارها'    => home_url( '/shop/?filter=onsale' ),
			),
		),
		array( 'label' => 'برندها',  'url' => home_url( '/brand' ) ),
		array( 'label' => 'وبلاگ',   'url' => get_permalink( get_page_by_path( 'blog' ) ) ?: home_url( '/blog' ) ),
		array( 'label' => 'تماس با ما','url' => get_permalink( get_page_by_path( 'contact' ) ) ?: home_url( '/contact' ) ),
	) );

	trendino_sync_menu( 'topbar', array(
		array( 'label' => 'پیگیری سفارش', 'url' => wc_get_page_permalink( 'orders' ) ?: home_url( '/orders' ) ),
		array( 'label' => 'تماس با ما',    'url' => get_permalink( get_page_by_path( 'contact' ) ) ?: home_url( '/contact' ) ),
		array( 'label' => 'سوالات متداول', 'url' => get_permalink( get_page_by_path( 'faq' ) ) ?: home_url( '/faq' ) ),
	) );

	trendino_sync_menu( 'footer-one', array(
		array( 'label' => 'پرسش‌های متداول',     'url' => get_permalink( get_page_by_path( 'faq' ) ) ?: home_url( '/faq' ) ),
		array( 'label' => 'شیوه بازگشت کالا',    'url' => get_permalink( get_page_by_path( 'return-policy' ) ) ?: home_url( '/return-policy' ) ),
		array( 'label' => 'شرایط استفاده',        'url' => get_permalink( get_page_by_path( 'terms' ) ) ?: home_url( '/terms' ) ),
		array( 'label' => 'حریم خصوصی',           'url' => get_permalink( get_page_by_path( 'privacy' ) ) ?: home_url( '/privacy' ) ),
	) );

	trendino_sync_menu( 'footer-two', array(
		array( 'label' => 'نحوه ثبت سفارش',  'url' => get_permalink( get_page_by_path( 'how-to-order' ) ) ?: home_url( '/how-to-order' ) ),
		array( 'label' => 'شیوه‌های پرداخت',  'url' => get_permalink( get_page_by_path( 'payment-methods' ) ) ?: home_url( '/payment-methods' ) ),
		array( 'label' => 'شیوه ارسال سفارش','url' => home_url( '/shipping' ) ),
		array( 'label' => 'پیگیری سفارش',    'url' => wc_get_page_permalink( 'orders' ) ?: home_url( '/orders' ) ),
	) );

	trendino_sync_menu( 'footer-three', array(
		array( 'label' => 'درباره ما',       'url' => get_permalink( get_page_by_path( 'about' ) ) ?: home_url( '/about' ) ),
		array( 'label' => 'تماس با ما',      'url' => get_permalink( get_page_by_path( 'contact' ) ) ?: home_url( '/contact' ) ),
		array( 'label' => 'فرصت‌های شغلی',  'url' => get_permalink( get_page_by_path( 'jobs' ) ) ?: home_url( '/jobs' ) ),
		array( 'label' => 'همکاری در فروش', 'url' => get_permalink( get_page_by_path( 'affiliate' ) ) ?: home_url( '/affiliate' ) ),
	) );
}

function trendino_sync_menu( $slug, $items ) {
	$locations = get_theme_mod( 'nav_menu_locations' );
	if ( empty( $locations[ $slug ] ) ) {
		$term = wp_insert_term( ucfirst( $slug ), 'nav_menu' );
		if ( ! is_wp_error( $term ) ) {
			$locations[ $slug ] = (int) $term['term_id'];
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	$menu_id = $locations[ $slug ];
	if ( ! $menu_id ) {
		return;
	}

	foreach ( $items as $item ) {
		if ( ! empty( $item['sub'] ) ) {
			$parent = trendino_menu_add_item( $menu_id, $item['label'], $item['url'] );
			foreach ( $item['sub'] as $sub_label => $sub_url ) {
				trendino_menu_add_item( $menu_id, $sub_label, $sub_url, $parent );
			}
		} else {
			trendino_menu_add_item( $menu_id, $item['label'], $item['url'], null, $item );
		}
	}
}

function trendino_menu_add_item( $menu_id, $label, $url, $parent = null, $props = array() ) {
	$item = array(
		'menu-item-object-id'   => 0,
		'menu-item-object'      => 'custom',
		'menu-item-type'        => 'custom',
		'menu-item-title'       => $label,
		'menu-item-url'         => $url,
		'menu-item-description' => '',
		'menu-item-classes'     => array(),
		'menu-item-xfn'         => '',
		'menu-item-status'      => 'publish',
	);

	if ( $parent ) {
		$item['menu-item-parent-id'] = $parent;
	}
	if ( ! empty( $props['active'] ) ) {
		$item['menu-item-classes'][] = 'current-menu-item';
	}
	if ( ! empty( $props['highlight'] ) ) {
		$item['menu-item-classes'][] = 'is-highlight';
	}

	return wp_update_nav_menu_item( $menu_id, 0, $item );
}

//----------------------------------------------------------------------------------
// صفحه اصلی (front page)
//----------------------------------------------------------------------------------

function trendino_set_front_page() {
	$home_page = get_page_by_path( 'home', OBJECT, 'page' );
	if ( $home_page ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_page->ID );
	}
}

//----------------------------------------------------------------------------------
// membuat تصویر Shutterstock SVG برای هر محتوا
//----------------------------------------------------------------------------------

function trendino_demo_create_placeholder_attachment( $title ) {
	$slug = sanitize_title( $title );

	$attachment = get_posts( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'name'        => 'trendino-demo-' . $slug,
		'numberposts' => 1,
	) );

	if ( ! empty( $attachment ) ) {
		return $attachment[0]->ID;
	}

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/svg+xml',
		'post_name'      => 'trendino-demo-' . $slug,
		'post_title'     => $title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	) );

	if ( is_wp_error( $attachment_id ) || empty( $attachment_id ) ) {
		return 0;
	}

	$svg = trendino_demo_svg_for_title( $title );

	$upload_dir = wp_upload_dir();
	$file_path  = trailingslashit( $upload_dir['basedir'] ) . 'trendino-demo-' . sanitize_file_name( $slug ) . '.svg';

	if ( file_put_contents( $file_path, $svg ) !== false ) {
		wp_update_attachment_metadata( $attachment_id, array(
			'width'   => 800,
			'height'  => 800,
			'filesize'=> strlen( $svg ),
		) );
	}

	return $attachment_id;
}

function trendino_demo_svg_for_title( $title ) {
	$hash = md5( $title );
	$h    = base_convert( substr( $hash, 0, 3 ), 16, 10 );
	$r    = ( $h % 256 );
	$g    = ( ( $h >> 8 ) % 256 );
	$b    = ( ( $h >> 16 ) % 256 );

	$label = mb_substr( $title, 0, 12, 'UTF-8' );

	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
			<defs>
				<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
					<stop offset="0" stop-color="rgb(%d,%d,%d)"/>
					<stop offset="1" stop-color="rgb(%d,%d,%d)"/>
				</linearGradient>
			</defs>
			<rect width="800" height="800" fill="url(#g)"/>
			<circle cx="400" cy="360" r="200" fill="rgba(255,255,255,.12)"/>
			<circle cx="400" cy="360" r="140" fill="rgba(255,255,255,.18)"/>
			<text x="400" y="420" text-anchor="middle"
				  font-family="Tahoma, sans-serif" font-size="64" font-weight="700"
				  fill="#ffffff" opacity=".9">%s</text>
		</svg>',
		$r, $g, $b,
		min( 255, $r + 40 ), min( 255, $g + 40 ), min( 255, $b + 40 ),
		htmlspecialchars( $label, ENT_QUOTES, 'UTF-8' )
	);
}

//----------------------------------------------------------------------------------
// تگ‌های محصول
//----------------------------------------------------------------------------------

add_action( 'init', 'trendino_demo_ensure_tags', 100 );
function trendino_demo_ensure_tags() {
	$tags = array( 'new', 'deal', 'best-seller' );
	foreach ( $tags as $tag ) {
		if ( ! get_term_by( 'slug', $tag, 'product_tag' ) ) {
			wp_insert_term( ucfirst( $tag ), 'product_tag', array( 'slug' => $tag ) );
		}
	}
}

//----------------------------------------------------------------------------------
// اعلان پس از ایمپورت
//----------------------------------------------------------------------------------

add_action( 'admin_notices', 'trendino_demo_admin_notice' );
function trendino_demo_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['trendino_demo_done'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>محتوای دمو ترندینو با موفقیت ایمپورت شد.</p></div>';
	}
}
