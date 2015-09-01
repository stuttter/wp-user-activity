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

	// Supports
	$supports = array(
		'editor',
		'comments',
		'post-formats'
	);

	// Capability types
	$cap_types = array(
		'activity',
		'activities'
	);

	// Post type arguments
	$args = array(
		'labels'               => $labels,
		'supports'             => $supports,
		'description'          => '',
		'public'               => true,
		'hierarchical'         => true,
		'exclude_from_search'  => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'show_in_nav_menus'    => true,
		'show_in_admin_bar'    => true,
		'menu_position'        => 3,
		'menu_icon'            => 'dashicons-backup',
		'capability_type'      => $cap_types,
		'capabilities'         => array(),
		'map_meta_cap'         => true,
		'register_meta_box_cb' => null,
		'taxonomies'           => array(),
		'has_archive'          => true,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => true,
	);

	// Register the activity type
	register_post_type( 'activity', $args );
}
