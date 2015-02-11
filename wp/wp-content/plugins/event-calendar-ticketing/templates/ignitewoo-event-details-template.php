<?php 
/**

Event details template

Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved

IMPORTANT: This template is designed to work with regular events AND event ticket sales as products in Woocommerce

FIXME: MAYBE MAKE A DIFFERENT TEMPLATE FOR PRODUCTS SO IT CAN BE STYLED DIFFERENTLY


*/

if ( !defined( 'ABSPATH' ) )
	die;

global $post, $ignitewoo_events, $ignitewoo_events_pro;

$starts = get_post_meta( $post->ID, '_ignitewoo_event_start', false );

asort( $starts );

$ends = get_post_meta( $post->ID, '_ignitewoo_event_end', false );

asort( $ends );

$main_settings = get_option( 'ignitewoo_events_main_settings', false ); 

$date_format = empty( $main_settings['date_format'] ) ? 'M j, Y' : $main_settings['date_format'];
$time_format = empty( $main_settings['time_format'] ) ? 'h:i a' : $main_settings['time_format'];

// Might return empty if this is a WooEvents Pro ticketed event
$cost = get_event_cost();

?>

<?php // Wrapper for microformats ?>
<div itemscope itemtype="http://schema.org/Event">
 
	<?php // For Schema.org markup since we cannot intercept WP writing the title on the page, display hidden ?>
	<span itemprop="name" style="display:none"><?php the_title() ?></span>
 
	<?php // ============ EVENT DATES ============== ?>

	<?php if ( 'ignitewoo_event' == $post->post_type ) { ?>
	
		<span class="summary">
		<?php 
			// Remove the plugin's own action hook otherwise an infinity loop will result. 
			remove_action( 'the_content', array( $ignitewoo_events, 'the_content' ), 5, 1 );
			
			if ( isset( $this->settings['use_shortcode'] ) && 'yes' != $this->settings['use_shortcode'] )
				the_content(); 
		?>
		</span>
		
	<?php } ?>
	
	<?php if ( !empty( $cost ) ) { ?>
	
		<div class="ignitewoo_event_cost"> 
		
			<?php _e( 'Cost', 'ignitewoo_events' )?>: <span itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" class="price"><?php echo $cost ?></span></span>
		
		</div>
	
	
	<?php } ?>
	
	<table class="ignitewoo_event_details event_dates">

		<?php if ( $starts && $ends ) { ?>

			<?php 
				if ( method_exists( $ignitewoo_events_pro, 'load_rules' ) )
					$ignitewoo_events_pro->load_rules();

				$duration = get_post_meta( $post->ID, '_ignitewoo_event_duration', true ); 
			?>

			<tr>
				<td colspan="3" class="ignitewoo_event_venue" colspan="2">
					<?php _e( 'Event Dates', 'ignitewoo_events' ) ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Starts', 'ignitewoo_events' ) ?></th>
				
				<th><?php _e( 'Ends', 'ignitewoo_events' ) ?></th>
				
				<?php if ( isset( $data['venue_google_calendar_link'] ) && 'yes' == $data['venue_google_calendar_link'] && ( isset( $data['venue_ical_calendar_link'] ) && 'yes' == $data['venue_ical_calendar_link'] ) ) { ?>
				
				<th><?php _e( 'Add To', 'ignitewoo_events' ) ?></th>
				
				<?php } ?>
			</tr>

			<?php foreach( $starts as $s ) { ?>

			<tr>
				<?php 
					if ( method_exists( $ignitewoo_events_pro, 'load_rules' ) )
						$end_date = date( IgniteWoo_Date_Series_Rules::DATE_FORMAT, strtotime( $s ) + $duration ); 
					else
						$end_date = get_post_meta( $post->ID, '_ignitewoo_event_end', true );

					if ( strtotime( $s ) < current_time('timestamp' ) )  { 
						$style = 'font-style:italic; text-decoration:line-through;'; 
						$expired = true;
					} else { 
						$style = ''; 
						$expired = false;
					}

				?>

				<td>
				
					<meta itemprop="startDate" content="<?php echo ign_event_date_to_iso( $s ) ?>">
					<span style="<?php echo $style ?>"><?php echo date( $date_format, strtotime( $s ) ); ?> <?php _e( 'at', 'ignitewoo_events' ) ?> <?php echo date( $time_format, strtotime( $s ) );?></span>
					</meta>
					
				</td>

				<td>
					<meta itemprop="endDate" content="<?php echo ign_event_date_to_iso( $end_date ) ?>">
					<span style="<?php echo $style ?>"><?php echo date( $date_format, strtotime( $end_date ) ) ?> <?php _e( 'at', 'ignitewoo_events' ) ?> <?php echo date( $time_format, strtotime( $end_date ) ); ?></span>
					</meta>
				</td>

				<td>
					<?php // For WooEvents Pro support ?>
					
					<?php if ( !$expired && ( isset( $data['venue_google_calendar_link'] ) && 'yes' == $data['venue_google_calendar_link'] ) ) {

						if ( method_exists( $ignitewoo_events_pro, 'gcal_link' ) )
							$gcal_link =  $ignitewoo_events_pro->gcal_link( $post->ID, $s, $end_date );

						if ( isset( $gcal_link ) && '' != $gcal_link ) { 
							?>

							<span class="ignitewoo_event_google_calendar_link"><a title=" <?php _e( 'Add to Google Calendar', 'ignitewoo_events' )?> " href="<?php echo $gcal_link ?>" target="_blank"><?php _e( 'gCal', 'ignitewoo_events' )?></a></span>

							<?php 
						}
					} ?>

					<?php // For WooEvents Pro support ?>

					<?php if ( !$expired && ( isset( $data['venue_ical_calendar_link'] ) && 'yes' == $data['venue_ical_calendar_link'] ) ) {

						if ( method_exists( $ignitewoo_events_pro, 'ical_link' ) )
							$ical_link =  $ignitewoo_events_pro->ical_link( $post->ID, $s, $end_date );

						if ( isset( $ical_link ) && '' != $ical_link ) { 
							?>

							<span class="ignitewoo_event_ical_calendar_link"><a title=" <?php _e( 'Add to Your Calendar', 'ignitewoo_events' )?> " href="<?php echo $ical_link ?>"><?php _e( 'iCal', 'ignitewoo_events' )?></a></span>

							<?php 
						}
					} ?>

				</td>
			</tr>

			<?php } ?>

		<?php } ?>


	</table>



