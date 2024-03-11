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
add_action( 'save_post',      'wp_user_activity_metabox_save'  );
add_action( 'add_meta_boxes', 'wp_user_activity_add_metaboxes' );

// User Profiles
add_filter( 'wp_user_profiles_sections',       'wp_user_activity_add_profile_section' );
add_action( 'wp_user_profiles_add_meta_boxes', 'wp_user_activity_add_user_profiles_metabox', 10, 2 );

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

// Untrash
add_filter( 'wp_untrash_post_status', 'wp_user_activity_untrash_to_previous_status', 10, 3 );

// Cron
add_action( 'init',                                       'wp_user_activity_schedule_cron' );
add_action( 'wp_user_activity_trash_old_activities',      'wp_user_activity_trash_old_activities' );
add_action( 'wp_user_activity_trash_old_activities_loop', 'wp_user_activity_trash_old_activities' );

// Admin only filter for list-table sorting
if ( is_admin() ) {
	add_filter( 'pre_get_posts', 'wp_user_activity_maybe_sort_by_fields'   );
	add_filter( 'pre_get_posts', 'wp_user_activity_maybe_filter_by_fields' );
}

/** Helpers *******************************************************************/

/**
 * Removes all actions from a WordPress actions, and stashes them in a global
 * in the event they need to be restored later.
 *
 * @since 1.1.0
 *
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function wp_user_activity_remove_all_actions( $tag, $priority = false ) {
	global $wp_filter, $merged_filters, $wp_user_activity_actions;

	// Reset the global
	$wp_user_activity_actions = new stdClass();

	// Filters exist
	if ( isset( $wp_filter[ $tag ] ) ) {

		// Filters exist in this priority
		if ( ! empty( $priority ) && isset( $wp_filter[ $tag ][ $priority ] ) ) {

			// Store filters in a backup
			$wp_user_activity_actions->wp_filter[ $tag ][ $priority ] = $wp_filter[ $tag ][ $priority ];

			// Unset the filters
			unset( $wp_filter[ $tag ][ $priority ] );

		// Priority is empty
		} else {

			// Store filters in a backup
			$wp_user_activity_actions->wp_filter[ $tag ] = $wp_filter[ $tag ];

			// Unset the filters
			unset( $wp_filter[ $tag ] );
		}
	}

	// Check merged filters
	if ( isset( $merged_filters[ $tag ] ) ) {

		// Store filters in a backup
		$wp_user_activity_actions->merged_filters[ $tag ] = $merged_filters[ $tag ];

		// Unset the filters
		unset( $merged_filters[ $tag ] );
	}

	return true;
}

/**
 * Restores filters from the $bbp global that were removed using
 * wp_user_activity_remove_all_actions()
 *
 * @since 1.1.0
 *
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function wp_user_activity_restore_all_actions( $tag, $priority = false ) {
	global $wp_filter, $merged_filters, $wp_user_activity_actions;

	// Filters exist
	if ( isset( $wp_user_activity_actions->wp_filter[ $tag ] ) ) {

		// Filters exist in this priority
		if ( ! empty( $priority ) && isset( $wp_user_activity_actions->wp_filter[ $tag ][ $priority  ] ) ) {

			// Store filters in a backup
			$wp_filter[ $tag ][ $priority ] = $wp_user_activity_actions->wp_filter[ $tag ][ $priority ];

			// Unset the filters
			unset( $wp_user_activity_actions->wp_filter[ $tag ][ $priority ] );

		// Priority is empty
		} else {

			// Store filters in a backup
			$wp_filter[ $tag ] = $wp_user_activity_actions->wp_filter[ $tag ];

			// Unset the filters
			unset( $wp_user_activity_actions->wp_filter[ $tag ] );
		}
	}

	// Check merged filters
	if ( isset( $wp_user_activity_actions->merged_filters[ $tag ] ) ) {

		// Store filters in a backup
		$merged_filters[ $tag ] = $wp_user_activity_actions->merged_filters[ $tag ];

		// Unset the filters
		unset( $wp_user_activity_actions->merged_filters[ $tag ] );
	}

	return true;
}
