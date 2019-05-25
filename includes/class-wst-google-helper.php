<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WST_Google_Helper {

	/**
	 * The Google Maps API key holder
	 * @var string
	 */
	private $mapApiKey;

	/**
	 * @return string
	 */
	public function getMapApiKey() {
		return $this->mapApiKey;
	}

	/**
	 * @param string $mapApiKey
	 */
	public function setMapApiKey( $mapApiKey ) {
		$this->mapApiKey = $mapApiKey;
	}

	/**
	 * The single instance of the class.
	 *
	 * @var WST_Google_Helper
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Instance.
	 * @return WST_Google_Helper
	 * @throws Exception
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WST_Google_Helper ) ) {
			self::$instance = new WST_Google_Helper;
		}
		return self::$instance;
	}

	public function __construct() {
	}

	/**
	 * Get Latitude/Longitude/Altitude based on an address
	 * @param $address
	 *
	 * @return string
	 */
	public function getCoordinates( $address ) {
		$path    = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . $address;
		if ( ! empty( $this->getMapApiKey() ) ) {
			$path = add_query_arg( 'key', $this->getMapApiKey(), $path );
		}
		$json = wp_remote_retrieve_body( wp_remote_get( esc_url( $path, null, null ) ) );
		$json_obj = json_decode( $json );

		$instance = array();
		if ( 'ZERO_RESULTS' == $json_obj->status ) {
			// The address supplied does not have a matching lat / lon.
			// No map is available.
			$instance['lat'] = '0';
			$instance['lon'] = '0';
		} else {

			$loc = $json_obj->results[0]->geometry->location;

			$lat = floatval( $loc->lat );
			$lon = floatval( $loc->lng );

			$instance['lat'] = "$lat";
			$instance['lon'] = "$lon";
		}
		return $instance['lat'] . ',' . $instance['lon'] . ',17z';
	}

	public function urlencode_address( $address ) {
		$address = strtolower( $address );
		$address = preg_replace( '/\s+/', ' ', trim( $address ) ); // Get rid of any unwanted whitespace
		$address = str_ireplace( ' ', '+', $address ); // Use + not %20
		urlencode( $address );
		return $address;
	}


} //end class
