<?php
/*
Plugin Name: Logos Showcase
Plugin URI: http://www.cmoreira.net/logos-showcase
Description: This plugin allows you to display images on a responsive grid or carousel. It's perfect to display logos of clients, partners, sponsors or any other group of images that requires this type of layout.
Author: Carlos Moreira
Version: 1.2.4
Author URI: http://cmoreira.net
*/

// Last modified: September 23 2013
// Last Edit:
// CSS fix for extra top padding 
// quotes bug for tooltip


// ordering code

require_once dirname(__FILE__) . '/ordering-code.php';

// shortcode generator page

require_once dirname(__FILE__) . '/shortcode-generator.php';

// widget code

require_once dirname(__FILE__) . '/widget-code.php';


//count for multiple pager layouts in same page

$lshowcase_slider_count = 0;
$lshowcase_slider_array = array();

// Adding the necessary actions to initiate the plugin

add_action( 'init', 'register_cpt_lshowcase' );
add_action( 'admin_init', 'register_lshowcase_settings' );
add_action( 'do_meta_boxes', 'lshowcase_image_box' );
add_action( 'admin_menu', 'lshowcase_shortcode_page_add' );
add_action( 'admin_menu', 'lshowcase_admin_page' );
add_filter( 'manage_posts_columns', 'lshowcase_columns_head' );
add_action( 'manage_posts_custom_column', 'lshowcase_columns_content', 10, 2 );

// Add support for post-thumbnails in case theme does not

add_action( 'init', 'lshowcase_add_thumbnails_for_cpt' );

function lshowcase_add_thumbnails_for_cpt()
{
	global $_wp_theme_features;
	if ($_wp_theme_features['post-thumbnails'] == 1) {
		return;
	}

	if (is_array($_wp_theme_features['post-thumbnails'][0]) && count($_wp_theme_features['post-thumbnails'][0]) >= 1) {
		array_push($_wp_theme_features['post-thumbnails'][0], 'lshowcase' );
		return;
	}

	if (empty($_wp_theme_features['post-thumbnails'])) {
		$_wp_theme_features['post-thumbnails'] = array(
			array(
				'lshowcase'
			)
		);
		return;
	}
}

// Add New Thumbnail Size

$lshowcase_crop = false;
$lshowcase_options = get_option( 'lshowcase-settings' );

if ($lshowcase_options['lshowcase_thumb_crop'] == "true" ) {
	$lshowcase_crop = true;
}

add_image_size( 'lshowcase-thumb', $lshowcase_options['lshowcase_thumb_width'], $lshowcase_options['lshowcase_thumb_height'], $lshowcase_crop);

// register the custom post type for the logos showcase

function register_cpt_lshowcase()
{
	$options = get_option( 'lshowcase-settings' );
	$name = $options['lshowcase_name_singular'];
	$nameplural = $options['lshowcase_name_plural'];
	$labels = array(
		'name' => _x($nameplural, 'lshowcase' ) ,
		'singular_name' => _x($name, 'lshowcase' ) ,
		'add_new' => _x( 'Add New ' . $name, 'lshowcase' ) ,
		'add_new_item' => _x( 'Add New ' . $name, 'lshowcase' ) ,
		'edit_item' => _x( 'Edit ' . $name, 'lshowcase' ) ,
		'new_item' => _x( 'New ' . $name, 'lshowcase' ) ,
		'view_item' => _x( 'View ' . $name, 'lshowcase' ) ,
		'search_items' => _x( 'Search ' . $nameplural, 'lshowcase' ) ,
		'not_found' => _x( 'No ' . $nameplural . ' found', 'lshowcase' ) ,
		'not_found_in_trash' => _x( 'No ' . $nameplural . ' found in Trash', 'lshowcase' ) ,
		'parent_item_colon' => _x( 'Parent ' . $name . ':', 'lshowcase' ) ,
		'menu_name' => _x($nameplural, 'lshowcase' ) ,
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'supports' => array(
			'title',
			'thumbnail',
			'custom-fields',
			'page-attributes'
		) ,
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'has_archive' => true,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'menu_icon' => plugins_url( 'images/icon16.png', __FILE__) ,
	);
	register_post_type( 'lshowcase', $args);
}

