<?php

if ( !defined('ABSPATH') )
	die();
	
class IgniteWoo_Events_Admin { 

	var $plugin_url = '';

	var $plugin_path = '';

	var $tab_index_start = 3000;


	function __construct() { 
		global $ignitewoo_events;
		
		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->plugin_path = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		add_action( 'init',  array( &$this, 'save_settings' ), 9999 );

		// must be higher than the init that adds the post type otherwise permalinks are not updated
		add_action( 'init', array( &$this, 'init' ), 99999995 ); 

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ), 999999 );

		add_action( 'admin_head', array( &$this, 'admin_head' ), 99999999 );

		add_action( 'admin_footer', array( &$this, 'admin_footer' ), 99999 );

		if ( !empty( $ignitewoo_events->settings['events_posting'] ) ) { 
		
			if ( class_exists( 'IgniteWoo_Events_Pro' ) && ( 'tickets_only' == $ignitewoo_events->settings['events_posting'] || 'events_and_tickets' == $ignitewoo_events->settings['events_posting'] ) )
				add_action( 'woocommerce_product_write_panels', array( &$this, 'event_data_panel' ), -1 );
			
			if ( 'events_only' == $ignitewoo_events->settings['events_posting'] || 'events_and_tickets' == $ignitewoo_events->settings['events_posting'] )
				add_action( 'add_meta_boxes', array( &$this, 'meta_box' ), 1 );
		} 
		else 
			add_action( 'add_meta_boxes', array( &$this, 'meta_box' ), 1 );
			
		add_filter( 'admin_menu', array( &$this, 'add_menu' ), -99, 1 );

		add_action( 'admin_print_styles', array( &$this, 'ignitewoo_admin_notices_styles' ) );

	}


	function init() { 

		add_action( 'save_post', array( &$this, 'save_post_data' ), 99999 ); // DO NOT CHANGE PRIORITY WITHOUT CHANGING remove_action calls
		
		add_action( 'delete_post', array( &$this, 'delete_post_data' ), 99999 ); // DO NOT CHANGE PRIORITY WITHOUT CHANGING remove_action calls


	}

	
	function meta_box() { 
		
		add_meta_box( 'ignitewoo-event-organizer', __( 'Event Details', 'ignitewoo_events' ), array( &$this, 'event_data_panel' ), 'ignitewoo_event', 'normal', 'high' );
		
	}

	function delete_post_data( $post_id = '' ) { 
		global $wpdb;
		
		$sql = 'delete from ' . $wpdb->posts . ' where post_type = "event_track_speaker" and post_parent = ' . $post_id;
		
		$wpdb->query( $sql );
	
	}
	
	
	function save_post_data( $post_id = '' ) { 
		global $wpdb, $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		if ( !isset( $post->post_type ) || ( 'ignitewoo_event' != $post->post_type && 'product' != $post->post_type ) )
			return;

		if ( empty( $post_id) || empty( $post) || empty( $_POST) ) 
			return;

		if ( is_int( wp_is_post_revision( $post ) ) ) 
			return;

		if ( is_int( wp_is_post_autosave( $post ) ) ) 
			return;

		if ( !current_user_can( 'edit_post', $post_id ) ) 
			return;

		
		// Delete post meta if this is not an event post because its existence is relied upon for expiration checking etc
		if ( !isset( $_POST['_ignitewoo_event'] ) || empty( $_POST['_ignitewoo_event'] ) ) { 
			delete_post_meta( $post->ID, '_ignitewoo_event' );
			delete_post_meta( $post->ID, '_ignitewoo_event_info' );
			delete_post_meta( $post->ID, '_ignitewoo_event_start' );
			delete_post_meta( $post->ID, '_ignitewoo_event_speakers' );
			delete_post_meta( $post->ID, '_ignitewoo_event_sponsors' );
			delete_post_meta( $post->ID, '_ignitewoo_event_organizers' );
			delete_post_meta( $post->ID, '_ignitewoo_event_venues' );
			return;
		}
		

		if ( !isset( $_POST['ignitewoo_event_info'] ) || empty( $_POST['ignitewoo_event_info'] ) )
			return;


		if ( !function_exists('sessions_cmp' ) ) {

			function sessions_cmp( $a, $b) {

				if ( $a['position'] == $b['position'] )
					return 0;

				return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}

		}

		update_post_meta( $post->ID, '_ignitewoo_event_info', $_POST['ignitewoo_event_info'] );

		if ( 'product' != $post->post_type  && !empty( $_POST['ignitewoo_event_info']['event_cost'] ) ) { 
			update_post_meta( $post->ID, '_price', $_POST['ignitewoo_event_info']['event_cost'] );
		}

		if ( !class_exists( 'IgniteWoo_Events_Pro' ) )
			update_post_meta( $post->ID, '_ignitewoo_event', 'yes' );
		else if ( isset( $_POST['_ignitewoo_event'] ) && !empty( $_POST['_ignitewoo_event'] ) )
			update_post_meta( $post->ID, '_ignitewoo_event', 'yes' );
		else 
			delete_post_meta( $post->ID, '_ignitewoo_event' );

		// Save fields as serialised array
		$form_fields = array();

		$field_name		= isset( $_POST['ignitewoo_event_info']['session_name']) ? $_POST['ignitewoo_event_info']['session_name'] : '';
		
		$field_datetime		= isset( $_POST['ignitewoo_event_info']['session_date_time'] ) ? $_POST['ignitewoo_event_info']['session_date_time'] : '';
		
		$field_description	= isset( $_POST['ignitewoo_event_info']['session_description'] ) ? $_POST['ignitewoo_event_info']['session_description'] : '';
	
		$field_organizer	= isset(  $_POST['ignitewoo_event_info']['session_organizer'] ) ? $_POST['ignitewoo_event_info']['session_organizer'] : '';
		
		$field_speaker_id	= isset(  $_POST['ignitewoo_event_info']['session_speaker_id'] ) ? $_POST['ignitewoo_event_info']['session_speaker_id'] : '';
		
		$field_speaker_desc	= isset( $_POST['ignitewoo_event_info']['session_speaker_desc'] ) ? $_POST['ignitewoo_event_info']['session_speaker_desc'] : '';
		
		$field_speaker_start	= isset( $_POST['ignitewoo_event_info']['session_speaker_start'] ) ? $_POST['ignitewoo_event_info']['session_speaker_start'] : '';
		
		$field_speaker_end	= isset(  $_POST['ignitewoo_event_info']['session_speaker_end'] ) ? $_POST['ignitewoo_event_info']['session_speaker_end'] : '';
		
		$field_sponsor		= isset( $_POST['ignitewoo_event_info']['session_sponsor'] ) ? $_POST['ignitewoo_event_info']['session_sponsor'] : '';
		
		$field_position 	= isset( $_POST['ignitewoo_event_info']['session_position'] ) ? $_POST['ignitewoo_event_info']['session_position'] : '';
		
		$field_required		= isset( $_POST['ignitewoo_event_info']['session_required'] ) ? $_POST['ignitewoo_event_info']['session_required'] : '';


		$sql = 'select ID from ' . $wpdb->posts . ' where post_parent = "' . $post->ID . '" and post_type = "event_track_speaker" ';

		$speaker_posts = $wpdb->get_results( $sql );

		if ( $speaker_posts )
			foreach( $speaker_posts as $sp ) 
				wp_delete_post( $sp->ID, true );


		for ( $i = 0; $i < sizeof( $field_name ); $i++ ) {
			
			if ( !isset( $field_name[$i] ) || ( '' == $field_name[$i] ) ) 
				continue;

			$speakers = array();

			$x = 0;

			if ( is_array( $field_speaker_id ) && count( $field_speaker_id[ $i ] ) > 0 ) 
			foreach( $field_speaker_id[ $i ] as $key => $s_field ) { 

				$new_speaker_post = array(
					'post_title' => get_the_title( $s_field ),
					'post_content' => $field_speaker_desc[ $i ][ $key ],
					'post_status' => 'publish',
					'post_author' => get_current_user_id(),
					'post_parent' =>  $post->ID,
					'post_type' => 'event_track_speaker',
					'menu_order' => $x
				);

				$new_speaker_post_id = wp_insert_post( $new_speaker_post );

				$speakers[] = array( 'post_id' => $new_speaker_post_id );

				update_post_meta( $new_speaker_post_id, 'speaker_id', $s_field );
				update_post_meta( $new_speaker_post_id, 'speaker_track', $i );
				update_post_meta( $new_speaker_post_id, 'speaker_position', $x );
				update_post_meta( $new_speaker_post_id, 'speaker_start', $field_speaker_start[ $i ][ $key ] );
				update_post_meta( $new_speaker_post_id, 'speaker_end', $field_speaker_end[ $i ][ $key ] );

				$x++;
			}

			// Add to array	 	
			$form_fields[] = array(
				'name' 		=> esc_attr( stripslashes( $field_name[$i] ) ),
				'datetime'	=>  esc_attr( stripslashes( $field_datetime[$i] ) ),
				'description' 	=> esc_attr( stripslashes( $field_description[$i] ) ),
				'organizer' 	=> esc_attr( stripslashes( $field_organizer[$i] ) ),
				'speaker' 	=> $speakers,
				'sponsor' 	=> esc_attr( stripslashes( $field_sponsor[$i] ) ),
				//'type' 		=> esc_attr( stripslashes( $field_type[$i] ) ),
				'position'	=> (int) $field_position[$i],
				'options' 	=> isset( $field_options ) ? $field_options : '',
				'required'	=> ( isset( $field_required[$i] ) ) ? 1 : 0
			);
		    
		}

		$organizer_ids = array();
		$sponsor_ids = array();
		$venue_ids = array();
		$speaker_ids = array();

		if ( isset( $_POST['ignitewoo_event_info']['session_speaker_id'] ) )
			$speaker_ids = $this->get_ids( $_POST['ignitewoo_event_info']['session_speaker_id'] );

		update_post_meta( $post->ID, '_speakers', $speaker_ids );

		if ( isset( $_POST['ignitewoo_event_info']['session_organizer'] ) )
			$organizer_ids = $this->get_ids( $_POST['ignitewoo_event_info']['session_organizer'] );

		update_post_meta( $post->ID, '_organizers', $organizer_ids );

		if ( isset( $_POST['ignitewoo_event_info']['session_sponsor'] ) )
			$sponsor_ids = $this->get_ids( $_POST['ignitewoo_event_info']['session_sponsor'] );

		update_post_meta( $post->ID, '_sponsors', $sponsor_ids );

		if ( isset( $_POST['ignitewoo_event_info']['event_venue'] ) )
			$venue_ids = $this->get_ids( $_POST['ignitewoo_event_info']['event_venue'] );

		update_post_meta( $post->ID, '_venues', $venue_ids ); 



		uasort( $form_fields, 'sessions_cmp' );


		update_post_meta( $post->ID, '_session_fields', $form_fields );

		// Save time frames seperately

		$start = $_POST['ignitewoo_event_info']['start_date'];

		$end = $_POST['ignitewoo_event_info']['end_date'];

		if ( '' != $start )
			$start = date( 'Y-m-d H:i:s', strtotime( $start ) );

		if ( '' != $end )
			$end = date( 'Y-m-d H:i:s', strtotime( $end ) );

		update_post_meta( $post->ID, '_ignitewoo_event_start', $start );

		update_post_meta( $post->ID, '_ignitewoo_event_end', $end );

		do_action( 'recurrence_save' );

	}


	function admin_scripts() { 
		global $woocommerce, $post, $typenow, $ignitewoo_events;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( 'product' != $typenow && 'ignitewoo_event' != $typenow ) 
			return;

		if ( !$post
			&& !in_array( $typenow, array( 'ignitewoo_event', 'event_organizer', 'event_venue', 'event_speaker', 'event_sponsor' ) ) 
			&& ( isset( $_GET['tab'] ) && 'ignitewoo_events' != $_GET['tab'] )
			&& ( isset( $_GET['page'] ) && 'ignitewoo_events_settings' != $_GET['page'] )
		    ) 
			return; 


		/*
		if ( !defined( WOOCOMMERCE_VERSION ) || version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			wp_deregister_script( 'jquery-tiptip' );
		
			wp_register_script( 'jquery-tiptip', $ignitewoo_events->plugin_url . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ) );

			wp_register_style( 'tiptip_css', $ignitewoo_events->plugin_url . '/assets/css/tiptip.css' );
		
			wp_deregister_script( 'chosen' );
		
			wp_register_script( 'chosen', $ignitewoo_events->plugin_url . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery' ), '1.0' );
			
		}
		*/

		if ( !wp_script_is( 'chosen', 'registered' ) ) {

			wp_register_script( 'chosen', $ignitewoo_events->plugin_url . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery' ), '1.0' );
			
			wp_register_style( 'ig_chosen_css', $ignitewoo_events->plugin_url . '/assets/css/chosen.css' );
			
		}
		
		if ( !wp_script_is( 'jquery-tiptip', 'registered' ) ) {
			
			wp_register_script( 'jquery-tiptip', $ignitewoo_events->plugin_url . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ) );

			wp_register_style( 'tiptip_css', $ignitewoo_events->plugin_url . '/assets/css/tiptip.css' );
		}
		
		wp_enqueue_script( 'jquery-tiptip' );	
		
		wp_enqueue_style( 'tiptip_css' );

		wp_enqueue_script( 'chosen' );

		wp_enqueue_style( 'ig_chosen_css' );

		wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ), '1.0' );

		wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery', 'jquery-ui-core' ), '1.0' );

		wp_enqueue_script( 'jquery-ui-slider', array( 'jquery', 'jquery-ui-core' ), '1.0' );

		wp_register_script( 'ig_datetimepicker', $ignitewoo_events->plugin_url . '/assets/js/datetimepicker/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker' ), '2.0' );

		wp_enqueue_script( 'ig_datetimepicker' );

		wp_register_script( 'ig_datetimepicker_slider', $ignitewoo_events->plugin_url . '/assets/js/datetimepicker/jquery-ui-sliderAccess.js', array( 'jquery', 'ig_datetimepicker', 'jquery-ui-datepicker' ), '2.0' );

		wp_enqueue_script( 'ig_datetimepicker_slider' );

		wp_enqueue_style( 'jquery-ui-style', ( is_ssl() ) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' : 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );

		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_register_style( 'ig_datetimepicker_css', $ignitewoo_events->plugin_url . '/assets/js/datetimepicker/jquery-ui-timepicker-addon.css' );

		wp_enqueue_style( 'ig_datetimepicker_css' );
		
		wp_register_style( 'ig_admin_css', $ignitewoo_events->plugin_url . '/assets/css/admin.css' );

		wp_enqueue_style( 'ig_admin_css' );

	}


	function admin_head() { 
		global $ignitewoo_events, $post;
		

		?>

		<style>
			#woocommerce-product-data ul.product_data_tabs li.ignitewoo_event_options a {
				<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) < 0 ) { ?> 
				background: url("<?php echo $ignitewoo_events->plugin_url ?>assets/images/wc-tab-icons.png") no-repeat scroll 9px -55px #F1F1F1;
				padding: 9px 9px 9px 34px;
				<?php } else { ?>
				padding: 10px;
				<?php } ?>
				color: #21759B;
				line-height: 16px;
				
				text-shadow: 0 1px 1px #FFFFFF;
			}
			.ignitewoo_event_tab.ignitewoo_event_options.active a {
				background-color: #F8F8F8 !important;
			}
			#ignitewoo_events_wrap #start_date, #ignitewoo_events_wrap #end_date { width: 150px; margin-right: 15px; }
			#ignitewoo_events_wrap .event_heading { margin-left: 12px; }
			#ignitewoo_events_main_settings .small { width: 65px; }
			#ignitewoo_events_wrap p:after {
				clear: both;
				content: ".";
				display: block;
				height: 0;
				visibility: hidden;
			}
			#ignitewoo_events_wrap p {
				margin: 9px 0;
				display: block;
				padding-left: 12px !important;
				padding-right: 20px !important;
				font-size: 12px;
				line-height: 24px;
				margin: 0 0 9px;
				padding: 5px 9px;
			}
			#ignitewoo_events_wrap label, #ignitewoo_events_wrap legend {
				float: left;
				padding: 0;
				width: 150px;
			}
			label {
				vertical-align: middle;
				cursor: pointer;
			}
			#ignitewoo_events_wrap p {
				font-size: 12px;
				line-height: 24px;
			}
			#ignitewoo_events_wrap .checkbox {
				margin: 7px 0;
				vertical-align: middle;
				width: auto;
			}
			#ignitewoo_events_wrap input {
				float: left;
				width: 50%;
			}
			#ignitewoo_events_wrap .event_form_speaker_add {
				width: auto !important;
			}
			#ignitewoo_events_wrap textarea, #ignitewoo_events_wrap input, #ignitewoo_events_wrap select {
				margin: 0;
			}
			.form-field input, .form-field textarea {
				border-style: solid;
				border-width: 1px;
				width: 9;
			}
			.chzn-container .chzn-results { 
				clear: both
			}
			.chzn-container-multi .chzn-choices .search-field input {
				background: none repeat scroll 0 0 transparent !important;
				border: 0 none !important;
				box-shadow: none;
				color: #999999;
				font-family: sans-serif;
				font-size: 100%;
				height: 15px;
				margin: 1px 0;
				outline: 0 none;
				padding: 5px;
			}
			.woocommerce_options_panel .chzn-container-multi .search-field input {
				min-width: 100%;
			}
			.chzn-container-multi .chzn-choices .search-field input {
				height: 21px !important;
			}
			/* Tooltips */
			.tips {
				cursor: help;
				text-decoration: none;
			}
			img.tips {
				padding: 5px 0 0 0;
			}
			<?php if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) >= 0 ) { ?> 
			#ignitewoo_event_product_data label {
				margin-left: 0px
			}
			<?php } ?>
		</style>
		
		<?php 
			if ( !empty( $post->post_type ) && in_array( $post->post_type, array( 'event_organizer', 'event_venue', 'event_sponsor', 'event_speaker', 'ignitewoo_event' ) ) ) { ?>
		<script>
		jQuery( document ).ready( function( $ ) { 

			jQuery( ".chosen" ).chosen();
			
			// Tooltips
			jQuery(".tips, .help_tip").tipTip({
				'attribute' : 'data-tip',
				'fadeIn' : 50,
				'fadeOut' : 50,
				'delay' : 200
			});


		});
		</script>
		<?php } ?>
		<?php
	}


	function admin_footer() { 
		global $post;
		?>
		<script>

		jQuery( document ).ready( function() { 

			function ignitewoo_event_type_change() { 

				if ( "checked" == jQuery( "#_ignitewoo_event" ).attr( "checked" ) )  {

					jQuery( ".ignitewoo_event_tab" ).show();

					jQuery( "input#_manage_stock" ).change();

				} else { 

					jQuery( ".ignitewoo_event_tab" ).hide();

				}

			}

			ignitewoo_event_type_change(); 

			jQuery( "#_ignitewoo_event" ).change( function() { 

				ignitewoo_event_type_change();

			});

			<?php if ( !empty( $post ) && isset( $post ) && 'ignitewoo_event' == $post->post_type || 'product' == $post->post_type ) { ?>

				jQuery( ".recur_date").datepicker();

				jQuery( ".event_datepicker" ).datetimepicker({
					dateFormat: "mm/dd/yy",
					ampm: true,
					addSliderAccess: true, 
					sliderAccessArgs: { touchonly: false }
				});

			<?php } ?>

			<?php if ( ( isset( $_GET['tab'] ) && 'ignitewoo_events' == $_GET['tab'] ) || ( isset( $_GET['page'] ) && 'ignitewoo_events_settings' == $_GET['page'] ) ) { ?>

				jQuery( ".all_day_times" ).datetimepicker({
					ampm: true,
					timeOnly: true
				});

			<?php } ?>

		});
		</script>
		<?php 
	}
	

	public function event_data_panel() {
		global $post;

		?>
		<div id="ignitewoo_event_product_data" class="panel woocommerce_options_panel">

			<?php do_action( 'before_ignitewoo_events_metabox' ) ?>
		
			<?php require_once( dirname( __FILE__ ) . '/events-meta-box.php' ); ?>

			<?php do_action( 'after_ignitewoo_events_metabox' ) ?>
			
			<div class="clear"></div>
		</div>
		<?php
	}


	function add_menu( $tabs = '' ) { 

		add_menu_page( __( 'WooEvents', 'ignitewoo_events' ), __( 'WooEvents™', 'ignitewoo_events' ), 'publish_posts', 'ignitewoo_events_settings', array( &$this, 'events_settings' ), null, 54 );

		add_submenu_page( 'ignitewoo_events_settings', __( 'Settings', 'ignitewoo_events' ), __( 'Settings', 'ignitewoo_events' ), 'publish_posts', 'ignitewoo_events_settings', array( &$this, 'events_settings' ) );


	}


	function events_settings() { 

		require_once( dirname( __FILE__ ) . '/events-settings.php' );

	}


	function save_settings() { 
		global $wp_rewrite;

		if ( !isset( $_POST ) || !isset( $_POST['_wpnonce'] ) )
			return;

		if ( !wp_verify_nonce( $_POST['_wpnonce'], 'ignitewoo_event_settings_save' ) )
			return;

		if ( !isset( $_POST['ignitewoo_event_settings'] ) || empty( $_POST['ignitewoo_event_settings'] ) )
			return;

		if ( !isset( $_POST['ignitewoo_event_settings']['maps_zoom'] ) || empty( $_POST['ignitewoo_event_settings']['maps_zoom'] ) )
			 $_POST['ignitewoo_event_settings']['maps_zoom'] = 15;

		else if ( intval( $_POST['ignitewoo_event_settings']['maps_zoom'] ) > 21 ) 
			$_POST['ignitewoo_event_settings']['maps_zoom'] = 21;

		else if ( intval( $_POST['ignitewoo_event_settings']['maps_zoom'] ) < 0 ) 
			$_POST['ignitewoo_event_settings']['maps_zoom'] = 0;

		if ( !isset( $_POST['ignitewoo_event_settings']['venue_slug'] ) || empty( $_POST['ignitewoo_event_settings']['events_slug'] ) )
			$_POST['ignitewoo_event_settings']['events_slug'] = 'events';
			
		if ( !isset( $_POST['ignitewoo_event_settings']['venue_slug'] ) || empty( $_POST['ignitewoo_event_settings']['venue_slug'] ) )
			$_POST['ignitewoo_event_settings']['venue_slug'] = 'event-venues';

		if ( !isset( $_POST['ignitewoo_event_settings']['organizer_slug'] ) || empty( $_POST['ignitewoo_event_settings']['organizer_slug'] ) )
			$_POST['ignitewoo_event_settings']['organizer_slug'] = 'event-organizers';

		if ( !isset( $_POST['ignitewoo_event_settings']['sponsor_slug'] ) || empty( $_POST['ignitewoo_event_settings']['sponsor_slug'] ) )
			$_POST['ignitewoo_event_settings']['sponsor_slug'] = 'event-sponsors';

		if ( !isset( $_POST['ignitewoo_event_settings']['speaker_slug'] ) || empty( $_POST['ignitewoo_event_settings']['speaker_slug'] ) )
			$_POST['ignitewoo_event_settings']['speaker_slug'] = 'event-speakers';

		update_option( 'ignitewoo_events_main_settings', $_POST['ignitewoo_event_settings'] );

		$wp_rewrite->flush_rules();
		
		update_option( 'ignitewoo_events_installed', 1 );
		
		wp_redirect( admin_url( 'admin.php?page=ignitewoo_events_settings' ) );

		die;

	}


	function ignitewoo_admin_install_notice() {
		?>
		<div id="message" class="updated ignitewoo-message wc-connect">
			<div class="squeezer">
				<p class="admin_event_notice_wrap"><?php _e( '<strong>Welcome to Events Calendar & Ticketing</strong> &#8211; You\'re almost ready to go :)', 'ignitewoo_events' ); ?>
				    <a href="<?php echo admin_url('admin.php?page=ignitewoo_events_settings'); ?>" class="button"><?php _e( 'Take me to Settings', 'ignitewoo_events' ); ?></a> 
				    <a href="http://ignitewoo.com/shop/wordpress-event-calendar-wooevents-pro-for-woocommerce" target="_blank" class="button"><?php _e( 'WooEvents&trade; Pro', 'ignitewoo_events' ); ?></a> 
				    <?php /* <a class="skip button" href="<?php echo add_query_arg('skip_install_woocommerce_pages', 'true', admin_url('admin.php?page=woocommerce_settings')); ?>"><?php _e('Skip setup', 'ignitewoo_events'); ?></a> */ ?>
				    <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://ignitewoo.com" data-text="Awesome #event #calendar #WordPress and #WooCommerce. Sell tickets too!" data-via="IgniteWoo" data-size="large" data-hashtags="IgniteWoo">Tweet</a>
				    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</p>
			</div>
		</div>
		<?php
	}

	function ignitewoo_admin_notices_styles() {

		if ( !get_option('ignitewoo_events_installed') ) {

			wp_enqueue_style( 'ignitewoo-activation', $this->plugin_url . '/assets/css/activation.css' );

			add_action( 'admin_notices', array( &$this, 'ignitewoo_admin_install_notice' ) );

		}

		//update_option( 'ignitewoo_events_installed', 1 );
		//delete_option( 'ignitewoo_events_installed');

	}


	function get_ids( $a = array() ) { 

		// test for multidimensional array
		$multi = array_filter( $a, 'is_array' );

		if ( count( $multi ) <= 0 ) 
			return implode( ',' , array_unique( $a ) );

		$ids = array();

		$x = call_user_func_array( 'array_merge',  $a );

		foreach( $x as $k => $v ) 
			$ids[] = $v;

		$ids = implode( ',' , array_unique( $ids ) );

		return $ids; 

	}

	
	function tab_index() {
	
		echo $this->tab_index_start;
		
		$this->tab_index_start++;
	}

}
