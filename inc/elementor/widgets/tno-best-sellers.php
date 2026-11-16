<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Trendino_Products_Widget extends \Elementor\Widget_Base {

	public function get_name() { return 'trendino_products'; }
	public function get_title() { return 'محصولات پرفروش ترندینو'; }
	public function get_icon() { return 'eicon-products'; }
	public function get_categories() { return [ 'trendino' ]; }

	protected function register_controls() {
		$this->start_controls_section('content_section', [
			'label' => 'محتوا',
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('widget_title', [
			'label' => 'عنوان اصلی',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'پرفروش ترین ها',
		]);

		$this->add_control('view_all_text', [
			'label' => 'متن دکمه مشاهده همه',
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => 'مشاهده همه',
		]);

		$this->add_control('view_all_link', [
			'label' => 'لینک دکمه',
			'type' => \Elementor\Controls_Manager::URL,
		]);

		$this->add_control('posts_per_page', [
			'label' => 'تعداد محصول در هر تب',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => 6,
		]);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control('tab_title', [
			'label' => 'عنوان تب',
			'type' => \Elementor\Controls_Manager::TEXT,
			'required' => true,
		]);
		$repeater->add_control('category_slug', [
			'label' => 'نامک دسته‌بندی ووکامرس (Slug)',
			'type' => \Elementor\Controls_Manager::TEXT,
			'required' => true,
		]);

		$this->add_control('tabs', [
			'label' => 'تب‌های دسته‌بندی',
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => [
				[ 'tab_title' => 'لپتاپ', 'category_slug' => 'laptop' ],
				[ 'tab_title' => 'گوشی', 'category_slug' => 'mobile' ],
				[ 'tab_title' => 'کنسول', 'category_slug' => 'console' ],
				[ 'tab_title' => 'ساعت', 'category_slug' => 'watch' ],
			],
			'title_field' => '{{{ tab_title }}}',
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( ! class_exists( 'WooCommerce' ) ) return;
		?>
		<div class="tno-bestsellers-wrapper">
			<!-- Header -->
			<div class="tno-bs-header">
                <div class="tno-bs-right">
                    <span class="tno-bs-dot"></span>
                    <h2 class="tno-bs-title"><?php echo esc_html( $settings['widget_title'] ); ?></h2>
                </div>

				<div class="tno-bs-divider"></div>
                
                <div class="tno-bs-left">
                    <div class="tno-bs-tabs">
                        <?php foreach ( $settings['tabs'] as $index => $tab ) : 
                            $active = $index === 0 ? 'active' : '';
                        ?>
                            <button class="tno-tab-btn <?php echo esc_attr($active); ?>" data-target="tno-tab-<?php echo esc_attr($tab['_id']); ?>">
                                <?php echo esc_html( $tab['tab_title'] ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php if ( ! empty( $settings['view_all_link']['url'] ) ) : ?>
                        <a href="<?php echo esc_url( $settings['view_all_link']['url'] ); ?>" class="tno-bs-viewall">
                            <?php echo esc_html( $settings['view_all_text'] ); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
			</div>

			<!-- Content Panes -->
			<div class="tno-bs-content">
				<?php foreach ( $settings['tabs'] as $index => $tab ) : 
					$active = $index === 0 ? 'active' : '';
				?>
					<div class="tno-tab-pane <?php echo esc_attr($active); ?>" id="tno-tab-<?php echo esc_attr($tab['_id']); ?>">
						<div class="tno-bs-grid">
							<?php
							$args = [
								'post_type'      => 'product',
								'posts_per_page' => $settings['posts_per_page'],
								'tax_query'      => [
									[
										'taxonomy' => 'product_cat',
										'field'    => 'slug',
										'terms'    => $tab['category_slug'],
									]
								]
							];
							$query = new \WP_Query( $args );

							if ( $query->have_posts() ) :
								while ( $query->have_posts() ) : $query->the_post();
									global $product;
									$regular_price = $product->get_regular_price();
									$sale_price    = $product->get_sale_price();
									$percentage    = 0;
									if ( $regular_price && $sale_price && $regular_price > $sale_price ) {
										$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
									}
									?>
									<div class="tno-hz-card">
										<div class="tno-hz-image">
											<a href="<?php the_permalink(); ?>">
												<?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?>
											</a>
										</div>
										<div class="tno-hz-info">
											<h3 class="tno-hz-title">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h3>
											<div class="tno-hz-meta">
                                                <div class="tno-hz-discount-wrap">
                                                    <?php if ( $percentage > 0 ) : ?>
                                                        <span class="tno-hz-badge"><?php echo esc_html($percentage); ?>%</span>
                                                    <?php endif; ?>
                                                </div>
												<div class="tno-hz-price">
													<?php if ( $product->is_on_sale() ) : ?>
														<del><?php echo wc_price( $regular_price ); ?></del>
													<?php endif; ?>
													<ins><?php echo number_format($product->get_price()); ?> <span>تومان</span></ins>
												</div>
											</div>
										</div>
									</div>
									<?php
								endwhile;
								wp_reset_postdata();
							else:
								echo '<p class="tno-no-products">محصولی در این دسته یافت نشد.</p>';
							endif;
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}