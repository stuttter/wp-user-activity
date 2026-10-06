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
