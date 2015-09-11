<?php

/**
 * User Activity User Actions
 *
 * @package User/Activity/Actions/User
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * User actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_User extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'user';

	/**
	 * Array of actions in this class
	 *
	 * @since 0.1.1
	 *
	 * @var array
	 */
	public $action_callbacks = array( 'login', 'logout', 'create', 'delete', 'update', 'login_fail' );

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Actions
		add_action( 'wp_login',        array( $this, 'wp_login'        ), 10, 2 );
		add_action( 'wp_logout',       array( $this, 'wp_logout'       ) );
		add_action( 'delete_user',     array( $this, 'delete_user'     ) );
		add_action( 'user_register',   array( $this, 'user_register'   ) );
		add_action( 'profile_update',  array( $this, 'profile_update'  ) );
		add_filter( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

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
	 *
	 * @return string
	 */
	public function login_action_callback( $post, $meta = array() ) {
		$text = esc_attr__( '%1$s logged in %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
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
	public function login_fail_action_callback( $post, $meta = array() ) {
		$text = esc_html__( 'Failed login attempt for "%1$s" %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
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
	 *
	 * @return string
	 */
	public function logout_action_callback( $post ) {
		$text = esc_html__( '%1$s logged out %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 *
	 * @return string
	 */
	public function create_action_callback( $post ) {
		$text = esc_html__( '%1$s registered %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 *
	 * @return string
	 */
	public function update_action_callback( $post ) {
		$text = esc_html__( '%1$s updated their account %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 *
	 * @return string
	 */
	public function delete_action_callback( $post ) {
		$text = esc_html__( '%1$s deleted their account %2$s.', 'wp-user-activity' );

		return sprintf(
			$text,
			$this->get_activity_author_link( $post ),
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * User logged in
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $user_login
	 * @param  string  $user
	 */
	public function wp_login( $user_login, $user ) {
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_name'    => $user->user_nicename,
			'object_subtype' => $user_login,
			'object_id'      => $user->ID,
			'user_id'        => $user->ID,
			'action'         => 'login'
		) );
	}

	/**
	 * User logged out
	 *
	 * @since 0.1.0
	 */
	public function wp_logout() {
		$user = wp_get_current_user();

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $user->user_nicename,
			'object_id'   => $user->ID,
			'action'      => 'logout'
		) );
	}

	/**
	 * User registered
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $user_id
	 */
	public function user_register( $user_id = 0 ) {
		$user = get_user_by( 'id', $user_id );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $user->user_nicename,
			'object_id'   => $user->ID,
			'action'      => 'create'
		) );
	}

	/**
	 * User updated their profile
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $user_id
	 */
	public function profile_update( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $user->user_nicename,
			'object_id'   => $user->ID,
			'action'      => 'update'
		) );
	}

	/**
	 * User deleted
	 *
	 * @since 0.1.0
	 *
	 * @param  int  $user_id
	 */
	public function delete_user( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $user->user_nicename,
			'object_id'   => $user->ID,
			'action'      => 'delete'
		) );
	}

	/**
	 * Username attempted login
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $user_login
	 */
	public function wp_login_failed( $user_login = '' ) {

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $user_login,
			'object_id'   => 0,
			'action'      => 'login_fail'
		) );
	}
}
new WP_User_Activity_Action_User();
