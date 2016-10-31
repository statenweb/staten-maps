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
		if ( have_rows( 'map_points', $atts['id'] ) ):

			// loop through the rows of data
			while ( have_rows( 'map_points', $atts['id'] ) ) : the_row();

				if ( 0 === $counter ) :
					$first_lat = get_sub_field( 'latitude' );
					$first_lng = get_sub_field( 'longitude' );
				endif;

				$marker_output[] = "var marker" . $atts['id'] . " = new google.maps.Marker({
                position: new google.maps.LatLng(" . get_sub_field( 'latitude' ) . ", " . get_sub_field( 'longitude' ) . "),
                map: map" . $atts['id'] . "

            });


            var infoWindow" . $atts['id'] . "_" . $counter . " = new google.maps.InfoWindow({
                content: '" . get_sub_field( 'address' ) . "'
            });

            google.maps.event.addListener(marker" . $atts['id'] . ", 'click', function () {
                infoWindow" . $atts['id'] . "_" . $counter . ".open(map" . $atts['id'] . ", marker" . $atts['id'] . ");
            });

            markers" . $atts['id'] . ".push(marker" . $atts['id'] . ");";


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
				scrollwheel: <?php echo esc_js( $atts['scrollwheel'] ); ?>
			});

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

}