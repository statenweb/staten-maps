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

		$first_lat                      = 0;
		$first_lng                      = 0;
		$counter                        = 0;
		$marker_output                  = [];
		$style                          = get_field( 'style_key', $atts['id'] );
		$default_tooltip_display_method = strtolower( get_field( 'default_tooltip_display_method', $atts['id'] ) );
		$style_json                     = null;
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

		$markers_js_output_array   = [];
		$markers_js_output_array[] = 'var icons = {';
		if ( have_rows( 'staten_maps_markers', 'options' ) ):

			while ( have_rows( 'staten_maps_markers', 'options' ) ): the_row();

				$markers_js_output_array[] = esc_js( get_sub_field( 'key' ) ) . ": {icon: '" . esc_url( get_sub_field( 'image' ) ) . "'},";

			endwhile;

		endif;
		$markers_js_output_array[] = '};';

		$markers_js_output = implode( ' ', $markers_js_output_array );

		if ( have_rows( 'map_points', $atts['id'] ) ):

			// loop through the rows of data
			while ( have_rows( 'map_points', $atts['id'] ) ) : the_row();

				if ( 0 === $counter ) :
					$first_lat = get_sub_field( 'latitude' );
					$first_lng = get_sub_field( 'longitude' );
				endif;

				$icon = get_sub_field( 'type' );

				$string = "marker" . $atts['id'] . " = new google.maps.Marker({";

				if ( $icon ):
					$string .= "icon: icons['" . esc_js( $icon ) . "'].icon,";
				endif;


				$string .= "        position: new google.maps.LatLng(" . get_sub_field( 'latitude' ) . ", " . get_sub_field( 'longitude' ) . "),
		                map: map" . $atts['id'] . ",
		                
		
		            });";

				if ( get_sub_field( 'tooltip' ) ):

					// lets clean up the tooltip

					$tooltip = get_sub_field( 'tooltip' );
					$tooltip = str_replace( '\'', '&#39;', $tooltip );


					$tooltip = str_replace( "\n", '', $tooltip );
					$string .= " 	
						
		
		
		            var infoWindow" . $atts['id'] . "_" . $counter . " = new google.maps.InfoWindow({
		                content: '" . rtrim( $tooltip ) . "'
		            });";

					$tooltip_method = strtolower( get_sub_field( 'tooltip_display_method' ) );
					if ( 'default' === $tooltip_method || ! get_sub_field( 'tooltip_display_method' ) ) :
						$tooltip_method = $default_tooltip_display_method;
					endif;

					if ( 'click' === $tooltip_method ) :

						$string .= "
						google.maps.event.addListener(marker" . $atts['id'] . ", 'click', function () {
						closeOtherMarkers();
		                infoWindow" . $atts['id'] . "_" . $counter . ".open(map" . $atts['id'] . ", marker" . $atts['id'] . ");
		                infoWindow" . $atts['id'] . "_" . $counter . ".setPosition(new google.maps.LatLng(" . get_sub_field( 'latitude' ) . ", " . get_sub_field( 'longitude' ) . "));
		            });";

					elseif ( 'hover' === $tooltip_method ):

						$string .= "google.maps.event.addListener(marker" . $atts['id'] . ", 'mouseover', function () {
		                infoWindow" . $atts['id'] . "_" . $counter . ".open(map" . $atts['id'] . ", marker" . $atts['id'] . ");
		                infoWindow" . $atts['id'] . "_" . $counter . ".setPosition(new google.maps.LatLng(" . get_sub_field( 'latitude' ) . ", " . get_sub_field( 'longitude' ) . "));
		            });
		            
		            google.maps.event.addListener(marker" . $atts['id'] . ", 'mouseout', function () {
		                infoWindow" . $atts['id'] . "_" . $counter . ".close(map" . $atts['id'] . ", marker" . $atts['id'] . ");
		            });";
					endif;


					$string .= "markers" . $atts['id'] . ".push(marker" . $atts['id'] . ");";
					$string .= "markersContainer.push(infoWindow" . $atts['id'] . "_" . $counter . ");";

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
			var markersContainer = [],
				closeOtherMarkers = function () {
					markersContainer.forEach(function (marker) {
						marker.close();
					});
				};

			<?php echo $markers_js_output;?>
			var markers<?php esc_attr_e( $atts['id'] ) ?> = [];
			var marker<?php esc_attr_e( $atts['id'] ) ?>;
			var map<?php esc_attr_e( $atts['id'] ) ?> = new google.maps.Map(document.querySelector('#map-container-<?php esc_attr_e( $atts['id'] ) ?>'), {
				center: new google.maps.LatLng(<?php echo esc_js( $first_lat )?>, <?php echo esc_js( $first_lng ); ?>),
				zoom: <?php echo esc_js( $atts['zoom'] ); ?>,
				scrollwheel: <?php echo esc_js( $atts['scrollwheel'] ); ?>,
				<?php if ( $style_json ) :?>
				styles: <?php echo $style_json; //escaped above ?>
				<?php endif; ?>
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

	public static function is_json( $string ) {
		json_decode( $string );

		return ( json_last_error() == JSON_ERROR_NONE );
	}

}