<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'admin_settings_page' );
add_action( 'admin_init', 'sdwoo_register_settings' );

function admin_settings_page(){
	add_submenu_page(
		'woocommerce',
		__( 'Subscriber Discounts', 'sdwoo' ),
		__( 'Subscriber Discounts', 'sdwoo' ),
		'manage_options',
		'subscriber-discounts-woo',
		'sdwoo_display_settings'
	);
}

function sdwoo_register_settings(){
	register_setting(
		'subscriber_discounts_settings_group',
		'sdwoo_settings',
		array(
			'sanitize_callback' => 'sdwoo_sanitize_settings',
		)
	);
}

/**
 * Sanitize settings.
 */
function sdwoo_sanitize_settings($options) {
	$keys = array(
		'mailchimp_key',
		'activecampaign_key',
		'discount_amount',
		'discount_type',
		'discount_use_one',
		'date_expires',
		'exclude_sale',
		'same_email',
		'discount_max',
		'email_subject',
		'from_email',
		'from_name',
		'name_placeholder',
		'message',
		'product_ids',
		'exclude_product_ids',
		'product_categories',
		'exclude_product_categories',
	);

	foreach( $keys as $key ) {
		if ( ! array_key_exists( $key, $options ) ) {
			$options[$key] = null;

			continue;
		}

		if ( is_array( $options[$key] ) ) {
			$options[$key] = array_map( 'sanitize_text_field', $options[$key] );
		} else if ( is_scalar( $options[$key] ) ) {
			$options[$key] = sanitize_text_field( $options[ $key ] );
		}
	}
	
	$as_arrays = array(
		'product_ids',
		'exclude_product_ids',
		'product_categories',
		'exclude_product_categories',
	);

	foreach ( $as_arrays as $key ) {
		if (! is_array( $options[$key] ) ) {
			$options[$key] = [];
		}
	}

	return $options;
}

add_action( 'admin_enqueue_scripts', 'sdwoo_searchable_menus' );
/**
 * CSS and JS for admin pages.
 */
function sdwoo_searchable_menus() {

	// Searchable Dropdown.
	wp_register_script( 'sdwoo_chosen_core_js', SDWOO_PLUGIN_URL . '/includes/js/chosen_v1.6.2/chosen.jquery.min.js', array( 'jquery' ), '1.0', false );

	wp_register_script( 'sdwoo_searchable', SDWOO_PLUGIN_URL . '/includes/js/searchable-menu.js', array( 'jquery' ), '1.0', false );

	wp_register_style( 'sdwoo_chosen_core_css', SDWOO_PLUGIN_URL . '/includes/js/chosen_v1.6.2/chosen.min.css' );
}

