<?php

/**
 * User Activity Admin
 *
 * @package User/Activity/Admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Filter activity posts list-table columns
 *
 * @since 0.1.0
 *
 * @param   array  $columns
 * @return  array
 */
function wp_user_activity_manage_posts_columns( $columns = array() ) {

	// Override all columns
	$new_columns = array(
		'cb'       => '<input type="checkbox" />',
		'severity' => '<span class="screen-reader-text">' . esc_html__( 'Severity', 'wp-user-activity' ) . '</span><span class="dashicons dashicons-shield" title="' . esc_html__( 'Severity', 'wp-user-activity' ) . '"></span>',
		'username' => esc_html__( 'User',     'wp-user-activity' ),
		'action'   => esc_html__( 'Action',   'wp-user-activity' ),
		'object'   => esc_html__( 'Object',   'wp-user-activity' )
	);

	// Return overridden columns
	return apply_filters( 'wp_user_activity_manage_posts_columns', $new_columns, $columns );
}

/**
 * Force the primary column
 *
 * @since 0.1.0
 *
 * @return string
 */
function wp_user_activity_list_table_primary_column( $name = '', $screen_id = '' ) {

	// Only on the `edit-activity` screen
	if ( 'edit-activity' === $screen_id ) {
		$name = 'action';
	}

	// Return possibly overridden name
	return $name;
}

/**
 * Output content for each activity item
 *
 * @since 0.1.0
 *
 * @param  string  $column
 * @param  int     $post_id
 */
function wp_user_activity_manage_custom_column_data( $column = '', $post_id = 0 ) {

	// Get post & metadata
	$post  = get_post( $post_id );
	$meta  = wp_get_user_activity_meta( $post_id );

	// Custom column IDs
	switch ( $column ) {

		// Attempt to output human-readable action
		case 'severity' :
			echo wp_get_user_activity_severity( $post, $meta );
			break;

		// Attempt to output human-readable action
		case 'action' :
			echo wp_get_user_activity_action( $post, $meta );
			break;

		// User who performed this activity
		case 'username' :
			echo '<a href="">' . get_avatar( $post->post_author, 32 ) . '</a>';
			break;

		// Attempt to output helpful connection to object
		case 'object' :
			echo wp_get_user_activity_object( $post, $meta );
			break;
	}
}

/**
 * Enqueue scripts
 *
 * @since 0.1.1
 */
function wp_user_activity_admin_assets() {

	// Bail if not an event post type
	if ( 'activity' !== get_post_type() ) {
		return;
	}

	// Date picker CSS (for jQuery UI calendar)
	wp_enqueue_style( 'wp_event_calendar_datepicker', wp_user_activity_get_plugin_url() . '/assets/css/activity.css', false, wp_user_activity_get_asset_version(), false );
}
