<?php

/**
 * User Activity User Widgets
 *
 * @package User/Activity/Actions/Widgets
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Widgets actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Widgets extends WP_User_Activity_Action {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'widget';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {


		// Setup callbacks
		$this->action_callbacks = array(

			// Update
			'update' => array(
				'labels' => array(
					'description' => esc_html__( '%1$s edited the "%2$s" widget %3$s.', 'wp-user-activity' )
				)
			),

			// Delete
			'delete' => array(
				'labels' => array(
					'description' => esc_html__( '%1$s deleted the "%2$s" widget %3$s.', 'wp-user-activity' )
				)
			)
		);

		// Actions
		add_action( 'widget_update_callback', array( $this, 'widget_update_callback' ), 9999, 4 );
		add_action( 'sidebar_admin_setup',    array( $this, 'widget_delete'          )          );

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
			$this->get_activity_action( 'delete' ),
			$this->get_activity_author_link( $post ),
			ucfirst( $meta->object_name ),
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Widgets updated
	 *
	 * @since 0.1.0
	 *
	 * @param  object     $instance
	 * @param  object     $new_instance
	 * @param  object     $old_instance
	 * @param  WP_Widget  $widget
	 */
	public function widget_update_action_callback( $instance, $new_instance, $old_instance, WP_Widget $widget ) {
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $this->get_sidebar(),
			'object_name'    => $widget->id_base,
			'object_id'      => 0,
			'action'         => 'update'
		) );
	}

	/**
	 * Widget deleted
	 *
	 * @since 0.1.0
	 */
	public function widget_delete() {

		// Bail if not widget deletion request
		if ( ! $this->is_widget_delete() ) {
			return;
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $this->get_sidebar(),
			'object_name'    => $_REQUEST['id_base'],
			'object_id'      => 0,
			'action'         => 'delete',
		) );
	}

	/**
	 * Is a user attempting to delete a widget?
	 *
	 * @since 0.1.0
	 *
	 * @return boolean
	 */
	protected function is_widget_delete() {

		// Bail if not post request
		if ( 'post' !== strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
			return false;
		}

		// Bail if no widget ID passed
		if ( empty( $_REQUEST['widget-id'] ) ) {
			return false;
		}

		if ( empty( $_REQUEST['delete_widget'] ) ) {
			return false;
		}

		// Backwards, but so be it
		return true;
	}

	/**
	 * Get the "sidebar" that a widget is for
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	protected function get_sidebar() {

		// Get sidebar request
		if ( isset( $_REQUEST['sidebar'] ) ) {
			return strtolower( $_REQUEST['sidebar'] );
		}

		// Unknown sidebar
		return 'unknown';
	}
}
new WP_User_Activity_Action_Widgets();
