<?php
/**
 * Shared test functions.
 *
 * @package User/Activity/Tests
 */

/**
 * Test replacement for wp_kses().
 *
 * @param mixed $value Value to filter.
 * @return string
 */
function wp_kses( $value ) {
	return (string) $value;
}

/**
 * Test replacement for esc_html__().
 *
 * @param string $text Text to translate and escape.
 * @return string
 */
function esc_html__( $text ) {
	return $text;
}

/**
 * Test replacement for get_current_blog_id().
 *
 * @return int
 */
function get_current_blog_id() {
	return (int) wpuat_test_call( __FUNCTION__ );
}
