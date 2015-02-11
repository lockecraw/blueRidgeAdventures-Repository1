<?php 

class IgniteWoo_Widget_Events_Mini_Cal extends WP_Widget {

	var $ignitewoo_widget_cssclass;
	var $ignitewoo_widget_description;
	var $ignitewoo_widget_idbase = 'ignitewoo_mini_cal';
	var $ignitewoo_widget_name;

	function IgniteWoo_Widget_Events_Mini_Cal() {

		$this->ignitewoo_widget_cssclass = 'widget_mini_cal';

		$this->ignitewoo_widget_description = __( 'Display a Mini Event Calendar on your site.', 'ignitewoo_events' );

		$this->ignitewoo_widget_idbase = 'ignitewoo_mini_cal';

		$this->ignitewoo_widget_name = __( 'WooEvents Pro Mini Calendar', 'ignitewoo_events' );

		$widget_ops = array( 'classname' => $this->ignitewoo_widget_cssclass, 'description' => $this->ignitewoo_widget_description );

		$this->WP_Widget( $this->ignitewoo_widget_idbase, $this->ignitewoo_widget_name, $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );

		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );

		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}


	function widget( $args, $instance ) {
		global $ignitewoo_events;

		$cache = wp_cache_get( 'widget_mini_cal', 'widget' );

		if ( !is_array( $cache ) ) 
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {

			echo $cache[ $args['widget_id'] ];

			return;
		}

		ob_start();

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Mini Event Calendar', 'ignitewoo_events' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;

		if ( $title ) 
			echo $before_title . $title . $after_title; 

		?>

		<div id='mini_calendar' class="mini_calendar"></div>

		<?php

		echo $after_widget;

		$content = ob_get_clean();

		if ( isset( $args['widget_id'] ) ) $cache[$args['widget_id']] = $content;

		echo $content;

		wp_cache_set( 'widget_mini_cal', $cache, 'widget' );

		wp_reset_postdata();
	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_mini_cal'] ) ) 
			delete_option( 'widget_mini_cal' );

		return $instance;
	}


	function flush_widget_cache() {
		wp_cache_delete( 'widget_mini_cal', 'widget' );
	}


	function form( $instance ) {

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ignitewoo_events' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php
	}
}