<?php // ======= Venue Details ======== ?>

<?php

if ( $venues->have_posts() ) while ( $venues->have_posts() ) { 

	$venues->the_post();

	$venue_meta = get_post_custom( $post->ID, true );

?>


	<table class="ignitewoo_event_details venue">

		<tr>
			<td class="ignitewoo_event_venue" colspan="2">
				<?php _e( 'Venue Details', 'ignitewoo_events' ) ?>
			</td>
		</tr>

		<tr>

			<td class="event_thumbs" itemprop="image" style="vertical-align:top; width: 33%">
				<a title="<?php the_title() ?>" href="<?php echo the_permalink() ?>">
				<?php the_post_thumbnail( 'thumbnail' ) ?>
				</a>
			</td>

			<td style="vertical-align:top">

				<div itemscope itemtype="http://schema.org/LocalBusiness">
				<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				
				<table style="width:100%">

					<?php if ( '' != get_the_title( $post->ID ) ) { ?>

						<tr>
							<td><span itemprop="name"><?php the_title() ?></span></td>
						</tr>

					<?php } ?>

					<?php if ( '' != $venue_meta['_generic_address'][0] ) { ?>

						<tr>
							<td>
								<span itemprop="streetAddress"><?php echo $venue_meta['_generic_address'][0] ?></span>
							</td>
						</tr>

					<?php } ?>


					<?php if ( '' != $venue_meta['_generic_city'][0] ) { ?>

						<tr>
							<td>
	
								<meta itemprop="addressLocality">
									<?php 
									echo $venue_meta['_generic_city'][0] . ', ';
									?>
								</meta>
								
								<meta itemprop="addressRegion">
									<?php
									if ( '' != $venue_meta['_generic_country_state'][0] ) { 

										$x = explode( ':', $venue_meta['_generic_country_state'][0] );

										if ( count( $x ) > 1 ) 
											echo $x[1]. ', '; // state

										echo $x[0];
										
										if ( !empty( $venue_meta['_generic_postalcode'][0] ) )
											echo ' ' . $venue_meta['_generic_postalcode'][0];

									}

									?>
								</meta>

							</td>
						</tr>

					<?php } ?>


					<?php if ( '' != $venue_meta['_generic_phone'][0] ) { ?>
						<tr>
							<td>
							<span itemprop="telephone">
								<?php echo $venue_meta['_generic_phone'][0] ?>
							</span>
							</td>
						</tr>
					<?php } ?>


					<?php if ( '' != $venue_meta['_generic_email'][0] ) { ?>

						<?php 
						// Obscure the email address so that spammer's Web page scrapers can find it easily. Javascript unobscures it after the page loads. So scrapers won't get a useful mailto:// link to scrape out of the page. 
						$email_addy = $this->obscure_email_address( $venue_meta['_generic_email'][0] ); 
						?>

						<tr>
							<td><span itemprop="email"><?php echo $email_addy ?></td>
						</tr>

					<?php } ?>


					<?php if ( '' != $venue_meta['_generic_website'] ) { ?>
						<tr>
							<td><a itemprop="url" href="<?php echo $venue_meta['_generic_website'][0] ?>" target="_blank"><?php echo $venue_meta['_generic_website'][0] ?></a></td>
						</tr>
					<?php } ?>

				</table>
				
				</div>
				</div>
				
			</td>
		</tr>

	</table>


<?php } ?>


