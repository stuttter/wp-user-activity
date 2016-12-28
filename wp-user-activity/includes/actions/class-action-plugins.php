<?php

/**
 * User Activity Plugins Actions
 *
 * @package User/Activity/Actions/Plugins
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Plugins actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Type_Plugins extends WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'plugin';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = 'admin-plugins';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Plugins', 'wp-user-activity' );

		// Activate
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'activate',
			'name'    => esc_html__( 'Activate', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s activated the "%2$s" plugin %3$s.', 'wp-user-activity' )
		) );

		// Deactivate
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'deactivate',
			'name'    => esc_html__( 'Deactivate', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deactivated the "%2$s" plugin %3$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s updated the "%2$s" plugin %3$s.', 'wp-user-activity' )
		) );

		// Install
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'install',
			'name'    => esc_html__( 'Install', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s installed the "%2$s" plugin %3$s.', 'wp-user-activity' )
		) );

		// File update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'file_update',
			'name'    => esc_html__( 'File Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s edited "%2$s" in the "%3$s" plugin file %4$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted the "%2$s" plugin %3$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'activated_plugin',          array( $this, 'activated_plugin'         ) );
		add_action( 'deactivated_plugin',        array( $this, 'deactivated_plugin'       ) );
		add_action( 'delete_plugin',             array( $this, 'deleted_plugin'           ) );
		add_action( 'upgrader_process_complete', array( $this, 'plugin_install_or_update' ), 10, 2 );
		add_filter( 'wp_redirect',               array( $this, 'plugin_modify'            ), 10, 2 );

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
	public function activate_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'activate' ),
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
	public function deactivate_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'deactivate' ),
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
	public function install_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'install' ),
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
	public function file_update_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'file_update' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$meta->object_subtype,
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
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Helper function for adding plugin activity
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $action
	 * @param  string  $plugin_name
	 */
	protected function add_plugin_activity( $action = '', $plugin_name = '' ) {

		// Get plugin name if is a path
		if ( false !== strpos( $plugin_name, '/' ) ) {
			$plugin_dir  = explode( '/', $plugin_name );
			$plugin_data = array_values( get_plugins( '/' . $plugin_dir[0] ) );
			$plugin_data = array_shift( $plugin_data );

			// Only override plugin name if a literal name was found
			if ( ! empty( $plugin_data['Name'] ) ) {
				$plugin_name = $plugin_data['Name'];
			}
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $plugin_name,
			'object_id'   => 0,
			'action'      => $action,
		) );
	}

	/**
	 * Plugin deactivated
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name
	 */
	public function deactivated_plugin( $plugin_name = '' ) {
		$this->add_plugin_activity( 'deactivate', $plugin_name );
	}

	/**
	 * Plugin activated
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name
	 */
	public function activated_plugin( $plugin_name = '' ) {
		$this->add_plugin_activity( 'activate', $plugin_name );
	}

	/**
	 * Plugin deleted
	 *
	 * @since 0.1.10
	 *
	 * @param string $plugin_name
	 */
	public function deleted_plugin( $plugin_name = '' ) {
		$this->add_plugin_activity( 'delete', $plugin_name );
	}

	/**
	 * Plugin modified
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $location
	 * @param  string  $status
	 *
	 * @return string
	 */
	public function plugin_modify( $location, $status ) {

		if ( false !== strpos( $location, 'plugin-editor.php' ) ) {

			if ( ! empty( $_POST ) && ( 'update' === $_REQUEST['action'] ) ) {

				$args = array(
					'object_type'    => $this->object_type,
					'object_subtype' => 'unknown',
					'object_name'    => 'unknown',
					'object_id'      => 0,
					'action'         => 'file_update'
				);

				if ( ! empty( $_REQUEST['file'] ) ) {
					$args['object_name'] = $_REQUEST['file'];

					// Get plugin name
					$plugin_dir  = explode( '/', $_REQUEST['file'] );
					$plugin_data = array_values( get_plugins( '/' . $plugin_dir[0] ) );
					$plugin_data = array_shift( $plugin_data );

					$args['object_subtype'] = $plugin_data['Name'];
				}

				wp_insert_user_activity( $args );
			}
		}

		// We are need return the instance, for complete the filter.
		return $location;
	}

	/**
	 * Plugin installed or updated
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $upgrader
	 * @param  array   $extra
	 */
	public function plugin_install_or_update( $upgrader, $extra ) {

		if ( ! isset( $extra['type'] ) || ( 'plugin' !== $extra['type'] ) ) {
			return;
		}

		if ( 'install' === $extra['action'] ) {
			$path = $upgrader->plugin_info();
			if ( ! $path ) {
				return;
			}

			$data = get_plugin_data( $upgrader->skin->result['local_destination'] . '/' . $path, true, false );

			// Insert activity
			wp_insert_user_activity( array(
				'object_type'    => $this->object_type,
				'object_subtype' => $data['Version'],
				'object_name'    => $data['Name'],
				'action'         => 'install'
			) );
		}

		if ( 'update' !== $extra['action'] ) {
			return;
		}

		if ( isset( $extra['bulk'] ) && true == $extra['bulk'] ) {
			$slugs = $extra['plugins'];
		} else {
			if ( ! isset( $upgrader->skin->plugin ) ) {
				return;
			}

			$slugs = array( $upgrader->skin->plugin );
		}

		foreach ( $slugs as $slug ) {
			$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $slug, true, false );

			// Insert activity
			wp_insert_user_activity( array(
				'object_type'    => $this->object_type,
				'object_subtype' => $data['Version'],
				'object_name'    => $data['Name'],
				'action'        => 'update'
			) );
		}
	}
}
