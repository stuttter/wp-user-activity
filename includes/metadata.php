<?php

/**
 * User Activity Metadata
 *
 * @package User/Activity/Metadata
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register activity metadata keys & sanitization callbacks
 *
 * @since 0.1.0
 */
function wp_user_activity_register_post_metadata() {
	register_meta( 'post', 'wp_user_activity_object_type',    'wp_user_activity_sanitize_object_type'    );
	register_meta( 'post', 'wp_user_activity_object_subtype', 'wp_user_activity_sanitize_object_subtype' );
	register_meta( 'post', 'wp_user_activity_object_name',    'wp_user_activity_sanitize_object_name'    );
	register_meta( 'post', 'wp_user_activity_object_id',      'wp_user_activity_sanitize_object_id'      );
	register_meta( 'post', 'wp_user_activity_action',         'wp_user_activity_sanitize_object_action'  );
}

/**
 * Return the object type
 *
 * @since 0.1.2
 *
 * @param   string  $value
 * @return  string
 */
function wp_user_activity_sanitize_object_type( $value = '' ) {
	return sanitize_key( $value );
}

/**
 * Return the object subtype
 *
 * @since 0.1.2
 *
 * @param   string  $value
 * @return  string
 */
function wp_user_activity_sanitize_object_subtype( $value = '' ) {
	return $value;
}

/**
 * Return the object name
 *
 * @since 0.1.2
 *
 * @param   string  $value
 * @return  string
 */
function wp_user_activity_sanitize_object_name( $value = '' ) {
	return $value;
}

/**
 * Return the object name
 *
 * @since 0.1.2
 *
 * @param   string  $value
 * @return  string
 */
function wp_user_activity_sanitize_object_id( $value = '' ) {
	return absint( $value );
}

/**
 * Return the object action
 *
 * @since 0.1.2
 *
 * @param   string  $value
 * @return  string
 */
function wp_user_activity_sanitize_object_action( $value = '' ) {
	return sanitize_key( $value );
}

