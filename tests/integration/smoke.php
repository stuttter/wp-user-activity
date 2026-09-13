<?php
/**
 * Exercise activity storage and multisite isolation in a real WordPress network.
 *
 * This file is loaded by the centrally maintained integration runner after the
 * production plugin build has been network activated.
 *
 * @package WP_User_ActivityTests
 */

defined( 'ABSPATH' ) || exit;

$assert = static function ( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- WP-CLI displays this fixed test diagnostic.
		throw new RuntimeException( $message );
	}
};

$find_activity = static function ( $object_name ) {
	return array_map(
		'intval',
		get_posts(
			array(
				'fields'         => 'ids',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Exact test-record lookup.
				'meta_key'       => 'wp_user_activity_object_name',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Exact test-record lookup.
				'meta_value'     => $object_name,
				'no_found_rows'  => true,
				'post_status'    => 'publish',
				'post_type'      => 'activity',
				'posts_per_page' => -1,
			)
		)
	);
};

$insert_activity = static function ( $object_name, $user_id, $object_id ) use ( $assert ) {
	$activity_id = wp_insert_user_activity(
		array(
			'action'         => 'update',
			'ip'             => '192.0.2.10',
			'object_id'      => $object_id,
			'object_name'    => $object_name,
			'object_subtype' => 'post',
			'object_type'    => 'post',
			'ua'             => 'Portfolio integration smoke',
			'user_id'        => $user_id,
		)
	);

	$assert( is_int( $activity_id ) && 0 < $activity_id, 'The public insertion API did not create an activity.' );

	return $activity_id;
};

$original_blog_id      = get_current_blog_id();
$secondary_blog_id     = 0;
$primary_activity_id   = 0;
$secondary_activity_id = 0;
$suffix                = strtolower( wp_generate_uuid4() );
$primary_name          = 'Portfolio primary ' . $suffix;
$secondary_name        = 'Portfolio secondary ' . $suffix;

try {
	$assert( is_multisite(), 'WP User Activity must use the multisite pilot.' );
	$assert( function_exists( '_wp_user_activity' ), 'The production plugin did not load.' );
	$assert( 'activity' === wp_user_activity_get_post_type(), 'The public activity post type ID changed.' );
	$assert( post_type_exists( 'activity' ), 'The activity post type was not registered.' );

	$activity_post_type = get_post_type_object( 'activity' );
	$assert( $activity_post_type instanceof WP_Post_Type, 'WordPress did not return the activity post type object.' );
	$assert( false === $activity_post_type->public, 'Activity records must remain private.' );
	$assert( false === $activity_post_type->publicly_queryable, 'Activity records must not become publicly queryable.' );
	$assert( true === $activity_post_type->show_ui, 'The activity administration UI must remain available.' );
	$assert( true === $activity_post_type->can_export, 'Activity records must remain exportable.' );
	$assert( true === $activity_post_type->delete_with_user, 'Activity records must remain associated with user deletion.' );
	$registered_meta_keys = array(
		'wp_user_activity_object_type',
		'wp_user_activity_object_subtype',
		'wp_user_activity_object_name',
		'wp_user_activity_object_id',
		'wp_user_activity_action',
	);
	foreach ( $registered_meta_keys as $registered_meta_key ) {
		$assert(
			registered_meta_key_exists( 'post', $registered_meta_key, 'activity' ),
			'Activity metadata was not registered: ' . $registered_meta_key
		);
	}

	$types = wp_user_activity_get_all_actions();
	$assert( isset( $types['post'] ) && $types['post'] instanceof WP_User_Activity_Type_Posts, 'The default post activity type was not registered.' );
	$assert( 10 === has_action( 'transition_post_status', array( $types['post'], 'transition_post_status' ) ), 'The post activity logger was not registered.' );

	$administrators = get_users(
		array(
			'fields' => 'ids',
			'number' => 1,
			'role'   => 'administrator',
		)
	);
	$assert( ! empty( $administrators ), 'The integration site has no administrator.' );
	$user_id = (int) reset( $administrators );
	wp_set_current_user( $user_id );

	$primary_activity_id = $insert_activity( $primary_name, $user_id, 101 );
	$primary_activity    = get_post( $primary_activity_id );
	$assert( $primary_activity instanceof WP_Post, 'The inserted primary activity could not be read.' );
	$assert( 'activity' === $primary_activity->post_type && 'publish' === $primary_activity->post_status, 'The activity storage contract changed.' );
	$assert( $user_id === (int) $primary_activity->post_author, 'The activity author was not persisted.' );
	$assert( array( $primary_activity_id ) === $find_activity( $primary_name ), 'The primary activity could not be queried by stored metadata.' );
	$assert(
		array(
			'object_type'    => 'post',
			'object_subtype' => 'post',
			'object_name'    => $primary_name,
			'object_id'      => '101',
			'action'         => 'update',
			'ip'             => '192.0.2.10',
			'ua'             => 'Portfolio integration smoke',
		) === wp_user_activity_get_meta( $primary_activity_id ),
		'The public metadata API did not return the persisted activity context.'
	);
	$assert( 10 === has_action( 'transition_post_status', array( $types['post'], 'transition_post_status' ) ), 'Activity insertion did not restore the transition logger.' );

	$network = get_network();
	$assert( $network instanceof WP_Network, 'WordPress did not return the current network.' );
	if ( is_subdomain_install() ) {
		$smoke_domain = 'portfolio-smoke-' . $suffix . '.' . $network->domain;
		$smoke_path   = $network->path;
	} else {
		$smoke_domain = $network->domain;
		$smoke_path   = trailingslashit( $network->path ) . 'portfolio-smoke-' . $suffix . '/';
	}

	$created_blog = wpmu_create_blog(
		$smoke_domain,
		$smoke_path,
		'Portfolio integration smoke',
		$user_id,
		array(),
		(int) $network->id
	);
	$assert( ! is_wp_error( $created_blog ) && 0 < $created_blog, 'WordPress could not create the isolated secondary site.' );
	$secondary_blog_id = (int) $created_blog;

	switch_to_blog( $secondary_blog_id );
	$assert( array() === $find_activity( $primary_name ), 'Primary-site activity leaked into the secondary site.' );
	$secondary_activity_id = $insert_activity( $secondary_name, $user_id, 202 );
	$assert( array( $secondary_activity_id ) === $find_activity( $secondary_name ), 'The secondary-site activity was not stored in that site.' );
	restore_current_blog();

	$assert( get_current_blog_id() === $original_blog_id, 'WordPress did not restore the primary site.' );
	$assert( array( $primary_activity_id ) === $find_activity( $primary_name ), 'The primary activity changed after switching sites.' );
	$assert( array() === $find_activity( $secondary_name ), 'Secondary-site activity leaked into the primary site.' );
} finally {
	while ( ms_is_switched() ) {
		restore_current_blog();
	}
	if ( 0 < $primary_activity_id ) {
		wp_delete_post( $primary_activity_id, true );
	}
	if ( 0 < $secondary_blog_id ) {
		switch_to_blog( $secondary_blog_id );
		if ( 0 < $secondary_activity_id ) {
			wp_delete_post( $secondary_activity_id, true );
		}
		restore_current_blog();
		require_once ABSPATH . 'wp-admin/includes/ms.php';
		wpmu_delete_blog( $secondary_blog_id, true );
	}
}
