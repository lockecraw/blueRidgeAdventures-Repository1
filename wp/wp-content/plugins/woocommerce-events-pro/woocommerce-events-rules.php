<?php
/**
* Recurrence Processing
*
* Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved
*
* Portions Copyright (c) 2012 by Modern Tribe and contributors
*
*/


class IgniteWoo_Events_Rules { 

	const DATEONLYFORMAT 	= 'F j, Y';
	
	const DATEDISPLAYFORMAT = 'M j, Y g:i a';
	
	const TIMEFORMAT	= 'g:i A';
	
	const HOURFORMAT	= 'g';
	
	const MINUTEFORMAT	= 'i';
	
	const MERIDIANFORMAT	= 'A';
	
	const DBDATEFORMAT	= 'Y-m-d';
	
	const DBDATETIMEFORMAT  = 'Y-m-d H:i:s';
	
	const DBTIMEFORMAT 	= 'H:i:s';
	
	const DBYEARMONTHTIMEFORMAT = 'Y-m';
	

	public static function save_events( $post_id ) {


//$meta = IgniteWoo_Events_Rules::get_recurrence_meta( $post_id );

//$rules = IgniteWoo_Events_Rules::get_series_rules( $post_id );

//var_dump( $meta, $rules ) ; die;

// var_dump( $_POST["ignitewoo_event_info"]['recurrence'] ); die;

//echo '<p>';

		/********************** test for When library. Not used, requires PHP 5.3.x or newer, and many WP sites don't use it yet
		$start = $_POST["ignitewoo_event_info"]["start_date"];

		if ( empty( $start ) ) 
			$start = date( DBDATETIMEFORMAT, time() );

		$start = new DateTime( $start );

		$start_date = $_POST["ignitewoo_event_info"]["start_date"];

		if ( empty( $start_date ) ) 
			$start_date = time();
		else
			$start_date = strtotime( $start_date ); 

		$type = strtolower( $_POST['ignitewoo_event_info']["recurrence"]['type'] );
		$custom_type = strtolower( $_POST['ignitewoo_event_info']["recurrence"]['custom-type'] );

		$end_type = $_POST['ignitewoo_event_info']["recurrence"]['end-type'];
		// On = specific date
		// After = count of X events

		$end_count = $_POST['ignitewoo_event_info']["recurrence"]['end-count'];

		if ( empty( $end_count ) ) 
			$end_count = 1;

		$end_after = $_POST['ignitewoo_event_info']["recurrence"]['end'];

		if ( empty( $end_after ) ) 
			$end_after =  $_POST["ignitewoo_event_info"]['end_date'];

		if ( empty( $end_after ) ) 
			$end_after = date( DBDATETIMEFORMAT, time() );

		require_once( dirname( __FILE__ ) . '/When.php' );

		$r = new When();

		$interval = 1;

		if ( 'custom' == $type ) { 

			$interval = $_POST['ignitewoo_event_info']['recurrence']['custom-interval'];

			if ( empty( $interval ) ) 
				$interval = 1;

			$days = $_POST['ignitewoo_event_info']['recurrence']['custom-week-day'];

			if ( empty( $days ) ) 
				$days = 1; // monday by default


			switch ( $custom_type ) { 

				case 'daily' : 
					break;
				case 'weekly' : 
					$r->byday( $days ); 
					break;
				case 'monthly': 

					if ( absint( $_POST['ignitewoo_event_info']['recurrence']['custom-month-number'] ) > 0 ) 
						$r->bymonthday( array( $_POST['ignitewoo_event_info']['recurrence']['custom-month-number'] ) );
 
					$days = $_POST['ignitewoo_event_info']['recurrence']['custom-month-day'];

					$r->byday( $days ); 

					break;

				case 'yearly' : 

					if ( absint( $_POST['ignitewoo_event_info']['recurrence']['custom-month-number'] ) > 0 ) 
						$r->bymonthday( array( $_POST['ignitewoo_event_info']['recurrence']['custom-month-number'] ) );
 
					$days = $_POST['ignitewoo_event_info']['recurrence']['custom-month-day'];

					$months = $_POST['ignitewoo_event_info']['recurrence']['custom-year-month'];

					$r->byday( $days ); 

					$r->bymonth( $months );

					break;

			}
		


			$r->interval( $interval );

			if ( 'After' == $end_type )
				$r->count( $end_count );
			else 
				$r->until( new DateTime( $end_after ) );

			$r->recur( $start, $custom_type );


		} else { 

			if ( 'every day' == $type ) 
				$type = 'daily';
			if ( 'every week' == $type ) 
				$type = 'weekly';
			if ( 'every month' == $type ) 
				$type = 'monthly';
			if ( 'every year' == $type ) 
				$type = 'yearly';


//echo "<p>type: $type / interval: $interval / end type: $end_type / end after: $end_after <p>";


			$r->interval( $interval );

			if ( 'After' == $end_type )
				$r->count( $end_count );
			else 
				$r->until( new DateTime( $end_after ) );

			if ( 'monthly' == $type )
				$r->bymonthday( array( date( 'd', $start_date ) ) );


			$r->recur( $start, $type );
		}


		while (  $result = $r->next() ) {

			echo ' ------->> ' . $result->format( 'Y-m-d H:i:s' ) . "<br/>\n";
			//echo $result->date . "<br/>\n";
		}

		die;
		*************************/


		extract( IgniteWoo_Events_Rules::get_recurrence_meta( $post_id ) );

		$rules = IgniteWoo_Events_Rules::get_series_rules( $post_id );
//var_dump( $rec_type, IgniteWoo_Events_Rules::get_recurrence_meta( $post_id ) ); die;
		// use the recurrence start meta if necessary because we can't guarantee which order the start date will come back in

		$settings = get_post_meta( $post_id, '_ignitewoo_event_info', true );

		$rec_start = strtotime( $settings['start_date'] );

		$event_end = strtotime( $settings['end_date']  );

		$duration = $event_end - $rec_start;

		$rec_end = 'On' == $rec_end_type ? strtotime( IgniteWoo_Date_Utils::endOfDay( $rec_end ) ) : $rec_end_count - 1; // subtract one because event is first occurrence

		// different update types
		delete_post_meta( $post_id, '_ignitewoo_event_start' );
		
		delete_post_meta( $post_id, '_ignitewoo_event_end' );
		
		delete_post_meta( $post_id, '_ignitewoo_event_duration' );

		// add back original start and end date
		add_post_meta( $post_id,'_ignitewoo_event_start', date( IgniteWoo_Events_Rules::DBDATETIMEFORMAT, $rec_start ) );
		
		add_post_meta( $post_id,'_ignitewoo_event_end', date( IgniteWoo_Events_Rules::DBDATETIMEFORMAT, $event_end ) );
		
		add_post_meta( $post_id,'_ignitewoo_event_duration', $duration );

		if ( $rec_type != "None" ) {

			$recurrence = new IgniteWoo_Recurrence( $rec_start, $rec_end, $rules, $rec_end_type == 'After' );

			$dates = (array) $recurrence->get_dates();

			$main_settings = get_option( 'ignitewoo_events_main_settings', false ); 

			$date_format = empty( $main_settings['date_format'] ) ? 'M j, Y' : $main_settings['date_format'];
			
			$time_format = empty( $main_settings['time_format'] ) ? 'h:i a' : $main_settings['time_format'];

			$date_attrs = array();

			// Add the start date to the attrs, admins are expected to match the start date to the rule set.
			// So if it's a custom recur on the 1st month of the month then the start date needs to be the 
			// first month of a month
			$date_attrs[] = date( $date_format . ' ' . $time_format, $rec_start );
			
			$date_attrs = array_unique( $date_attrs );
			
			// add meta for all dates in recurrence
			foreach( $dates as $date ) {

				add_post_meta( $post_id,'_ignitewoo_event_start', date( IgniteWoo_Events_Rules::DBDATETIMEFORMAT, $date ) );

				$date_attrs[] = date( $date_format . ' ' . $time_format, $date );

			}
	
			/** 

			if recurring: 
				set product type to variable
				get attribs, add our dates to the Date attrib field
				Make the attribute first in the list unless the admin re-ordered the attr fields
			*/

			if ( count( $date_attrs ) > 0 ) { 

				$date_attrs = implode( '|', $date_attrs );

				$attrs = get_post_meta( $post_id, '_product_attributes', true ); 
//var_dump( $date_attrs, $attrs );
				if ( !$attrs ) { 
					$attrs = array();
					$position = 1;
				} else { 
					$position = count( $attrs ) + 1;
				}

				$attrs['date'] = array( 
							'name' => __( 'Date', 'ignitewoo_events' ),
							'value' => $date_attrs, 
							'position' => $position,
							'is_visible' => '1',
							'is_variation' => '1',
							'is_taxonomy' => '0',
						 );

				update_post_meta( $post_id, '_product_attributes', $attrs );

				wp_set_object_terms( $post_id, 'variable', 'product_type' );

				// Let the user link their own variations
				// IgniteWoo_Events_Rules::link_all_variations( $post_id );

				add_filter( 'redirect_post_location', array( 'IgniteWoo_Events_Rules', 'event_message' ), 9999 );

			}

		}
	}


