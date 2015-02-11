<?php
/**
* Event Taxonomies 
* Copyright (c) IgniteWoo.com - All Rights Reserved
*/

if ( !defined('ABSPATH') )
	die();

class IgniteWoo_Events_Taxonomies {

	function __construct() { 

		add_action( 'init', array( &$this, 'init' ), 99999990 );

		if ( is_admin() ) { 

			require_once( dirname( __FILE__ ) . '/events-post-types-meta.php' );

			$this->taxonomy_meta = new IgniteWoo_Events_Taxonomy_Meta();

		}
		
		add_filter('manage_posts_columns', array( &$this, 'columns_head' ) );  
		
		add_action('manage_posts_custom_column', array( &$this, 'columns_content' ), 10, 2);

		add_filter("manage_edit-ignitewoo_event_sortable_columns", array( &$this, 'sort' ) );
		
		add_filter( 'request', array( &$this, 'event_sort_orderby' ) );
		
	}


	function init() { 

		$this->settings = get_option( 'ignitewoo_events_main_settings', false );

		if ( empty( $this->settings['events_slug'] ) )
			$this->settings['events_slug'] = 'event';
			
		if ( empty( $this->settings['venue_slug'] ) )
			$this->settings['venue_slug'] == 'event-venues';

		if ( empty( $this->settings['speaker_slug'] ) )
			$this->settings['speaker_slug'] == 'event-speakers';

		if ( empty( $this->settings['sponsor_slug'] ) )
			$this->settings['sponsor_slug'] == 'event-sponsors';

		if ( empty( $this->settings['organizer_slug'] ) )
			$this->settings['organizer_slug'] == 'event-organizer';

		$this->events();
		$this->organizers();
		$this->venues();
		$this->sponsors();
		$this->speakers();

	}


