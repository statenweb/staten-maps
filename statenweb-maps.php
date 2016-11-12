<?php
/*
 * Plugin Name: Maps
 * Plugin URI:  https://statenweb.com
 * Description: Easy maps, nuff said!
 * Version:     0.1.1
 */


use Staten_Maps\Ajax\Lat_Lng;
use Staten_Maps\Data_Structures\Post_Types\Staten_Map;
use Staten_Maps\Enqueues;
use Staten_Maps\Settings\Global_Settings;

spl_autoload_register( function ( $class ) {
	$base = explode( '\\', $class );
	if ( strtolower('staten_maps') === strtolower($base[0]) ) {
		$file = __DIR__ . '/' . strtolower( str_replace( [ '\\', '_' ], [
					DIRECTORY_SEPARATOR,
					'-'
				], $class ) . '.php' );
		if ( file_exists( $file ) ) {
			require $file;
		} else {
			die( sprintf( 'File %s not found', $file ) );
		}
	}

} );


add_action( 'admin_init', function () {
	$acf = false;
	if ( class_exists( 'acf' ) ) {

		$acf         = new acf;
		$acf->initialize();

		$version     = $acf->settings['version'];
		$acf = false;
		$first_digit = (int) substr( $version, 0, 1 );

		if ( $first_digit >= 5 ) {
			$acf = true;
		}
	}

	if ( ! $acf ) {
		add_action( 'admin_notices', function () {
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'You need to install and activate the Advanced Custom Fields plugin v5 or greater for this plugin to work properly.', 'statenweb-maps' ); ?></p>
			</div>
			<?php
		} );

		return;
	}
}, PHP_INT_MAX );


$staten_map_post_type = new Staten_Map();
$staten_map_post_type->init();
$ajax_lat_lng = new Lat_Lng();
$ajax_lat_lng->init();
$global_settings = new Global_Settings();
$global_settings->init();
$enqueues = new Enqueues();
$enqueues->init();
$staten_map = new \Staten_Maps\Shortcodes\Staten_Map();
$staten_map->init();