// register custom category
// WP Menu Categories

add_action( 'init', 'lshowcase_build_taxonomies', 0);

function lshowcase_build_taxonomies()
{
	register_taxonomy( 'lshowcase-categories', 'lshowcase', array(
		'hierarchical' => true,
		'label' => 'Categories',
		'query_var' => true,
		'rewrite' => true
	));
}

// move featured image box to top

function lshowcase_image_box()
{
	remove_meta_box( 'postimagediv', 'lshowcase', 'side' );
	add_meta_box( 'postimagediv', __( 'Logo Image' ) , 'post_thumbnail_meta_box', 'lshowcase', 'normal', 'high' );
}

// change Title Info

function lshowcase_change_default_title($title)
{
	$screen = get_current_screen();
	$options = get_option( 'lshowcase-settings' );
	$name = $options['lshowcase_name_singular'];
	$nameplural = $options['lshowcase_name_plural'];
	if ( 'lshowcase' == $screen->post_type) {
		$title = 'Insert ' . $name . ' Name Here';
	}

	return $title;
}

add_filter( 'enter_title_here', 'lshowcase_change_default_title' );

function lshowcase_wps_translation_mangler($translation, $text, $domain)
{
	global $post;
	if (isset($post)) {
		if ($post->post_type == 'lshowcase' ) {
			$translations = & get_translations_for_domain($domain);
			if ($text == 'Publish' ) {
				return $translations->translate( 'SAVE' );
			}
		}
	}

	return $translation;
}

add_filter( 'gettext', 'lshowcase_wps_translation_mangler', 10, 4);

// Order by menu_order in the ADMIN screen

function lshowcase_admin_order($wp_query)
{
	if (is_post_type_archive( 'lshowcase' ) && is_admin()) {
		if (!isset($_GET['orderby'])) {
			$wp_query->set( 'orderby', 'menu_order' );
			$wp_query->set( 'order', 'ASC' );
		}
	}
}

// This will default the ordering admin to the 'menu_order' - will disable other ordering options

add_filter( 'pre_get_posts', 'lshowcase_admin_order' );
/**
 * Display the URL metabox
 */

function lshowcase_url_custom_metabox()
{
	global $post;
	$urllink = get_post_meta($post->ID, 'urllink', true);
	$urldesc = get_post_meta($post->ID, 'urldesc', true);
	if ($urllink != "" && !preg_match( "/http(s?):\/\//", $urllink)) {
		$errors = 'Url not valid';
		$urllink = 'http://';
	}

	// output invlid url message and add the http:// to the input field

	if (isset($errors)) {
		echo $errors;
	} ?>
<table cellpadding="10"><tr><td valign="top">	
<p><label for="siteurl">Url:<br />
  <input id="siteurl" size="37" name="siteurl" type="url" value="<?php
	if ($urllink) {
		echo $urllink;
	} ?>" /></label></p>
        </td><td valign="top">
	<p><label for="urldesc">Description:<br />

		<textarea id="urldesc" name="urldesc" ><?php
	if ($urldesc) {
		echo $urldesc;
	} ?></textarea></label></p>
        </td>
        <td valign="top"><p>Use this fields to fill out the URL you want your Image Logo to have. <br />
          The description field will be the alt tag (alternative text) for the URL and Image.</p>
        <p>Don't forget to set a featured Image below. </p></td>
</tr>
</table>
<?php
}

/**
 * Process the custom metabox fields
 */

function lshowcase_save_custom_url($post_id)
{
	global $post;
	if (isset($post)) {
		if ($post->post_type == 'lshowcase' ) {
			if ($_POST) {
				update_post_meta($post->ID, 'urllink', $_POST['siteurl']);
				update_post_meta($post->ID, 'urldesc', $_POST['urldesc']);
			}
		}
	}
}

// Add action hooks. Without these we are lost

add_action( 'admin_init', 'lshowcase_add_custom_metabox' );
add_action( 'save_post', 'lshowcase_save_custom_url' );
/**
 * Add meta box
 */

