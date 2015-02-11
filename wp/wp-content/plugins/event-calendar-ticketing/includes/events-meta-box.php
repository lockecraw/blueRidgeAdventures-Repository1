<?php
/**
* Events post meta box 
* Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved
*/

if ( !defined('ABSPATH') )
	die();

	global $post, $woocommerce, $event_info, $ignitewoo_events;

	$event_info = $ignitewoo_events->get_post_data();
	
	$event_info = wp_parse_args( $event_info, $event_defaults = array( 
			'event_venue' => array(),
			'event_primary_organizer' => array(),
			'event_speaker' => array(),
			'event_primary_sponsor' => array(),
			'session_speaker_id' => array(),
			'display_organizer' => '',
			'event_cost'	=> '',
			'venue_map' 	=> ''
		) );

	$event_settings  = get_option( 'ignitewoo_events_main_settings', false );

	if ( empty( $event_info['start_date'] ) ) { 

		$d = date( 'm/d/y', time() );

		if ( !empty( $event_settings['all_day_start'] ) )
			$t = $event_settings['all_day_start'];
		else
			$t = date( 'h:i a', time() );

		$event_info['start_date'] = $d . ' ' . $t;

	}


	if ( empty( $event_info['end_date'] ) ) { 

		$d = date( 'm/d/y', time() );

		if ( '' != $event_settings['all_day_end'] )
			$t = $event_settings['all_day_end'];
		else
			$t = date( 'h:i a', time() );

		$event_info['end_date'] = $d . ' ' . $t;
	}


// -----------------------------------------------

?>

<script> 

	jQuery( document ).ready( function() { 

		<?php 
			if ( empty( $event_info['recurrence']['type'] ) ) 
				$ign_e_type = 'Never';
			else
				$ign_e_type = $event_info['recurrence']['type'];
		?>

		var ignitewoo_event_recurr_type_val = "<?php echo $ign_e_type; ?>";

		jQuery( "#ignitewoo_event_recurr_type").val( ignitewoo_event_recurr_type_val );
		jQuery( "#ignitewoo_event_recurr_type").change();

		jQuery( "#recurrence_end_on" ).change();

		jQuery( ".ignitewoo_event_type_selector" ).change( function() { 

			if ( "single_session" == jQuery( this ).val() ) {
				jQuery( "#ignitewoo_multi_session_wrap" ).hide();
				jQuery( "#ignitewoo_single_session_wrap" ).show();
			} else { 
				jQuery( "#ignitewoo_single_session_wrap" ).hide();
				jQuery( "#ignitewoo_multi_session_wrap" ).show();
			}
		});

	});

</script>

<?php 

global $typenow;

if ( !empty( $typenow ) && 'product' != $typenow ) { 

	?>

	<input type="hidden" name="_ignitewoo_event" value="yes">

	<?php 
} 
?>


