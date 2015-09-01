<?php

/**
 * User Activity Posts Actions
 *
 * @package User/Activity/Actions/Posts
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Posts actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Posts extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'post';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
		add_action( 'delete_post',            array( $this, 'delete_post' ) );
		//add_action( 'edit_post',              array( $this, 'edit_post'   ) );

		// Setup callbacks
		parent::__construct( array(
			'create'  => array( $this, 'create_callback'  ),
			'update'  => array( $this, 'update_callback'  ),
			'delete'  => array( $this, 'delete_callback'  ),
			'trash'   => array( $this, 'trash_callback'   ),
			'untrash' => array( $this, 'untrash_callback' ),
			'spam'    => array( $this, 'spam_callback'    ),
			'unspam'  => array( $this, 'unspam_callback'  ),
			'future'  => array( $this, 'future_callback'  ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s created "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s edited the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s deleted the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s trashed the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s untrashed the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s spammed the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
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
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s unspammed the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.1
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function future_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );
		$pto  = get_post_type_object( $meta->object_subtype );
		$text = __( '%1$s scheduled the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $pto->labels->singular_name ),
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Helper function for getting a post title
	 *
	 * @since 0.1.0
	 *
	 * @param   int     $post
	 * @return  string
	 */
	protected function _draft_or_post_title( $post = 0 ) {
		$title = get_the_title( $post );

		// Force title to empty string
		if ( empty( $title ) || __( 'Auto Draft' ) === $title ) {
			$title = '';
		}

		return $title;
	}

	/**
	 * Post status is changing
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $new_status
	 * @param  string  $old_status
	 * @param  id      $post
	 * @return type
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {

		// Bail if nav menu item or activity item
		if ( in_array( get_post_type( $post->ID ), array( 'nav_menu_item', 'activity' ) ) ) {
			return;
		}

		// Bail if auto-draft
		if ( 'auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status ) ) {
			return;
		}

		// Created
		if ( 'auto-draft' === $old_status && ( 'auto-draft' !== $new_status && 'inherit' !== $new_status ) ) {
			$action = 'create';

		// Trashed
		} elseif ( 'trash' === $new_status ) {
			$action = 'trash';

		// Untrashed
		} elseif ( 'trash' === $old_status && 'publish' === $new_status ) {
			$action = 'untrash';

		// Spammed
		} elseif ( 'spam' === $new_status ) {
			$action = 'spam';

		// Unspammed
		} elseif ( 'spam' === $old_status && 'publish' === $new_status ) {
			$action = 'unspam';

		// Scheduled
		} elseif ( 'future' === $new_status ) {
			$action = 'future';

		// Updated
		} else {
			$action = 'update';
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $post->post_type,
			'object_name'    => $this->_draft_or_post_title( $post->ID ),
			'object_id'      => $post->ID,
			'action'         => $action
		) );
	}

	/**
	 * Post deleted
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $post_id
	 */
	public function delete_post( $post_id = 0 ) {

		// @todo handle revisions better
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Bail if nav menu item or activity item
		if ( in_array( get_post_type( $post_id ), array( 'nav_menu_item', 'activity' ) ) ) {
			return;
		}

		// Get the post
		$post = get_post( $post_id );

		// Bail if auto-draft (@todo handle inherited children)
		if ( in_array( $post->post_status, array( 'auto-draft', 'inherit' ) ) ) {
			return;
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $post->post_type,
			'object_name'    => $this->_draft_or_post_title( $post->ID ),
			'object_id'      => $post->ID,
			'action'         => 'delete'
		) );
	}

	/**
	 * Post edit
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $post_id
	 */
	public function edit_post( $post_id = 0 ) {

		// Bail if nav menu item or activity item
		if ( in_array( get_post_type( $post_id ), array( 'nav_menu_item', 'activity' ) ) ) {
			return;
		}

		// Get the post
		$post = get_post( $post_id );

		// Bail if auto-draft (@todo handle inherited children)
		if ( in_array( $post->post_status, array( 'auto-draft', 'inherit' ) ) ) {
			return;
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $post->post_type,
			'object_name'    => $this->_draft_or_post_title( $post->ID ),
			'object_id'      => $post->ID,
			'action'         => 'update'
		) );
	}
}
new WP_User_Activity_Action_Posts();
