<?php
	global $ignitewoo_events, $post, $woocommerce, $ignitewoo_events_admin;

	$event_info = $ignitewoo_events->get_post_data();
	
	$event_defaults = array( 
		'max_ticket_interval' => '',
		'max_ticket_qty' => '',
		'max_ticket_interval_type' => '',
		'max_ticket_restriction' => '',
	);
	
	$event_info = wp_parse_args( $event_info, $event_defaults );
?>

	<div style="border-bottom: 1px solid #DFDFDF;">

		<?php 

			if ( empty( $event_info['max_ticket_qty'] ) ) $event_info['max_ticket_qty'] = '';
			if ( empty( $event_info['max_ticket_interval'] ) ) $event_info['max_ticket_interval'] = '';
			if ( empty( $event_info['max_ticket_interval_type'] ) ) $event_info['max_ticket_interval_type'] = '';

		?>

		<h4  class="event_heading"><?php _e( 'Ticket Restrictions', 'ignitewoo_events'); ?></h4>

		<p class="form-field">
			<label><?php _e( 'Max Quantity', 'ignitewoo_events' ); ?></label>
			<input style="float:none; width: 15px" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='checkbox' name='ignitewoo_event_info[max_ticket_restriction]' value='yes' <?php echo checked( $event_info['max_ticket_restriction'], 'yes', false )?> /> 
			<?php _e( 'Restrict buyers to purchasing no more than', 'ignitewoo_events' ) ?> 
			<input style="float:none; width:50px;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='text' name='ignitewoo_event_info[max_ticket_qty]' value='<?php echo $event_info['max_ticket_qty'] ?>' /> 
			<?php _e( 'tickets every', 'ignitewoo_events' ) ?> 
			<input style="float:none; width:50px;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='text' name='ignitewoo_event_info[max_ticket_interval]' value='<?php echo $event_info['max_ticket_interval'] ?>' /> 
			<select name="ignitewoo_event_info[max_ticket_interval_type]" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>"> 
				<option value="hour" <?php echo selected( $event_info['max_ticket_interval_type'], 'hour', false ) ?>><?php _e( 'hour(s)', 'ignitewoo_events' ) ?></option>
				<option value="day" <?php echo selected( $event_info['max_ticket_interval_type'], 'day', false ) ?>><?php _e( 'days(s)', 'ignitewoo_events' ) ?></option>
				<option value="month" <?php echo selected( $event_info['max_ticket_interval_type'], 'month', false ) ?>><?php _e( 'month(s)', 'ignitewoo_events' ) ?></option>
				<option value="year" <?php echo selected( $event_info['max_ticket_interval_type'], 'year', false ) ?>><?php _e( 'year(s)', 'ignitewoo_events' ) ?></option>
			</select> 
			<p class="description">
				<?php _e( 'When used, all restriction setting fields above are required. Also note than this is a "best effort" setting because purchases are tracked by email address.', 'ignitewoo_events' )?>
			</p>

		</p>

	</div>