	function organizers() { 
		register_post_type('event_organizer', 
			array(	'label' => 'Event Organizers',
				'description' => '',
				'public' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => 'ignitewoo_events_settings',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => $this->settings['organizer_slug'] ),
				'query_var' => false,
				'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'comments', 'page-attributes' ),
				'labels' => array (
					'name' => 'Event Organizers',
					'singular_name' => 'Event Organizer',
					'menu_name' => 'Event Organizers',
					'add_new' => 'Add Organizer',
					'add_new_item' => 'Add New Organizer',
					'edit' => 'Edit Organizers',
					'edit_item' => 'Edit Organizer',
					'new_item' => 'New Organizer',
					'view' => 'View Organizer',
					'view_item' => 'View Organizer',
					'search_items' => 'Search Organizer',
					'not_found' => 'No Organizers Found',
					'not_found_in_trash' => 'No Organizers Found in Trash',
					'parent' => 'Parent Organizer',
				),
			) 
		);
	}


	function venues() { 
		register_post_type('event_venue', 
			array(	'label' => 'Event Venues',
				'description' => '',
				'public' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => 'ignitewoo_events_settings',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' =>  array( 'slug' => $this->settings['venue_slug'] ),
				'query_var' => false,
				'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'comments', 'page-attributes' ),
				'labels' => array (
					'name' => 'Event Venues',
					'singular_name' => 'Event Venue',
					'menu_name' => 'Event Venues',
					'add_new' => 'Add Venue',
					'add_new_item' => 'Add New Venue',
					'edit' => 'Edit Venues',
					'edit_item' => 'Edit Venue',
					'new_item' => 'New Venue',
					'view' => 'View Venue',
					'view_item' => 'View Venue',
					'search_items' => 'Search Venue',
					'not_found' => 'No Venues Found',
					'not_found_in_trash' => 'No Venues Found in Trash',
					'parent' => 'Parent Venue',
				),
			) 
		);
	}


	function sponsors() { 
		register_post_type('event_sponsor', 
			array(	'label' => 'Event Sponsors',
				'description' => '',
				'public' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => 'ignitewoo_events_settings',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' =>  array( 'slug' => $this->settings['sponsor_slug'] ),
				'query_var' => false,
				'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'comments', 'page-attributes' ),
				'labels' => array (
					'name' => 'Event Sponsors',
					'singular_name' => 'Event Sponsor',
					'menu_name' => 'Event Sponsors',
					'add_new' => 'Add Sponsor',
					'add_new_item' => 'Add New Sponsor',
					'edit' => 'Edit Sponsors',
					'edit_item' => 'Edit Sponsor',
					'new_item' => 'New Sponsor',
					'view' => 'View Sponsor',
					'view_item' => 'View Sponsor',
					'search_items' => 'Search Sponsor',
					'not_found' => 'No Sponsors Found',
					'not_found_in_trash' => 'No Sponsors Found in Trash',
					'parent' => 'Parent Sponsor',
				),
			) 
		);
	}


	function speakers() { 
		register_post_type('event_speaker', 
			array(	'label' => 'Event Speakers',
				'description' => '',
				'public' => true,
				'publicly_queryable' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_in_menu' => 'ignitewoo_events_settings',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' =>  array( 'slug' => $this->settings['speaker_slug'] ),
				'query_var' => false,
				'supports' => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'comments', 'page-attributes' ),
				'labels' => array (
					'name' => 'Event Speakers',
					'singular_name' => 'Event Speaker',
					'menu_name' => 'Event Speakers',
					'add_new' => 'Add Speaker',
					'add_new_item' => 'Add New Speaker',
					'edit' => 'Edit Speakers',
					'edit_item' => 'Edit Speaker',
					'new_item' => 'New Speaker',
					'view' => 'View Speaker',
					'view_item' => 'View Speaker',
					'search_items' => 'Search Speaker',
					'not_found' => 'No Speakers Found',
					'not_found_in_trash' => 'No Speakers Found in Trash',
					'parent' => 'Parent Speaker',
				),
			) 
		);
	}


	function events() { 
		global $ignitewoo_events;
		
		if ( !empty( $ignitewoo_events->settings['events_posting'] ) )
			if ( class_exists( 'IgniteWoo_Events_Pro' ) && 'tickets_only' == $ignitewoo_events->settings['events_posting'] )
				return;

		$base_slug = $this->settings['events_slug'];
	
		$base_slug = _x( $base_slug, 'slug', 'ignitewoo_events' );

		if ( empty( $base_slug ) || !isset( $base_slug ) )
			$base_slug = 'event';
	
		$category_base = trailingslashit( $base_slug );

		$category_slug = _x( 'event-category', 'slug', 'ignitewoo_events' );

		$tag_slug = _x( 'event-tag', 'slug', 'ignitewoo_events' );

		//$product_base = trailingslashit( $base_slug );

		//$admin_only_query_var = ( is_admin() ) ? true : false;

		register_post_type( "ignitewoo_event",
			array(
				'labels' => array(
						'name' 			=> __( 'Events', 'ignitewoo_events' ),
						'singular_name' 	=> __( 'Event', 'ignitewoo_events' ),
						'menu_name'		=> _x( 'Events', 'Admin menu name', 'ignitewoo_events' ),
						'add_new' 		=> __( 'Add Event', 'ignitewoo_events' ),
						'add_new_item' 		=> __( 'Add New Event', 'ignitewoo_events' ),
						'edit' 			=> __( 'Edit', 'ignitewoo_events' ),
						'edit_item' 		=> __( 'Edit Event', 'ignitewoo_events' ),
						'new_item' 		=> __( 'New Event', 'ignitewoo_events' ),
						'view' 			=> __( 'View Event', 'ignitewoo_events' ),
						'view_item' 		=> __( 'View Event', 'ignitewoo_events' ),
						'search_items' 		=> __( 'Search Events', 'ignitewoo_events' ),
						'not_found' 		=> __( 'No Events found', 'ignitewoo_events' ),
						'not_found_in_trash' 	=> __( 'No Events found in trash', 'ignitewoo_events' ),
						'parent' 		=> __( 'Parent Event', 'ignitewoo_events' )
					),
				'description' 		=> __( 'This is where you can add new products to your store.', 'ignitewoo_events' ),
				'public' 		=> true,
				'show_ui' 		=> true,
				'show_in_menu'		=> 'ignitewoo_events_settings',
				'capability_type' 	=> 'post',
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 		=> false, // Hierarchical causes memory issues - WP loads all records!
				'rewrite' 		=> array( 'slug' => $base_slug, 'with_front' => false, 'feeds' => $base_slug ),
				'query_var' 		=> true,
				'supports' 		=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes' ),
				'has_archive' 		=> $base_slug,
				'show_in_nav_menus' 	=> true
			)
		);

		register_taxonomy( 'event_cat',
			'ignitewoo_event',
			apply_filters( 'ignitewoo_event_cat_taxonomy_args', array(
				'hierarchical' => true,
				'label'  => __( 'Event Categories', 'ignitewoo_events'),
				'labels' => array(
					'name' => __( 'Event Categories', 'ignitewoo_events'),
					'singular_name' => __( 'Event Category', 'ignitewoo_events'),
					'menu_name'			=> _x( 'Categories', 'Admin menu name', 'ignitewoo_events' ),
					'search_items' => __( 'Search Event Categories', 'ignitewoo_events'),
					'all_items' => __( 'All Event Categories', 'ignitewoo_events'),
					'parent_item' => __( 'Parent Event Category', 'ignitewoo_events'),
					'parent_item_colon' => __( 'Parent Event Category:', 'ignitewoo_events'),
					'edit_item' => __( 'Edit Event Category', 'ignitewoo_events'),
					'update_item' => __( 'Update Event Category', 'ignitewoo_events'),
					'add_new_item' => __( 'Add New Event Category', 'ignitewoo_events'),
					'new_item_name' => __( 'New Event Category Name', 'ignitewoo_events')
					),
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array(
						'slug' => $category_slug,
						'with_front' => false,
						'hierarchical' => true,
						//'ep_mask' => EP_CATEGORIES
					),
			) )
		);

		register_taxonomy( 'event_tag',
			'ignitewoo_event',
			apply_filters( 'ignitewoo_event_tag_taxonomy_args', array(
				'hierarchical' => false,
				'label'  => __( 'Event Tags', 'ignitewoo_events'),
				'labels' => array(
					'name' => __( 'Event Tags', 'ignitewoo_events'),
					'singular_name' => __( 'Event Tag', 'ignitewoo_events'),
					'menu_name'			=> _x( 'Tags', 'Admin menu name', 'ignitewoo_events' ),
					'search_items' => __( 'Search Event Tags', 'ignitewoo_events'),
					'all_items' => __( 'All Event Tags', 'ignitewoo_events'),
					'parent_item' => __( 'Parent Event Tag', 'ignitewoo_events'),
					'parent_item_colon' => __( 'Parent Event Tag:', 'ignitewoo_events'),
					'edit_item' => __( 'Edit Event Tag', 'ignitewoo_events'),
					'update_item' => __( 'Update Event Tag', 'ignitewoo_events'),
					'add_new_item' => __( 'Add New Event Tag', 'ignitewoo_events'),
					'new_item_name' => __( 'New Event Tag Name', 'ignitewoo_events')
					),
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array(
						'slug' => $tag_slug,
						'with_front' => false,
						'hierarchical' => true,
						//'ep_mask' => EP_CATEGORIES
					),
			) )
		);
		
	}
		

	function columns_head( $defaults ) {  
		global $typenow;
		
		if ( 'ignitewoo_event' != $typenow ) 
			return $defaults;

		$cols = array();
		
		foreach( $defaults as $col => $val ) { 
		
			// Insert after title column
			if ( 'title' == $col ) { 
			
				$cols['title'] = $val;
					
				$cols['event_start'] = __( 'Event Start', 'ignitewoo_events' );
				
				$cols['event_end'] = __( 'Event End', 'ignitewoo_events' );
			
				continue;
			}
			
			$cols[ $col ] = $val;
		
		}

		return $cols;  
	}  
	
	
	function columns_content( $column_name, $post_id ) {
		global $typenow; 
		
		if ( 'ignitewoo_event' != $typenow ) 
			return $column_name;
			
		switch( $column_name ) { 
		
			case 'event_start': 
				echo get_post_meta( $post_id, '_ignitewoo_event_start', true );
				break;
			
			case 'event_end': 
				echo get_post_meta( $post_id, '_ignitewoo_event_end', true );
				break;
		  
		}  
	}  
		
		
	
	function sort( $columns ) {
		global $typenow; 
		
		if ( 'ignitewoo_event' != $typenow ) 
			return $columns;
			
		$columns['event_start'] = 'event_start_date';
		
		$columns['event_end'] = 'event_end_date';
		
		return $columns;
		
	}
	
	
	function event_sort_orderby( $vars ) {
		global $typenow; 
		
		if ( 'ignitewoo_event' != $typenow ) 
			return $vars;
			
		if ( empty( $vars['orderby'] ) || !isset( $vars['orderby'] ) )
			return $vars; 
		
		switch( $vars['orderby'] ) {
		
			case 'event_start':

				$vars = array_merge( $vars, array(
						'meta_key' => 'ignitewoo_event_start',
						'orderby' => 'meta_value'
						)
				);
		
				break;
				
			case 'event_end': 

				$vars = array_merge( $vars, array(
						'meta_key' => 'ignitewoo_event_end',
						'orderby' => 'meta_value'
						)
				);
		}
		
		return $vars; 
		
	}

}