function lshowcase_add_custom_metabox()
{
	add_meta_box( 'lshowcase-custom-metabox', __( 'URL &amp; Description' ) , 'lshowcase_url_custom_metabox', 'lshowcase', 'normal', 'high' );
}

/**
 * Get and return the values for the URL and description
 */

function lshowcase_get_url_desc_box()
{
	global $post;
	$urllink = get_post_meta($post->ID, 'urllink', true);
	$urldesc = get_post_meta($post->ID, 'urldesc', true);
	return array(
		$urllink,
		$urldesc
	);
}

// get the array of data
// $urlbox = get_url_desc_box();
// echo $urlbox[0]; // echo out the url of a post
// echo $urlbox[1]; // echo out the url description of a post


// add options page

function lshowcase_admin_page()
{
	$menu_slug = 'edit.php?post_type=lshowcase';
	$submenu_page_title = 'Settings';
	$submenu_title = 'Settings';
	$capability = 'manage_options';
	$submenu_slug = 'lshowcase_settings';
	$submenu_function = 'lshowcase_settings_page';
	$defaultp = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
}

// options page build

function lshowcase_settings_page()
{
?>
    <div class="wrap">
<h2>Settings</h2>
    <?php
	if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == "true" ) {
		$msg = "Settings Updated";
		lshowcase_message($msg);
	} ?>
	<form method="post" action="options.php" id="dsform">
    <?php
	settings_fields( 'lshowcase-plugin-settings' );
	$options = get_option( 'lshowcase-settings' );
?>
    <table width="70%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td colspan="3"><h2>Logo Showcase Names</h2></td>
    </tr>
  <tr>
    <td align="right">Singular Name:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_name_singular]" value="<?php
	echo $options['lshowcase_name_singular']; ?>" /></td>
    <td rowspan="2" valign="top"><p class="howto">What do you want to call this feature?</p>
      <p class="howto">For Administration purposes only.</p></td>
  </tr>
  <tr>
    <td align="right">Plural Name:</td>
    <td>    <input type="text" name="lshowcase-settings[lshowcase_name_plural]" value="<?php
	echo $options['lshowcase_name_plural']; ?>" />
