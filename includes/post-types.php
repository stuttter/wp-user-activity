<?php

/**
 * User Activity Post Types
 *
 * @package User/Activity/PostTypes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register the User Activity post types
 *
 * @since 0.1.0
 */
function wp_user_activity_register_post_types() {
	register_post_type( 'activity', wp_user_activity_get_post_type_args() );
}

/**
 * Return the post type arguments
 *
 * @since 0.1.0
 *
 * @return array
 */
function wp_user_activity_get_post_type_args() {

	// Labels
	$labels = array(
		'name'                  => _x( 'Activity', 'post type general name', 'wp-user-activity' ),
		'singular_name'         => _x( 'Activity', 'post type singular name', 'wp-user-activity' ),
		'add_new'               => _x( 'Add New', 'activity', 'wp-user-activity' ),
		'add_new_item'          => __( 'Add New Activity', 'wp-user-activity' ),
		'edit_item'             => __( 'Edit Activity', 'wp-user-activity' ),
		'new_item'              => __( 'New Activity', 'wp-user-activity' ),
		'view_item'             => __( 'View Activity', 'wp-user-activity' ),
		'search_items'          => __( 'Search Activity', 'wp-user-activity' ),
		'not_found'             => __( 'No activity found.', 'wp-user-activity' ),
		'not_found_in_trash'    => __( 'No activity found in Trash.', 'wp-user-activity' ),
		'parent_item_colon'     => __( 'Parent:', 'wp-user-activity' ),
		'all_items'             => __( 'All Activity', 'wp-user-activity' ),
		'featured_image'        => __( 'Photo', 'wp-user-activity' ),
		'set_featured_image'    => __( 'Set featured image', 'wp-user-activity' ),
		'remove_featured_image' => __( 'Remove photo', 'wp-user-activity' ),
		'use_featured_image'    => __( 'Use as featured image', 'wp-user-activity' ),
	);

	// Capability types
	$cap_types = array(
		'activity',
		'activities'
	);

	// Capabilities
	$caps = array(
		'create_posts'        => 'create_activities',
		'edit_posts'          => 'edit_activities',
		'edit_others_posts'   => 'edit_others_activities',
		'publish_posts'       => 'publish_activities',
		'read_private_posts'  => 'read_private_activities',
		'read_hidden_posts'   => 'read_hidden_activities',
		'delete_posts'        => 'delete_activities',
		'delete_others_posts' => 'delete_others_activities'
	);

	// Filter & return
	return apply_filters( 'wp_user_activity_get_post_type_args', array(
		'labels'               => $labels,
		'supports'             => false,
		'description'          => '',
		'public'               => false,
		'hierarchical'         => true,
		'exclude_from_search'  => true,
		'publicly_queryable'   => false,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'show_in_nav_menus'    => false,
		'show_in_admin_bar'    => false,
		'menu_position'        => 3,
		'menu_icon'            => 'dashicons-backup',
		'capabilities'         => $caps,
		'capability_type'      => $cap_types,
		'register_meta_box_cb' => null,
		'taxonomies'           => array(),
		'has_archive'          => false,
		'rewrite'              => false,
		'query_var'            => false,
		'can_export'           => true,
		'delete_with_user'     => true,
	) );
}

/**
 * Filter the post content & append activity action to it
 *
 * @since 0.1.0
 *
 * @param   string  $content
 *
 * @return  string
 */
function wp_user_activity_append_action_to_the_content( $content = '' ) {

	// Get the current post
	$post = get_post();

	// Bail if not an activity post
	if ( 'activity' !== $post->post_type ) {
		return $content;
	}

	// Setup empty array
	$retval = array();

	// Maybe append action, if not empty
	$action = wp_get_user_activity_action( $post );
	if ( ! empty( $action ) ) {
		$retval[] = $action;
	}

	// Maybe append content, if not empty
	if ( ! empty( $content ) ) {
		$retval[] = $content;
	}

	// Prepend the action
	$_retval = implode( '<br>', $retval );

	// Return content with action prepended
	return $_retval;
}
