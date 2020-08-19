<?php
/**
 * Functions to display the output for product and category select menus in field settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Prepares a list of products for select menus.
 *
 * @param string $field_id setting ID.
 * @param string $pids setting product ids.
 */
function sdwoo_product_list( $field_id, $pids ) {
	$pids = ( isset( $pids ) && '' != $pids ) ? (array) maybe_unserialize( $pids ) : array();

	$args                   = array();
	$args['fields']         = 'ids';
	$args['post_type']      = 'product';
	$args['post_status']    = 'publish';
	$args['posts_per_page'] = -1;
	$products               = new WP_QUERY( $args );
	ob_start();
	?>
	<select multiple id="sdwoo_settings[<?php echo esc_attr( $field_id ); ?>]" class="sdwoo-searchable" name="sdwoo_settings[<?php echo esc_attr( $field_id ); ?>][]">
		<?php
		foreach ( $products->posts as $id ) {
			?>

		<option value="<?php echo esc_attr( $id ); ?>" <?php selected( in_array( $id, $pids ) ); ?>><?php echo esc_html( get_the_title( $id ) ); ?></option>

			<?php
		}
		?>
	</select>
	<?php
	return ob_get_clean();
}

/**
 * Prepares a list of categories for select menus.
 *
 * @param string $field_id setting ID.
 * @param array $cids category ids.
 */
function sdwoo_category_list( $field_id, $cids ) {
	$cids = ( isset( $cids ) && '' != $cids ) ? (array) maybe_unserialize( $cids ) : array();

	$taxonomy       = 'product_cat';
	$orderby        = 'name';
	$show_count     = 0;    // 1 for yes, 0 for no.
	$pad_counts     = 0;    // 1 for yes, 0 for no.
	$hierarchical   = 1;    // 1 for yes, 0 for no.
	$title          = '';
	$empty          = 0;

	$args = array(
		'taxonomy'      => $taxonomy,
		'orderby'       => $orderby,
		'show_count'    => $show_count,
		'pad_counts'    => $pad_counts,
		'hierarchical'  => $hierarchical,
		'title_li'      => $title,
		'hide_empty'    => $empty,
	);
	$categories = get_categories( $args );

	ob_start();
	?>
	<select multiple id="sdwoo_settings[<?php echo esc_attr( $field_id ); ?>]" class="sdwoo-searchable" name="sdwoo_settings[<?php echo esc_attr( $field_id ); ?>][]">
		<?php
		foreach ( $categories as $cat ) {
			?>

		<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( in_array( $cat->term_id, $cids ) ); ?>><?php echo esc_html( $cat->name ); ?></option>

			<?php
		}
		?>
	</select>
	<?php
	return ob_get_clean();
}
