<?php

/**
 * User Activity Functions
 *
 * @package User/Activity/Functions
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Get all activity actions
 *
 * @since 2.2.2
 * @return array
 */
function wp_user_activity_get_all_actions() {
	global $wp_user_activity_actions;

	// Return if already set
	if ( ! empty( $wp_user_activity_actions ) && is_array( $wp_user_activity_actions ) ) {
		return $wp_user_activity_actions;
	}

	// Set as empty array
	$wp_user_activity_actions = array();

	// Return all actions
	return $wp_user_activity_actions;
}

/**
 * Register the default user activity types
 *
 * @since 0.1.0
 */
function wp_user_activity_register_default_types() {
	foreach ( wp_get_default_user_activity_types() as $type ) {
		wp_register_user_activity_type( $type );
	}
}

/**
 * Get the default user activity types
 *
 * Filter this array to autoload more activity types
 *
 * @since 0.1.0
 */
function wp_get_default_user_activity_types() {
	return apply_filters( 'wp_get_default_user_activity_types', array(
		'WP_User_Activity_Type_Attachment',
		'WP_User_Activity_Type_Comments',
		'WP_User_Activity_Type_Core',
		'WP_User_Activity_Type_Export',
		'WP_User_Activity_Type_Menu',
		'WP_User_Activity_Type_Plugins',
		'WP_User_Activity_Type_Posts',
		'WP_User_Activity_Type_Site_Settings',
		'WP_User_Activity_Type_Taxonomy',
		'WP_User_Activity_Type_Theme',
		'WP_User_Activity_Type_User',
		'WP_User_Activity_Type_Widgets'
	) );
}

/**
 * Register a new activity type
 *
 * This is pretty lame, but it's
 *
 * @since 0.1.0
 *
 * @param  string  $class_name
 */
function wp_register_user_activity_type( $class_name = '' ) {
	if ( class_exists( $class_name ) ) {
		new $class_name;
	}
}

/**
 * Insert a new user activity item
 *
 * @since 0.1.0
 *
 * @param array $args
 * @return int  $activity_id ID of new activity item
 */
function wp_insert_user_activity( $args = array() ) {

	// Parse arguments
	$r = wp_parse_args( $args, array(
		'user_id'        => get_current_user_id(),
		'object_type'    => '',
		'object_subtype' => '',
		'object_name'    => '',
		'object_id'      => 0,
		'action'         => '',
		'ip'             => wp_user_activity_current_user_ip(),
		'ua'             => wp_user_activity_current_user_ua(),
	) );

	// Allow JIT bypass of user activity insertion
	if ( apply_filters( 'wp_pre_insert_user_activity', false, $r, $args ) ) {
		return;
	}

	// Copy user ID
	$user_id = $r['user_id'];

	// Unset user_id from $r array so it's not saved in meta
	unset( $r['user_id'] );

	// Setup empty meta-input array
	$meta_input = array();

	// Add post meta
	foreach ( $r as $key => $value ) {
		$meta_input[ 'wp_user_activity_' . $key ] = $value;
	}

	// Remove all actions to avoid infinite loops
	wp_user_activity_remove_all_actions( 'transition_post_status' );

	// Create activity entry
	$activity_id = wp_insert_post( array(
		'post_type'   => wp_user_activity_get_post_type(),
		'post_author' => $user_id,
		'post_status' => 'publish',
		'meta_input'  => $meta_input
	) );

	// Restore all actions to avoid breaking other plugins
	wp_user_activity_restore_all_actions( 'transition_post_status' );

	return $activity_id;
}

/**
 * Get activity item metadata
 *
 * @since 0.1.1
 *
 * @param  int  $post_id
 * @return array
 */
