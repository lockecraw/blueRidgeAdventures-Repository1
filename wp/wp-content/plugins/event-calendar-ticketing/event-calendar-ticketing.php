<?php
/*
Plugin Name: Event Calendar & Ticketing
Plugin URI: http://ignitewoo.com
Description: Full featured super-powered event calendar and ticketing management system. 
Author: IgniteWoo.com
Version: 2.2.34
Author URI: http://ignitewoo.com
License: GNU AGPLv3 
License URI: http://www.gnu.org/licenses/agpl-3.0.html
*/

/** 

    LICENSE: GNU AGPLv3

    Copyright (c) 2012 - IgniteWoo.com - ALL RIGHTS RESERVED 

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. You use this 
    software at your own risk. See the GNU Affero General Public License 
    for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/


--------------------------------------------------------------------

*/


if ( !defined('ABSPATH') )
	die();

class IgniteWoo_Events { 

	var $plugin_url; 

	var $taxonomies; 

	var $post_data = '';

	function __construct() { 

		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->plugin_path = WP_PLUGIN_DIR . '/' . str_replace( basename( __FILE__ ), '' , plugin_basename( __FILE__ ) );

		$this->settings = get_option( 'ignitewoo_events_main_settings', false ); 

		add_action( 'init', array( &$this, 'load_plugin_textdomain' ) );
		
		add_action( 'init', array( &$this, 'is_ssl' ) );
		
		// Runs every page load for maximum accuracy in expiration detection - cron would not provide the same accuracy
		add_action( 'init', array( &$this, 'maybe_unpublish_expired' ), 99999991 ); // DO NOT CHANGE PRIORITY - Depends on running AFTER CPTs are created
		
		add_action( 'wp', array( &$this, 'wp' ) );
		
		add_filter( 'the_posts', array( &$this, 'maybe_add_calendar_scripts_and_styles'  ), 1, 1 );

		add_filter( 'the_content', array( &$this, 'the_content' ), 5, 1 );
		
		add_shortcode( 'event_details', array( &$this, 'shortcode_processor' ), 5, 1 );

		add_action( 'wp_ajax_ignitewoo_get_events', array( &$this, 'ignitewoo_get_events' ) );

		add_action( 'wp_ajax_nopriv_ignitewoo_get_events', array( &$this, 'ignitewoo_get_events' ) );

		add_action( 'woocommerce_ignitewoo_event_add_to_cart', 'woocommerce_simple_add_to_cart' );
		
		add_action( 'widgets_init', array( &$this, 'ignitewoo_events_register_widgets' ), -99 );

		add_action( 'plugin_row_meta', array( &$this, 'add_meta_links' ), 10, 2 );

		add_shortcode( 'ignitewoo_events_calendar', array( &$this, 'events_calendar' ) );
		add_shortcode( 'events_calendar', array( &$this, 'events_calendar' ) );
		
		add_action( 'ignitewoo_events_map', array( &$this, 'google_map' ), 20 );

	}


