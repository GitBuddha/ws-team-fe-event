<?php
/*
Plugin Name: WS-Team: FE Event
Plugin URI:
Description: Front-End Event Creator
Author:
Version: 1.0.0
Author URI:
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WS_Team_FE_Event {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Minimum PHP version required
	 *
	 * @var string
	 */
	private $min_php = '5.6.0';

	/**
	 * @var object
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initializes the WS_Team_FE_Event() class
	 *
	 * @since 1.0.0
	 * @since 1.0.0 Rename `__construct` function to `setup` and call it only once
	 *
	 * Checks for an existing WS_Team_FE_Event() instance
	 * and if it doesn't find one, creates it.
	 *
	 * @return object
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WS_Team_FE_Event ) ) {
			self::$instance = new WS_Team_FE_Event;
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup the plugin
	 *
	 * Sets up all the appropriate hooks and actions within our plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	private function setup() {
		// dry check on older PHP versions, if found deactivate itself with an error
		register_activation_hook( __FILE__, array( $this, 'auto_deactivate' ) );

		if ( ! $this->is_supported_php() ) {
			return;
		}

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// instantiate classes
		$this->instantiate();

		// Loaded action
		do_action( 'wst_loaded' );
	}

	/**
	 * Check if the PHP version is supported
	 *
	 * @return bool
	 */
	public function is_supported_php() {
		if ( version_compare( PHP_VERSION, $this->min_php, '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Bail out if the php version is lower than
	 *
	 * @return void
	 */
	public function auto_deactivate() {
		if ( $this->is_supported_php() ) {
			return;
		}

		deactivate_plugins( basename( __FILE__ ) );

		$error = __( '<h1>An Error Occured</h1>', 'wst' );
		$error .= __( '<h2>Your installed PHP Version is: ', 'wst' ) . PHP_VERSION . '</h2>';
		$error .= __( '<p>The <strong>WS-Team FE Event</strong> plugin requires PHP version <strong>', 'wst' ) . $this->min_php . __( '</strong> or greater', 'wst' );
		$error .= __( '<p>The version of your PHP is ', 'wst' ) . '<a href="http://php.net/supported-versions.php" target="_blank"><strong>' . __( 'unsupported and old', 'wst' ) . '</strong></a>.';
		$error .= __( 'You should update your PHP software or contact your host regarding this matter.</p>', 'wst' );
		wp_die( $error, __( 'Plugin Activation Error', 'wst' ), array( 'response' => 200, 'back_link' => true ) );
	}

	/**
	 * Define the plugin constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'WST_VERSION', $this->version );
		define( 'WST_FILE', __FILE__ );
		define( 'WST_PATH', dirname( WST_FILE ) );
		define( 'WST_INCLUDES', WST_PATH . '/includes' );
		define( 'WST_TEMPLATES', WST_PATH . '/templates' );
		define( 'WST_URL', plugins_url( '', WST_FILE ) );
		define( 'WST_ASSETS', WST_URL . '/assets' );
	}

	/**
	 * Include the required files
	 *
	 * @return void
	 */
	private function includes() {
		require_once WST_INCLUDES . '/class-autoloader.php';
	}

	/**
	 * Instantiate classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function instantiate() {
		new WST_Admin();
	}

	/**
	 * @return WST_Templates
	 */
	public function templates() {
		return WST_Templates::init();
	}


	/**
	 * @return WST_Google_Helper
	 * @throws Exception
	 */
	public function google() {
		return WST_Google_Helper::init();
	}

}

/**
 * Init the WS_Team_FE_Event plugin
 *
 * @return WS_Team_FE_Event the plugin object
 */
function WST() {
	return WS_Team_FE_Event::init();
}

// kick it off
WST();