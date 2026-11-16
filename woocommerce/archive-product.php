<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 * Path: woocommerce/archive-product.php
 */

defined( 'ABSPATH' ) || exit;

get_header();

$custom_elementor_tpl = absint( get_theme_mod( 'tno_plp_elementor_template_id', 0 ) );
?>

<main class="container tno-plp-page-container">
    <?php
    if ( $custom_elementor_tpl > 0 && class_exists( '\Elementor\Plugin' ) ) {
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $custom_elementor_tpl );
    } else {
        if ( get_theme_mod( 'tno_plp_show_breadcrumb', true ) ) {
            tno_render_plp_breadcrumb();
        }

        if ( get_theme_mod( 'tno_plp_show_top_cats', true ) ) {
            tno_render_plp_top_categories();
        }

        tno_render_complete_plp_section();

        if ( get_theme_mod( 'tno_plp_show_seo_box', true ) ) {
            tno_render_plp_seo_box();
        }
    }
    ?>
</main>

<?php
get_footer();