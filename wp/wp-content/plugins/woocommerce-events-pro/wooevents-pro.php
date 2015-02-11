<?php
/*
Plugin Name: WooEvents Pro
Plugin URI: http://ignitewoo.com
Description: WooEvents Pro lets you sell event tickets. Woohoo! - Copyright (c) 2013 - IgniteWoo.com - ALL RIGHTS RESERVED
Author: IgniteWoo.com
Version: 2.1.12
Author URI: http://ignitewoo.com
*/

/** 

Copyright (c) 2012, 2013 - IgniteWoo.com - ALL RIGHTS RESERVED 

The software is distrbuted WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE. You use this software at your own risk.

*/



add_action( 'plugins_loaded', 'ignitewoo_events_pro_init', 99999999 );

function ignitewoo_events_pro_init() { 
	global $ignitewoo_events, $ignitewoo_events_pro, $woocommerce;

	if ( !class_exists( 'IgniteWoo_Events' ) || !class_exists( 'Woocommerce' ) )
		add_action( 'admin_notices', 'ignitewoo_events_warning' );
		
	if ( empty( $ignitewoo_events ) || empty( $woocommerce ) )
		return;

	$ignitewoo_events_pro = new IgniteWoo_Events_Pro();

}


function ignitewoo_events_warning() { 
	?>
	
	<div class="error">
		<p><strong>WooEvents Pro requires the use of the free Event Calendar & Ticketing and WooCommerce plugins. Go to Plugins -> Add New to install them now</strong></p>

	</div>
	
	<?php 
}


class IgniteWoo_Events_Pro { 


	function __construct() { 
		global $ignitewoo_events;

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->plugin_path = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->register_forms();

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );
		
		require_once( dirname( __FILE__ ) . '/woocommerce-events-cart-helpers.php' );

		$cart_helpers = new IgniteWoo_Events_Pro_Cart_Helpers();

		add_action( 'init',  array( &$this, 'plugin_init' ), 20 );
		
		add_action( 'init',  array( &$this, 'maybe_handle_ical_link' ), -99 );
		
		add_action( 'wp', array( &$this, 'ignitewoo_print_tickets' ), 99 );
		
		add_action( 'wp_head', array( &$this, 'wp_head' ), 15 );

		add_action( 'widgets_init', array( &$this, 'register_widgets' ), -99 );
		
		add_action( 'woocommerce_before_my_account', array( &$this, 'show_customer_events' ) );
		
		add_action( 'woocommerce_email_before_order_table', array( &$this, 'order_include_printing_instructions' ) );
		
		add_action( 'woocommerce_before_checkout_form', array( $this, 'pre_check_for_excess_ticket_quantities' )  );

		add_action( 'woocommerce_after_checkout_validation', array( &$this, 'process_checkout' ), -99 );

		add_action( 'wp_ajax_woocommerce_update_order_review', array( &$this, 'process_checkout' ), -99 );

		add_action( 'wp_ajax_nopriv_woocommerce_update_order_review', array( &$this, 'process_checkout' ), -99 );

		add_action( 'woocommerce_before_cart_table', array( &$this, 'woocommerce_before_cart_table' ), 1 );

		add_action( 'ignitewoo_events_pro_expiration', array( &$this, 'expiration_helper' ), 5, 2 );

		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

		add_filter( 'single_template', array( &$this, 'get_post_type_template' ) ) ;

		if ( !empty( $ignitewoo_events->settings['remove_past_dates'] ) && 'yes' == $ignitewoo_events->settings['remove_past_dates'] )
			add_filter( 'woocommerce_available_variation', array( &$this, 'woocommerce_available_variation' ), 1, 3 );
		
		add_action( 'admin_init', array( &$this, 'ignitewoo_ticket_checkin_admin' ), 1 );

