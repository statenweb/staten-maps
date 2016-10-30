<?php

namespace Staten_Maps\Ajax;

use Staten_Maps\Utils\Latitude_and_Longitude;

class Lat_Lng {

	const NONCE = 'lat_lng_nonce';

	public function init() {

		$this->attach_hooks();
	}

	public function attach_hooks() {
		add_action( 'wp_ajax_get_lat_lng', array( $this, 'get_lat_lng' ) );
	}


	public function get_lat_lng() {
		self::check_nonce();

		$latitude_and_longitude = new Latitude_and_Longitude( $_POST['address'] );
		$latitude_and_longitude->process();
		$lat = $latitude_and_longitude->get( 'lat' );
		$lng = $latitude_and_longitude->get( 'lng' );
		if ( $lat && $lng ) {
			$response = array(
				'lat' => $lat,
				'lng' => $lng,
			);

			wp_send_json_success( $response );
		}


		wp_send_json_error();
	}

	private static function check_nonce( $name = 'security' ) {
		if ( ! check_ajax_referer( self::NONCE, $name ) ) :
			wp_send_json_error();
		endif;
	}

}