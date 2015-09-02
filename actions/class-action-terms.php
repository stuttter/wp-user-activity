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
		add_action( 'created_term', array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'edited_term',  array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'delete_term',  array( $this, 'created_edited_deleted_term' ), 10, 4 );

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
		$text = esc_html__( '%1$s created the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author( $post ),
			$meta->object_name,
			$this->get_taxonomy_singular_name( $meta->object_subtype ),
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
		$text = esc_html__( '%1$s edited "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author( $post ),
			$meta->object_name,
			$this->get_taxonomy_singular_name( $meta->object_subtype ),
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
		$text = esc_html__( '%1$s deleted the "%2$s" %3$s %4$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author( $post ),
			$meta->object_name,
			$this->get_taxonomy_singular_name( $meta->object_subtype ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Get the singular label of the taxonomy
	 *
	 * @since 0.1.2
	 *
	 * @param  string $taxonomy
	 *
	 * @return string
	 */
	protected function get_taxonomy_singular_name( $taxonomy = '' ) {

		// Set default & look for more descriptive labels
		$retval = $taxonomy;
		$tax    = get_taxonomy( $taxonomy );

		// Use lowercase singular label
		if ( ! empty( $tax ) ) {
			$retval = strtolower( $tax->labels->singular_name );
		}

		return $retval;
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