	function event_message( $loc ) {
	
		return add_query_arg( 'message', 427, $loc );
		
	} 


	/* Helper
	function array_cartesian( $input ) {

		$result = array();

		while ( list( $key, $values ) = each( $input ) ) {

		@set_time_limit(0 );

		// If a sub-array is empty, it doesn't affect the cartesian product
		if ( empty( $values ) ) {
			continue;
		}

		// Special case: seeding the product array with the values from the first sub-array
		if ( empty( $result ) ) {

			foreach( $values as $value ) {
				$result[] = array( $key => $value );
			}

		} else {
			// Second and subsequent input sub-arrays work like this:
			//   1. In each existing array inside $product, add an item with
			//      key == $key and value == first item in input sub-array
			//   2. Then, for each remaining item in current input sub-array,
			//      add a copy of each existing array inside $product with
			//      key == $key and value == first item in current input sub-array

			// Store all items to be added to $product here; adding them on the spot
			// inside the foreach will result in an infinite loop
			$append = array();
			
			foreach( $result as &$product ) {
			
			// Do step 1 above. array_shift is not the most efficient, but it
			// allows us to iterate over the rest of the items with a simple
			// foreach, making the code short and familiar.
			$product[$key] = array_shift( $values );

			// $product is by reference (that's why the key we added above
			// will appear in the end result), so make a copy of it here
			$copy = $product;

			// Do step 2 above.
			foreach( $values as $item ) {
			
				$copy[$key] = $item;
				
				$append[] = $copy;
			}

			// Undo the side effecst of array_shift
			array_unshift( $values, $product[$key] );
			}

			// Out of the foreach, we can add to $results now
			$result = array_merge( $result, $append );
		}
		}

		return $result;
	}

	// NOT a good idea to do this, so it is disabled
	// Based on WooCommerce core code: 
	function link_all_variations( $post_id ) {
		global $woocommerce;

		@set_time_limit( 0 ); 

		if ( !$post_id) 
			return;

		$variations = array();

		if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0' ) >= 0 )
			$_product = get_product( $post_id );
		else 
			$_product = new WC_Product( $post_id );

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( !$attribute['is_variation'] ) 
				continue;

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
			
				$post_terms = wp_get_post_terms( $post_id, $attribute['name'] );

				$options = array();
				
				foreach ( $post_terms as $term ) {
				
					$options[] = $term->slug;
				}
				
			} else { 
			
				$options = explode('|', $attribute['value'] );
				
			}


			$options = array_map('trim', $options );

			$variations[$attribute_field_name] = $options;

			@set_time_limit(0 );

		}

		// Return if none were found
		if ( 0 == sizeof( $variations ) ) 
			return;
			
		delete_transient( 'wc_product_children_ids_' . $post_id );

		// Get existing variations so we don't create duplicates
		$available_variations = array();

		foreach( $_product->get_children() as $child_id ) {

			$child = $_product->get_child( $child_id );

			if ( ! empty( $child->variation_id ) ) {
			//if ( $child instanceof WC_Product_Variation ) {

				$available_variations[ $child_id ] = $child->get_variation_attributes();

			}
		}

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title' => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_parent' => $post_id,
			'post_type' => 'product_variation'
		 );


		
		$variation_ids = array();

		$added = 0;

		//$possible_variations = cartesian_product( $variations );

		$possible_variations = IgniteWoo_Events_Rules::array_cartesian( $variations );

//var_dump( $variations, $available_variations, $possible_variations ); die;

		$set_variations = array();

		$x = 0;

		$duration = get_post_meta( $post_id, '_ignitewoo_event_duration', true ); 

		foreach ( $possible_variations as $variation ) {

			$x++;

			@set_time_limit(0 );

			$id = array_search( $variation, $available_variations );

			// Variation already exist?
			if ( false !== $id ) {

				$set_variations[] = $id;

				continue;

			}

			$variation_post_data['menu_order'] = $x; 

			$variation_id = wp_insert_post( $variation_post_data );

			$variation_ids[] = $variation_id;

			$set_variations[] = $variation_id;

			foreach ( $variation as $key => $value )
				update_post_meta( $variation_id, $key, $value );


			$end_date = date( 'M d, Y h:i a', strtotime( $variation['attribute_date'] ) + $duration );
			
			$end_date_stamp = strtotime( $variation['attribute_date'] ) + $duration;

			update_post_meta( $variation_id, 'end_date', $end_date );
			
			update_post_meta( $variation_id, 'end_date_stamp', $end_date_stamp );

			$added++;

			// Max 1000
			if ( $added > 1000 ) 
				break;

		}

var_dump( $available_variations, $set_variations );
die;
		// Clean out excess variations - happens when someone changes the recurrence to a lesser amount or
		// when the schedule itself changes
		foreach( array_keys( $available_variations ) as $a ) { 

			if ( !in_array( $a, $set_variations ) )
				wp_delete_post( $a, true );

		}

		$woocommerce->clear_product_transients( $post_id );

	}
	*/