<?php wp_reset_postdata(); unset( $venues ); unset( $venue_meta ); ?> 




<?php // ======= Primary Event Organizer Details ======== ?>

<?php 

if ( $primary_organizers->have_posts() ) while ( $primary_organizers->have_posts() ) { 

	$primary_organizers->the_post();

	$primary_organizers_meta = get_post_custom( $post->ID, true );

?>
	<?php if ( empty( $data['display_organizer'] ) || 'yes' == $data['display_organizer'] ) { ?>

		<div itemscope itemtype="http://schema.org/Person">
		
		<table class="ignitewoo_event_details organizer">

			<tr>
				<td class="ignitewoo_event_organizer" colspan="2">
					<?php _e( 'Organizer Details', 'ignitewoo_events' ) ?>
				</td>
			</tr>

			<tr>

				<td class="event_thumbs" style="vertical-align:top; width: 33%">
				
					<a itemprop="image" title="<?php the_title() ?>" href="<?php echo the_permalink() ?>">
					<?php the_post_thumbnail( 'thumbnail' ) ?>
					
					</a>
				</td>

				<td style="vertical-align:top">

					<table style="width:100%">


						<?php if ( '' != get_the_title( $post->ID ) ) { ?>

							<tr>
								<td><span itemprop="name"><?php the_title() ?></span></td>
							</tr>

						<?php } ?>

					<?php if ( '' != $primary_organizers_meta['_generic_address'][0] ) { ?>

						<tr>
							<td>
								<span itemprop="streetAddress"><?php echo $primary_organizers_meta['_generic_address'][0] ?></span>
							</td>
						</tr>

					<?php } ?>


					<?php if ( '' != $primary_organizers_meta['_generic_city'][0] ) { ?>

						<tr>
							<td>
	
								<meta itemprop="addressLocality">
									<?php 
									echo $primary_organizers_meta['_generic_city'][0] . ', ';
									?>
								</meta>
								
								<meta itemprop="addressRegion">
									<?php
									if ( '' != $primary_organizers_meta['_generic_country_state'][0] ) { 

										$x = explode( ':', $primary_organizers_meta['_generic_country_state'][0] );

										if ( count( $x ) > 1 ) 
											echo $x[1]. ', '; // state

										echo $x[0];
										
										if ( !empty( $primary_organizers_meta['_generic_postalcode'][0] ) )
											echo ' ' . $primary_organizers_meta['_generic_postalcode'][0];

									}

									?>
								</meta>

							</td>
						</tr>

					<?php } ?>

						<?php if ( '' != $primary_organizers_meta['_generic_email'][0] ) { ?>

							<?php 
							// Obscure the email address so that spammer's Web page scrapers can find it easily. Javascript unobscures it after the page loads. So scrapers won't get a useful mailto:// link to scrape out of the page. 
							$email_addy = $this->obscure_email_address( $primary_organizers_meta['_generic_email'][0] ); 
							?>

							<tr>
								<td><span itemprop="email"><?php echo $email_addy ?></span></td>
							</tr>

						<?php } ?>


						<?php if ( '' != $primary_organizers_meta['_generic_website'] ) { ?>
							<tr>
								<td>
									<span itemprop="url">
										<a href="<?php echo $primary_organizers_meta['_generic_website'][0] ?>" target="_blank"><?php echo $primary_organizers_meta['_generic_website'][0] ?></a>
									</span>
								</td>
							</tr>
						<?php } ?>


						<?php if ( '' != $primary_organizers_meta['_generic_phone'] ) { ?>
							<tr>
								<td><span itemprop="telephone"><?php echo $primary_organizers_meta['_generic_phone'][0] ?></span></td>
							</tr>
						<?php } ?>

					</table>
				</td>
			</tr>
		</table>
		
		</div>

	<?php } ?>

<?php } ?>


