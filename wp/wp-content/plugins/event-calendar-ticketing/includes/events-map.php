<?php
/**

Google Map template

Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved

*/

if ( !defined('ABSPATH') )
	die();

global $ignitewoo_events, $post;

$event_settings = $ignitewoo_events->get_event_settings();

$post_settings = get_post_meta( $post->ID, '_ignitewoo_event_info', true );

?>

<style>
	.ignitewoo_gmap { 
		height: <?php echo is_numeric( $event_settings['maps_height'] ) ? "{$event_settings['maps_height']}px" : $event_settings['maps_height'] ?>; 
		width: <?php echo is_numeric( $event_settings['maps_width'] ) ? "{$event_settings['maps_width']}px" : $event_settings['maps_width'] ?>; 
		margin-bottom: 15px;
	}
</style>

<?php if ( !empty( $post_settings['venue_map'] ) && 'yes' == $post_settings['venue_map'] ) { ?>

<div id="ignitewoo_googlemaps" class="ignitewoo_gmap"></div>

<?php } ?>