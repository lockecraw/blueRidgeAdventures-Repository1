<?php

/** Template tags */


// For use with Schema.org  data
function ign_event_date_to_iso( $date ) {

	$tz = get_option('timezone_string');
	
        if ( $tz ) {
        
                date_default_timezone_set( $tz );
                
                $datetime = date_create( $date );
                
		$datetime->setTimezone( new DateTimeZone('UTC') );
		
		$offset = $datetime->getOffset();
		
		return date('c', strtotime( $date ) );
	
	} else
		return date('c', strtotime( $s ) ) . 'Z';

}

function get_event_cost() { 
	global $post, $ignitewoo_events;
	
	// Let WooCom put the price on the product page? 
	if ( 'product' == $post->post_type )
		return null; 
		
	$cost = get_post_meta( $post->ID, '_price', true );

	if ( empty( $cost ) )
		return null;
		
	// Use WooCommerce currency symbol and symbol placement settings
	if ( class_exists( 'Woocommerce' ) ) { 
	
		$cs = get_woocommerce_currency_symbol();
		$pos = get_option( 'woocommerce_currency_pos' );

	
	} else {
	
		$cs = $ignitewoo_events->settings['currency_symbol'];
		$pos = $ignitewoo_events->settings['symbol_location'];
	}

	if ( 'left' == $pos ) 
		$cost = $cs . $cost;
	else if ( 'right' == $pos )
		$cost = $cost . $cs;

	return $cost;
	
}