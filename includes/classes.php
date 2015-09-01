<?php

/**
 * User Activity Classes
 *
 * @package User/Activity/Classes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Empty abstract class
 *
 * Class WP_User_Activity_Action_Base
 */
abstract class WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = '';

	/**
	 * Probably hook things in here
	 *
	 * @since 0.1.0
	 */
	public function __construct( $callbacks = array() ) {
		foreach ( $callbacks as $action => $callback ) {
			wp_user_activity_register_action_callback( $this->object_type, $action, $callback );
		}
	}

	/**
	 * Get user that performed activity
	 *
	 * @since 0.1.0
	 *
	 * @param   int   $post_id
	 *
	 * @return  object
	 */
	protected function get_user( $post_id = 0 ) {
		return get_user_by( 'id', get_post_field( 'post_author', $post_id ) );
	}

	/**
	 * Get the formatted date & time for the activity
	 *
	 * @since 0.1.0
	 *
	 * @param   int  $post_id
	 *
	 * @return  string
	 */
	protected function get_how_long_ago( $post_id = 0 ) {
		$post  = get_post( $post_id );
		$date  = get_the_date( get_option( 'date_format' ), $post->ID );
		$time  = get_the_time( get_option( 'time_format' ), $post->ID );
		$both  = "{$date} {$time}";
		$pt    = strtotime( $post->post_date );
		$human = wp_user_activity_human_diff_time( $pt, current_time( 'timestamp' ) );
		return '<time pubdate datetime="' . esc_attr( $both ) . '" title="' . esc_attr( $both ) . '">' . sprintf( '%s ago', $human ) . '</time>';
	}
}
