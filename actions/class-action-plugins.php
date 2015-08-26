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
class WP_User_Activity_Action_Plugins extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'plugin';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'activated_plugin',   array( $this, 'activated_plugin'   ) );
		add_action( 'deactivated_plugin', array( $this, 'deactivated_plugin' ) );
		add_filter( 'wp_redirect',        array( $this, 'plugin_modify'      ), 10, 2 );

		add_action( 'upgrader_process_complete', array( $this, 'plugin_install_or_update' ), 10, 2 );

		parent::__construct();
	}

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
			$plugin_name = $plugin_data['Name'];
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
		$this->add_plugin_activity( 'deactivated', $plugin_name );
	}

	/**
	 * Plugin activated
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name
	 */
	public function activated_plugin( $plugin_name = '' ) {
		$this->add_plugin_activity( 'activated', $plugin_name );
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
					'action'         => 'file_updated',
					'object_id'      => 0,
					'object_type'    => 'plugin',
					'object_subtype' => 'plugin_unknown',
					'object_name'    => 'file_unknown',
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
				'action'         => 'installed',
				'object_type'    => 'plugin',
				'object_subtype' => $data['Version'],
				'object_name'    => $data['Name'],
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
				'action'        => 'update',
				'object_type'    => 'plugin',
				'object_subtype' => $data['Version'],
				'object_name'    => $data['Name'],
			) );
		}
	}
}
new WP_User_Activity_Action_Plugins();