		if ( is_admin() ) { 
	
			add_filter('post_updated_messages', array( &$this, 'product_updated_messages' ), 9999 ) ;

			if ( !empty( $ignitewoo_events->settings['events_posting'] ) ) 
				if ( !empty( $ignitewoo_events ) && 'tickets_only' == $ignitewoo_events->settings['events_posting'] || 'events_and_tickets' == $ignitewoo_events->settings['events_posting'] ) {
				
					add_action( 'woocommerce_product_write_panel_tabs', array( &$this, 'tab' ), -1 );

					add_filter( 'product_type_options', array( &$this, 'product_type_options' ), 9999, 1 );

				}
			
			//add_action( 'woocommerce_product_write_panels', array( &$this, 'event_data_panel' ), -1 );
			
			add_action( 'wp_ajax_ignitewoo_event_checkin', array( &$this, 'ignitewoo_event_checkin' ) );

			add_action( 'ignitewoo_events_pro_post_recurrence', array( &$this, 'meta_recurrence' ) );

			add_action( 'ignitewoo_events_pro_ticket_limits', array( &$this, 'meta_ticket_limits' ) );

			add_action( 'ignitewoo_events_gcal_ical_map', array( &$this, 'meta_gcal_ical_map' ) );

			add_action( 'ignitewoo_events_pro_printable_tickets', array( &$this, 'meta_printable_tickets' ) );

			add_action( 'ignitewoo_events_pro_custom_forms', array( &$this, 'meta_custom_forms' ) );

			add_action( 'ignitewoo_events_sessions_tracks', array( &$this, 'meta_sessions_tracks' ) );

			add_action( 'ignitewoo_events_pro_settings', array( &$this, 'settings_maps_codes' ) );

			add_action( 'ignitewoo_events_pro_dates_desc', array( &$this, 'dates_desc' ) );

			add_action( 'save_post', array( &$this, 'process_meta_box' ), 99, 2 );

			add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), -1 );

			add_action( 'recurrence_save', array( &$this, 'save_post_data' ), 1 );

			add_action( 'ignitewoo_events_pro_load_rules', array( &$this, 'load_rules' ), 1 );

			add_action( 'admin_head', array( &$this, 'admin_head' ), 99999999 );

			add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ), -1 );

			add_action( 'plugin_row_meta', array( &$this, 'add_meta_links' ), 10, 2 );

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( &$this, 'add_plugin_action_links' ) );

			require_once( dirname( __FILE__ ) . '/woocommerce-events-reports.php' );

		}

	}

	function plugin_init() { 
		global $wp_roles; 
		
		if ( class_exists( 'WP_Roles' ) ) 
		if ( !isset( $wp_roles ) ) 
			$wp_roles = new WP_Roles();   
	
		if ( is_object( $wp_roles ) ) { 

			$caps = array( 
				'read' => true,
				'edit_posts' => false,
				'delete_posts' => false,

			);

			remove_role( 'ticket_checkin' );
			remove_role( 'ticket_checking' );
			
			add_role ('ticket_checkin', 'Ticket Checkin', $caps );

			$role = get_role( 'ticket_checkin' ); 
			
			$role->add_cap( 'ticket_checkin' );

		}
	}
	
	function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_events' );

		// Allow upgrade safe, site specific language files in /wp-content/languages/woocommerce-subscriptions/
		load_textdomain( 'ignitewoo_events', WP_LANG_DIR . '/ignitewoo_events-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		load_plugin_textdomain( 'ignitewoo_events', false, $plugin_rel_path );
		

	}
	
	
	function register_forms() { 

		register_post_type('event_forms', 
			array(	'label' => 'Event Forms',
				'description' => '',
				'public' => true,
				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => 'ignitewoo_events_settings',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'query_var' => false,
				'supports' => array('title','author'),
				'labels' => array (
					'name' => 'Event Forms',
					'singular_name' => 'Event Form',
					'menu_name' => 'Event Forms',
					'add_new' => 'Add Form',
					'add_new_item' => 'Add New Form',
					'edit' => 'Edit Forms',
					'edit_item' => 'Edit Form',
					'new_item' => 'New Form',
					'view' => 'View Form',
					'view_item' => 'View Form',
					'search_items' => 'Search Form',
					'not_found' => 'No Forms Found',
					'not_found_in_trash' => 'No Forms Found in Trash',
					'parent' => 'Parent Form',
				),
			) 
		);
	}


	function enqueue_scripts() { 

		$css = file_exists( get_stylesheet_directory() . '/ignitewoo_events/events-pro.css' ) ? get_stylesheet_directory_uri() . '/ignitewoo_events/events-pro.css' : $this->plugin_url . '/assets/css/events-pro.css';

                wp_enqueue_style( 'ignitewoo_events_pro_frontend_styles', $css );

	}

	
	function wp_head() { 
		global $post, $product;

		if ( !is_active_widget( false, false, 'ignitewoo_mini_cal', true ) )
			return;
		
		if ( empty( $this->settings ) )
			$this->settings = get_option( 'ignitewoo_events_main_settings', false ); 

		if ( !$this->settings ) { 
			$this->settings = array();
			$this->settings['tooltip_color'] = 'blue';
		}

		if ( empty( $this->settings['calendar_start_day'] ) ) 
			$this->settings['calendar_start_day'] = '0';

		// Mini Calendar Widget Script
		?>

		<script>
		jQuery(document).ready(function() {

			ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

			var date = new Date();
			var d = date.getDate();
			var m = date.getMonth();
			var y = date.getFullYear();
			jQuery( ".mini_calendar" ).fullCalendar({
				theme: false,
				firstDay: '<?php echo !empty( $this->settings['calendar_start_day'] ) ? $this->settingsv['calendar_start_day'] : '' ?>',
				header: {
					left: 'prev,next today',
					center: 'title',
					right: ''
				},
				editable: false,

				// add event name to title attribute on mouseover
				eventMouseover: function(event, jsEvent, view) {
					if (view.name !== 'agendaDay') {
					jQuery(jsEvent.target).attr('title', event.title);
					}
				},
				events: function(start, end, callback) {
					jQuery.ajax({
						type: "post",
						cache: true,
						url: ajaxurl,
						dataType: "json",
						data: {
							action: "ignitewoo_get_events",
							n: "<?php echo wp_create_nonce( 'ignitewoo_get_events' ) ?>",
							start: Math.round( start.getTime() / 1000 ),
							end: Math.round( end.getTime() / 1000 )
							},
					}).done( function( data ) {

						var events = [];
						jQuery.each( data, function( i, item ) {

							events.push({
								title: item.title,
								start: item.start,
								end: item.end,
								url: item.url,
								description: item.description,
								allDay: item.allDay,
							});
						});
						callback( events );
					});
					
				},
				eventBackgroundColor: "<?php echo !empty( $this->settings['event_bg_color'] ) ? $this->settings['event_bg_color'] : '' ?>",
				eventTextColor: "<?php echo !empty( $this->settings['event_fg_color'] ) ? $this->settings['event_fg_color'] : '' ?>",
			})
		});
		</script>

		<?php

	}
	
	
	function product_updated_messages( $messages ) {

		$messages['product'] = array(
			427 => __( 'Event recurrance and product attributes updated. Make sure your event variations are linked and configured properly if you changed the recurrence schedule' ),
		);

		return $messages;
	}
	

	function register_widgets() {
	
		if ( !class_exists( 'IgniteWoo_Widget_Events_Mini_Cal' ) )
			require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-widgets.php' );
		
		register_widget( 'IgniteWoo_Widget_Events_Mini_Cal' );

	}
	
	function ignitewoo_ticket_checkin_admin() {

		add_filter( 'woocommerce_prevent_admin_access', array( &$this, 'ignitewoo_ticket_checkin_access' ), 99999, 1 );
	}

	
	function ignitewoo_ticket_checkin_access( $prevent_access = true ) {
		global $ignitewoo_events;

		if ( current_user_can( 'administrator' ) ) 
			return false;
		else if ( current_user_can( 'ticket_checkin' ) && 'yes' == $ignitewoo_events->settings['ticket_users'] )
			return false;
		else
			return $prevent_access;
	} 


	function process_meta_box( $post_id, $post ) {
		global $typenow, $post;

		if ( empty( $post ) ) 
			return;

		//if ( empty( $_POST['_ignitewoo_event'] ) )
		//	delete_post_meta( $post_id, '_ignitewoo_event' );
			
		if ( !in_array( $post->post_type, array( 'event_forms', 'event_organizer', 'event_venue', 'event_speaker', 'event_sponsor' ) ) && 
			!in_array( $typenow, array( 'event_forms', 'event_organizer', 'event_venue', 'event_speaker', 'event_sponsor' ) ) 
		    )
			return;

		if ( 'event_forms' == $typenow ) {
			$this->process_forms_meta_box( $post_id, $post );
			return;
		}

	}


	function process_forms_meta_box( $post_id, $post ) { 
		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-forms-process-metabox.php' );
		process_forms_meta_box( $post_id, $post );
	}


	function add_meta_box() { 
	
		add_meta_box( 'ignitewoo-event-forms', __( 'Event Form Fields', 'ignitewoo_events' ), array( &$this, 'meta_box' ), 'event_forms', 'normal', 'high' );
		
		add_meta_box( 'woocommerce-product-data', __('Event Tickets', 'ignitewoo_events'), array( &$this, 'woocommerce_order_data_box' ), 'shop_order', 'side', 'high' ) ;

	}

	
	function meta_box() { 
		global $post;
		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-forms-metabox.php' );
		meta_box( $post );
	}


	function meta_recurrence() { 
		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-post-meta.php' );
	}


	function meta_ticket_limits() { 
		global $typenow; 

		if ( 'ignitewoo_event' == $typenow ) 
			return;
			
		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-ticket-limits.php' );
	}

	function tab() { 
		?>
		<li class="ignitewoo_event_tab ignitewoo_event_options show_if_ignitewoo_event"><a href="#ignitewoo_event_product_data"><?php _e('Event Info', 'ignitewoo_events' ); ?></a></li>
		<?php 
	}


	function product_type_options( $opts ) { 

		if ( !class_exists( 'Woocommerce' ) ) { 

			// unset all options
			$opts = array();

			return $opts; 

		}

		$opts['ignitewoo_event'] = array(
				'id' => '_ignitewoo_event', 
				'wrapper_class' => 'show_if_simple show_if_variable show_if_ignitewoo_event_checkbox', 
				'label' => __('Event', 'ignitewoo_events'), 
				'description' => __('Check this box if this is an event.', 'ignitewoo_events') 
			);

		return $opts;

	}
	


	function meta_printable_tickets() { 
		global $ignitewoo_events, $ignitewoo_events_admin, $typenow; 

		if ( 'ignitewoo_event' == $typenow ) 
			return;
			
		$event_info = $ignitewoo_events->get_post_data();

		$event_defaults = array( 
			'print_tickets' => '',
		);
		
		$event_info = wp_parse_args( $event_info, $event_defaults );
		
		?>
		<p class="form-field">
			<label><?php _e( 'Printable Tickets', 'ignitewoo_events' ); ?></label>
			<input style="float:none" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[print_tickets]' value='yes' <?php if ( 'yes' == $event_info['print_tickets'] ) echo 'checked="checked"'; ?> /> <?php _e( "Allow buyers to print tickets for this event from their Order Details page", 'ignitewoo_events' ) ?>
		</p>
		<?php
	}


	function meta_custom_forms() { 
		global $typenow; 

		if ( 'ignitewoo_event' == $typenow ) 
			return;
			
		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-custom-forms.php' );
	}


	function meta_sessions_tracks() { 

		global $post;

		require_once( dirname( __FILE__ ) . '/woocommerce-events-pro-sessions-tracks.php' );
	}


	function meta_gcal_ical_map() { 
		global $ignitewoo_events, $ignitewoo_events_admin;

		$event_info = $ignitewoo_events->get_post_data();
		
		$event_defaults = array( 
			'venue_ical_calendar_link' => '',
			'venue_google_calendar_link' => '',
		);
		
		$event_info = wp_parse_args( $event_info, $event_defaults );
		?>

		<p class="form-field">
			<label><?php _e( 'iCal Calendar Link', 'ignitewoo_events' ); ?></label>
			<input style="float:none" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[venue_ical_calendar_link]' value='yes' <?php if ( 'yes' == $event_info['venue_ical_calendar_link'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Display a iCal export link on individual event pages?', 'ignitewoo_events' ) ?>
		</p>

		<p class="form-field">
			<label><?php _e( 'Google Calendar Link', 'ignitewoo_events' ); ?></label>
			<input style="float:none" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[venue_google_calendar_link]' value='yes' <?php if ( 'yes' == $event_info['venue_google_calendar_link'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Display a Google Calendar export link location on individual event pages?', 'ignitewoo_events' ) ?>
		</p>
		<?php
	}


	function dates_desc() { 
		?> 
		<p class="description"><?php _e( 'For recurring events, the duration of each event is calculated by determining the duration between the start and end date.', 'ignitewoo_events' )?></p>
		<p class="description"><?php _e( 'Example: For a recurring event that happens every day from 8am to 5pm, set the start date/time to 8am and the end date/time to 5pm of the same day.', 'ignitewoo_events' )?></p>
		<?php
	}


	function save_post_data() { 
		global $ignitewoo_events, $post;

		require_once( dirname( __FILE__ ) . '/woocommerce-events-rules.php' );

		remove_action( 'woocommerce_process_product_meta', array( $ignitewoo_events, 'save_post_data' ), 99999 );

		remove_action( 'woocommerce_process_product_meta', array( 'IgniteWoo_Events', 'save_post_data' ), 99999 );

		//remove_action( 'woocommerce_process_product_meta', array( $this, 'save_post_data' ), 99999 );

		$update_event_rules = new IgniteWoo_Events_Rules();

		$update_event_rules->save_events( $post->ID );

	}

	
	function woocommerce_available_variation( $variations, $_product, $variation ) { 

		if ( empty( $variations['attributes']['attribute_date'] ) || empty( $_product->post->ID ) )
			return $variations;
		
		if ( 'yes' != get_post_meta( $_product->post->ID, '_ignitewoo_event', true ) )
			return $variations;
			
		$attr = strtoupper( str_replace( '-', ' ', $variations['attributes']['attribute_date'] ) );
		
		$attr = substr( $attr, 0, 6 ) . ', ' . substr( $attr, 7, 4 );

		$date = strtotime( $attr );

		if ( empty( $date ) )
			return $variations;

		if ( current_time( 'timestamp', false ) > $date )
			return array();
			
		return $variations;
	}


	function load_rules() { 

		require_once( dirname( __FILE__ ) . '/woocommerce-events-rules.php' );
	}



	function admin_head() { 
		global $post;

		require_once( dirname( __FILE__ ) . '/woocommerce-events-admin-styles-scripts.php' );
	}


	function admin_scripts() { 

		if ( !empty( $_GET['page'] ) && 'ignitewoo_events_settings' != $_GET['page'] ) 
			return;

		wp_enqueue_script('farbtastic');
		
		wp_enqueue_style('farbtastic');

	}


	function settings_maps_codes() { 
		global $data;

		if ( empty( $data['event_bg_color'] ) ) 
			$data['event_bg_color'] = '#3366cc';

		if ( empty( $data['event_fg_color'] ) ) 
			$data['event_fg_color'] = '#ffffff';

		if ( empty( $data['tooltip_color'] ) ) 
			$data['tooltip_color'] = 'tipped';

		if ( empty( $data['qr_code_order'] ) ) 
			$data['qr_code_order'] = 'mini';

		?>

		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Remove Past Dates', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<input class="small" type="checkbox" value="yes" <?php if ( isset( $data['remove_past_dates'] ) && 'yes' == $data['remove_past_dates'] ) echo 'checked="checked"' ; ?> name="ignitewoo_event_settings[remove_past_dates]" > Enable
				<p class="description"><?php _e( 'When enabled, if your WooCommerce-based events are recurring then dates in the past will not appear in the date selection dropdown on the product page', 'ignitewoo_events' )?></p>

			</td>
		</tr>
		
		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Show QR Codes', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<input class="small" type="checkbox" value="yes" <?php if ( isset( $data['qr_code'] ) && 'yes' == $data['qr_code'] ) echo 'checked="checked"' ; ?> name="ignitewoo_event_settings[qr_code]" > Enable
				<p class="description"><?php _e( 'When enabled QR Codes will appear on customer\'s My Account page for each event when the event has Print Tickets enabled. When the customer presents this code at the event you can scan it with QR Code Scanner app on your mobile device. When scanned you will be taken directly to the customer order in your store where you can verify the purchase. Note that the first time you scan a code you may be prompted to login to your site as an admin or store manager before you can view the order.', 'ignitewoo_events' )?></p>

			</td>
		</tr>

		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Ticket Scan Interface', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<label><input class="small" type="radio" value="full" <?php if ( 'full' == $data['qr_code_order'] ) echo 'checked="checked"' ; ?> name="ignitewoo_event_settings[qr_code_order]" > <?php _e( 'Full Order Interface', 'ignitewoo_events' ) ?></label>
				<label><input class="small" type="radio" value="mini" <?php if ( 'mini' == $data['qr_code_order'] ) echo 'checked="checked"' ; ?> name="ignitewoo_event_settings[qr_code_order]" > <?php _e( 'Mini Order Interface', 'ignitewoo_events' ) ?></label>
				<p class="description"><?php _e( 'When QR Codes are enabled and you scan the code, you can be taken directly to the full customer order or to a mini order interface.', 'ignitewoo_events' )?></p>

			</td>
		</tr>

		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Ticket Scan Users', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<label><input class="small" type="checkbox" value="yes" <?php if ( 'yes' == $data['ticket_users'] ) echo 'checked="checked"' ; ?> name="ignitewoo_event_settings[ticket_users]" > <?php _e( 'Only admins and users with the Ticket Checkin role can access the mini order interface', 'ignitewoo_events' ) ?></label>
				

			</td>
		</tr>
		
		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Foreground Color', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<input type="text" value="<?php echo $data['event_fg_color'] ?>" id="event_fg_color_code" name="ignitewoo_event_settings[event_fg_color]"> <?php _e( 'Foreground color of events on the calendar', 'ignitewoo_events' ); ?>  
				<div class="ignitewoo_events_colorpicker" id="colorpickerdiv_event_fg_color"></div>
			</td>
		</tr>

		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Background Color', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<input type="text" value="<?php echo $data['event_bg_color'] ?>" id="event_bg_color_code" name="ignitewoo_event_settings[event_bg_color]"> <?php _e( 'Background color of events on the calendar', 'ignitewoo_events' ); ?> 
				<div class="ignitewoo_events_colorpicker" id="colorpickerdiv_event_bg_color"></div>
			</td>
		</tr>


		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Calendar Tooltip Color', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<select name="ignitewoo_event_settings[tooltip_color]">
					<option value="red" <?php echo selected( $data['tooltip_color'], 'red', false ) ?>><?php _e( 'Red', 'ignitewoo_events' )?></option>
					<option value="blue" <?php echo selected( $data['tooltip_color'], 'blue', false ) ?>><?php _e( 'Blue', 'ignitewoo_events' )?></option>
					<option value="green" <?php echo selected( $data['tooltip_color'], 'green', false ) ?>><?php _e( 'Green', 'ignitewoo_events' )?></option>
					<option value="plain" <?php echo selected( $data['tooltip_color'], 'plain', false ) ?>><?php _e( 'Pale Yellow', 'ignitewoo_events' )?></option>
					<option value="dark" <?php echo selected( $data['tooltip_color'], 'dark', false ) ?>><?php _e( 'Black & Grey', 'ignitewoo_events' )?></option>
					<option value="light" <?php echo selected( $data['tooltip_color'], 'light', false ) ?>><?php _e( 'White & Gray', 'ignitewoo_events' )?></option>
					<option value="bootstrap" <?php echo selected( $data['tooltip_color'], 'bootstrap', false ) ?>><?php _e( 'White & Grey - Larger', 'ignitewoo_events' )?></option>
					<option value="jtools" <?php echo selected( $data['tooltip_color'], 'jtools', false ) ?>><?php _e( 'White on Black w/White Border', 'ignitewoo_events' )?></option>
					<option value="youtube" <?php echo selected( $data['tooltip_color'], 'youtube', false ) ?>><?php _e( 'White on Black', 'ignitewoo_events' )?></option>
					<option value="tipsy" <?php echo selected( $data['tooltip_color'], 'tipsy', false ) ?>><?php _e( 'White on Black 2', 'ignitewoo_events' )?></option>
					<option value="cluetip" <?php echo selected( $data['tooltip_color'], 'cluetip', false ) ?>><?php _e( 'Greyish', 'ignitewoo_events' )?></option>
					<option value="tipped" <?php echo selected( $data['tooltip_color'], 'tipped', false ) ?>><?php _e( 'Blue title bar, White content area', 'ignitewoo_events' )?></option>
				</select>
				<p class="description"><?php _e( 'Sets the color scheme for the tooltip that appears when hovering over an event on the calendar.', 'ignitewoo_events' )?></p>

			</td>
		</tr>

		<tr>
			<th style="width: 120px; vertical-align:top">
				<h4 style="margin:0"><label><?php _e( 'Calendar Week Start', 'ignitewoo_events' ); ?></label></h4>
			</th>
			<td>
				<?php if ( !isset( $data['calendar_start_day'] ) ) $data['calendar_start_day'] = 0 ?>
				<select name="ignitewoo_event_settings[calendar_start_day]">
					<option value="0" <?php echo selected( $data['calendar_start_day'], '0', false ) ?>><?php _e( 'Sunday', 'ignitewoo_events' )?></option>
					<option value="1" <?php echo selected( $data['calendar_start_day'], '1', false ) ?>><?php _e( 'Monday', 'ignitewoo_events' )?></option>
					<option value="2" <?php echo selected( $data['calendar_start_day'], '2', false ) ?>><?php _e( 'Tuesday', 'ignitewoo_events' )?></option>
					<option value="3" <?php echo selected( $data['calendar_start_day'], '3', false ) ?>><?php _e( 'Wednesday', 'ignitewoo_events' )?></option>
					<option value="4" <?php echo selected( $data['calendar_start_day'], '4', false ) ?>><?php _e( 'Thursday', 'ignitewoo_events' )?></option>
					<option value="5" <?php echo selected( $data['calendar_start_day'], '5', false ) ?>><?php _e( 'Friday', 'ignitewoo_events' )?></option>
					<option value="6" <?php echo selected( $data['calendar_start_day'], '6', false ) ?>><?php _e( 'Saturday', 'ignitewoo_events' )?></option>
				</select>
				<p class="description"><?php _e( 'Set the day of the starting day of week for the calendar.', 'ignitewoo_events' )?></p>

			</td>
		</tr>

		<?php 
	}

	
	function order_include_printing_instructions( $order ) { 
		
		if ( !$this->order_contains_tickets( $order ) )
			return;
			
		$myaccount_page = get_permalink( woocommerce_get_page_id( 'myaccount' ) );
		
		echo apply_filters( 'event_print_tickets_message', sprintf( __( 'To print your tickets visit your <a href="%s">My Account</a> page at <a href="%s">%s', 'ignitewoo_events' ), $myaccount_page, get_bloginfo( 'url' ), get_bloginfo( 'name' ) ) );
		
	}
	
	
	function order_contains_tickets( $order ) { 

		if ( 'completed' != $order->status && 'processing' != $order->status ) 
			return;
		
		if ( sizeof( $order->get_items() ) > 0 ) { 

			foreach( $order->get_items() as $item ) {

				$_product = get_product( $item['product_id'] );

				if ( empty( $_product ) )
					continue;
					
				if ( 'yes' != get_post_meta( $_product->id, '_ignitewoo_event', true ) )
					continue;

				if ( absint( $item['qty'] < 1 ) )
					continue;
					
				$meta = get_post_meta( $_product->id, '_ignitewoo_event_info', true );
				
				if ( empty( $meta['print_tickets'] ) || 'yes' != $meta['print_tickets'] )
					continue;
					
				return true;
					
			}
		}
	}
	
	
	function show_customer_events() { 
		global $woocommerce, $post;

		$customer_id = get_current_user_id();

		$args = array(
			'numberposts' => 9999999,
			'meta_key' => '_customer_user',
			'meta_value' => $customer_id,
			'post_type' => 'shop_order',
			'post_status' => 'publish' 
		);

		$customer_orders = get_posts( $args);

		if ( !$customer_orders )
			return;
			
		$template = locate_template( array( 'ignitewoo_events/ignitewoo-event-ticket-list.php' ), false, false );

		if ( '' != $template ) 
			require ( $template );
		else 
			require ( dirname( __FILE__ ) . '/templates/ignitewoo-event-ticket-list.php' );

	}
	


	function get_ticket_list( $order = '', $settings ) { 
		global $user_ID;
		
		if ( !$user_ID )
			return array(); 
			
		if ( empty( $order ) ) 
			return array();

		if ( $user_ID != $order->customer_user )
			return array();
			
		$tickets = array();

		foreach( $order->get_items() as $item ) {

			$_product = $order->get_product_from_item( $item );

			if ( !$_product ) 
				continue;

			if ( !empty( $_product->variation_id ) )
				$prefix = $_product->variation_id;
			else 
				$prefix = $_product->id;
			
			if ( !isset( $item['item_meta'] ) || empty( $item['item_meta'] ) )
				$item['item_meta'] = array();

			//$pid = $item['product_id']; 

			if ( 'yes' != get_post_meta( $item['product_id'], '_ignitewoo_event', true ) )
				continue;

			if ( absint( $item['qty'] < 1 ) )
				continue;

			$meta = get_post_meta( $item['product_id'], '_ignitewoo_event_info', true );

			if ( empty( $meta['print_tickets'] ) || 'yes' != $meta['print_tickets'] ) 
				continue;

			if ( !empty( $meta['event_venue'] ) )
				$venue_list = get_post( $meta['event_venue'][0] );

			if ( $venue_list ) { 

				$venue_meta = get_post_custom( $venue_list->ID, true );

				$address = $venue_meta['_generic_address'][0];
				$city = $venue_meta['_generic_city'][0];
				$data = $venue_meta['_generic_country_state'][0];

				if ( isset( $data ) && false != $data )
					$data = explode( ':', $data);
				else 
					$data = array();

				$country = $data[0];

				if ( count( $data ) > 1 ) 
				    $state = $data[1];
				else
				    $state = '';

				$venues[] = array( 
					    'name' => get_the_title( $venue_list->ID ),
					    'address' => $address,
					    'city' => $city,
					    'country' => $country,
					    'state' => $state,
					    'email' => $venue_meta['_generic_email'][0],
					    'phone' => $venue_meta['_generic_phone'][0],
					    'web' => $venue_meta['_generic_website'][0],
					    );

			}

			if ( !empty( $meta['print_tickets'] ) && 'yes' == $meta['print_tickets'] ) { 

				$tickets[] = array( 
					'order_id' => $order->id,
					'item_id' => $_product->id,
					'ticket_number_prefix' => $order->id . '-' . $prefix . '-',
					'qty' => $item['qty'],
					'product_id' => $_product->id,
					'item_meta' => $item['item_meta'], 
					'title' => get_the_title( $_product->id ),
					'meta' => $meta,
					'venues' => $venues
				);

			}

		}

		return $tickets; 

	}


	function ignitewoo_print_tickets() { 

		global $user_ID; 

		if ( !$user_ID ) 
			return;

		if ( !isset( $_GET['ignitewoo_print_tickets'] ) || absint( $_GET['ignitewoo_print_tickets'] ) <= 0 || !wp_verify_nonce( $_GET['n'], 'ignitewoo_print_tickets' ) )
			return;
		
		$template = locate_template( array( 'ignitewoo_events/ignitewoo-event-tickets-template.php' ), false, false );

		if ( '' != $template ) 
			require_once ( $template );
		else 
			require_once( dirname( __FILE__ ) . '/templates/ignitewoo-event-tickets-template.php' );

		$out = ob_get_contents();

		die;

	}


	function gen_qr_code( $settings = array(), $order_id = 0, $ticket_number = 0, $size = array( 150, 150 ) ) { 

		$interface = $settings['qr_code_order'];
	
		if ( !is_array( $size ) )
			$size = array( 150, 150 );
	
		$size = $size[0] . 'x' . $size[1];

		if ( 'mini' == $interface )
			$url = admin_url( '?ignitewoo_ticket_id=' . $ticket_number ); 
		else 
			$url = urlencode( admin_url( 'post.php?post='. $order_id . '&action=edit' ) );

		// Docs: https://developers.google.com/chart/infographics/docs/qr_codes?csw=1
		$url = 'https://chart.googleapis.com/chart?chs=' . $size . '&cht=qr&chl=' . $url . '&choe=UTF-8';

		return $url;
		
	}


	function woocommerce_before_cart_table() { 
		global $woocommerce;

		$this->check_for_excess_ticket_quantities(); 
	}


	function process_checkout() { 

		$this->check_for_excess_ticket_quantities( 'checkout' );

	}


	// Helper function that checks orders for exceeding any purchase limit restrictions
	// Used by check_for_excess_ticket_quantities();
	function check_restriction( $data, $product_id ) { 
		global $wpdb, $user_ID; 

		if ( empty( $user_ID ) && empty( $_REQUEST['billing_email'] ) )
			return false;
		
		if ( !empty( $_REQUEST['billing_email'] ) )
			$billing_email = $_REQUEST['billing_email'];
		else
			$billing_email = get_user_meta( $user_ID, 'billing_email', true );

		$interval = $data['max_ticket_interval'];

		$interval_type = $data['max_ticket_interval_type']; 

		$now = current_time( 'timestamp' ); 

		$since = strtotime( '-'. $interval . ' ' . $interval_type , $now );

		$date = date( 'Y-m-d H:i:s', $since );

		$sql = 'select ID from ' . $wpdb->posts . ' left join ' . $wpdb->postmeta . ' on ID = post_id 
			where meta_key = "_billing_email" and meta_value="' . $billing_email . '"
			AND post_type = "shop_order" 
			AND post_status = "publish"
			AND post_date > "' . $date . '"';

		$orders = $wpdb->get_results( $sql );

		if ( !$orders )
			return false;

		$already_purchased = false;

		$qty = 0;

		foreach( $orders as $o ) { 

			$order = new WC_Order( $o->ID );

			if ( !$order ) 
				continue; 

			// Don't consider orders that have failed, been cancelled, or been refunded
			if ( in_array( $order->status, array( 'failed', 'cancelled', 'refunded' ) ) )
				continue; 

			foreach( $order->get_items() as $item ) { 

				if ( $item['product_id'] == $product_id )
					$qty += $item['qty'];

			}


		}

		if ( $qty >= $data['max_ticket_qty'] ) 
			return $qty;
		else
			return false;

	}

	
	// Checkout page, when pages loads
	function pre_check_for_excess_ticket_quantities( $checkout ) { 
	
		$this->check_for_excess_ticket_quantities( 'checkout' );
	
	}

	// Check cart items for exceeding any purchase limit restrictions
	// Also check past orders for the same purchases
	// TODO: Make sure this adds together quantities of the same master product
	function check_for_excess_ticket_quantities( $location = 'cart' ) { 
		global $woocommerce;

//var_dump( $woocommerce->cart->cart_contents ); die;
		// doing this messes up the cart total calculations when custom forms are used for events
		//$woocommerce->cart->get_cart_from_session(); 
//return;
		if ( sizeof( $woocommerce->cart->get_cart() ) <= 0 )
			return;

		$alert = array();

		$cart_page_id = woocommerce_get_page_id( 'cart' );

		$cart_url = get_permalink( $cart_page_id );

		$err_items = array();
		
		/**
			// Cart
			count quantities of the same master product into one sum
			check the master product for purchase limit restrictions
			
			// Past orders
			After that, get all orders for this customer based on email address or user ID
			Iterate the items in the purchase to find event items
			count quantities of the same master product into one sum
			check the master product for purchase limit restrictions
			
			SEE check_restriction() above which handles checking orders
		
		*/
		
		$ids_in_cart = array();

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {

			if ( 'yes' != get_post_meta( $values['product_id'], '_ignitewoo_event', true ) )
				continue;
				
			if ( !empty( $ids_in_cart[ $values['product_id'] ] ) )
				$ids_in_cart[ $values['product_id'] ]['quantity'] += $values['quantity'];
			else
				$ids_in_cart[ $values['product_id'] ] = array( 'quantity' => $values['quantity'], 'data' => $values );
			
		}


		foreach ( $ids_in_cart as $id => $vals ) { 

			$err = '';
			
			$values = $vals['data'];
			
			$values['quantity'] = $vals['quantity']; // Adjust to match total in cart across all variations

			$_product = $values['data'];
			
			if ( $_product->exists() && $values['quantity'] > 0 ) {

				$data = get_post_meta( $values['product_id'], '_ignitewoo_event_info', true );

				if ( empty( $data ) ) 
					continue;

				if ( empty( $data['max_ticket_restriction'] ) || 'yes' != $data['max_ticket_restriction'] ) 
					continue;

				if ( empty( $data['max_ticket_qty'] ) || 0 == absint( $data['max_ticket_qty'] ) ) 
					continue;

				if ( empty( $data['max_ticket_interval'] ) || 0 == absint( $data['max_ticket_interval'] ) ) 
					continue;

				if ( $data['max_ticket_qty'] > 1 ) 
					$interval = $data['max_ticket_interval_type'] . 's';
				else
					$interval = $data['max_ticket_interval_type']; 

				if ( $values['quantity'] > absint( $data['max_ticket_qty'] ) ) { 

					if ( !in_array( $values['product_id'], $err_items ) ) { 

						$err .= '<p>' . __( sprintf( 'The maximum number of tickets you may purchase for %s is', $_product->get_title() ), 'ignitewoo_events' ) . ' '; 
					
						$alert[] = $err . ' ' . $data['max_ticket_qty'] . ' ' .  __( 'every', 'ignitewoo_events' ) . ' ' . $data['max_ticket_interval'] . ' ' . __( $interval, 'ignitewoo_events' ) . '. '; //  . ' ' . __( 'for', 'ignitewoo_events' ) . ' ' . $_product->get_title();
						
						$err_items[] = $values['product_id'];
					}
					
				} else /* if ( 'cart' == $location ) */ { 

					$qty = $this->check_restriction( $data, $values['product_id'] ); 

					if ( $qty !== false && $qty > 0 )  {

						if ( !in_array( $values['product_id'], $err_items ) ) {	
							
							$err .= '<p>' . __( sprintf( 'The maximum number of tickets you may purchase for %s is', $_product->get_title() ), 'ignitewoo_events' ) . ' '; 
							
							$err .= ' ' . $data['max_ticket_qty'] . ' ' .  __( 'every', 'ignitewoo_events' ) . ' ' . $data['max_ticket_interval'] . ' ' . __( $interval, 'ignitewoo_events' )  . '. '; 
							
							$err .= __( 'You have already purchased', 'ignitewoo_events' ) . ' ' . $qty . '</p>';

							$alert[] = $err; 
							
							$err_items[] = $values['product_id'];
						}
					}

				}

			}

		}

		if ( count( $alert ) > 0 ) {

			if ( !empty( $_REQUEST['action'] ) && 'woocommerce-checkout' == $_REQUEST['action'] ) { 

				$err = '<p>' . __( sprintf( 'The number of tickets in your cart exceeds the purchase limit restrictions. Visit <a href="%s">your cart</a> for more information and to adjust quantities.', $cart_url ) , 'ignitewoo_events' ) . '</p>';
				
				//$err .= '<p>' . __( sprintf( 'The maximum number of tickets you may purchase for %s is', $_product->get_title() ), 'ignitewoo_events' ) . ' '; 

				$woocommerce->add_error( $err . implode( ';&nbsp; ' , $alert ) );

				return true; 

			}


			if ( 'cart' != $location && !isset( $_REQUEST['action'] ) ) { 
			
				$error = '<p>' . __( sprintf( 'The number of tickets in your cart exceeds the purchase limit restrictions. Visit <a href="%s">your cart</a> for more information and to adjust quantities.', $cart_url ) , 'ignitewoo_events' ) . '</p>';
			
				$error = '<ul class="woocommerce-error"><li>' . $error . implode( ';&nbsp; ' , $alert ) . '</li></ul>';
				
				echo $error;
				
				return true;
				
			}

			
			if ( 'cart' == $location ) { 

				echo '<div class="ticket_limit_warning_' . $location . '">';

				//echo sprintf( __( 'Note: The maximum number of tickets you may purchase for %s is', 'ignitewoo_events' ) . ' ', $_product->get_title() );

				echo implode( ';&nbsp; ' , $alert ); 


				//if ( 'cart' != $location ) 
					_e( 'You will not be able to complete the checkout process until you reduce the quantity.', 'ignitewoo_events' );

				echo '</div>';

			}

			return true; 

		}

		return false;

	}


	function expiration_helper( $id, $settings ) {
		global $wpdb, $post;

		// get all children
		$children = get_posts( 'post_parent=' . $id . '&post_type=product_variation&orderby=ID&order=ASC&fields=ids&post_status=any&numberposts=-1' );

		if ( !$children ) 
			return; 

		$total_expired = 0; 

		// get end date and check it
		foreach( $children as $child ) { 

			$end_date_stamp = get_post_meta( $child, 'end_date_stamp', true );

			if ( !$end_date_stamp )
				continue;

			if ( $end_date_stamp < current_time('timestamp' ) ) { 

				$total_expired++;

				$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $child ) );
			}

		}

		// are all expired? If so adjust parent product
		if ( $children && $total_expired == count( $children ) ) { 

			$wpdb->update( $wpdb->posts, array( 'post_status' => $settings['event_expiration'] ), array( 'ID' => $id ) );

			if ( 'trash' == $settings['event_expiration'] ) { 

				delete_post_meta( $id, '_ignitewoo_event_info' );
				delete_post_meta( $id, '_ignitewoo_event_end' );
				delete_post_meta( $id, '_ignitewoo_event_start' );

			}

		}

	}


	function get_events_for_template( $type, $id ) { 
		global $wpdb;

		$sql = 'select ID, post_title, post_content from ' . $wpdb->posts . ' left join ' . $wpdb->postmeta . ' on post_id = ID
			where post_type = "product" and post_status = "publish" 
			and ( meta_key = "' . $type . '" and FIND_IN_SET ( ' . $id . ', meta_value ) )';

		$res = $wpdb->get_results( $sql );

		return $res; 

	}


	function get_post_type_template( $single_template ) {
		global $post;

		if ( !in_array( $post->post_type, array( 'event_speaker', 'event_organizer', 'event_venue', 'event_sponsor' ) ) )
			return $single_template;

                $template_name = 'single-' . $post->post_type . '.php';

                $located = locate_template( $template_name );

                if ( $located )
			return $located;

		$single_template = dirname( __FILE__ ) . '/templates/post-types/single-' . $post->post_type . '.php';

		return $single_template;

	}


	function woocommerce_order_data_box() { 
		global $order; 

		$order = new WC_Order( $_GET['post'] );

		$tickets = array();

		// loop through items, find tickets, check quantity
		foreach( $order->get_items() as $item ) { 

			if ( 'yes' != get_post_meta( $item['product_id'], '_ignitewoo_event', true ) ) 
				continue;

			if ( absint( $item['qty'] ) <= 0 )
				continue; 

			$tickets[] = $item; 

		}

		if ( count( $tickets ) <= 0 ) 
			return;

		?>

		<script>

			jQuery( document ).ready( function() { 

				jQuery( ".checkin_button" ).click( function() { 

					if ( !confirm( "<?php _e( 'Perform check in?', 'ignitewoo_events' ) ?>" ) )
						return false; 

					var ticket = jQuery( this ).attr( "ticket_id" );
					var order_id = jQuery( this ).attr( "order_id" );
					var button = jQuery( this );

					var n = "<?php echo wp_create_nonce( 'event_checkin' ) ?>";

					jQuery.post( ajaxurl, { action: "ignitewoo_event_checkin", ticket_id: ticket, order_id: order_id, n: n }, function( data ) { 

						if ( "1" == data ) { 

							button.fadeOut( "slow" );

						}

					});


				});

			});

		</script>

		<div class="event-panel-wrap event_product_data">

			<?php 

			$settings = get_option( 'ignitewoo_events_main_settings', false );

			$sequence = 0; 

			?><table width="100%"><?php

			$order_meta = get_post_custom( $_GET['post'], true );

			foreach( $tickets as $t ) {

				for( $i = 0; $i < $t['qty']; $i++ ) { 
					$sequence++;
					$ticket_number = $order->id . '-' . $t['product_id'] . '-' . $sequence;
					echo '<tr>';
					echo '<td style="vertical-align:middle; padding: 10px; width:50%; border-bottom:1px solid #ccc">' . $ticket_number . '</td>';

					if ( empty( $order_meta[ $ticket_number ][0] ) ) 
						echo '<td style="vertical-align:middle; padding: 10px; border-bottom:1px solid #ccc"><input type="button" class="button checkin_button" name="button" value="Check In" ticket_id="' . $ticket_number. '" order_id="' . $_GET['post'] . '"></td>';
					else 
						echo '<td style="vertical-align:middle; padding: 10px; border-bottom:1px solid #ccc">' . __( 'Checked in', 'ignitewoo_events' ) . '<br/>' . date( $settings['date_format'] . ' - ' . $settings['time_format'], $order_meta[ $ticket_number ][0] ) . ' </td>';

					echo '</tr>';
				}

			}

			?>

			</table>
		</div>
		<?php

	}


	function ignitewoo_event_checkin() { 

		@ini_set( 'display_errors', 0 );

		if ( !wp_verify_nonce( $_POST['n'], 'event_checkin' ) )
			die;

		if ( absint( $_POST['order_id'] ) <= 0 || empty( $_POST['ticket_id'] ) )
			die;

		update_post_meta( $_POST['order_id'], $_POST['ticket_id'], time() );

		die( '1' );

	}


	function add_meta_links( $links, $file ) {

		$plugin_path = trailingslashit( dirname(__FILE__) );
                $plugin_dir = trailingslashit( basename( $plugin_path ) );

		if ( $file == $plugin_dir . 'wooevents-pro.php' ) {

			$links[]= '<a href="http://ignitewoo.com/contact-us"><strong>' . __( 'Support', 'ignitewoo_events' ) . '</strong></a>';
			$links[]= '<a href="http://ignitewoo.com">' . __( 'View Add-ons / Upgrades' ) . '</a>';
			$links[]= '<img style="height:24px;vertical-align:bottom;margin-left:12px" src="http://ignitewoo.com/wp-content/uploads/2012/02/ignitewoo-bar-black-bg-rounded2-300x86.png">';

		}
		return $links;
	}


	function add_plugin_action_links( $links ) {

		return array_merge(
			    array( 'settings' => '<a href="' . admin_url( 'admin.php?page=ignitewoo_events_settings' ) . '">Settings</a>' ),
			    $links 
			);

	}

	function gcal_link( $post_id = '', $start_date = '', $end_date = '' ) { 
		global $post;

		if ( !isset( $post_id ) ) 
			$post_id = $post->ID; 

		if ( !$post_id ) 
			return;

		if ( !isset( $start_date ) || !isset( $end_date ) ) 
			return;

		$start_date = strtotime( $start_date );

		$end_date = strtotime( $end_date );

		$dates = date( 'Ymd', $start_date  ) . 'T' . date( 'Hi00', $start_date ) . '/' . date( 'Ymd', $end_date ) . 'T' . date( 'Hi00', $end_date );

		$settings = get_post_meta( $post_id, '_ignitewoo_event_info', true );

		$venue_id = $settings['event_venue'][0];

		if ( !$venue_id ) 
			return;

		$meta = get_post_custom( $venue_id, true );

		if ( !isset( $meta['_generic_address'][0] ) )
			return false;

		$location = $meta['_generic_address'][0] . ' ';

		$location .= $meta['_generic_city'][0] . ' ';

		if ( '' != $meta['_generic_country_state'][0] ) { 

			$x = explode( ':', $meta['_generic_country_state'][0] );

			if ( count( $x ) > 1 ) 
				$location .= $x[1]. ', '; // state

			$location .= $x[0]; // country

		}

		$base_url = 'http://www.google.com/calendar/event';

		$event_details = get_the_content();

		if ( strlen( $event_details ) >= 996 )
			$event_details = substr( get_the_content(), 0, 996 ) . '...';

		$params = array(
			'action' => 'TEMPLATE',
			'text' => str_replace( ' ', '+', strip_tags( urlencode( $post->post_title ) ) ),
			'dates' => $dates,
			'details' => str_replace( ' ', '+', strip_tags( apply_filters( 'the_content', urlencode( $event_details ) ) ) ),
			'location' => str_replace( ' ', '+', urlencode( $location ) ),
			'sprop' => get_option( 'blogname' ),
			'trp' => 'false',
			'sprop' => 'website:' . home_url(),
		);

		$params = apply_filters( 'ignitewoo_google_calendar_parameters', $params );

		$url = add_query_arg( $params, $base_url );

		return esc_url( $url );

	}


	function ical_link( $post_id = '', $start_date = '', $end_date = '' ) { 
		global $post; 

		if ( !isset( $post_id ) ) 
			$post_id = $post->ID; 

		if ( !$post_id ) 
			return;

		$type = $post->post_type;
		
		$args = 'action=ignitewoo_ical_link&post=' . $post_id . '&s=' . $start_date . '&e=' . $end_date . '&t=' . $type . '&n=' . wp_create_nonce( 'ignitewoo_events_ical' );

		$url = get_bloginfo( 'url' ) . '/?'. $args; 

		return esc_url( $url );

	}


	function maybe_handle_ical_link() { 

		if ( !isset( $_GET['n'] ) || empty( $_GET['n'] ) || !isset( $_GET['e'] ) || empty( $_GET['e'] ) || !isset( $_GET['s'] ) || empty( $_GET['s'] ) || empty( $_GET['t'] ) )
			return;

		if ( !isset( $_GET['post'] ) || empty( $_GET['post'] ) )
			return;

		if ( !isset( $_GET['action'] ) || empty( $_GET['action'] ) || 'ignitewoo_ical_link' != $_GET['action'] )
			return;

		if ( !wp_verify_nonce( $_GET['n'], 'ignitewoo_events_ical' ) )
			return;

		$post_id = absint( $_GET['post'] );

		if ( !$post_id ) 
			return;

		$this->ical_export( $post_id, $_GET['s'], $_GET['e'] );

		die;

	}


	function ical_export( $post_id = '', $start_date = '', $end_date = '', $type = '' ) { 

		$tz_string = get_option( 'timezone_string' );

		$events = '';

		$siteurl = get_bloginfo( 'url' );

		$blogname = get_bloginfo( 'name' );

		/*
		if ( class_exists( 'Woocommerce' ) && class_exists( 'IgniteWoo_Events_Pro' ) )
			$type = 'product';
		else
			$type = 'ignitewoo_event';
			
		*/
		
		if ( $post_id ) {

			$events_posts = new WP_Query( array( 'p' => $post_id, 'post_status' => 'publish', 'post_type' => $type ) );

		} else {

			$query_args = array( 

				'posts_per_page' => -1, 
				'no_found_rows' => 1, 
				'post_status' => 'publish', 
				'post_type' => $type,
				'meta_key' => '_ignitewoo_event_start',
				'meta_query' => array( 
							'relation' => 'AND', 
							array(
								'key' => '_ignitewoo_event_start',
								'value' => '',
								'compare' => '!='
							),
							array(
								'key' => '_ignitewoo_event',
								'value' => 'yes',
								'compare' => '='
							),
						),
				'orderby' => 'meta_value',
				'order' => 'ASC'
			);

			$events_posts = new WP_Query( $query_args );
		}

		//foreach ( $events_posts as $events_post ) {

		if ( $events_posts->have_posts() ) while ( $events_posts->have_posts() ) {

			global $post;

			require_once( dirname( __FILE__ ) . '/woocommerce-events-rules.php' );

			$events_posts->the_post();

			$settings = get_post_meta( $post->ID, '_ignitewoo_event_info', true );


			if ( !$post_id ) { 

				if ( isset( $settings['recurrence'] ) && 'None' != $settings['recurrence']['type'] ) {

					$duration = get_post_meta( $post->ID, '_ignitewoo_event_duration', true );

					$start_date = get_post_meta( $post->ID, '_ignitewoo_event_start_initial', true );

					$end_date = date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, strtotime( $start_date ) + $duration );

				} else {

					$start_date = get_post_meta( $post->ID, '_ignitewoo_event_start', true );

					$end_date = get_post_meta( $post->ID, '_ignitewoo_event_end', true );

				}

			}

			$venue_id = $settings['event_venue'][0];

			if ( !$venue_id ) 
				return;

			$venue_meta = get_post_custom( $venue_id, true );

			$address = array();

			$address[] = $venue_meta['_generic_address'][0];
			$address[] = $venue_meta['_generic_city'][0];

			if ( '' != $venue_meta['_generic_country_state'][0] ) { 

				$x = explode( ':', $venue_meta['_generic_country_state'][0] );

				if ( count( $x ) > 1 ) 
					$address[] = $x[1]. ', '; // state

				$address[] = $x[0]; // country

			}

			$address[] = $venue_meta['_generic_postalcode'][0];

			$address = implode( ' ' , $address );

			// convert 2010-04-08 00:00:00 to 20100408T000000 or YYYYMMDDTHHMMSS

			$start_date = str_replace( array( '-', ' ', ':' ) , array( '', 'T', '' ) , $start_date );

			$end_date = str_replace( array( '-', ' ', ':' ) , array( '', 'T', '' ) , $end_date );

			$type = 'DATE-TIME';

			$description = get_the_content();

			$title = get_the_title();

			$link = get_permalink();

			$description = preg_replace( "/[\n\t\r]/", ' ', strip_tags( $description ) );

			$item = array();

			$item[] = "DTSTART;VALUE=$type:" . $start_date;

			$item[] = "DTEND;VALUE=$type:" . $end_date;

			$item[] = 'DTSTAMP:' . date( 'Ymd\THis', time() );

			$item[] = 'CREATED:' . str_replace( array( '-', ' ', ':' ) , array( '', 'T', '' ) , $post->post_date );

			$item[] = 'LAST-MODIFIED:' . str_replace( array( '-', ' ', ':' ) , array( '', 'T', '' ) , $post->post_modified );

			$item[] = 'UID:' . $post->ID . '-' . strtotime( $start_date ).'-'.strtotime( $end_date ) . '@' . $siteurl;

			$item[] = 'SUMMARY:' . $title;

			$item[] = 'DESCRIPTION:' . str_replace( ',', '\,', $description );

			$item[] = 'LOCATION:' . html_entity_decode( $address, ENT_QUOTES );

			$item[] = 'URL:' . $link;

			$item = apply_filters( 'ignitewoo_ical_feed_item', $item, $post );

			$events .= "BEGIN:VEVENT\n" . implode( "\n", $item ) . "\nEND:VEVENT\n";
		}

		wp_reset_postdata();

		header( 'Content-type: text/calendar' );
		header( 'Content-Disposition: attachment; filename="IgniteWoo_Events_Calendar.ics"' );

		$content = "BEGIN:VCALENDAR\n";
		$content .= "VERSION:2.0\n";
		$content .= 'PRODID:-//' . $blogname . ' - ECPv3.0' . "//NONSGML v1.0//EN\n";
		$content .= "CALSCALE:GREGORIAN\n";
		$content .= "METHOD:PUBLISH\n";
		$content .= 'X-WR-CALNAME:' . apply_filters( 'tribe_ical_feed_calname', $blogname ) . "\n";
		$content .= 'X-ORIGINAL-URL:' . $siteurl . "\n";
		$content .= 'X-WR-CALDESC:Events for ' . $blogname . "\n";

		if ( $tz_string ) $content .= 'X-WR-TIMEZONE:' . $tz_string . "\n";

		$content = apply_filters( 'tribe_ical_properties', $content );
		$content .= $events;
		$content .= 'END:VCALENDAR';

		echo $content;

		die;

	}
}

















































if ( ! function_exists( 'ignitewoo_queue_update' ) )
	require_once( dirname( __FILE__ ) . '/ignitewoo_updater/ignitewoo_update_api.php' );

$this_plugin_base = plugin_basename( __FILE__ );

add_action( "after_plugin_row_" . $this_plugin_base, 'ignite_plugin_update_row', 1, 2 );

ignitewoo_queue_update( plugin_basename( __FILE__ ), '05ddfe030ff6165110b41d639c93cbb9', '792' );



