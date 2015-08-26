<?php

/**
 * User Activity Export Actions
 *
 * @package User/Activity/Actions/Export
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Export actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Export extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'export';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'export_wp', array( $this, 'export_wp' ) );

		// Setup callbacks
		parent::__construct( array(
			'export' => array( $this, 'export_callback' ),
		) );
	}

	/** Actions ***************************************************************/

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
	public function export_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );

		return sprintf( '%1$s exported "%2$s" %4$s.',
			$user->display_name,
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Data exported out of WordPress
	 *
	 * @since 0.1.0
	 *
	 * @param array $args
	 */
	public function export_wp( $args = array() ) {

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => isset( $args['content'] ) ? $args['content'] : 'all',
			'object_id'   => 0,
			'action'      => 'export',
		) );
	}
}
new WP_User_Activity_Action_Export();