<?php wp_reset_postdata(); unset( $primary_organizers ); unset( $primary_organizers_meta ); ?> 


<?php // ======= Sponsor Details ======== ?>
<?php 

if ( $primary_sponsors->have_posts() ) while ( $primary_sponsors->have_posts() ) { 

	$primary_sponsors->the_post();

	$primary_sponsors_meta = get_post_custom( $post->ID, true );

?>

	<div itemscope itemtype="http://schema.org/Person">

	<table class="ignitewoo_event_details sponsor">

		<tr>
			<td class="ignitewoo_event_sponsor" colspan="2">
				<?php _e( 'Event Sponsors', 'ignitewoo_events' ) ?>
			</td>
		</tr>

		<tr>

			<td class="event_thumbs" style="vertical-align:top; width: 33%">
				<a itemprop="image" title="<?php the_title() ?>" href="<?php echo the_permalink() ?>">
				<?php the_post_thumbnail( 'thumbnail' ) ?>
				</a>
			</td>

			<td style="vertical-align:top">

				<table style="width:100%">


					<?php if ( '' != get_the_title( $post->ID ) ) { ?>

						<tr>
							<td><span itemprop="name"><?php the_title() ?></span></td>
						</tr>

					<?php } ?>

					<?php if ( '' != $primary_sponsors_meta['_generic_address'][0] ) { ?>

						<tr>
							<td>
								<span itemprop="streetAddress"><?php echo $primary_sponsors_meta['_generic_address'][0] ?></span>
							</td>
						</tr>

					<?php } ?>


					<?php if ( '' != $primary_sponsors_meta['_generic_city'][0] ) { ?>

						<tr>
							<td>
	
								<meta itemprop="addressLocality">
									<?php 
									echo $primary_sponsors_meta['_generic_city'][0] . ', ';
									?>
								</meta>
								
								<meta itemprop="addressRegion">
									<?php
									if ( '' != $primary_sponsors_meta['_generic_country_state'][0] ) { 

										$x = explode( ':', $primary_sponsors_meta['_generic_country_state'][0] );

										if ( count( $x ) > 1 ) 
											echo $x[1]. ', '; // state

										echo $x[0];
										
										if ( !empty( $primary_sponsors_meta['_generic_postalcode'][0] ) )
											echo ' ' . $primary_sponsors_meta['_generic_postalcode'][0];

									}

									?>
								</meta>

							</td>
						</tr>

					<?php } ?>
					<?php if ( '' != $primary_sponsors_meta['_generic_email'][0] ) { ?>

						<?php 
						// Obscure the email address so that spammer's Web page scrapers can find it easily. Javascript unobscures it after the page loads. So scrapers won't get a useful mailto:// link to scrape out of the page. 
						$email_addy = $this->obscure_email_address( $primary_sponsors_meta['_generic_email'][0] ); 
						?>

						<tr>
							<td><span itemprop="email"><?php echo $email_addy ?></span></td>
						</tr>

					<?php } ?>


					<?php if ( '' != $primary_sponsors_meta['_generic_website'] ) { ?>
						<tr>
							<td>
								<span itemprop="url">
									<a href="<?php echo $primary_sponsors_meta['_generic_website'][0] ?>" target="_blank"><?php echo $primary_sponsors_meta['_generic_website'][0] ?></a>
								</span>
							</td>
						</tr>
					<?php } ?>


					<?php if ( '' != $primary_sponsors_meta['_generic_phone'] ) { ?>
						<tr>
							<td><span itemprop="telephone"><?php echo $primary_sponsors_meta['_generic_phone'][0] ?></span></td>
						</tr>
					<?php } ?>

				</table>
			</td>
		</tr>
	</table>
	
	</div>
	
<?php } ?>


