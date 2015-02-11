<?php
/**
 * Widget
 */
class Lshowcase_Widget extends WP_Widget

{
	public

	function __construct()
	{
		$options = get_option( 'lshowcase-settings' );
		$name = $options['lshowcase_name_singular'];
		$nameplural = $options['lshowcase_name_plural'];
		$widget_ops = array(
			'classname' => 'lshowcase_widget',
			'description' => 'Display ' . $name . ' images on your website'
		);
		parent::__construct( 'lshowcase_widget', $nameplural, $widget_ops);
	}

	public

	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$orderby = strip_tags($instance['orderby']);
		$category = $instance['category'];
		$style = strip_tags($instance['style']);
		$interface = strip_tags($instance['interface']);
		$activeurl = $instance['activeurl'];
		$tooltip = $instance['tooltip'];
		$limit = $instance['limit'];
		$slidersettings = "";
		$img = 0;
		echo $before_widget;
		if (!empty($title)) echo $before_title . $title . $after_title;
		echo build_lshowcase($orderby, $category, $activeurl, $style, $interface, $tooltip, $limit, $slidersettings,$img);
		echo $after_widget;
	}

	public

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['orderby'] = strip_tags($new_instance['orderby']);
		$instance['category'] = $new_instance['category'];
		$instance['style'] = strip_tags($new_instance['style']);
		$instance['interface'] = strip_tags($new_instance['interface']);
		$instance['activeurl'] = $new_instance['activeurl'];
		$instance['tooltip'] = $new_instance['tooltip'];
		$instance['limit'] = $new_instance['limit'];
		return $instance;
	}

	public

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, array(
			'title' => '',
			'orderby' => 'menu_order',
			'category' => '0',
			'style' => 'normal',
			'interface' => 'grid',
			'activeurl' => '1'
		));
		$title = strip_tags($instance['title']);
		$orderby = strip_tags($instance['orderby']);
		$category = $instance['category'];
		$style = strip_tags($instance['style']);
		$interface = strip_tags($instance['interface']);
		$activeurl = $instance['activeurl'];
		$tooltip = $instance['tooltip'];
		$limit = $instance['limit'];
?>
        <p><label for="<?php
		echo $this->get_field_id( 'title' ); ?>">Title:</label>
        <input class="widefat" id="<?php
		echo $this->get_field_id( 'title' ); ?>" name="<?php
		echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php
		echo esc_attr($title); ?>" /></p>
 
        
<p>
        <label for="<?php
		echo $this->get_field_id( 'orderby' ); ?>">Order By:<br />
        </label>
        <select id="<?php
		echo $this->get_field_id( 'orderby' ); ?>" name="<?php
		echo $this->get_field_name( 'orderby' ); ?>">
            <option value="none" <?php
		selected($orderby, 'none' ); ?>>Default</option>
            <option value="name" <?php
		selected($orderby, 'name' ); ?>>Title</option>
            <option value="ID" <?php
		selected($orderby, 'ID' ); ?>>ID</option>
            <option value="date" <?php
		selected($orderby, 'date' ); ?>>Date</option>
            <option value="modified" <?php
		selected($orderby, 'modified' ); ?>>Modified</option>
            <option value="rand" <?php
		selected($orderby, 'rand' ); ?>>Random</option>
        </select></p>
        
              <p><label for="<?php
		echo $this->get_field_id( 'limit' ); ?>">Number of Images to display:</label><br />

        <input size="3" id="<?php
		echo $this->get_field_id( 'limit' ); ?>" name="<?php
		echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php
		echo esc_attr($limit); ?>" /><span class="howto"> (Leave blank or 0 to display all)</span></p>
 	
    
<p><label for="<?php
		echo $this->get_field_id( 'category' ); ?>">Category</label>
     :
       <br />
        <select id="<?php
		echo $this->get_field_id( 'category' ); ?>" name="<?php
		echo $this->get_field_name( 'category' ); ?>">
          <option value="0" <?php
		selected($category, '0' ); ?>>All</option>
        
  <?php
		$terms = get_terms( "lshowcase-categories" );
		$count = count($terms);
		if ($count > 0) {
			foreach($terms as $term) {
				echo "<option value='" . $term->slug . "'" . selected($category, $term->slug) . ">" . $term->name . "</option>";
			}
		}

