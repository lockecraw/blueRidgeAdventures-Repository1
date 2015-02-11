<?php 

/** 

Ticket orders list for user's My Account page 

*/ 


global $woocommerce, $wp_taxonomies, $ignitewoo_events_pro;

?>

<h2><?php echo apply_filters( 'myaccount_events_list_label' , __( 'My Events', 'ignitewoo_events' ) ); ?></h2>

<table>
	<thead>
	<tr>
		<th><?php _e( 'Name', 'ignitewoo_events' ) ?></th>

		<th><?php _e( 'Dates', 'ignitewoo_events' ) ?></th>

	</tr>
	</thead>

<?php 

$my_account_page_url = woocommerce_get_page_id( 'myaccount' );

$my_account_page_url = get_page_uri( $my_account_page_url );
	
// No orders that contain tickets? 
if ( sizeof( $customer_orders ) < 0 ) { 

	if ( 'yes' == $ignitewoo_events->settings['qr_code'] ) 
		$span = 3; 
	else 
		$span = 2;

	?>

	<tr>
		<td colspan="<?php echo $span ?>">
			<?php _e( "You haven't purchased attendance for any events.", 'ignitewoo_events' ) ?>
		</td>
	</tr>

	</table>

	<?php

	return;
}


// List the orders and associated ticket info
foreach ( $customer_orders as $customer_order ) {

	$order = new WC_Order( $customer_order->ID );

	if ( !$ignitewoo_events_pro->order_contains_tickets( $order ) )
		continue;
		
	// If the order is not complete or not in processing status then don't display any ticket info
	if ( 'completed' != $order->status && 'processing' != $order->status ) 
		continue;

	if ( sizeof( $order->get_items() ) > 0 ) { 

		foreach( $order->get_items() as $item ) {

			$_product = $order->get_product_from_item( $item );

			if ( empty( $_product ) ) 
				continue;
				
			// You can alter the attributes display however you see fit. We don't provide support for template mods

			if ( 'variation' == $_product->product_type ) 
				$variation_attributes = $_product->get_variation_attributes();

			if ( 'yes' != get_post_meta( $_product->id, '_ignitewoo_event', true ) )
				continue;

			if ( absint( $item['qty'] < 1 ) )
				continue;

			$event_meta = get_post_meta( $_product->id, '_ignitewoo_event_info', true );

			$venue_list = new WP_Query( array( 'post_type' => 'event_venue', 'post_status' => 'publish', 'post__in' => array( $event_meta['event_venue'][0] ), 'posts_per_page' => 999999 ) );

			$venues = array();

			$addresses = array(); 

			if ( $venue_list->have_posts() ) while( $venue_list->have_posts() ) { 

				$venue_list->the_post();

				$venue_meta = get_post_custom( $post->ID , true );

				if ( !isset( $venue_meta ) || '' == $venue_meta ) 
					continue; 

				$address = $venue_meta['_generic_address'][0];

				$city = $venue_meta['_generic_city'][0];

				$c_data = explode( ':', $venue_meta['_generic_country_state'][0] );

				$country = $c_data[0];

				if ( count( $c_data ) > 1 ) 
					$state = $c_data[1];
				else 
					$state = '';

				$venues[] = array( 
						'name' => get_the_title( $post->ID ),
						'address' => $address,
						'city' => $city,
						'country' => $country,
						'state' => $state
						);

			}

			wp_reset_postdata();

			?>
			
			<tr>
				<td>
					<?php _e( 'Order #', 'ignitewoo_events' )?><a title="<?php _e( 'View order details', 'ignitewoo_events' ) ?>" href="<?php echo get_bloginfo( 'url' ) . '/' . $my_account_page_url ?>/view-order/?order=<?php echo $customer_order->ID ?>"><?php echo $customer_order->ID ?></a><br/>
					
					<a title="<?php _e( 'View order details', 'ignitewoo_events' ) ?>" href="<?php echo get_bloginfo( 'url' ) . '/' . $my_account_page_url ?>/view-order/?order=<?php echo $customer_order->ID ?>"><?php echo get_the_title( $_product->id ) ?></a>
				</td>  


				<td>
					<?php if ( !empty( $venues ) ) foreach( $venues as $v ) { ?>

						<p>
							<span class="myaccount_venue_name"><?php echo $v['name'] ?></span><br/>
							<a title=" <?php _e( 'View map ', 'ignitewoo_events' ) ?> " href="http://maps.google.com/?q=<?php echo $v['address'] . ' ' . $v['city'] . ' ' . $v['state'] . ' ' . $v['country'] ?>" target="_blank">
								<span class="myaccount_venue_info"><?php echo $v['address'] ?></span><br/>
								<span class="myaccount_venue_info">
									<?php echo $v['city'] ?>, 
									<?php if ( isset( $v['state'] ) && '' != $v['state'] ) echo $v['state'] . ', '; ?>
									<?php echo $v['country'] ?>
								</span>
							</a>
						</p>

					<?php } ?>

					<p class="myaccount_event_dates">
					
						<?php // Simple event, non-variable, non-recurring ?>
						<?php if ( 'None' == $event_meta['recurrence'] ) { ?>
							
							<?php echo $event_meta['start_date'] ?> 
							<?php if ( '' != $event_meta['end_date'] ) { ?>
								&mdash; <br/><?php echo $event_meta['end_date'] ?>
							<?php } ?>
							
						<?php // recurring event as a product variation ?>
						<?php } else if ( !empty( $variation_attributes ) ) { ?>
						
							<?php 	/** Display attributes if they exist.
								We do not support custom template mods, so if this section doesn't wo							
					?>rk for you either remove it, or modify it to suit your needs */
							?>
							<?php foreach( $variation_attributes as $name => $value ) { ?>
							
								<?php 
							
								$aname = str_replace( 'attribute_', '', $name );

								if ( taxonomy_exists( $aname ) ) {
						
									$aname = str_replace( 'attribute_', '', $name );
									
									$t_title = $wp_taxonomies[ $aname ]->label;
									
									$orderby = $woocommerce->attribute_orderby( $aname );

									switch ( $orderby ) {
									
										case 'name' :
											$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
											break;
										case 'id' :
											$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false );
											break;
										case 'menu_order' :
											$args = array( 'menu_order' => 'ASC' );
											break;
									}

									$terms = get_terms( $aname, $args );
									
									$aname = str_replace( 'pa_', '', $aname );
									
									foreach ( $terms as $term ) {
									
										if ( $term->slug !== $value )
											continue;

										?><strong><?php echo $t_title;?></strong>: <?php echo $term->name; ?>
										
										<br/>
										
										<?php
									}
									
								} else { 
							
									?>
							
									<strong><?php echo ucwords( $aname );?></strong>: 
									<?php echo ucwords( $value ) ?>
									
									<br/>
									
									<?php

								}
						
							}
							
						} ?>
					</p>
				</td>

				<td>
					<?php 
					if ( !empty( $event_meta['print_tickets'] ) && 'yes' == $event_meta['print_tickets'] ) { ?>
						<a title="<?php _e( 'Print all tickets for this order', 'ignitewoo_events' ) ?>" href="<?php echo get_permalink( woocommerce_get_page_id( 'myaccount' ) ) .  '?ignitewoo_print_tickets=' . $order->id . '&n=' . wp_create_nonce( 'ignitewoo_print_tickets' ) ?>" target="_blank">
							<?php _e( 'Print / View Tickets', 'ignitewoo_events' ) ?>
						</a>
					<?php } ?>
				</td>


			</tr>

			<?php 

		}

	} 
	
} 
?>
</table>