<?php wp_reset_postdata(); unset( $primary_sponsors ); unset( $primary_sponsors_meta ); ?> 


<?php if ( isset( $sessions ) && is_array( $sessions ) && count( $sessions ) > 0 ) { ?>

	<?php if ( class_exists( 'IgniteWoo_Events_Pro' ) ) { ?>
	<h3 class="ignitewoo_events_sessions_header"><?php _e( 'Sessions', 'ignitewoo_events' ) ?></h3>
	<?php } ?>

	<?php foreach( $sessions as $session ) {  ?>

		<table class="ignitewoo_event_details session ">
			<tr>
				    <td class="ignitewoo_event_session">
					    <?php if ( 'product' == $post->post_type ) { ?>
					    <span class="session_name"><?php echo $session['name'] ?></span>
					    <?php } else { ?>
					    <span class="ignitewoo_event_speaker"><?php _e( 'Speakers', 'ignitewoo_events' ) ?></span>
					    <?php }  ?>
				    </td>
			</tr>

			<tr>
				<td>

					<?php if ( isset( $session['datetime'] ) && !empty( $session['datetime'] ) ) { ?>
						<p class="session_time"><?php echo date( 'F j, Y - g:i a', strtotime( $session['datetime'] ) ) ?></p>
					<?php } ?>

					<p class="session_desc"><?php echo isset( $session['description'] ) ? $session['description'] : '' ?></p>


					<?php if ( isset( $session['organizer'] ) && !empty( $session['organizer'] ) && count( (array)$session['organizer'] ) > 0 ) { ?>

						<p class="session_organizer_heading"><?php _e( 'Session Organizers', 'ignitewoo_events' ) ?></p>

						<?php foreach( (array)$session['organizer'] as $o ) { ?>

							<?php $p = new WP_Query( array( 'p' => $o, 'post_type' => 'event_organizer', 'post_status' => 'publish' ) ) ?>

							<?php if ( $p->have_posts() ) while( $p->have_posts() ) { ?>

								<?php $p->the_post() ?>

								<div itemscope itemtype="http://schema.org/Person">
								<div class="session_organizer">

									<div class="session_organizer_pic">
										<a itemtype="image" title="<?php the_title() ?>" href="<?php echo the_permalink() ?>">
											<?php echo the_post_thumbnail( 'thumbnail' ) ?>
										</a>
									</div>

									<div class="session_organizer_details">
										<p itemtype="name" class="session_organizer_name">
											<?php the_title() ?>
										</p>
										<p itemtype="description" class="session_organizer_bio">
											<?php the_excerpt() ?> [...]
										</p>
									</div>

								</div>
								</div>
								<div style="clear:both"></div>
							<?php } ?>

							<?php wp_reset_query(); ?>
						<?php } ?>
					<?php } ?>


					<div style="clear:both"></div>


					<?php if ( isset( $session['sponsor'] ) && !empty( $session['sponsor'] ) && count( (array)$session['sponsor'] ) > 0 ) { ?>

						<p class="session_sponsor_heading"><?php _e( 'Session Sponsors', 'ignitewoo_events' ) ?></p>

						<?php foreach( (array)$session['sponsor'] as $o ) { ?>

							<?php $p = new WP_Query( array( 'p' => $o, 'post_type' => 'event_sponsor', 'post_status' => 'publish' ) ) ?>

							<?php if ( $p->have_posts() ) while( $p->have_posts() ) { ?>

								<?php $p->the_post() ?>

								<div itemscope itemtype="http://schema.org/Person">
								<div class="session_sponsor">

									<div class="session_sponsor_pic">
										<a itemtype="image" title="<?php the_title() ?>" href="<?php echo the_permalink() ?>">
											<?php echo the_post_thumbnail( 'thumbnail' ) ?>
										</a>
									</div>

									<div class="session_sponsor_details">
										<p itemtype="name" class="session_sponsor_name">
											<?php the_title() ?>
										</p>
										<p itemtype="description" class="session_sponsor_bio">
											<?php the_excerpt() ?> [...]
										</p>
									</div>

								</div>
								</div>
								
								<div style="clear:both"></div>
							<?php } ?>

							<?php wp_reset_query(); ?>
						<?php } ?>
					<?php } ?>


					<div style="clear:both"></div>


					<?php if ( isset( $session['speaker_data'] ) && !empty( $session['speaker_data'] ) && count( (array)$session['speaker_data'] ) > 0 ) { ?>

						<p class="session_speaker_heading"><?php _e( 'Session Speakers', 'ignitewoo_events' ) ?></p>

						<?php $offset = 0; ?>

						<?php foreach( (array)$session['speaker_data'] as $o ) { ?>

							<?php $p = new WP_Query( array( 'p' => $o['id'], 'post_type' => 'event_speaker', 'post_status' => 'publish' ) ) ?>

							<?php if ( $p->have_posts() ) while( $p->have_posts() ) { ?>

								<?php $p->the_post() ?>

								<div itemscope itemtype="http://schema.org/Person">
								<div class="session_speaker">

									<div class="session_speaker_details">
										<div class="session_speaker_pic">
											<a itemtype="image" title="<?php the_title() ?>" href="<?php echo the_permalink() ?>"><?php echo the_post_thumbnail( 'thumbnail' ) ?></a>
										</div>

										<p itemtype="name" class="session_speaker_name">
											<?php the_title() ?>
										</p>

									
										<p class="session_speaker_time">
										
											<?php if ( !empty( $session['speaker_data'][ $offset ]['start'] ) ) echo date( 'M j, Y - g:i a', strtotime( $session['speaker_data'][ $offset ]['start'] ) ) ?>
											
											<?php if ( !empty( $session['speaker_data'][ $offset ]['end'] ) ) echo ' &ndash; ' . date( 'M j, Y - g:i a', strtotime( $session['speaker_data'][ $offset ]['end'] ) ) ?>
										</p>
								

										<p itemtype="description" class="session_speaker_bio">
											<?php echo wpautop( $session['speaker_data'][ $offset ]['desc'] ) ?>
										</p>
									</div>

								</div>
								<div style="clear:both"></div>
							<?php } ?>

							<?php wp_reset_query(); // Themers, do not overlook the need for this ?>
							
							<?php $offset++ ?>
							
						<?php } ?>
					<?php } ?>

				</td>
			<tr/>
		</table>

	<?php } ?>

<?php } ?>


<?php unset( $sessions ) ?>

</div> 

<?php 
	// Insert Google Map if that option is turned on 
	do_action( 'ignitewoo_events_map' );
?>