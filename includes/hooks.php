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

// Assets
add_action( 'admin_head', 'wp_user_activity_admin_assets' );

// Admin
add_filter( 'manage_activity_posts_columns',       'wp_user_activity_manage_posts_columns'             );
add_filter( 'manage_activity_posts_custom_column', 'wp_user_activity_manage_custom_column_data', 10, 2 );
add_filter( 'list_table_primary_column',           'wp_user_activity_list_table_primary_column', 10, 2 );
