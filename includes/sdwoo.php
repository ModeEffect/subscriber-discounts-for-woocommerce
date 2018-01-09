<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Subscriber_Discounts_Woo {

	public function sdwoo_run() {
		if( !class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_init', array( $this, 'sdwoo_deactivate' ), 5 );
			add_action( 'admin_notices', array( $this, 'sdwoo_error_message' ) );
			return;
		}
	}
	/**
	 * deactivates the plugin if WooCommerce isn't running
	 *
	 * @since  1.0.0
	 *
	 */
	public function sdwoo_deactivate() {
		deactivate_plugins( plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/subscriber-discounts-for-woocommerce.php' );
	}

	/**
	 * error message if we're not using  WooCommerce
	 *
	 * @since  1.0.0
	 *
	 */
	public function sdwoo_error_message() {
		$url = 'https://wordpress.org/plugins/woocommerce/';
		$error = sprintf( wp_kses( __( 'Sorry, Subscriber Discounts requires <a href="%s">WooCommerce</a>. It has been deactivated.', 'sdwoo' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );

		echo '<div id="message" class="error"><p>' . $error . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}
