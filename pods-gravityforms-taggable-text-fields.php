<?php
/**
 * Plugin Name:     Pods Gravityforms Taggable Text Fields
 * Plugin URI:      https://github.com/mgratch/pods-gravityforms-taggable-text-fields
 * Description:     Convert a text field to a taggable field with Pods, Gravity Forms, and the Pods Gravity Forms add-on.
 * Author:          Marc Gratch
 * Author URI:      https://marcgratch.com
 * Text Domain:     pods-gravityforms-taggable-text-fields
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pods_Gravityforms_Taggable_Text_Fields
 */


/**
 * Check if a newly added tag exists already, if it does find the `term_id`
 *
 * @param $tag_data
 * @param $value
 * @param $pods_data
 * @param $field
 * @param $field_items
 *
 * @return mixed
 */
function gf_pods_filter_maybe_add_tags( $tag_data, $value, $pods_data, $field, $field_items ) {

	$gform_check = strpos( implode( ',', array_keys( $_REQUEST ) ), 'gform' );
	$gform_check = false === $gform_check ? strpos( implode( ',', array_keys( $_POST ) ), 'gform' ) : true;

	if ( ! $gform_check ) {
		return $tag_data;
	}

	if ( is_int( reset( $tag_data ) ) ) {
		$term = get_term_by( 'term_id', reset( $tag_data ), $pods_data->datatype );
	} else {
		$term = get_term_by( 'name', reset( $tag_data ), $pods_data->datatype );
	}

	if ( ! $term ) {
		$term = get_term_by( 'slug', reset( $tag_data ), $pods_data->datatype );
	}

	if ( $term ) {
		$tag_data['term_id'] = $term->term_id;
	}

	return $tag_data;
}

add_filter( 'pods_api_save_pod_item_taggable_data', 'gf_pods_filter_maybe_add_tags', 10, 5 );

/**
 * Convert strings with commas into tags -- don't use commas inside a tag please
 *
 * @param $pieces
 * @param $is_new_item
 *
 * @return mixed
 */
function gf_pods_filter_maybe_split_tags( $pieces, $is_new_item ) {

	$gform_check = strpos( implode( ',', array_keys( $_REQUEST ) ), 'gform' );
	$gform_check = false === $gform_check ? strpos( implode( ',', array_keys( $_POST ) ), 'gform' ) : true;

	if ( ! $gform_check ) {
		return $pieces;
	}

	// Get all the taxonomies for this post_type
	$tss = $ts = get_object_taxonomies( $pieces['pod']['name'] );

	// Relationship fields are pick fields so lets get those then quickly reduce all the non pick fields to 1 key => value pair
	$tax_fields = array_flip( wp_list_pluck( $pieces['fields'], 'pick_val', 'name' ) );

	// Make sure to do the same check on object fields
	$obj_tax_fields = array();
	foreach ( $pieces['object_fields'] as $value ) {
		if ( is_object( $value ) ) {
			if ( isset( $value->name ) ) {
				$obj_tax_fields[ $value->name ] = $value->pickval;
			} else {
				$obj_tax_fields[] = $value->pickval;
			}
		} else {
			if ( isset( $value['name'] ) ) {
				$obj_tax_fields[ $value['name'] ] = isset( $value['pick_val'] ) ? $value['pick_val'] : '';
			} else {
				$obj_tax_fields[] = isset( $value['pick_val'] ) ? $value['pick_val'] : '';
			}
		}
	}

	$obj_tax_fields = array_flip( $obj_tax_fields );

	// Unset the non-pick fields
	unset( $tax_fields[''] );
	unset( $obj_tax_fields[''] );

	// Make sure we are only sorting taxonomies that are in use
	$ts = array_intersect( array_keys( $tax_fields ), $ts );

	// Make sure we are only sorting taxonomies that are in use
	$tss = array_intersect( array_keys( $obj_tax_fields ), $tss );

	// Make sure we are only using pick fields that ARE taxonomies
	foreach ( $tax_fields as $tax => $name ) {
		if ( ! array_key_exists( $tax, array_flip( $ts ) ) ) {
			unset( $tax_fields[ $tax ] );
		} else {
			$pieces['fields'][ $name ]['value'] = explode( ',', $pieces['fields'][ $name ]['value'] );
		}
	}

	// Make sure we are only using pick fields that ARE taxonomies
	foreach ( $obj_tax_fields as $tax => $name ) {
		if ( ! array_key_exists( $tax, array_flip( $tss ) ) ) {
			unset( $obj_tax_fields[ $tax ] );
		} else {
			$pieces['object_fields'][ $name ]['value'] = explode( ',', $pieces['object_fields'][ $name ]['value'] );
		}
	}

	return $pieces;
}

add_filter( 'pods_api_pre_save_pod_item_artistfilesdirectory', 'gf_pods_filter_maybe_split_tags', 10, 2 );