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
	register_meta( 'post', 'wp_user_activity_object_type',    'sanitize_key'   );
	register_meta( 'post', 'wp_user_activity_object_subtype', '__return_value' );
	register_meta( 'post', 'wp_user_activity_object_name',    '__return_value' );
	register_meta( 'post', 'wp_user_activity_object_id',      'absint'         );
	register_meta( 'post', 'wp_user_activity_action',         'sanitize_key'   );
}

/**
 * Return the value being passed into it.
 *
 * This is a very dumb hack to allow us to register meta keys when the value
 * does not require additional input sanitization.
 *
 * @since 0.1.0
 *
 * @param   string  $value
 * @return  string
 */
function __return_value( $value = '' ) {
	return $value;
}
