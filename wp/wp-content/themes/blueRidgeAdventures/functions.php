<?php
add_theme_support( 'woocommerce' );
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '',
		'after_title' => '',
	));
?>
<?php
function get_excerpt($count){
  $permalink = get_permalink($post->ID);
  $excerpt = get_the_content();
  $excerpt = strip_tags($excerpt);
  $excerpt = substr($excerpt, 0, $count);
  $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
  $excerpt = $excerpt.'...';
  return $excerpt;
}

if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' ); 
}

add_image_size( 'homeRaceLogos', 135, 135 );

function register_my_menu() {
  register_nav_menu('Header Menu',__( 'Header Menu' ));
   register_nav_menu('Upper Header Navigation',__( 'Upper Header Navigation' ));
   register_nav_menu('Footer Navigation',__( 'Footer Navigation' ));
}
add_action( 'init', 'register_my_menu' );

function gp_frontend_additionals() {
	
	if (!is_admin()) {
										
		wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', 'jquery');
		wp_enqueue_script('custom');
	}	
}

add_action('wp_enqueue_scripts', 'gp_frontend_additionals');

function get_topmost_parent($post_id){
  $parent_id = get_post($post_id)->post_parent;
  if($parent_id == 0){
    return $post_id;
  }else{
    return get_topmost_parent($parent_id);
  }
}


/** Woocommerce Hooks*/
/*---Move Product Title
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 1 );
*/
/*--- Early Registration Text*/
function changeSaleToRegistrationLoop(){
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	if ( has_term( 'race', 'product_cat' ) ) {
	global $post, $product;
		if ($product->is_in_stock( )) {
		 	if ($product->is_on_sale()) {
				 echo '<div class="registrationAlert">Early Registration Open!</div>';
 				} 
			else {
				 echo '<div class="registrationAlert">Registration Open!</div>';
			}
		}
		else {
			echo '<div class="registrationAlert">Sold Out!</div>';
		}	
    } 

else {
       global $post, $product;
		 if ($product->is_on_sale()) : 

	echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product);
 endif; 
}
}
add_action('woocommerce_before_shop_loop_item_title','changeSaleToRegistrationLoop', 9);

function changeSaleToRegistrationSingle(){
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	if ( has_term( 'race', 'product_cat' ) ) {
	global $post, $product;
		 if ($product->is_in_stock( )) {
		 	if ($product->is_on_sale()) {
				 echo '<h3>Early Registration Open!</h3>';
 				} 
			else {
				 echo '<h3>Registration Open!</h3>';
			}
		}
		else {
			echo '<h3>Sold Out!</h3>';
		}	
    } 

else {
       global $post, $product;
		 if ($product->is_on_sale()) : 

	echo apply_filters('woocommerce_sale_flash', '<h3>'.__( 'On Sale!', 'woocommerce' ).'</h3>', $post, $product);
 endif; 
}
  
}
add_action('woocommerce_single_product_summary','changeSaleToRegistrationSingle', 7 );


/*--- Left Registration Block w/ image, price, and registration status*/
function removeRaceImages () {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
}
add_action('woocommerce_before_single_product_summary','removeRaceImages', 2 );

function showEventDate () {
	global $post, $ignitewoo_events, $ignitewoo_events_pro;
	$starts = get_post_meta( $post->ID, '_ignitewoo_event_start', false );
	$date_format = empty( $main_settings['date_format'] ) ? 'l, F jS' : $main_settings['date_format'];
	foreach( $starts as $s ) {
		$printDate = '<h2>'.date( $date_format, strtotime( $s ) ).'</h2>'; 
	}
		echo $printDate;
}

function leftRegistrationBlock () {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'leftRegistrationBlockHook', 'woocommerce_show_product_images', 3 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 8 );	
		add_action( 'woocommerce_single_product_summary', 'showEventDate', 6 );	
}
add_action('leftRegistrationBlockHook','leftRegistrationBlock', 1);


/*--- Remove Category from single product page 
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
*/

/*--- Adding Form Title */
function addRegistrationFormTitle(){
	if ( has_term( 'race', 'product_cat' ) ) {
		 global $post, $product;
		 if ( has_term( 'mountain-race', 'product_cat' ) ) {
		 	$raceClass = 'mountain_race';
		 }
		 if ( has_term( 'kids-race', 'product_cat' ) ) {
		 	$raceClass = 'kid_races';
		 }
		 if ( has_term( 'road-race', 'product_cat' ) ) {
		 	$raceClass = 'road_races';
		 }
		 if ($product->is_in_stock( )) {
		echo('<hr class="'.$raceClass.'"/><h2>Registration Form</h2>
			<p>Please fill out the details and add your registration to your cart. <strong>*ALL FIELDS REQUIRED.</strong> All other information needed will be gathered from you at check out. Thank you for your participation, we look forward to seeing you at the race!</p>
			');
		}
	}
}			
add_action('woocommerce_single_product_summary','addRegistrationFormTitle', 29);

/*--- Remove Tabs */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

/*--- Add Additional FEE */
add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_surcharge' );
function woocommerce_custom_surcharge() {
  global $woocommerce;
 
	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;
 
	$percentage = 0.05;
	$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;	
	$woocommerce->cart->add_fee( 'Processing Fee:', $surcharge, true, 'standard' );
}	

/*--- Remove Woocommerce Syles ---*/
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

/*--- 12 products per page */
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

?>
