<?php
/**
 * Base class every Trendino Elementor widget extends.
 *
 * @package Trendino
 */

namespace Trendino\Elementor\Widgets;

use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Class Base_Widget
 */
abstract class Base_Widget extends Widget_Base {

	/**
	 * Component asset handles this widget needs (see trendino_component_assets()).
	 *
	 * @var string[]
	 */
	protected $components = array();

	/**
	 * Widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'trendino-content' );
	}

	/**
	 * Keywords shown in the Elementor search panel.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'trendino', 'shop', 'woocommerce' );
	}

	/**
	 * Style dependencies resolved from the component map.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		$handles = array( 'trendino-base' );

		foreach ( $this->components as $component ) {
			$handles[] = 'trendino-' . $component;
		}

		$this->register_component_assets();

		return $handles;
	}

	/**
	 * Script dependencies resolved from the component map.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		$handles = array();
		$map     = trendino_component_assets();

		foreach ( $this->components as $component ) {
			if ( ! empty( $map[ $component ]['js'] ) ) {
				$handles[] = 'trendino-' . $component;
			}
		}

		$this->register_component_assets();

		return $handles;
	}

	/**
	 * Make sure the handles used by get_*_depends() exist in the WP registry.
	 *
	 * @return void
	 */
	protected function register_component_assets() {
		$map = trendino_component_assets();

		foreach ( $this->components as $component ) {
			if ( ! isset( $map[ $component ] ) ) {
				continue;
			}

			$handle = 'trendino-' . $component;

			if ( ! empty( $map[ $component ]['css'] ) && ! wp_style_is( $handle, 'registered' ) ) {
				wp_register_style( $handle, TRENDINO_ASSETS . 'css/' . $map[ $component ]['css'], array( 'trendino-base' ), TRENDINO_VERSION );
				wp_style_add_data( $handle, 'rtl', 'replace' );
			}

			if ( ! empty( $map[ $component ]['js'] ) && ! wp_script_is( $handle, 'registered' ) ) {
				wp_register_script( $handle, TRENDINO_ASSETS . 'js/' . $map[ $component ]['js'], array( 'trendino-app' ), TRENDINO_VERSION, true );
			}
		}
	}

	/**
	 * Whether WooCommerce is required for this widget to render.
	 *
	 * @return bool
	 */
	protected function requires_woocommerce() {
		return false;
	}

	/**
	 * Print an editor-only notice.
	 *
	 * @param string $message Message.
	 * @return void
	 */
	protected function editor_notice( $message ) {
		if ( ! trendino_is_elementor_preview() ) {
			return;
		}

		printf(
			'<div class="trendino-editor-notice">%s</div>',
			esc_html( $message )
		);
	}

	/**
	 * Guard used at the top of render(): bails (with a notice in the editor) when
	 * WooCommerce is required but missing.
	 *
	 * @return bool True when the widget may render.
	 */
	protected function can_render() {
		if ( $this->requires_woocommerce() && ! trendino_is_woocommerce_active() ) {
			$this->editor_notice( esc_html__( 'This widget requires WooCommerce to be installed and active.', 'trendino' ) );

			return false;
		}

		return true;
	}
}
