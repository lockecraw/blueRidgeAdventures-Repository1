<?php
/**
* Event Taxonomy Meta
* Copyright (c) IgniteWoo.com - All Rights Reserved
*/

if ( !defined('ABSPATH') )
	die();

class IgniteWoo_Events_Taxonomy_Meta {

	function __construct() { 

		add_action( 'admin_enqueue_styles', array( &$this, 'meta_box_css' ) );

		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ), -1 );

		add_action( 'save_post', array( &$this, 'process_meta_box' ), 1, 2 );

	}


	function process_meta_box( $post_id, $post ) {
		global $typenow, $post;

		if ( empty( $post ) ) 
			return;
		
		if ( 
			!in_array( $post->post_type, array( 'event_forms', 'event_organizer', 'event_venue', 'event_speaker', 'event_sponsor' ) ) 
			&& 
			!in_array( $typenow, array( 'event_forms', 'event_organizer', 'event_venue', 'event_speaker', 'event_sponsor' ) ) 
		    )
			return;

		$this->process_generic_meta_box( $post_id, $post );

	}


	function generic_meta_box( $post_id, $post ) {
		global $post;
		
		require_once( dirname( __FILE__ ) . '/countries.php' );
		
		$countries = new IgniteWoo_Event_Countries();

		?>
		<style>
			#ignitewoo_events_custom_table th { width: 150px; text-align: right; padding-right: 15px; } 
		</style>
		<table id="ignitewoo_events_custom_table" style="line-height: 2em;" >
		<tr class="form-field">
			<th scope="row" valign="top"><label for="tag-slug"><?php _e( 'Street Address', 'ignitewoo_events' )?></label></th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_address', true ); ?>" id="tag-slug" name="generic_address">

			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="tag-slug"><?php _e( 'City', 'ignitewoo_events' )?></label></th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_city', true ); ?>" id="tag-slug" name="generic_city">
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				    <label for="tag-slug"><?php _e( 'State / Country', 'ignitewoo_events' )?></label>
			</th>
			<td>
				    <?php 

						$c_data = get_post_meta( $post->ID, '_generic_country_state', true );

						if ( isset( $c_data ) && false != $c_data )
							$c_data = explode( ':', $c_data);
						else 
							$c_data = array();

						if ( isset( $c_data[0] ) )	
							$country = $c_data[0];
						else
							$country = '';

						if ( count( $c_data ) > 1 ) 
							$state = $c_data[1];

						if ( ( isset( $state ) && '' == $state ) || empty( $state ) )
							$state = '*';

				    ?>
				    <select style="width: 250px" class="chosen" name="generic_country_state" data-placeholder="<?php _e( 'Choose a country&raquo;', 'ignitewoo_events' ); ?>" title="<?php _e( 'Country / State', 'ignitewoo_events' )?>">
						<?php echo $countries->country_dropdown_options( $country, $state ); ?>
				    </select>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				    <label for="tag-slug"><?php _e( 'Postal Code', 'ignitewoo_events' )?></label>
			</th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_postalcode', true ); ?>" id="tag-slug" name="generic_postalcode">
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				    <label for="tag-slug"><?php _e( 'Phone Number', 'ignitewoo_events' )?></label>
			</th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_phone', true ); ?>" id="tag-slug" name="generic_phone">
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				    <label for="tag-slug"><?php _e( 'Web Site', 'ignitewoo_events' )?></label>
			</th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_website', true ); ?>" id="tag-slug" name="generic_website">
			</td>
		<tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				    <label for="tag-slug"><?php _e( 'Email Address', 'ignitewoo_events' )?></label>
			</th>
			<td>
				    <input type="text" size="40" value="<?php echo get_post_meta( $post->ID, '_generic_email', true ); ?>" id="tag-slug" name="generic_email">
			</td>
		</tr>
		</table>
		<?php
	}



	function process_generic_meta_box( $post_id, $post ) { 

		update_post_meta( $post_id, '_generic_address', $_POST['generic_address'] );
		update_post_meta( $post_id, '_generic_city', $_POST['generic_city'] );
		update_post_meta( $post_id, '_generic_country_state', $_POST['generic_country_state'] );
		update_post_meta( $post_id, '_generic_postalcode', $_POST['generic_postalcode'] );
		update_post_meta( $post_id, '_generic_email', $_POST['generic_email'] );
		update_post_meta( $post_id, '_generic_phone', $_POST['generic_phone'] );
		update_post_meta( $post_id, '_generic_website', $_POST['generic_website'] );

	}


	/** Forms */
	// This section based on code by WooThemes. Copyright (c) - 2011 WooThemes, 
	// Copyright (c) 2012 - IgniteWoo.com

	function add_meta_box() {

		add_meta_box( 'ignitewoo-event-organizer', __( 'Organizer Details', 'ignitewoo_events' ), array( &$this, 'generic_meta_box' ), 'event_organizer', 'normal', 'high' );

		add_meta_box( 'ignitewoo-event-venue', __( 'Venue Details', 'ignitewoo_events' ), array( &$this, 'generic_meta_box' ), 'event_venue', 'normal', 'high' );

		add_meta_box( 'ignitewoo-event-speaker', __( 'Speaker Details', 'ignitewoo_events' ), array( &$this, 'generic_meta_box' ), 'event_speaker', 'normal', 'high' );

		add_meta_box( 'ignitewoo-event-sponsor', __( 'Sponsor Details', 'ignitewoo_events' ), array( &$this, 'generic_meta_box' ), 'event_sponsor', 'normal', 'high' );

	}

	
	function meta_box_css() {

		global $typenow;

		if ( 'event_forms' == $typenow || 'product' == $typenow ) 
			wp_enqueue_style( 'ignitewoo_form_fields_css', plugins_url( basename( dirname(__FILE__) ) ) . '/assets/css/admin.css' );

	}

}
