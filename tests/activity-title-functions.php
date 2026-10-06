<?php
/**
 * Activity title test functions.
 *
 * @package User/Activity/Tests
 */

/** Test replacement for clean_post_cache(). */
function clean_post_cache() {
	return wpuat_test_call( __FUNCTION__, func_get_args() );
}

/**
 * Test replacement for sanitize_post_field().
 *
 * @param string $field   Field name.
 * @param mixed  $value   Field value.
 * @param int    $post_id Post ID.
 * @param string $context Sanitization context.
 */
function sanitize_post_field( $field, $value, $post_id, $context ) {
	$result = wpuat_test_call( __FUNCTION__, array( $field, $value, $post_id, $context ) );
	return null === $result ? $value : $result;
}

/**
 * Test replacement for sanitize_text_field().
 *
 * @param string $value Text to sanitize.
 */
function sanitize_text_field( $value ) {
	return trim( wp_strip_all_tags( $value ) );
}

/**
 * Decode HTML entities in generated titles.
 *
 * @param string $text Encoded text.
 */
function wp_specialchars_decode( $text ) {
	return html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Remove markup from generated titles.
 *
 * @param string $text Markup to remove.
 */
function wp_strip_all_tags( $text ) {
	return preg_replace( '/<[^>]*>/', '', $text );
}
