<?php

/**
 * User Activity Core Actions
 *
 * @package User/Activity/Actions/Core
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Core actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Core extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'core';

	/**
	 * Array of actions in this class
	 *
	 * @since 0.1.1
	 *
	 * @var array
	 */
	public $action_callbacks = array( 'update', 'auto_update' );

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Actions
		add_action( '_core_updated_successfully', array( $this, 'core_updated_successfully' ) );

		// Setup callbacks
		parent::__construct();
	}

	/** Actions ***************************************************************/

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 *
	 * @return string
	 */
	public function update_action_callback( $post ) {
		$text = esc_html__( '%1$s updated WordPress %2$s.', 'wp-user-activity' );

		return sprintf( $text,
			$this->get_activity_author_link( $post ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function auto_update_action_callback( $post ) {
		$text = esc_html__( 'WordPress auto-updated %1$s.', 'wp-user-activity' );

		return sprintf( $text,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Updated WordPress core
	 *
	 * @since 0.1.0
	 *
	 * @global  string  $pagenow
	 * @param   string  $wp_version
	 */
	public function core_updated_successfully( $wp_version ) {
		global $pagenow;

		// Auto updated
		if ( 'update-core.php' !== $pagenow ) {
			$object_name = 'WordPress Auto Updated';
			$action      = 'auto_update';
		} else {
			$object_name = 'WordPress Updated';
			$action      = 'update';
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $object_name,
			'object_id'   => get_current_blog_id(),
			'severity'    => 'notice',
			'action'      => $action
		) );
	}
}
new WP_User_Activity_Action_Core();