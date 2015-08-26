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
	return array(
		'cb'       => '<input type="checkbox" />',
		'username' => esc_html__( 'User',   'wp-user-activity' ),
		'action'   => esc_html__( 'Action', 'wp-user-activity' ),
		'object'   => esc_html__( 'Object', 'wp-user-activity' )
	);
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
		case 'username' :
			echo get_avatar( $post->post_author, 32 );
			echo '<strong><a href="">' . get_userdata( $post->post_author )->display_name . '</a></strong>';
			break;

		case 'action' :
			echo wp_get_user_activity_action( $post, $meta );
			break;

		case 'object' :
			echo wp_get_user_activity_object( $post, $meta );
			break;
	}
}