	public static function get_recurrence_meta( $post_id, $recurrence_data = null ) {

		if ( !$recurrence_data ) { 
		
			$settings = get_post_meta( $post_id, '_ignitewoo_event_info', true );
			
			$recurrence_data = $settings['recurrence'];
		}

		$rec_array = array();

		if ( $recurrence_data ) {

			$rec_array['rec_type'] = $recurrence_data['type'];
			
			$rec_array['rec_end_type'] = $recurrence_data['end-type'];
			
			$rec_array['rec_end'] = $recurrence_data['end'];
			
			$rec_array['rec_end_count'] = $recurrence_data['end-count'];

			$rec_array['rec_custom_type'] = $recurrence_data['custom-type'];
			
			$rec_array['rec_custom_interval'] = $recurrence_data['custom-interval'];
			
			// the following two fields are just text fields used in the display
			$rec_array['rec_custom_type_text'] = $recurrence_data['custom-type-text'];
			
			$rec_array['rec_occurrence_count_text'] = $recurrence_data['occurrence-count-text'];

			$rec_array['rec_custom_week_day'] = !empty( $recurrence_data['custom-week-day'] ) ? $recurrence_data['custom-week-day'] : null;
			
			$rec_array['rec_custom_month_number'] = $recurrence_data['custom-month-number'];
			
			$rec_array['rec_custom_month_day'] = $recurrence_data['custom-month-day'];

			$rec_array['rec_custom_year_month'] = !empty( $recurrence_data['custom-year-month'] ) ? $recurrence_data['custom-year-month'] : array();
			
			$rec_array['rec_custom_year_filter'] = !empty( $recurrence_data['custom-year-filter'] ) ?  $recurrence_data['custom-year-filter'] : null;
			
			$rec_array['rec_custom_year_month_number'] = $recurrence_data['custom-year-month-number'];
			
			$rec_array['rec_custom_year_month_day'] = $recurrence_data['custom-year-month-day'];

		} else {

			$rec_array['rec_type'] = null;
			
			$rec_array['rec_end_type'] = null;
			
			$rec_array['rec_end'] = null;
			
			$rec_array['rec_end_count'] = null;
			
			$rec_array['rec_custom_type'] = null;
			
			$rec_array['rec_custom_interval'] = null;
			
			$rec_array['rec_custom_type_text'] = null;
			
			$rec_array['rec_occurrence_count_text'] = null;
			
			$rec_array['rec_custom_week_day'] = null;
			
			$rec_array['rec_custom_month_number'] = null;
			
			$rec_array['rec_custom_month_day'] = null;
			
			$rec_array['rec_custom_year_filter'] = null;
			
			$rec_array['rec_custom_year_month_number'] = null;
			
			$rec_array['rec_custom_year_month_day'] = null;
			
			$rec_array['rec_custom_year_month'] = array();

		}

		return $rec_array;
	}