<div id="ignitewoo_events_wrap" class="options_group">

	<?php do_action('ignitewoo_events_eventform_top', $post->ID); ?>

	<div style="border-bottom: 1px solid #DFDFDF;">

		<h4 class="event_heading"><?php _e( 'Event Time &amp; Date', 'ignitewoo_events' ); ?></h4>

		<?php do_action( 'ignitewoo_events_pro_dates_desc' ) ?>

		<p class="form-field">
			<label><?php _e( 'Start date / time', 'ignitewoo_events' ); ?></label>
			<input class="event_datepicker" tabindex="<?php $this->tab_index(); ?>" type='text' id='start_date' name='ignitewoo_event_info[start_date]' value='<?php echo $event_info['start_date']; ?>' />
		</p>

		<p class="form-field">
			<label><?php _e( 'End date / time', 'ignitewoo_events' ); ?></label>
			<input class="event_datepicker"  tabindex="<?php $this->tab_index(); ?>" type='text' id='end_date' name='ignitewoo_event_info[end_date]' value='<?php echo $event_info['end_date']; ?>' />
		</p>

		<?php do_action( 'ignitewoo_events_pro_post_recurrence' ) ?>

	</div>

	<?php do_action( 'ignitewoo_events_pro_ticket_limits' ) ?>


	<style>
	#ignitewoo_single_session_wrap .chosen-container {
		width: 200px !important;
	}
	</style>

	<div style="border-bottom: 1px solid #DFDFDF;">

		<h4  class="event_heading"><?php _e( 'Event Details', 'ignitewoo_events'); ?></h4>

		<div id="ignitewoo_single_session_wrap"> 
			<?php
				$venues = new WP_Query( array( 'post_type' => 'event_venue', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) );
				
				$orgs = new WP_Query( array( 'post_type' => 'event_organizer', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) );
				
				$sponsors = new WP_Query( array( 'post_type' => 'event_sponsor', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) );
				$p = '';
			?>

				<p class="form-field">
					<label><?php _e( 'Venue', 'ignitewoo_events' ); ?></label>
					<?php if ( !isset( $venues->posts ) || count( $venues->posts ) <= 0 ) { ?>
						<?php _e( 'No venues exist. Before you can select a venue you must create and publish one', 'ignitewoo_events' ) ?>
					<?php } else { ?>
						<select class="chosen_select chosen" style="width: 250px !important" id="ignitewoo_event_venue_select" name="ignitewoo_event_info[event_venue][]">
							<option value=""><?php _e( 'Select a venue', 'ignitewoo_events' )?></option>
							<?php foreach ( $venues->posts as $p ) { ?>
								<option <?php if ( in_array( $p->ID, $event_info['event_venue'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
							<?php } ?>
						</select>
						<img class="help_tip" data-tip="<?php _e( ' Select the venue at which this event will take place ', 'ignitewoo_events' ) ?>" src="<?php echo $this->plugin_url ?>../assets/images/help.png" />
					<?php } ?>
				</p>
				

				<p class="form-field">
					<label><?php _e( 'Display Map', 'ignitewoo_events' ); ?></label>
					<input style="float:none; width:15px" tabindex="<?php $this->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[venue_map]' value='yes' <?php if ( isset( $event_info['venue_map'] ) && 'yes' == $event_info['venue_map'] ) echo 'checked="checked"'; ?> /> <?php _e( "Display a map to the venue on the event's page", 'ignitewoo_events' ) ?>
				</p>

				<p class="form-field">
					<label><?php _e( 'Display Details', 'ignitewoo_events' ); ?></label>
					<input style="float:none; width:15px" tabindex="<?php $this->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[display_organizer]' value='yes' <?php if ( isset( $event_info['display_organizer'] ) && 'yes' == $event_info['display_organizer'] ) echo 'checked="checked"'; ?> /> <?php _e( "Display the event organizer's contact info on individual event pages", 'ignitewoo_events' ) ?>
				</p>

				<?php do_action( 'ignitewoo_events_gcal_ical_map' ) ?>
				
				<p class="form-field">
					<label><?php _e( 'Event Organizers', 'ignitewoo_events' ); ?></label>
					<?php if ( !isset( $orgs->posts ) || count( $orgs->posts ) <= 0 ) { ?>
						<?php _e( 'No organizers exist. Before you can add organizers you must create and publish one', 'ignitewoo_events' ) ?>
					<?php } else { ?>
						<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_organizer_select" name="ignitewoo_event_info[event_primary_organizer][]">
							<?php foreach ( $orgs->posts as $p ) { ?>
								<option <?php if ( in_array( $p->ID, $event_info['event_primary_organizer'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
							<?php } ?>
						</select>
						<img class="help_tip" data-tip="<?php _e( ' Optionally select organizers that are coordinating the entire event ', 'ignitewoo_events' ) ?>" src="<?php echo $this->plugin_url ?>../assets/images/help.png" />
					<?php } ?>
				</p>

				<p class="form-field">
					<label><?php _e( 'Event Sponsors', 'ignitewoo_events' ); ?></label>
					<?php if ( !isset( $sponsors->posts ) || count( $sponsors->posts ) <= 0 ) { ?>
						<?php _e( 'No sponsors exist. Before you can add sponsors you must create and publish one', 'ignitewoo_events' ) ?>
					<?php } else { ?>
						<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_sponsor_select" name="ignitewoo_event_info[event_primary_sponsor][]">
							<?php foreach ( $sponsors->posts as $p ) { ?>
								<option <?php if ( in_array( $p->ID, $event_info['event_primary_sponsor'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
							<?php } ?>
						</select>
						<img class="help_tip" data-tip="<?php _e( ' Optionally select sponsors that are sponsoring the entire event ', 'ignitewoo_events' ) ?>" src="<?php echo $this->plugin_url ?>../assets/images/help.png" />
					<?php } ?>
				</p>

				<?php if ( !class_exists( 'IgniteWoo_Events_Pro' ) ) { ?>

					<?php $speakers = new WP_Query( array( 'post_type' => 'event_speaker', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) ); ?>

					<p class="form-field">
						<label><?php _e( 'Event Speakers', 'ignitewoo_events' ); ?></label>
						<?php if ( !isset( $speakers->posts ) || count( $speakers->posts ) <= 0 ) { ?>
							<?php _e( 'No speakers exist. Before you can add speakers you must create and publish one', 'ignitewoo_events' ) ?>
						<?php } else { ?>
							<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_speaker_select" name="ignitewoo_event_info[session_speaker_id][]">
								<?php foreach ( $speakers->posts as $p ) { ?>
									<option <?php if ( in_array( $p->ID, $event_info['session_speaker_id'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
								<?php } ?>
							</select>
							<img class="help_tip" data-tip="<?php _e( ' Optionally select speakers for the event ', 'ignitewoo_events' ) ?>" src="<?php echo $this->plugin_url ?>../assets/images/help.png" />
						<?php } ?>
					</p>
				<?php } ?>

				<?php if ( 'product' != $post->post_type ) { ?>
					<p class="form-field">
						<label><?php _e( 'Event Cost', 'ignitewoo_events' ); ?></label>
						<input style="width: 75px" type="text" value="<?php echo $event_info['event_cost'] ?>" name="ignitewoo_event_info[event_cost]">
					</p>

				<?php } ?>

				<?php do_action( 'ignitewoo_events_pro_printable_tickets' ) ?>

				<?php do_action( 'ignitewoo_events_pro_custom_forms' ) ?>

				<div style="border-bottom: 1px dotted #bbbbbb; height: 10px; margin-bottom: 20px;"></div>

				<?php do_action( 'ignitewoo_events_sessions_tracks' ) ?>

		</div>

	</div>

</div>