function wp_user_activity_get_meta( $post_id = 0 ) {
	return apply_filters( 'wp_user_activity_get_meta', array(
		'object_type'    => get_post_meta( $post_id, 'wp_user_activity_object_type',    true ),
		'object_subtype' => get_post_meta( $post_id, 'wp_user_activity_object_subtype', true ),
		'object_name'    => get_post_meta( $post_id, 'wp_user_activity_object_name',    true ),
		'object_id'      => get_post_meta( $post_id, 'wp_user_activity_object_id',      true ),
		'action'         => get_post_meta( $post_id, 'wp_user_activity_action',         true ),
		'ip'             => get_post_meta( $post_id, 'wp_user_activity_ip',             true ),
		'ua'             => get_post_meta( $post_id, 'wp_user_activity_ua',             true )
	) );
}

/**
 * Get a human readable representation of the time elapsed since a given date.
 *
 * Based on function created by Dunstan Orchard - http://1976design.com
 *
 * This function will return a read representation of the time elapsed
 * since a given date.
 * eg: 2 hours and 50 minutes
 * eg: 4 days
 * eg: 4 weeks and 6 days
 *
 * Note that fractions of minutes are not represented in the return string. So
 * an interval of 3 minutes will be represented by "3 minutes", as will an
 * interval of 3 minutes 59 seconds.
 *
 * @since 0.1.2
 *
 * @param int|string $older_date The earlier time from which you're calculating
 *                               the time elapsed. Enter either as an integer Unix timestamp,
 *                               or as a date string of the format 'Y-m-d h:i:s'.
 * @param int|bool   $newer_date Optional. Unix timestamp of date to compare older
 *                               date to. Default: false (current time).
 *
 * @return string String representing the time since the older date, eg
 *         "2 hours and 50 minutes".
 */
