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
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'wp_update_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'wp_create_nav_menu', array( $this, 'menu_created_or_updated' ) );
		add_action( 'delete_nav_menu',    array( $this, 'menu_deleted'            ), 10, 3 );

		parent::__construct();
	}

	/**
	 * Menu created or updated
	 *
	 * @since 0.1.0
	 *
	 * @param id $nav_menu_selected_id
	 */
	public function menu_created_or_updated( $nav_menu_selected_id ) {

		$menu_object = wp_get_nav_menu_object( $nav_menu_selected_id );

		if ( ! empty( $menu_object ) ) {

			// Get action
			if ( 'wp_create_nav_menu' === current_filter() ) {
				$action = 'create';
			} else {
				$action = 'update';
			}

			// Insert activity
			wp_insert_user_activity( array(
				'object_type' => $this->object_type,
				'object_name' => $menu_object->name,
				'object_id'   => $nav_menu_selected_id,
				'action'      => $action,
			) );
		}
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

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $deleted_term->name,
			'object_id'   => $deleted_term->term_id,
			'action'      => 'delete',
		) );
	}
}
new WP_User_Activity_Action_Menu();