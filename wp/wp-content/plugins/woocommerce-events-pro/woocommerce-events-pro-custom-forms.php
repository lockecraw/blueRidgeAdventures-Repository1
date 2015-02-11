<?php
	global $wpdb, $ignitewoo_events, $woocommerce, $ignitewoo_events_admin;

	$event_info = $ignitewoo_events->get_post_data();

	$event_defaults = array( 
		'event_form' => array(),
		'event_form_position' => '',
		'event_form_show_title' => '',

	);
	
	$event_info = wp_parse_args( $event_info, $event_defaults );
	

?>
		<div style="border-bottom: 1px dotted #bbbbbb; height: 10px; margin-bottom: 20px;"></div>

		<p class="form-field">
			<label><?php _e( 'Include Forms', 'ignitewoo_events' ); ?></label>

			<?php
				$forms = new WP_Query( array( 'post_type' => 'event_forms', 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) );

				if ( !$forms || !$forms->have_posts() )
					$forms = false;
			?>

			<?php if ( !$forms ) { ?>

				<?php _e( sprintf( 'You have not created any custom events forms. If you would like to do so visit the <a href="%s" target="_blank">Event Forms</a> page', admin_url( 'edit.php?post_type=event_forms' ) ), 'ignitewoo_events' ); ?>

			<?php } else { ?>

				<?php $help = __( 'You can optionally select a form to be included on the product page to collect additional information when someone purchases attendance at this event', 'ignitewoo_events' ); ?>

				<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_form_select" name="ignitewoo_event_info[event_form][]">
					<?php foreach ( $forms->posts as $p ) { ?>
						<option <?php if ( in_array( $p->ID, $event_info['event_form'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
					<?php } ?>
				</select>
				<img class="help_tip" data-tip="<?php echo $help ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
			<?php } ?>
		</p>

		<div class="form-field">
			<label style="margin-left: 10px"><?php _e( 'Form Position', 'ignitewoo_events' ); ?></label>
			<table style="padding-left: 5px">
			<tr><td><input style="float:none;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='radio' id='' name='ignitewoo_event_info[event_form_position]' value='before_button' <?php if ( 'before_button' == $event_info['event_form_position'] || '' == $event_info['event_form_position'] ) echo 'checked="checked"' ?> /> <?php _e( 'Before "Add to Cart" button', 'ignitewoo_events' ) ?></td></tr>
			<tr><td><input style="float:none;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='radio' id='' name='ignitewoo_event_info[event_form_position]' value='after_button' <?php if ( 'after_button' == $event_info['event_form_position'] || '' == $event_info['event_form_position'] ) echo 'checked="checked"' ?> /> <?php _e( 'After "Add to Cart" button', 'ignitewoo_events' ) ?></td></tr>
			<tr><td><input style="float:none;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='radio' id='' name='ignitewoo_event_info[event_form_position]' value='after_description' <?php if ( 'after_description' == $event_info['event_form_position'] ) echo 'checked="checked"' ?> /> <?php _e( 'After product description', 'ignitewoo_events' ) ?></td></tr>
			</table>
		</div>

		<p class="form-field">
			<label><?php _e( 'Show Form Title', 'ignitewoo_events' ); ?></label>
			<input style="float:none;" tabindex="<?php $ignitewoo_events_admin->tab_index(); ?>" type='checkbox' id='' name='ignitewoo_event_info[event_form_show_title]' value='yes' <?php if ( 'yes' == $event_info['event_form_show_title'] ) echo 'checked="checked"' ?> /> <?php _e( 'Check this box to show the form title above the form', 'ignitewoo_events' ) ?>
		</p>
