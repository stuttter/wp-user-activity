<?php

/**
 * User Activity Post Statuses
 *
 * @package User/Activity/PostStatuses
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register custom post statuses for activity items
 *
 * @since 0.1.0
 */
function wp_user_activity_register_post_statuses() {

	// Emergency
	register_post_status( 'emergency', array(
		'label'                     => _x( 'Emergency', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Emergency <span class="count">(%s)</span>', 'Emengency <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Alert
	register_post_status( 'alert', array(
		'label'                     => _x( 'Alert', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Alert <span class="count">(%s)</span>', 'Alert <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Critical
	register_post_status( 'critical', array(
		'label'                     => _x( 'Critical', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Critical <span class="count">(%s)</span>', 'Critical <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Error
	register_post_status( 'error', array(
		'label'                     => _x( 'Error', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Error <span class="count">(%s)</span>', 'Error <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Warning
	register_post_status( 'warning', array(
		'label'                     => _x( 'Warning', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Warning <span class="count">(%s)</span>', 'Warning <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Notice
	register_post_status( 'notice', array(
		'label'                     => _x( 'Notice', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Notice <span class="count">(%s)</span>', 'Notice <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Info
	register_post_status( 'info', array(
		'label'                     => _x( 'Info', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Info <span class="count">(%s)</span>', 'Info <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );

	// Debug
	register_post_status( 'debug', array(
		'label'                     => _x( 'Debug', 'activity', 'wp-user-activity' ),
		'label_count'               => _nx_noop( 'Debug <span class="count">(%s)</span>', 'Debug <span class="count">(%s)</span>', 'activity', 'wp-user-activity' ),
		'protected'                 => true,
		'exclude_from_search'       => true,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'publicly_queryable'        => false
	) );
}