	function load_plugin_textdomain() {
	
		$locale = apply_filters( 'plugin_locale', get_locale(), 'ignitewoo_events' );

		// Allow upgrade safe, site specific language files in /wp-content/languages/woocommerce-subscriptions/
		load_textdomain( 'ignitewoo_events', WP_LANG_DIR . '/ignitewoo_events-'.$locale.'.mo' );

		$plugin_rel_path = apply_filters( 'ignitewoo_translation_file_rel_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		load_plugin_textdomain( 'ignitewoo_events', false, $plugin_rel_path );

	}
	
	
	function is_ssl() { 
	
		if ( is_ssl() ) 
			$this->plugin_url = str_replace( 'http://', 'https://', $this->plugin_url );
	
	}
	
	
	function wp() { 
		global $post;
		
		if ( !empty( $post->ID ) && 'yes' == get_post_meta( $post->ID, '_ignitewoo_event', true ) )
				add_filter( 'body_class', array( &$this, 'event_class_names' ) );
				
	}
	
	
	function event_class_names($classes) {

		$classes[] = 'ignitewoo_event';
		
		return $classes;
	}
	
	
	// Somewhat hacky, but how else we gonna get this done so that scripts and css don't load unless required?
	// This looks for the shortcode and queues when found.
	function maybe_add_calendar_scripts_and_styles( $posts ) {

		if ( empty( $posts ) ) 
			return $posts;
	
		$got_it = false; 

		foreach ( $posts as $post ) {

			if ( false !== stripos( $post->post_content, '[ignitewoo_events_calendar' ) ) { 

				$got_it = true;
				
				break;

			}

		}

		if ( $got_it || is_active_widget( false, false, 'ignitewoo_mini_cal', true ) || is_active_widget( false, false, 'ignitewoo_upcoming_events', true ) ) {

			wp_register_style( 'ig_calendar_css', $this->plugin_url . '/assets/js/calendar/fullcalendar/fullcalendar.css' );

			wp_enqueue_style( 'ig_calendar_css' );

			wp_register_style( 'ig_calendar_css', $this->plugin_url . '/assets/js/calendar/fullcalendar/fullcalendar.print.css' );

			wp_enqueue_style( 'ig_calendar_css' );

			wp_register_script( 'ig_calendar', $this->plugin_url . '/assets/js/calendar/fullcalendar/fullcalendar.min.js', array( 'jquery' ), '2.0' );

			wp_enqueue_script( 'ig_calendar' );

			
			wp_register_style( 'ig_qtip_css', $this->plugin_url . '/assets/css/jquery.qtip.min.css' );

			wp_enqueue_style( 'ig_qtip_css' );

			wp_register_script( 'ig_qtip', $this->plugin_url . '/assets/js/jquery-qtip/jquery.qtip.min.js', array( 'jquery' ), '2.0' );

			wp_enqueue_script( 'ig_qtip' );


		}

		$css = file_exists( get_stylesheet_directory() . '/ignitewoo_events/ignitewoo_events.css' ) ? get_stylesheet_directory_uri() . '/ignitewoo_events/ignitewoo_events.css' : $this->plugin_url . '/assets/css/ignitewoo_events.css';

		wp_enqueue_style( 'ignitewoo_events_frontend_styles', $css );

		$mini_cal_css = file_exists( get_stylesheet_directory() . '/ignitewoo_events/widgets.css' ) ? get_stylesheet_directory_uri() . '/ignitewoo_events/widgets.css' : $this->plugin_url . '/assets/css/widgets.css';

		wp_enqueue_style( 'ignitewoo_events_mini_cal_style', $mini_cal_css );

		
		return $posts;
	}


	function get_post_data( $product_id = null ) { 
		global $post;

		if ( !empty( $product_id ) && empty( $this->post_data ) ) 
			$this->post_data = get_post_meta( $product_id, '_ignitewoo_event_info', true );
		else if ( empty( $this->post_data ) && !empty( $post ) )
			$this->post_data = get_post_meta( $post->ID, '_ignitewoo_event_info', true );

		return $this->post_data; 

	}


	function get_event_settings() { 

		if ( !isset( $this->main_event_settings ) )
			$this->main_event_settings = get_option( 'ignitewoo_events_main_settings', false );

		if ( !$this->main_event_settings ) { 

			$s['default_country_state'] = get_option( 'woocommerce_default_country', false );
			$s['all_day_start'] = '9:00 am';
			$s['all_day_end'] = '5:00 pm';
			$s['maps_width'] = '100%';
			$s['maps_height'] = '400';
			$s['maps_zoom'] = 11;
			$s['venue_slug'] = 'event-venues';
			$s['organizer_slug'] = 'event-organizers';
			$s['sponsor_slug'] = 'event-sponsors';
			$s['speaker_slug'] = 'event-speakers';
			$s['event_expiration'] = 'draft';
			$s['date_format'] = 'M d, Y';
			$s['time_format'] = 'h:i a';
			$s['tooltip_color'] = 'tipped';
			$s['event_fg_color'] = '#ffffff';
			$s['event_bg_color'] = '#3366cc';
			$s['calendar_start_day'] = 0;
			$s['currency_symbol'] = '$';
			$s['symbol_location'] = 'left';

			update_option( 'ignitewoo_events_main_settings', $s );

		}

		return $this->main_event_settings;

	}


	function events_settings() { 
		global $woocommerce;

		require_once( dirname( __FILE__ ) . '/includes/events-settings.php'  );

	}

	
	// Handles event expiration for this plugin and WooEvents Pro plugin where events are products
	function maybe_unpublish_expired() { 

		$this->settings = $this->get_event_settings();
		
		if ( !isset( $this->settings['event_expiration'] ) || 'none' == $this->settings['event_expiration' ] ) 
			return;
			
		$this->process_expired( 'ignitewoo_event' );
		
		if ( class_exists( 'Woocommerce' ) )
			$this->process_expired( 'product' );
			
	}
	
	
	function process_expired( $post_type ) { 
		global $post, $wpdb;
		
		// Exclude post if someone is editing a post - handle edit redirects too
		if ( !empty( $_POST['action'] ) && 'editpost' == $_POST['action'] && !empty( $_POST['post_ID'] ) )
			$exclude = ' and ID not in ('. $_POST['post_ID'] . ')';
			
		else if ( false !== strpos( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) && !empty( $_GET['action'] ) && !empty( $_GET['post'] ) )
			$exclude = ' and ID not in ('. $_GET['post'] . ')';
		else
			$exclude = '';

		$sql = 'select ID from ' . $wpdb->posts . ' left join ' . $wpdb->postmeta . ' on post_id = ID 
			where 
			( meta_key = "_ignitewoo_event" and meta_value = "yes" )
			AND post_type = "'. $post_type . '" AND post_status = "publish" ' . $exclude;

		$res = $wpdb->get_results( $sql );

		if ( !$res ) 
			return;

		foreach( $res as $r ) { 

			if ( 'yes' !== get_post_meta( $r->ID, '_ignitewoo_event', true ) )
				continue;

			$info = get_post_meta( $r->ID, '_ignitewoo_event_info', true );

			if ( class_exists( 'Woocommerce' ) )
				$type = wp_get_object_terms( $r->ID, 'product_type' );
			else 
				$type = array( 'null' => 'null' );

			if ( !is_wp_error( $type ) && isset( $type[0] ) && 'variable' == $type[0]->slug ) {

				// Product has variations, but none are for recurring events
				if ( isset( $info['recurrence']['type'] ) && 'none' == strtolower( $info['recurrence']['type'] ) ) { 

					$end_date = get_post_meta( $r->ID, '_ignitewoo_event_end', true );

					if ( !$end_date || strtotime( $end_date ) > current_time( 'timestamp', false ) )
						continue;

					remove_action( 'woocommerce_ignitewoo_event_add_to_cart', 'woocommerce_simple_add_to_cart' );

					$wpdb->update( $wpdb->posts, array( 'post_status' => $this->settings['event_expiration'] ), array( 'ID' => $r->ID ) );

					if ( 'trash' == $this->settings['event_expiration'] ) { 

						delete_post_meta( $r->ID, '_ignitewoo_event_info' );
						delete_post_meta( $r->ID, '_ignitewoo_event_end' );
						delete_post_meta( $r->ID, '_ignitewoo_event_start' );

					}

					continue;

				}

				do_action( 'ignitewoo_events_pro_expiration', $r->ID, $this->settings ); 


			} else { 

				// get end date and check it
				$end_date = get_post_meta( $r->ID, '_ignitewoo_event_end', true );

				if ( !$end_date || strtotime( $end_date ) > current_time( 'timestamp', false ) )
					continue;

				remove_action( 'woocommerce_ignitewoo_event_add_to_cart', 'woocommerce_simple_add_to_cart' );

				$wpdb->update( $wpdb->posts, array( 'post_status' => $this->settings['event_expiration'] ), array( 'ID' => $r->ID ) );

				if ( 'trash' == $this->settings['event_expiration'] ) { 

					delete_post_meta( $r->ID, '_ignitewoo_event_info' );
					delete_post_meta( $r->ID, '_ignitewoo_event_end' );
					delete_post_meta( $r->ID, '_ignitewoo_event_start' );

				}
			}

		}

	}
	

	function shortcode_processor( $attr = array() ) { 

		ob_start();

		$out = $this->gen_event_info( '', false );
		
		$out = ob_get_clean();
		
		return $out;

	}
	
	
	function the_content( $content ) { 
		global $post;

		if ( isset( $this->settings['use_shortcode'] ) && 'yes' == $this->settings['use_shortcode'] )
			return $content;
		
		if ( !is_main_query() )
			return $content;
			
		if ( function_exists( 'woocommerce_get_page_id' ) ) { 
		
			$shop_page = woocommerce_get_page_id( 'shop' );
			
			if ( is_page( $shop_page ) )
				return $content;
				
			if ( is_product_category() || is_product_tag() )
				return $content;

		}
		
		if ( class_exists( 'IgniteWoo_Events_Pro' ) && ( 'ignitewoo_event' != $post->post_type  && 'product' != $post->post_type ) ) { 

			return $content;
		
		} else if ( 'ignitewoo_event' != $post->post_type && 'product' != $post->post_type ) {
		
			return $content;

		}

		if ( 'yes' != get_post_meta( $post->ID, '_ignitewoo_event', true ) )
			return $content;
			
		
		return $this->gen_event_info( $content );
		
	}
	
	
	function gen_event_info( $content = null, $do_shortcode = true ) { 
		global $post;

		$data = $this->get_post_data();

		$sessions = get_post_meta( $post->ID, '_session_fields', true );

		if ( !$data )
			return $content;
		
		if ( isset( $data['event_venue'] ) )
			$venues = $data['event_venue']; 
		else
			$venues = array( 'xxx' );

		//if ( isset( $venues ) && is_array( $venues ) )
			$venues = new WP_Query( array( 'post_type' => 'event_venue', 'post_status' => 'publish', 'post__in' => $venues, 'posts_per_page' => 999999 ) );

		if ( !empty( $data['event_primary_organizer'] ) )
			$primary_organizers = $data['event_primary_organizer'];
		else
			$primary_organizers = array( 'xxx' );

		//if ( isset( $primary_organizers ) && is_array( $primary_organizers ) && count( $primary_organizers ) > 0 )
			$primary_organizers = new WP_Query( array( 'post_type' => 'event_organizer', 'post_status' => 'publish', 'post__in' => $primary_organizers, 'posts_per_page' => 999999 ) );


		if ( isset( $data['event_primary_sponsor'] ) )
			$primary_sponsors = $data['event_primary_sponsor'];
		else
			$primary_sponsors = array( 'xxx' );

		//if ( isset( $primary_sponsors ) && is_array( $primary_sponsors ) && count( $primary_sponsors ) > 0 )
			$primary_sponsors = new WP_Query( array( 'post_type' => 'event_sponsor', 'post_status' => 'publish', 'post__in' => $primary_sponsors, 'posts_per_page' => 999999 ) );


		if ( class_exists( 'IgniteWoo_Events_Pro' ) && $sessions && is_array( $sessions ) )
			for( $i = 0, $b = count( $sessions ); $i < $b; $i++ ) {
				
				if ( isset( $sessions[$i]['speaker'] ) && is_array( $sessions[$i]['speaker'] ) && count( $sessions[$i]['speaker'] ) > 0 ) { 

					$speakers = array();

					foreach( $sessions[$i]['speaker'] as $s )
						$speakers[] = $s['post_id']; 

					$speaker_data = new WP_Query( array( 'post_type' => 'event_track_speaker', 'post_status' => 'publish', 'post_parent' => $post->ID, 'post__in' => $speakers, 'posts_per_page' => 999999 ) );

					if ( $speaker_data->have_posts() ) {

						while( $speaker_data->have_posts() ) {

							$speaker_data->the_post(); 

							$meta = get_post_custom( $post->ID, true );

							$sessions[$i]['speaker_data'][] = array(
											    'id' => $meta['speaker_id'][0],
											    'start' => $meta['speaker_start'][0],
											    'end' => $meta['speaker_end'][0],
											    'desc' => get_the_content( $post->ID )
											);
						}

						wp_reset_postdata();

					}

					unset( $meta );
					unset( $speaker_data );

				}

			}

		
		if ( !class_exists( 'IgniteWoo_Events_Pro' ) && 'ignitewoo_event' == $post->post_type ) { 
		
			$speakers = get_post_meta( $post->ID, '_speakers', true );

			if ( $speakers ) { 

				$speakers = explode( ',' , $speakers );

				foreach( $speakers as $k => $s )
					$sessions[0]['speaker_data'][] = array(
						    'id' => $s,
						    'start' => '',
						    'end' => '', 
						    'desc' => $this->ignitewoo_the_excerpt( get_the_content( $s ), 500 ), 
						);
				}
		} 

		$template = locate_template( array( 'ignitewoo_events/ignitewoo-event-details-template.php' ), false, false );

		if ( '' != $template ) 
			require_once ( $template );
		else 
			require_once( dirname( __FILE__ ) . '/templates/ignitewoo-event-details-template.php' );

		// False when we're using this function to retrieve content for shortcode processing
		if ( !$do_shortcode ) 
			return;
			
		return do_shortcode( $content );

	}


	function google_map() { 

		require_once( dirname( __FILE__ ) . '/includes/events-map.php' );
		
		require_once( dirname( __FILE__ ) . '/includes/events-map-footer.php' );

		add_action( 'wp_footer', array( &$this, 'map_footer_script' ) );
	}


	function map_footer_script() {  
		gmap_footer();
	}
	
	
	// This function obscures email addresses on the public side of the site ( venue contact info, etc ) 
	// to help prevent spammers from scraping those email addresses.
	// The visitor's browser JS engine decodes the addresses so they become visible.
	// So, only spammers that scrape with a JS engine in place can get the addresses.
	function obscure_email_address( $email ) { 

		$character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; 

		$key = str_shuffle( $character_set ); 

		$cipher_text = ''; 

		$id = 'e'.rand( 1,999999999 ); 

		for ( $i=0; $i < strlen( $email ); $i+=1 ) 
			$cipher_text.= $key[ strpos( $character_set,$email[$i] ) ]; 

		$script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; 

		$script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e) ));'; 

		$script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; 

		$script = "eval(\"" . str_replace( array( "\\",'"' ),array( "\\\\", '\"' ), $script ) . "\")"; 

		$script = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>'; 

		return '<span class="email" id="'. $id . '">[javascript protected email address]</span>' . $script;
	}


	function events_calendar( $attr = array() ) { 

		extract( shortcode_atts( array(
			'type' => 'both',
			'cat' => 'all'
		), $attr ) );

		ob_start();

		$this->render_calendar( $type, $cat ); 

		$out = ob_get_contents();

		ob_end_clean();

		return $out;

	}


	function ignitewoo_the_excerpt( $excerpt, $charlength ) {

		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {

			$subex = mb_substr( $excerpt, 0, $charlength - 5 );

			$exwords = explode( ' ', $subex );

			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );

			if ( $excut < 0 )
				$out = mb_substr( $subex, 0, $excut );
			else
				$out = $subex;

			$out .= '[...]';

		} else {

			$out = $excerpt;

		}

		return $out;

	}


	function ignitewoo_get_events() { 
		global $wpdb, $post; 

		// So that other plugins don't break our output here with their own error messages being inserted 
		@ini_set( 'display_errors', false );
		
		if ( !isset( $_POST['start'] ) || !isset( $_POST['end'] )  || !isset( $_POST['n'] ) )
			die;

		// Use a start date of 3 months ago - this may need to be changed later. Depends on use cases
		if ( isset( $_POST['start'] ) ) { 
			$start = date( 'Y-m-d H:i:s', strtotime( '-3 month', $_POST['start'] ) );

		}

		if ( isset( $_POST['end'] ) ) { 
			$end = date( 'Y-m-d H:i:s', $_POST['end']);
		}

		if ( !$start && !$end ) 
			die;
			
		if ( !empty( $_POST['type'] ) ) 
			$type = $_POST['type'];
		else 
			$type = null;
			
		$join = '';
		
		$where = '';
		
		if ( ( !empty( $type ) && 'tickets' == $type ) && ( !empty( $_POST['cat'] ) && 'all' != $_POST['cat'] ) ) {

			$join = "LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
			LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
			LEFT JOIN {$wpdb->terms} AS term USING( term_id )";

			$cat = esc_attr( $_POST['cat'] );

			$where = "AND tax.taxonomy = 'product_cat'
			AND term.slug = '{$cat}'";
		
		}

		if ( class_exists( 'IgniteWoo_Events_Pro' ) && 'tickets' == $type )
			$types = 'AND ( post_type = "product" )';
		else if ( !class_exists( 'IgniteWoo_Events_Pro' ) && ( 'tickets' == $type || 'both' == $type ) )
			$types = 'AND ( post_type = "ignitewoo_event" )';
		else if ( 'events' == $type )
			$types = 'AND ( post_type = "ignitewoo_event" )';

		$sql = ' 
			SELECT distinct ID, post_content, post_title, m1.meta_value as start_date, m2.meta_value as end_date, m3.meta_value as settings
			FROM `' . $wpdb->posts . '` posts 
			left join `' . $wpdb->postmeta . '` m1 on ID = m1.post_id 
			left join `' . $wpdb->postmeta . '` m2 on ID = m2.post_id 
			left join `' . $wpdb->postmeta . '` m3 on ID = m3.post_id 
			left join `' . $wpdb->postmeta . '` m4 on ID = m4.post_id 
			'. $join . '
			WHERE 
			( m1.meta_key = "_ignitewoo_event_start" and m1.meta_value >= "' . $start . '" )
			AND ( m2.meta_key = "_ignitewoo_event_end" and m2.meta_value != "" )
			AND m3.meta_key = "_ignitewoo_event_info"
			AND ( m4.meta_key = "_ignitewoo_event" and m4.meta_value = "yes" )
			AND post_status = "publish" 
			' . $where . '
			' . $types . '
			ORDER BY TIMESTAMP( m1.meta_value ) DESC
		';

		$posts = $wpdb->get_results( $sql );

		if ( !isset( $posts ) || '' == $posts || !is_array( $posts ) || count( $posts ) <= 0 ) 
			die( json_encode( array() ) );

		do_action( 'ignitewoo_events_pro_load_rules' );

		$events = array();

		if ( !empty( $posts ) )
		foreach( $posts as $p ) { 

			$data = maybe_unserialize( $p->settings );

			if ( !$data )
				continue;

			if ( function_exists( 'get_product' ) )
				$product = get_product( $p->ID );
			
			if ( !empty( $product ) && ( $product->is_type( 'variable' ) || $product->is_type( 'variation' ) ) ) {  
			
				$attrs = $product->get_attributes(); 
				
			}
			
			$duration = get_post_meta( $p->ID, '_ignitewoo_event_duration', true );

			if ( class_exists( 'IgniteWoo_Events_Rules' ) && isset( $data['recurrence'] ) && 'None' != $data['recurrence']['type'] ) {

				$dfrom = $p->start_date;

				$dto = date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, strtotime( $p->start_date ) + $duration );

			} else if ( !empty( $attrs ) && !empty( $attrs['date'] ) && !empty( $attrs['date']['value'] ) ) { 
			
				$dates = array_map( 'trim', explode( '|', $attrs['date']['value'] ) );
				
				foreach( $dates as $d ) { 
					
					$dfrom = strtotime( $d );
					$dto = strtotime( $d );

					$fm = date( 'm', $dfrom );
					$fd = date( 'd', $dfrom );
					$fy = date( 'Y', $dfrom );
					$fh = date( 'H', $dfrom );
					$fi = date( 'i', $dfrom );

					// offset of bizarro JS 
					$fm = $fm - 1;
					if ( $fm < 0 ) $fm = 11;

					$tm = date( 'm', $dto );
					$td = date( 'd', $dto );
					$ty = date( 'Y', $dto );
					$th = date( 'H', $dto );
					$ti = date( 'i', $dto );

					// offset of bizarro JS 
					$tm = $tm - 1;
					if ( $tm < 0 ) $tm = 11;


					$dfrom = date( 'Y-m-d H:i', $dfrom );
					$dto = date( 'Y-m-d H:i', $dto );

					$terms = wp_get_post_terms( $p->ID, 'product_cat', array( 'fields' => 'ids' ) );

					//$t = array();
					
					if ( !empty( $terms ) ) { 
						foreach( $terms as $k => $t ) 
							$terms[ $k ] = 'event_cat_' . $t;
					} else {
						$terms = '';
					}

					$events[] = array( 
							'start' => $dfrom,
							'end' => $dto,
							'title' => $p->post_title,
							'description' => $this->ignitewoo_the_excerpt( str_replace( '[event_details]', '', $p->post_content ), 150 ),
							'url' => get_permalink( $p->ID ),
							'classes' => implode( ' ' , $terms ),
							'allDay' => false, // causes time to display on calendar
						);
			
				
				
				}
			
			} else {

				$dfrom = $p->start_date;

				$dto = $p->end_date;

			}

			if ( '' == $dfrom || '' == $dto )
				continue;

			$dfrom = strtotime( $dfrom );
			$dto = strtotime( $dto );


			$fm = date( 'm', $dfrom );
			$fd = date( 'd', $dfrom );
			$fy = date( 'Y', $dfrom );
			$fh = date( 'H', $dfrom );
			$fi = date( 'i', $dfrom );

			// offset of bizarro JS 
			$fm = $fm - 1;
			if ( $fm < 0 ) $fm = 11;

			$tm = date( 'm', $dto );
			$td = date( 'd', $dto );
			$ty = date( 'Y', $dto );
			$th = date( 'H', $dto );
			$ti = date( 'i', $dto );

			// offset of bizarro JS 
			$tm = $tm - 1;
			if ( $tm < 0 ) $tm = 11;


			$dfrom = date( 'Y-m-d H:i', $dfrom );
			$dto = date( 'Y-m-d H:i', $dto );

			$events[] = array( 
					'start' => $dfrom,
					'end' => $dto,
					'title' => $p->post_title,
					'description' => $this->ignitewoo_the_excerpt( str_replace( '[event_details]', '', $p->post_content ), 150 ),
					'url' => get_permalink( $p->ID ),
					'allDay' => false, // causes time to display on calendar
				);

		}

		echo json_encode( $events );

		die;

	}


	function render_calendar( $type = '', $cat = 'all' ) { 
		global $wpdb;

		$error_setting = @ini_get( 'display_errors' );
		
		@ini_set( 'display_errors', 0 );
		
		$settings = get_option( 'ignitewoo_events_main_settings', false ); 

		if ( !$settings ) { 
			$settings = array();
			$settings['tooltip_color'] = 'blue';
		}

		if ( empty( $settings['calendar_start_day'] ) ) 
			$settings['calendar_start_day'] = 0;

		?>
		<div>
			<div id="calendar_loading"><?php _e( 'Loading...', 'ignitewoo_events' ) ?></div>
			<div id="ignitewoo_events_calendar_wrap">
			</div>
		</div>

		<script type='text/javascript'>

			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

			jQuery(document).ready( function( $ ) {
				var date = new Date();
				var d = date.getDate();
				var m = date.getMonth();
				var y = date.getFullYear();

				jQuery( "#ignitewoo_events_calendar_wrap" ).fullCalendar({
					firstDay: '<?php echo $settings['calendar_start_day'] ?>',
					loading: function( bool ) {
						if (bool) {
							var offset_top = jQuery( ".fc-border-separate" ).offset().top;
							var offset_left = jQuery( ".fc-border-separate" ).offset().left;
							var div_width = jQuery( ".fc-border-separate" ).width();
							var notice_width = jQuery( "#calendar_loading" ).width()
							var offset_side = ( ( div_width / 2 ) - ( notice_width / 2 ) ) + offset_left ;
							var offset = parseInt( offset_top ) + "px";

							jQuery( "#calendar_loading" ).css( { "top" : offset, "left" : offset_side } );
							jQuery( "#ignitewoo_events_calendar_wrap" ).css( "opacity", "0.2" );
							jQuery( "#calendar_loading").css( "display", "block" );
						} else {
							jQuery( "#calendar_loading").css( "display", "none" );
							jQuery( "#ignitewoo_events_calendar_wrap" ).css( "opacity", "1" );
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
								// our hypothetical feed requires UNIX timestamps
								start: Math.round( start.getTime() / 1000 ),
								end: Math.round( end.getTime() / 1000 ),
								type: "<?php echo $type ?>",
								cat: "<?php echo $cat ?>"
								},
						}).done( function( data ) {
							    var events = [];
							    
							    if ( null != data && data.length > 0 )
							    jQuery.each( data, function( i, item ) {

								    events.push({
									    title: item.title,
									    start: item.start,
									    end: item.end,
									    url: item.url,
									    description: item.description,
									    allDay: item.allDay,
									    className: item.classes
								    });
							    });
							    callback( events );
						});
						
					},
					eventRender: function(event, element) {
						element.qtip({
							content: {    
							    title: { text: event.title },
							    text: '<span class="title">Start: </span>' + ($.fullCalendar.formatDate(event.start, 'hh:mmtt')) + '<br><span class="title">Description: </span>' + event.description       
							},
							position: { 
								  my: 'bottom center',
								  at: 'top center',
							},
							show: { solo: true },
							style: { 
								classes: 'ui-tooltip-shadow ui-tooltip-<?php echo !empty( $settings['tooltip_color'] ) ? $settings['tooltip_color'] : '' ?>'
							},
						});
					},
					error: function() {
						alert( " <?php _e( 'There was an error while fetching events.', 'ignitewoo_events' )?> ");
						},
					header: {
							left: 'prev,next today',
							center: 'title',
							right: 'month,agendaWeek,agendaDay'
						},
					editable: false,
					eventBackgroundColor: "<?php echo !empty( $settings['event_bg_color'] ) ? $settings['event_bg_color'] : '' ?>",
					eventTextColor: "<?php echo !empty( $settings['event_fg_color'] ) ? $settings['event_fg_color'] : ''?>",
				});
				
			});

		</script>
		<style type='text/css'>
			#calendar {
				width: 100%;
				margin: 0 auto;
				}

		</style>

	<?php

		@ini_set( 'error_reporting', $error_setting );
	}


	function ignitewoo_events_register_widgets() {
	
		require_once( dirname( __FILE__ ) . '/includes/events-widgets.php' );
		
		register_widget( 'IgniteWoo_Widget_Upcoming_Events' );

	}


	function add_meta_links( $links, $file ) {

		$plugin_path = trailingslashit( dirname(__FILE__) );
		
                $plugin_dir = trailingslashit( basename( $plugin_path ) );

		if ( $file == $plugin_dir . 'event-calendar-ticketing.php' ) {

			$links []= '<a href="http://ignitewoo.com/contact-us">' . __( 'Support', 'ignitewoo_events' ) . '</a>';
			
			$links []= '<a href="http://ignitewoo.com">' . __( 'View Add-ons / Upgrades' ) . '</a>';

		}
		return $links;
	}

}


