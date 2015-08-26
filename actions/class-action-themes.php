<?php

/**
 * User Activity Theme Actions
 *
 * @package User/Activity/Actions/Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Theme actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Theme extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'theme';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_filter( 'wp_redirect',                         array( $this, 'theme_modify'  ), 10, 2 );
		add_action( 'switch_theme',                        array( $this, 'switch_theme'  ), 10, 2 );
		add_action( 'delete_site_transient_update_themes', array( $this, 'theme_deleted' ) );
		add_action( 'upgrader_process_complete',           array( $this, 'theme_install_or_update' ), 10, 2 );

		// Theme customizer
		add_action( 'customize_save', array( $this, 'theme_customizer_modified' ) );

		parent::__construct();
	}

	/**
	 * Theme modified
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $location
	 * @param  string  $status
	 *
	 * @return string
	 */
	public function theme_modify( $location, $status ) {

		if ( false !== strpos( $location, 'theme-editor.php?file=' ) ) {

			if ( ! empty( $_POST ) && ( 'update' === $_POST['action'] ) ) {

				$args = array(
					'object_type'    => $this->object_type,
					'object_subtype' => 'theme_unknown',
					'object_name'    => 'file_unknown',
					'object_id'      => 0,
					'action'         => 'file_updated'
				);

				if ( ! empty( $_POST['file'] ) ) {
					$args['object_name'] = $_POST['file'];
				}

				if ( ! empty( $_POST['theme'] ) ) {
					$args['object_subtype'] = $_POST['theme'];
				}

				wp_insert_user_activity( $args );
			}
		}

		return $location;
	}

	/**
	 * Theme switched
	 *
	 * @since 0.1.0
	 *
	 * @param  string    $new_name
	 * @param  WP_Theme  $new_theme
	 */
	public function switch_theme( $new_name, WP_Theme $new_theme ) {

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $new_theme->get_stylesheet(),
			'object_name'    => $new_name,
			'object_id'      => 0,
			'action'         => 'activated'
		) );
	}

	/**
	 * Theme modified
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Customize_Manager $obj
	 */
	public function theme_customizer_modified( WP_Customize_Manager $obj ) {
		$args = array(
			'object_type'    => $this->object_type,
			'object_subtype' => $obj->theme()->display( 'Name' ),
			'object_name'    => 'Theme Customizer',
			'object_id'      => 0,
			'action'         => 'update'
		);

		// Accessed the customizer
		if ( 'customize_preview_init' === current_filter() ) {
			$args['action'] = 'read';
		}

		wp_insert_user_activity( $args );
	}

	/**
	 * Theme deleted
	 *
	 * @since 0.1.0
	 */
	public function theme_deleted() {
		$backtrace_history = debug_backtrace();
		$delete_theme_call = null;

		foreach ( $backtrace_history as $call ) {
			if ( isset( $call['function'] ) && 'delete_theme' === $call['function'] ) {
				$delete_theme_call = $call;
				break;
			}
		}

		if ( empty( $delete_theme_call ) ) {
			return;
		}

		$name = $delete_theme_call['args'][0];

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $name,
			'action'      => 'delete',
		) );
	}

	/**
	 * Theme install of update
	 *
	 * @since 0.1.0
	 *
	 * @param Theme_Upgrader $upgrader
	 * @param array $extra
	 */
	public function theme_install_or_update( $upgrader, $extra ) {

		// Bail if not a theme
		if ( ! isset( $extra['type'] ) || ( 'theme' !== $extra['type'] ) ) {
			return;
		}

		// Install
		if ( 'install' === $extra['action'] ) {

			// Bail if no theme found
			$slug = $upgrader->theme_info();
			if ( empty( $slug ) ) {
				return;
			}

			wp_clean_themes_cache();

			$theme   = wp_get_theme( $slug );
			$name    = $theme->name;
			$version = $theme->version;

			// Insert activity
			wp_insert_user_activity( array(
				'object_type'    => $this->object_type,
				'object_subtype' => $version,
				'object_name'    => $name,
				'action'         => 'installed'
			) );

		// Update
		} elseif ( 'update' === $extra['action'] ) {

			// Get theme slugs
			if ( isset( $extra['bulk'] ) && ( true == $extra['bulk'] ) ) {
				$slugs = $extra['themes'];
			} else {
				$slugs = array( $upgrader->skin->theme );
			}

			// Activity for each theme
			foreach ( $slugs as $slug ) {

				$theme      = wp_get_theme( $slug );
				$stylesheet = $theme['Stylesheet Dir'] . '/style.css';
				$theme_data = get_file_data( $stylesheet, array( 'Version' => 'Version' ) );

				$name    = $theme['Name'];
				$version = $theme_data['Version'];

				// Insert activity
				wp_insert_user_activity( array(
					'object_type'    => $this->object_type,
					'object_subtype' => $version,
					'object_name'    => $name,
					'action'         => 'update'
				) );
			}
		}
	}
}
new WP_User_Activity_Action_Theme();