	public static function get_series_rules( $post_id ) {

		extract( IgniteWoo_Events_Rules::get_recurrence_meta( $post_id ) );

		$rules = null;

		if ( !$rec_custom_interval)
			$rec_custom_interval = 1;

		if ( 'Every Day' == $rec_type || ( 'Custom' == $rec_type && 'Daily' == $rec_custom_type ) ) {
		
			$rules = new IgniteWoo_Day_Series_Rules( $rec_type == "Every Day" ? 1 : $rec_custom_interval );

		} else if ( 'Every Week' == $rec_type ) {
		
			$rules = new IgniteWoo_Week_Series_Rules( 1 );

		} else if ( 'Custom' == $rec_type && 'Weekly' == $rec_custom_type ) {
		
			$rules = new IgniteWoo_Week_Series_Rules( $rec_custom_interval ? $rec_custom_interval : 1, $rec_custom_week_day );

		} else if ( 'Every Month' == $rec_type ) {
		
			$rules = new IgniteWoo_Month_Series_Rules( 1 );

		} else if ( 'Custom' == $rec_type && 'Monthly' == $rec_custom_type ) {
		
			$rec_custom_month_day_of_month = is_numeric( $rec_custom_month_number) ? array( $rec_custom_month_number) : null;
			
			$rec_custom_month_number = self::ordinal_to_int( $rec_custom_month_number );
			
			$rules = new IgniteWoo_Month_Series_Rules( $rec_custom_interval ? $rec_custom_interval : 1, $rec_custom_month_day_of_month, $rec_custom_month_number, $rec_custom_month_day );
			

		} else if ( 'Every Year' == $rec_type ) {
		
			$rules = new IgniteWoo_Year_Series_Rules( 1 );

		} else if ( 'Custom' == $rec_type && 'Yearly' == $rec_custom_type ) {

			$rules = new IgniteWoo_Year_Series_Rules( $rec_custom_interval ? $rec_custom_interval : 1, $rec_custom_year_month, $rec_custom_year_filter ? $rec_custom_year_month_number : null, $rec_custom_year_filter ? $rec_custom_year_month_day : null );

		}

		return $rules;
	}


