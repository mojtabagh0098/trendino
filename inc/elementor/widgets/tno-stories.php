<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم به فایل
}

class Elementor_TNO_Stories_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'tno_stories';
	}

	public function get_title() {
		return 'استوریهای ترندینو';
	}

	public function get_icon() {
		return 'eicon-inner-section';
	}

	public function get_categories() {
		return [ 'trendino' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => 'محتوا',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'story_title',
			[
				'label' => 'عنوان',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'عنوان استوری',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'story_image',
			[
				'label' => 'تصویر',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'story_link',
			[
				'label' => 'لینک',
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'stories_list',
			[
				'label' => 'لیست استوریها',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'story_title' => 'لپتاپ',
					],
					[
						'story_title' => 'اسپیکر',
					],
				],
				'title_field' => '{{{ story_title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="container c-stories">
			<?php foreach ( $settings['stories_list'] as $item ) : 
				$image_url = !empty($item['story_image']['url']) ? $item['story_image']['url'] : \Elementor\Utils::get_placeholder_image_src();
				$link_url = $item['story_link']['url'];
				
				if ( ! empty( $link_url ) ) {
					$this->add_link_attributes( 'story_link_' . $item['_id'], $item['story_link'] );
				}
				?>
				<div class="c-stories__item elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
					<?php if ( ! empty( $link_url ) ) : ?><a <?php echo $this->get_render_attribute_string( 'story_link_' . $item['_id'] ); ?>><?php endif; ?>
					<div class="c-stories__circle">
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $item['story_title'] ); ?>" />
					</div>
					<span><?php echo esc_html( $item['story_title'] ); ?></span>
					<?php if ( ! empty( $link_url ) ) : ?></a><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}