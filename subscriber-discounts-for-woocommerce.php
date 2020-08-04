<?php
/**
 * Plugin Name: Subscriber Discounts for WooCommerce
 * Plugin URI: https://amplifyplugins.com
 * Description: Automatically email a discount code to new subscribers.
 * Tags: WooCommerce, MailChimp, ActiveCampaign, Discounts
 * Version: 1.3.2
 * WC tested up to: 4.3.1
 * Author: Scott DeLuzio
 * Author URI: https://scottdeluzio.com
 * Text Domain: sdwoo
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action('plugins_loaded', 'subscriber_discounts_woo_plugin_init');
function subscriber_discounts_woo_plugin_init() {
	load_plugin_textdomain( 'sdwoo', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
/* Check if WooCommerce is Installed
--------------------------------------------- */
function sd_woo_require() {
	$files = array(
		'sdwoo',
	); //array for future use

	foreach( $files as $file ) {
		require plugin_dir_path( __FILE__ ) . 'includes/' . $file . '.php';
	}
  $sdwoocheck = new Subscriber_Discounts_Woo();
  $sdwoocheck->sdwoo_run();

}
add_action( 'admin_init', 'sd_woo_require' );

global $sdwoo_options;
$sdwoo_options = get_option( 'sdwoo_settings' );
/*
 * Includes for our Plugin
 */
 if ( ! defined( 'SDWOO_PLUGIN' ) ) {
   define( 'SDWOO_PLUGIN', __FILE__ );
 }
 if( ! defined( 'SDWOO_PLUGIN_DIR' ) ) {
  	define( 'SDWOO_PLUGIN_DIR', dirname( __FILE__ ) );
 }
 if( ! defined( 'SDWOO_PLUGIN_URL' ) ) {
	define( 'SDWOO_PLUGIN_URL', plugins_url( '', __FILE__ ) );
}
/* Include Files */
function sdwoo_file_includes(){
	$files = array(
		'create-discount',
		'options-page',
		'mailchimp-discount',
		'activecampaign-discount'
	); //array for future use

	foreach( $files as $file ) {
		include( SDWOO_PLUGIN_DIR . '/includes/' . $file . '.php' );
	}
	global $sdwoo_options;
	if ( isset( $sdwoo_options[ 'mailchimp_key' ] ) && $sdwoo_options[ 'mailchimp_key' ] != '' ){
		$create = new SDWOO_Mailchimp_Create_Discount();
		$create->create_discount();
	}
	if ( isset( $sdwoo_options[ 'activecampaign_key' ] ) && $sdwoo_options[ 'activecampaign_key' ] != '' ){
		$create = new SDWOO_Activecampaign_Create_Discount();
		$create->create_discount();
	}
}
add_action( 'init', 'sdwoo_file_includes' );