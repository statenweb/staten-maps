<?php

namespace Staten_Maps;


class Enqueues {

	const HANDLE_SCRIPT = 'staten-maps';
	const HANDLE_GOOGLE_MAPS = 'staten-maps-google-maps-api';


	public function init() {

		$this->attach_hooks();

	}

	public function attach_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'script_loader_tag', array( $this, 'async_enqueues' ), 10, 3 );
	}

	public function enqueue() {

		$api_key = get_field( 'staten_maps_google_maps_api_key', 'options' );
		$api_url = add_query_arg( array( 'key' => $api_key ), 'https://maps.googleapis.com/maps/api/js' );

		wp_enqueue_script( self::HANDLE_GOOGLE_MAPS, $api_url );

	}

	public function async_enqueues( $tag, $handle, $src ) {

		$async_scripts = array( self::HANDLE_GOOGLE_MAPS );

		if ( in_array( $handle, $async_scripts ) ) {
			$tag = str_replace('src=', 'async="async" defer="defer" src=', $tag);
		}

		return $tag;
	}


}