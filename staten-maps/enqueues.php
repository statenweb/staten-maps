<?php

namespace Staten_Maps;


class Enqueues {

	private $handle = 'staten-maps';

	public function init() {

		$this->attach_hooks();

	}

	public function attach_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {

	}


}