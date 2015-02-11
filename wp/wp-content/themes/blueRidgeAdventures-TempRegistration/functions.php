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
function new_excerpt_more($excerpt) {
	return str_replace('[...]', '...', $excerpt);
}
add_filter('wp_trim_excerpt', 'new_excerpt_more');

if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' ); 
}

function register_my_menu() {
  register_nav_menu('TempRegistration',__( 'Header Menu' ));
}
add_action( 'init', 'register_my_menu' );

function gp_frontend_additionals() {
	
	if (!is_admin()) {
										
		wp_register_script('custom', get_template_directory_uri() . '/js/custom.js', 'jquery');
		wp_enqueue_script('custom');
	}	
}

add_action('wp_enqueue_scripts', 'gp_frontend_additionals');

/** Woocommerce Hooks*/
/*---Move Product Title*/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 1 );

/*--- Early Registration Text*/
function changeSaleToRegistrationLoop(){
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	if ( has_term( 'race', 'product_cat' ) ) {
	global $post, $product;
		 if ($product->is_on_sale()) {
			 echo '<div class="registrationAlert">Early Registration Open!</div>';
 			} 
		else {
			 echo '<div class="registrationAlert">Registration Open!</div>';
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
		 if ($product->is_on_sale()) {
			 echo '<div class="registrationAlertSingleProduct">Early Registration Open!</div>';
 			} 
		else {
			 echo '<div class="registrationAlertSingleProduct">Registration Open!</div>';
		}
    } 

else {
       global $post, $product;
		 if ($product->is_on_sale()) : 

	echo apply_filters('woocommerce_sale_flash', '<div class="registrationAlertSingleProduct">'.__( 'Sale!', 'woocommerce' ).'</div>', $post, $product);
 endif; 
}
  
}
add_action('leftRegistrationBlockHook','changeSaleToRegistrationSingle', 10 );


/*--- Left Registration Block w/ image, price, and registration status*/
function removeRaceImages () {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
}
add_action('woocommerce_before_single_product_summary','removeRaceImages', 2 );

function leftRegistrationBlock () {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		add_action( 'leftRegistrationBlockHook', 'woocommerce_show_product_images', 3 );
		add_action( 'leftRegistrationBlockHook', 'woocommerce_template_single_price', 5 );	
}
add_action('leftRegistrationBlockHook','leftRegistrationBlock', 1);


/*--- Remove Category from single product page */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );


/*--- Adding Form Title */
function addRegistrationFormTitle(){
	if ( has_term( 'race', 'product_cat' ) ) {
		echo('<div class="registrationHeader"><h1>Registration Form</h1>
			<p><strong>Please fill out the details and add your registration to your cart. - *ALL FIELDS REQUIRED. <br/>All other information needed will be gathered from you at check out. Thank you for your participation, we look forward to seeing you at the race!</strong></p></div>
			<style>
				.variations_form {
					border: 1px solid #cccccc;
					padding: 20px;
					background: #eee6d6;
					margin: 0 0 20px 0;
				}
			</style>
			');
			
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
?>
