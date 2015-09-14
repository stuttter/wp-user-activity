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
class WP_User_Activity_Type_Theme extends WP_User_Activity_Type {

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

		// Set name
		$this->name = esc_html__( 'Themes', 'wp-user-activity' );

		// Customize
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'customize',
			'name'    => esc_html__( 'Customize', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s customized the "%2$s" theme %3$s.', 'wp-user-activity' )
		) );

		// Activate
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'activate',
			'name'    => esc_html__( 'Activate', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s activated the "%2$s" theme %3$s.', 'wp-user-activity' )
		) );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s updated the "%2$s" theme %3$s.', 'wp-user-activity' )
		) );

		// Install
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'install',
			'name'    => esc_html__( 'Install', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s installed the "%2$s" theme %3$s.', 'wp-user-activity' )
		) );

		// File update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'file_update',
			'name'    => esc_html__( 'File Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s edited "%2$s" in the "%3$s" theme file %4$s.', 'wp-user-activity' )
		) );

		// Delete
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'delete',
			'name'    => esc_html__( 'Delete', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s deleted the "%2$s" theme %3$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'delete_site_transient_update_themes', array( $this, 'theme_deleted'           ) );
		add_action( 'upgrader_process_complete',           array( $this, 'theme_install_or_update' ), 10, 2 );
		add_action( 'switch_theme',                        array( $this, 'switch_theme'            ), 10, 2 );
		add_filter( 'wp_redirect',                         array( $this, 'theme_modify'            ), 10, 2 );

		// Theme customizer
		add_action( 'customize_save', array( $this, 'theme_customizer_modified' ) );

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
	public function customize_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'customize' ),
			$this->get_activity_author_link( $post ),
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
	public function activate_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'customize' ),
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
					'object_subtype' => 'unknown',
					'object_name'    => 'unknown',
					'object_id'      => 0,
					'action'         => 'file_update'
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
			'action'         => 'activate'
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
			'object_name'    => esc_html__( 'Theme Customizer', 'wp-user-activity' ),
			'object_id'      => 0,
			'action'         => 'customize'
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
				'action'         => 'install'
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
