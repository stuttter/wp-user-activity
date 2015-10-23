<?php

/**
 * User Activity Hooks
 *
 * @package User/Activity/Hooks
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Global
add_action( 'init', 'wp_user_activity_register_post_types'    );
add_action( 'init', 'wp_user_activity_register_post_metadata' );

// Setup the default activity types
add_action( 'init', 'wp_user_activity_register_default_types', 11 );

// Caps
add_filter( 'map_meta_cap', 'wp_user_activity_meta_caps', 10, 4 );

// Append activity action
add_filter( 'the_content', 'wp_user_activity_append_action_to_the_content', 10, 2 );

// Metaboxes
add_action( 'add_meta_boxes', 'wp_user_activity_add_metaboxes' );
add_action( 'save_post',      'wp_user_activity_metabox_save'  );

// Quick edit
add_filter( 'page_row_actions',           'wp_user_activity_disable_quick_edit_link', 10, 2 );
add_filter( 'bulk_actions-edit-activity', 'wp_user_activity_disable_bulk_action'            );

// Assets
add_action( 'admin_head', 'wp_user_activity_admin_assets' );

// Menu Humility
add_action( 'admin_menu', 'wp_user_activity_menu_humility' );

// List Table
add_filter( 'disable_months_dropdown', 'wp_user_activity_disable_months_dropdown', 10, 2 );
add_action( 'restrict_manage_posts',   'wp_user_activity_add_dropdown_filters'           );

// Columns
add_filter( 'manage_activity_posts_columns',         'wp_user_activity_manage_posts_columns'             );
add_filter( 'manage_activity_posts_custom_column',   'wp_user_activity_manage_custom_column_data', 10, 2 );
add_filter( 'manage_edit-activity_sortable_columns', 'wp_user_activity_sortable_columns' );
add_filter( 'list_table_primary_column',             'wp_user_activity_list_table_primary_column', 10, 2 );

// Admin only filter for list-table sorting
if ( is_admin() ) {
	add_filter( 'pre_get_posts', 'wp_user_activity_maybe_sort_by_fields'   );
	add_filter( 'pre_get_posts', 'wp_user_activity_maybe_filter_by_fields' );
}

// WP User Profiles
add_filter( 'wp_user_profiles_sections', 'wp_user_activity_add_profile_section' );
