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
class WP_User_Activity_Type_Posts extends WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'post';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = 'admin-post';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Posts', 'wp-user-actiivity' );

		// Create
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'create',
			'name'    => esc_html__( 'Create', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s created the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s edited the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Trash
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'trash',
			'name'    => esc_html__( 'Trash', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s trashed the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Untrash
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'untrash',
			'name'    => esc_html__( 'Untrash', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s untrashed the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Spam
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'spam',
			'name'    => esc_html__( 'Spam', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s spammed the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Unspammed
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'unspam',
			'name'    => esc_html__( 'Unspam', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s unspammed the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Future
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'future',
			'name'    => esc_html__( 'Scheduled', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s scheduled the "%2$s" %3$s %4$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
		add_action( 'delete_post',            array( $this, 'delete_post' ) );

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
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
			$this->get_activity_action( 'delete' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
	public function trash_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'trash' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
	public function untrash_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'untrash' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
	public function spam_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'spam' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
	public function unspam_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'unspam' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
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
	public function future_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'future' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_post_type_singular_name( $meta->object_subtype ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Get the singular label for the post type
	 *
	 * @since 0.1.2
	 *
	 * @param  string $post_type
	 *
	 * @return string
	 */
	protected function get_post_type_singular_name( $post_type = '' ) {

		// Set default & look for more descriptive labels
		$retval = $post_type;
		$pto    = get_post_type_object( $post_type );

		// Use lowercase singular label
		if ( ! empty( $pto ) ) {
			$retval = strtolower( $pto->labels->singular_name );
		}

		return $retval;
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

		// Get post & title
		$post  = get_post( $post );
		$title = $post->post_title;

		// Force title to empty string
		if ( empty( $title ) || esc_html__( 'Auto Draft' ) === $title ) {
			$title = 'untitled';
		}

		// No title if not supported
		if ( ! post_type_supports( $post->post_type, 'title' ) ) {
			$title = 'untitled';
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
