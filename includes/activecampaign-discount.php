<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class SDWOO_Activecampaign_Create_Discount extends SDWOO_Create_Discount{

	/**
	 * Our discount type. Used for type specific filters/actions
	 * @var string
	 * @since 1.1.0
	 */
	public $discount_type = 'activecampaign';

	/**
	 * The key used in the webhook.
	 * @return string
	 * @since 1.1.0
	 */
	public function get_key(){
		$key = esc_attr( $this->sdwoo_options[ 'activecampaign_key' ] );
		return $key;
	}

	/**
	 * Get email address from the webhook
	 *
	 * @access public
	 * @since 1.1.0
	 * @return string
	 */
	public function get_email() {
		$email = '';
		if( isset( $_POST['contact']['email'] ) ){
			$email = wp_strip_all_tags( $_POST['contact']['email'] );
		}
		if ( ! is_email( $email ) ){
			$email = '';
		}
		return $email;
	}

	/**
	 * Get contact name from the webhook
	 *
	 * @access public
	 * @since 1.1.0
	 * @return string
	 */
	public function get_name() {
		$name = '';
		if( isset( $_POST['contact']['first_name'] ) ){
			$name = $_POST['contact']['first_name'];
		}
		if ( empty( $name ) ){
			$name = $this->sdwoo_options['name_placeholder'];
		}
		return $name;
	}
}