<?php
/**
 * Test replacements for WordPress cron functions.
 *
 * @package User_Activity
 */

/**
 * Test replacement for wp_clear_scheduled_hook().
 *
 * @return mixed
 */
function wp_clear_scheduled_hook() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for wp_next_scheduled().
 *
 * @return mixed
 */
function wp_next_scheduled() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for wp_schedule_event().
 *
 * @return mixed
 */
function wp_schedule_event() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for wp_schedule_single_event().
 *
 * @return mixed
 */
function wp_schedule_single_event() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for wp_trash_post().
 *
 * @return mixed
 */
function wp_trash_post() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for wp_date().
 *
 * @param string   $format    Date format.
 * @param int|null $timestamp Unix timestamp.
 * @param mixed    $timezone  Timezone object.
 * @return string
 */
function wp_date( $format, $timestamp = null, $timezone = null ) {
	$result = wpuat_test_call( __FUNCTION__, array( $format, $timestamp, $timezone ) );

	return null === $result
		? gmdate( $format, null === $timestamp ? time() : $timestamp )
		: $result;
}
