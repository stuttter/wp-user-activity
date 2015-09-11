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
add_action( 'init', 'wp_user_activity_register_post_statuses' );

add_filter( 'the_content', 'wp_user_activity_append_action_to_the_content', 10, 2 );
// Assets
add_action( 'admin_head', 'wp_user_activity_admin_assets' );

// List Table
add_filter( 'disable_months_dropdown', 'wp_user_activity_disable_months_dropdown', 10, 2 );
add_action( 'restrict_manage_posts',   'wp_user_activity_add_dropdown_filters'           );

// Columns
add_filter( 'manage_activity_posts_columns',         'wp_user_activity_manage_posts_columns'             );
add_filter( 'manage_activity_posts_custom_column',   'wp_user_activity_manage_custom_column_data', 10, 2 );
add_filter( 'manage_edit-activity_sortable_columns', 'wp_user_activity_sortable_columns' );
add_filter( 'pre_get_posts',                         'wp_user_activity_maybe_sort_by_fields' );
add_filter( 'list_table_primary_column',             'wp_user_activity_list_table_primary_column', 10, 2 );