function sdwoo_display_settings(){
	global $sdwoo_options;
	$options = array(
		'mailchimp_key'						=> isset( $sdwoo_options['mailchimp_key'] )					? $sdwoo_options['mailchimp_key'] : '',
		'activecampaign_key'				=> isset( $sdwoo_options['activecampaign_key'] )			? $sdwoo_options['activecampaign_key'] : '',
		'discount_amount'					=> isset( $sdwoo_options['discount_amount'] )				? $sdwoo_options['discount_amount'] : '',
		'discount_type'						=> isset( $sdwoo_options['discount_type'] )					? $sdwoo_options['discount_type'] : '',
		'discount_use_one'					=> isset( $sdwoo_options['discount_use_one'] )				? $sdwoo_options['discount_use_one'] : '',
		'date_expires'						=> isset( $sdwoo_options['date_expires'] )					? $sdwoo_options['date_expires'] : '',
		'exclude_sale'						=> isset( $sdwoo_options['exclude_sale'] )					? $sdwoo_options['exclude_sale'] : '',
		'same_email'						=> isset( $sdwoo_options['same_email'] )					? $sdwoo_options['same_email'] : '',
		'discount_max'						=> isset( $sdwoo_options['discount_max'] )					? $sdwoo_options['discount_max'] : '',
		'email_subject'						=> isset( $sdwoo_options['email_subject'] )					? $sdwoo_options['email_subject'] : '',
		'from_email'						=> isset( $sdwoo_options['from_email'] )					? $sdwoo_options['from_email'] : '',
		'from_name'							=> isset( $sdwoo_options['from_name'] )						? $sdwoo_options['from_name'] : '',
		'name_placeholder'					=> isset( $sdwoo_options['name_placeholder'] )				? $sdwoo_options['name_placeholder'] : '',
		'message'							=> isset( $sdwoo_options['message'] )						? $sdwoo_options['message'] : '',
		'product_ids'						=> isset( $sdwoo_options['product_ids'] )					? $sdwoo_options['product_ids'] : '',
		'exclude_product_ids'				=> isset( $sdwoo_options['exclude_product_ids'] )			? $sdwoo_options['exclude_product_ids'] : '',
		'product_categories'				=> isset( $sdwoo_options['product_categories'] )			? $sdwoo_options['product_categories'] : '',
		'exclude_product_categories'		=> isset( $sdwoo_options['exclude_product_categories'] )	? $sdwoo_options['exclude_product_categories'] : '',
	);
	wp_enqueue_script( 'sdwoo_chosen_core_js' );
	wp_enqueue_script( 'sdwoo_searchable' );
	wp_enqueue_style( 'sdwoo_chosen_core_css' );
	?>
	<div class="wrap">
	<h2><?php _e('Subscriber Discount Settings', 'sdwoo'); ?></h2>
	<?php do_action( 'sd_woo_license' ); ?>
	<form method="post" action="options.php">

		<?php settings_fields( 'subscriber_discounts_settings_group' ); ?>
		<h3 class="title"><?php _e( 'Discount Code Settings', 'sdwoo' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'MailChimp Key', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[mailchimp_key]" name="sdwoo_settings[mailchimp_key]" type="text" class="regular-text" value="<?php echo $options['mailchimp_key']; ?>"/>
						<p class="description" for="sdwoo_settings[mailchimp_key]"><?php _e( 'Enter a random string of letters and numbers to be used as a means of verifying data is coming from MailChimp. Leave blank if not using MailChimp.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Active Campaign Key', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[activecampaign_key]" name="sdwoo_settings[activecampaign_key]" type="text" class="regular-text" value="<?php echo $options['activecampaign_key']; ?>"/>
						<p class="description" for="sdwoo_settings[activecampaign_key]"><?php _e( 'Enter a random string of letters and numbers to be used as a means of verifying data is coming from Active Campaign. Leave blank if not using Active Campaign.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Discount Amount', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[discount_amount]" name="sdwoo_settings[discount_amount]" type="text" class="regular-text" value="<?php echo $options['discount_amount']; ?>"/>
						<p class="description" for="sdwoo_settings[discount_amount]"><?php _e( 'Amount of the discount (i.e. 20 for 20% or $20)', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Discount Type', 'sdwoo' ); ?>
					</th>
					<td>
						<select id="sdwoo_settings[discount_type]" name="sdwoo_settings[discount_type]">
							<!-- fixed_cart, percent, fixed_product, percent_product -->
							<option value='fixed_cart' <?php selected( 'fixed_cart', $options[ 'discount_type' ]); ?> ><?php _e( 'Fixed', 'sdwoo' ); ?></option>
							<option value='percent' <?php selected( 'percent', $options[ 'discount_type' ]); ?> ><?php _e( 'Percent', 'sdwoo' ); ?></option>
						</select>
						<p class="description" for="sdwoo_settings[discount_type]"><?php _e( 'Is the discount amount a fixed amount or percentage?', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Use Only One Coupon?', 'sdwoo' ); ?>
					</th>
					<td>
						<input type="checkbox" id="sdwoo_settings[discount_use_one]" name="sdwoo_settings[discount_use_one]" value="yes" <?php checked( 'yes', $options[ 'discount_use_one' ] ); ?> />
						<p class="description" for="sdwoo_settings[discount_use_one]"><?php _e( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Expire Discount After', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[date_expires]" name="sdwoo_settings[date_expires]" type="text" class="regular-text" value="<?php echo $options['date_expires']; ?>"/>
						<p class="description" for="sdwoo_settings[date_expires]"><?php _e( 'Enter the number of days to expire the discount code after creation.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Exclude Sale Items?', 'sdwoo' ); ?>
					</th>
					<td>
						<input type="checkbox" id="sdwoo_settings[exclude_sale]" name="sdwoo_settings[exclude_sale]" value="yes" <?php checked( 'yes', $options[ 'exclude_sale' ] ); ?> />
						<p class="description" for="sdwoo_settings[exclude_sale]"><?php _e( 'Check this box if the coupon cannot be used with items that are on sale.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Require Same Email?', 'sdwoo' ); ?>
					</th>
					<td>
						<input type="checkbox" id="sdwoo_settings[same_email]" name="sdwoo_settings[same_email]" value="yes" <?php checked( 'yes', $options[ 'same_email' ] ); ?> />
						<p class="description" for="sdwoo_settings[same_email]"><?php _e( 'Check to require the customer use the same email address as they subscribed to your newsletter with.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Max Uses', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[discount_max]" name="sdwoo_settings[discount_max]" type="text" class="regular-text" value="<?php echo $options['discount_max']; ?>"/>
						<p class="description" for="sdwoo_settings[discount_max]"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Products', 'sdwoo' ); ?>
					</th>
					<td>
						<?php echo sdwoo_product_list( 'product_ids', $options['product_ids'] ); ?>
						<p class="description" for="sdwoo_settings[product_ids]"><?php _e( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Excluded Products', 'sdwoo' ); ?>
					</th>
					<td>
						<?php echo sdwoo_product_list( 'exclude_product_ids', $options['exclude_product_ids'] ); ?>
						<p class="description" for="sdwoo_settings[exclude_product_ids]"><?php _e( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Product Categories', 'sdwoo' ); ?>
					</th>
					<td>
						<?php echo sdwoo_category_list( 'product_categories', $options['product_categories'] ); ?>
						<p class="description" for="sdwoo_settings[product_categories]"><?php _e( 'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Excluded Categories', 'sdwoo' ); ?>
					</th>
					<td>
						<?php echo sdwoo_category_list( 'exclude_product_categories', $options['exclude_product_categories'] ); ?>
						<p class="description" for="sdwoo_settings[exclude_product_categories]"><?php _e( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'sdwoo' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<h3 class="title"><?php _e( 'Email Settings', 'sdwoo' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Email Subject', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[email_subject]" name="sdwoo_settings[email_subject]" type="text" class="regular-text" value="<?php echo $options['email_subject']; ?>"/>
						<p class="description" for="sdwoo_settings[email_subject]"><?php _e( 'What will appear in the subject line of the email sent to your subscribers. (i.e. Thanks for subscribing here is your discount.)', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'From Email Address', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[from_email]" name="sdwoo_settings[from_email]" type="text" class="regular-text" value="<?php echo $options['from_email']; ?>"/>
						<p class="description" for="sdwoo_settings[from_email]"><?php _e( 'The email address that this message should come from. (i.e. support@yourdomain.com)', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'From Display Name', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[from_name]" name="sdwoo_settings[from_name]" type="text" class="regular-text" value="<?php echo $options['from_name']; ?>"/>
						<p class="description" for="sdwoo_settings[from_name]"><?php _e( 'The name that this message should come from. (i.e. Your Store Name)', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Customer Placeholder Name', 'sdwoo' ); ?>
					</th>
					<td>
						<input id="sdwoo_settings[name_placeholder]" name="sdwoo_settings[name_placeholder]" type="text" class="regular-text" value="<?php echo $options['name_placeholder']; ?>"/>
						<p class="description" for="sdwoo_settings[name_placeholder]"><?php _e( 'To be used in the {firstname} placeholder if no name is provided by the customer. (i.e. There! or You!)', 'sdwoo' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'Message', 'sdwoo' ); ?>
					</th>
					<td>
						<textarea rows="6" cols="75" id="sdwoo_settings[message]" name="sdwoo_settings[message]"><?php echo $options['message']; ?></textarea>
						<p class="description" for="sdwoo_settings[message]"><?php _e( 'The message to be sent to your customer with the discount code. (i.e. Hey {firstname}! Thanks for signing up for our newsletter. Here is your discount code: {code}).<br />You can use the following placeholder tags to be filled in when the message is sent: <br /><code>{firstname}</code> - enters the customer\'s first name or the value entered as the customer placeholder name above.<br /><code>{code}</code> - use where you want the discount code to be displayed in your message.<br />Basic HTML markup can be used to customize the layout:<br /><code>&lt;br /&gt;</code>: Inserts a line break<br /><code>&lt;strong&gt;Your text&lt;/strong&gt;</code>: Makes "Your text" bold<br /><code>&lt;em&gt;Your text&lt;/em&gt;</code>: Makes "Your text" italicised', 'sdwoo' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'sdwoo' ); ?>" />
		</p>
	</form>
	<?php
}