function wp_user_activity_human_diff_time( $older_date, $newer_date = false ) {

	// Format
	if ( ! is_numeric( $older_date ) ) {
		$older_date = strtotime( $older_date );
	}

	if ( ! is_numeric( $newer_date ) ) {
		$newer_date = strtotime( $newer_date );
	}

	// Catch issues with flipped old vs. new dates
	$flipped = false;

	// array of time period chunks
	$chunks = array(
		YEAR_IN_SECONDS,
		30 * DAY_IN_SECONDS,
		WEEK_IN_SECONDS,
		DAY_IN_SECONDS,
		HOUR_IN_SECONDS,
		MINUTE_IN_SECONDS,
		1
	);

	if ( ! empty( $older_date ) && ! is_numeric( $older_date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
		$older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
	}

	/**
	 * $newer_date will equal false if we want to know the time elapsed between
	 * a date and the current time. $newer_date will have a value if we want to
	 * work out time elapsed between two known dates.
	 */
	$newer_date = empty( $newer_date )
		? current_time( 'timestamp' )
		: $newer_date;

	// Difference in seconds
	$since = $newer_date - $older_date;

	// Flipped
	if ( $since < 0 ) {
		$flipped = true;
		$since   = $older_date - $newer_date;
	}

	// Step one: the first chunk
	for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
		$seconds = $chunks[$i];

		// Finding the biggest chunk (if the chunk fits, break)
		$count = floor( $since / $seconds );
		if ( 0 != $count ) {
			break;
		}
	}

	// Set output var
	switch ( $seconds ) {
		case YEAR_IN_SECONDS :
			$output = sprintf( _n( '%s year',   '%s years',   $count, 'wp-user-activity' ), $count );
			break;
		case 30 * DAY_IN_SECONDS :
			$output = sprintf( _n( '%s month',  '%s months',  $count, 'wp-user-activity' ), $count );
			break;
		case WEEK_IN_SECONDS :
			$output = sprintf( _n( '%s week',   '%s weeks',   $count, 'wp-user-activity' ), $count );
			break;
		case DAY_IN_SECONDS :
			$output = sprintf( _n( '%s day',    '%s days',    $count, 'wp-user-activity' ), $count );
			break;
		case HOUR_IN_SECONDS :
			$output = sprintf( _n( '%s hour',   '%s hours',   $count, 'wp-user-activity' ), $count );
			break;
		case MINUTE_IN_SECONDS :
			$output = sprintf( _n( '%s minute', '%s minutes', $count, 'wp-user-activity' ), $count );
			break;
		default:
			$output = sprintf( _n( '%s second', '%s seconds', $count, 'wp-user-activity' ), $count );
	}

	// Step two: the second chunk
	// A quirk in the implementation means that this
	// condition fails in the case of minutes and seconds.
	// We've left the quirk in place, since fractions of a
	// minute are not a useful piece of information for our
	// purposes
	if ( $i + 2 < $j ) {
		$seconds2 = $chunks[$i + 1];
		$count2   = floor( ( $since - ( $seconds * $count ) ) / $seconds2 );

		// Add to output var
		if ( 0 != $count2 ) {
			$output .= _x( ',', 'Separator in time since', 'wp-user-activity' ) . ' ';

			switch ( $seconds2 ) {
				case 30 * DAY_IN_SECONDS :
					$output .= sprintf( _n( '%s month',  '%s months',  $count2, 'wp-user-activity' ), $count2 );
					break;
				case WEEK_IN_SECONDS :
					$output .= sprintf( _n( '%s week',   '%s weeks',   $count2, 'wp-user-activity' ), $count2 );
					break;
				case DAY_IN_SECONDS :
					$output .= sprintf( _n( '%s day',    '%s days',    $count2, 'wp-user-activity' ), $count2 );
					break;
				case HOUR_IN_SECONDS :
					$output .= sprintf( _n( '%s hour',   '%s hours',   $count2, 'wp-user-activity' ), $count2 );
					break;
				case MINUTE_IN_SECONDS :
					$output .= sprintf( _n( '%s minute', '%s minutes', $count2, 'wp-user-activity' ), $count2 );
					break;
				default:
					$output .= sprintf( _n( '%s second', '%s seconds', $count2, 'wp-user-activity' ), $count2 );
			}
		}
	}

	if ( true === $flipped ) {
		$output = '-' . $output;
	}

	/**
	 * Filters the human readable representation of the time elapsed since a
	 * given date.
	 *
	 * @since 0.1.2
	 *
	 * @param string $output     Final string
	 * @param string $older_date Earlier time from which we're calculating time elapsed
	 * @param string $newer_date Unix timestamp of date to compare older time to
	 */
	return apply_filters( 'wp_user_activity_human_diff_time', $output, $older_date, $newer_date );
}

/**
 * Register a callback to help with human-readable actions
 *
 * @since 0.1.0
 *
 * @param  string  $object_type
 * @param  string  $action
 * @param  string  $callback
 */
function wp_user_activity_register_action_callback( $object_type = '', $action = '', $callback = '' ) {
	$key = "wp_get_user_activity_{$object_type}_{$action}";

	add_filter( $key, $callback, 10, 2 );
}

/**
 * Get the icon for an activity-type
 *
 * @since 0.1.0
 *
 * @param   int    $post
 * @param   array  $meta
 *
 * @return  string
 */
function wp_get_user_activity_type_icon( $post = 0, $meta = array() ) {

	// Get actions
	$actions = wp_user_activity_get_all_actions();

	// Get the post
	$_post = get_post( $post );

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_user_activity_get_meta( $_post->ID );
	}

	// Get activity type
	$object_type = ( $meta ?? [] )['object_type'] ?? '';
	$type = is_array( $actions ) && ! empty( $actions[ $object_type ] )
		? $actions[ $object_type ]
		: '';

	// Get type name
	$type_name = ! empty( $type )
		? $type->get_name()
		: '';

	// Get type name
	$type_icon = ! empty( $type )
		? $type->get_icon()
		: '';

	// Format the icon
	$retval = '<i class="dashicons dashicons-' . esc_attr( $type_icon ) . '" title="' . esc_attr( $type_name ) . '"></i>';

	// Filter & return
	return apply_filters( 'wp_get_user_activity_type_icon', $retval, $post, $meta );
}