register_activation_hook( __FILE__, 'ignitewoo_events_flush_rewrite_rules' );

function ignitewoo_events_flush_rewrite_rules() { 
	global $ignitewoo_events, $wp_rewrite;

	require_once( dirname( __FILE__ ) . '/includes/events-post-types.php'  );

	$wp_rewrite->flush_rules();
	
	flush_rewrite_rules();

}


register_deactivation_hook( __FILE__, 'ignitewoo_events_deactivate' );

function ignitewoo_events_deactivate() { 
	delete_option( 'ignitewoo_events_installed' );
}


add_action( 'init', 'ignitewoo_events_admin_init', 10 );

function ignitewoo_events_admin_init() { 
	global $ignitewoo_events_admin; 

	if ( !is_admin() )
		return;

	require_once( dirname( __FILE__ ) . '/includes/events-admin.php' );

	$ignitewoo_events_admin = new IgniteWoo_Events_Admin();

}

add_action( 'plugins_loaded', 'event_calendar_ticketing_init', 100 );

function event_calendar_ticketing_init() { 
	global $ignitewoo_events;

	$ignitewoo_events = new IgniteWoo_Events();

	if ( empty( $ignitewoo_events->taxonomies ) ) { 

		require_once( dirname( __FILE__ ) . '/includes/events-post-types.php'  );

		$ignitewoo_events->taxonomies = new IgniteWoo_Events_Taxonomies();
	}
	
	if ( !is_admin() )
		require_once( dirname( __FILE__ ) . '/includes/events-template-tags.php'  );

}
