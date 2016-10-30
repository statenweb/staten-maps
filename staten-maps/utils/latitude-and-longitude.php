<?php

namespace Staten_Maps\Utils;

class Latitude_and_Longitude {


	private $address;
	private $lat;
	private $lng;
	private $base_url = 'http://maps.google.com/maps/api/geocode/json';

	/**
	 * If you enter in an address this returns the first result from Google's API for latitude / longitude
	 * or false on no results or error
	 *
	 * @param $address
	 *
	 * @return mixed
	 */
	public function __construct( $address ) {
		$this->address = $address;
		$this->process();
	}

	/**
	 * Get the latitude and longitude
	 *
	 * @return bool|object
	 */
	public function process() {

		$address = urlencode( $this->address );

		$url = add_query_arg( array( 'address' => $address, 'sensor' => 'false' ), $this->base_url );

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) || empty( $request['response']['code'] ) || 200 != $request['response']['code'] ) {
			return false;
		}

		$request_body = wp_remote_retrieve_body( $request );
		$result       = json_decode( $request_body );

		if ( ! $result || ( ! empty( $result->status ) && 'ZERO_RESULTS' === $result->status ) ) {
			return false;
		}


		$this->lat = $result->results[0]->geometry->location->lat;
		$this->lng = $result->results[0]->geometry->location->lng;

	}

	public function get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}
	}


}