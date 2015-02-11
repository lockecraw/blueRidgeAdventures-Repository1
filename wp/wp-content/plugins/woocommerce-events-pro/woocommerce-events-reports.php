<?php

if ( !defined( 'ABSPATH' ) ) 
	die;

class IgniteWoo_Events_Pro_Reports {

	var $plugin_url;

	function __construct() {

		$this->plugin_url =  WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		add_filter( 'woocommerce_reports_charts', array( &$this, 'reports' ) );

		add_action( 'admin_head', array( &$this, 'admin_head' ) ) ;

	}


	function admin_head() { 

		wp_register_script( 'ig_tablesorter', $this->plugin_url . '/assets/js/datatables/jquery.dataTables.min.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter' );

		wp_register_script( 'ig_tablesorter_clip', $this->plugin_url . '/assets/js/datatables/media/js/ZeroClipboard.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter_clip' );

		wp_register_script( 'ig_tablesorter_tools', $this->plugin_url . '/assets/js/datatables/media/js/TableTools.js', array( 'jquery' ), '2.0' );

		wp_enqueue_script( 'ig_tablesorter_tools' );

		wp_register_style( 'ig_tablesorter_css', $this->plugin_url . '/assets/js/datatables/media/css/jquery.dataTables.css' );

		wp_enqueue_style( 'ig_tablesorter_css' );

		wp_register_style( 'ig_tablesorter_tools_css', $this->plugin_url . '/assets/js/datatables/media/css/TableTools.css' );

		wp_enqueue_style( 'ig_tablesorter_tools_css' );

		?>

		<script type="text/javascript" charset="utf-8">

			jQuery( document ).ready( function () {

			    <?php if ( isset( $_POST['ignitewoo_event_select'] ) && absint( $_POST['ignitewoo_event_select'] ) > 0 ) { ?>

				var oTable = jQuery( '.tablesorter' ).dataTable({
					"aLengthMenu": [[10, 25, 50, 100, 200, 999, -1], [10, 25, 50, 100, 200, 999, "All"]],
                                        "iDisplayLength": -1,
				});

				
				var oTableTools = new TableTools( oTable, {
					"sSwfPath": "<?php echo $this->plugin_url ?>/assets/js/datatables/media/swf/copy_csv_xls_pdf.swf",
					"buttons": [
						"copy",
						"csv",
						"xls",
						"pdf",
						{ "type": "print", "buttonText": "Print me!" }
					],

					"aButtons": [ 
						{
						"sExtends" : "copy",
						},
						{
						"sExtends" : "csv",
						"sFieldBoundary" : '"',
						},
						{
						"sExtends" : "xls",
						"sFieldBoundary" : '"',
						},
						{
						"sExtends" : "pdf",
						},
						{
						"sExtends" : "print",
						},
					]
				} );
				
				jQuery( '.tablesorter' ).before( oTableTools.dom.container );


	



			    <?php } ?>
			} );
		</script>

		<style>
		    .woocommerce-reports table tr td{ padding-right: 10px; }
		    /* .tablesorter tr th { min-width: 150px; } */
		    .help_tip { width: 20px }
		</style>

		<?php 
	}


	function reports( $reports ) { 

		$reports['events'] = array( 
			'title' 	=>  __( 'Events', 'ignitewoo_events' ),
			'charts' 	=> array(
						array(
							'title' => __('Event Reporting', 'ignitewoo_events'),
							'description' => '',
							'hide_title' => true,
							'function' => 'ignitewoo_event_details'
						),
					)
			);
		return $reports;

	}

}

$ignitewoo_events_pro_reports = new IgniteWoo_Events_Pro_Reports();

add_action( 'admin_init', 'ignitewoo_events_pro_scanner', 99999 );

