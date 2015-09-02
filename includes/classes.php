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
	 * Array of action callback methods
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $action_callbacks = array();

	/**
	 * Probably hook things in here
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->register_action_callbacks();
	}

	/**
	 * Register callbacks based on `action_callbacks` array
	 *
	 * @since 0.1.1
	 */
	protected function register_action_callbacks() {
		foreach ( $this->action_callbacks as $callback ) {

			// Create a method callback key
			$method = "{$callback}_action_callback";

			// Register method if it exists
			if ( method_exists( $this, $method ) ) {
				wp_user_activity_register_action_callback( $this->object_type, $callback, array( $this, $method ) );
			}
		}
	}

	/**
	 * Get user that performed activity
	 *
	 * @since 0.1.0
	 *
	 * @param   int   $post
	 *
	 * @return  object
	 */
	protected function get_activity_author( $post = 0 ) {

		// Get the post
		$post = get_post( $post );
		$user = get_user_by( 'id', $post->post_author );

		// Default return value
		$link = false;

		// If in admin, user admin area links
		if ( is_admin() && current_user_can( 'edit_user', $user->ID ) ) {
			$link = get_edit_user_link( $user->ID );

		// Link to author URL if not in admin
		} else {
			$link = get_author_posts_url( $user->ID );
		}

		// Link user if a link was found
		if ( false !== $link ) {
			$retval = '<a href="' . esc_url( $link ) . '" class="wp-user-activity user-link">' . $user->display_name . '</a>';
		} else {
			$retval = $user->display_name;
		}

		// Return
		return $retval;
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
