<?php

declare(strict_types=1);

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

$GLOBALS['wpuat_test'] = array();

function wpuat_test_call( $name, $arguments = array() ) {
	$GLOBALS['wpuat_test']['calls'][ $name ][] = $arguments;

	if ( isset( $GLOBALS['wpuat_test']['callbacks'][ $name ] ) ) {
		return call_user_func_array( $GLOBALS['wpuat_test']['callbacks'][ $name ], $arguments );
	}

	return isset( $GLOBALS['wpuat_test']['returns'][ $name ] )
		? $GLOBALS['wpuat_test']['returns'][ $name ]
		: null;
}

function __( $text ) { return $text; }
function _x( $text ) { return $text; }
function _n( $single, $plural, $number ) { return 1 === (int) $number ? $single : $plural; }
function absint( $value ) { return abs( (int) $value ); }
function add_action() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function add_filter() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function apply_filters( $hook, $value ) {
	$arguments = func_get_args();
	$result = wpuat_test_call( __FUNCTION__ . ':' . $hook, $arguments );
	return null === $result ? $value : $result;
}
/**
 * Test replacement for date_i18n().
 *
 * @param string $format    Date format.
 * @param int    $timestamp Unix timestamp.
 */
function date_i18n( $format, $timestamp ) {
	return gmdate( $format, $timestamp );
}
/**
 * Test replacement for esc_attr().
 *
 * @param mixed $value Value to escape.
 */
function esc_attr( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}
/**
 * Test replacement for esc_html().
 *
 * @param mixed $value Value to escape.
 */
function esc_html( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}
function get_current_user_id() { return (int) ( wpuat_test_call( __FUNCTION__ ) ?: 0 ); }
/**
 * Test replacement for get_option().
 *
 * @param string $name Option name.
 */
function get_option( $name ) {
	return 'date_format' === $name ? 'Y-m-d' : 'H:i';
}
function get_post() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function get_post_meta() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function get_post_type() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function is_admin() { return (bool) wpuat_test_call( __FUNCTION__ ); }
function is_user_logged_in() { return (bool) wpuat_test_call( __FUNCTION__ ); }
function register_meta() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function register_post_type() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $value ) ); }
/**
 * Test replacement for wp_kses_post().
 *
 * @param mixed $value Value to filter.
 */
function wp_kses_post( $value ) {
	return strip_tags( (string) $value, '<a><abbr><br><em><i><span><strong><time>' );
}
function wp_insert_post() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( $defaults, (array) $args ); }

class WP_Post {
	public $ID = 0;
	public $post_type = '';
}

require_once dirname( __DIR__ ) . '/wp-user-activity/includes/capabilities.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/metadata.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/post-types.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/common.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/hooks.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/admin.php';

$GLOBALS['wpuat_initial_calls'] = $GLOBALS['wpuat_test']['calls'];