	/*
	public static function recurrence_to_text( $post_id = null ) {

		$text = "";
		$custom_text = "";
		$occurrence_text = "";
		
		if ( $post_id == null ) {
			global $post;
			$post_id = $post->ID;
		}
		
		extract( IgniteWoo_Events_Rules::get_recurrence_meta( $post_id) );
		
		if ( $rec_type == "Every Day" ) {
			$text = __("Every day", 'ignitewoo_events' ); 
			$occurrence_text = sprintf( _n(" for %d day", " for %d days", $rec_end_count, 'ignitewoo_events'), $rec_end_count );
			$custom_text = ""; 

		} else if ( $rec_type == "Every Week" ) {
			$text = __("Every week", 'ignitewoo_events' );
			$occurrence_text = sprintf( _n(" for %d week", " for %d weeks", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	
	
		} else if ( $rec_type == "Every Month" ) {
			$text = __("Every month", 'ignitewoo_events' );
			$occurrence_text = sprintf( _n(" for %d month", " for %d months", $rec_end_count, 'ignitewoo_events'), $rec_end_count );
						
		} else if ( $rec_type == "Every Year" ) {
			$text = __("Every year", 'ignitewoo_events' );
			$occurrence_text = sprintf( _n(" for %d year", " for %d years", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	
				
		} else if ( $rec_type == "Custom" ) {

			if ( $rec_custom_type == "Daily" ) {
				$text = $rec_custom_interval == 1 ? 
					__("Every day", 'ignitewoo_events') : 
					sprintf( __("Every %d days", 'ignitewoo_events'), $rec_custom_interval );

				$occurrence_text = sprintf( _n(", recurring %d time", ", recurring %d times", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	
			} else if ( $rec_custom_type == "Weekly" ) {
				$text = $rec_custom_interval == 1 ? 
					__("Every week", 'ignitewoo_events') : 
					sprintf( __("Every %d weeks", 'ignitewoo_events'), $rec_custom_interval );	
				$custom_text = sprintf( __(" on %s", 'ignitewoo_events'), self::daysToText( $rec_custom_week_day) );
				$occurrence_text = sprintf( _n(", recurring %d time", ", recurring %d times", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	

			} else if ( $rec_custom_type == "Monthly" ) {
				$text = $rec_custom_interval == 1 ? 
					__("Every month", 'ignitewoo_events') : 
					sprintf( __("Every %d months", 'ignitewoo_events'), $rec_custom_interval );	

				$number_display = is_numeric( $rec_custom_month_number) ? TribeDateUtils::numberToOrdinal( $rec_custom_month_number ) : strtolower( $rec_custom_month_number ); 
				$custom_text = sprintf( __(" on the %s %s", 'ignitewoo_events'), $number_display,  is_numeric( $rec_custom_month_number) ? __("day", 'ignitewoo_events') : self::daysToText( $recCustomMonthDay) );
				$occurrence_text = sprintf( _n(", recurring %d time", ", recurring %d times", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	

			} else if ( $rec_custom_type == "Yearly" ) {
				$text = $rec_custom_interval == 1 ? 
					__("Every year", 'ignitewoo_events') : 
					sprintf( __("Every %d years", 'ignitewoo_events'), $rec_custom_interval );												
				
				$custom_year_number = $rec_custom_year_month_number != -1 ? TribeDateUtils::numberToOrdinal( $rec_custom_year_month_number) : __("last", 'ignitewoo_events' );
				
				$day = $rec_custom_year_filter ? $custom_year_number : TribeDateUtils::numberToOrdinal( date( 'j', strtotime( TribeEvents::getRealStartDate( $post_id ) ) ) );
				$of_week = $rec_custom_year_filter ? self::daysToText( $rec_custom_year_month_day) : "";
				$months = self::monthsToText( $rec_custom_year_month );
				$custom_text = sprintf( __(" on the %s %s of %s", 'ignitewoo_events'), $day, $of_week, $months );				
				$occurrence_text = sprintf( _n(", recurring %d time", ", recurring %d times", $rec_end_count, 'ignitewoo_events'), $rec_end_count );	
			} 
		}
		
		// end text
		if ( $rec_end_type == "On" ) {
			$end_text = ' '.sprintf( __(" until %s", 'ignitewoo_events'), date_i18n(get_option('date_format'), strtotime( $rec_end) )) ;
		} else {
			$end_text = $occurrence_text;
		}

		return sprintf( __('%s%s%s', 'ignitewoo_events'), $text, $custom_text, $end_text );
	}

	
	static function daysToText( $days) {
		$day_words = array( __("Monday", 'ignitewoo_events'), __("Tuesday", 'ignitewoo_events'), __("Wednesday", 'ignitewoo_events'), __("Thursday", 'ignitewoo_events'), __("Friday", 'ignitewoo_events'), __("Saturday", 'ignitewoo_events'), __("Sunday", 'ignitewoo_events') );
		$count = sizeof( $days );
		$day_text = "";
		
		for( $i = 0; $i < $count ; $i++) {
			if ( $count > 2 && $i == $count - 1 ) {
				$day_text .= __(", and", 'ignitewoo_events').' ';
			} else if ( $count == 2 && $i == $count - 1) {
				$day_text .= ' '.__("and", 'ignitewoo_events').' ';
			} else if ( $count > 2 && $i > 0) {
				$day_text .= __(",", 'ignitewoo_events').' ';
			}

			$day_text .= $day_words[$days[$i]-1] ? $day_words[$days[$i]-1] : "day";
		}
		
		return $day_text;
	}
	

	static function monthsToText( $months) {
		$month_words = array( __("January", 'ignitewoo_events'), __("February", 'ignitewoo_events'), __("March", 'ignitewoo_events'), __("April", 'ignitewoo_events'), 
			 __("May", 'ignitewoo_events'), __("June", 'ignitewoo_events'), __("July", 'ignitewoo_events'), __("August", 'ignitewoo_events'), __("September", 'ignitewoo_events'), __("October", 'ignitewoo_events'), __("November", 'ignitewoo_events'), __("December", 'ignitewoo_events') );
		$count = sizeof( $months );
		$month_text = "";
		
		for( $i = 0; $i < $count ; $i++) {
			if ( $count > 2 && $i == $count - 1 ) {
				$month_text .= __(", and ", 'ignitewoo_events' );
			} else if ( $count == 2 && $i == $count - 1) {
				$month_text .= __(" and ", 'ignitewoo_events' );				
			} else if ( $count > 2 && $i > 0) {
				$month_text .= __(", ", 'ignitewoo_events' );
			}
			
			$month_text .= $month_words[$months[$i]-1];
		}
		
		return $month_text;
	}

	*/


	static function ordinal_to_int( $ordinal ) {
	
		switch( $ordinal ) {
		
			case 'First' : return 1;
			
			case 'Second' : return 2;
			
			case 'Third' : return 3;
			
			case 'Fourth' : return 4;
			
			case 'Last' : return -1;
			
			default: return null;
		   
		}
		
	}

}


class IgniteWoo_Recurrence {

	private $start_date;
	
	private $end;
	
	private $series_rules;
	
	private $by_occurrence_count;

	public function  __construct( $start_date, $end, $series_rules, $by_occurrence_count = false) {
	
		$this->start_date = $start_date;
		
		$this->end = $end;
		
		$this->series_rules = $series_rules;
		
		$this->by_occurrence_count = $by_occurrence_count;
		
	}

	
	/**
	 * Using the rules engine, find all dates in the series 
	 *
	 * @return An array of all dates in the series
	 */
	public function get_dates() {

		if ( $this->series_rules ) {
		
			$dates = array();
			
			$cur_date = $this->start_date;

			if ( $this->by_occurrence_count ) {
			
				// a set number of occurrences
				for( $i = 0; $i < $this->end; $i++ ) {
				
					$cur_date = $this->series_rules->get_next_date( $cur_date );
					
					// Makes sure to assign the proper hours to the date.
					$cur_date = mktime ( date( 'H', $this->start_date ), date("i", $this->start_date ), date( 's', $this->start_date ), date( 'n', $cur_date ),  date( 'j', $cur_date ), date( 'Y', $cur_date ) );

					$dates[] = $cur_date;
				}				
			} else {

				// date driven
				while (  $cur_date <= $this->end ) {
				
					$cur_date = $this->series_rules->get_next_date( $cur_date );

					// Makes sure to assign the proper hours to the date.
					$cur_date = mktime ( date( 'H', $this->start_date ), date("i", $this->start_date ), date( 's', $this->start_date ), date( 'n', $cur_date ),  date( 'j', $cur_date ), date( 'Y', $cur_date ) );
 
					if ( $cur_date <= $this->end )
						$dates[] = $cur_date;
				}
			}

			return $dates;
		}
	}
}


