<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Code adapted with permission from code provided by Ashley Gibson (Nose Graze)
 * https://www.nosegraze.com
 *
 */
class SDWOO_Create_Discount {
	public function __construct(){
		$this->sdwoo_options = get_option( 'sdwoo_settings' );
	}
	/**
	 * Our discount type. Used for type specific filters/actions
	 * @var string
	 * @since 1.1.0
	 */
	public $discount_type = 'default';


	/**
	 * The key used in the webhook.
	 * @return string
	 * @since 1.1.0
	 */
	public function get_key(){
		$key = false;
		return $key;
	}

	/**
	 * The name we'll give the discount when it is created
	 * @since 1.1.0
	 * @return string
	 */
	public function get_discount_name(){
		$discount_name = $this->sdwoo_options[ 'discount_name' ] . ' ' . $this->get_email();
		return $discount_name;
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
		if( isset( $_POST['email'] ) ){
			$email = $_POST['email'];
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
		if( isset( $_POST['data']['merges'] ) ){
			$name = $_POST['data']['merges']['FNAME'];
		}
		if ( empty( $name ) ){
			$name = $this->sdwoo_options['name_placeholder'];
		}
		return $name;
	}

	/**
	 * Can we create a discount?
	 *
	 * @access public
	 * @since 1.1.0
	 * @return bool Whether the discount should be created
	 */
	public function can_discount() {
		$discount = true;
		if ( ! isset( $_GET['trigger-special-discount'] ) || ! isset( $_GET['discount-key'] ) || $_GET['discount-key'] != $this->get_key() || ! function_exists( 'woo_store_discount' ) ) {
			$discount = false;
		}
		if ( ! $this->get_key() ){
			$discount = false;
		}
		if ( $this->get_email() == '' ){
			$discount = false;
		}
		// Only create a discount if the type is another type
		if ( $this->discount_type == 'default' ){
			$discount = false;
		}
		return (bool) $discount;
	}

	/**
	 * Create the discount code
	 *
	 * @access public
	 * @since 1.1.0
	 * @return void
	 */
	public function create_discount(){
		if ( !$this->can_discount() ){
			return;
		}
		$email_address	= $this->get_email(); // Create a variable so the function doesn't have to be called several times.
		$timestamp		= time();
		$numbers_array	= str_split( $timestamp . rand( 10, 99 ) );
		$letters_array	= array_combine( range( 1, 26 ), range( 'a', 'z' ) );
		$final_code		= '';

		foreach ( $numbers_array as $key => $value ) {
			$final_code .= $letters_array[ $value ];
		}

		$final_code		= apply_filters( 'sdwoo_discount_code', $final_code, $email_address );

		$discount_args	= array(
			'code'					=> $final_code,
			'email'					=> 'yes' == $this->sdwoo_options[ 'same_email' ] ? $email_address : '',
			'max'					=> $this->sdwoo_options[ 'discount_max' ],
			'amount'				=> $this->sdwoo_options[ 'discount_amount' ],
			'type'					=> $this->sdwoo_options[ 'discount_type' ],
			'use_one'				=> 'yes' == $this->sdwoo_options[ 'discount_use_one' ] ? 'yes' : 'no',
			'exclude_sale_items'	=> 'yes' == $this->sdwoo_options[ 'exclude_sale' ] ? 'yes' : '',
		);

		//Create the discount
		woo_store_discount( $discount_args );

		//Send the discount to the subscriber
		$first_name = $this->get_name();

		$vars = array(
			'{firstname}'	=> esc_html( $first_name ),
			'{code}'		=> esc_html( $final_code ),
		);

		$message = strtr( $this->sdwoo_options[ 'message' ], $vars );

		$subject = $this->sdwoo_options[ 'email_subject' ];

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $this->sdwoo_options[ 'from_name' ] . ' <' . $this->sdwoo_options[ 'from_email' ] . '>'
		);
		wp_mail( $email_address, $subject, $message, $headers );
	}
}

function woo_store_discount( $discount_args ){
	$coupon_code		= $discount_args[ 'code' ];					// Code
	$amount				= $discount_args[ 'amount' ];				// Coupon Amount
	$discount_type		= $discount_args[ 'type' ];					// Type: fixed_cart, percent, fixed_product, percent_product
	$individual_use		= $discount_args[ 'use_one' ];				// Can coupon be used with other coupons?
	$usage_limit		= $discount_args[ 'max' ];
	$exclude_sale_items	= $discount_args[ 'exclude_sale_items' ];	// Should the coupon be applied to items that are on sale?
	$email 				= $discount_args[ 'email' ];

	$coupon = array(
		'post_title'	=> $coupon_code,
		'post_content'	=> '',
		'post_status'	=> 'publish',
		'post_author'	=> 1,
		'post_type'		=> 'shop_coupon'
	);

	$new_coupon_id = wp_insert_post( $coupon );

	// Add meta
	update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
	update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
	update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
	update_post_meta( $new_coupon_id, 'product_ids', '' );
	update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
	update_post_meta( $new_coupon_id, 'customer_email', $email );
	update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
	update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );
	update_post_meta( $new_coupon_id, 'expiry_date', '' );
	update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
	update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
}