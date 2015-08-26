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

// Admin
add_filter( 'manage_activity_posts_columns',       'wp_user_activity_manage_posts_columns'             );
add_filter( 'manage_activity_posts_custom_column', 'wp_user_activity_manage_custom_column_data', 10, 2 );