class IgniteWoo_Date_Utils {

	// default formats, they are overridden by WP options or by arguments to date methods
	const DATEONLYFORMAT 		= 'F j, Y';
	
	const TIMEFORMAT		= 'g:i A';
	
	const HOURFORMAT		= 'g';
	
	const MINUTEFORMAT		= 'i';
	
	const MERIDIANFORMAT		= 'A';
	
	const DBDATEFORMAT	 	= 'Y-m-d';
	
	const DBDATETIMEFORMAT 		= 'Y-m-d H:i:s';
	
	const DBTIMEFORMAT 		= 'H:i:s';
	
	const DBYEARMONTHTIMEFORMAT	= 'Y-m';

	
	public static function dateOnly( $date, $is_timestamp = false ) {
	
		$date = $is_timestamp ? $date : strtotime( $date );
		
		return date( IgniteWoo_Date_Utils::DBDATEFORMAT, $date );
	}

	
	public static function timeOnly( $date ) {
	
		return date( IgniteWoo_Date_Utils::DBTIMEFORMAT, strtotime( $date ) );
		
	}

	
	public static function hourOnly( $date ) {
	
		return date( IgniteWoo_Date_Utils::HOURFORMAT, strtotime( $date ) );
		
	}

	
	public static function minutesOnly( $date ) {
	
		return date( IgniteWoo_Date_Utils::MINUTEFORMAT, strtotime( $date ) );
		
	}

	
	public static function meridianOnly( $date ) {
	
		return date( IgniteWoo_Date_Utils::MERIDIANFORMAT, strtotime( $date ) );
		
	}

	
	public static function dateAndTime( $date, $is_timestamp = false ) {
	
		$date = $is_timestamp ? $date : strtotime( $date );
		
		return date( IgniteWoo_Date_Utils::DBDATETIMEFORMAT, $date );
		
	}

	
	public static function endOfDay( $date, $is_timestamp = false ) {
	
		$date = $is_timestamp ? $date : strtotime( $date );
		
		$date = date( IgniteWoo_Date_Utils::DBDATEFORMAT, $date );
		
		$date = strtotime( $date . ' 23:59:59' );
		
		return date( IgniteWoo_Date_Utils::DBDATETIMEFORMAT, $date );
		
	}

	
	public static function beginningOfDay( $date, $is_timestamp = false ) {
	
		$date = $is_timestamp ? $date : strtotime( $date );
		
		$date = date( IgniteWoo_Date_Utils::DBDATEFORMAT, $date );
		
		$date = strtotime( $date . ' 00:00:00' );
		
		return date( IgniteWoo_Date_Utils::DBDATETIMEFORMAT, $date );
		
	}

	
	public static function addTimeToDate( $date, $time ) {
	
		$date = self::dateOnly( $date );
		
		return date( IgniteWoo_Date_Utils::DBDATETIMEFORMAT, strtotime( $date . $time) );
		
	}
	

	public static function timeBetween( $date1, $date2 ) {
	
	    return abs( strtotime( $date1 ) - strtotime( $date2 ) );
	    
	}
	
	
	// returns the last day of the month given a php date
	public static function getLastDayOfMonth( $timestamp ) {
	
		$curmonth = date( 'n', $timestamp );
		
		$curYear = date( 'Y', $timestamp );
		
		$nextmonth = mktime(0, 0, 0, $curmonth+1, 1, $curYear );
		
		$lastDay = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextmonth ) . ' -1 day' );
		
		return date( 'j', $lastDay );
		
	}

	
	// returns true if the timestamp is a weekday
	public static function isWeekday( $curdate ) {
	
		return in_array( date( 'N', $curdate ), array( 1, 2, 3, 4, 5 ) );
		
	}

	
	// returns true if the timestamp is a weekend
	public static function is_weekend( $curdate ) {
	
		return in_array( date( 'N', $curdate ), array(6,7) );
		
	}

	
	// gets the last day of the week in a month (ie the last Tuesday).  Passing in -1 gives you the last day in the month
	public static function get_last_day_of_week_in_month( $curdate, $day_of_week) {
	
		$nextdate = mktime ( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $curdate ), IgniteWoo_Date_Utils::getLastDayOfMonth( $curdate ), date( 'Y', $curdate ) );

		while ( date( 'N', $nextdate ) != $day_of_week  && $day_of_week != -1) {
		
			$nextdate = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' -1 day' );
			
		}

		return $nextdate;
	}


	// gets the first day of the week in a month (ie the first Tuesday).
	public static function get_first_day_of_week_in_month( $curdate, $day_of_week) {
	
		$nextdate = mktime (0, 0, 0, date( 'n', $curdate ), 1, date( 'Y', $curdate ) );

		while (  !( $day_of_week > 0 && date( 'N', $nextdate ) == $day_of_week) &&
			!( -1 == $day_of_week && IgniteWoo_Date_Utils::isWeekday( $nextdate ) ) &&
			!( -2 == $day_of_week && IgniteWoo_Date_Utils::is_weekend( $nextdate ) ) ) {
			
			$nextdate = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + 1 day' );
			
		}

		return $nextdate;
	}

	// from http://php.net/manual/en/function.date.php
	public static function numberToOrdinal( $number) {
	
		return $number . ( ( ( strlen( $number ) > 1 ) && ( '1' == substr( $number, -2, 1 ) ) ) ? 'th' : date( 'S', mktime( 0, 0, 0, 0, substr( $number, -1 ), 0 ) ) );
	}
}



