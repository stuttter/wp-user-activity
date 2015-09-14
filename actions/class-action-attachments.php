<?php

/**
 * User Activity Attachment Actions
 *
 * @package User/Activity/Actions/Attachments
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Attachment actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Type_Attachment extends WP_User_Activity_Type {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'attachment';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Attachments', 'wp-user-actiivity' );

		// Create
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'create',
			'name'    => esc_html__( 'Create', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s uploaded "%2$s" %3$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s edited "%2$s" %3$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted "%2$s" %3$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'add_attachment',    array( $this, 'add_attachment'    ) );
		add_action( 'edit_attachment',   array( $this, 'edit_attachment'   ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ) );

		// Setup callbacks
		parent::__construct();
	}

	/** Callbacks *************************************************************/

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
	public function create_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'create' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
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
	public function update_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'update' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
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
	public function delete_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'update' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Helper function for logging attachment actions
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $action
	 * @param  int     $attachment_id
	 */
	protected function add_attachment_activity( $action = '', $attachment_id = 0 ) {
		$post = get_post( $attachment_id );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $post->post_mime_type,
			'object_name'    => $post->post_title,
			'object_id'      => $attachment_id,
			'action'         => $action,
		) );
	}

	/**
	 * Attachment added
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function add_attachment( $attachment_id = 0 ) {
		$this->add_attachment_activity( 'create', $attachment_id );
	}

	/**
	 * Attachment edited
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function edit_attachment( $attachment_id = 0 ) {
		$this->add_attachment_activity( 'update', $attachment_id );
	}

	/**
	 * Attachment deleted
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function delete_attachment( $attachment_id = 0 ) {
		$this->add_attachment_activity( 'delete', $attachment_id );
	}
}
