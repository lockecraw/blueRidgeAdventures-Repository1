<?php
	global $wpdb, $ignitewoo_events, $post, $woocommerce, $ignitewoo_events_pro;

	$event_info = $ignitewoo_events->get_post_data();
	
	$event_defaults = array( 

			'type' => '',
			'end' => '',
			'end-type' => '',
			'end-count' => '',
			'custom-type' => '',
			'custom-interval' => '',
			'custom-week-day' => array(),
			'custom-month-number' => '',
			'custom-month-day' => '',
			'custom-year-month' => array(),
			'custom-year-month-day' => '',
			'custom-year-month-number' => '',

	);
	
	if ( !isset( $event_info ) || !isset( $event_info['recurrence'] ) )
		$event_info = array( 'recurrence' => array() );
		
	$event_info['recurrence'] = wp_parse_args( $event_info['recurrence'], $event_defaults );
		


	?>

		<div class="form-field">
			<label style="margin-left: 10px;"><?php _e( 'Recurrence', 'ignitewoo_events' ); ?></label>

			<table id="recurrence" style="margin-bottom: 20px">
				<tr class="recurrence-row">
					<td></td>
					<td colspan="2">
						<?php _e( 'Each recurring event time span will be the same as Start / End dates indicated above.', 'ignitewoo_events' ) ?>
					</td>
				</tr>
				<tr class="recurrence-row">
					<td></td>
					<td>
						<input type="hidden" value="false" name="is_recurring">
						<input type="hidden" value="" name="recurrence_action">
						
						<select id="ignitewoo_event_recurr_type" name="ignitewoo_event_info[recurrence][type]">
						
							<?php
							if ( empty( $event_info['recurrence']['type'] ) || 'None' == $event_info['recurrence']['type'] ) 
								$selected = 'selected="selected"';
							else 
								$selected = '';
							?>
						
							<option <?php echo $selected ?> value="None" data-plural=""><?php _e( 'Never', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Every Day',  $event_info['recurrence']['type'] ); ?> value="Every Day" data-plural="days" data-single="day"><?php _e( 'Every Day', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Every Week',  $event_info['recurrence']['type'] ); ?> value="Every Week" data-plural="weeks" data-single="week"><?php _e( 'Every Week', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Every Month',  $event_info['recurrence']['type'] ); ?> value="Every Month" data-plural="months" data-single="month"><?php _e( 'Every Month', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Every Year',  $event_info['recurrence']['type'] ); ?> value="Every Year" data-plural="years" data-single="year"><?php _e( 'Every Year', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Custom',  $event_info['recurrence']['type'] ); ?> value="Custom" data-plural="events" data-single="event"><?php _e( 'Custom', 'ignitewoo_events' )?></option>
							
						</select>
						<span style="display: none;" id="recurrence-end">
							&mdash; <?php _e( 'End', 'ignitewoo_events')?> 
							<select id="recurrence_end_on" name="ignitewoo_event_info[recurrence][end-type]">
							
								<option <?php selected( 'On',  $event_info['recurrence']['end-type'] ); ?> value="On"><?php _e( 'On', 'ignitewoo_events' )?></option>
								
								<option <?php selected( 'After',  $event_info['recurrence']['end-type'] ); ?> value="After"><?php _e( 'After', 'ignitewoo_events' )?></option>
								
							</select>
							
							<input type="text" style="display: none;" value="<?php echo  $event_info['recurrence']['end'] ?>" id="recurrence_end" name="ignitewoo_event_info[recurrence][end]" class="recur_date" placeholder="2012-10-02" autocomplete="off">
							
							<span style="" id="rec-count"><input type="text" style="width: 40px;" value="<?php echo  $event_info['recurrence']['end-count'] ?>" id="recurrence_end_count" name="ignitewoo_event_info[recurrence][end-count]" autocomplete="off"> <span id="occurence-count-text"><?php _e( 'event', 'ignitewoo_events' )?></span></span>
							
							<span class="rec-error" id="rec-end-error" style="display: none;"><?php _e( 'You must select a recurrence end date', 'ignitewoo_events' )?></span>
						</span>
					</td>
				</tr>
				<tr style="display: none;" id="custom-recurrence-frequency" class="recurrence-row">
					<td></td>
					<td>
						<?php _e( 'Frequency', 'ignitewoo_events' )?> 
						<select name="ignitewoo_event_info[recurrence][custom-type]" style="float:none">
						
							<option <?php selected( 'Daily',  $event_info['recurrence']['custom-type'] ); ?> data-tablerow="" data-plural="Day(s)" value="Daily"><?php _e( 'Daily', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Weekly',  $event_info['recurrence']['custom-type'] ); ?> data-tablerow="#custom-recurrence-weeks" data-plural="Week(s) on:" value="Weekly"><?php _e( 'Weekly', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Monthly',  $event_info['recurrence']['custom-type'] ); ?>  data-tablerow="#custom-recurrence-months" data-plural="Month(s) on the:" value="Monthly"><?php _e( 'Monthly', 'ignitewoo_events' )?></option>
							
							<option <?php selected( 'Yearly',  $event_info['recurrence']['custom-type'] ); ?> data-tablerow="#custom-recurrence-years" data-plural="Year(s) on:" value="Yearly"><?php _e( 'Yearly', 'ignitewoo_events' )?></option>
							
						</select>
						
						<?php _e( 'Every', 'ignitewoo_events' )?> <input style="float:none" type="text" value="<?php echo $event_info['recurrence']['custom-interval'] ?>" name="ignitewoo_event_info[recurrence][custom-interval]"> <span id="recurrence-interval-type"><?php _e( 'Year(s) on:', 'ignitewoo_events' )?></span>
						
						<input type="hidden" value="Year(s) on:" name="ignitewoo_event_info[recurrence][custom-type-text]">
						
						<input type="hidden" value="event" name="ignitewoo_event_info[recurrence][occurrence-count-text]">
						
						<span class="rec-error" id="rec-days-error" style="display: none;"><?php _e( 'Frequency of recurring event must be a number', 'ignitewoo_events' )?></span>

					</td>
				</tr>
						<tr style="display: none;" id="custom-recurrence-weeks" class="custom-recurrence-row">
					<td></td>
					<td>
						<label><input <?php if ( in_array( '1', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?> type="checkbox" value="1" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Monday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '2', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?> type="checkbox" value="2" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Tuesday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '3', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?>  type="checkbox" value="3" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Wednesday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '4', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?>  type="checkbox" value="4" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Thursday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '5', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?> type="checkbox" value="5" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Friday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '6', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?> type="checkbox" value="6" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Saturday', 'ignitewoo_events' )?></label>
						
						<label><input <?php if ( in_array( '7', $event_info['recurrence']['custom-week-day'] ) ) echo 'checked="checked"' ?>  type="checkbox" value="7" name="ignitewoo_event_info[recurrence][custom-week-day][]"> <?php _e( 'Sunday', 'ignitewoo_events' )?></label>
						
					</td>
				</tr>
				<tr style="display: none;" id="custom-recurrence-months" class="custom-recurrence-row">
					<td></td>
					<td>
						<div id="recurrence-month-on-the">
							<select name="ignitewoo_event_info[recurrence][custom-month-number]">
							
								<option <?php selected( 'First',  $event_info['recurrence']['custom-month-number'] ); ?> value="First"><?php _e( 'First', 'ignitewoo_events' )?></option>
								
								<option <?php selected( 'Second',  $event_info['recurrence']['custom-month-number'] ); ?> value="Second"><?php _e( 'Second', 'ignitewoo_events' )?></option>
								
								<option <?php selected( 'Third',  $event_info['recurrence']['custom-month-number'] ); ?> value="Third"><?php _e( 'Third', 'ignitewoo_events' )?></option>
								
								<option <?php selected( 'Fourth',  $event_info['recurrence']['custom-month-number'] ); ?> value="Fourth"><?php _e( 'Fourth', 'ignitewoo_events' )?></option>
								
								<option <?php selected( 'Last',  $event_info['recurrence']['custom-month-number'] ); ?>  value="Last"><?php _e( 'Last', 'ignitewoo_events' )?></option>
								
								<option value="">--</option>
								
									<?php for( $i=1; $i < 31; $i++ ) { ?>
										<option <?php selected( $i,  $event_info['recurrence']['custom-month-number'] ); ?> value="<?php echo $i ?>"><?php echo $i ?></option>
									<?php } ?>
							</select>
							<select style="display: inline" name="ignitewoo_event_info[recurrence][custom-month-day]">
								<option <?php selected( '1',  $event_info['recurrence']['custom-month-day'] ); ?> value="1"><?php _e( 'Monday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '2',  $event_info['recurrence']['custom-month-day'] ); ?> value="2"><?php _e( 'Tuesday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '3',  $event_info['recurrence']['custom-month-day'] ); ?> value="3"><?php _e( 'Wednesday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '4',  $event_info['recurrence']['custom-month-day'] ); ?> value="4"><?php _e( 'Thursday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '5',  $event_info['recurrence']['custom-month-day'] ); ?> value="5"><?php _e( 'Friday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '6',  $event_info['recurrence']['custom-month-day'] ); ?> value="6"><?php _e( 'Saturday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '7',  $event_info['recurrence']['custom-month-day'] ); ?> value="7"><?php _e( 'Sunday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '-',  $event_info['recurrence']['custom-month-day'] ); ?> value="-">--</option>
								
								<option <?php selected( '-1', $event_info['recurrence']['custom-month-day'] ); ?> value="-1"><?php _e('Day', 'ignitewoo_events' )?></option>
								
							</select>
						</div>
					</td>
				</tr>
				<tr style="display: none;" id="custom-recurrence-years" class="custom-recurrence-row">
					<td></td>
					<td>
						<div>
							<label><input <?php if ( in_array( '1', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="1" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Jan', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '2', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="2" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Feb', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '3', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="3" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Mar', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '4', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="4" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Apr', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '5', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="5" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('May', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '6', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="6" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Jun', 'ignitewoo_events' )?></label>
							
						</div>
						<div style="clear:both"></div>
						<div>
							<label><input <?php if ( in_array( '7', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="7" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Jul', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '8', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="8" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Aug', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '9', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="9" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Sep', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '10', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="10" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Oct', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '11', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="11" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Nov', 'ignitewoo_events' )?></label>
							
							<label><input <?php if ( in_array( '12', $event_info['recurrence']['custom-year-month'] ) ) echo 'checked="checked"' ?> type="checkbox" value="12" name="ignitewoo_event_info[recurrence][custom-year-month][]"> <?php _e('Dec', 'ignitewoo_events' )?></label>
							
						</div>
						<div style="clear:both"></div>				
						<div>
							<input type="checkbox" value="1" name="ignitewoo_event_info[recurrence][custom-year-filter]">
							<?php _e('On the:', 'ignitewoo_events' )?>	
							<select name="ignitewoo_event_info[recurrence][custom-year-month-number]">
							
								<option <?php selected( '1',  $event_info['recurrence']['custom-year-month-number'] ); ?> value="1"><?php _e('First', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '2',  $event_info['recurrence']['custom-year-month-number'] ); ?> value="2"><?php _e('Second', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '3',  $event_info['recurrence']['custom-year-month-number'] ); ?> value="3"><?php _e('Third', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '4',  $event_info['recurrence']['custom-year-month-number'] ); ?> value="4"><?php _e('Fourth', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '-1',  $event_info['recurrence']['custom-year-month-number'] ); ?> value="-1"><?php _e('Last', 'ignitewoo_events' )?></option>
								
							</select>
							<select name="ignitewoo_event_info[recurrence][custom-year-month-day]">
								<option <?php selected( '1',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="1"><?php _e('Monday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '2',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="2"><?php _e('Tuesday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '3',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="3"><?php _e('Wednesday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '4',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="4"><?php _e('Thursday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '5',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="5"><?php _e('Friday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '6',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="6"><?php _e('Saturday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '7',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="7"><?php _e('Sunday', 'ignitewoo_events' )?></option>
								
								<option <?php selected( '-',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="-">--</option>
								
								<option <?php selected( '-1',  $event_info['recurrence']['custom-year-month-day'] ); ?> value="-1"><?php _e('Day', 'ignitewoo_events' )?></option>
								
							</select>
						</div>
					</td>
				</tr>
			</table>

			<?php 

				$sql = 'select meta_value from ' . $wpdb->postmeta . ' where post_id = ' . $post->ID . ' and meta_key="_ignitewoo_event_start" order by CAST( meta_value as DATE ) ASC';

				$dates = $wpdb->get_results( $sql );

				$duration = get_post_meta( $post->ID, '_ignitewoo_event_duration', true ); 

				$ignitewoo_events_pro->load_rules();

				$main_settings = get_option( 'ignitewoo_events_main_settings', false ); 

				$date_format = empty( $main_settings['date_format'] ) ? 'M j, Y' : $main_settings['date_format'];

				$time_format = empty( $main_settings['time_format'] ) ? 'h:i a' : $main_settings['time_format'];

			?> 

			<div id="event_sched_list" style="display:none">

				<label style="margin-left: 10px;"><?php _e( 'Schedule / Availability', 'ignitewoo_events' ); ?></label>

				<table id="recurrence" style="margin-bottom: 20px">
					<tr>
						<td colspan="3" style="font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 10px">
							<?php _e( 'Auto-generated Schedule','ignitewoo_events' )?>
							<img class="help_tip" data-tip="<?php _e( ' This may change after you set a schedule and update the product. To adjust price & stock view the Variations tab', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
						</td>
					</tr>

					<tr>
						<th style="text-align:left"><?php _e( 'Start Date', 'ignitewoo_events' )?></th>
						<th style="text-align:left"><?php _e( 'End Date', 'ignitewoo_events' )?></th>
					</tr>

					<?php if ( $dates ) foreach( $dates as $d ) { ?>

					<?php 
						if ( strtotime( $d->meta_value ) < current_time('timestamp' ) )  { 
							$style = 'font-style:italic; text-decoration:line-through;'; 
							$expired = true;
						} else { 
							$style = ''; 
							$expired = false;
						}
					?>

					<tr>
						<td style="padding-right: 20px;" >
							<span style="<?php echo $style ?>"><?php echo date( $date_format . ' ' . $time_format, strtotime( $d->meta_value ) ) ?></span>
						</td>

						<td style="padding-right: 20px;" >
							<span style="<?php echo $style ?>"><?php echo date( $date_format . ' ' . $time_format, strtotime( $d->meta_value ) + $duration ); ?></span>
							<?php 
								if ( $expired )
									echo ' &nbsp; - <em>( ' . __( 'Expired', 'ignitewoo_events' ) . ' )</em>';
							?>
						</td>
					</tr>
					<?php } ?>
				</table>

			</div>

		</div>