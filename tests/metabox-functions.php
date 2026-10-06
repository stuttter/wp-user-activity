<?php
/**
 * Activity metabox test functions.
 *
 * @package User/Activity/Tests
 */

/** Test replacement for current_user_can(). */
function current_user_can() {
	return (bool) wpuat_test_call( __FUNCTION__, func_get_args() );
}

/** Test replacement for delete_post_meta(). */
function delete_post_meta() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for esc_html_e().
 *
 * @param string $text Text to escape and print.
 */
function esc_html_e( $text ) {
	echo esc_html( $text );
}

/** Test replacement for get_users(). */
function get_users() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for selected().
 *
 * @param mixed $selected Selected value.
 * @param mixed $current  Current value.
 */
function selected( $selected, $current ) {
	if ( (string) $selected === (string) $current ) {
		echo 'selected="selected"';
	}
}

/** Test replacement for update_post_meta(). */
function update_post_meta() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/** Test replacement for wp_create_nonce(). */
function wp_create_nonce() {
	return 'metabox-nonce';
}

/** Test replacement for wp_is_post_revision(). */
function wp_is_post_revision() {
	return (bool) wpuat_test_call( __FUNCTION__, func_get_args() );
}

/** Test replacement for wp_verify_nonce(). */
function wp_verify_nonce() {
	return (bool) wpuat_test_call( __FUNCTION__, func_get_args() );
}
