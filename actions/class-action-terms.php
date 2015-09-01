<?php

/**
 * User Activity Taxonomy Actions
 *
 * @package User/Activity/Actions/Taxonomy
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Taxonomy extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'term';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'created_term', array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'edited_term',  array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'delete_term',  array( $this, 'created_edited_deleted_term' ), 10, 4 );

		// Setup callbacks
		parent::__construct( array(
			'create' => array( $this, 'create_callback' ),
			'update' => array( $this, 'update_callback' ),
			'delete' => array( $this, 'delete_callback' )
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
		$tax  = get_taxonomy( $meta->object_subtype );
		$text = __( '%1$s created the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $tax->labels->singular_name ),
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
		$tax  = get_taxonomy( $meta->object_subtype );
		$text = __( '%1$s edited "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $tax->labels->singular_name ),
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
		$tax  = get_taxonomy( $meta->object_subtype );
		$text = __( '%1$s deleted the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$user->display_name,
			$meta->object_name,
			strtolower( $tax->labels->singular_name ),
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Handle create/edit/delete term actions
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $term_id
	 * @param  int     $tt_id
	 * @param  string  $taxonomy
	 * @param  string  $deleted_term
	 */
	public function created_edited_deleted_term( $term_id, $tt_id, $taxonomy, $deleted_term = null ) {

		// Make sure do not action nav menu taxonomy.
		if ( 'nav_menu' === $taxonomy ) {
			return;
		}

		if ( 'delete_term' === current_filter() ) {
			$term = $deleted_term;
		} else {
			$term = get_term( $term_id, $taxonomy );
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {

			if ( 'edited_term' === current_filter() ) {
				$action = 'update';
			} elseif ( 'delete_term' === current_filter() ) {
				$action  = 'delete';
				$term_id = '';
			} else {
				$action = 'create';
			}

			// Insert activity
			wp_insert_user_activity( array(
				'object_type'    => $this->object_type,
				'object_subtype' => $taxonomy,
				'object_name'    => $term->name,
				'object_id'      => $term_id,
				'action'         => $action
			) );
		}
	}
}
new WP_User_Activity_Action_Taxonomy();
