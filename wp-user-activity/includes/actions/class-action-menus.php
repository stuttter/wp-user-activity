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
class WP_User_Activity_Type_Menu extends WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'menu';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = 'menu';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Menus', 'wp-user-activity' );

		// Create
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'create',
			'name'    => esc_html__( 'Create', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s created the "%2$s" menu %3$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s edited the "%2$s" menu %3$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted the "%2$s" menu %3$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'wp_update_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'wp_create_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'delete_nav_menu',    array( $this, 'menu_deleted'            ), 10, 3 );

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
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function create_action_callback( $post, $meta ) {
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
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function update_action_callback( $post, $meta ) {
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
	 * @param  object  $meta
	 *
	 * @return string
	 */
	public function delete_action_callback( $post, $meta ) {
		return sprintf(
			$this->get_activity_action( 'delete' ),
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
