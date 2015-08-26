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

		parent::__construct();
	}

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

		// Bail if a revision
		if ( wp_is_post_revision( $post->ID ) ) {
			return;
		}

		// Bail if nav menu item or activity item
		if ( in_array( get_post_type( $post->ID ), array( 'nav_menu_item', 'activity' ) ) ) {
			return;
		}

		// Page created
		if ( 'auto-draft' === $old_status && ( 'auto-draft' !== $new_status && 'inherit' !== $new_status ) ) {
			$action = 'create';

		// Bail
		} elseif ( 'auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status ) ) {
			return;

		// Page was deleted
		} elseif ( 'trash' === $new_status ) {
			$action = 'delete';

		// Page updated
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
}
new WP_User_Activity_Action_Posts();