?></select></p>
        
        
        

  
  
          <p>
            <label for="<?php
		echo $this->get_field_id( 'activeurl' ); ?>">URL:<br />
            </label>
        <select id="<?php
		echo $this->get_field_id( 'activeurl' ); ?>" name="<?php
		echo $this->get_field_name( 'activeurl' ); ?>">
          <option value="inactive" <?php
		selected($activeurl, 'inactive' ); ?>>Inactive</option>
          <option value="new" <?php
		selected($activeurl, 'new' ); ?>>Open in new window</option>
          <option value="same" <?php
		selected($activeurl, 'same' ); ?>>Open in same window</option>
        </select></p>
         
  
  
   <p>
     <label for="<?php
		echo $this->get_field_id( 'style' ); ?>">Style:</label>
        <br />
        <select id="<?php
		echo $this->get_field_id( 'style' ); ?>" name="<?php
		echo $this->get_field_name( 'style' ); ?>">
          
          <?php
		$stylesarray = lshowcase_styles_array();
		foreach($stylesarray as $option => $key) {
?>
          
          <option value="<?php
			echo $option; ?>" <?php
			selected($style, $option); ?>><?php
			echo $key['description']; ?></option>
          <?php
		} ?>
          
</select></p>
       
        <p>Layout:
          <br />
          <select id="<?php
		echo $this->get_field_id( 'interface' ); ?>" name="<?php
		echo $this->get_field_name( 'interface' ); ?>">
          <option value="hcarousel" <?php
		selected($interface, 'hcarousel' ); ?>>Horizontal Carousel</option>
          <option value="grid" <?php
		selected($interface, 'grid' ); ?>>Normal Grid</option>
          <option value="grid12" <?php
		selected($interface, 'grid12' ); ?>>Responsive Grid - 12 Columns</option> 
          <option value="grid8" <?php
		selected($interface, 'grid11' ); ?>>Responsive Grid - 11 Columns</option>
          <option value="grid8" <?php
		selected($interface, 'grid10' ); ?>>Responsive Grid - 10 Columns</option>
          <option value="grid8" <?php
		selected($interface, 'grid9' ); ?>>Responsive Grid - 9 Columns</option>
          <option value="grid8" <?php
		selected($interface, 'grid8' ); ?>>Responsive Grid - 8 Columns</option> 
          <option value="grid8" <?php
		selected($interface, 'grid7' ); ?>>Responsive Grid - 7 Columns</option>
          <option value="grid6" <?php
		selected($interface, 'grid6' ); ?>>Responsive Grid - 6 Columns</option> 
          <option value="grid5" <?php
		selected($interface, 'grid5' ); ?>>Responsive Grid - 5 Columns</option>  
          <option value="grid4" <?php
		selected($interface, 'grid4' ); ?>>Responsive Grid - 4 Columns</option>
          <option value="grid3" <?php
		selected($interface, 'grid3' ); ?>>Responsive Grid - 3 Columns</option>
          <option value="grid2" <?php
		selected($interface, 'grid2' ); ?>>Responsive Grid - 2 Columns</option>
          <option value="grid1" <?php
		selected($interface, 'grid1' ); ?>>Responsive Grid - 1 Columns</option>     
          
</select></p>
       
       <p>
     <label for="<?php
		echo $this->get_field_id( 'tooltip' ); ?>">Show Tooltip:</label>
        <br />
        <select id="<?php
		echo $this->get_field_id( 'tooltip' ); ?>" name="<?php
		echo $this->get_field_name( 'tooltip' ); ?>">
          <option value="true" <?php
		selected($tooltip, 'true' ); ?>>Yes - Title</option>
 		
 		<option value="true-description" <?php
		selected($tooltip, 'true-description' ); ?>>Yes - Description</option>

          <option value="false" <?php
		selected($tooltip, 'false' ); ?>>No</option>  
          
</select></p>
       
        <?php
	}
}

add_action( 'widgets_init', 'register_lshowcase_widget' );
/**
 * Register widget
 *
 * This functions is attached to the 'widgets_init' action hook.
 */

function register_lshowcase_widget()
{
	register_widget( 'Lshowcase_Widget' );
}
?>