</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><h2>Logo Image Size Settings</h2></td>
    </tr>
  <tr>
    <td align="right">Width</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_thumb_width]" value="<?php
	echo $options['lshowcase_thumb_width']; ?>" /></td>
    <td rowspan="3" valign="top"><span class="howto">This will be the size of the Images. When they are uploaded they will follow this settings. If you change this settings after the image is uploaded they will show scaled.</span></td>
  </tr>
  <tr>
    <td align="right">Height</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_thumb_height]" value="<?php
	echo $options['lshowcase_thumb_height']; ?>" /></td>
    </tr>
  <tr>
    <td align="right">Crop</td>
    <td><select name="lshowcase-settings[lshowcase_thumb_crop]">
      <option value="true" <?php
	selected($options['lshowcase_thumb_crop'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_thumb_crop'], 'false' ); ?>>No</option>
    </select></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><h2>Carousel Settings</h2></td>
    </tr>
  <tr>
    <td align="right" nowrap>Auto Scroll</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_autoscroll]">
      <option value="true"  <?php
	selected($options['lshowcase_carousel_autoscroll'], 'true' ); ?>>Yes - Auto Scroll With Pause</option>
      <option value="ticker"  <?php
	selected($options['lshowcase_carousel_autoscroll'], 'ticker' ); ?>>Yes - Auto Scroll Non Stop</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autoscroll'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">Slides will automatically transition</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Pause Time</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_pause]" value="<?php
	echo $options['lshowcase_carousel_pause']; ?>" /></td>
    <td><span class="howto">The amount of time (in ms) between each auto transition (if Auto Scroll with Pause is On)</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Pause on Hover</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_autohover]">
      <option value="true" <?php
	selected($options['lshowcase_carousel_autohover'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autohover'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">Auto scroll will pause when mouse hovers over slider</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Auto Controls</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_autocontrols]">
      <option value="true" <?php
	selected($options['lshowcase_carousel_autocontrols'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_autocontrols'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">If active, "Start" / "Stop" controls will be added (Doesn't work for Auto Scroll Non Stop)</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Transition Speed:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_speed]" value="<?php
	echo $options['lshowcase_carousel_speed']; ?>" /></td>
    <td><span class="howto">Slide transition duration (in ms - intenger) </span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Image Margin:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_slideMargin]" value="<?php
	echo $options['lshowcase_carousel_slideMargin']; ?>" /></td>
    <td><span class="howto">Margin between each image (intenger)</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Infinite Loop:</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_infiniteLoop]">
      <option value="true" <?php
	selected($options['lshowcase_carousel_infiniteLoop'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_infiniteLoop'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">If Active, clicking "Next" while on the last slide will transition to the first slide and vice-versa</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Show Pager:</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_pager]">
      <option value="true" <?php
	selected($options['lshowcase_carousel_pager'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_pager'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">If Active, a pager will be added. (Doesn't work for Auto Scroll Non Stop)</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Show Controls:</td>
    <td><select name="lshowcase-settings[lshowcase_carousel_controls]">
      <option value="true" <?php
	selected($options['lshowcase_carousel_controls'], 'true' ); ?>>Yes</option>
      <option value="false" <?php
	selected($options['lshowcase_carousel_controls'], 'false' ); ?>>No</option>
    </select></td>
    <td><span class="howto">If Active, "Next" / "Prev" image controls will be added. (Doesn't work for Auto Scroll Non Stop)</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Minimum Slides:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_minSlides]" value="<?php
	echo $options['lshowcase_carousel_minSlides']; ?>" /></td>
    <td><span class="howto">The minimum number of slides to be shown. Slides will be sized down if carousel becomes smaller than the original size.</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Maximum Slides:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_maxSlides]" value="<?php
	echo $options['lshowcase_carousel_maxSlides']; ?>" /></td>
    <td><span class="howto">The maximum number of slides to be shown. Slides will be sized up if carousel becomes larger than the original size.</span></td>
  </tr>
  <tr>
    <td align="right" nowrap>Number of Slides to move:</td>
    <td><input type="text" name="lshowcase-settings[lshowcase_carousel_moveSlides]" value="<?php
	echo $options['lshowcase_carousel_moveSlides']; ?>" /></td>
    <td><span class="howto">The number of slides to move on transition.  If zero, the number of fully-visible slides will be used.</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  
 </table>

    
    
	<input type="submit" class="button-primary" value="<?php
	_e( 'Save Changes' ) ?>" />
</form>
<?php
}

// register settings

function register_lshowcase_settings()
{
	register_setting( 'lshowcase-plugin-settings', 'lshowcase-settings' );
}

// register default values

register_activation_hook(__FILE__, 'lshowcase_defaults' );

function lshowcase_defaults()
{
	$tmp = get_option( 'lshowcase-settings' );

	// check for settings version

	if ((!is_array($tmp)) || !isset($tmp['lshowcase_carousel_autoscroll'])) {
		delete_option( 'lshowcase-settings' );
		$arr = array(
			"lshowcase_name_singular" => "Logo",
			"lshowcase_name_plural" => "Logos",
			"lshowcase_thumb_width" => "200",
			"lshowcase_thumb_height" => "200",
			"lshowcase_thumb_crop" => "false",
			"lshowcase_carousel_autoscroll" => "false",
			"lshowcase_carousel_pause" => "4000",
			"lshowcase_carousel_autohover" => "false",
			"lshowcase_carousel_autocontrols" => "false",
			"lshowcase_carousel_speed" => "500",
			"lshowcase_carousel_slideMargin" => "10",
			"lshowcase_carousel_infiniteLoop" => "true",
			"lshowcase_carousel_pager" => "false",
			"lshowcase_carousel_controls" => "true",
			"lshowcase_carousel_minSlides" => "1",
			"lshowcase_carousel_maxSlides" => "8",
			"lshowcase_carousel_moveSlides" => "1",
			"lshowcase_capability_type_settings" => "manage_options",
			"lshowcase_capability_type_manage" => "manage_options",
			"lshowcase_empty" => "2",
		);
		update_option( 'lshowcase-settings', $arr);
	}
}

// To Show styled messages

function lshowcase_message($msg)
{ ?>
  <div id="message" class="updated"><p><?php
	echo $msg; ?></p></div>
<?php
}

// Add new column

function lshowcase_columns_head($defaults)
{
	global $post;
	if ($post->post_type == 'lshowcase' ) {
		$defaults['featured_image'] = 'Image';
	}

	return $defaults;
}

// SHOW THE FEATURED IMAGE in admin

function lshowcase_columns_content($column_name, $post_ID)
{
	global $post;
	if ($post->post_type == 'lshowcase' ) {
		if ($column_name == 'featured_image' ) {
			echo get_the_post_thumbnail($post_ID, array(
				80,
				80
			));
		}
	}
}

// Shortcode
// Add shortcode functionality

add_shortcode( 'show-logos', 'shortcode_lshowcase' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );

function shortcode_lshowcase($atts)
{
	$orderby = (array_key_exists( 'orderby', $atts) ? $atts['orderby'] : "menu_order" );
	$category = (array_key_exists( 'category', $atts) ? $atts['category'] : "0" );
	$style = (array_key_exists( 'style', $atts) ? $atts['style'] : "normal" );
	$interface = (array_key_exists( 'interface', $atts) ? $atts['interface'] : "grid" );
	$activeurl = (array_key_exists( 'activeurl', $atts) ? $atts['activeurl'] : "inactive" );
	$tooltip = (array_key_exists( 'tooltip', $atts) ? $atts['tooltip'] : "false" );
	$limit = (array_key_exists( 'limit', $atts) ? $atts['limit'] : 0);
	$slidersettings = (array_key_exists( 'carousel', $atts) ? $atts['carousel'] : "");
	$img = (array_key_exists( 'img', $atts) ? $atts['img'] : 0);
	$html = build_lshowcase($orderby, $category, $activeurl, $style, $interface, $tooltip, $limit, $slidersettings,$img);
	return $html;
}



/*
*
* /////////////////////////////
* FUNCTION TO DISPLAY THE LOGOS
* /////////////////////////////
*
*/

function build_lshowcase($order = "menu_order", $category = "", $activeurl = "new", $style = "normal", $interface = "grid", $tooltip = "false", $limit = - 1, $slidersettings="", $imgwo=0)
{
	global $lshowcase_slider_count;
	global $post;


	//image size override
	$imgwidth = "";
	if($imgwo!="" && $imgwo!=0 && $imgwo!='0'){
		$imgwidth = explode(',',$imgwo);
		}


	$html = "";
	$thumbsize = "lshowcase-thumb";
	$class = "lshowcase-thumb";
	$divwrap = "lshowcase-wrap-normal";
	$divwrapextra = "";
	$divboxclass = "lshowcase-box-normal";
	$divboxinnerclass = "lshowcase-boxInner-normal";
	if ($order == 'none' ) {
		$order = 'menu_order';
	};
	if ($interface != "grid" && $interface != "hcarousel" && $interface != "vcarousel" ) {
		$columncount = substr($interface, 4);
		$divboxclass = "lshowcase-box-" . $columncount;
		$divboxinnerclass = "lshowcase-boxInner";
		$divwrap = "lshowcase-wrap-responsive";
	}

	if ($interface == "hcarousel" ) {
		$divwrapextra = "style='display:none;' class='lshowcase-wrap-carousel-".$lshowcase_slider_count."'";
		$class = "lshowcase-thumb";
		$divwrap = "lshowcase-wrap-normal";
		$divboxclass = "lshowcase-box-normal";
		$divboxinnerclass = "lshowcase-slide";
		lshowcase_add_carousel_js();
	}

	$stylearray = lshowcase_styles_array();
	$class = $stylearray[$style]["class"];

	//tooltip code
	if ($tooltip == 'true' || $tooltip == 'true-description' ) {
		$class.= " lshowcase-tooltip";

		lshowcase_add_tooltip_js();
	}

	$postsperpage = - 1;
	$nopaging = true;
	if ($limit >= 1) {
		$postsperpage = $limit;
		$nopaging = false;
	}

	$ascdesc = 'DESC';
	if ($order == 'name' || $order == 'menu_order' ) {
		$ascdesc = 'ASC';
	};
	$args = array(
		'post_type' => 'lshowcase',
		'lshowcase-categories' => $category,
		'orderby' => $order,
		'order' => $ascdesc,
		'posts_per_page' => $postsperpage,
		'nopaging' => $nopaging
	);
	$loop = new WP_Query($args);

	// to force random again - uncomment in case random is not working
	// if($order=='rand' ) {
	// shuffle( $loop->posts );
	// }

	$html.= '<div class="lshowcase-clear-both">&nbsp;</div>';
	$html.= '<div class="lshowcase-logos"><div ' . $divwrapextra . ' >';
	while ($loop->have_posts()):
		$loop->the_post();
		if (has_post_thumbnail()):

			//check if there is img overide settings
			if(is_array($imgwidth)) {
				$thumbsize = $imgwidth;
				
			}


			$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , $thumbsize);
			$lshowcase_options = get_option( 'lshowcase-settings' );
			$width = $image[1];
			$height = " height = '".$image[2]."' ";
			$dwidth = $lshowcase_options['lshowcase_thumb_width'];
			$desc = get_post_meta(get_the_ID() , 'urldesc', true);

			//to filter the quotes and make them html compatible
			$desc = str_replace("'", '&apos;', $desc);

			if(is_array($imgwidth)) {
				$dwidth = $thumbsize[0];
			}

			if ($interface != "hcarousel" ) {
				$html.= "<div class='" . $divwrap . "'>";
				$html.= '<div class="' . $divboxclass . '">';
				$height = "";
			}

			if ($interface == "grid" ) {
				$html.= '<div class="' . $divboxinnerclass . '" style="width:' . $dwidth . 'px; align:center; text-align:center;">';
			}
			else {
				$html.= '<div class="' . $divboxinnerclass . '">';
			}

			$url = get_post_meta(get_the_ID() , 'urllink', true);

			//set default attributes for tooltip 
			if ($tooltip=="true" || $tooltip=="false") {
				$alt = $desc;
				$title = the_title_attribute( 'echo=0' );
			}

			//switch attributes to reflect on toolip
			if($tooltip=="true-description") {
				$title = $desc;
				$alt = the_title_attribute( 'echo=0' );
			}	



			if ($activeurl != "inactive" && $url != "" ) {
				$target = "";
				if ($activeurl == "new" ) {
					$target = "target='_blank'";
				}

				$html.= "<a href='" . $url . "' alt='" . $alt . "' " . $target . ">";
				$html.= "<img src='" . $image[0] . "' width='" . $width . "' ".$height." alt='" . $alt . "' title='" . $title . "' class='" . $class . "' />";

				// $html .= get_the_post_thumbnail($post->ID,$thumbsize,array( 'class' => $class, 'alt'	=> $alt, 'title' => $title));

				$html.= "</a>";
			}
			else {
				$html.= "<img src='" . $image[0] . "' width='" . $width . "' ".$height." alt='" . $alt . "' title='" . $title . "' class='" . $class . "' />";

				// $html .= get_the_post_thumbnail($post->ID,$thumbsize,array( 'class' => $class, 'alt'	=> $alt, 'title' => $title));

			}


			//uncomment if you want the description content to display below the images.
			//will not work well on responsive grid layout
			//$html .= "<div class='lshowcase-description'>".$desc."</div>";

			if ($interface != "hcarousel" ) {
				$html.= "</div></div>";

			}

			$html.= "</div>";

			


		endif;
	endwhile;

	// Restore original Post Data

	wp_reset_postdata();
	$html.= '</div></div><div class="lshowcase-clear-both">&nbsp;</div>';

	//Add Carousel Code 
	if ( $interface == 'hcarousel') {

				lshowcase_bxslider_options_js($lshowcase_slider_count,$slidersettings,$dwidth);
				$lshowcase_slider_count++;
			}


	lshowcase_add_main_css();
	return $html;
}

/* CSS enqueue functions */

function lshowcase_add_main_css()
{
	wp_deregister_style( 'lshowcase-main-style' );
	wp_register_style( 'lshowcase-main-style', plugins_url( '/styles.css', __FILE__) , array() , false, false);
	wp_enqueue_style( 'lshowcase-main-style' );
}

/*   JS for Slider */

function lshowcase_add_carousel_js()
{

	// wp_enqueue_script( 'jquery' );

	wp_deregister_script( 'lshowcase-bxslider' );
	wp_register_script( 'lshowcase-bxslider', plugins_url( '/bxslider/jquery.bxslider.js', __FILE__) , array(
		'jquery'
	) , false, false);
	wp_enqueue_script( 'lshowcase-bxslider' );
	wp_deregister_style( 'lshowcase-bxslider-style' );
	wp_register_style( 'lshowcase-bxslider-style', plugins_url( '/bxslider/jquery.bxslider.css', __FILE__) , array() , false, false);
	wp_enqueue_style( 'lshowcase-bxslider-style' );
	
}

function lshowcase_add_individual_carousel_js($sliderarray)
{

	

	wp_deregister_script( 'lshowcase-bxslider-individual' );
	wp_register_script( 'lshowcase-bxslider-individual', plugins_url( '/js/carousel.js', __FILE__) , array(
		'jquery',
		'lshowcase-bxslider'
	) , false, false);
	wp_enqueue_script( 'lshowcase-bxslider-individual' );

	wp_localize_script('lshowcase-bxslider-individual', 'lssliderparam', $sliderarray);
	
	
}

/* Tooltip Scripts */

function lshowcase_add_tooltip_js()
{
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

/* Styles Function */
/* YOU CAN ADD NEW STYLES.
ADD ITEMS TO ARRAY
*/

function lshowcase_styles_array()
{
	$styles = array(
		"normal" => array(
			"class" => "lshowcase-normal",
			"description" => "Normal - No Styles",
		) ,
		"boxhighlight" => array(
			"class" => "lshowcase-boxhighlight",
			"description" => "Box Highlight on hover",
		) ,
		"grayscale" => array(
			"class" => "lshowcase-grayscale",
			"description" => "Always Grayscale",
		) ,
		"hgrayscale" => array(
			"class" => "lshowcase-hover-grayscale",
			"description" => "Grayscale and Color on hover",
		)
	);
	return $styles;
}


function lshowcase_bxslider_options_js($id, $slidersettings,$slidewidth)
{
	global $lshowcase_jquery_noconflict;
	global $lshowcase_slider_array;

	$mode = "'horizontal'";
	$options = get_option( 'lshowcase-settings' );
	if ($slidewidth=="" || $slidewidth == 0 || $slidewidth == '0') {
		$slidewidth = $options['lshowcase_thumb_width'];
	}
	
	$name = '.lshowcase-wrap-carousel-'.$id;

	if( $slidersettings == "" ) {
		
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
		
		
		

	} else {

		$carouselset = explode(',', $slidersettings);		
		$autoscroll = $carouselset[0];
		$pausetime = $carouselset[1];
		$autohover = $carouselset[2];
		$pager = $carouselset[7];
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

			if ($autohover=='true') {
				$usecss = 'false';
			} 
		}

		$autocontrols = $carouselset[3];
		$speed = $carouselset[4];
		$slidemargin = $carouselset[5];
		$loop = $carouselset[6];
		
		$controls = $carouselset[8];
		$minslides = $carouselset[9];
		$maxslides = $carouselset[10];
		$moveslides = $carouselset[11];	
		
	}

$new_ls_array = array('divid' => '.lshowcase-wrap-carousel-'.$id,
						'auto' => $auto,
						'pause' => $pausetime,
						'autohover' => $autohover,
						'ticker' => $ticker,
						'tickerhover' => $tickerhover,
						'useCSS' => $usecss,
						'autocontrols' => $autocontrols,
						'speed' => $speed,
						'slidewidth' => $slidewidth,
						'slidemargin' => $slidemargin,
						'infiniteloop' => $loop,
						'pager' => $pager,
						'controls' => $controls,
						'minslides' => $minslides,
						'maxslides' => $maxslides,
						'moveslides' => $moveslides 
						);

array_push($lshowcase_slider_array, $new_ls_array);
	
lshowcase_add_individual_carousel_js($lshowcase_slider_array);

}

?>