<?php
/**
* Upcoming Events Widget
* Copyright (c) 2012 - IgniteWoo.com - All Rights Reserved
*/


if ( !defined('ABSPATH') )
	die();

class IgniteWoo_Widget_Upcoming_Events extends WP_Widget {

	var $ignitewoo_widget_cssclass;
	var $ignitewoo_widget_description;
	var $ignitewoo_widget_idbase;
	var $ignitewoo_widget_name;


	function IgniteWoo_Widget_Upcoming_Events() {

		$this->ignitewoo_widget_cssclass = 'widget_upcoming_events';

		$this->ignitewoo_widget_description = __( 'Display a list of upcoming events on your site.', 'ignitewoo_events' );

		$this->ignitewoo_widget_idbase = 'ignitewoo_upcoming_events';

		$this->ignitewoo_widget_name = __( 'Event Calendar Upcoming Events', 'ignitewoo_events' );

		$widget_ops = array( 'classname' => $this->ignitewoo_widget_cssclass, 'description' => $this->ignitewoo_widget_description );

		$this->WP_Widget( $this->ignitewoo_widget_idbase, $this->ignitewoo_widget_name, $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );

		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );

		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}


	function widget( $args, $instance ) {
		global $ignitewoo_events, $wpdb;

		$cache = wp_cache_get( 'widget_upcoming_events', 'widget' );

		if ( !is_array( $cache ) ) 
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {

			echo $cache[ $args['widget_id'] ];

			return;
		}

		ob_start();

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Upcoming Events', 'ignitewoo_events' ) : $instance['title'], $instance, $this->id_base );

		if ( !$number = (int) $instance['number'] )
			$number = 10;

		else if ( $number < 1 )
			$number = 1;

		else if ( $number > 999 )
			$number = 999;

		$settings = get_option( 'ignitewoo_events_main_settings', false ); 

		$show_all_recur = $instance['show_all_recur'];

		$sql = 'select p.ID, p.post_title, m2.meta_value  from ' . $wpdb->posts . ' p ' . 
			' left join ' . $wpdb->postmeta . ' m1 on m1.post_id = ID ' . 
			' left join ' . $wpdb->postmeta . ' m2 on m2.post_id = ID ' . 
			' where post_status = "publish" AND ( post_type = "product" OR post_type = "ignitewoo_event" ) ' . 
			' AND ( m1.meta_key = "_ignitewoo_event" AND m1.meta_value = "yes" ) ' . 
			' AND ( m2.meta_key = "_ignitewoo_event_end" AND m2.meta_value >= "' . date( 'Y-m-d H:i:s', current_time( 'timestamp', false ) ) . '" ) ' . 
			' AND ( m2.meta_key = "_ignitewoo_event_end" AND m2.meta_value != "" ) ' . 
			' ORDER BY CAST( m2.meta_value as DATE ) ASC LIMIT ' . $number ;

		$posts = $wpdb->get_results( $sql );

		echo $before_widget;

		if ( $title ) 
			echo $before_title . $title . $after_title; 

		$displayed = array();

		if ( $posts ) { 

			//global $product; 
		?>

			    <ul class="product_list_widget event_list_widget floated">

			    <?php foreach ( $posts as $post ) { 

					if ( in_array( $post->ID, $displayed ) )
						continue;
					else
						$displayed[] = $post->ID;

					$price = '';
					
					if ( empty( $product ) && function_exists( 'get_product' ) ) {
					
						$product = get_product( $post->ID );
						
						if ( method_exists( $product, 'get_price_html' ) ) { 
							$image = $product->get_image();
							$price = $product->get_price_html();
						}
					}

					if ( !class_exists( 'Woocommerce' ) || empty( $product ) ) { 
						$product = new stdClass();
						$data = $ignitewoo_events->get_post_data();
						$product->price = '';
						$image = get_the_post_thumbnail( $post->ID );
					}
						
					if ( empty( $image ) )
						$image = get_the_post_thumbnail( $post->ID );

					?>

					<li>
						<div>
						<div class="events_upcoming_image">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" title="<?php echo esc_attr($post->post_title ? $post->post_title : $post->ID); ?>">
								<?php echo $image ?>
							</a>
						</div>
						<div class="events_upcoming_info">
							<div class="event_title_wrap">
							<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" title="<?php echo esc_attr($post->post_title ? $post->post_title : $post->ID); ?>">
							
							<?php
								if ( $post->post_title ) 
									echo get_the_title( $post->ID ); 
								else 
									echo $post->ID; 
							?>
							</a>
							</div>
							
							<?php 

							//if ( 'yes' == $show_all_recur ) 
								$start = get_post_meta( $post->ID, '_ignitewoo_event_start', false ); 
							//else
								//$start = (array)$post->meta_value;
//var_dump( $post->ID, get_post_meta( $post->ID, '_ignitewoo_event_start', false ) );
							$duration = get_post_meta( $post->ID, '_ignitewoo_event_duration', true ); 

							if ( isset( $start ) && !empty( $start ) ) {

								$count = count( $start );

								foreach( $start as $s ) { 

									if ( $count > 1 && 'yes' == $show_all_recur ) 
										echo '<p>';

									echo '<span class="start_date">' . date( $settings['date_format'] . ' ' . $settings['time_format'] , @strtotime( $s ) ) . '</span>';
									
									if ( empty( $duration ) ) { 
									
										$duration = 0;
										
										$s = get_post_meta( $post->ID, '_ignitewoo_event_end', true );
										
									}

									echo '<br/><span class="end_date">' . date( $settings['date_format'] . ' ' . $settings['time_format'] , strtotime( $s ) + $duration ) . '</span>';

									if ( $count > 1 && 'yes' == $show_all_recur ) 
										echo '</p>';

									if ( 'yes' != $show_all_recur )
										break;
								}

								if ( $count <= 1 || 'yes' != $show_all_recur ) 
									echo '</br>';

							}
							?>

							<?php 
							

							if ( isset( $price ) && !empty( $price ) )
								echo '<span class="event_upcoming_price">' . $price . '</span>';
							?>
						</div>
						</div>
					</li>
					
					<?php unset( $product ) ?>

				<?php } ?>

				</ul>

		<?php } else { ?>
			    <ul class="product_list_widget">
				    <li><?php _e( 'No upcoming events', 'ignitewoo_events' )?></li>
			    </ul>
		<?php } 

		echo $after_widget;

		$content = ob_get_clean();

		if ( isset( $args['widget_id'] ) ) $cache[$args['widget_id']] = $content;

		echo $content;

		wp_cache_set( 'widget_upcoming_events', $cache, 'widget' );

		wp_reset_postdata();
	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$instance['number'] = (int) $new_instance['number'];

		$instance['show_all_recur'] = $new_instance['show_all_recur'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_upcoming_events'] ) ) 
			delete_option( 'widget_upcoming_events' );

		return $instance;
	}


	function flush_widget_cache() {
		wp_cache_delete( 'widget_upcoming_events', 'widget' );
	}


	function form( $instance ) {

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		if ( !isset( $instance['number'] ) || !$number = (int) $instance['number'] )
			$number = 2;

		$show_all_recur = isset( $instance['show_all_recur'] ) ? $instance['show_all_recur'] : false;

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ignitewoo_events' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of events to show:', 'ignitewoo_events' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show_all_recur' ); ?>">
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_all_recur' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_all_recur' ) ); ?>" type="checkbox" value="yes" <?php if( 'yes' == $show_all_recur ) echo 'checked="checked"'; ?> />
			<?php _e( 'Show all recurrences for recurring events. When disabled only the next recurrence will be shown.', 'ignitewoo_events' ); ?></label>
		</p>
		<?php
	}
}

