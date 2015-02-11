<?php
// add shortcode generator page
function lshowcase_shortcode_page_add()
{
	$menu_slug = 'edit.php?post_type=lshowcase';
	$submenu_page_title = 'Shortcode Generator';
	$submenu_title = 'Shortcode Generator';
	$capability = 'manage_options';
	$submenu_slug = 'lshowcase_shortcode';
	$submenu_function = 'lshowcase_shortcode_page';
	$defaultp = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
	add_action($defaultp, 'lshowcase_enqueue_admin_js' );
}

function lshowcase_enqueue_admin_js()
{
	wp_deregister_script( 'lshowcaseadmin' );
	wp_register_script( 'lshowcaseadmin', plugins_url( '/js/shortcode-builder.js', __FILE__) , array(
		'jquery'
	));
	wp_enqueue_script( 'lshowcaseadmin' );
	wp_deregister_style( 'lshowcase-main-style' );
	wp_register_style( 'lshowcase-main-style', plugins_url( '/styles.css', __FILE__) , array() , false, false);
	wp_enqueue_style( 'lshowcase-main-style' );

	// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value

	wp_localize_script( 'lshowcaseadmin', 'ajax_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));
	wp_deregister_script( 'lshowcase-bxslider' );
	wp_register_script( 'lshowcase-bxslider', plugins_url( '/bxslider/jquery.bxslider.js', __FILE__) , array(
		'jquery'
	) , false, false);
	wp_enqueue_script( 'lshowcase-bxslider' );
	wp_deregister_style( 'lshowcase-bxslider-style' );
	wp_register_style( 'lshowcase-bxslider-style', plugins_url( '/bxslider/jquery.bxslider.css', __FILE__) , array() , false, false);
	wp_enqueue_style( 'lshowcase-bxslider-style' );
	wp_deregister_script( 'ls-jquery-ui' );
	wp_register_script( 'ls-jquery-ui', plugins_url( '/js/jquery-ui-1.10.2.custom.min.js', __FILE__) , array(
		'jquery'
	) , false, false);
	wp_enqueue_script( 'ls-jquery-ui' );
	wp_deregister_script( 'lshowcase-tooltip' );
	wp_register_script( 'lshowcase-tooltip', plugins_url( '/js/tooltip.js', __FILE__) , array(
		'ls-jquery-ui'
	) , false, false);
	wp_enqueue_script( 'lshowcase-tooltip' );
}

add_action( 'wp_ajax_lshowcase', 'lshowcase_run_preview' );

function lshowcase_run_preview()
{
	$orderby = $_POST['porder'];
	$category = $_POST['pcategory'];
	$activeurl = $_POST['purl'];
	$style = $_POST['pstyle'];
	$interface = $_POST['pinterface'];
	$tooltip = $_POST['ptooltip'];
	$limit = $_POST['plimit'];
	$slidersettings = "";
	$img = $_POST['pimg'];
	$html = build_lshowcase($orderby, $category, $activeurl, $style, $interface, $tooltip, $limit, $slidersettings, $img);
	echo $html;
	die(); // this is required to return a proper result
}

function lshowcase_shortcode_page()
{ 
	settings_fields( 'lshowcase-plugin-settings' );
	$options = get_option( 'lshowcase-settings' );
	
	?>
	
<h1>Shortcode Generator</h1>
    
     
    <table cellpadding="10" cellspacing="10">
      <tr><td valign="top">
    <div class="postbox" style="width:300px;">
    <form id="shortcode_generator" style="padding:20px;">
           
<p>
        <label for="orderby">Order By:</label>
        <select id="orderby" name="orderby" onChange="lshowcaseshortcodegenerate()">
            <option value="none">Default (Order Field)</option>
            <option value="name">Title</option>
            <option value="ID">ID</option>
            <option value="date">Date</option>
            <option value="modified">Modified</option>
            <option value="rand">Random</option>
        </select></p>
 	 <p><label for="limit">Number of Images to display:</label><input size="3" id="limit" name="limit" type="text" value="0" onChange="lshowcaseshortcodegenerate()" /><span class="howto"> (Leave blank or 0 to display all)</span></p>
    
<p><label for="category">Category</label>:
     
        <select id="category" name="category" onChange="lshowcaseshortcodegenerate()">
          <option value="0">All</option>
        
  <?php
	$terms = get_terms( "lshowcase-categories" );
	$count = count($terms);
	if ($count > 0) {
		foreach($terms as $term) {
			echo "<option value='" . $term->slug . "'>" . $term->name . "</option>";
		}
	}

?></select></p>
 <p>
            <label for="activeurl">URL:
            </label>
        <select id="activeurl" name="activeurl" onChange="lshowcaseshortcodegenerate()">
          <option value="inactive">Inactive</option>
          <option value="new">Open in new window</option>
          <option value="same">Open in same window</option>
        </select></p>
         
  
  
   <p>
     <label for="style">Style:</label>
        
        <select id="style" name="style" onChange="lshowcaseshortcodegenerate()">
          <?php
	$stylesarray = lshowcase_styles_array();
	foreach($stylesarray as $option => $key) {
?>
          
          <option value="<?php
		echo $option; ?>"><?php
		echo $key['description']; ?></option>
          <?php
	} ?>
		</select></p>

		<p>
		     <label for="tooltip">Show Tooltip:</label>
		       
		        <select id="tooltip" name="tooltip" onChange="lshowcaseshortcodegenerate()">
		          
		          <option value="false">No</option> 
		          <option value="true">Yes - Show Title</option> 
		          <option value="true-description">Yes - Show Description</option> 
		          
		</select>

		</p>

		

       
        <p>Layout:
          
          <select id="interface" name="interface" onChange="lshowcaseshortcodegenerate()">
          <option value="hcarousel" selected>Horizontal Carousel</option>
          <option value="grid"  >Normal Grid</option>
          <option value="grid12" >Responsive Grid - 12 Columns</option> 
          <option value="grid11" >Responsive Grid - 11 Columns</option>
          <option value="grid10" >Responsive Grid - 10 Columns</option>
          <option value="grid9" >Responsive Grid - 9 Columns</option>
          <option value="grid8" >Responsive Grid - 8 Columns</option>
          <option value="grid7" >Responsive Grid - 7 Columns</option> 
          <option value="grid6" >Responsive Grid - 6 Columns</option> 
          <option value="grid5" >Responsive Grid - 5 Columns</option>  
          <option value="grid4" >Responsive Grid - 4 Columns</option>
          <option value="grid3" >Responsive Grid - 3 Columns</option>
          <option value="grid2" >Responsive Grid - 2 Columns</option>
          <option value="grid1" >Responsive Grid - 1 Columns</option>     
          
</select></p>



<div id="ls_carousel_type">
	<p id="ls_carousel_settings_option" style="display:none;">
		<label for="">Carousel Settings: </label>
		<input name="use_defaults" id="use_defaults" type="radio" value="1" checked="checked" onclick="hidecustomsettings();" />
              Default
                <input name="use_defaults" id="use_defaults" type="radio" value="0" onclick="showcustomsettings();" />
              Custom
	</p>
	<div id="ls_carousel_settings" style="display:none; background:#FFF; padding:5px;"> 
		
<table width="100%">
  <tr>
    <td nowrap >Auto Scroll</td>
    <td><select name="lshowcase_carousel_autoscroll" onChange="lshowcaseshortcodegenerate()">
      <option value="true"  <?php
	selected($options['lshowcase_carousel_autoscroll'], 'true' ); ?>>Yes - With Pause</option>
      <option value="ticker"  <?php
	selected($options['lshowcase_carousel_autoscroll'], 'ticker' ); ?>>Yes - Non Stop</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autoscroll'], 'false' ); ?>>No</option>
    </select></td>
  
  
  </table>
  <table width="100%" id="lst_pause_time">
  
  <tr>
    <td nowrap >Pause Time</td>
    <td><input type="text" name="lshowcase_carousel_pause" value="<?php
	echo $options['lshowcase_carousel_pause']; ?>" onChange="lshowcaseshortcodegenerate()" size="10" /></td>
  </tr><tr><td colspan="2"><span class="howto">The amount of time (in ms) between each auto transition</span></td>
  </tr>
  </table>
  <table width="100%" id="lst_pause_hover">
  <tr>
    <td nowrap >Pause on Hover</td>
    <td><select name="lshowcase_carousel_autohover" onChange="lshowcaseshortcodegenerate()">
      <option value="true" <?php
	selected($options['lshowcase_carousel_autohover'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autohover'], 'false' ); ?>>No</option>
    </select></td>
  </tr><tr><td colspan="2"><span class="howto">Auto scroll will pause when mouse hovers over slider</span></td>
  </tr>
  
  </table>
  <table width="100%" id="lst_auto_controls">
  
  <tr>
    <td nowrap >Auto Controls</td>
    <td><select name="lshowcase_carousel_autocontrols" onChange="lshowcaseshortcodegenerate()">
      <option value="true" <?php
	selected($options['lshowcase_carousel_autocontrols'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autocontrols'], 'false' ); ?>>No</option>
    </select></td>
  </tr><tr><td colspan="2"><span class="howto">If active, "Start" / "Stop" controls will be added</span></td>
  </tr>
  
  </table>
  <table width="100%">
  
  <tr>
    <td nowrap >Transition Speed:</td>
    <td><input type="text" name="lshowcase_carousel_speed" value="<?php
	echo $options['lshowcase_carousel_speed']; ?>" onChange="lshowcaseshortcodegenerate()" size="10" /></td>
  </tr><tr><td colspan="2"><span class="howto">Slide transition duration (in ms - intenger) </span></td>
  </tr>
  <tr>
    <td nowrap >Image Margin:</td>
    <td><input type="text" size="10" name="lshowcase_carousel_slideMargin" value="<?php
	echo $options['lshowcase_carousel_slideMargin']; ?>" onChange="lshowcaseshortcodegenerate()" /></td>
  </tr><tr><td colspan="2"><span class="howto">Margin between each image (intenger)</span></td>
  </tr>
  </table>
  <table width="100%" id="lst_carousel_common_settings">
  <tr>
    <td nowrap >Infinite Loop:</td>
    <td><select name="lshowcase_carousel_infiniteLoop" onChange="lshowcaseshortcodegenerate()">
      <option value="true" <?php
	selected($options['lshowcase_carousel_infiniteLoop'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_infiniteLoop'], 'false' ); ?>>No</option>
    </select></td>

  <tr>
    <td nowrap >Show Pager:</td>
    <td><select name="lshowcase_carousel_pager" onChange="lshowcaseshortcodegenerate()">
      <option value="true" <?php
	selected($options['lshowcase_carousel_pager'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_pager'], 'false' ); ?>>No</option>
    </select></td>
  </tr><tr><td colspan="2"><span class="howto">If Active, a pager will be added.</span></td>
  </tr>
  <tr>
    <td nowrap >Show Controls:</td>
    <td><select name="lshowcase_carousel_controls" onChange="lshowcaseshortcodegenerate()">
      <option value="true" <?php
	selected($options['lshowcase_carousel_controls'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_controls'], 'false' ); ?>>No</option>
    </select></td>
  </tr><tr><td colspan="2"><span class="howto">If Active, "Next" / "Prev" image controls will be added.</span></td>
  </tr>
  <tr>
    <td nowrap >Minimum Slides:</td>
    <td><input size="10" type="text" name="lshowcase_carousel_minSlides" value="<?php
	echo $options['lshowcase_carousel_minSlides']; ?>" onChange="lshowcaseshortcodegenerate()" /></td>
  </tr><tr><td colspan="2"><span class="howto">The minimum number of slides to be shown.</span></td>
  </tr>
  <tr>
    <td nowrap >Maximum Slides:</td>
    <td><input size="10" type="text" name="lshowcase_carousel_maxSlides" value="<?php
	echo $options['lshowcase_carousel_maxSlides']; ?>" onChange="lshowcaseshortcodegenerate()" /></td>
  </tr><tr><td colspan="2"><span class="howto">The maximum number of slides to be shown.</span></td>
  </tr>
  <tr>
    <td nowrap >Slides to move:</td>
    <td><input size="10" type="text" name="lshowcase_carousel_moveSlides" value="<?php
	echo $options['lshowcase_carousel_moveSlides']; ?>" onChange="lshowcaseshortcodegenerate()" /></td>
  </tr><tr><td colspan="2"><span class="howto">The number of slides to move on transition.  If zero, the number of fully-visible slides will be used.</span></td>
</tr>
</table>
<table width="100%" style="border-top:1px dashed #CCC; margin-top:20px; padding:10px;">
<tr>
    <td nowrap >Image Size Override:</td>
    <td><input size="10" type="text" name="lshowcase_image_size_overide" value="" size="10" onChange="lshowcaseshortcodegenerate()" /></td>
  </tr><tr><td colspan="2"><span class="howto">Leave blank to use default values.
In case you want to override the default image size settings, use this field to put the width and height values in the following format: width,height
ex. 100,100.
Smallest value will prevail if images don't have exactly this size.</span></td>
</tr>




</table>


	</div>

</div>

</form>
    </div>
    </td><td valign="top"><h3>Shortcode</h3> 
    Use this shortcode to display the list of logos in your posts or pages! Just copy this piece of text and place it where you want it to display.
    
      <div id="shortcode" style="padding:10px; background-color:#f5f5f5;"></div>
    
    <h3>PHP Function</h3>
    Use this PHP function to display the list of logos directly in your theme files!
    <div id="phpcode" style="padding:10px; background-color:#f5f5f5;"> </div>
    
    <h3> Preview</h3>
      
	<div id="preview"></div>
    <div id="hcarouselhelp" style="margin-top:50px; display:none;">You can also change the way the Carousel/Slider works in the <a href="edit.php?post_type=lshowcase&page=lshowcase_settings">Settings Page</a></div>
    
      </td></tr></table>
    
    
<?php
	$options = get_option( 'lshowcase-settings' );
	$mode = "'horizontal'";
	$slidewidth = $options['lshowcase_thumb_width'];
	
	$autoscroll = $options['lshowcase_carousel_autoscroll'];
	$pausetime = $options['lshowcase_carousel_pause'];
	$autohover = $options['lshowcase_carousel_autohover'];
	$pager = $options['lshowcase_carousel_pager'];
	$tickerhover = $autohover;
	$ticker = 'false';
	$usecss = 'true';
	$auto = 'true';

	if ($autoscroll == 'false') {
		$auto = 'false';
	}

	if ($autoscroll=='ticker') {
		$ticker = 'true';
		$tickerhover = $autohover;
		$autoscroll = 'true';
		$pager = 'false';
		$auto = 'false';
		
		if ($tickerhover=='true') {
			$usecss = 'false';
		} 
	}

	$autocontrols = $options['lshowcase_carousel_autocontrols'];
	$speed = $options['lshowcase_carousel_speed'];
	$slidemargin = $options['lshowcase_carousel_slideMargin'];
	$loop = $options['lshowcase_carousel_infiniteLoop'];
	$controls = $options['lshowcase_carousel_controls'];
	$minslides = $options['lshowcase_carousel_minSlides'];
	$maxslides = $options['lshowcase_carousel_maxSlides'];
	$moveslides = $options['lshowcase_carousel_moveSlides'];



?>
<script type="text/javascript">

	
	
	function checkslider()
	{
	
		 
		 var layout = document.getElementById( 'interface' ).value;

		 

		if(document.getElementsByName('use_defaults')[1].checked) { 

			
			var slidewidth = <?php echo $slidewidth; ?>;

			var imgwo = document.getElementsByName('lshowcase_image_size_overide')[0].value;
			if (imgwo!="") {

				 var imgwarray = imgwo.split(",");
				 slidewidth = parseInt(imgwarray[0]);
			};

			var autoscroll = document.getElementsByName('lshowcase_carousel_autoscroll')[0].value;
			var pause = parseInt(document.getElementsByName('lshowcase_carousel_pause')[0].value);
			
			var autohover = (document.getElementsByName('lshowcase_carousel_autohover')[0].value === 'true');
			var pager = (document.getElementsByName('lshowcase_carousel_pager')[0].value === 'true');

			

			var tickerhover = autohover;
			var ticker = false;			
			var usecss = true;
			var auto = true;


			if (autoscroll == 'false') {
				auto = false;
			}

			if (autoscroll=='ticker') {
				ticker = true;
				tickerhover = autohover;
				pager = false;
				auto = false;
				
				if (tickerhover==true) {
					usecss = false;
				} 
			}


			var autocontrols = (document.getElementsByName('lshowcase_carousel_autocontrols')[0].value === 'true');
			var speed = parseInt(document.getElementsByName('lshowcase_carousel_speed')[0].value);
			var slidemargin = parseInt(document.getElementsByName('lshowcase_carousel_slideMargin')[0].value);
			var infiniteloop = (document.getElementsByName('lshowcase_carousel_infiniteLoop')[0].value === 'true');
			
			var controls = (document.getElementsByName('lshowcase_carousel_controls')[0].value === 'true');
			var minslides = parseInt(document.getElementsByName('lshowcase_carousel_minSlides')[0].value);
			var maxslides = parseInt(document.getElementsByName('lshowcase_carousel_maxSlides')[0].value);
			var moveslides = parseInt(document.getElementsByName('lshowcase_carousel_moveSlides')[0].value);

		}


		else {

			 var mode = <?php echo $mode; ?>;
			 var slidewidth = <?php echo $slidewidth; ?>;
			 var auto = <?php echo $auto; ?>;
			 var pause = <?php echo $pausetime; ?>;
			 var autohover = <?php echo $autohover; ?>;
			 var ticker = <?php echo $ticker; ?>;
			 var tickerhover = <?php echo $tickerhover; ?>;
			 var usecss = <?php echo $usecss; ?>;
			 var autocontrols = <?php echo $autocontrols; ?>;
			 var speed = <?php echo $speed; ?>;
			 var slidemargin = <?php echo $slidemargin; ?>;
			 var infiniteloop = <?php echo $loop; ?> ;
			 var pager = <?php echo $pager; ?>;
			 var controls = <?php echo $controls; ?>;		 
			 var minslides = <?php echo $minslides; ?>;
			 var maxslides = <?php echo $maxslides; ?>;
			 var moveslides = <?php echo $moveslides; ?>;
		}
	
	if(layout=="hcarousel" ) {

		 var sliderDiv = jQuery( '.lshowcase-wrap-carousel-0' );

		sliderDiv.fadeIn('slow');
			
		sliderDiv.bxSlider({
		
			auto: auto,		
			pause: pause,
			autoHover: autohover,
			ticker: ticker,
			tickerHover: tickerhover,
			useCSS: usecss,
			autoControls: autocontrols,
			mode: mode,
			speed: speed,
			slideMargin: slidemargin,
			infiniteLoop: infiniteloop,
		    pager: pager, 
			controls: controls,
		    slideWidth: slidewidth+20,
		    minSlides: minslides,
		    maxSlides: maxslides,
		    moveSlides: moveslides,
		    autoDirection: 'next'	//change to 'prev' if you want to reverse order

			});
		}
	}
	
	
	function checktooltip() {
		
	var tooltip = document.getElementById( 'tooltip' ).value;
	
	if(tooltip=="true" || tooltip=="true-description") {
		
			jQuery( '.lshowcase-tooltip' ).tooltip({
			content: function () { return jQuery(this).attr('title') },
			position: {
			my: "center bottom-20",
			at: "center top",
			using: function( position, feedback ) {
			jQuery( this ).css( position );
			jQuery( "<div>" )
			.addClass( "lsarrow" )
			.addClass( feedback.vertical )
			.addClass( feedback.horizontal )
			.appendTo( this );
			}
			}
			});
		}

	}
	
	
	
	 </script>
     <?php
}
?>