/**
 * Get the activity action
 *
 * @since 0.1.0
 *
 * @param   int    $post
 * @param   array  $meta
 *
 * @return  string
 */
function wp_get_user_activity_action( $post = 0, $meta = array() ) {

	// Get the post
	$_post = get_post( $post );

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_user_activity_get_meta( $_post->ID );
	}

	// Assemble the filter key
	$object_type = ( $meta ?? [] )['object_type'] ?? '';
	$action = ( $meta ?? [] )['action'] ?? '';
	$key = "wp_get_user_activity_{$object_type}_{$action}";

	// Filter & return
	$retval = apply_filters( $key, $_post, (object) $meta );

	// Return the action if no human readable action was found
	if ( $retval instanceof WP_Post ) {
		return ( $meta ?? [] )['action'] ?? '';
	}

	// Filter & return
	return apply_filters( 'wp_get_user_activity_action', $retval, $_post, $meta );
}

/**
 * Get the activity action
 *
 * @since 0.1.0
 *
 * @param   int    $post
 * @param   array  $meta
 *
 * @return  string
 */
function wp_get_user_activity_ip( $post = 0, $meta = array() ) {

	// Get the post
	$_post = get_post( $post );

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_user_activity_get_meta( $_post->ID );
	}

	// Get IP address
	$retval = ! empty( ( $meta ?? [] )['ip'] ?? '' )
		? ( $meta ?? [] )['ip']
		: '0.0.0.0';

	// Filter & return
	return apply_filters( 'wp_get_user_activity_ip', $retval, $_post, $meta );
}

/**
 * Get the activity action
 *
 * @since 0.1.0
 *
 * @param   int    $post
 * @param   array  $meta
 *
 * @return  string
 */
function wp_get_user_activity_ua( $post = 0, $meta = array() ) {

	// Get the post
	$_post = get_post( $post );

	// Get meta if none passed
	if ( empty( $meta ) ) {
		$meta = wp_user_activity_get_meta( $_post->ID );
	}

	// Get user agent
	$retval = ! empty( ( $meta ?? [] )['ua'] ?? '' )
		? ( $meta ?? [] )['ua']
		: '&mdash;';

	// Filter & return
	return apply_filters( 'wp_get_user_activity_ua', $retval, $_post, $meta );
}

/**
 * Get the user's IP address
 *
 * @since 0.1.0
 *
 * @return string
 */
function wp_user_activity_current_user_ip() {

	// Default value
	$retval = false;

	// Look for logged in session
	if ( is_user_logged_in() ) {
		$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
		$session = $manager->get( wp_get_session_token() );
		$retval  = $session['ip'] ?? '';
	}

	// No session IP
	if ( empty( $retval ) || ! is_user_logged_in() ) {

		// Check for remote address
		$remote_address = ! empty( $_SERVER['REMOTE_ADDR'] )
			? $_SERVER['REMOTE_ADDR']
			: '0.0.0.0';

		// Remove any unsavory bits
		$retval = preg_replace( '/[^0-9a-fA-F:., ]/', '', $remote_address );
	}

	// Filter & return
	return apply_filters( 'wp_user_activity_current_user_ip', $retval );
}

/**
 * Get the user's browser user-agent
 *
 * @since 0.1.0
 *
 * @return string
 */
function wp_user_activity_current_user_ua() {

	// Default value
	$retval = false;

	// Look for logged in session
	if ( is_user_logged_in() ) {
		$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
		$session = $manager->get( wp_get_session_token() );
		$retval  = $session['ua'] ?? '';
	}

	// No session UA
	if ( empty( $retval ) || ! is_user_logged_in() ) {
		$retval = ! empty( $_SERVER['HTTP_USER_AGENT'] )
			? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 )
			: '';
	}

	// Filter & return
	return apply_filters( 'wp_user_activity_current_user_ua', $retval );
}