interface IgniteWoo_Date_Series_Rules {

	const DATE_ONLY_FORMAT = 'Y-m-d';
	
	const DATE_FORMAT = 'Y-m-d H:i:s';
	
	public function get_next_date( $curdate );
}



/**
* Rules for daily recurrences
*/
class IgniteWoo_Day_Series_Rules implements IgniteWoo_Date_Series_Rules {

	private $days_between;

	public function __construct( $days_between = 1) {
	
		$this->days_between = $days_between;
		
	}

	
	public function get_next_date( $curdate ) {
	
		return strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $curdate ) . ' + ' . $this->days_between . ' days' );
		
	}
}


/**
* Rules for weekly recurrences
*/
class IgniteWoo_Week_Series_Rules implements IgniteWoo_Date_Series_Rules {

	private $weeks_between;
	
	private $days;
	

	public function __construct( $weeks_between = 1, $days = array() ) {
	
		$this->weeks_between = $weeks_between;
		
		$this->days = $days; // days are integers representing days
		
		sort( $this->days );
	}
	
	
	public function get_next_date( $curdate ) {
	
		$nextdate = $curdate;

		if ( sizeof( $this->days) > 0 ) {
		
			// get current day of week
			$curDayOfWeek = date("N", $curdate );

			// find the selected day that is equal or higher to the current day
			$nextDayOfWeek = $this->getNextDayOfWeek( $curDayOfWeek );

			while (  date("N", $nextdate ) != $nextDayOfWeek )			
				$nextdate = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . " + 1 day" );

			if ( $nextDayOfWeek > $curDayOfWeek )
			
				return strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) );
			
			else if ( $nextDayOfWeek < $curDayOfWeek )
				return strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $this->weeks_between - 1 ) . ' weeks' );
		}
		
		return strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . $this->weeks_between . ' weeks' );
		
	}

	
	function getNextDayOfWeek( $curDayOfWeek) {
	
		foreach( $this->days as $day) {
		
			if ( $day > $curDayOfWeek)
				return $day;
		}

		return $this->days[0];
	}
}


/**
 * Rules for monthly recurrences
 */
class IgniteWoo_Month_Series_Rules implements IgniteWoo_Date_Series_Rules {

	private $months_between;
	
	private $days_of_month;
	
	private $week_of_month;
	
	private $day_of_week;

	public function __construct( $months_between = 1, $days_of_month = array(), $week_of_month = null, $day_of_week = null) {
	
		$this->months_between = $months_between;
		
		$this->days_of_month = (array)$days_of_month;
		
		$this->week_of_month = $week_of_month;
		
		$this->day_of_week = $day_of_week;

		sort( $this->days_of_month );
		
	}

	
	public function get_next_date( $curdate ) {
	
		$next_day_of_month = date( 'j', $curdate );

		if ( $this->week_of_month && $this->day_of_week) {
		
			return $this->get_nth_day_of_week( $curdate, $this->day_of_week, $this->week_of_month );
		
		} else { 
		
			// normal date based recurrence

			if (sizeof( $this->days_of_month) > 0) {
			
				$next_day_of_month = $this->get_next_day_of_month( $next_day_of_month );

				while ( IgniteWoo_Date_Utils::getLastDayOfMonth( $curdate ) < $next_day_of_month) {
				
					$next_day_of_month = $this->days_of_month[0];
					
					$curdate = mktime( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $curdate ) + $this->months_between, 1, date( 'Y', $curdate ) );
					
				}
				
			}

			if ( $next_day_of_month > date( 'j', $curdate ) ) {
			
				// no need to jump ahead stay in current month
				return mktime( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $curdate ), $next_day_of_month, date( 'Y', $curdate ) );
				
			} else {
				
				$nextdate = mktime ( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $curdate ) + $this->months_between, 1, date( 'Y', $curdate ) );

				while ( IgniteWoo_Date_Utils::getLastDayOfMonth( $nextdate ) < $next_day_of_month) {
				
					$nextdate = mktime ( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $nextdate ) + $this->months_between, 1, date( 'Y', $nextdate ) );
					
				}

				return mktime( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $nextdate ), $next_day_of_month, date( 'Y', $nextdate ) );
				
			}
		}
	}

	
	function get_nth_day_of_week( $curdate, $day_of_week, $week_of_month) {
	
		$curmonth = date( 'n', $curdate );

		if ( -1 == $week_of_month ) {
		
			// last week
		
			$nextdate = IgniteWoo_Date_Utils::get_last_day_of_week_in_month( $curdate, $day_of_week );

			if ( $curdate == $nextdate ) {
			
				$curdate = mktime (0, 0, 0, date( 'n', $curdate ) + $this->months_between, 1, date( 'Y', $curdate ) );
				
				$nextdate = IgniteWoo_Date_Utils::get_last_day_of_week_in_month( $curdate, $day_of_week );
				
			}

			return $nextdate;
			
		} else {
		
			$nextdate = IgniteWoo_Date_Utils::get_first_day_of_week_in_month( $curdate, $day_of_week );
			
			$maybe_date = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $week_of_month-1) . ' weeks' );

			// if on the correct date or before current date, then try next month
			if ( date( IgniteWoo_Date_Series_Rules::DATE_ONLY_FORMAT, $maybe_date ) <= date( IgniteWoo_Date_Series_Rules::DATE_ONLY_FORMAT, $curdate ) ) {
			
				$curdate = mktime (0, 0, 0, date( 'n', $curdate ) + $this->months_between, 1, date( 'Y', $curdate ) );
				
				$nextdate = IgniteWoo_Date_Utils::get_first_day_of_week_in_month( $curdate, $day_of_week );
				
				$maybe_date = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $week_of_month-1 ) . ' weeks' );
				
			}

			// if this doesn't exist, then try next month
			while ( date( 'n', $maybe_date ) != date( 'n', $nextdate ) ) { 
			
				$nextdate = mktime (0, 0, 0, date( 'n', $nextdate ) + $this->months_between, 1, date( 'Y', $nextdate ) );
				
				$nextdate = IgniteWoo_Date_Utils::get_first_day_of_week_in_month( $curdate, $day_of_week );
				
				$maybe_date = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $week_of_month-1) . ' weeks' );
				
			}

			return $maybe_date;
		}
	}

	
	function get_next_day_of_month( $curDayOfMonth) {
	
		foreach( $this->days_of_month as $day) {
		
			if ( $day > $curDayOfMonth)
				return $day;
				
		}

		return $this->days_of_month[0];
	}
}


