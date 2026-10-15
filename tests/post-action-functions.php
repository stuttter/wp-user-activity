<?php
/**
 * Post activity action test functions.
 *
 * @package User/Activity/Tests
 */

/**
 * Test replacement for is_post_type_hierarchical().
 *
 * @return bool
 */
function is_post_type_hierarchical() {
	return (bool) wpuat_test_call( __FUNCTION__, func_get_args() );
}
