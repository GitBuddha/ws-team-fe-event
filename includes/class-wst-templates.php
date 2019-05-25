<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


final class WST_Templates {

	/**
	 * All data of templates
	 *
	 * @var array
	 */
	public $php_templates = array();


	/**
	 * The single instance of the class.
	 *
	 * @var WST_Templates
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of WST_Templates is loaded or can be loaded.
	 *
	 * @return WST_Templates - Main instance.
	 * @since 1.0
	 * @static
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WST_Templates ) ) {
			self::$instance = new WST_Templates;
		}
		return self::$instance;
	}

	public function __construct() {
	}


	/**
	 * Get other templates (e.g. files table) passing attributes and including the file.
	 *
	 * @access public
	 *
	 * @param string $template_name
	 * @param string $path (default: '')
	 * @param array $t_args (default: array())
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	public function get_template( $template_name, $path = '', $t_args = array(), $echo = false ) {
		if ( ! empty( $t_args ) && is_array( $t_args ) ) {
			extract( $t_args );
		}

		$located = $path  . $template_name;

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
			return '';
		}

		ob_start();
		include( $located );
		$html = ob_get_clean();

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

		return '';
	}
}