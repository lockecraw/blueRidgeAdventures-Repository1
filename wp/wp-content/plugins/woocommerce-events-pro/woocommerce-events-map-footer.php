<?php 
	function gmap_footer() { 
		global $post, $product, $ignitewoo_events;

		//if ( !class_exists( 'Woocommerce' ) ) 
		//	return;

		if ( empty( $product ) || empty( $product->product_type ) || !is_product() || 'yes' != get_post_meta( $product->id, '_ignitewoo_event', true ) ) 
			return;

		$event_settings = $ignitewoo_events->get_event_settings();

		$data = $ignitewoo_events->get_post_data();

		$addresses = array(); 

		if ( !empty( $data['venue_map'] ) && 'yes' == $data['venue_map'] ) { 

			if ( isset( $data['event_venue'] ) )
				$venues = $data['event_venue'][0]; 
			else
				$venues = '';

			$venues = new WP_Query( array( 'post_type' => 'event_venue', 'post_status' => 'publish', 'post__in' => array( $venues ), 'posts_per_page' => 999999 ) );

			if ( !$venues || !$venues->have_posts() ) 
				return;


			if ( $venues->have_posts() ) while( $venues->have_posts() ) { 

				$venues->the_post();

				$venue_meta = get_post_custom( $post->ID , true );

				if ( !isset( $venue_meta ) || '' == $venue_meta ) 
					continue; 

				$c_data = explode( ':', $venue_meta['_generic_country_state'][0] );

				$country = $c_data[0];

				if ( count( $c_data ) > 1 ) 
					$state = $c_data[1];
				else 
					$state = '';

				$address = $venue_meta['_generic_address'][0] . ', ' . $venue_meta['_generic_city'][0] . ' ';

				if ( '' != $state ) 
					$address .= $state . ' ';

				$address .= $country;

				$addresses[] = $address; 

			}

			wp_reset_postdata();

		}

		if ( empty( $addresses ) ) 
			return;

		?>

		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

		<script type="text/javascript">

			jQuery( document ).ready( function() { 

				var event_address;

				//geocode_addresses();

				initialize();

			});

			first_address = "";

			function initialize() { 

				var geocoder = new google.maps.Geocoder();

				first_address = geocode_addresses( "<?php echo $addresses[0] ?>" )

			}


			function draw_map() {

				var geocoder = new google.maps.Geocoder();

				var ignitewoo_event_options = {
					zoom: <?php echo $event_settings['maps_zoom']; ?>,
					center: first_address,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};

				var map = new google.maps.Map( document.getElementById( "ignitewoo_googlemaps" ), ignitewoo_event_options );

				<?php $i = 0; ?>

				<?php foreach( $addresses as $addr ) { ?>

					geocoder.geocode( 
						{ "address" : "[{ <?php echo $addr ?> }]" },
						function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {

								event_address = results[0].geometry.location

								var marker = new google.maps.Marker(
									{
										map: map,
										title: "<?php echo get_the_title( $post->ID ) ?>",
										position: event_address
									}
								);

							}
						}
					);

				<?php } ?>

			}

			function geocode_addresses( addr ) {

				var geocoder = new google.maps.Geocoder();

				var address = "[{" + addr +  "}]";

				geocoder.geocode( 
					{ 'address': address }, 
					function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							first_address = results[0].geometry.location
							draw_map();
						}
					}
				);

			}

		</script>

		<?php

	}