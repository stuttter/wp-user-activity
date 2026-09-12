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
function get_current_user_id() { return (int) ( wpuat_test_call( __FUNCTION__ ) ?: 0 ); }
function get_post() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function get_post_meta() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function get_post_type() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function is_admin() { return (bool) wpuat_test_call( __FUNCTION__ ); }
function is_user_logged_in() { return (bool) wpuat_test_call( __FUNCTION__ ); }
function register_meta() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function register_post_type() { return wpuat_test_call( __FUNCTION__, func_get_args() ); }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $value ) ); }
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

$GLOBALS['wpuat_initial_calls'] = $GLOBALS['wpuat_test']['calls'];
