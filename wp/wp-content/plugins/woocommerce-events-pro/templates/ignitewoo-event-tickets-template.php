<?php
/**

Event tickets template

Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved

*/

global $ignitewoo_events_pro, $ignitewoo_events;

if ( !defined( 'ABSPATH' ) )
	die;

	global $user_ID;

	$order_id = absint( $_GET['ignitewoo_print_tickets'] );

	// Ensure the logged in user is the owner of the order

	$order = new WC_Order( $order_id );

	if ( !$order ) {
		die;
	}

	if ( $order->user_id != $user_ID ) {
		die;
	}

	$tickets = array();

	$venues = array();


	// Load it up the tickets and info - if any

	$settings = get_option( 'ignitewoo_events_main_settings', false ); 

	if ( sizeof( $order->get_items() ) > 0 ) { 

		// Returns empty if there are no tickets in the order, or when the user is not logged in ( $user_ID is zero or empty ) or when the logged in user is not the user that made the purchase
		$tickets = $ignitewoo_events_pro->get_ticket_list( $order, $settings );

	} else { 

		if ( 'yes' == $settings['qr_code'] ) 
			$span = 3; 
		else 
			$span = 2;

		ob_end_flush();

		_e( 'This order does not contain any items.', 'ignitewoo_events' );

		die;

	}

	// ================ Display the ticket template / data ==============

	if ( count( $tickets ) <= 0 ) { 

		ob_end_flush();

		echo '<p>' . __( 'There are no tickets available for this order', 'ignitewoo_events' ) . '</p>';

		die;
	}

	// $tickets is all ticketed events in the order

	// $ticket becomes a line item in the order, which has a quanity - e.g. Great Concert, Qty: 4, so show 4 tickets for this event

	?>
<html>
	<head>
		<style>
			body { font-family: "Times Roman Times-Roman"; color: #000000; } 
			table { page-break-after:always; page-break-inside:avoid; }
			tr    { page-break-inside:avoid; page-break-after:auto; }
			thead { display:table-header-group; }
			tfoot { display:table-footer-group; }
			.ignitewoo_ticket_item { 
				<?php /** NOTE: WE DO NOT SUPPORT BACKGROUND IMAGES FOR PDF TICKETS AT THIS TIME */ ?>
				background-color: #ffffff;
				border-collapse: collapse;
				clear:both;
				height: 370px;
				overflow: hidden;
				border: 1px solid #4B8BCD;
				width: 491px;
			}
			.ignitewoo_ticket_item .title_area { 
				background-color: #4B8BCD;
			}
			.ticket_event_title { 
				color: white;
				font-size: 25px;
				font-weight: bold;
				padding: 30px 0;
				text-align: center;
				width: 322px;
			}
			.ticket_qr { 
				padding: 15px 17px 0 0;
			}
			.ticket_location { 

			}
			.ticket_data {
				padding: 0 20px 0 0;
			}
			.ticket_venus { 
				padding: 6px 0 0 18px;
			}
			.ticket_venus p { 
				margin: 3px;
			}
			.ticket_venus .venue_name { 
				font-size: 16px;
				font-weight: bold;
				margin-top: 10px;
			}
			.ticket_venus .venue_info { 
				font-size: 14px;
			}
			.ticket_dates { 

				padding: 18px 0 0 18px;
			}
			.ticket_details { 
				padding: 18px 0 0 18px;
			}

		</style>
	</head>
	<body>

	<?php

	foreach( $tickets as $ticket ) { 

		$series_num = 0;

		for ( $i = 0; $i < $ticket['qty']; $i++ ) { 

			$series_num++;

			$ticket_number = $ticket['ticket_number_prefix'] . $series_num;

			?>

				<table class="ignitewoo_ticket_item"> 
					<tr>
					
					    <td class="title_area">
						<div class="ticket_event_title"><?php echo get_the_title( $ticket['product_id'] ) ?></div>
					    </td>
					    
					    <td class="title_area"><div class="ticket_qr">

							<?php 
							
							if ( !empty( $settings['qr_code'] ) && 'yes' == $settings['qr_code'] ) { 

								// Pass in the event product settings, order id, ticket number, and image size
							
								$qr = $ignitewoo_events_pro->gen_qr_code( $settings, $ticket['order_id'], $ticket_number, array( 150, 150 ) );
								
								echo '<img class="ignitewoo_qr_code" src="' . $qr . '">';
							}
							?>
							
							</div>
					    </tr>
					    <tr>
						<td>
							<div class="ticket_location">

								<div class="ticket_venus">

									<?php foreach( $ticket['venues'] as $v ) { ?>

										<p class="venue_name"><?php echo $v['name'] ?></p>
										<p class="venue_info"><?php echo $v['address'] ?></p>
										<p class="venue_info">
											<?php echo $v['city'] ?>, 
											<?php if ( isset( $v['state'] ) && '' != $v['state'] ) echo $v['state'] . ', '; ?>
											<?php echo $v['country'] ?>
										</p>
										<p class="venue_info"><?php echo $v['phone'] ?></p>
										<p class="venue_info"><?php echo $v['email'] ?></p>
										<p class="venue_info"><?php echo $v['web'] ?></p>
									<?php } ?>

								</div>
					
								<?php // If the ticket item has no meta, show the dates, otherwise it might be a recurring event, in which case the date is in the meta to be displayed later in this template  ?>
								<?php if ( empty( $ticket['meta'] ) || count( $ticket['meta'] ) <=0 ) { ?>
								<div class="ticket_dates">

									<?php echo date( $settings['date_format'] . ' ' . $settings['time_format'], strtotime( $ticket['meta']['start_date'] ) ) ?> 

									<?php if ( '' != $ticket['meta']['end_date'] ) { ?>
										&mdash; <br/><?php echo date( $settings['date_format'] . ' ' . $settings['time_format'], strtotime( $ticket['meta']['end_date'] ) ) ?>
									<?php } ?>

								</div>
								<?php } ?>

							</div>
						</td>
						<td>
							<div class="ticket_data"> 

								<div class="ticket_details">

									<?php 
										// ticket number: 
										echo '<p>' . $ticket_number . '</p>';
									?> 

									<?php 
										foreach( $ticket['item_meta'] as $key => $vals ) { 
										
											// Do not print customer-supplied meta data that contains a URL - could expose sensitive info 
											// depending on what the customer uploaded via the Event forms
											
											if ( '_' == substr( $key, 0, 1 ) )
												continue;
												
											
											if ( !empty( $key ) ) {
											
												$key = str_replace( 'pa_' , '', $key );
												
												$key = ucwords( str_replace( '-', ' ', $key ) );
											
											}
											
											
											
											if ( false === strpos( $vals[0], 'http://' ) && false === strpos( $vals[0], 'https://' ) )
												echo '<p>' . $key . ' &ndash; ' . $vals[0] . '</p>';
										}
									?>

								</div>

							</div>
						</td>
					    </tr>
				</table>

				<div style="margin-bottom: 50px"></div>

	<?php

		}
	}

	?>

	</body>
</html>
