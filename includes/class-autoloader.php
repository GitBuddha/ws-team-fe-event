<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WST Autoloader.
 *
 * @class       WST_Autoloader
 * @version     1.0.0
 * @package     WST/Classes
 * @category    Class
 */
class WST_Autoloader {

    /**
     * The Constructor.
     */
    public function __construct() {
        if ( function_exists( "__autoload" ) ) {
            spl_autoload_register( "__autoload" );
        }
        spl_autoload_register( array( $this, 'autoload' ) );
    }

    /**
     * Take a class name and turn it into a file name.
     *
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class( $class ) {
        return 'class-' . str_replace( '_', '-', $class ) . '.php';
    }

    /**
     * Include a class file.
     *
     * @param  string $path
     * @return bool successful or not
     */
    private function load_file( $path ) {
        if ( $path && is_readable( $path ) ) {
            require_once( $path );
            return true;
        }
        return false;
    }

    /**
     * Auto-load WST classes on demand to reduce memory consumption.
     *
     * @param string $class
     */
    public function autoload( $class ) {
        $class = strtolower( $class );

        if ( 0 !== strpos( $class, 'wst_' ) ) {
            return;
        }

        $file  = $this->get_file_name_from_class( $class );
        $path  = '';

        if ( empty( $path ) || ! $this->load_file( $path . $file ) ) {
            $this->load_file( WST_INCLUDES. '/' . $file );
        }
    }
} //end class

new WST_Autoloader();
