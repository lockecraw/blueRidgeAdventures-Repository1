<?php
/*
Plugin Name: WooCommerce PayPal Payments Advanced Gateway
Plugin URI: http://www.woothemes.com/products/paypal-advanced/
Description: A payment gateway for PayPal Payments Advanced (https://www.paypal.com/webapps/mpp/paypal-payments-advanced). A PayPal Advanced account is required for this gateway to function. Paypal Adavnced currently only supports USD.
Version: 1.7.8
Author: Kiran P
Author URI: http://www.limecuda.com
Text Domain: wc_paypaladv

Paypal Payments Advanced Docs: https://cms.paypal.com
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '0898a0a035d9e9c0bddf4b31a3906ae9', '18742' );

add_action( 'plugins_loaded', 'woocommerce_paypaladv_init' ) ;


add_action( 'admin_enqueue_scripts', 'wc_paypaladv__enqueue_color_picker' );
function wc_paypaladv__enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wc_paypaladv-script-handle', plugins_url('js/misc.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

function woocommerce_paypaladv_init() {
	global $woocommerce;

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

	 //Localisation
	load_plugin_textdomain( 'wc_paypaladv', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// include required files based on admin or site
	require_once( plugin_dir_path( __FILE__ ) . "class-wc-paypal-advanced.php" ); //core class

}
