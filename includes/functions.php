<?php

function wp_insert_user_activity( $args = array() ) {

	// Parse arguments
	$r = wp_parse_args( $args, array(
		'object_type'    => '',
		'object_subtype' => '',
		'object_name'    => '',
		'object_id'      => 0,
		'action'         => '',
		'severity'       => 'info',
	) );

	// Create activity entry
	$post_id = wp_insert_post( array(
		'post_type'   => 'activity',
		'post_author' => get_current_user_id(),
		'post_status' => $r['severity']
	) );

	// Add post meta
	foreach ( $r as $key => $value ) {
		add_post_meta( $post_id, $key, $value );
	}
}

function wp_get_user_activity_meta( $post_id = 0 ) {
	return array(
		'object_type'    => get_post_meta( $post_id, 'object_type',     true ),
		'object_subtype' => get_post_meta( $post_id, 'object_subtype',  true ),
		'object_name'    => get_post_meta( $post_id, 'object_name',     true ),
		'object_id'      => get_post_meta( $post_id, 'object_id',       true ),
		'action'         => get_post_meta( $post_id, 'action',          true ),
		'severity'       => get_post_meta( $post_id, 'severity',        true )
	);
}

/**
 * Register a callback to help with human-readable actions
 *
 * @since 0.1.0
 *
 * @param  string  $object_type
 * @param  string  $action
 * @param  string  $callback
 */
function wp_user_activity_register_action_callback( $object_type = '', $action = '', $callback = '' ) {
	$key = "wp_get_user_activity_{$object_type}_{$action}";

	add_filter( $key, $callback, 10, 2 );
}

/**
 * Get the activity action
 *
 * @since 0.1.0
 *
 * @param   int    $post
 * @param   array  $meta
 *
 * @return  string
 */
function wp_get_user_activity_action( $post = 0, $meta = array() ) {

	// Assemble the filter key
	$key = "wp_get_user_activity_{$meta['object_type']}_{$meta['action']}";

	// Get the post
	$post = get_post( $post );

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_get_user_activity_meta( $post->ID );
	}

	// Filter & return
	$retval = apply_filters( $key, $post, (object) $meta );

	// Return the action if no human readable action was found
	if ( $retval instanceof WP_Post ) {
		return $meta['action'];
	}

	return $retval;
}

/**
 * Get the activity object data
 *
 * @since 0.1.0
 *
 * @param   int   $post_id
 * @param   array $meta
 *
 * @return  string
 */
function wp_get_user_activity_object( $post_id = 0, $meta = array() ) {

	// Define local values
	$retval = array();

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_get_user_activity_meta( $post_id );
	}

	// Assemble the object data
	foreach ( $meta as $key => $value ) {

		// Dash if empty
		if ( empty( $value ) ) {
			continue;
		}

		// Output the object data
		$retval[] = sprintf( '%s : %s', ucfirst( str_replace( 'object_', '', $key ) ), $value );
	}

	// Assemble
	$retval = implode( '<br>', $retval );

	// Filter & return
	return apply_filters( 'wp_get_user_activity_object', $retval, $post_id );
}
