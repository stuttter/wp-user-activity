<?php

/**
 * User Activity Comments Actions
 *
 * @package User/Activity/Actions/Comments
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Comment actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Type_Comments extends WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'comment';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = 'admin-comments';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Comments', 'wp-user-actiivity' );

		// Create
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'create',
			'name'    => esc_html__( 'Create', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s left a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Pending
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'pending',
			'name'    => esc_html__( 'Pending', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s left a pending comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s updated a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Trash
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'trash',
			'name'    => esc_html__( 'Trash', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s trashed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Untrash
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'untrash',
			'name'    => esc_html__( 'Untrash', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s untrashed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Spam
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'spam',
			'name'    => esc_html__( 'Spam', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s spammed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Unspammed
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'unspam',
			'name'    => esc_html__( 'Unspam', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s unspammed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'wp_insert_comment', array( $this, 'handle_comment' ), 10, 2 );
		add_action( 'edit_comment',      array( $this, 'handle_comment' ) );
		add_action( 'trash_comment',     array( $this, 'handle_comment' ) );
		add_action( 'untrash_comment',   array( $this, 'handle_comment' ) );
		add_action( 'spam_comment',      array( $this, 'handle_comment' ) );
		add_action( 'unspam_comment',    array( $this, 'handle_comment' ) );
		add_action( 'delete_comment',    array( $this, 'handle_comment' ) );

		// Setup callbacks
		parent::__construct();
	}

	/** Callbacks *************************************************************/

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 1.0.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function pending_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'pending' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function create_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'create' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function update_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'update' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function delete_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'delete' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function trash_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'trash' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function untrash_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'untrash' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function spam_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'spam' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function unspam_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'unspam' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Helper function for adding comment activity
	 *
	 * @since 0.1.0
	 *
	 * @param  int     $id
	 * @param  string  $action
	 * @param  int     $comment
	 */
	protected function add_comment_activity( $id, $action, $comment = null ) {

		// Get comment
		if ( is_null( $comment ) ) {
			$comment = get_comment( $id );
		}

		// Get the post so we can use raw db data
		$post = get_post( $comment->comment_post_ID );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $post->post_type,
			'object_name'    => $post->post_title,
			'object_id'      => $id,
			'action'         => $action
		) );
	}

	/**
	 * Handle
	 * @since 0.1.0
	 *
	 * @param  int  $comment_ID
	 * @param  int  $comment
	 */
	public function handle_comment( $comment_ID, $comment = null ) {

		// Get comment
		if ( is_null( $comment ) ) {
			$comment = get_comment( $comment_ID );
		}

		// Default action
		$action = 'create';

		// Based on current filter
		switch ( current_filter() ) {

			// New
			case 'wp_insert_comment' :
				$action = ( 1 === (int) $comment->comment_approved )
					? 'create'
					: 'pending';
				break;

			// Edit
			case 'edit_comment' :
				$action = 'update';
				break;

			// Delete
			case 'delete_comment' :
				$action = 'delete';
				break;

			// Trash
			case 'trash_comment' :
				$action = 'trash';
				break;

			// Untrash
			case 'untrash_comment' :
				$action = 'untrash';
				break;

			// Spam
			case 'spam_comment' :
				$action = 'spam';
				break;

			// Unspam
			case 'unspam_comment' :
				$action = 'unspam';
				break;
		}

		$this->add_comment_activity( $comment_ID, $action, $comment );
	}

	/**
	 * Comment transition
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $new_status
	 * @param  string  $old_status
	 * @param  object  $comment
	 */
	public function transition_comment_status( $new_status, $old_status, $comment ) {
		$this->add_comment_activity( $comment->comment_ID, $new_status, $comment );
	}
}
