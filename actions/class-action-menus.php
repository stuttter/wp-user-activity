<?php

/**
 * User Activity Menu Actions
 *
 * @package User/Activity/Actions/Menu
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Menu actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Menu extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'menu';

	/**
	 * Array of actions in this class
	 *
	 * @since 0.1.1
	 *
	 * @var array
	 */
	public $action_callbacks = array( 'create', 'update', 'delete' );

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Actions
		add_action( 'wp_update_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'wp_create_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'delete_nav_menu',    array( $this, 'menu_deleted'            ), 10, 3 );

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
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function create_action_callback( $post, $meta = array() ) {
		$text = esc_html__( '%1$s create the "%2$s" menu %3$s.', 'wp-user-activity' );

		return sprintf(
			$text,
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
		$text = esc_html__( '%1$s edited the "%2$s" menu %3$s.', 'wp-user-activity' );

		return sprintf(
			$text,
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
		$text = esc_html__( '%1$s deleted the "%2$s" menu %3$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
			ucfirst( $meta->object_name ),
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Menu created or updated
	 *
	 * @since 0.1.0
	 *
	 * @param id $nav_menu_selected_id
	 */
	public function menu_created_or_updated( $nav_menu_selected_id ) {

		// Get a menu object
		$menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

		// Bail if not a menu object
		if ( empty( $menu_object ) ) {
			return;
		}

		// Get action
		$action = ( 'wp_create_nav_menu' === current_filter() )
			? 'create'
			: 'update';

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $menu_object->name,
			'object_id'   => $nav_menu_selected_id,
			'action'      => $action,
		) );
	}

	/**
	 * Menu deleted
	 *
	 * @sice 0.1.0
	 *
	 * @param  id  $term
	 * @param  id  $tt_id
	 * @param  id  $deleted_term
	 */
	public function menu_deleted( $term, $tt_id, $deleted_term ) {
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $deleted_term->name,
			'object_id'   => $deleted_term->term_id,
			'action'      => 'delete',
		) );
	}
}
new WP_User_Activity_Action_Menu();
