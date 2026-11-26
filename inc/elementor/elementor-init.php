<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * افزودن دستهبندی جدید به المنتور
 */
function tno_add_elementor_widget_categories( $elements_manager ) {
	$elements_manager->add_category(
		'trendino',
		[
			'title' => 'Trendino',
			'icon'  => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'tno_add_elementor_widget_categories', 99 );

/**
 * ثبت ویجتهاالمنتور برای قالب
 */
function tno_register_elementor_widgets( $widgets_manager ) {

	// 1. فراخوانی فایلهای حاوی کلاس ویجتها
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-box-title.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-stories.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-hero.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-categories.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-flash-deal.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-carousel.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-best-sellers.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-promo-banner.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-section-header.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-taste-box.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-articles.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-archive-plp.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-page-intro.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-faq.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-about-stats.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-about-timeline.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-about-team.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-about-testimonials.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-contact-cards.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-support-banner.php' );
	require_once( get_template_directory() . '/inc/elementor/widgets/tno-social-cards.php' );


	// 2. ثبت (Register) کردن ویجتها در المنتور
	$widgets_manager->register( new \Elementor_TNO_Box_Title_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Stories_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Hero_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Categories_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Flash_Deal_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Carousel_Widget() );
	$widgets_manager->register( new \Elementor_Trendino_Products_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Promo_Banner_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Section_Header_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Taste_Box_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Articles_Widget() );
	$widgets_manager->register( new \Elementor_TNO_PLP_Categories_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Archive_Products_Widget() );
	$widgets_manager->register( new \Elementor_TNO_Category_SEO_Widget() );
	$widgets_manager->register( new \TNO_Widget_Page_Intro() );
	$widgets_manager->register( new \TNO_Widget_FAQ() );
	$widgets_manager->register( new \TNO_Widget_About_Stats() );
	$widgets_manager->register( new \TNO_Widget_About_Timeline() );
	$widgets_manager->register( new \TNO_Widget_About_Team() );
	$widgets_manager->register( new \TNO_Widget_About_Testimonials() );
	$widgets_manager->register( new \TNO_Widget_Contact_Cards() );
	$widgets_manager->register( new \TNO_Widget_Support_Banner() );
	$widgets_manager->register( new \TNO_Widget_Social_Cards() );
	

}

// استفاده از هوک استاندارد المنتور برای ثبت ویجت
add_action( 'elementor/widgets/register', 'tno_register_elementor_widgets' );

// add_action('elementor/editor/after_enqueue_styles', function () {
//     wp_enqueue_style(
//         'theme-style',
//         get_stylesheet_uri()
//     );
// });