function ignitewoo_events_pro_scanner() { 
	global $user_ID, $ignitewoo_events;
	
	if ( empty( $_GET['ignitewoo_ticket_id'] ) )
		return;

	// avoid redirection loops
	if ( !empty( $_GET['event_redirect'] ) && 'true' == $_GET['event_redirect'] )
		return;

	// parse ticket number info
	$parts = explode( '-', $_GET['ignitewoo_ticket_id'] );

	$vendor_can_access = false;
	
	// Is the logged in user the vendor for this product?
	if ( class_exists( 'IgniteWoo_Vendor_Stores' ) ) { 
	
		$vendor = ign_get_product_vendors( $parts[1] );

		if ( !empty( $vendor[0]->admins[0]->ID ) ) {
		
			$vendor_id = $vendor[0]->admins[0]->ID;
			
			if ( !empty( $user_ID ) && $vendor_id == $user_ID ) 
				$vendor_can_access = true;

		}
	
	}

	if ( !$vendor_can_access && ( !is_user_logged_in() || !current_user_can( 'administrator' ) ) ) {

		// User with ticket_checkin role? 
		if ( 'yes' == $ignitewoo_events->settings['ticket_users'] && !current_user_can('ticket_checkin' ) ) {
			wp_redirect( get_bloginfo( 'wpurl' ) . '/wp-login.php?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) );
			die;
		}

	}


	if ( !is_array( $parts ) || count( $parts ) < 3 )
		die( __( 'Error (1) processing the ticket.', 'ignitewoo_events' ) );

	if ( absint( $parts[0] ) <= 0 || absint( $parts[1] ) <= 0 || absint( $parts[2] ) <=0 )
		die( __( 'Error (2) processing the ticket.', 'ignitewoo_events' ) );	


	$settings = get_option( 'ignitewoo_events_main_settings', false );

	// Redirect to the full order editor page
	if ( 'full' == $settings['qr_code_order'] ) {

		$url = admin_url( 'post.php?post=' . $parts[0] . '&action=edit&ignitewoo_ticket_id=' . $_GET['ignitewoo_ticket_id'] . '&event_redirect=true' ); 

		wp_redirect( $url );

		die;
	}


	// Interface settings must be empty or "mini" so continue on with this function


	// Maybe process checkin
	if ( !empty( $_POST['action'] ) && 'event_checkin' == $_POST['action'] && wp_verify_nonce( $_POST['_wpnonce'], 'event_checkin' ) ) { 

		update_post_meta( $parts[0], $_GET['ignitewoo_ticket_id'], current_time( 'timestamp', false ) );

		wp_redirect( $_SERVER['REQUEST_URI'] );

		die;

	}


	// get order
	$order = new WC_Order( $parts[0] );

	if ( empty( $order->id ) ) 
		die( __( 'Error (3) processing the ticket.', 'ignitewoo_events' ) );	

	// loop through items, find tickets, check quantity

	foreach( $order->get_items() as $item ) { 

		if ( 'yes' != get_post_meta( $item['product_id'], '_ignitewoo_event', true ) ) 
			continue;

		if ( $item['product_id'] != $parts[1] && $item['variation_id'] != $parts[1] ) 
			continue;

		if ( absint( $item['qty'] ) <= 0 )
			continue; 

		if ( $item['qty'] < $parts[2] )
			continue;

		$ticket = $item; 

		break; 

	}


	$path = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

	$css = file_exists( get_stylesheet_directory() . '/ignitewoo_events/mini-checkin.css' ) ? get_stylesheet_directory_uri() . '/ignitewoo_events/mini-checkin.css' : $path . '/assets/css/mini-checkin.css';

	?>

	<html>
	<head>
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo $css ?>" />
	</head>
	<body>

	<div id="ticket_order_mini_wrap">

		<h2><?php _e( 'Ticketing Check-in', 'ignitewoo_events' ) ?></h2>

		<div id="ticket_order_info">


			<?php if ( empty( $ticket ) ) { ?>

				<span class="error"><?php _e( 'Error. Ticket not found', 'ignitewoo_events' ) ?></span>

			<?php } else { ?>

				<?php $order_url = admin_url( 'post.php?post=' . $order->id . '&action=edit' ); ?>

				<table id="ignitewoo_mini_checkin">
					<tr><td class="ticket_label"><?php _e( 'Event Name', 'ignitewoo_events' )?></td><td> <?php echo get_the_title( $item['product_id'] ) ?></td></tr>
					<tr><td class="ticket_label"><?php _e( 'Event ID', 'ignitewoo_events' )?></td><td> <?php echo $item['product_id'] ?></td></tr>
					<tr><td class="ticket_label"><?php _e( 'Order #', 'ignitewoo_events' )?></td><td> <a target="_blank" href="<?php echo $order_url ?>"><?php echo $order->id ?></a></td></tr>
					<tr><td class="ticket_label"><?php _e( 'Order Status', 'ignitewoo_events' )?></td><td> <?php echo ucfirst( $order->status ) ?></td></tr>
					<tr><td class="ticket_label"><?php _e( 'Ticket Number', 'ignitewoo_events' )?></td><td> <?php echo $parts[2]; ?></td></tr>

					<?php if ( !empty( $item['variation_id'] ) ) { ?>

						<?php 

							$names = array(); 

							foreach( $item['item_meta'] as $k => $v ) {
							
								if ( '_' == substr( $k, 0, 1 ) )
									continue;
									
								$names[] = '<span class="meta_label">' . $k . ':</span> ' . $v[0];
								
							}

							$names = implode( '<br/> ', $names );

						?>

						<tr><td class="ticket_label"><?php _e( 'Ticket Data', 'ignitewoo_events' )?></td><td> <?php echo $names; ?></td></tr>

					<?php } ?>

					<tr>
						<td colspan="2">
							<?php 
								$checkin = get_post_meta( $order->id, $_GET['ignitewoo_ticket_id'] , true );

							?>

							<?php if ( $checkin ) { ?>
							
								<?php $tz = get_option( 'timezone_string' ) ?>

								<div class="checked_in"><?php _e( 'CHECKED IN:', 'ignitewoo_events' ) ?><br/>
								
								<?php echo date( $settings['date_format'] . ' - ' . $settings['time_format'], $checkin ) . ' (' . $tz  .')'?> 
								
								</div>

							<?php } else { ?>

								<form action="" method="post">
									<?php wp_nonce_field( 'event_checkin' ) ?>
									
									<input type="hidden" name="action" value="event_checkin" />
									
									<input class="event_checkin_button" type="submit" name="submit" value=" <?php _e( 'Check In Now', 'ignitewoo_events' ) ?>" />
									
								</form>

							<?php } ?>
						</td>
					</tr>

				</table>

			<?php } ?>

		</div>

	</div>

	</body>
	</html>

	<?php 

	die;
}


function ignitewoo_event_details() { 
	global $wpdb, $woocommerce;

	@ini_set( 'display_errors', 0 );
	
	$sql = ' 
	SELECT ID, post_title 
	FROM `' . $wpdb->posts . '` 
	left join `' . $wpdb->postmeta . '` m1 on ID = m1.post_id 
	WHERE 
	m1.meta_key = "_ignitewoo_event" and m1.meta_value = "yes" 
	ORDER BY post_title ASC
	';

	$posts = $wpdb->get_results( $sql );

	if ( !isset( $posts ) || '' == $posts ) { 
		_e( 'No events have been created yet.', 'ignitewoo_events' );
		return;
	}


	$current_event = isset( $_POST['ignitewoo_event_select'] ) ? absint( $_POST['ignitewoo_event_select'] ) : '';

	$event = array();

	$options = '';

	foreach( $posts as $p ) { 

		$options .= '<option ' . selected( $current_event, $p->ID, true ) . ' value="' . $p->ID . '">' . $p->post_title . '</option>';

	}


	// Set defaults
	if ( empty( $_POST ) ) { 

		$_POST['ignitewoo_event_report_fields']['id'] = 1;
		$_POST['ignitewoo_event_report_fields']['firstname'] = 1;
		$_POST['ignitewoo_event_report_fields']['lastname'] = 1;
		$_POST['ignitewoo_event_report_fields']['quantity'] = 1;
		$_POST['ignitewoo_event_report_fields']['tickets'] = 1;
		$_POST['ignitewoo_event_report_fields']['data'] = 1;
		$_POST['ignitewoo_event_report_fields']['cost'] = 1;
		$_POST['ignitewoo_event_report_fields']['items'] = 1;
		$_POST['ignitewoo_event_report_fields']['address'] = 1;
		$_POST['ignitewoo_event_report_fields']['city'] = 1;
		$_POST['ignitewoo_event_report_fields']['state'] = 1;
		$_POST['ignitewoo_event_report_fields']['postalcode'] = 1;
		$_POST['ignitewoo_event_report_fields']['country'] = 1;
		$_POST['ignitewoo_event_report_fields']['phone'] = 1;
		$_POST['ignitewoo_event_report_fields']['email'] = 1;
		$_POST['ignitewoo_event_report_fields'][''] = 1;

		$_POST['ignitewoo_event_report_fields']['status'] = array( 'completed', 'processing' );1;

	}

	$statuses = apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'pending', 'on-hold', 'failed', 'refunded', 'cancelled'  ) );

	?>

	<div id="poststuff" class="ignitewoo-reports-wrap">
		<div class="woocommerce-reports">
			<h2><?php _e( 'Generate reports showing who is attending an event', 'ignitewoo_events' ) ?></h2>
			<div class="ignitewoo_postbox">
				<h3><span><?php _e('Select an Event', 'ignitewoo_events'); ?></span></h3>
				<div class="inside">
					<form action="" method="post">
					<p class="stat">
						<select class="chosen" style="width:300px" id="ignitewoo_event_select" name="ignitewoo_event_select"> 
							<option value=""><?php _e( 'Select an event', 'ignitewoo_events' )?></option>
							<?php echo $options ?>
						</select> 
						<input style="margin-left: 10px; position:relative; top: -8px" class="button-primary" type="submit" value=" <?php _e( 'Generate Report', 'ignitewoo_events' ) ?> ">			    
					</p>
					<p>
						<?php _e( 'Select report fields', 'ignitewoo_events' )?> <img class="help_tip" data-tip="<?php _e( 'Note that Cost reflects the total for items in the report. If you include Order Items in your report then Cost reflects the order total.', 'ignitewoo_events' )?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
					</p>
					<table><tr>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['id'], 1 )?> type="checkbox" name="ignitewoo_event_report_fields[id]" value="1"> <?php _e( 'Order ID', 'ignitewoo_events' )?></label></td>
					
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['firstname'], 1 )?> type="checkbox" name="ignitewoo_event_report_fields[firstname]" value="1"> <?php _e( 'First name', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['lastname'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[lastname]" value="1"> <?php _e( 'Last name', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['quantity'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[quantity]" value="1"> <?php _e( 'Quantity', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['tickets'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[tickets]" value="1"> <?php _e( 'Tickets', 'ignitewoo_events' )?></label></td>
						
						<?php /*
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['data'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[data]" value="1"> <?php _e( 'Extra Data', 'ignitewoo_events' )?></label></td>
						*/ ?>
						
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['cost'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[cost]" value="1"> <?php _e( 'Cost', 'ignitewoo_events' )?></label></td>
						
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['items'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[items]" value="1"> <?php _e( 'Order Items', 'ignitewoo_events' )?></label></td>
						
					</tr><tr>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['address'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[address]" value="1"> <?php _e( 'Address', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['city'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[city]" value="1"> <?php _e( 'City', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['state'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[state]" value="1"> <?php _e( 'State', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['postalcode'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[postalcode]" value="1"> <?php _e( 'Postal code', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['country'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[country]" value="1"> <?php _e( 'Country', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['phone'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[phone]" value="1"> <?php _e( 'Phone', 'ignitewoo_events' )?></label></td>
						<td><label><input class="report_field" <?php checked( $_POST['ignitewoo_event_report_fields']['email'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[email]" value="1"> <?php _e( 'Email', 'ignitewoo_events' )?></label></td>
					</tr><tr>
						<td colspan="7"><label><input class="report_field" <?php if ( isset( $_POST['ignitewoo_event_report_fields']['combine_address'] ) ) checked( $_POST['ignitewoo_event_report_fields']['combine_address'], 1 )?>  type="checkbox" name="ignitewoo_event_report_fields[combine_address]" value="1"> <?php _e( 'Combine all selected contact info into the Address field', 'ignitewoo_events' )?></label></td>
					</tr></table>

					<br/>

					<p><?php _e( 'Select the orders whose status matches the settings below:', 'ignitewoo_events' ) ?></p>

					<table></tr>
						<?php foreach( $statuses as $status ) { ?>
							<td><label><input class="report_field" <?php if ( in_array( $status, $_POST['ignitewoo_event_report_fields']['status'] ) ) echo 'checked="checked"' ?>  type="checkbox" name="ignitewoo_event_report_fields[status][]" value="<?php echo $status?>"> <?php _e( ucfirst( $status ), 'ignitewoo_events' )?></label></td>
						<?php } ?>
					</tr></table>


					</form>
				</div>
			</div>
		</div>

		<?php if ( $current_event && $attendees = ignitewoo_get_event_attendees( $current_event ) ) { ?>
		<div class="woocommerce-reports-main">
			<div class="postbox">
				<h3><span><?php _e('Event Purchases', 'ignitewoo_events'); ?></span></h3>
				<p><?php _e( 'This report includes purchases that have a status of completed, processing, and on-hold', 'ignitewoo_events' ) ?></p>
				<div class="inside chart">
					<div id="placeholder" style="width:100%; overflow:hidden; height:568px; position:relative;"></div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php
}

function ignitewoo_get_event_attendees( $event_id ) { 
	global $wpdb;

	//added by Gary
	$order_item_titles = array();

	//$start_date = date( 'Ym', strtotime( '-12 MONTHS', current_time('timestamp') ) ) . '01';

	//$end_date = date( 'Ymd', current_time( 'timestamp' ) );

	$max_sales = $max_totals = 0;

	$product_sales = $product_totals = $buyers = array();
	
	// Get titles and ID's related to product
	// $chosen_product_titles = array();

	$children_ids = array();

	$children = (array) get_posts( 'post_parent=' . $event_id . '&fields=ids&post_status=any&numberposts=-1' );

	$children_ids = $children_ids + $children;

	//$chosen_product_titles[] = get_the_title( $event_id );

	if ( !isset( $_POST['ignitewoo_event_report_fields']['status'] ) || count( $_POST['ignitewoo_event_report_fields']['status'] ) < 1 ) 
		$statuses = array( 'complete' );
	else 
		$statuses = $_POST['ignitewoo_event_report_fields']['status'];

	foreach( $statuses as $s ) 
		$ss[] = "'" . $s . "'";

	$statuses = implode ( ',' , $ss );
	/*
	// Get order items
	$sql = "
		SELECT ID, post_date, meta.meta_value AS items, posts.post_date FROM {$wpdb->posts} AS posts
		
		LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
		LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID=rel.object_ID
		LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
		LEFT JOIN {$wpdb->terms} AS term USING( term_id )

		WHERE 	meta.meta_key 		= '_order_items'
		AND 	posts.post_type 	= 'shop_order'
		AND 	posts.post_status 	= 'publish'
		AND 	tax.taxonomy		= 'shop_order_status'
		AND	term.slug IN (" . $statuses . ")
		AND	posts.post_date	> date_sub( NOW(), INTERVAL 1 YEAR )
		ORDER BY posts.post_date ASC
	";
	$order_items = $wpdb->get_results( $sql );
	*/
	$chosen_product_ids = array( $event_id );

	if ( $chosen_product_ids && is_array( $chosen_product_ids ) ) {

		$start_date = date( 'Ym', strtotime( '-48 MONTHS', current_time('timestamp') ) ) . '01';
		$end_date 	= date( 'Ymd', current_time( 'timestamp' ) );

		$max_sales = $max_totals = 0;
		$product_sales = $product_totals = array();

		// Get titles and ID's related to product
		$chosen_product_titles = array();
		
		$children_ids = array();

		foreach ( $chosen_product_ids as $product_id ) {
			$children = (array) get_posts( 'post_parent=' . $product_id . '&fields=ids&post_status=any&numberposts=-1' );
			$children_ids = $children_ids + $children;
			$chosen_product_titles[] = get_the_title( $product_id );
		}

		// Get order items
		$order_items = apply_filters( 'woocommerce_event_reports_sales_order_items', $wpdb->get_results( "
			SELECT posts.ID as ID, order_item_meta_2.meta_value as product_id, posts.post_date, SUM( order_item_meta.meta_value ) as item_quantity, order_item_meta.order_item_id as item_id, SUM( order_item_meta_3.meta_value ) as line_total
			FROM {$wpdb->prefix}woocommerce_order_items as order_items

			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_3 ON order_items.order_item_id = order_item_meta_3.order_item_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )

			WHERE 	posts.post_type 	= 'shop_order'
			AND 	order_item_meta_2.meta_value IN ('" . implode( "','", array_merge( $chosen_product_ids, $children_ids ) ) . "')
			AND 	posts.post_status 	= 'publish'
			AND 	tax.taxonomy		= 'shop_order_status'
			AND		term.slug			IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "')
			AND 	order_items.order_item_type = 'line_item'
			AND 	order_item_meta.meta_key = '_qty'
			AND 	order_item_meta_2.meta_key = '_product_id'
			AND 	order_item_meta_3.meta_key = '_line_total'
			GROUP BY order_items.order_id
			ORDER BY posts.post_date ASC
		" ), array_merge( $chosen_product_ids, $children_ids ) );

	}
	
	if ( $order_items ) {

		foreach ( $order_items as $pid => $order_item ) {

			$date 	= date( 'Ym', strtotime( $order_item->post_date ) );

			if ( 'yes' != get_post_meta( $order_item->product_id, '_ignitewoo_event', true ) )
				continue;

			if ( $order_item->product_id != $event_id  & ! in_array( $order_item->product_id, $children_ids ) ) 
				continue;

			/*
			if ( 1 == $_POST['ignitewoo_event_report_fields']['items'] ) { 	
			
				$id = $wpdb->get_var( 'select meta_value from ' . $wpdb->prefix . 'woocommerce_order_itemmeta where order_item_id = ' . $order_item->item_id . ' and meta_key="_variation_id"' );
				
				if ( !$id ) 
					$id = $order_item->product_id;
				
				$all_items[] = array( 'name' => get_the_title( $order_item->product_id ), 'id' => $id 	 );
			
			} */

			$info = get_post_custom( $order_item->ID, false );

			$row_cost = $order_item->line_total;

			$buyers[] = array( 
					'id' => $order_item->ID,
					'name' => $info['_billing_last_name'][0] . ' ' . $info['_billing_first_name'][0],
					'first_name' =>  $info['_billing_first_name'][0],
					'last_name' => $info['_billing_last_name'][0],
					'billing_address' => $info['_billing_address_1'][0] . $info['_billing_address_2'][0],
					'billing_city' => $info['_billing_city'][0],
					'billing_state' => $info['_billing_state'][0],
					'billing_country' => $info['_billing_country'][0],
					'billing_postalcode' => $info['_billing_postcode'][0],
					'email' => $info['_billing_email'][0],
					'phone' => $info['_billing_phone'][0],
					'qty' => $order_item->item_quantity,
					'total' => $row_cost,
					'item_meta' => array(), // $item['item_meta'],
					'prefix' => $order_item->ID . '-' . $order_item->product_id . '-',  // ticket numbers prefix
					//'items' => $all_items,
					'order_total' => get_post_meta( $order_item->ID, '_order_total', true )
					);

		}
		
	}

	if ( !$buyers ) { 

		_e( 'No buyers for this event', 'ignitewoo_events' );

		return false;

	}

	?>

	<h2><?php echo get_the_title( $event_id ) ?></h2>

	<p>
		<?php 
			$ss = array();
			_e( 'Statuses:', 'ignitewoo_events' );
			$statuses = explode( ',' , $statuses );
			echo ' '; 
			foreach( $statuses as $s ) 
				$ss[] = ucfirst( str_replace( "'", "", $s ) );
			$ss = implode( ', ' , $ss );
			echo $ss;
		?>
	</p>

	<?php if ( !isset( $_POST['ignitewoo_event_report_fields']['combine_address'] ) ) $_POST['ignitewoo_event_report_fields']['combine_address'] = ''; ?>
	
	<table class="tablesorter">
		<thead>
			<tr>
				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['id'] ) { ?>
					<th><?php _e( 'Order ID', 'ignitewoo_events'); ?></th>
				<?php } ?>
				
				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['firstname'] ) { ?>
					<th><?php _e( 'First Name', 'ignitewoo_events'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['lastname'] ) { ?>
					<th><?php _e( 'Last Name', 'ignitewoo_events'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['address'] ) { ?>
					<th><?php _e( 'Address', 'ignitewoo_events'); ?></th>
				<?php } ?>


				<?php if ( 1 != $_POST['ignitewoo_event_report_fields']['combine_address'] ) { ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['city'] ) { ?>
						<th><?php _e( 'City', 'ignitewoo_events'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['state'] ) { ?>
						<th><?php _e( 'State', 'ignitewoo_events'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['country'] ) { ?>
						<th><?php _e( 'Country', 'ignitewoo_events'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['postalcode'] ) { ?>
						<th><?php _e( 'Postal Code', 'ignitewoo_events'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['email'] ) { ?>
						<th><?php _e( 'Email', 'ignitewoo_events'); ?></th>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['phone'] ) { ?>
						<th><?php _e( 'Phone', 'ignitewoo_events'); ?></th>
					<?php } ?>

				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['quantity'] ) { ?>
					<th><?php _e( 'Quanity', 'ignitewoo_events'); ?></th>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['tickets'] ) { ?>
					<th><?php _e( 'Ticket Numbers', 'ignitewoo_events'); ?></th>
				<?php } ?>

				<?php /* if ( 1 == $_POST['ignitewoo_event_report_fields']['data'] ) { ?>
					<th><?php _e( 'Extra Data', 'ignitewoo_events'); ?></th>
				<?php } */ ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['items'] ) { ?>
					
					<?php 
						//loop through each buyer and their order items to get a complete list of all possible order items
						foreach( $buyers as $b ) {
							
							$o = new WC_Order( $b['id'] );
							
							$items = $o->get_items(); 
							
							foreach( $items as $item ) { 

								foreach( $item['item_meta'] as $key => $data ) {
								
									if ( false !== strpos( $data[0], 'http://' ) || false !== strpos( $data[0] , 'https://' ) )
										continue;
									
									if ( '_' != substr( $key, 0, 1 ) ){		
										$order_item_titles[$key] = $key;
									}
								
								}
							}

						} 
						foreach($order_item_titles as $x => $title){
							echo '<th>'.$title.'</th>';
						}
					?>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['cost'] ) { ?>
					<th><?php _e( 'Cost', 'ignitewoo_events'); ?></th>
				<?php } ?>



			</tr>
		</thead>
		<tbody>
			<?php foreach( $buyers as $b ) { ?>
			<tr>
				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['id'] ) { ?>
					<td>
						<?php 
							echo $b['id']; 
							//if ( $b['billing'] != $b['shipping'] )
							//	echo  '<br/><br/><strong>' . __( 'Shipped to:', 'ignitewoo_events' ) . '</strong><br/>' . $b['shipping'] 
						?>
					</td>
				<?php } ?>
				
				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['firstname'] ) { ?>
					<td>
						<?php 
							echo $b['first_name']; 
							//if ( $b['billing'] != $b['shipping'] )
							//	echo  '<br/><br/><strong>' . __( 'Shipped to:', 'ignitewoo_events' ) . '</strong><br/>' . $b['shipping'] 
						?>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['lastname'] ) { ?>
					<td>
						<?php 
							echo $b['last_name']; 
							//if ( $b['billing'] != $b['shipping'] )
							//	echo  '<br/><br/><strong>' . __( 'Shipped to:', 'ignitewoo_events' ) . '</strong><br/>' . $b['shipping'] 
						?>
					</td>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['combine_address'] ) { //1 == $_POST['ignitewoo_event_report_fields']['address'] ) { ?>
					<td>

							<?php //if ( 1 == $_POST['ignitewoo_donation_report_fields']['combine_address'] ) { ?>

								<?php echo $b['billing_address']; ?>

								<?php echo '<br/>'; ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['city'] ) { ?>
									<?php echo $b['billing_city']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['state'] ) { ?>
									<?php echo $b['billing_state']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['country'] ) { ?>
									<?php echo $b['billing_country']; ?> 
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['postalcode'] ) { ?>
									<?php echo $b['billing_postalcode']; ?>
								<?php } ?>

								<?php echo '<br/>'; ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['phone'] ) { ?>
									<?php echo htmlentities( $b['phone'] ); ?><br/>
								<?php } ?>

								<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['email'] ) { ?>
									<?php echo htmlentities( $b['email'] ); ?>
								<?php } ?>
							<?php //} ?>

					</td>
				<?php } ?>

				<?php if ( 1 != $_POST['ignitewoo_event_report_fields']['combine_address'] ) { ?>
				
					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['address'] ) { ?>
						<td>
							<?php echo $b['billing_address']; ?>
						</td>
					<?php } ?>
					
				
					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['city'] ) { ?>
						<td>
							<?php echo $b['billing_city']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['state'] ) { ?>
						<td>
							<?php echo $b['billing_state']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['country'] ) { ?>
						<td>
							<?php echo $b['billing_country']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['postalcode'] ) { ?>
						<td>
							<?php echo $b['billing_postalcode']; ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['email'] ) { ?>
						<td>
							<?php echo htmlentities( $b['email'] ); ?>
						</td>
					<?php } ?>

					<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['phone'] ) { ?>
						<td>
							<?php echo htmlentities( $b['phone'] ); ?>
						</td>
					<?php } ?>
				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['quantity'] ) { ?>
					<td>
						<?php 
							echo $b['qty'];
						?>
					</td>

				<?php } ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['tickets'] ) { ?>
					<td>
						<?php 
							for( $i = 1; $i <= $b['qty']; $i++ ) 
								echo $b['prefix'] . $i . '<br/><br/>';
						?>
					</td>
				<?php } ?>


				<?php /* if ( 1 == $_POST['ignitewoo_event_report_fields']['data'] ) { ?>
					<td>
						<?php 
							
								
							if ( !empty( $b['item_meta'] ) )
							foreach( $b['item_meta'] as $key => $vals ) { 
							
								// Do not print customer-supplied meta data that contains a URL - could expose sensitive info 
								// depending on what the customer uploaded via the Event forms
								if ( false === strpos( $vals['meta_value'], 'http://' ) && false === strpos( $vals['meta_value'], 'https://' ) )
									echo '<p>' . $vals['meta_name'] . ' &ndash; ' . $vals['meta_value'] . '</p>';

							}
						?>
					</td>
				<?php } */ ?>
				
				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['items'] ) { 
					/*
					?>
					<td>
					    <table class="tablesorter_mini" style="width: 100%">
					    <thead><tr>
						    <th style="width:25%;text-align:left;background-color:transparent; border-bottom: 1px dotted #333"><?php _e( 'SKU', 'ignitewoo_events' )?></th>
						    <th style="text-align:left;background-color:transparent; border-bottom: 1px dotted #333"><?php _e( 'Name', 'ignitewoo_events' ) ?></th>
					    </tr></thead>
					    <tbody>
					    <?php 
						//if ( !empty( $b['items'] ) ) {
							*/

							$my_order_items = array();
							
							$o = new WC_Order( $b['id'] );
							
							$items = $o->get_items(); 
							
							foreach( $items as $item ) { 
								
								//$_product = get_product( $item['id'] );

								//echo '<tr><td>';

								//if ( isset( $item['item_meta']['_sku'] ) ) echo $item['item_meta']['_sku'][0]; else echo '-';
								
								//echo '</td><td>' . $item['name'];

								//if (isset($_product->variation_data)) echo '<br/>' . woocommerce_get_formatted_variation( $_product->variation_data, true );

								foreach( $item['item_meta'] as $key => $data ) {
								
									if ( false !== strpos( $data[0], 'http://' ) || false !== strpos( $data[0] , 'https://' ) )
										continue;
									
									if ( '_' != substr( $key, 0, 1 ) ){										
										//echo '<br/>' . $key . ': ' . $data[0];					
										//echo '<td>'.$data[0].'</td>';
										$my_order_items[$key] = $data[0];
									}
								
								}

								//echo '</td></tr>';

								$b['total'] = $b['order_total'];
							}


							foreach($order_item_titles as $x => $key){
								echo '<td>';
								if(isset($my_order_items[$key])){
									echo $my_order_items[$key];
								}
								echo '</td>';
							}
						/*
						//}
					    ?>
					    </tbody>
					    </table>
					</td>
				<?php 
				*/
				} ?>

				<?php if ( 1 == $_POST['ignitewoo_event_report_fields']['cost'] ) { ?>
					<td><?php echo get_woocommerce_currency_symbol() . $b['total'] ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>

<?php
}

