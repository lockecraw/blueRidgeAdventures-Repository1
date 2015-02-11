<?php
/**
 * Template for displaying single organizers
 *
 */

get_header(); ?>

	<div id="container">
		<div id="content" class="single_event_content" role="main">

		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			<h2><?php the_title(); ?></h2>

			<div class="ignitewoo_events_post_thumb">

				<?php the_post_thumbnail() ?>

				<?php $meta = get_post_custom( $post->ID, true ); ?>

				<?php if ( !empty( $meta['_generic_address'][0] ) ) { ?>
					<p><?php echo $meta['_generic_address'][0] ?></p>
				<?php } ?>

				<p>
					<?php if ( !empty( $meta['_generic_city'][0] ) ) { ?>
						<?php echo $meta['_generic_city'][0] ?>
					<?php } ?>

					<?php 

						if ( !empty( $meta['_generic_country_state'][0] ) ) { 

							$data = $meta['_generic_country_state'][0];

							if ( isset( $data ) && false != $data )
								$data = explode( ':', $data);
							else 
								$data = array();

							$country = $data[0];

							if ( count( $data ) > 1 ) 
							    $state = $data[1];
							else
							    $state = '';
						}

						echo $state . ' ' . $country; 

						if ( !empty( $meta['_generic_postalcode'][0] ) )
							echo ' ' . $meta['_generic_postalcode'][0];

					?>
				</p>

				<?php if ( !empty( $meta['_generic_phone'][0] ) ) { ?>
					<p><?php echo $meta['_generic_phone'][0] ?></p>
				<?php } ?>

				<?php if ( !empty( $meta['_generic_email'][0] ) ) { ?>
					<p><?php echo $meta['_generic_email'][0] ?></p>
				<?php } ?>

				<?php if ( !empty( $meta['_generic_website'][0] ) ) { ?>
					<p><?php echo $meta['_generic_website'][0] ?></p>
				<?php } ?>
			</div>

			<div class="ignitewoo_events_post_content">
				<?php the_content() ?>
			</div>

		<?php endwhile; ?>

		<?php // ------------- Display all associated events ------------------- ?>

		<?php 

			global $ignitewoo_events, $ignitewoo_events_pro;

			$events = $ignitewoo_events_pro->get_events_for_template( '_organizers', $post->ID ); 

			if ( $events ) { 

				?>

				<div class="single_event_list">

					<h3 class="events_heading"><?php _e( 'Associated Events', 'ignitewoo_events' ) ?></h3>

					<?php 

					foreach( $events as $e ) { 

						?>

						    <h3><a href="<?php echo get_permalink( $e->ID ) ?>"><?php echo get_the_title( $e->ID ) ?></a></h3>

						    <?php echo get_the_post_thumbnail( $e->ID ) ?>

						    <?php /** Optional: 

							// Display event information
							// For assistance contact IgniteWoo.com for customization rates

							$meta = get_post_meta( $e->ID, '_ignitewoo_event_info', true );
							// var_dump($meta); // display all available data in the array

							?>

							<p><?php _e( 'Venue', 'ignitewoo_events' ) ?>: <?php echo get_the_title( $meta['event_venue'][0] ) ?></p>

							<?php 
							*/
						    ?>

						    <p><?php echo $ignitewoo_events->ignitewoo_the_excerpt( $e->post_content, 490 /* max word count of excerpt */ ); ?></p>

						<?php
					}

					?>

				</div>

				<?php
			}
		?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
