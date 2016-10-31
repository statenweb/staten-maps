<?php

namespace Staten_Maps\Shortcodes;

class Staten_Map {

	const SHORTCODE = 'staten-map';

	public function init() {
		$this->attach_hooks();
	}

	public function attach_hooks() {
		add_shortcode( self::SHORTCODE, array( $this, 'shortcode' ) );
	}

	public function shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'scrollwheel' => 'false',
			'id'          => null,
			'min-height'  => '500px',
			'zoom'        => '13',
		), $atts );

		if ( ! $atts['id'] ) {
			return null;
		}


		ob_start();

		$first_lat     = 0;
		$first_lng     = 0;
		$counter       = 0;
		$marker_output = [];
		$style         = get_field( 'style_key', $atts['id'] );
		$style_json    = null;
		if ( $style ) :

			if ( have_rows( 'staten_maps_styles', 'options' ) ) :
				while ( have_rows( 'staten_maps_styles', 'options' ) ) : the_row();
					if ( $style === get_sub_field( 'handle' ) ) :
						$style_json = get_sub_field( 'style' );
					endif;
				endwhile;
			endif;

		endif;

		if ( ! self::is_json( $style_json ) ) {
			$style_json = null;
		}

		if ( have_rows( 'map_points', $atts['id'] ) ):

			// loop through the rows of data
			while ( have_rows( 'map_points', $atts['id'] ) ) : the_row();

				if ( 0 === $counter ) :
					$first_lat = get_sub_field( 'latitude' );
					$first_lng = get_sub_field( 'longitude' );
				endif;

				$string = "var marker" . $atts['id'] . " = new google.maps.Marker({
		                animation: google.maps.Animation.DROP,
		                position: new google.maps.LatLng(" . get_sub_field( 'latitude' ) . ", " . get_sub_field( 'longitude' ) . "),
		                map: map" . $atts['id'] . ",
		                
		
		            });";

				if ( get_sub_field( 'tooltip' ) ):
					$string .= " 	
						
		
		
		            var infoWindow" . $atts['id'] . "_" . $counter . " = new google.maps.InfoWindow({
		                content: '" . esc_js( get_sub_field( 'tooltip' ) ) . "'
		            });
		
		            google.maps.event.addListener(marker" . $atts['id'] . ", 'click', function () {
		                infoWindow" . $atts['id'] . "_" . $counter . ".open(map" . $atts['id'] . ", marker" . $atts['id'] . ");
		            });
		
		            markers" . $atts['id'] . ".push(marker" . $atts['id'] . ");";
				endif;

				$marker_output[] = $string;

				$counter ++;

			endwhile;
		endif;


		?>
		<div style="height:100%; width:100%;">
			<div id="map-container-<?php esc_attr_e( $atts['id'] ) ?>"
			     style="min-height:<?php esc_attr_e( $atts['min-height'] ); ?>;"></div>
		</div>
		<script>
			var markers<?php esc_attr_e( $atts['id'] ) ?> = [];
			var map<?php esc_attr_e( $atts['id'] ) ?> = new google.maps.Map(document.querySelector('#map-container-<?php esc_attr_e( $atts['id'] ) ?>'), {
					center: new google.maps.LatLng(<?php echo esc_js( $first_lat )?>, <?php echo esc_js( $first_lng ); ?>),
					zoom: <?php echo esc_js( $atts['zoom'] ); ?>,
					scrollwheel: <?php echo esc_js( $atts['scrollwheel'] ); ?>,
					<?php if ( $style_json ) :?>
					styles: <?php echo $style_json; //escaped above ?>
					<?php endif; ?>
				})
				;

			google.maps.event.addListenerOnce(map<?php esc_attr_e( $atts['id'] ) ?>, 'idle', function () {
				setTimeout(function () {
					google.maps.event.trigger(map<?php esc_attr_e( $atts['id'] ) ?>, 'resize');
				}, 100);
			});
			<?php foreach ( $marker_output as $marker_single ):
				echo $marker_single;
			endforeach;?>
		</script><?php
		return ob_get_clean();
	}

	public static function is_json( $string ) {
		json_decode( $string );

		return ( json_last_error() == JSON_ERROR_NONE );
	}

}