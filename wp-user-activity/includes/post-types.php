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
		'name'                  => _x( 'Activity', 'plural', 'wp-user-activity' ),
		'singular_name'         => _x( 'Activity', 'singular', 'wp-user-activity' ),
		'add_new'               => _x( 'Add New', 'activity singular', 'wp-user-activity' ),
		'add_new_item'          => _x( 'Add New Activity', 'singular', 'wp-user-activity' ),
		'edit_item'             => _x( 'Edit Activity', 'singular', 'wp-user-activity' ),
		'new_item'              => _x( 'New Activity', 'singular', 'wp-user-activity' ),
		'view_item'             => _x( 'View Activity', 'singular', 'wp-user-activity' ),
		'search_items'          => _x( 'Search Activity', 'plural', 'wp-user-activity' ),
		'not_found'             => _x( 'No activity found.', 'plural', 'wp-user-activity' ),
		'not_found_in_trash'    => _x( 'No activity found in Trash.', 'plural', 'wp-user-activity' ),
		'parent_item_colon'     => _x( 'Parent Activity:', 'singular', 'wp-user-activity' ),
		'all_items'             => _x( 'All Activity', 'wp-user-activity' ),
		'featured_image'        => __( 'Featured image', 'wp-user-activity' ),
		'set_featured_image'    => __( 'Set featured image', 'wp-user-activity' ),
		'remove_featured_image' => __( 'Remove featured image', 'wp-user-activity' ),
		'use_featured_image'    => __( 'Use as featured image', 'wp-user-activity' ),
	);

	// Capability types
	$cap_types = array(
		'activity',
		'activities'
	);

	// Capabilities
	$caps = array(

		// Meta caps
		'edit_post'              => 'edit_activity',
		'read_post'              => 'read_activity',
		'delete_post'            => 'delete_activity',

		// Primitive/meta caps
		'read'                   => 'read',
		'create_posts'           => 'create_activities',

		// Primitive caps (used outside of map_meta_cap)
		'edit_posts'             => 'edit_activities',
		'edit_others_posts'      => 'edit_others_activities',
		'publish_posts'          => 'publish_activities',
		'read_private_posts'     => 'read_private_activities',

		// Primitive caps (used inside of map_meta_cap)
		'delete_posts'           => 'delete_activities',
		'delete_private_posts'   => 'delete_private_activities',
		'delete_published_posts' => 'delete_published_activities',
		'delete_others_posts'    => 'delete_others_activities',
		'edit_private_posts'     => 'edit_private_activities',
		'edit_published_posts'   => 'edit_published_activities'
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

	// If `the_content` is called without setting a post context, don't trigger notices.
	if ( ! $post instanceof \WP_Post ) {
		return $content;
	}

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
