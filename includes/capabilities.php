<?php

/**
 * User Activity Capabilities
 *
 * @package UserActivity/Capabilities
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Maps event capabilities
 *
 * @since 0.1.0
 *
 * @param  array   $caps     Capabilities for meta capability
 * @param  string  $cap      Capability name
 * @param  int     $user_id  User id
 * @param  array   $args     Arguments
 *
 * @return array   Actual capabilities for meta capability
 */
function wp_user_activity_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {

	// What capability is being checked?
	switch ( $cap ) {

		// Reading
		case 'read_activity' :
			$caps = array( 'read' );
			break;

		// Creating
		case 'create_activities' :
			$caps = array( 'do_not_allow' );
			break;

		// Editing
		case 'publish_activities' :
		case 'edit_activities' :
		case 'edit_others_activities' :
		case 'edit_activity' :

		// Deleting
		case 'delete_activity' :
		case 'delete_activities' :
		case 'delete_others_activities'  :
			$caps = array( 'list_users' );
			break;
	}

	return apply_filters( 'wp_user_activity_meta_caps', $caps, $cap, $user_id, $args );
}
