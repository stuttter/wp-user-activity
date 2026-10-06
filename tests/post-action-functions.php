<?php
/**
 * Post activity action test functions.
 *
 * @package User/Activity/Tests
 */

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
 * Test replacement for is_post_type_hierarchical().
 *
 * @return bool
 */
function is_post_type_hierarchical() {
	return (bool) wpuat_test_call( __FUNCTION__, func_get_args() );
}
