<?php
/**
 * Plugin Name: Disable Lazy Loading
 * Plugin URI: http://domain.com/starter-plugin/
 * Description: Disbale core lazy loading feature. you can disable by post type or totally.
 * Version: 1.0.0
 * Author: Sharaz Shahid
 * Author URI: https://twitter.com/sharazghouri1
 * Requires at least: 5.5
 * Tested up to: 5.5
 *
 * Text Domain: disable-ll
 * Domain Path: /languages/
 *
 * @package disable_lazy_loading
 * @category Core
 * @author sharaz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Returns the main instance of disable_lazy_loading to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Disable_Lazy_Loading
 */
function disable_lazy_loading() {
	return Disable_Lazy_Loading::instance();
} // End disable_lazy_loading()

add_action( 'plugins_loaded', 'disable_lazy_loading' );

/**
 * Main Disable_Lazy_Loading Class
 *
 * @since 1.0.0
 * @package Disable_Lazy_Loading
 */
final class Disable_Lazy_Loading {
	/**
	 * Disable_lazy_Loading The single instance of Disable_lazy_Loading.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	/**
	 * The admin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->token       = 'disable-ll';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = '1.0.0';
		$this->default_setting();
		$this->settings = get_option( 'dll_basics' );
		if ( is_admin() ) {
			require_once 'classes/class-disable-lazy-loading-admin.php';
			$this->admin = new Disable_Lazy_Loading_Admin();
		}

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'wp', array( $this, 'disable_lazy_loading' ) );
		//add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	} // End __construct()

	/**
	 * Main Disable_lazy_Loading Instance
	 *
	 * Ensures only one instance of Disable_lazy_Loading is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Disable_lazy_Loading()
	 * @return Main Disable_lazy_Loading instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Default settings.
	 *
	 * @since 1.0.0
	 */
	public function default_setting() {

		if ( ! get_option( 'dll_basics' ) ) {
			$default = [
				'disable_ll'        => 'off',
				'exclude_post_type' => '',
				'exclude_ids'       => '',
			];
			update_option( 'dll_basics', $default );
		}
	}

	public function disable_lazy_loading() {

		if ( 'on' == $this->settings['disable_ll'] ) {
			$exc_post_types = explode( ',', $this->settings['exclude_post_type'] );
			$exc_ids        = explode( ',', $this->settings['exclude_ids'] );
			global $post;
			var_dump( !in_array( $post->post_type, $exc_post_types ) , !in_array( $post->ID, $exc_ids ));
			if ( ! in_array( $post->post_type, $exc_post_types ) && ! in_array( $post->ID, $exc_ids ) ) {
				add_filter( 'wp_lazy_loading_enabled', '__return_false' );
			}
		}
	}
	/**
	 * Load the localisation file.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'disable-ll', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function install() {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 *
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	/**
	 * Get option .
	 *
	 * @since 1.0.0
	 *
	 * @param string $option option name.
	 * @param string $section section name.
	 * @param string $default default value.
	 * @return mixed
	 */
	function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

} // End Class
