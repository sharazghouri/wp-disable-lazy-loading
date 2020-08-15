<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'Disable_Lazy_Loading_Admin' ) ) :
	/**
	 * Starter_Plugin_Admin Class
	 *
	 * @class Starter_Plugin_Admin
	 * @version 1.0.0
	 * @since 1.0.0
	 * @package Disable_Lazy_Loading
	 * @author sharaz
	 */
	class Disable_Lazy_Loading_Admin {

		private $settings_api;

		/**
		 * construct function .
		 */
		function __construct() {
			require_once 'class-disable-lazy-loading-settings.php';
			$this->settings_api = new Disable_Lazy_Loading_Settings();

				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		/**
		 * Admin init hooks.
		 *
		 * @since 1.0.0
		 */
		function admin_init() {

			// set the settings.
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// initialize settings.
			$this->settings_api->admin_init();
		}

		/**
		 * Register setting page.
		 *
		 * @since 1.0.0
		 */
		function admin_menu() {
				add_options_page( __( 'Lazy Loading', 'disable-core-lazy-loading' ), __( 'Lazy Loading', 'disable-core-lazy-loading' ), 'delete_posts', 'lazy_loading', array( $this, 'plugin_page' ) );
		}

		/**
		 * Setting section.
		 *
		 * @return array $sections setting section.
		 */
		function get_settings_sections() {
				$sections = array(
					array(
						'id'    => 'dll_basics',
						'title' => __( 'Settings', 'disable-core-lazy-loading' ),
					),

				);
				return $sections;
		}

		/**
		 * Returns all the settings fields.
		 *
		 * @since 1.0.0
		 * @return array settings fields.
		 */
		function get_settings_fields() {
			$post_types = get_post_types( [ 'public' => true ], 'names' );
			$post_types = array_merge( [ '' => 'Select' ], $post_types );

				$settings_fields = [
					'dll_basics' => [
						[
							'name'  => 'disable_ll',
							'label' => __( 'Disable Lazy Loading', 'disable-core-lazy-loading' ),
							'desc'  => __( 'Check if you want to disable core lazy loading.', 'disable-core-lazy-loading' ),
							'type'  => 'checkbox',
						],
						[
							'name'              => 'exclude_post_type',
							'type'              => 'text',
							'label'             => __( 'Exclude Post Types', 'disable-core-lazy-loading' ),
							'desc'              => __( 'post,page,etc.', 'disable-core-lazy-loading' ),
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],
						[
							'name'              => 'exclude_ids',
							'type'              => 'text',
							'label'             => __( 'Exclude By ID', 'disable-core-lazy-loading' ),
							'desc'              => __( '(Page/Post/Custom Post Type) ID.', 'disable-core-lazy-loading' ),
							'placeholder'       => '1,3,4',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],
					],
				];

				return $settings_fields;
		}

		/**
		 * Plugin setting page.
		 *
		 * @since 1.0.0
		 */
		function plugin_page() {
				echo '<div class="wrap">';

				$this->settings_api->show_navigation();
				$this->settings_api->show_forms();

				echo '</div>';
		}

		/**
		 * Get all the pages.
		 *
		 * @since 1.0.0
		 * @return array page names with key value pairs
		 */
		function get_pages() {
				$pages         = get_pages();
				$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

				return $pages_options;
		}

	}
	endif;