/**
 * Rules for yearly recurrences
 */
class IgniteWoo_Year_Series_Rules implements IgniteWoo_Date_Series_Rules {

	private $years_between;
	
	private $months_of_year;
	
	private $week_of_month;
	
	private $day_of_week;

	public function __construct( $years_between = 1, $months_of_year = array(), $week_of_month = null, $day_of_week = null) {
	
		$this->years_between = $years_between;
		
		$this->months_of_year = $months_of_year;
		
		$this->week_of_month = $week_of_month;
		
		$this->day_of_week = $day_of_week;

		sort( $this->months_of_year );
	}

	
	public function get_next_date( $curdate ) {

		$next_month_of_year = date( 'n', $curdate );
		
		$day_of_month = date( 'j', $curdate );

		if (sizeof( $this->months_of_year) > 0) {
		
			$next_month_of_year = $this->get_next_month_of_year( $next_month_of_year );

		}

		if ( $this->week_of_month && $this->day_of_week) {

			// 4th wednesday of next month
			return $this->get_nth_day_of_month( $curdate, $this->day_of_week, $this->week_of_month, $next_month_of_year );
			
		} else {
		
			// normal date based recurrence_data
		
			$nextdate = $this->advance_date( $curdate, $next_month_of_year );
//var_dump( 'YEAR RULES get_next_date', date( 'Y-m-d', $nextdate ) , date( 'Y-m-d', $curdate ) ); die;
			// TODO: TEST AHEAD FOR INVALID RECURSIONS (ie every February 29 or September 31 which will result in an infinite loop)
			
			while ( date( 'j', $curdate ) != date( 'j', $nextdate ) ) { // date wrapped
			
				$nextdate = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . " - 1 months" ); // back it up a month to get to the correct one
				
				$next_month_of_year = $this->get_next_month_of_year( date( 'n', $nextdate ) ); // get the next month in the series
				
				$nextdate = $this->advance_date( $curdate, $next_month_of_year );
			}

			return mktime( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), date( 'n', $nextdate ),  date( 'j', $nextdate ), date( 'Y', $nextdate ) );
			
		}
	}

	
	function advance_date( $curdate, $next_month_of_year, $day_of_month = null) {
	
		if ( $next_month_of_year > date( 'n', $curdate ) ) { // is curdate correct here?
		
			$nextdate = mktime( date( 'H', $curdate ), date("i", $curdate ), date( 's', $curdate ), $next_month_of_year, $day_of_month ? $day_of_month : date( 'j', $curdate ), date( 'Y', $curdate ) );
			
		} else {
		
			$nextdate = mktime (0, 0, 0, $next_month_of_year, $day_of_month ? $day_of_month : date( 'j', $curdate ), date( 'Y', $curdate ) + $this->years_between );
			
		}

		return $nextdate;
	}

	
	function get_nth_day_of_month( $curdate, $day_of_week, $week_of_month, $next_month_of_year) {
	
		$nextdate = $this->advance_date( $curdate, $next_month_of_year, 1 ); // advance to correct month
		
		$nextdate = IgniteWoo_Date_Utils::get_first_day_of_week_in_month( $nextdate, $day_of_week );

		if ( -1 == $week_of_month ) { // LAST WEEK
		
			$nextdate = IgniteWoo_Date_Utils::get_last_day_of_week_in_month( $nextdate, $day_of_week );
			
			return $nextdate;
			
		} else {
		
			$maybe_date = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $week_of_month-1) . ' weeks' );

			// if this doesn't exist, then try next month
			while ( date( 'n', $maybe_date ) != date( 'n', $nextdate ) ) {
			
				// advance again
				$next_month_of_year = $this->get_next_month_of_year( date( 'n', $nextdate ) );
				
				$nextdate = $this->advance_date( $nextdate, $next_month_of_year );
				
				$nextdate = IgniteWoo_Date_Utils::get_first_day_of_week_in_month( $curdate, $day_of_week );
				
				$maybe_date = strtotime( date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, $nextdate ) . ' + ' . ( $week_of_month-1) . ' weeks' );
			}

			return $maybe_date;
		}
	}
	

	function get_next_month_of_year( $curMonth) {
	
		foreach( $this->months_of_year as $month) {
		
			if ( $month > $curMonth)
				return $month;
		}

		return $this->months_of_year[0];
	}
}

