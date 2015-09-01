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
class WP_User_Activity_Action_Comments extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'comment';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'wp_insert_comment',         array( $this, 'handle_comment' ), 10, 2 );
		add_action( 'edit_comment',              array( $this, 'handle_comment' ) );
		add_action( 'trash_comment',             array( $this, 'handle_comment' ) );
		add_action( 'untrash_comment',           array( $this, 'handle_comment' ) );
		add_action( 'spam_comment',              array( $this, 'handle_comment' ) );
		add_action( 'unspam_comment',            array( $this, 'handle_comment' ) );
		add_action( 'delete_comment',            array( $this, 'handle_comment' ) );
		//add_action( 'transition_comment_status', array( $this, 'transition_comment_status' ), 10, 3 );

		// Setup callbacks
		parent::__construct( array(
			'create'  => array( $this, 'create_callback'  ),
			'update'  => array( $this, 'update_callback'  ),
			'delete'  => array( $this, 'delete_callback'  ),
			'trash'   => array( $this, 'trash_callback'   ),
			'untrash' => array( $this, 'untrash_callback' ),
			'spam'    => array( $this, 'spam_callback'    ),
			'unspam'  => array( $this, 'unspam_callback'  ),
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
	public function create_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s left a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function update_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s updated a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function delete_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s deleted a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function trash_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s trashed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function untrash_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s untrashed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function spam_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s spammed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function unspam_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$text = __( '%1$s unspammed a comment on the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
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

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => get_post_type( $comment->comment_post_ID ),
			'object_name'    => get_the_title( $comment->comment_post_ID ),
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
	 * @param  int     $comment
	 */
	public function transition_comment_status( $new_status, $old_status, $comment ) {
		$this->add_comment_activity( $comment->comment_ID, $new_status, $comment );
	}
}
new WP_User_Activity_Action_Comments();
